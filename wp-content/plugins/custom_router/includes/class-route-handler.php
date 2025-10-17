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
            case 'posts':
                $this->handle_posts();
                break;
            case 'page':
                $this->render_page( 'sample-page', [ 'title' => 'Example Page' ] );
                break;
            default:
                $this->json_response( [ 'error' => 'Route not found' ], 404 );
        }
    }

    private function handle_posts() {
        if ( $this->action === 'list' ) {
            $posts = get_posts( [ 'numberposts' => 5 ] );
            $this->json_response( $posts );
        } elseif ( $this->action === 'details' && isset( $_GET['id'] ) ) {
            $post = get_post( intval( $_GET['id'] ) );
            $this->json_response( $post );
        } else {
            $this->json_response( [ 'error' => 'Invalid action' ], 400 );
        }
    }

    private function json_response( $data, $status = 200 ) {
        status_header( $status );
        wp_send_json( $data );
    }

    private function render_page( $template, $data = [] ) {
        extract( $data );
        include ROUTER_PATH . 'templates/' . $template . '.php';
    }
}
