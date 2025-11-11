<?php
/**
 * @package custom_router
 */
/*
Plugin Name: custom_router
Description: custom router for InBody Development with SPA support
Version: 0.1
Author: Jiwon Kang
*/

if (!defined('ABSPATH')) {
    exit;
}

define('ROUTER_PATH', plugin_dir_path(__FILE__));
define('ROUTER_URL', plugin_dir_url(__FILE__));  // Fixed: was using path instead of url

require_once ROUTER_PATH . 'includes/class-router.php';
require_once ROUTER_PATH . 'includes/class-router-admin.php';
require_once ROUTER_PATH . 'includes/class-route-handler.php';

final class CustomRouterPlugin {

    private static $instance = null;
    public $router;
    public $admin;

    public static function get_instance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->router = new Router();
        $this->admin  = new RouterAdmin( $this->router );

        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

        // NEW: Enqueue frontend scripts
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );

        // Register SPA page template
        add_filter( 'theme_page_templates', [ $this, 'register_page_template' ] );
        add_filter( 'template_include', [ $this, 'load_page_template' ] );
    }

    public function activate() {
        // Force hard flush on activation (true = delete and regenerate)
        flush_rewrite_rules(true);
        error_log('Custom Router Plugin Activated - Rewrite rules flushed');
    }

    public function deactivate() {
        // Clean up rewrite rules on deactivation
        flush_rewrite_rules(true);
        error_log('Custom Router Plugin Deactivated - Rewrite rules flushed');
    }

    /**
     * Enqueue SPA router assets
     */
    public function enqueue_frontend_assets() {
        // Only load on pages where you want SPA functionality
        // You can add conditions here

        // Enqueue JS
        wp_enqueue_script(
            'spa-router',
            ROUTER_URL . 'assets/spa-router.js',
            array('jquery'),
            '1.0.0',
            true
        );

        // Pass data to JavaScript
        wp_localize_script('spa-router', 'SPARouterData', array(
            'ajaxUrl'         => admin_url('admin-ajax.php'),
            'apiEndpoint'     => home_url('/wp-spa/load'),
            'siteName'        => get_bloginfo('name'),
            'currentPageSlug' => get_post_field('post_name', get_the_ID()),
            'nonce'           => wp_create_nonce('spa_router_nonce')
        ));

        // Enqueue CSS
        wp_enqueue_style(
            'spa-router',
            ROUTER_URL . 'assets/spa-router.css',
            array(),
            '1.0.0'
        );
    }

    /**
     * Register the SPA page template
     */
    public function register_page_template( $templates ) {
        $templates['spa-page-template.php'] = 'SPA Page Template';
        return $templates;
    }

    /**
     * Load the SPA page template from plugin directory
     */
    public function load_page_template( $template ) {
        global $post;

        // Check if the page has our template assigned
        if ( $post && get_page_template_slug( $post->ID ) === 'spa-page-template.php' ) {
            $plugin_template = ROUTER_PATH . 'templates/spa-page-template.php';

            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }

        return $template;
    }
}

CustomRouterPlugin::get_instance();