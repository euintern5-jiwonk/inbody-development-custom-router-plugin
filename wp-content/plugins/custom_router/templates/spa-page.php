<?php
/**
 * Template Name: SPA Page
 * 
 * Use this template for pages that should work with SPA routing
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <!-- This is the container that will be replaced -->
        <div id="spa-content">
            <?php
            while ( have_posts() ) :
                the_post();
                
                // Display Elementor content
                if ( class_exists( '\Elementor\Plugin' ) ) {
                    echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( get_the_ID() );
                } else {
                    the_content();
                }
                
            endwhile;
            ?>
        </div>

    </main>
</div>

<?php
get_sidebar();
get_footer();
?>