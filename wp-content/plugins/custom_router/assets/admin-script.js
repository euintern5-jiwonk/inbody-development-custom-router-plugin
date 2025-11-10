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
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        RouterAdminJS.init();
    });

})(jQuery);