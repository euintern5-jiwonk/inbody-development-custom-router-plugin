<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class RouteHandler {

    private $route;
    private $action;

    public function __construct( $route, $action ) {
        $this->route  = sanitize_text_field( $route );
        $this->action = sanitize_text_field( $action );
    }

    public function dispatch() {
        switch ( $this->route ) {
            case 'posts':
                $this->handle_posts();
                break;
            case 'page':
                $this->handle_page_content();
                break;
            case 'products':
                $this->handle_products();
                break;
            case 'spa':
                $this->handle_spa_page();
                break;
            default:
                $this->json_response( [ 'error' => 'Route not found' ], 404 );
        }
    }

    private function handle_posts() {
        if ( $this->action === 'list' ) {
            $posts = get_posts( [ 'numberposts' => 5 ] );
            $this->json_response( $posts );
        } elseif ( $this->action === 'details' && isset( $_GET['id'] ) ) {
            $post = get_post( intval( $_GET['id'] ) );
            $this->json_response( $post );
        } else {
            $this->json_response( [ 'error' => 'Invalid action' ], 400 );
        }
    }

    /**
     * Handle SPA page content requests
     * Returns ONLY the page content without header/footer
     */
    private function handle_spa_page() {
        // TODO: (FIXME) function not called
        // Get page slug or ID from query params
        $page_slug = isset( $_GET['slug'] ) ? sanitize_title( $_GET['slug'] ) : '';
        $page_id   = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        
        // Debug logging
        error_log('Handle SPA Page function called');
        error_log('Page Slug: ' . $page_slug . ' Page ID: ' . $page_id);

        if ( empty( $page_slug ) && empty( $page_id ) ) {
            $this->json_response( [ 'error' => 'Page slug or ID required' ], 400 );
            return;
        }

        // Get the page
        if ( $page_id ) {
            $page = get_post( $page_id );
        } else {
            $page = get_page_by_path( $page_slug, OBJECT, 'page' );
        }

        if ( ! $page || $page->post_status !== 'publish' ) {
            $this->json_response( [ 'error' => 'Page not found' ], 404 );
            return;
        }

        // Get the Elementor content
        $content = $this->get_elementor_content( $page->ID );

        // Return JSON with page data
        $this->json_response( [
            'success' => true,
            'page' => [
                'id'      => $page->ID,
                'title'   => get_the_title( $page->ID ),
                'slug'    => $page->post_name,
                'content' => $content,
                'url'     => get_permalink( $page->ID ),
            ]
        ] );
    }

    /**
     * Handle product-related requests
     */
    private function handle_products() {
        switch ( $this->action ) {
            case 'list':
                $this->get_products_list();
                break;
                
            case 'detail':
                $this->get_product_detail();
                break;
                
            case 'category':
                $this->get_products_by_category();
                break;
                
            default:
                $this->json_response( [ 'error' => 'Invalid action' ], 400 );
        }
    }

    /**
     * Get products list with ACF data
     */
    private function get_products_list() {
        $page = isset( $_GET['page'] ) ? intval( $_GET['page'] ) : 1;
        $per_page = isset( $_GET['per_page'] ) ? intval( $_GET['per_page'] ) : 12;
        $orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'date';
        $order = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'DESC';
        
        // Query products (assuming 'product' custom post type)
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'post_status'    => 'publish',
            'orderby'        => $orderby,
            'order'          => $order,
        );
        
        // Add meta query for ACF fields if needed
        if ( isset( $_GET['in_stock'] ) && $_GET['in_stock'] === 'true' ) {
            $args['meta_query'] = array(
                array(
                    'key'     => 'stock_status',
                    'value'   => 'in_stock',
                    'compare' => '=',
                ),
            );
        }
        
        $query = new WP_Query( $args );
        
        $products = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $products[] = $this->format_acf_product_data( get_the_ID() );
            }
            wp_reset_postdata();
        }
        
        $this->json_response( [
            'success'    => true,
            'products'   => $products,
            'pagination' => [
                'current_page' => $page,
                'total_pages'  => $query->max_num_pages,
                'total_items'  => $query->found_posts,
                'per_page'     => $per_page,
            ]
        ] );
    }

    /**
     * Get single product detail with ACF data
     */
    private function get_product_detail() {
        $product_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        $slug = isset( $_GET['slug'] ) ? sanitize_title( $_GET['slug'] ) : '';
        
        if ( $product_id ) {
            $product = get_post( $product_id );
        } elseif ( $slug ) {
            $product = get_page_by_path( $slug, OBJECT, 'product' );
        } else {
            $this->json_response( [ 'error' => 'Product ID or slug required' ], 400 );
            return;
        }
        
        if ( ! $product || $product->post_type !== 'product' ) {
            $this->json_response( [ 'error' => 'Product not found' ], 404 );
            return;
        }
        
        $product_data = $this->get_acf_product_data( $product->ID );
        
        $this->json_response( [
            'success' => true,
            'product' => $product_data
        ] );
    }

    /**
     * Get products by category
     */
    private function get_products_by_category() {
        $category = isset( $_GET['category'] ) ? sanitize_text_field( $_GET['category'] ) : '';
        $page = isset( $_GET['page'] ) ? intval( $_GET['page'] ) : 1;
        $per_page = isset( $_GET['per_page'] ) ? intval( $_GET['per_page'] ) : 12;
        
        if ( ! $category ) {
            $this->json_response( [ 'error' => 'Category required' ], 400 );
            return;
        }
        
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_category', // Change to your taxonomy
                    'field'    => 'slug',
                    'terms'    => $category,
                ),
            ),
        );
        
        $query = new WP_Query( $args );
        
        $products = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $products[] = $this->format_acf_product_data( get_the_ID() );
            }
            wp_reset_postdata();
        }
        
        $this->json_response( [
            'success'    => true,
            'category'   => $category,
            'products'   => $products,
            'pagination' => [
                'current_page' => $page,
                'total_pages'  => $query->max_num_pages,
                'total_items'  => $query->found_posts,
            ]
        ] );
    }


    /**
     * Alternative: Return full HTML page fragment
     */
    private function handle_page_content() {
        $page_slug = isset( $_GET['slug'] ) ? sanitize_title( $_GET['slug'] ) : '';
        
        if ( empty( $page_slug ) ) {
            $this->html_response( '<p>Page not found</p>', 404 );
            return;
        }

        $page = get_page_by_path( $page_slug, OBJECT, 'page' );
        
        if ( ! $page ) {
            $this->html_response( '<p>Page not found</p>', 404 );
            return;
        }

        // Return raw HTML
        $content = $this->get_elementor_content( $page->ID );
        $this->html_response( $content );
    }

    /**
     * Helper: Get Elementor content
     */
    private function get_elementor_content( $page_id ) {
        // Check if Elementor is active and page is built with Elementor
        if ( class_exists( '\Elementor\Plugin' ) ) {
            $elementor_instance = \Elementor\Plugin::instance();
            
            // Check if this page uses Elementor
            $document = $elementor_instance->documents->get( $page_id );
            
            if ( $document && $document->is_built_with_elementor() ) {
                // Get Elementor content
                ob_start();
                echo $elementor_instance->frontend->get_builder_content( $page_id, true );
                return ob_get_clean();
            }
        }

        // Fallback to regular WordPress content
        $page = get_post( $page_id );
        $content = apply_filters( 'the_content', $page->post_content );
        return $content;
    }

    /**
     * Helper: Get product data
     */
    private function get_acf_product_data( $product_id ) {
        $product = get_post( $product_id );
        
        if ( ! $product || $product->post_type !== 'product' ) {
            return false;
        }
        
        return $this->format_acf_product_data( $product_id );
    }

    /**
     * Format product data with ACF fields
     */
    private function format_acf_product_data( $product_id ) {
        // Check if ACF is active
        if ( ! function_exists( 'get_field' ) ) {
            return $this->format_basic_product_data( $product_id );
        }

        $product = get_post( $product_id );
        
        // Basic product info
        $data = array(
            'id'          => $product_id,
            'title'       => get_the_title( $product_id ),
            'slug'        => $product->post_name,
            'content'     => apply_filters( 'the_content', $product->post_content ),
            'excerpt'     => get_the_excerpt( $product_id ),
            'url'         => get_permalink( $product_id ),
            'date'        => get_the_date( 'c', $product_id ),
        );

        // ACF Fields - Customize these based on your ACF field setup
        
        // Images
        $featured_image = get_field( 'product_image', $product_id );
        $data['images'] = array(
            'featured' => $this->format_acf_image( $featured_image ),
        );
        
        // If no ACF image, use featured image
        if ( empty( $data['images']['featured'] ) ) {
            $data['images']['featured'] = $this->format_wp_featured_image( $product_id );
        }

        // Specifications (ACF Repeater Field)
        $data['specifications'] = $this->get_acf_specifications( $product_id );

        // Features (ACF Repeater or WYSIWYG)
        $data['features'] = get_field( 'product_features', $product_id );

        // Categories
        $data['categories'] = $this->get_product_categories( $product_id );

        // Downloads (ACF File fields)
        $data['downloads'] = array(
            'manual'    => $this->format_acf_file( get_field( 'product_manual', $product_id ) ),
            'datasheet' => $this->format_acf_file( get_field( 'product_datasheet', $product_id ) ),
        );

        // SEO (if using ACF for SEO instead of Yoast)
        $data['seo'] = array(
            'meta_title'       => get_field( 'seo_title', $product_id ),
            'meta_description' => get_field( 'seo_description', $product_id ),
            'keywords'         => get_field( 'seo_keywords', $product_id ),
        );

        return $data;
    }

    /**
     * Helper: Format basic product data (fallback when ACF is not available)
     */
    private function format_basic_product_data( $product_id ) {
        $product = get_post( $product_id );
        
        return array(
            'id'          => $product_id,
            'title'       => get_the_title( $product_id ),
            'slug'        => $product->post_name,
            'content'     => apply_filters( 'the_content', $product->post_content ),
            'excerpt'     => get_the_excerpt( $product_id ),
            'url'         => get_permalink( $product_id ),
            'image'       => $this->format_wp_featured_image( $product_id ),
            'categories'  => $this->get_product_categories( $product_id ),
            'date'        => get_the_date( 'c', $product_id ),
        );
    }

    /**
     * Get ACF specifications (Repeater field)
     */
    private function get_acf_specifications( $product_id ) {
        if ( ! function_exists( 'get_field' ) ) {
            return array();
        }

        $specs = get_field( 'product_specifications', $product_id );
        
        if ( ! $specs ) {
            return array();
        }

        $formatted_specs = array();
        foreach ( $specs as $spec ) {
            $formatted_specs[] = array(
                'label' => $spec['spec_label'] ?? '',
                'value' => $spec['spec_value'] ?? '',
            );
        }

        return $formatted_specs;
    }

    /**
     * Format ACF image field
     */
    private function format_acf_image( $image ) {
        if ( ! $image ) {
            return null;
        }

        // If image is an array (return format: array)
        if ( is_array( $image ) ) {
            return array(
                'id'     => $image['ID'] ?? null,
                'url'    => $image['url'] ?? '',
                'alt'    => $image['alt'] ?? '',
                'title'  => $image['title'] ?? '',
                'width'  => $image['width'] ?? 0,
                'height' => $image['height'] ?? 0,
                'sizes'  => array(
                    'thumbnail' => $image['sizes']['thumbnail'] ?? '',
                    'medium'    => $image['sizes']['medium'] ?? '',
                    'large'     => $image['sizes']['large'] ?? '',
                    'full'      => $image['url'] ?? '',
                ),
            );
        }

        // If image is an ID (return format: ID)
        if ( is_numeric( $image ) ) {
            return array(
                'id'     => $image,
                'url'    => wp_get_attachment_url( $image ),
                'alt'    => get_post_meta( $image, '_wp_attachment_image_alt', true ),
                'title'  => get_the_title( $image ),
                'sizes'  => array(
                    'thumbnail' => wp_get_attachment_image_url( $image, 'thumbnail' ),
                    'medium'    => wp_get_attachment_image_url( $image, 'medium' ),
                    'large'     => wp_get_attachment_image_url( $image, 'large' ),
                    'full'      => wp_get_attachment_url( $image ),
                ),
            );
        }

        // If image is a URL (return format: URL)
        if ( is_string( $image ) ) {
            return array(
                'url' => $image,
            );
        }

        return null;
    }

    /**
     * Format WordPress featured image
     */
    private function format_wp_featured_image( $product_id ) {
        $image_id = get_post_thumbnail_id( $product_id );
        
        if ( ! $image_id ) {
            return null;
        }

        return array(
            'id'     => $image_id,
            'url'    => get_the_post_thumbnail_url( $product_id, 'full' ),
            'alt'    => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
            'title'  => get_the_title( $image_id ),
            'sizes'  => array(
                'thumbnail' => get_the_post_thumbnail_url( $product_id, 'thumbnail' ),
                'medium'    => get_the_post_thumbnail_url( $product_id, 'medium' ),
                'large'     => get_the_post_thumbnail_url( $product_id, 'large' ),
                'full'      => get_the_post_thumbnail_url( $product_id, 'full' ),
            ),
        );
    }

    /**
     * Format ACF file field
     */
    private function format_acf_file( $file ) {
        if ( ! $file ) {
            return null;
        }

        // If file is an array (return format: array)
        if ( is_array( $file ) ) {
            return array(
                'id'       => $file['ID'] ?? null,
                'url'      => $file['url'] ?? '',
                'title'    => $file['title'] ?? '',
                'filename' => $file['filename'] ?? '',
                'filesize' => $file['filesize'] ?? 0,
                'mime_type' => $file['mime_type'] ?? '',
            );
        }

        // If file is an ID
        if ( is_numeric( $file ) ) {
            return array(
                'id'       => $file,
                'url'      => wp_get_attachment_url( $file ),
                'title'    => get_the_title( $file ),
                'filename' => basename( get_attached_file( $file ) ),
            );
        }

        // If file is a URL
        if ( is_string( $file ) ) {
            return array(
                'url' => $file,
            );
        }

        return null;
    }

    /**
     * Helper: Get product categories
     */
    private function get_product_categories( $product_id ) {
        $terms = get_the_terms( $product_id, 'product_category' );
        
        if ( ! $terms || is_wp_error( $terms ) ) {
            return array();
        }

        $categories = array();
        foreach ( $terms as $term ) {
            $categories[] = array(
                'id'   => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'url'  => get_term_link( $term ),
            );
        }

        return $categories;
    }

    /**
     * Helper: Inject product data into template
     */
    private function inject_product_data( $content, $product_data ) {
        // Replace placeholders in content
        $replacements = array(
            '{{product_name}}'        => $product_data['name'],
            '{{product_price}}'       => '$' . number_format( $product_data['price'], 2 ),
            '{{product_description}}' => $product_data['description'],
            '{{product_image}}'       => $product_data['image'],
            '{{product_id}}'          => $product_data['id'],
        );
        
        foreach ( $replacements as $placeholder => $value ) {
            $content = str_replace( $placeholder, $value, $content );
        }
        
        return $content;
    }

    /**
     * Helper: Get page meta data
     */
    private function get_page_meta( $page_id ) {
        return array(
            'description' => get_post_meta( $page_id, '_yoast_wpseo_metadesc', true ),
            'keywords'    => get_post_meta( $page_id, '_yoast_wpseo_focuskw', true ),
            'og_image'    => get_post_meta( $page_id, '_yoast_wpseo_opengraph-image', true ),
        );
    }

    /**
     * Response helpers
     */
    private function json_response( $data, $status = 200 ) {
        status_header( $status );
        wp_send_json( $data );
    }

    private function html_response( $html, $status = 200 ) {
        status_header( $status );
        header( 'Content-Type: text/html; charset=UTF-8' );
        echo $html;
        exit;
    }

    private function render_page( $template, $data = [] ) {
        extract( $data );
        include ROUTER_PATH . 'templates/' . $template . '.php';
    }
}