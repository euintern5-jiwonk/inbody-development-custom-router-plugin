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
    }

    public function activate() {
        $this->router->register_rewrite_rules();
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
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
            'apiEndpoint'     => home_url('/api/spa/load'),
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
}

CustomRouterPlugin::get_instance();