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
            case 'spa':
                $this->handle_spa_page();
                break;
                
            case 'products':
                $this->handle_products();
                break;
                
            default:
                $this->json_response( [ 'error' => 'Route not found' ], 404 );
        }
    }

    /**
     * Handle SPA page loading
     */
    private function handle_spa_page() {
        $page_slug = isset( $_GET['slug'] ) ? sanitize_title( $_GET['slug'] ) : '';
        $page_id   = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        
        // Get additional parameters
        $product_id = isset( $_GET['product_id'] ) ? intval( $_GET['product_id'] ) : 0;

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

        // Get content
        $content = $this->get_elementor_content( $page->ID );
        
        // If this is a product detail page, inject product data
        if ( $product_id && $page_slug === 'product-template' ) {
            $product_data = $this->get_product_data( $product_id );
            $content = $this->inject_product_data( $content, $product_data );
        }

        // Return response
        $this->json_response( [
            'success' => true,
            'page' => [
                'id'      => $page->ID,
                'title'   => get_the_title( $page->ID ),
                'slug'    => $page->post_name,
                'content' => $content,
                'url'     => get_permalink( $page->ID ),
                'meta'    => $this->get_page_meta( $page->ID ),
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
     * Get products list
     */
    private function get_products_list() {
        $page = isset( $_GET['page'] ) ? intval( $_GET['page'] ) : 1;
        $per_page = isset( $_GET['per_page'] ) ? intval( $_GET['per_page'] ) : 12;
        
        // Query WooCommerce products (or custom post type)
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'post_status'    => 'publish',
        );
        
        $query = new WP_Query( $args );
        
        $products = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $products[] = $this->format_product_data( get_the_ID() );
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
            ]
        ] );
    }

    /**
     * Get single product detail
     */
    private function get_product_detail() {
        $product_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        
        if ( ! $product_id ) {
            $this->json_response( [ 'error' => 'Product ID required' ], 400 );
            return;
        }
        
        $product_data = $this->get_product_data( $product_id );
        
        if ( ! $product_data ) {
            $this->json_response( [ 'error' => 'Product not found' ], 404 );
            return;
        }
        
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
        
        if ( ! $category ) {
            $this->json_response( [ 'error' => 'Category required' ], 400 );
            return;
        }
        
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 12,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_cat',
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
                $products[] = $this->format_product_data( get_the_ID() );
            }
            wp_reset_postdata();
        }
        
        $this->json_response( [
            'success'  => true,
            'category' => $category,
            'products' => $products,
        ] );
    }

    /**
     * Helper: Get product data
     */
    private function get_product_data( $product_id ) {
        $product = get_post( $product_id );
        
        if ( ! $product || $product->post_type !== 'product' ) {
            return false;
        }
        
        return $this->format_product_data( $product_id );
    }

    /**
     * Helper: Format product data
     */
    private function format_product_data( $product_id ) {
        // TODO: [[FIXME]] not using WooCommerce plugin!!!
        // TODO: create new function that retrieves product info using acf plugin
        $product = wc_get_product( $product_id ); // WooCommerce
        
        if ( ! $product ) {
            return array(
                'id'          => $product_id,
                'title'       => get_the_title( $product_id ),
                'description' => get_the_excerpt( $product_id ),
                'price'       => get_post_meta( $product_id, '_price', true ),
                'image'       => get_the_post_thumbnail_url( $product_id, 'large' ),
                'url'         => get_permalink( $product_id ),
            );
        }
        
        return array(
            'id'            => $product->get_id(),
            'name'          => $product->get_name(),
            'slug'          => $product->get_slug(),
            'description'   => $product->get_description(),
            'short_desc'    => $product->get_short_description(),
            'price'         => $product->get_price(),
            'regular_price' => $product->get_regular_price(),
            'sale_price'    => $product->get_sale_price(),
            'on_sale'       => $product->is_on_sale(),
            'image'         => wp_get_attachment_url( $product->get_image_id() ),
            'gallery'       => $this->get_product_gallery( $product ),
            'categories'    => $this->get_product_categories( $product ),
            'in_stock'      => $product->is_in_stock(),
            'stock_qty'     => $product->get_stock_quantity(),
            'url'           => get_permalink( $product->get_id() ),
        );
    }

    /**
     * Helper: Get product gallery images
     */
    private function get_product_gallery( $product ) {
        $gallery_ids = $product->get_gallery_image_ids();
        $gallery = array();
        
        foreach ( $gallery_ids as $image_id ) {
            $gallery[] = array(
                'id'    => $image_id,
                'url'   => wp_get_attachment_url( $image_id ),
                'thumb' => wp_get_attachment_image_url( $image_id, 'thumbnail' ),
                'alt'   => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
            );
        }
        
        return $gallery;
    }

    /**
     * Helper: Get product categories
     */
    private function get_product_categories( $product ) {
        $category_ids = $product->get_category_ids();
        $categories = array();
        
        foreach ( $category_ids as $cat_id ) {
            $category = get_term( $cat_id, 'product_cat' );
            $categories[] = array(
                'id'   => $category->term_id,
                'name' => $category->name,
                'slug' => $category->slug,
            );
        }
        
        return $categories;
    }

    /**
     * Helper: Get Elementor content
     */
    private function get_elementor_content( $page_id ) {
        if ( class_exists( '\Elementor\Plugin' ) ) {
            $elementor_instance = \Elementor\Plugin::instance();
            $document = $elementor_instance->documents->get( $page_id );
            
            if ( $document && $document->is_built_with_elementor() ) {
                return $elementor_instance->frontend->get_builder_content( $page_id, true );
            }
        }
        
        $page = get_post( $page_id );
        return apply_filters( 'the_content', $page->post_content );
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
}