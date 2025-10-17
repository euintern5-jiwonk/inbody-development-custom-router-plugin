<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Router {

    private $routes;

    public function __construct() {
        $this->routes = get_option( 'custom_routes', [] );

        add_filter( 'query_vars', [ $this, 'register_query_vars' ] );
        add_filter( 'rewrite_rules_array', [ $this, 'add_rewrite_rules' ] );
        add_action( 'template_redirect', [ $this, 'handle_request' ] );
    }

    public function register_query_vars( $vars ) {
        $vars[] = 'route';
        $vars[] = 'action';
        return $vars;
    }

    public function register_rewrite_rules() {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    public function add_rewrite_rules( $rules ) {
        $new_rules = [];

        // Default route (example)
        $new_rules['api/([^/]+)/([^/]+)/?$'] = 'index.php?route=$matches[1]&action=$matches[2]';

        // Custom routes from admin
        if ( ! empty( $this->routes ) ) {
            foreach ( $this->routes as $slug => $route ) {
                $regex = $route['regex'];
                $query = $route['query'];
                $new_rules[ $regex ] = $query;
            }
        }

        return $new_rules + $rules;
    }

    public function handle_request() {
        $route  = get_query_var( 'route' );
        $action = get_query_var( 'action' );

        if ( $route && $action ) {
            $handler = new RouteHandler( $route, $action );
            $handler->dispatch();
            exit;
        }
    }

    public function add_route( $slug, $regex, $query ) {
        $this->routes[ $slug ] = [
            'regex' => $regex,
            'query' => $query
        ];
        update_option( 'custom_routes', $this->routes );
        flush_rewrite_rules();
    }

    public function get_routes() {
        return $this->routes;
    }
}
