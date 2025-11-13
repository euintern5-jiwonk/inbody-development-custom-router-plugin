/**
 * SPA Router - Client-side routing for WordPress
 */
(function($) {
    'use strict';

    const SPARouter = {
        // Configuration
        config: {
            contentContainer: '#spa-content',
            loaderClass: 'spa-loading',
            linkSelector: 'a[data-spa-link]',
            apiEndpoint: SPARouterData.apiEndpoint,
        },

        // Cache
        cache: {},

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.handlePopState();
            console.log('SPA Router initialized');
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            const self = this;

            // Intercept navigation links
            $(document).on('click', this.config.linkSelector, function(e) {
                e.preventDefault();
                
                const $link = $(this);
                const PageParent = $link.data('page-parent');
                const pageSlug = $link.data('page-slug');
                const pageId = $link.data('page-id');
                const productId = $link.data('product-id');
                const url = $link.attr('href');
                console.log('Slug: ', pageSlug, ' PageID: ', pageId);

                if (pageSlug) {
                    self.loadPage(PageParent, pageSlug, url, { product_id: productId });
                } else if (pageId) {
                    self.loadPageById(pageId, url);
                }
            });

            // Browser back/forward
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.pageSlug) {
                    self.loadPage(e.state.pageSlug, null, e.state.params, false);
                }
            });
        },

        /**
         * Render page content
         */
        renderPage: function(page, url, slug, params, pushState) {
            this.updateContent(page.content);
            this.updateTitle(page.title);
            
            if (pushState) {
                this.updateURL(url || page.url, {
                    pageSlug: slug,
                    params: params,
                    title: page.title
                });
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
            $(document).trigger('spa:page-loaded', [page]);
        },



        /**
         * Load page by ID
         */
        loadPageById: function(id, url, pushState = true) {
            this.showLoader();

            $.ajax({
                url: this.config.apiEndpoint,
                method: 'GET',
                data: { id: id },
                dataType: 'json',
                success: (response) => {
                    if (response.success) {
                        this.updateContent(response.page.content);
                        this.updateTitle(response.page.title);
                        
                        if (pushState) {
                            this.updateURL(url || response.page.url, {
                                pageId: id,
                                title: response.page.title
                            });
                        }

                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        $(document).trigger('spa:page-loaded', [response.page]);
                    } else {
                        this.showError('Page not found');
                    }
                },
                error: () => {
                    this.showError('Failed to load page');
                },
                complete: () => {
                    this.hideLoader();
                }
            });
        },

        /**
         * Load page by slug
         */
        loadPage: function(parent, slug, url, params = {}, pushState = true) {
            // Check cache
            const cacheKey = slug + JSON.stringify(params);
            if (this.cache[cacheKey]) {
                console.log('Loading from cache:', slug);
                this.renderPage(this.cache[cacheKey], url, slug, params, pushState);
                return;
            }

            this.showLoader();

            // Build API URL - slug is now part of the path: /wp-spa/load/slug
            // TODO: check data-page-slug="parent/child" works
            let apiUrl;
            if (parent) {
                apiUrl = this.config.apiEndpoint + '/' + encodeURIComponent(parent) + '/' + encodeURIComponent(slug);
            } else {
                // apiUrl = this.config.apiEndpoint + '/' + encodeURIComponent(slug);
                apiUrl = this.config.apiEndpoint + '/' + slug;
            }

            console.log('Loading page via API:', apiUrl);

            // TODO: (FIXME) ajax 400 error occur
            $.ajax({
                url: apiUrl,
                method: 'GET',
                dataType: 'json',
                success: (response) => {
                    console.log('API Response received:', response);

                    if (response.success && response.page) {
                        console.log('Page title:', response.page.title);
                        console.log('Page content length:', response.page.content ? response.page.content.length : 0);
                        console.log('Content preview:', response.page.content ? response.page.content.substring(0, 200) : 'empty');

                        // Cache the response
                        this.cache[cacheKey] = response.page;

                        this.renderPage(response.page, url, slug, params, pushState);
                    } else {
                        console.error('API returned success=false or no page data:', response);
                        this.showError('Page not found');
                    }
                },
                error: (xhr) => {
                    console.error('AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        url: apiUrl
                    });
                    this.showError('Failed to load page (Error ' + xhr.status + ')');
                },
                complete: () => {
                    this.hideLoader();
                }
            });
        },

        /**
         * Update page content
         */
        updateContent: function(html) {
            const $container = $(this.config.contentContainer);

            console.log('Updating content, HTML length:', html ? html.length : 0);

            // Faster fade: 150ms instead of 200ms
            $container.fadeOut(150, function() {
                // Insert new HTML
                $container.html(html);

                // Fade in the content faster
                $container.fadeIn(150);

                // NOTE: Elementor is initialized on page load and doesn't need reinitialization
                // The content returned from the API already has Elementor CSS classes
                // and will render correctly without calling init()
            });
        },

        /**
         * Update page title
         */
        updateTitle: function(title) {
            document.title = title + ' - ' + SPARouterData.siteName;
        },

        /**
         * Update browser URL without reload
         */
        updateURL: function(url, state) {
            history.pushState(state, '', url);
        },

        /**
         * Handle browser back/forward
         */
        handlePopState: function() {
            // Initial page load state
            const initialState = {
                pageSlug: SPARouterData.currentPageSlug,
                title: document.title
            };
            history.replaceState(initialState, '', window.location.href);
        },

        /**
         * Show loading indicator
         */
        showLoader: function() {
            // Add progress bar if it doesn't exist
            if (!$('.spa-progress-bar').length) {
                $('body').append('<div class="spa-progress-bar"></div>');
            }

            const $progressBar = $('.spa-progress-bar');

            // Reset and show
            $progressBar.css({
                'width': '0%',
                'opacity': '1',
                'display': 'block'
            });

            // Animate progress smoothly
            setTimeout(() => $progressBar.css('width', '30%'), 50);
            setTimeout(() => $progressBar.css('width', '70%'), 150);
        },

        /**
         * Hide loading indicator
         */
        hideLoader: function() {
            const $progressBar = $('.spa-progress-bar');

            // Complete the progress
            $progressBar.css('width', '100%');

            // Fade out and remove after a short delay
            setTimeout(() => {
                $progressBar.fadeOut(200, function() {
                    $(this).remove();
                });
            }, 200);
        },

        /**
         * Show error message
         */
        showError: function(message) {
            const $container = $(this.config.contentContainer);
            $container.html(`
                <div class="spa-error">
                    <h2>Error</h2>
                    <p>${message}</p>
                </div>
            `);
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        SPARouter.init();
    });

    // Expose to global scope
    window.SPARouter = SPARouter;

})(jQuery);