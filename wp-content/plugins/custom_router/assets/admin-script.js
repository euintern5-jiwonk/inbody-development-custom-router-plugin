/**
 * Router Admin JavaScript
 */
(function($) {
    'use strict';

    const RouterAdminJS = {
        
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initTooltips();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Delete single route confirmation
            $('.delete-route-btn').on('click', function(e) {
                if (!confirm(RouterAdmin.confirmDelete)) {
                    e.preventDefault();
                    return false;
                }
            });

            // Delete all routes confirmation
            $('#delete-all-routes').on('click', function(e) {
                if (!confirm(RouterAdmin.confirmDeleteAll)) {
                    e.preventDefault();
                    return false;
                }
            });

            // Test route button
            $('.test-route-btn').on('click', function(e) {
                e.preventDefault();
                const slug = $(this).data('slug');
                const regex = $(this).data('regex');
                RouterAdminJS.testRoute(slug, regex);
            });

            // Close modal
            $('.router-modal-close').on('click', function() {
                $('.router-modal').hide();
            });

            // Close modal on outside click
            $(window).on('click', function(e) {
                if ($(e.target).hasClass('router-modal')) {
                    $('.router-modal').hide();
                }
            });

            // Auto-dismiss notices
            setTimeout(function() {
                $('.notice.is-dismissible').fadeOut();
            }, 5000);

            // Form validation
            $('#submit').on('click', function(e) {
                if (!RouterAdminJS.validateForm()) {
                    e.preventDefault();
                    return false;
                }
            });

            // Slug auto-generation
            $('#slug').on('blur', function() {
                const value = $(this).val();
                if (value) {
                    $(this).val(value.toLowerCase().replace(/[^a-z0-9-]/g, ''));
                }
            });

            // Copy to clipboard functionality
            this.addCopyButtons();

            // Verify rewrite rules button
            $('#check-rules-btn').on('click', function(e) {
                e.preventDefault();
                RouterAdminJS.verifyRewriteRules();
            });

            // Flush rewrite rules button
            $('#flush-rules-btn').on('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to flush rewrite rules? This will regenerate all WordPress rewrite rules.')) {
                    RouterAdminJS.flushRewriteRules();
                }
            });
        },

        /**
         * Test route
         */
        testRoute: function(slug, regex) {
            const modal = $('#test-route-modal');
            const resultDiv = $('#test-route-result');
            
            resultDiv.html('<div class="router-spinner"></div> Testing route...');
            modal.show();

            // Simulate test
            setTimeout(function() {
                const testUrl = RouterAdminJS.generateTestUrl(regex);
                const result = RouterAdminJS.testRegexPattern(regex, testUrl);
                
                let html = '<h3>Test Results</h3>';
                html += '<p><strong>Regex Pattern:</strong> <code>' + regex + '</code></p>';
                html += '<p><strong>Test URL:</strong> <code>' + testUrl + '</code></p>';
                html += '<p><strong>Match:</strong> ' + (result.match ? '<span style="color: green;">✓ Success</span>' : '<span style="color: red;">✗ Failed</span>') + '</p>';
                
                if (result.match && result.captures.length > 0) {
                    html += '<p><strong>Captured Groups:</strong></p>';
                    html += '<ul>';
                    result.captures.forEach(function(capture, index) {
                        html += '<li>$matches[' + (index + 1) + '] = <code>' + capture + '</code></li>';
                    });
                    html += '</ul>';
                }
                
                html += '<h4>Example Usage:</h4>';
                html += '<pre><code>// In your handler:\n';
                html += '$route = get_query_var(\'route\');\n';
                html += '$action = get_query_var(\'action\');\n\n';
                html += '// Would give you:\n';
                if (result.captures.length > 0) {
                    html += '// $matches[1] = \'' + result.captures[0] + '\'\n';
                    if (result.captures.length > 1) {
                        html += '// $matches[2] = \'' + result.captures[1] + '\'';
                    }
                }
                html += '</code></pre>';
                
                resultDiv.html(html);
            }, 500);
        },

        /**
         * Generate test URL from regex
         */
        generateTestUrl: function(regex) {
            // Remove regex special chars and create a test URL
            let url = regex
                .replace(/\([^\)]+\)/g, 'test-value')
                .replace(/\?/g, '')
                .replace(/\$/g, '')
                .replace(/\\/g, '');
            
            return window.location.origin + '/' + url;
        },

        /**
         * Test regex pattern against URL
         */
        testRegexPattern: function(pattern, url) {
            // Extract path from URL
            const path = url.replace(window.location.origin + '/', '');
            
            // Convert WordPress regex to JavaScript regex
            const jsPattern = pattern
                .replace(/\$/, '')
                .replace(/\(\[\^\/\]\+\)/g, '([^/]+)');
            
            const regex = new RegExp('^' + jsPattern);
            const match = path.match(regex);
            
            return {
                match: match !== null,
                captures: match ? match.slice(1) : []
            };
        },

        /**
         * Validate form before submission
         */
        validateForm: function() {
            let isValid = true;
            const slug = $('#slug').val().trim();
            const regex = $('#regex').val().trim();
            const query = $('#query').val().trim();

            // Clear previous errors
            $('.form-error').remove();

            // Validate slug
            if (slug === '') {
                this.showFieldError('#slug', 'Slug is required');
                isValid = false;
            } else if (!/^[a-z0-9-]+$/.test(slug)) {
                this.showFieldError('#slug', 'Slug can only contain lowercase letters, numbers, and hyphens');
                isValid = false;
            }

            // Validate regex
            if (regex === '') {
                this.showFieldError('#regex', 'Regex pattern is required');
                isValid = false;
            } else {
                // Test if it's a valid regex
                try {
                    new RegExp(regex);
                } catch(e) {
                    this.showFieldError('#regex', 'Invalid regex pattern: ' + e.message);
                    isValid = false;
                }
            }

            // Validate query
            if (query === '') {
                this.showFieldError('#query', 'Query string is required');
                isValid = false;
            } else if (!query.includes('index.php?')) {
                this.showFieldError('#query', 'Query must start with "index.php?"');
                isValid = false;
            }

            return isValid;
        },

        /**
         * Show field error
         */
        showFieldError: function(fieldId, message) {
            $(fieldId).after('<p class="form-error" style="color: #d63638; margin-top: 5px;">' + message + '</p>');
            $(fieldId).css('border-color', '#d63638');
            
            // Remove error on focus
            $(fieldId).one('focus', function() {
                $(this).css('border-color', '');
                $(this).next('.form-error').remove();
            });
        },

        /**
         * Add copy buttons to code elements
         */
        addCopyButtons: function() {
            $('.routes-table code').each(function() {
                const $code = $(this);
                const text = $code.text();
                
                if (text.length > 20) {
                    const $btn = $('<button class="button button-small copy-btn" style="margin-left: 5px;">Copy</button>');
                    
                    $btn.on('click', function(e) {
                        e.preventDefault();
                        RouterAdminJS.copyToClipboard(text, $(this));
                    });
                    
                    $code.after($btn);
                }
            });
        },

        /**
         * Copy to clipboard
         */
        copyToClipboard: function(text, $button) {
            // Create temporary textarea
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            
            try {
                document.execCommand('copy');
                
                // Show success feedback
                const originalText = $button.text();
                $button.text('Copied!').css('color', '#00a32a');
                
                setTimeout(function() {
                    $button.text(originalText).css('color', '');
                }, 2000);
            } catch(err) {
                console.error('Failed to copy:', err);
                alert('Failed to copy to clipboard');
            }
            
            $temp.remove();
        },

        /**
         * Initialize tooltips
         */
        initTooltips: function() {
            // Simple tooltip implementation
            $('[data-tooltip]').each(function() {
                const $el = $(this);
                const text = $el.data('tooltip');

                $el.on('mouseenter', function() {
                    const $tooltip = $('<div class="router-tooltip">' + text + '</div>');
                    $('body').append($tooltip);

                    const offset = $el.offset();
                    $tooltip.css({
                        top: offset.top - $tooltip.outerHeight() - 5,
                        left: offset.left + ($el.outerWidth() / 2) - ($tooltip.outerWidth() / 2)
                    });
                });

                $el.on('mouseleave', function() {
                    $('.router-tooltip').remove();
                });
            });
        },

        /**
         * Verify rewrite rules via AJAX
         */
        verifyRewriteRules: function() {
            const $btn = $('#check-rules-btn');
            const $statusDiv = $('#verification-status');

            // Show loading state
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Checking...');

            $.ajax({
                url: RouterAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'verify_rewrite_rules',
                    nonce: RouterAdmin.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        RouterAdminJS.updateVerificationDisplay(response.data);

                        // Show success notice
                        RouterAdminJS.showNotice('Verification complete!', 'success');
                    } else {
                        RouterAdminJS.showNotice('Failed to verify rules.', 'error');
                    }
                },
                error: function() {
                    RouterAdminJS.showNotice('AJAX error occurred.', 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Check Rules');
                }
            });
        },

        /**
         * Flush rewrite rules via AJAX
         */
        flushRewriteRules: function() {
            const $btn = $('#flush-rules-btn');

            // Show loading state
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Flushing...');

            $.ajax({
                url: RouterAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'flush_rewrite_rules',
                    nonce: RouterAdmin.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        RouterAdminJS.updateVerificationDisplay(response.data.verification);
                        RouterAdminJS.showNotice(response.data.message, 'success');
                    } else {
                        RouterAdminJS.showNotice('Failed to flush rules.', 'error');
                    }
                },
                error: function() {
                    RouterAdminJS.showNotice('AJAX error occurred.', 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-admin-tools"></span> Flush Rules');
                }
            });
        },

        /**
         * Update verification display with new data
         */
        updateVerificationDisplay: function(data) {
            const $statusDiv = $('#verification-status');

            // Update status class
            $statusDiv.removeClass('verification-success verification-warning verification-error')
                      .addClass('verification-' + data.status);

            // Build HTML
            let iconHtml = '';
            if (data.status === 'success') {
                iconHtml = '<span class="dashicons dashicons-yes-alt"></span>';
            } else if (data.status === 'warning') {
                iconHtml = '<span class="dashicons dashicons-warning"></span>';
            } else {
                iconHtml = '<span class="dashicons dashicons-dismiss"></span>';
            }

            let descriptionText = data.status !== 'success'
                ? 'Click "Flush Rules" to regenerate WordPress rewrite rules.'
                : 'All rewrite rules are properly registered.';

            let headerHtml = `
                <div class="verification-header">
                    <span class="verification-icon">${iconHtml}</span>
                    <div class="verification-message">
                        <strong>${data.message}</strong>
                        <p class="description">${descriptionText}</p>
                    </div>
                </div>
            `;

            // Build rules table
            let rulesHtml = '<div class="verification-details"><h3>Rule Details</h3><table class="widefat"><thead><tr>';
            rulesHtml += '<th>Status</th><th>Pattern</th><th>Description</th><th>Query String</th>';
            rulesHtml += '</tr></thead><tbody>';

            data.rules.forEach(function(rule) {
                const statusIcon = rule.registered
                    ? '<span class="dashicons dashicons-yes" style="color: #46b450;"></span>'
                    : '<span class="dashicons dashicons-no" style="color: #dc3232;"></span>';

                const queryCell = rule.query
                    ? '<code>' + rule.query + '</code>'
                    : '<em>Not registered</em>';

                const rowClass = rule.registered ? 'rule-registered' : 'rule-missing';

                rulesHtml += `<tr class="${rowClass}">
                    <td>${statusIcon}</td>
                    <td><code>${rule.pattern}</code></td>
                    <td>${rule.description}</td>
                    <td>${queryCell}</td>
                </tr>`;
            });

            rulesHtml += '</tbody></table></div>';

            // Add diagnostics if available
            if (data.diagnostics) {
                let diagnosticsHtml = `
                    <details style="margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #dcdcde; border-radius: 4px;">
                        <summary style="cursor: pointer; font-weight: 600; user-select: none;">
                            <span class="dashicons dashicons-admin-tools" style="vertical-align: middle;"></span>
                            Show Advanced Diagnostics
                        </summary>
                        <div style="margin-top: 15px;">
                            <table class="widefat" style="margin-top: 10px;">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Init Hook Fired</th>
                                        <td>${data.diagnostics.init_hook_fired ? '✓ Yes' : '✗ No'}</td>
                                    </tr>
                                    <tr>
                                        <th>WP Rewrite Object Exists</th>
                                        <td>${data.diagnostics.wp_rewrite_exists ? '✓ Yes' : '✗ No'}</td>
                                    </tr>
                                    <tr>
                                        <th>Permalink Structure</th>
                                        <td>
                                            <code>${data.diagnostics.permalink_structure || 'Default (No pretty permalinks)'}</code>
                                            ${!data.diagnostics.permalink_structure ? '<br><strong style="color: #d63638;">⚠ Warning: Pretty permalinks are required for custom rewrite rules!</strong><br><a href="/wp-admin/options-permalink.php" class="button button-small" style="margin-top: 5px;">Enable Pretty Permalinks</a>' : ''}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Database Rules Count</th>
                                        <td>${data.diagnostics.db_rules_count}</td>
                                    </tr>
                                    <tr>
                                        <th>WP Rewrite Rules Count</th>
                                        <td>${data.diagnostics.wp_rewrite_rules_count}</td>
                                    </tr>
                                    <tr>
                                        <th>Custom Routes Stored</th>
                                        <td>${data.diagnostics.custom_routes_count}</td>
                                    </tr>
                                </tbody>
                            </table>
                `;

                // Add sample of DB rules
                if (data.diagnostics.all_db_rules && Object.keys(data.diagnostics.all_db_rules).length > 0) {
                    diagnosticsHtml += '<h4 style="margin-top: 20px;">All WordPress Rewrite Rules (First 10)</h4>';
                    diagnosticsHtml += '<div style="max-height: 200px; overflow-y: auto; background: #fff; padding: 10px; border: 1px solid #dcdcde; border-radius: 3px;">';
                    diagnosticsHtml += '<pre style="margin: 0; font-size: 11px;">';

                    let count = 0;
                    for (const [pattern, query] of Object.entries(data.diagnostics.all_db_rules)) {
                        if (count++ >= 10) break;
                        diagnosticsHtml += pattern + ' => ' + query + '\n';
                    }
                    if (Object.keys(data.diagnostics.all_db_rules).length > 10) {
                        diagnosticsHtml += '\n... and ' + (Object.keys(data.diagnostics.all_db_rules).length - 10) + ' more rules';
                    }

                    diagnosticsHtml += '</pre></div>';
                } else {
                    diagnosticsHtml += '<p style="margin-top: 20px; color: #d63638;"><strong>No rules found in database!</strong></p>';
                }

                diagnosticsHtml += '</div></details>';
                rulesHtml += diagnosticsHtml;
            }

            $statusDiv.html(headerHtml + rulesHtml);
        },

        /**
         * Show admin notice
         */
        showNotice: function(message, type) {
            const $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            $('.wrap').prepend($notice);

            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        RouterAdminJS.init();
    });

})(jQuery);