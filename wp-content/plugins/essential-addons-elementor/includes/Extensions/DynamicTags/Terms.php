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

class Terms extends Tag {
	
	/**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'eael-dynamic-tags-terms';
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('Terms', 'essential-addons-elementor');
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
        $this->add_control('taxonomy', 
        [
            'label' => __('Taxonomy', 'essential-addons-elementor'), 
            'type' => Controls_Manager::SELECT, 
            'options' => ClassesHelper::get_allowed_taxonomies(), 
            'default' => 'category'
        ]);

        $this->add_control('orderby', [
            'label'     => __('Order By', 'essential-addons-elementor'), 
            'type'      => Controls_Manager::SELECT, 
            'options'   => Helper::get_post_orderby_options(), 
            'default'   => 'date'
        ]);

        $this->add_control('order', 
        [
            'label'     => __('Order', 'essential-addons-elementor'), 
            'type'      => Controls_Manager::SELECT, 
            'options'   => [
                'ASC' => __('Ascending', 'essential-addons-elementor'), 
                'DESC' => __('Descending', 'essential-addons-elementor')
            ], 
            'default'   => 'DESC'
        ]);

        $this->add_control('hide_empty', 
        [
            'label' => __('Hide Empty', 'essential-addons-elementor'), 
            'type' => Controls_Manager::SWITCHER
        ]);

        $this->add_control('total_results', 
        [
            'label'     => __('Total Results', 'essential-addons-elementor'), 
            'type'      => Controls_Manager::NUMBER, 
            'default'   => '10'
        ]);

        $this->add_control('data_format', 
        [
            'label'     => __('Data Format', 'essential-addons-elementor'), 
            'type'      => Controls_Manager::SELECT, 
            'options'   => [
                'name'     => __('Name', 'essential-addons-elementor'), 
                'name_id'  => __('Name | ID', 'essential-addons-elementor'), 
                'id'        => __('ID', 'essential-addons-elementor')
            ], 
            'default'   => 'name'
        ]);

        $this->add_control('separator', 
        [
            'label'     => __('Separator', 'essential-addons-elementor'), 
            'type'      => Controls_Manager::SELECT, 
            'options'   => [
                'new_line'      => __('New Line', 'essential-addons-elementor'), 
                'line_break'    => __('Line Break', 'essential-addons-elementor'), 
                'comma'         => __('Comma', 'essential-addons-elementor')
            ], 
            'default'   => 'new_line', 
            'multiple'  => \true
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
        if ( empty( $settings ) ) {
            return;
        }
        
        $args = $this->get_args();
        if ( empty( $args ) ) {
            return;
        }
        
        $term_query = new \WP_Term_Query( $args );
        if ( empty( $term_query->terms ) ) {
            return;
        }

        $count = count( $term_query->terms );
        $i = 1;

        foreach ( $term_query->terms as $term ) {
            echo $this->get_term_by_format( $term->term_id );
            
            if ( $i < $count ) {
                echo $this->separator( $settings['separator'] );
            }
            
            $i++;
        }
    }

    /**
     * Get Term By Format
     *
     * @return string|int|false
     */
    protected function get_term_by_format( $id )
    {
        if ( ! term_exists( $id ) ) {
            return;
        }

        $data_format = $this->get_settings('data_format');
        
        switch ($data_format) {
            case 'name_id':
                return esc_html(get_term_field('name', $id)) . ' | ' . $id;
            case 'id':
                return $id;
            default:
                return esc_html(get_term_field('name', $id));
        }
    }

    /**
     * Get Args
     *
     * @return array<string,int|string>
     */
    protected function get_args()
    {
        $settings = $this->get_settings_for_display();

        # TODO exclude terms
        $args = [
            'taxonomy' => $settings['taxonomy'],
            'number' => $settings['total_results'], 
            'order' => $settings['order'],
            'orderby' => $settings['orderby'], 
            'hide_empty' => 'yes' === $settings['hide_empty'], 
        ];

        return $args;
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
            case 'new_line':
                return "\n";
            case 'comma':
                return ', ';
        }
        return '';
    }
}