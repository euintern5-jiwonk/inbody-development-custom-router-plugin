<?php

namespace Essential_Addons_Elementor\Pro\Extensions\DynamicTags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Acf_Relationship extends Posts {
	
	/**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'eael-dynamic-tags-acf-relationship';
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('ACF Relationship', 'essential-addons-elementor');
    }

    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('eael_acf_relationship_field', 
        [
            'label' => __('ACF Relationship Field', 'essential-addons-elementor'), 
            'type' => 'eael_acf_relationship_tag_query', 
            'placeholder' => __('Select the field', 'essential-addons-elementor'), 
            'label_block' => true, 
            'query_type' => 'acf', 
            'object_type' => 'post_object,relationship'
        ]);
        parent::register_controls();
    }

    protected function get_extra_args()
    {
        $args = parent::get_extra_args();
        $settings = $this->get_settings_for_display();
        $relations = get_field($settings['eael_acf_relationship_field'], false);
        if (!$relations) {
            $relations = get_sub_field($settings['eael_acf_relationship_field'], false);
        }
        if (empty($relations)) {
            return;
        }
        
        return $args + ['post__in' => $relations];
    }
}