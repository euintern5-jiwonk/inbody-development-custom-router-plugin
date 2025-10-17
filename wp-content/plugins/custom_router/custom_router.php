<?php
/**
 * @package custom_router
 */
/*
Plugin Name: custom_router
Description: custom router for InBody Development
Version: 0.0
Author: Jiwon Kang
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('ROUTER_PATH', plugin_dir_path(__FILE__));
define('ROUTER_URL', plugin_dir_path(__FILE__));

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
    }

    public function activate() {
        $this->router->register_rewrite_rules();
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }
}

CustomRouterPlugin::get_instance();