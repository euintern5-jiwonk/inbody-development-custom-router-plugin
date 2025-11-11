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
        error_log('=== Starting to register custom rewrite rules ===');

        // Generic API route for backwards compatibility
        add_rewrite_rule(
            '^api/([^/]+)/([^/]+)/?$',
            'index.php?route=$matches[1]&action=$matches[2]',
            'top'
        );

        // load SPA page by slug
        add_rewrite_rule(
            '^wp-spa/load/([^/]+)/?$',
            'index.php?route=spa&action=load&slug=$matches[1]',
            'top'
        );

        // load SPA page by ID
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
                error_log('Added custom route: ' . $slug . ' => ' . $route['regex']);
            }
        }

        error_log('=== Finished registering custom rewrite rules ===');
    }

    /**
     * Get diagnostic information about rewrite rules
     * This helps debug why rules might not be registering
     *
     * @return array Diagnostic information
     */
    public function get_diagnostics() {
        global $wp_rewrite;

        $wp_rules_db = get_option('rewrite_rules');
        $wp_rules_obj = $wp_rewrite->wp_rewrite_rules();

        $diagnostics = [
            'init_hook_fired' => did_action('init') > 0,
            'wp_rewrite_exists' => isset($wp_rewrite),
            'db_rules_count' => is_array($wp_rules_db) ? count($wp_rules_db) : 0,
            'wp_rewrite_rules_count' => is_array($wp_rules_obj) ? count($wp_rules_obj) : 0,
            'permalink_structure' => get_option('permalink_structure'),
            'custom_routes_count' => count($this->routes),
            'all_db_rules' => $wp_rules_db ?: [],
            'all_wp_rewrite_rules' => $wp_rules_obj ?: []
        ];

        return $diagnostics;
    }

    /**
     * Verify that our custom rules exist in WordPress's rewrite rules
     * Returns array with status information
     *
     * @return array Status information about rewrite rules
     */
    public function verify_rewrite_rules() {
        global $wp_rewrite;

        $wp_rules = get_option('rewrite_rules');

        // Also check wp_rewrite object which has rules before they're flushed
        $wp_rewrite_rules = $wp_rewrite->wp_rewrite_rules();

        if (!$wp_rules) {
            return [
                'status' => 'error',
                'message' => 'No rewrite rules found in database! Permalink structure: ' . get_option('permalink_structure'),
                'found_count' => 0,
                'total_count' => 0,
                'rules' => [],
                'diagnostics' => $this->get_diagnostics()
            ];
        }

        $our_patterns = [
            'api/([^/]+)/([^/]+)/?$' => 'Generic API endpoint',
            'wp-spa/load/([^/]+)/?$' => 'SPA page loader by slug',
            'spa-page-id/([0-9]+)/?$' => 'SPA page loader by ID'
        ];

        $rules_status = [];
        $found_count = 0;

        foreach ($our_patterns as $pattern => $description) {
            // WordPress stores rules with ^ prefix, so check both with and without
            $pattern_with_caret = '^' . $pattern;
            $is_registered_db = isset($wp_rules[$pattern]) || isset($wp_rules[$pattern_with_caret]);
            $is_registered_obj = isset($wp_rewrite_rules[$pattern]) || isset($wp_rewrite_rules[$pattern_with_caret]);

            if ($is_registered_db) {
                $found_count++;
            }

            // Get the actual query string from whichever pattern exists
            $query = null;
            if (isset($wp_rules[$pattern])) {
                $query = $wp_rules[$pattern];
            } elseif (isset($wp_rules[$pattern_with_caret])) {
                $query = $wp_rules[$pattern_with_caret];
            } elseif (isset($wp_rewrite_rules[$pattern])) {
                $query = $wp_rewrite_rules[$pattern];
            } elseif (isset($wp_rewrite_rules[$pattern_with_caret])) {
                $query = $wp_rewrite_rules[$pattern_with_caret];
            }

            $rules_status[] = [
                'pattern' => $pattern_with_caret, // Show the pattern as WordPress stores it
                'description' => $description,
                'registered' => $is_registered_db,
                'registered_in_memory' => $is_registered_obj,
                'query' => $query
            ];
        }

        // Add custom routes from database
        if (!empty($this->routes)) {
            foreach ($this->routes as $slug => $route) {
                $pattern = $route['regex'];
                // Check both with and without ^ prefix
                $pattern_with_caret = (strpos($pattern, '^') === 0) ? $pattern : '^' . $pattern;
                $is_registered_db = isset($wp_rules[$pattern]) || isset($wp_rules[$pattern_with_caret]);
                $is_registered_obj = isset($wp_rewrite_rules[$pattern]) || isset($wp_rewrite_rules[$pattern_with_caret]);

                if ($is_registered_db) {
                    $found_count++;
                }

                // Get the actual query string from whichever pattern exists
                $query = null;
                if (isset($wp_rules[$pattern])) {
                    $query = $wp_rules[$pattern];
                } elseif (isset($wp_rules[$pattern_with_caret])) {
                    $query = $wp_rules[$pattern_with_caret];
                } elseif (isset($wp_rewrite_rules[$pattern])) {
                    $query = $wp_rewrite_rules[$pattern];
                } elseif (isset($wp_rewrite_rules[$pattern_with_caret])) {
                    $query = $wp_rewrite_rules[$pattern_with_caret];
                }

                $rules_status[] = [
                    'pattern' => $pattern_with_caret,
                    'description' => 'Custom route: ' . $slug,
                    'registered' => $is_registered_db,
                    'registered_in_memory' => $is_registered_obj,
                    'query' => $query
                ];
            }
        }

        $total_rules = count($our_patterns) + count($this->routes);

        if ($found_count === 0) {
            $status = 'error';
            $message = 'No custom rules found in WordPress database!';
        } elseif ($found_count < $total_rules) {
            $status = 'warning';
            $message = "Some rules are missing ({$found_count}/{$total_rules} registered). Flush rewrite rules to fix.";
        } else {
            $status = 'success';
            $message = "All custom rewrite rules are registered ({$found_count}/{$total_rules})!";
        }

        return [
            'status' => $status,
            'message' => $message,
            'found_count' => $found_count,
            'total_count' => $total_rules,
            'rules' => $rules_status,
            'diagnostics' => $this->get_diagnostics()
        ];
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
