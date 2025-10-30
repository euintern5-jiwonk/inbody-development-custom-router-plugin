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
        $vars[] = 'slug';
        $vars[] = 'id';
        return $vars;
    }

    public function register_rewrite_rules() {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    public function add_rewrite_rules( $rules ) {
        $new_rules = [];

        // Default routes
        $new_rules['api/([^/]+)/([^/]+)/?$'] = 'index.php?route=$matches[1]&action=$matches[2]';
        $new_rules['spa-page/([^/]+)/?$'] = 'index.php?route=spa&action=load&slug=$matches[1]';

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

    /**
     * Add a new route
     */
    public function add_route( $slug, $regex, $query ) {
        $this->routes[ $slug ] = [
            'regex' => $regex,
            'query' => $query,
            'created_at' => current_time( 'mysql' ),
        ];
        update_option( 'custom_routes', $this->routes );
        flush_rewrite_rules();
    }

    /**
     * Delete a single route
     * 
     * @param string $slug The route slug to delete
     * @return bool True on success, false on failure
     */
    public function delete_route( $slug ) {
        if ( ! isset( $this->routes[ $slug ] ) ) {
            return false;
        }

        unset( $this->routes[ $slug ] );
        
        $result = update_option( 'custom_routes', $this->routes );
        
        if ( $result ) {
            flush_rewrite_rules();
        }
        
        return $result;
    }

    /**
     * Delete all custom routes
     * 
     * @return bool True on success, false on failure
     */
    public function delete_all_routes() {
        $this->routes = [];
        
        $result = update_option( 'custom_routes', [] );
        
        if ( $result ) {
            flush_rewrite_rules();
        }
        
        return $result;
    }

    /**
     * Get all routes
     * 
     * @return array Array of routes
     */
    public function get_routes() {
        return $this->routes;
    }

    /**
     * Get a specific route by slug
     * 
     * @param string $slug Route slug
     * @return array|false Route data or false if not found
     */
    public function get_route( $slug ) {
        return isset( $this->routes[ $slug ] ) ? $this->routes[ $slug ] : false;
    }

    /**
     * Check if a route exists
     * 
     * @param string $slug Route slug
     * @return bool
     */
    public function route_exists( $slug ) {
        return isset( $this->routes[ $slug ] );
    }

    /**
     * Update an existing route
     * 
     * @param string $slug Route slug
     * @param string $regex New regex pattern
     * @param string $query New query string
     * @return bool True on success, false on failure
     */
    public function update_route( $slug, $regex, $query ) {
        if ( ! isset( $this->routes[ $slug ] ) ) {
            return false;
        }

        $this->routes[ $slug ] = [
            'regex' => $regex,
            'query' => $query,
            'created_at' => $this->routes[ $slug ]['created_at'] ?? current_time( 'mysql' ),
            'updated_at' => current_time( 'mysql' ),
        ];

        $result = update_option( 'custom_routes', $this->routes );
        
        if ( $result ) {
            flush_rewrite_rules();
        }
        
        return $result;
    }
}