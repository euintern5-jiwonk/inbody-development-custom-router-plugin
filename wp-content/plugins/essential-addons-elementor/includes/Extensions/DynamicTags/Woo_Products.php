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

class Woo_Products extends Tag {
	
	/**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'eael-dynamic-tags-woo-products';
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('Woo Products', 'essential-addons-elementor');
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
        $this->init_content_wc_notice_controls();

        if ( ! function_exists( 'WC' ) ) {
            return;
        }

        $args['post_types'] = [
            'product' => __('Product', 'essential-addons-elementor'),
        ];

        $args['hide_controls'] = ['post_type', 'pa_color_ids', 'pa_size_ids'];
        
        $this->add_control('eael_tag_filter_by', [
            'label'     => esc_html__('Filter By', 'essential-addons-elementor'),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'recent-products',
            'options'   => $this->eael_tag_filter_by_options(),
            'condition' => [
              'post_type!' => 'source_dynamic',
            ],
        ]);

        $this->add_control('eael_tag_order_by', [
            'label'     => __('Order By', 'essential-addons-elementor'),
            'type'      => Controls_Manager::SELECT,
            'options'   => $this->eael_tag_order_by_options(),
            'default'   => 'date',
            'condition' => [
                'eael_tag_filter_by!' => [ 'best-selling-products', 'top-products' ],
            ]
        ]);

        ClassesHelper::query_dynamic_tags( $this, $args );

        $this->add_control('post_status', 
        [
            'label'         => __('Post Status', 'essential-addons-elementor'), 
            'type'          => Controls_Manager::SELECT2, 
            'options'       => get_post_statuses(), 
            'multiple'      => true, 
            'label_block'   => true, 
            'default'       => ['publish'],
            'separator'     => 'before',
        ]);

        // $this->add_control('data_format', 
        // [
        //     'label'     => __('Data Format', 'essential-addons-elementor'), 
        //     'type'      => Controls_Manager::SELECT, 
        //     'options'   => [
        //         'title'     => __('Title', 'essential-addons-elementor'), 
        //         'title_id'  => __('Title | ID', 'essential-addons-elementor'), 
        //         'id'        => __('ID', 'essential-addons-elementor')
        //     ], 
        //     'default'   => 'title'
        // ]);

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
        $settings['separator'] = $settings['separator'] ?? '';
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

    protected function init_content_wc_notice_controls() {
		if ( ! function_exists( 'WC' ) ) {
			$this->add_control( 'eael_global_warning_text', [
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( '<strong>WooCommerce</strong> is not installed/activated on your site. Please install and activate <a href="plugin-install.php?s=woocommerce&tab=search&type=term" target="_blank">WooCommerce</a> first.', 'essential-addons-elementor' ),
				'content_classes' => 'eael-warning',
			] );
			return;
		}
	}

    protected function eael_tag_order_by_options()
    {
        return [
            'ID'            => __('Product ID', 'essential-addons-elementor'),
            'title'         => __('Product Title', 'essential-addons-elementor'),
            '_price'        => __('Price', 'essential-addons-elementor'),
            '_sku'          => __('SKU', 'essential-addons-elementor'),
            'date'          => __('Date', 'essential-addons-elementor'),
            'modified'      => __('Last Modified Date', 'essential-addons-elementor'),
            'parent'        => __('Parent Id', 'essential-addons-elementor'),
            'rand'          => __('Random', 'essential-addons-elementor'),
            'menu_order'    => __('Menu Order', 'essential-addons-elementor'),
        ];
    }

    protected function eael_tag_filter_by_options()
    {
        return [
            'recent-products'       => esc_html__('Recent Products', 'essential-addons-elementor'),
            'featured-products'     => esc_html__('Featured Products', 'essential-addons-elementor'),
            'best-selling-products' => esc_html__('Best Selling Products', 'essential-addons-elementor'),
            'sale-products'         => esc_html__('Sale Products', 'essential-addons-elementor'),
            'top-products'          => esc_html__('Top Rated Products', 'essential-addons-elementor'),
            // 'manual'                => esc_html__('Manual Selection', 'essential-addons-elementor'),
        ];
    }
}