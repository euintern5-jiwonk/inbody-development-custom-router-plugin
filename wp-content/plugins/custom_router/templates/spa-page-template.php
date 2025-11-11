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
            // Elementor will automatically display its content through the_content()
            the_content();
            ?>
        </div>

    </main>
</div>

<?php
get_footer();
?>