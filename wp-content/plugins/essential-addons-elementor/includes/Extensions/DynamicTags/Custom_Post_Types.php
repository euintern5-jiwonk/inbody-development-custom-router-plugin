<?php

namespace Essential_Addons_Elementor\Pro\Extensions\DynamicTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;
use Essential_Addons_Elementor\Classes\Helper;
use Essential_Addons_Elementor\Pro\Classes\Helper as ClassesHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Post_Types extends Tag {
	
	/**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'eael-dynamic-tags-custom-post-types';
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('Custom Post Types', 'essential-addons-elementor');
    }

    /**
     * Get Group
     *
     * @return string
     */
    public function get_group()
    {
        return 'eael-advanced-dynamic-tags';
    }

    /**
     * Get Categories
     *
     * @return array<string>
     */
    public function get_categories()
    {
        return [Module::BASE_GROUP, Module::TEXT_CATEGORY];
    }

    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        $custom_post_types = get_post_types(['public' => true, '_builtin' => false], 'objects');
        $custom_post_types = wp_list_pluck($custom_post_types, 'label', 'name');

        unset($custom_post_types['elementor_library']);
        unset($custom_post_types['attachment']);
        unset($custom_post_types['product']);

        $args['post_types'] = $custom_post_types;

        ClassesHelper::query_dynamic_tags( $this, $args );

        // $this->add_control('post_status', 
        // [
        //     'label'         => __('Post Status', 'essential-addons-elementor'), 
        //     'type'          => Controls_Manager::SELECT2, 
        //     'options'       => get_post_statuses(), 
        //     'multiple'      => true, 
        //     'label_block'   => true, 
        //     'default'       => ['publish'],
        //     'separator'     => 'before',
        // ]);

        $this->add_control('data_format', 
        [
            'label'     => __('Data Format', 'essential-addons-elementor'), 
            'type'      => Controls_Manager::SELECT, 
            'options'   => [
                'title'     => __('Title', 'essential-addons-elementor'), 
                'title_id'  => __('Title | ID', 'essential-addons-elementor'), 
                'id'        => __('ID', 'essential-addons-elementor')
            ], 
            'default'   => 'title'
        ]);

        $this->add_control('separator', 
        [
            'label'     => __('Separator', 'essential-addons-elementor'), 
            'type'      => Controls_Manager::SELECT, 
            'options'   => [
                'none'      => __('None', 'essential-addons-elementor'), 
                'line_break'    => __('Line Break', 'essential-addons-elementor'), 
                'comma'         => __('Comma', 'essential-addons-elementor'),
            ], 
            'default'   => 'none', 
            'multiple'  => true
        ]);
    }

    /**
     * Render
     *
     * @return void
     */
    public function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $settings['post_type'] = ! empty( $settings['post_type'] ) ? ClassesHelper::validate_post_types( $settings['post_type'] ) : '';
        
        $args = Helper::get_query_args($settings);
        $extra_args = $this->get_extra_args();
        $args = array_merge( $args, $extra_args );

        if (empty($args)) {
            return;
        }
        
        $wp_query = new \WP_Query($args);
        if ($wp_query->have_posts()) {
            while ($wp_query->have_posts()) {
                $wp_query->the_post();
                
                if ( 'none' === $settings['separator'] ) {
                    echo $this->get_post_by_format();
                } else {
                    echo '<a href=' . get_the_permalink() . '>' . $this->get_post_by_format() . '</a>';
                }

                if ($wp_query->current_post + 1 !== $wp_query->post_count) {
                    echo $this->separator($settings['separator']);
                }
            }

            wp_reset_postdata();
        }
    }

    /**
     * Get Post By Format
     *
     * @return string|int|false
     */
    protected function get_post_by_format()
    {
        $data_format = $this->get_settings('data_format');

        switch ($data_format) {
            case 'title_id':
                return esc_html(get_the_title()) . ' | ' . get_the_ID();
            case 'id':
                return get_the_ID();
            default:
                return esc_html(get_the_title());
        }

        return '';
    }

    /**
     * Get Args
     *
     * @return array<string,int|string>
     */
    protected function get_extra_args()
    {
        $settings = $this->get_settings_for_display();

        $extra_args = [
            'post_status' => $settings['post_status'] ?? '',
        ];

        return $extra_args;
    }

    /**
     * Separator
     *
     * @param string $option
     * @return string
     */
    protected function separator(string $option)
    {
        switch ($option) {
            case 'line_break':
                return '<br />';
            case 'none':
                return "\n";
            case 'comma':
                return ', ';
        }
        return '';
    }
}