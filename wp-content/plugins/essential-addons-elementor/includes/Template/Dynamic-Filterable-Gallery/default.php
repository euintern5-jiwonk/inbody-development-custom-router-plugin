<?php
use Essential_Addons_Elementor\Classes\Helper;
/**
 * Template Name: Default
 *
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$helperClass                  = new Essential_Addons_Elementor\Pro\Classes\Helper();
$show_category_child_items    = ! empty( $settings['category_show_child_items'] ) && 'yes' === $settings['category_show_child_items'] ? 1 : 0;
$show_product_cat_child_items = ! empty( $settings['product_cat_show_child_items'] ) && 'yes' === $settings['product_cat_show_child_items'] ? 1 : 0;
$classes                      = $helperClass->get_dynamic_gallery_item_classes( $show_category_child_items, $show_product_cat_child_items );
$has_post_thumbnail           = has_post_thumbnail();

$post_url        = get_the_permalink();
$post_title      = get_the_title();
$post_id         = get_the_ID();
$post_desc       = get_the_excerpt() ? get_the_excerpt() : get_the_content();
$image_clickable = 'yes' === $settings['eael_dfg_full_image_clickable'] && $settings['eael_fg_grid_style'] == 'eael-cards';

$gallery   = [];
$classes[] = get_post_field( 'post_name' );
$gallery[ $post_id ] = [
    'post_id'     => $post_id,
    'post_url'    => $post_url,
    'html_class'  => 'dynamic-gallery-item ' . urldecode( implode(' ', $classes) ),
    'thumb_id'    => get_post_thumbnail_id(),
    'title'       => $post_title,
    'img_url'     => $has_post_thumbnail ? wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']): \Elementor\Utils::get_placeholder_image_src(),
    'alt_text'    => $has_post_thumbnail ? get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true)     : '',
    'description' => wp_trim_words( strip_shortcodes( $post_desc ), $settings['eael_post_excerpt'], '<a class="eael_post_excerpt_read_more" href="' . get_the_permalink() . '"'. ( $settings['read_more_link_nofollow'] ? 'rel="nofollow"' : '' ) . '' . ( $settings['read_more_link_target_blank'] ? 'target="_blank"' : '' ) .'> ' . $settings['eael_post_excerpt_read_more'] . '</a>')
];

if( isset( $settings['eael_gf_hide_parent_items'] ) && 'yes' === $settings['eael_gf_hide_parent_items'] ){
    $gallery[ $post_id ]['html_class'] = 'dynamic-gallery-item-hide';
}

if( 'yes' === $settings['fetch_acf_image_gallery'] && class_exists( 'ACF' ) && ! empty( $settings['eael_acf_gallery_keys'] ) ){
    $acf_gallery = [];
    foreach( $settings['eael_acf_gallery_keys'] as $key ){
        $_acf_gallery = get_field( $key, get_the_ID() );
        if( ! empty( $_acf_gallery ) ){
            $acf_gallery = array_merge( $_acf_gallery, $acf_gallery );
        }
    }

    if( ! empty( $acf_gallery ) ){
        $use_parent_data = 'yes' === $settings['eael_gf_afc_use_parent_data'];
        $classes[] = 'dynamic-gallery-item';
        foreach( $acf_gallery as $item ){
            if ( empty( $item['ID'] ) ) {
                $attachment_id = false;
                if( 'integer' === gettype( $item ) ) {
                    $attachment_id = $item;
                } else if ( 'string' === gettype( $item ) ) {
                    $attachment_id = Helper::eael_get_attachment_id_from_url( $item );
                }

                if( ! $attachment_id ){
                    continue;
                } else {
                    $attachment = get_post( $attachment_id );
                    if( ! is_object( $attachment ) || ! isset( $attachment->ID ) ){
                        continue;
                    }
                    $item = [];
                    $item['ID']          = $attachment->ID;
                    $item['title']       = $attachment->post_title;
                    $item['description'] = $attachment->post_content;
                }
            }

            $description = '';
            if ( $use_parent_data  ) {
                $description = $post_desc;
            } else if ( isset( $item['description'] ) ) {
                $description = $item['description'];
            } else {
                $attachment = get_post( $item['ID'] );
                if( $attachment && isset( $attachment->post_content ) ) {
                    $description = $attachment->post_content;
                }
            }

            $gallery[] = [
                'post_id'     => $item['ID'],
                'post_url'    => $post_url,
                'html_class'  => urldecode( implode(' ', $classes) ),
                'thumb_id'    => $item['ID'],
                'title'       => $use_parent_data ? $post_title : $item['title'],
                'img_url'     => wp_get_attachment_image_url( $item['ID'], $settings['image_size'] ),
                'alt_text'    => isset( $item['alt'] ) ? $item['alt'] : $item['title'],
                'description' => wp_trim_words( strip_shortcodes( $description ), $settings['eael_post_excerpt'], '<a class="eael_post_excerpt_read_more" href="' . $post_url . '"'. ( $settings['read_more_link_nofollow'] ? 'rel="nofollow"' : '' ) . '' . ( $settings['read_more_link_target_blank'] ? 'target="_blank"' : '' ) .'> ' . $settings['eael_post_excerpt_read_more'] . '</a>'),
                'is_acf_image' => 1,
            ];
        }
    }
}

if ( 'eael-hoverer' === $settings['eael_fg_grid_style'] ) {
    foreach( $gallery as $key => $gallery_item ){
        echo '<div class="' . esc_attr( $gallery_item['html_class'] ) . '">
            <div class="dynamic-gallery-item-inner" data-itemid="' . esc_attr( $gallery_item['post_id'] ) . '" data-parent="' . ( $gallery_item['post_id'] === $post_id ? 0 : esc_attr( $post_id ) ) . '">
                <div class="dynamic-gallery-thumbnail">';
                    $thumb_url = $has_post_thumbnail ? wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']) : \Elementor\Utils::get_placeholder_image_src();
                    $alt_text = $has_post_thumbnail ? get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true) : '';
                    echo '<img src="' . esc_url( $gallery_item['img_url'] ) . '" alt="' . esc_attr( $gallery_item['alt_text'] ) . '">';

                    if ('eael-none' !== $settings['eael_fg_grid_hover_style']) {
                        echo  '<div class="caption ' . esc_attr( $settings['eael_fg_grid_hover_style'] ) . ' ">';
                            if ('true' == $settings['eael_fg_show_popup']) {
                                if ('media' == $settings['eael_fg_show_popup_styles']) {
                                    $thumb_url = wp_get_attachment_image_url( $gallery_item['thumb_id'], 'full');
                                    echo '<a href="' . esc_url( $thumb_url ) . '" class="popup-media eael-magnific-link" data-elementor-open-lightbox="yes"></a>';
                                } elseif ('buttons' == $settings['eael_fg_show_popup_styles']) {
                                    echo '<div class="item-content">';
                                        $item_content = '';
                                        if($settings['eael_show_hover_title']) {
                                            $item_content .= '<h2 class="title"><a href="' . esc_url( $gallery_item['post_url'] ) . '"'. ( $settings['title_link_nofollow'] ? 'rel="nofollow"' : '' ) . '' . ( $settings['title_link_target_blank'] ? 'target="_blank"' : '' ) .'>' . $gallery_item['title'] . '</a></h2>';
                                        }
                                        if($settings['eael_show_hover_excerpt']) {
                                            $item_content .= '<p>' . $gallery_item['description'] . '</p>';
                                        }
                                        echo wp_kses( $item_content, Helper::eael_allowed_tags() );
                                    echo '</div>';
                                    echo '<div class="buttons">';
                                        if (!empty($settings['eael_section_fg_zoom_icon'])) {
                                            $thumb_url = $has_post_thumbnail ? wp_get_attachment_image_url( $gallery_item['thumb_id'], 'full') : \Elementor\Utils::get_placeholder_image_src();
                                            
                                            if ( ! empty( $gallery_item['is_acf_image'] ) ) {
                                                $thumb_url = wp_get_attachment_image_url($gallery_item['thumb_id'], 'full');
                                            }

                                            echo '<a href="'. esc_url( $thumb_url ) .'" class="eael-magnific-link" data-elementor-open-lightbox="yes">';

                                                if( isset($settings['eael_section_fg_zoom_icon']['url']) ) {
                                                    echo '<img class="eael-dnmcg-svg-icon" src="'.esc_url($settings['eael_section_fg_zoom_icon']['url']).'" alt="'.esc_attr(get_post_meta($settings['eael_section_fg_zoom_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
                                                }else if ( ! empty( $settings['eael_section_fg_zoom_icon_new'] ) ) {
                                                    \Elementor\Icons_Manager::render_icon($settings['eael_section_fg_zoom_icon_new'], ['aria-hidden' => 'true']);
                                                } else {
                                                    echo '<i class="' . esc_attr($settings['eael_section_fg_zoom_icon']) . '"></i>';
                                                }
                                            echo '</a>';
                                        }

                                        if (!empty($settings['eael_section_fg_link_icon'])) {
                                            echo  '<a href="' . esc_url( get_the_permalink() ) . '"'. ( $settings['link_nofollow'] ? 'rel="nofollow"' : '' ) . '' . ( $settings['link_target_blank'] ? 'target="_blank"' : '' ) .'>';
                                                if( isset($settings['eael_section_fg_link_icon']['url'])) {
                                                    echo '<img class="eael-dnmcg-svg-icon" src="'.esc_url($settings['eael_section_fg_link_icon']['url']).'" alt="'.esc_attr(get_post_meta($settings['eael_section_fg_link_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
                                                }else if ( ! empty( $settings['eael_section_fg_link_icon_new'] ) ) {
                                                    \Elementor\Icons_Manager::render_icon($settings['eael_section_fg_link_icon_new'], ['aria-hidden' => 'true']);
                                                } else {
                                                    echo '<i class="' . esc_attr($settings['eael_section_fg_link_icon']) . '"></i>';
                                                }
                                            echo '</a>';
                                        }
                                    echo '</div>';
                                }
                            }
                        echo '</div>';
                    }
                echo '</div>
            </div>
        </div>';
    }
} 
else if ( 'eael-cards' === $settings['eael_fg_grid_style'] ) {
    foreach( $gallery as $gallery_item ){
        echo '<div class="' . esc_attr( $gallery_item['html_class'] ) . '">
            <div class="dynamic-gallery-item-inner" data-itemid="' . esc_attr( $gallery_item['post_id'] ) . ' ">';

            if ( $image_clickable ){
                echo '<a href="' . esc_url( $gallery_item['post_url'] ) . '"'. ( $settings['image_link_nofollow'] ? 'rel="nofollow"' : '' ) . '' . ( $settings['image_link_target_blank'] ? 'target="_blank"' : '' ) .'>';
            }
            echo '<div class="dynamic-gallery-thumbnail">';
                    $thumb_url = $has_post_thumbnail ? wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']) : \Elementor\Utils::get_placeholder_image_src();
                    $alt_text = $has_post_thumbnail ? get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true) : '';
                    echo '<img src="' . esc_url( $gallery_item['img_url'] ) . '" alt="' . esc_attr( $gallery_item['alt_text'] ) . '">';


                    if ('media' == $settings['eael_fg_show_popup_styles'] && 'eael-none' == $settings['eael_fg_grid_hover_style']) {
                        $thumb_url = $has_post_thumbnail ? wp_get_attachment_image_url( $gallery_item['thumb_id'], 'full') : \Elementor\Utils::get_placeholder_image_src();
                        
                        if ( ! empty( $gallery_item['is_acf_image'] ) ) {
                            $thumb_url = wp_get_attachment_image_url($gallery_item['thumb_id'], 'full');
                        }

                        echo '<a href="'. esc_url( $thumb_url ) .'" class="popup-only-media eael-magnific-link" data-elementor-open-lightbox="yes"></a>';
                    }

                    if ('eael-none' !== $settings['eael_fg_grid_hover_style'] && ! $image_clickable ) {
                        if ('media' == $settings['eael_fg_show_popup_styles']) {
                            echo '<div class="caption media-only-caption">';
                        } else {
                            echo '<div class="caption ' . esc_attr($settings['eael_fg_grid_hover_style']) . ' ">';
                        }
                        if ('true' == $settings['eael_fg_show_popup']) {
                            $thumb_url = $has_post_thumbnail ? wp_get_attachment_image_url( $gallery_item['thumb_id'], 'full') : \Elementor\Utils::get_placeholder_image_src();
                                
                            if ( ! empty( $gallery_item['is_acf_image'] ) ) {
                                $thumb_url = wp_get_attachment_image_url($gallery_item['thumb_id'], 'full');
                            }
                            
                            if ('media' == $settings['eael_fg_show_popup_styles']) {
                                echo '<a href="'. esc_url( $thumb_url ) .'" class="popup-media eael-magnific-link" data-elementor-open-lightbox="yes"></a>';
                            } elseif ('buttons' == $settings['eael_fg_show_popup_styles']) {
                                echo '<div class="buttons">';
                                    if (!empty($settings['eael_section_fg_zoom_icon'])) {
                                        echo  '<a href="'. esc_url( $thumb_url ) .'" class="eael-magnific-link" data-elementor-open-lightbox="yes">';

                                            if( isset($settings['eael_section_fg_zoom_icon']['url']) ) {
                                                echo '<img class="eael-dnmcg-svg-icon" src="'.esc_url($settings['eael_section_fg_zoom_icon']['url']).'" alt="'.esc_attr(get_post_meta($settings['eael_section_fg_zoom_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
                                            }else if ( ! empty( $settings['eael_section_fg_zoom_icon_new'] ) ) {
                                                \Elementor\Icons_Manager::render_icon($settings['eael_section_fg_zoom_icon_new'], ['aria-hidden' => 'true']);
                                            }else {
                                                echo '<i class="' . esc_attr($settings['eael_section_fg_zoom_icon']) . '"></i>';
                                            }
                                        echo '</a>';
                                    }

                                    if (!empty($settings['eael_section_fg_link_icon'])) {
                                        echo  '<a href="' . esc_url( $gallery_item['post_url'] ) . '"'. ( $settings['link_nofollow'] ? 'rel="nofollow"' : '' ) . '' . ( $settings['link_target_blank'] ? 'target="_blank"' : '' ) .'>';
                                            if( isset($settings['eael_section_fg_link_icon']['url'])) {
                                                echo '<img class="eael-dnmcg-svg-icon" src="'.esc_url($settings['eael_section_fg_link_icon']['url']).'" alt="'.esc_attr(get_post_meta($settings['eael_section_fg_link_icon']['id'], '_wp_attachment_image_alt', true)).'" />';
                                            }else if ( ! empty( $settings['eael_section_fg_link_icon_new'] ) ) {
                                                \Elementor\Icons_Manager::render_icon($settings['eael_section_fg_link_icon_new'], ['aria-hidden' => 'true']);
                                            }else {
                                                echo '<i class="' . esc_attr($settings['eael_section_fg_link_icon']) . '"></i>';
                                            }
                                        echo '</a>';
                                    }
                                echo '</div>';
                            }
                        }
                        echo '</div>';
                    }
                echo '</div>';

            if ( $image_clickable ){
                echo '</a>';
            }

            echo ' <div class="item-content">';
                if($settings['eael_show_hover_title']) {
                    echo '<h2 class="title"><a href="' . esc_url( $gallery_item['post_url'] ) . '"'. ( $settings['title_link_nofollow'] ? 'rel="nofollow"' : '' ) . '' . ( $settings['title_link_target_blank'] ? 'target="_blank"' : '' ) .'>' . wp_kses(  $gallery_item['title'], Helper::eael_allowed_tags() ) . '</a></h2>';
                } if($settings['eael_show_hover_excerpt']) {
                    echo '<p>' . wp_kses( $gallery_item['description'], Helper::eael_allowed_tags() ) . '</p>';
                }

                    if (('buttons' == $settings['eael_fg_show_popup_styles']) && ('eael-none' == $settings['eael_fg_grid_hover_style'])) {
                        echo '<div class="buttons entry-footer-buttons">';
                            if (!empty($settings['eael_section_fg_zoom_icon'])) {
                                $attachment_url = wp_get_attachment_image_url( $gallery_item['thumb_id'], 'full');
                                echo '<a href="' . esc_url( $attachment_url ) . '" class="eael-magnific-link" data-elementor-open-lightbox="yes"><i class="' . esc_attr($settings['eael_section_fg_zoom_icon']) . '"></i></a>';
                            }
                            if (!empty($settings['eael_section_fg_link_icon'])) {
                                echo '<a href="' . esc_url(  $gallery_item['post_url'] ) . '"'. ( $settings['link_nofollow'] ? 'rel="nofollow"' : '' ) . '' . ( $settings['link_target_blank'] ? 'target="_blank"' : '' ) .'><i class="' . esc_attr($settings['eael_section_fg_link_icon']) . '"></i></a>';
                            }
                        echo '</div>';
                    }
                echo '</div>
            </div>
        </div>';
    }
}
