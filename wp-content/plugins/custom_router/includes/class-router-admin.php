<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class RouterAdmin {
    // TODO: add sidebar menu icons

    private $router;

    public function __construct( $router ) {
        $this->router = $router;
        add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
        add_action( 'admin_post_add_route', [ $this, 'handle_add_route' ] );
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

    public function render_admin_page() {
        $routes = $this->router->get_routes();
        ?>
        <div class="wrap">
            <h1>Custom Router - Manage Routes</h1>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="add_route">
                <?php wp_nonce_field( 'add_route_nonce', 'nonce' ); ?>

                <table class="form-table">
                    <tr>
                        <th><label for="slug">Slug</label></th>
                        <td><input type="text" name="slug" required></td>
                    </tr>
                    <tr>
                        <th><label for="regex">Regex Pattern</label></th>
                        <td><input type="text" name="regex" placeholder="example: custom/([^/]+)/?$" required></td>
                    </tr>
                    <tr>
                        <th><label for="query">Query</label></th>
                        <td><input type="text" name="query" placeholder="index.php?route=$matches[1]" required></td>
                    </tr>
                </table>

                <p><input type="submit" class="button-primary" value="Add Route"></p>
            </form>

            <hr>
            <h2>Existing Routes</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Slug</th>
                        <th>Regex</th>
                        <th>Query</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( $routes ) : ?>
                        <?php foreach ( $routes as $slug => $route ) : ?>
                            <tr>
                                <td><?php echo esc_html( $slug ); ?></td>
                                <td><?php echo esc_html( $route['regex'] ); ?></td>
                                <td><?php echo esc_html( $route['query'] ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3">No custom routes found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function handle_add_route() {
        if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'add_route_nonce' ) ) {
            wp_die( 'Unauthorized request' );
        }

        $slug  = sanitize_title( $_POST['slug'] );
        $regex = sanitize_text_field( $_POST['regex'] );
        $query = sanitize_text_field( $_POST['query'] );

        $this->router->add_route( $slug, $regex, $query );

        wp_redirect( admin_url( 'admin.php?page=router&added=1' ) );
        exit;
    }
}
