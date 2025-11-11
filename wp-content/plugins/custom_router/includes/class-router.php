<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Router {

    private $routes;

    public function __construct() {
        $this->routes = get_option( 'custom_routes', [] );

        add_filter( 'query_vars', [ $this, 'register_query_vars' ] );
        add_action( 'init', [ $this, 'register_custom_rewrite_rules' ] );
        add_action( 'template_redirect', [ $this, 'handle_request' ] );
    }

    // Don't forget to register 'slug' and 'id' as query vars
    public function register_query_vars( $vars ) {
        $vars[] = 'route';
        $vars[] = 'action';
        $vars[] = 'slug';
        $vars[] = 'id';
        return $vars;
    }

    /**
     * Register custom rewrite rules using add_rewrite_rule()
     * This is the WordPress standard way - rules are added on every page load
     */
    public function register_custom_rewrite_rules() {
        // Generic API route for backwards compatibility
        add_rewrite_rule(
            '^api/([^/]+)/([^/]+)/?$',
            'index.php?route=$matches[1]&action=$matches[2]',
            'top'
        );

        // SPA page loading route (won't be blocked by content blockers)
        add_rewrite_rule(
            '^wp-spa/load/?$',
            'index.php?route=spa&action=load',
            'top'
        );

        // SPA page by slug
        add_rewrite_rule(
            '^spa-page/([^/]+)/?$',
            'index.php?route=spa&action=load&slug=$matches[1]',
            'top'
        );

        // SPA page by ID
        add_rewrite_rule(
            '^spa-page-id/([0-9]+)/?$',
            'index.php?route=spa&action=load&id=$matches[1]',
            'top'
        );

        // Custom routes from admin
        if ( ! empty( $this->routes ) ) {
            foreach ( $this->routes as $slug => $route ) {
                add_rewrite_rule(
                    '^' . $route['regex'],
                    $route['query'],
                    'top'
                );
            }
        }

        error_log('Custom rewrite rules registered');
    }

    /**
     * Legacy method - kept for backwards compatibility
     * Now we use add_rewrite_rule() in init hook instead
     */
    public function register_rewrite_rules() {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    public function handle_request() {
        $route  = get_query_var( 'route' );
        $action = get_query_var( 'action' );

        // Debug logging
        error_log('Router handle_request called');
        error_log('Route: ' . $route . ', Action: ' . $action);
        error_log('Request URI: ' . $_SERVER['REQUEST_URI']);

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

    public function get_route( $slug ) {
        return isset( $this->routes[ $slug ] ) ? $this->routes[ $slug ] : false;
    }

    public function get_routes() {
        return $this->routes;
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
