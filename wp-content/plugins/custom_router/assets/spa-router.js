/**
 * Enhanced SPA Router with Product Features
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
            cartEndpoint: SPARouterData.homeUrl + '/api/cart',
        },

        // Cache
        cache: {},
        
        // Cart state
        cart: {
            items: [],
            total: 0,
        },

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.handlePopState();
            this.loadCart();
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
                const pageSlug = $link.data('page-slug');
                const pageId = $link.data('page-id');
                const productId = $link.data('product-id');
                const url = $link.attr('href');

                if (pageSlug) {
                    self.loadPage(pageSlug, url, { product_id: productId });
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
         * Load page by slug
         */
        loadPage: function(slug, url, params = {}, pushState = true) {
            // Check cache
            const cacheKey = slug + JSON.stringify(params);
            if (this.cache[cacheKey]) {
                console.log('Loading from cache:', slug);
                this.renderPage(this.cache[cacheKey], url, slug, params, pushState);
                return;
            }

            this.showLoader();

            // Build query params
            const queryParams = $.param(Object.assign({ slug: slug }, params));

            $.ajax({
                url: this.config.apiEndpoint + '?' + queryParams,
                method: 'GET',
                dataType: 'json',
                success: (response) => {
                    if (response.success) {
                        // Cache the response
                        this.cache[cacheKey] = response.page;
                        
                        this.renderPage(response.page, url, slug, params, pushState);
                    } else {
                        this.showError('Page not found');
                    }
                },
                error: (xhr) => {
                    this.showError('Failed to load page');
                    console.error('AJAX Error:', xhr);
                },
                complete: () => {
                    this.hideLoader();
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
         * Update content with transitions
         */
        updateContent: function(html) {
            const $container = $(this.config.contentContainer);
            
            // Fade out
            $container.addClass('fade-out');
            
            setTimeout(() => {
                $container.html(html);
                
                // Re-initialize Elementor
                if (typeof elementorFrontend !== 'undefined') {
                    elementorFrontend.init();
                    
                    if (elementorFrontend.elementsHandler) {
                        elementorFrontend.elementsHandler.runReadyTrigger();
                    }
                }
                
                // Fade in
                $container.removeClass('fade-out').addClass('fade-in');
                
                setTimeout(() => {
                    $container.removeClass('fade-in');
                }, 300);
            }, 200);
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        SPARouter.init();
    });

    // Expose to global scope
    window.SPARouter = SPARouter;

})(jQuery);