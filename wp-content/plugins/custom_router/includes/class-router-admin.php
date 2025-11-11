<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class RouterAdmin {
    
    private $router;

    public function __construct( $router ) {
        $this->router = $router;
        add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
        add_action( 'admin_post_add_route', [ $this, 'handle_add_route' ] );
        add_action( 'admin_post_delete_route', [ $this, 'handle_delete_route' ] );
        add_action( 'admin_post_delete_all_routes', [ $this, 'handle_delete_all_routes' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

        // AJAX handlers
        add_action( 'wp_ajax_verify_rewrite_rules', [ $this, 'ajax_verify_rewrite_rules' ] );
        add_action( 'wp_ajax_flush_rewrite_rules', [ $this, 'ajax_flush_rewrite_rules' ] );
    }

    public function add_admin_page() {
        add_menu_page(
            'Router',
            'Router',
            'manage_options',
            'router',
            [ $this, 'render_admin_page' ],
            'dashicons-randomize',
            81
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts( $hook ) {
        // Only load on our plugin page
        if ( $hook !== 'toplevel_page_router' ) {
            return;
        }

        wp_enqueue_style(
            'router-admin-css',
            ROUTER_URL . 'assets/admin-style.css',
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'router-admin-js',
            ROUTER_URL . 'assets/admin-script.js',
            array( 'jquery' ),
            '1.0.0',
            true
        );

        wp_localize_script( 'router-admin-js', 'RouterAdmin', array(
            'confirmDelete' => __( 'Are you sure you want to delete this route?', 'custom-router' ),
            'confirmDeleteAll' => __( 'Are you sure you want to delete ALL routes? This cannot be undone!', 'custom-router' ),
            'nonce' => wp_create_nonce( 'router_admin_nonce' ),
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        ) );
    }

    /**
     * Render the admin page
     */
    public function render_admin_page() {
        $routes = $this->router->get_routes();
        $verification = $this->router->verify_rewrite_rules();

        // Get messages from URL params
        $message = isset( $_GET['message'] ) ? sanitize_text_field( $_GET['message'] ) : '';
        $error = isset( $_GET['error'] ) ? sanitize_text_field( $_GET['error'] ) : '';

        ?>
        <div class="wrap router-admin-wrap">
            <h1 class="wp-heading-inline">
                <?php _e( 'Custom Router - Manage Routes', 'custom-router' ); ?>
            </h1>

            <a href="#add-route-form" class="page-title-action">
                <?php _e( 'Add New Route', 'custom-router' ); ?>
            </a>

            <hr class="wp-header-end">

            <?php if ( $message ) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php echo esc_html( $this->get_message_text( $message ) ); ?></p>
                </div>
            <?php endif; ?>

            <?php if ( $error ) : ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php echo esc_html( $this->get_error_text( $error ) ); ?></p>
                </div>
            <?php endif; ?>

            <!-- Rewrite Rules Verification Status -->
            <div class="router-section router-verification-section">
                <div class="section-header">
                    <h2><?php _e( 'Rewrite Rules Status', 'custom-router' ); ?></h2>
                    <div class="verification-actions">
                        <button type="button" id="check-rules-btn" class="button">
                            <span class="dashicons dashicons-update"></span>
                            <?php _e( 'Check Rules', 'custom-router' ); ?>
                        </button>
                        <button type="button" id="flush-rules-btn" class="button">
                            <span class="dashicons dashicons-admin-tools"></span>
                            <?php _e( 'Flush Rules', 'custom-router' ); ?>
                        </button>
                    </div>
                </div>

                <div id="verification-status" class="verification-card verification-<?php echo esc_attr( $verification['status'] ); ?>">
                    <div class="verification-header">
                        <span class="verification-icon">
                            <?php if ( $verification['status'] === 'success' ) : ?>
                                <span class="dashicons dashicons-yes-alt"></span>
                            <?php elseif ( $verification['status'] === 'warning' ) : ?>
                                <span class="dashicons dashicons-warning"></span>
                            <?php else : ?>
                                <span class="dashicons dashicons-dismiss"></span>
                            <?php endif; ?>
                        </span>
                        <div class="verification-message">
                            <strong><?php echo esc_html( $verification['message'] ); ?></strong>
                            <p class="description">
                                <?php
                                if ( $verification['status'] !== 'success' ) {
                                    echo __( 'Click "Flush Rules" to regenerate WordPress rewrite rules.', 'custom-router' );
                                } else {
                                    echo __( 'All rewrite rules are properly registered.', 'custom-router' );
                                }
                                ?>
                            </p>
                        </div>
                    </div>

                    <div class="verification-details">
                        <h3><?php _e( 'Rule Details', 'custom-router' ); ?></h3>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php _e( 'Status', 'custom-router' ); ?></th>
                                    <th><?php _e( 'Pattern', 'custom-router' ); ?></th>
                                    <th><?php _e( 'Description', 'custom-router' ); ?></th>
                                    <th><?php _e( 'Query String', 'custom-router' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $verification['rules'] as $rule ) : ?>
                                    <tr class="<?php echo $rule['registered'] ? 'rule-registered' : 'rule-missing'; ?>">
                                        <td>
                                            <?php if ( $rule['registered'] ) : ?>
                                                <span class="dashicons dashicons-yes" style="color: #46b450;"></span>
                                            <?php else : ?>
                                                <span class="dashicons dashicons-no" style="color: #dc3232;"></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><code><?php echo esc_html( $rule['pattern'] ); ?></code></td>
                                        <td><?php echo esc_html( $rule['description'] ); ?></td>
                                        <td>
                                            <?php if ( $rule['query'] ) : ?>
                                                <code><?php echo esc_html( $rule['query'] ); ?></code>
                                            <?php else : ?>
                                                <em><?php _e( 'Not registered', 'custom-router' ); ?></em>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (isset($verification['diagnostics'])) : ?>
                    <!-- Diagnostics Section (Collapsible) -->
                    <details style="margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #dcdcde; border-radius: 4px;">
                        <summary style="cursor: pointer; font-weight: 600; user-select: none;">
                            <span class="dashicons dashicons-admin-tools" style="vertical-align: middle;"></span>
                            <?php _e( 'Show Advanced Diagnostics', 'custom-router' ); ?>
                        </summary>
                        <div style="margin-top: 15px;">
                            <table class="widefat" style="margin-top: 10px;">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;"><?php _e( 'Init Hook Fired', 'custom-router' ); ?></th>
                                        <td><?php echo $verification['diagnostics']['init_hook_fired'] ? '✓ Yes' : '✗ No'; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'WP Rewrite Object Exists', 'custom-router' ); ?></th>
                                        <td><?php echo $verification['diagnostics']['wp_rewrite_exists'] ? '✓ Yes' : '✗ No'; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'Permalink Structure', 'custom-router' ); ?></th>
                                        <td>
                                            <code><?php echo esc_html($verification['diagnostics']['permalink_structure'] ?: 'Default (No pretty permalinks)'); ?></code>
                                            <?php if (empty($verification['diagnostics']['permalink_structure'])) : ?>
                                                <br><strong style="color: #d63638;">⚠ Warning: Pretty permalinks are required for custom rewrite rules!</strong>
                                                <br><a href="<?php echo admin_url('options-permalink.php'); ?>" class="button button-small" style="margin-top: 5px;">Enable Pretty Permalinks</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'Database Rules Count', 'custom-router' ); ?></th>
                                        <td><?php echo esc_html($verification['diagnostics']['db_rules_count']); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'WP Rewrite Rules Count', 'custom-router' ); ?></th>
                                        <td><?php echo esc_html($verification['diagnostics']['wp_rewrite_rules_count']); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e( 'Custom Routes Stored', 'custom-router' ); ?></th>
                                        <td><?php echo esc_html($verification['diagnostics']['custom_routes_count']); ?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <h4 style="margin-top: 20px;"><?php _e( 'All WordPress Rewrite Rules (First 10)', 'custom-router' ); ?></h4>
                            <div style="max-height: 200px; overflow-y: auto; background: #fff; padding: 10px; border: 1px solid #dcdcde; border-radius: 3px;">
                                <pre style="margin: 0; font-size: 11px;"><?php
                                    $db_rules = $verification['diagnostics']['all_db_rules'];
                                    if (!empty($db_rules)) {
                                        $count = 0;
                                        foreach ($db_rules as $pattern => $query) {
                                            if ($count++ >= 10) break;
                                            echo esc_html($pattern) . ' => ' . esc_html($query) . "\n";
                                        }
                                        if (count($db_rules) > 10) {
                                            echo "\n... and " . (count($db_rules) - 10) . " more rules";
                                        }
                                    } else {
                                        echo 'No rules found in database';
                                    }
                                ?></pre>
                            </div>
                        </div>
                    </details>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="router-stats-card">
                <div class="stat-item">
                    <span class="stat-label"><?php _e( 'Total Routes', 'custom-router' ); ?></span>
                    <span class="stat-value"><?php echo count( $routes ); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label"><?php _e( 'Active', 'custom-router' ); ?></span>
                    <span class="stat-value stat-success"><?php echo count( $routes ); ?></span>
                </div>
                <div class="stat-item">
                    <a href="<?php echo esc_url( admin_url( 'options-permalink.php' ) ); ?>" class="button">
                        <?php _e( 'Flush Rewrite Rules', 'custom-router' ); ?>
                    </a>
                </div>
            </div>

            <!-- Existing Routes Table -->
            <div class="router-section">
                <div class="section-header">
                    <h2><?php _e( 'Existing Routes', 'custom-router' ); ?></h2>
                    <?php if ( ! empty( $routes ) ) : ?>
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="delete-all-form">
                            <input type="hidden" name="action" value="delete_all_routes">
                            <?php wp_nonce_field( 'delete_all_routes_nonce', 'nonce' ); ?>
                            <button type="submit" class="button button-link-delete" id="delete-all-routes">
                                <?php _e( 'Delete All Routes', 'custom-router' ); ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if ( $routes ) : ?>
                    <table class="wp-list-table widefat fixed striped routes-table">
                        <thead>
                            <tr>
                                <th class="column-slug"><?php _e( 'Slug', 'custom-router' ); ?></th>
                                <th class="column-regex"><?php _e( 'Regex Pattern', 'custom-router' ); ?></th>
                                <th class="column-query"><?php _e( 'Query String', 'custom-router' ); ?></th>
                                <th class="column-example"><?php _e( 'Example URL', 'custom-router' ); ?></th>
                                <th class="column-actions"><?php _e( 'Actions', 'custom-router' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $routes as $slug => $route ) : ?>
                                <tr>
                                    <td class="column-slug">
                                        <strong><?php echo esc_html( $slug ); ?></strong>
                                    </td>
                                    <td class="column-regex">
                                        <code><?php echo esc_html( $route['regex'] ); ?></code>
                                    </td>
                                    <td class="column-query">
                                        <code><?php echo esc_html( $route['query'] ); ?></code>
                                    </td>
                                    <td class="column-example">
                                        <?php echo $this->generate_example_url( $route['regex'] ); ?>
                                    </td>
                                    <td class="column-actions">
                                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="delete-route-form" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_route">
                                            <input type="hidden" name="slug" value="<?php echo esc_attr( $slug ); ?>">
                                            <?php wp_nonce_field( 'delete_route_nonce', 'nonce' ); ?>
                                            <button type="submit" class="button button-small button-link-delete delete-route-btn">
                                                <?php _e( 'Delete', 'custom-router' ); ?>
                                            </button>
                                        </form>
                                        
                                        <button type="button" class="button button-small test-route-btn" data-slug="<?php echo esc_attr( $slug ); ?>" data-regex="<?php echo esc_attr( $route['regex'] ); ?>">
                                            <?php _e( 'Test', 'custom-router' ); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="no-routes-message">
                        <p><?php _e( 'No custom routes found. Add your first route below!', 'custom-router' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Add New Route Form -->
            <div class="router-section" id="add-route-form">
                <h2><?php _e( 'Add New Route', 'custom-router' ); ?></h2>
                
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="route-form">
                    <input type="hidden" name="action" value="add_route">
                    <?php wp_nonce_field( 'add_route_nonce', 'nonce' ); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="slug">
                                    <?php _e( 'Slug', 'custom-router' ); ?>
                                    <span class="required">*</span>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="slug" id="slug" class="regular-text" required>
                                <p class="description">
                                    <?php _e( 'A unique identifier for this route (lowercase, no spaces)', 'custom-router' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="regex">
                                    <?php _e( 'Regex Pattern', 'custom-router' ); ?>
                                    <span class="required">*</span>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="regex" id="regex" class="large-text code" required>
                                <p class="description">
                                    <?php _e( 'Example: <code>shop/products/([^/]+)/?$</code>', 'custom-router' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="query">
                                    <?php _e( 'Query String', 'custom-router' ); ?>
                                    <span class="required">*</span>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="query" id="query" class="large-text code" required>
                                <p class="description">
                                    <?php _e( 'Example: <code>index.php?route=shop&action=$matches[1]</code>', 'custom-router' ); ?>
                                </p>
                            </td>
                        </tr>
                    </table>

                    <!-- Examples Accordion -->
                    <div class="route-examples">
                        <h3><?php _e( 'Common Route Patterns', 'custom-router' ); ?></h3>
                        <details>
                            <summary><?php _e( 'API Endpoints', 'custom-router' ); ?></summary>
                            <pre><code>Regex: api/([^/]+)/([^/]+)/?$
Query: index.php?route=$matches[1]&action=$matches[2]
URL:   /api/posts/list</code></pre>
                        </details>
                        <details>
                            <summary><?php _e( 'Product Pages', 'custom-router' ); ?></summary>
                            <pre><code>Regex: shop/([^/]+)/([^/]+)/?$
Query: index.php?route=shop&category=$matches[1]&product=$matches[2]
URL:   /shop/electronics/laptop</code></pre>
                        </details>
                        <details>
                            <summary><?php _e( 'User Profiles', 'custom-router' ); ?></summary>
                            <pre><code>Regex: user/([^/]+)/?$
Query: index.php?route=user&username=$matches[1]
URL:   /user/johndoe</code></pre>
                        </details>
                    </div>

                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Add Route', 'custom-router' ); ?>">
                    </p>
                </form>
            </div>

            <!-- Test Modal -->
            <div id="test-route-modal" class="router-modal" style="display: none;">
                <div class="router-modal-content">
                    <span class="router-modal-close">&times;</span>
                    <h2><?php _e( 'Test Route', 'custom-router' ); ?></h2>
                    <div id="test-route-result"></div>
                </div>
            </div>

            <!-- Documentation -->
            <div class="router-section router-docs">
                <h2><?php _e( 'Documentation', 'custom-router' ); ?></h2>
                <div class="docs-grid">
                    <div class="doc-card">
                        <h3><?php _e( 'Regex Patterns', 'custom-router' ); ?></h3>
                        <ul>
                            <li><code>([^/]+)</code> - Matches any character except /</li>
                            <li><code>([0-9]+)</code> - Matches numbers only</li>
                            <li><code>([a-z]+)</code> - Matches lowercase letters</li>
                            <li><code>/?$</code> - Optional trailing slash + end</li>
                        </ul>
                    </div>
                    <div class="doc-card">
                        <h3><?php _e( 'Query Variables', 'custom-router' ); ?></h3>
                        <ul>
                            <li><code>$matches[1]</code> - First captured group</li>
                            <li><code>$matches[2]</code> - Second captured group</li>
                            <li><code>route=value</code> - Custom query var</li>
                            <li><code>action=value</code> - Custom query var</li>
                        </ul>
                    </div>
                    <div class="doc-card">
                        <h3><?php _e( 'Best Practices', 'custom-router' ); ?></h3>
                        <ul>
                            <li>Use descriptive slugs</li>
                            <li>Test patterns before deploying</li>
                            <li>Flush rewrite rules after changes</li>
                            <li>Keep patterns specific to avoid conflicts</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Handle adding a new route
     */
    public function handle_add_route() {
        if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'add_route_nonce' ) ) {
            wp_die( __( 'Unauthorized request', 'custom-router' ), 403 );
        }

        $slug  = isset( $_POST['slug'] ) ? sanitize_title( $_POST['slug'] ) : '';
        $regex = isset( $_POST['regex'] ) ? sanitize_text_field( $_POST['regex'] ) : '';
        $query = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';

        // Validation
        if ( empty( $slug ) || empty( $regex ) || empty( $query ) ) {
            wp_redirect( admin_url( 'admin.php?page=router&error=empty_fields' ) );
            exit;
        }

        // Check if slug already exists
        $routes = $this->router->get_routes();
        if ( isset( $routes[ $slug ] ) ) {
            wp_redirect( admin_url( 'admin.php?page=router&error=duplicate_slug' ) );
            exit;
        }

        // Add the route
        $this->router->add_route( $slug, $regex, $query );

        wp_redirect( admin_url( 'admin.php?page=router&message=route_added' ) );
        exit;
    }

    /**
     * Handle deleting a single route
     */
    public function handle_delete_route() {
        if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_route_nonce' ) ) {
            wp_die( __( 'Unauthorized request', 'custom-router' ), 403 );
        }

        $slug = isset( $_POST['slug'] ) ? sanitize_title( $_POST['slug'] ) : '';

        if ( empty( $slug ) ) {
            wp_redirect( admin_url( 'admin.php?page=router&error=invalid_slug' ) );
            exit;
        }

        // Delete the route
        $result = $this->router->delete_route( $slug );

        if ( $result ) {
            wp_redirect( admin_url( 'admin.php?page=router&message=route_deleted' ) );
        } else {
            wp_redirect( admin_url( 'admin.php?page=router&error=delete_failed' ) );
        }
        exit;
    }

    /**
     * Handle deleting all routes
     */
    public function handle_delete_all_routes() {
        if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_all_routes_nonce' ) ) {
            wp_die( __( 'Unauthorized request', 'custom-router' ), 403 );
        }

        // Delete all routes
        $result = $this->router->delete_all_routes();

        if ( $result ) {
            wp_redirect( admin_url( 'admin.php?page=router&message=all_routes_deleted' ) );
        } else {
            wp_redirect( admin_url( 'admin.php?page=router&error=delete_all_failed' ) );
        }
        exit;
    }

    /**
     * Generate example URL from regex pattern
     */
    private function generate_example_url( $regex ) {
        $home_url = home_url();
        
        // Simple pattern matching for examples
        $example = preg_replace( '/\([^\)]+\)/', '<span class="example-segment">segment</span>', $regex );
        $example = str_replace( '/?$', '', $example );
        
        return '<code class="example-url">' . esc_html( $home_url ) . '/<span class="example-pattern">' . $example . '</span></code>';
    }

    /**
     * Get message text
     */
    private function get_message_text( $message ) {
        $messages = array(
            'route_added'        => __( 'Route added successfully!', 'custom-router' ),
            'route_deleted'      => __( 'Route deleted successfully!', 'custom-router' ),
            'all_routes_deleted' => __( 'All routes deleted successfully!', 'custom-router' ),
        );

        return isset( $messages[ $message ] ) ? $messages[ $message ] : $message;
    }

    /**
     * Get error text
     */
    private function get_error_text( $error ) {
        $errors = array(
            'empty_fields'     => __( 'All fields are required.', 'custom-router' ),
            'duplicate_slug'   => __( 'A route with this slug already exists.', 'custom-router' ),
            'invalid_slug'     => __( 'Invalid route slug.', 'custom-router' ),
            'delete_failed'    => __( 'Failed to delete route.', 'custom-router' ),
            'delete_all_failed' => __( 'Failed to delete all routes.', 'custom-router' ),
        );

        return isset( $errors[ $error ] ) ? $errors[ $error ] : $error;
    }

    /**
     * AJAX handler to verify rewrite rules
     */
    public function ajax_verify_rewrite_rules() {
        check_ajax_referer( 'router_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $verification = $this->router->verify_rewrite_rules();
        wp_send_json_success( $verification );
    }

    /**
     * AJAX handler to flush rewrite rules
     */
    public function ajax_flush_rewrite_rules() {
        check_ajax_referer( 'router_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        // Flush the rewrite rules
        flush_rewrite_rules( true );

        // Verify after flushing
        $verification = $this->router->verify_rewrite_rules();

        wp_send_json_success( array(
            'message' => 'Rewrite rules have been flushed and regenerated.',
            'verification' => $verification
        ) );
    }
}