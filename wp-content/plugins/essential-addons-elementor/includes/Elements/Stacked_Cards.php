<?php
namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Widget_Base;
use \Elementor\Plugin;

use \Essential_Addons_Elementor\Pro\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Stacked_Cards extends Widget_Base {
   public function get_name() {
		return 'eael-stacked-cards';
	}

   public function get_title() {
      return esc_html__( 'Stacked Cards', 'essential-addons-elementor' );
   }

   public function get_icon() {
		return 'eaicon-stacked-cards';
	}

	public function get_categories() {
		return [ 'essential-addons-elementor' ];
	}

	public function get_keywords() {
		return [
			'ea stacked cards',
			'ea stacked',
			'ea cards',
			'stacked cards',
			'stacked',
			'cards',
			'ea',
		];
	}

	public function get_custom_help_url() {
		return 'https://essential-addons.com/elementor/docs/ea-stacked-cards/';
	}

   protected function register_controls() {
		// Content Tab Start
		$this->eael_stacked_card_control();
		$this->eael_stacked_card_settings();
		// Content Tab End

		// Style Tab Start
		$this->eael_style_control();
		// Style Tab End
	}

	/**
	 * Content Control
	 */
	public function eael_stacked_card_control() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Card Items', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'eael_stacked_card_content_type',
			[
				'label'   => esc_html__('Content Type', 'essential-addons-elementor'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'content'  => esc_html__('Content', 'essential-addons-elementor'),
					'template' => esc_html__('Saved Templates', 'essential-addons-elementor'),
				],
				'default' => 'content',
			]
		);

			$repeater->add_control(
				'eael_stacked_card_item_title',
				[
					'label'       => esc_html__( 'Title', 'essential-addons-elementor' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => esc_html__( 'Card Item' , 'essential-addons-elementor' ),
					'label_block' => true,
					'condition' => [ 
						'eael_stacked_card_content_type' => 'content',
					]
				]
			);

			$repeater->add_control(
				'eael_stacked_card_item_title_tag',
				[
					'label'   => esc_html__( 'Title Tag', 'essential-addons-elementor' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'h2',
					'options' => [
						'h1'   => esc_html__( 'H1', 'essential-addons-elementor' ),
						'h2'   => esc_html__( 'H2', 'essential-addons-elementor' ),
						'h3'   => esc_html__( 'H3', 'essential-addons-elementor' ),
						'h4'   => esc_html__( 'H4', 'essential-addons-elementor' ),
						'h5'   => esc_html__( 'H5', 'essential-addons-elementor' ),
						'h6'   => esc_html__( 'H6', 'essential-addons-elementor' ),
						'span' => esc_html__( 'Span', 'essential-addons-elementor' ),
						'p'    => esc_html__( 'P', 'essential-addons-elementor' ),
						'div'  => esc_html__( 'Div', 'essential-addons-elementor' ),
					],
					'condition' => [ 
						'eael_stacked_card_content_type' => 'content',
					]
				]
			);

			$repeater->add_control(
				'eael_stacked_card_primary_templates',
				[
					'label'       => esc_html__('Choose Template', 'essential-addons-elementor'),
					'type'        => 'eael-select2',
					'source_name' => 'post_type',
					'source_type' => 'elementor_library',
					'label_block' => true,
					'condition'   => [
						'eael_stacked_card_content_type' => 'template',
					],
			]
		);

		$repeater->start_controls_tabs(
			'eael_stacked_card_tabs'
		);
		$repeater->start_controls_tab(
			'eael_stacked_card_media_tab',
			[
				'label' => esc_html__( 'Media', 'essential-addons-elementor' ),
				'condition' => [
					'eael_stacked_card_content_type' => 'content',
				],
			]
		);

		$repeater->add_control(
			'eael_stacked_card_list_type',
			[
				'label'   => esc_html__( 'Choose Type', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'none' => [
						'title' => esc_html__( 'None', 'essential-addons-elementor' ),
						'icon'  => 'fa fa-ban',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'essential-addons-elementor' ),
						'icon'  => 'eicon-image-bold',
					],
					'video' => [
						'title' => esc_html__( 'Video', 'essential-addons-elementor' ),
						'icon'  => 'eicon-video-playlist',
					],
					'icon' => [
						'title' => esc_html__( 'Icon', 'essential-addons-elementor' ),
						'icon'  => 'eicon-icon-box',
					],
				],
				'default' => 'image',
				'condition' => [
					'eael_stacked_card_content_type' => 'content',
				],
			]
		);

		$repeater->add_control(
			'eael_stacked_card_list_direction',
			[
				'label'   => esc_html__( 'Direction', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'row' => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-arrow-left',
					],
					'row-reverse' => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-arrow-right',
					],
				],
				'default' => 'row',
				'condition' => [
					'eael_stacked_card_content_type' => 'content',
				],
			]
		);

		$repeater->add_control(
			'eael_stacked_card_image',
			[
				'label'       => esc_html__( 'Choose Image', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'media_types' => [ 'image', 'svg' ],
				'default'     => [
					'url' => EAEL_PRO_PLUGIN_URL . 'assets/front-end/img/placeholder.png',
				],
				'condition' => [ 
					'eael_stacked_card_list_type' => 'image',
					'eael_stacked_card_content_type' => 'content',
				]
			]
		);

		$repeater->add_control(
			'eael_stacked_card_video',
			[
				'label'       => esc_html__( 'Choose Video', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'media_types' => [ 'video' ],
				'condition' => [ 
					'eael_stacked_card_list_type' => 'video',
					'eael_stacked_card_content_type' => 'content',
				]
			]
		);

		$repeater->add_control(
			'eael_stacked_card_icon',
			[
				'label'       => esc_html__( 'Choose Icon', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-cube',
					'library' => 'fa-solid',
				],
				'condition' => [ 
					'eael_stacked_card_list_type' => 'icon',
					'eael_stacked_card_content_type' => 'content',
				]
			]
		);

		$repeater->add_responsive_control(
			'eael_stacked_card_item_image_width',
			[
				'label'      => esc_html__( 'Width', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [ 
					'eael_stacked_card_content_type' => 'content',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eael-stacked-cards__media' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .eael-stacked-cards__media img' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .eael-stacked-cards__media video' => 'width: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$repeater->add_responsive_control(
			'eael_stacked_card_item_height',
			[
				'label'      => esc_html__( 'Height', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'vh', 'px' ],
				'range' => [
					'vh' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'vh',
				],
				'condition' => [ 
					'eael_stacked_card_content_type' => 'content',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eael-stacked-cards__media img' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .eael-stacked-cards__media video' => 'height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'eael_stacked_card_content_tab',
			[
				'label' => esc_html__( 'Content', 'essential-addons-elementor' ),
				'condition' => [
					'eael_stacked_card_content_type' => 'content',
				],
			]
		);

		$repeater->add_control(
			'eael_stacked_card_item_content_media',
			[
				'label'   => esc_html__( 'Choose Type', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'none' => [
						'title' => esc_html__( 'None', 'essential-addons-elementor' ),
						'icon'  => 'fa fa-ban',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'essential-addons-elementor' ),
						'icon'  => 'eicon-image-bold',
					],
					'icon' => [
						'title' => esc_html__( 'Icon', 'essential-addons-elementor' ),
						'icon'  => 'eicon-icon-box',
					],
				],
				'default' => 'image',
				'condition' => [
					'eael_stacked_card_content_type' => 'content',
				],
			]
		);

		$repeater->add_control(
			'eael_stacked_card_item_content_image',
			[
				'label'       => esc_html__( 'Choose Image', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'media_types' => [ 'image', 'svg' ],
				'default'     => [
					'url' => EAEL_PRO_PLUGIN_URL . 'assets/front-end/img/pencil.png',
				],
				'condition' => [ 
					'eael_stacked_card_item_content_media' => 'image',
					'eael_stacked_card_content_type' => 'content',
				]
			]
		);

		$repeater->add_responsive_control(
			'eael_stacked_card_item_content_image_width',
			[
				'label'      => esc_html__( 'Width', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'condition' => [ 
					'eael_stacked_card_item_content_media' => 'image',
					'eael_stacked_card_content_type' => 'content',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eael-stacked-cards__content__media .eael-stacked-cards__content__image' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_control(
			'eael_stacked_card_item_content_icon',
			[
				'label'       => esc_html__( 'Choose Icon', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-cube',
					'library' => 'fa-solid',
				],
				'condition' => [ 
					'eael_stacked_card_item_content_media' => 'icon',
					'eael_stacked_card_content_type' => 'content',
				]
			]
		);

		$repeater->add_control(
			'eael_stacked_card_item_content',
			[
				'label'       => esc_html__( 'Content', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'default'     => esc_html__( 'Insert your own content to showcase or highlight the feature you want to represent. Add any blocks you want to customize it as per your preference.', 'essential-addons-elementor' ),
				'label_block' => true,
				'condition' => [ 
					'eael_stacked_card_content_type' => 'content',
				]
			]
		);

		$repeater->add_control(
			'eael_stacked_card_item_content_show_button',
			[
				'label'        => esc_html__( 'Show Button', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [ 
					'eael_stacked_card_content_type' => 'content',
				]
			]
		);

		$repeater->add_control(
			'eael_stacked_card_item_content_button_text',
			[
				'label'       => esc_html__( 'Button Text', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Read More', 'essential-addons-elementor' ),
				'condition'   => [ 
					'eael_stacked_card_content_type' => 'content',
					'eael_stacked_card_item_content_show_button' => 'yes',
				]
			]
		);

		$repeater->add_control(
			'eael_stacked_card_item_content_button_link',
			[
				'label'   => esc_html__( 'Button Link', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::URL,
				'options' => [ 'url', 'is_external', 'nofollow' ],
				'default' => [
					'url'         => esc_html__( '#', 'essential-addons-elementor' ),
					'is_external' => false,
					'nofollow'    => true,
				],
				'label_block' => true,
				'condition'   => [ 
					'eael_stacked_card_content_type' => 'content',
					'eael_stacked_card_item_content_show_button' => 'yes',
				]
			]
		);

		$repeater->add_control(
			'eael_stacked_card_content_btn_normal_color',
			[
				'label'     => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#333',
				'condition'   => [ 
					'eael_stacked_card_content_type' => 'content',
					'eael_stacked_card_item_content_show_button' => 'yes',
				]
			]
		);

		$repeater->add_control(
			'eael_stacked_card_content_btn_normal_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition'   => [ 
					'eael_stacked_card_content_type' => 'content',
					'eael_stacked_card_item_content_show_button' => 'yes',
				]
			]
		);

		$repeater->add_control(
			'eael_stacked_card_content_btn_hover_color',
			[
				'label'     => esc_html__( 'Hover Text Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition'   => [ 
					'eael_stacked_card_content_type' => 'content',
					'eael_stacked_card_item_content_show_button' => 'yes',
				]
			]
		);

		$repeater->add_control(
			'eael_stacked_card_content_btn_hover_bg_color',
			[
				'label'     => esc_html__( 'Hover Background Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition'   => [ 
					'eael_stacked_card_content_type' => 'content',
					'eael_stacked_card_item_content_show_button' => 'yes',
				]
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();
		
		$repeater->add_control(
			'eael_stacked_card_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_stacked_card_list',
			[
				'label'       => esc_html__( '', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default' => [
					[
						'eael_stacked_card_item_title' => esc_html__( 'Card Item #1', 'essential-addons-elementor' ),
						'eael_stacked_card_bg_color' => 'rgba(250,236,188,1)',
					],
					[
						'eael_stacked_card_item_title' => esc_html__( 'Card Item #2', 'essential-addons-elementor' ),
						'eael_stacked_card_bg_color' => 'rgba(196,252,221,1)',
					],
					[
						'eael_stacked_card_item_title' => esc_html__( 'Card Item #3', 'essential-addons-elementor' ),
						'eael_stacked_card_bg_color' => 'rgba(215,215,251,1)',
					],
				],
				'title_field' => '{{{ eael_stacked_card_item_title }}}',
			]
		);

		$this->end_controls_section();
	}

	// Stacked Card Settion
	public function eael_stacked_card_settings() {
		$this->start_controls_section(
			'eael_stacked_card_settings',
			[
				'label' => esc_html__( 'Settings', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'eael_stacked_card_style',
			[
				'label'   => esc_html__( 'Card Style', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'vertical',
				'options' => [
					'vertical'   => esc_html__( 'Vertical', 'essential-addons-elementor' ),
					'horizontal' => esc_html__( 'Horizontal', 'essential-addons-elementor' ),
				],
			]
		);

		$this->add_control(
			'eael_stacked_card_transform',
			[
				'label'   => esc_html__( 'Transform', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'translate',
				'options' => [
					'none'      => esc_html__( 'None', 'essential-addons-elementor' ),
					'rotate'    => esc_html__( 'Rotate', 'essential-addons-elementor' ),
					'translate' => esc_html__( 'Translate', 'essential-addons-elementor' ),
					'scale'     => esc_html__( 'Scale', 'essential-addons-elementor' ),
				],
			]
		);

		$this->add_control(
			'eael_stacked_card_rotation',
			[
				'label' => esc_html__( 'Rotation', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => -100,
						'max'  => 100,
						'step' => .5,
					],
				],
				'default' => [
					'size' => 6,
				],
				'condition' => [ 'eael_stacked_card_transform' => 'rotate' ]
			]
		);

		$this->add_control(
			'eael_stacked_card_translate',
			[
				'label' => esc_html__( 'Translate', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 30,
				],
				'condition' => [ 'eael_stacked_card_transform' => 'translate' ]
			]
		);

		$this->add_control(
			'eael_stacked_card_scale',
			[
				'label' => esc_html__( 'Scale', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => -100,
						'max'  => 100,
						'step' => .5,
					],
				],
				'default' => [
					'size' => 0,
				],
				'condition' => [ 'eael_stacked_card_transform' => 'scale' ]
			]
		);

		$this->add_control(
			'eael_stacked_card_item_opacity',
			[
				'label' => esc_html__( 'Opacity', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'step' => .1,
					],
				],
				'default' => [
					'size' => 1,
				],
				'condition' => [ 'eael_stacked_card_style' => 'vertical' ]
			]
		);

		$this->add_control(
			'eael_stacked_card_filter',
			[
				'label'   => esc_html__( 'Filter', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'separator'    => 'before',
				'default' => 'none',
				'options' => [
					'none'      => esc_html__( 'None', 'essential-addons-elementor' ),
					'blur'      => esc_html__( 'Blur', 'essential-addons-elementor' ),
					'opacity'   => esc_html__( 'Opacity', 'essential-addons-elementor' ),
					'grayscale' => esc_html__( 'Grayscale', 'essential-addons-elementor' ),
					'sepia'     => esc_html__( 'Sepia', 'essential-addons-elementor' ),
				],
				'condition' => [ 'eael_stacked_card_style' => 'vertical' ]
			]
		);

		$this->add_control(
			'eael_stacked_card_blur',
			[
				'label' => esc_html__( 'Blur', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 1,
				],
				'condition' => [ 
					'eael_stacked_card_filter' => 'blur',
					'eael_stacked_card_style' => 'vertical',
				]
			]
		);

		$this->add_control(
			'eael_stacked_card_opacity',
			[
				'label' => esc_html__( 'Opacity', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'default' => [
					'size' => 25,
				],
				'condition' => [
					'eael_stacked_card_filter' => 'opacity',
					'eael_stacked_card_style' => 'vertical'
				]
			]
		);

		$this->add_control(
			'eael_stacked_card_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'default' => [
					'size' => 50,
				],
				'condition' => [ 
					'eael_stacked_card_filter' => 'grayscale',
					'eael_stacked_card_style' => 'vertical',
				]
			]
		);

		$this->add_control(
			'eael_stacked_card_sepia',
			[
				'label' => esc_html__( 'Sepia', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'default' => [
					'size' => 50,
				],
				'condition' => [ 
					'eael_stacked_card_filter' => 'sepia',
					'eael_stacked_card_style' => 'vertical',
				]
			]
		);

		$this->add_control(
			'eael_stacked_card_start_form',
			[
				'label'     => esc_html__( 'Card Direction From (Bottom/Top)', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'description' => esc_html__( 'Set the starting point of the card. When we select plus (+) value the card will go from bottom to top, When select minus (-) value it will go top to bottom', 'essential-addons-elementor' ),
				'separator' => 'before',
				'range'     => [
					'px' => [
						'min'  => -500,
						'max'  => 500,
						'step' => 10,
					],
				],
				'default' => [
					'size' => 350,
				],
				'condition' => [ 
					'eael_stacked_card_style' => 'vertical',
				]
			]
		);

		$this->add_control(
			'eael_stacked_card_start',
			[
				'label'       => esc_html__( 'Card Start From', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'separator'   => 'before',
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 10,
					],
				],
				'default' => [
					'size' => 100,
				],
				'description' => esc_html__( 'From where stacked card animation will start in pixels', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_stacked_card_end_from',
			[
				'label'     => esc_html__( 'Card End From', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'separator' => 'before',
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__( 'Default', 'essential-addons-elementor' ),
					'custom'  => esc_html__( 'Custom', 'essential-addons-elementor' ),
				],
				'condition' => [ 
					'eael_stacked_card_style' => 'vertical',
				],
				'description' => esc_html__( 'To where stacked card animation will end', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_stacked_card_end',
			[
				'label'       => esc_html__( 'Custom End Value', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'separator'   => 'before',
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 20,
					],
				],
				'default' => [
					'size' => 500,
				],
				'condition' => [ 
					'eael_stacked_card_end_from' => 'custom',
					'eael_stacked_card_style' => 'vertical',
				],
				'description' => esc_html__( 'The ScrollTrigger\'s ending scroll position in pixels', 'essential-addons-elementor' )
			]
		);

		$this->add_control(
			'eael_stacked_card_hr_end',
			[
				'label'       => esc_html__( 'End Value', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'separator'   => 'before',
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 50,
					],
				],
				'default' => [
					'size' => 200,
				],
				'condition' => [ 'eael_stacked_card_style' => 'horizontal' ]
			]
		);

		$this->add_control(
			'eael_stacked_card_marker',
			[
				'label'        => esc_html__( 'Show Marker', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'separator'    => 'before',
				'return_value' => 'true',
				'description'  => esc_html__( 'Show marker during development to see where card animation start and end', 'essential-addons-elementor' ),
			]
		);


		$this->end_controls_section();
	}

	/**
	 * Style Control
	 */
	public function eael_style_control() {
		$this->eael_stacked_card_style_container();

		$this->eael_stacked_card_icon_style();

		$this->eael_stacked_card_title_style();
		
		$this->eael_stacked_card_content_style();

		$this->eael_stacked_card_button_style();
		

		//Icon Style
		$this->start_controls_section(
			'eael_stacked_card_icon_style',
			[
				'label' => esc_html__( 'Media', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_image_section_width',
			[
				'label'      => esc_html__( 'Section Width', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-stacked-cards__media' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_image_gap',
			[
				'label'      => esc_html__( 'Gap', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-stacked-cards__item' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_image_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__media' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_image_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__media' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_stacked_card_icon_header',
			[
				'label'     => esc_html__( 'Icon', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 500,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .eael-stacked-cards__media i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-stacked-cards__media svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_stacked_card_icon_color',
			[
				'label'      => esc_html__( 'Icon Color', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__media i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-stacked-cards__media svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__media svg, {{WRAPPER}} .eael-stacked-cards__media i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_icon_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__media svg, {{WRAPPER}} .eael-stacked-cards__media i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_icon_align',
			[
				'label'   => esc_html__( 'Icon Alignment', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .eael-stacked-cards__media' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function eael_stacked_card_style_container() {
		$this->start_controls_section(
			'eael_stacked_card_container_style',
			[
				'label' => esc_html__( 'Container', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_container_height',
			[
				'label'      => esc_html__( 'Container Height', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'vh', 'px' ],
				'range'      => [
					'vh' => [
						'min'  => 10,
						'max'  => 200,
						'step' => 5,
					],
					'px' => [
						'min'  => 1,
						'max'  => 2000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'vh',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .eael-stacked-cards__container' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'eael_stacked_card_item_border',
				'selector' => '{{WRAPPER}} .eael-stacked-cards__item, {{WRAPPER}} .eael-stacked-cards__item_hr',
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_item_border_radiout',
			[
				'label'      => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'custom' ],
				'default'    => [
					'top'      => 20,
					'right'    => 20,
					'bottom'   => 20,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__item, {{WRAPPER}} .eael-stacked-cards__item_hr' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_stacked_card_item_box_shadow',
				'selector' => '{{WRAPPER}} .eael-stacked-cards__item, {{WRAPPER}} .eael-stacked-cards__item_hr',
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_item_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'custom' ],
				'default'    => [
					'top'      => 60,
					'right'    => 45,
					'bottom'   => 60,
					'left'     => 45,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__item, {{WRAPPER}} .eael-stacked-cards__item_hr' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}

	public function eael_stacked_card_icon_style() {
		$this->start_controls_section(
			'eael_stacked_card_content_icon_style',
			[
				'label' => esc_html__( 'Icon', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_item_content_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 500,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors' => [
					'{{WRAPPER}} .eael-stacked-cards__content__media i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-stacked-cards__content__media svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_stacked_card_item_content_icon_color',
			[
				'label'      => esc_html__( 'Icon Color', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__content__media i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-stacked-cards__content__media svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_item_content_icon_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'custom' ],
				'default'    => [
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 50,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__content__media' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}

	public function eael_stacked_card_title_style() {
		$this->start_controls_section(
			'eael_stacked_card_content_title_style',
			[
				'label' => esc_html__( 'Title', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_stacked_card_content_title_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-stacked-cards__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_stacked_card_content_title_typography',
				'selector' => '{{WRAPPER}} .eael-stacked-cards__title',
				'fields_options' => [
					'typography' => ['default' => 'yes'],
					'font_size' => ['default' => ['size' => 40]],
					'font_family' => ['default' => 'Inter'],
					'font_weight' => ['default' => 600],
				],
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_content_title_align',
			[
				'label'   => esc_html__( 'Alignment', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'left',
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .eael-stacked-cards__title' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
	}

	public function eael_stacked_card_content_style() {
		$this->start_controls_section(
			'eael_stacked_card_content_content_style',
			[
				'label' => esc_html__( 'Content', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_stacked_card_content_text_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-stacked-cards__content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_stacked_card_content_text_typography',
				'selector' => '{{WRAPPER}} .eael-stacked-cards__content p',
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_content_align',
			[
				'label'   => esc_html__( 'Alignment', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'left',
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .eael-stacked-cards__content p' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function eael_stacked_card_button_style() {
		$this->start_controls_section(
			'eael_stacked_card_content_button',
			[
				'label' => esc_html__( 'Button', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_stacked_card_content_button_typography',
				'selector' => '{{WRAPPER}} .eael-stacked-cards__link',
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_content_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'custom' ],
				'default' => [
					'top'    => 10,
					'right'  => 30,
					'bottom' => 10,
					'left'   => 30,
					'unit'   => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_content_button_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'           => 'eael_stacked_card_content_btn_normal_border',
				'label'          => esc_html__( 'Border', 'essential-addons-elementor' ),
				'fields_options' => [
					'border' => [
						'label'   => esc_html__( 'Border Type', 'essential-addons-elementor' ),
						'default' => 'solid',
					],
					'width' => [
						'label' => esc_html__( 'Border Width', 'essential-addons-elementor' ),
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' =>  false,
						],
					],
					'color' => [
						'label'   => esc_html__( 'Border Color', 'essential-addons-elementor' ),
						'default' => '#333',
					],

				],
				'selector' => '{{WRAPPER}} .eael-stacked-cards__link',
			]
		);

		$this->add_responsive_control(
			'eael_stacked_card_content_button_border_radiout',
			[
				'label'      => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'custom' ],
				'default' => [
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
					'unit'   => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-stacked-cards__link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render Stacked Cards
	 *
	 * @access protected
	 */
	protected function eael_stacked_card_data( $settings ) {
		$eael_stacked_card_data = [];
		$eael_stacked_card_data['rotation'] = ! empty( $settings['eael_stacked_card_rotation']['size'] ) ? $settings['eael_stacked_card_rotation']['size'] : 0;
		$eael_stacked_card_data['translate'] = ! empty( $settings['eael_stacked_card_translate']['size'] ) ? $settings['eael_stacked_card_translate']['size'] : 0;
		$eael_stacked_card_data['scale'] = ! empty( $settings['eael_stacked_card_scale']['size'] ) ? $settings['eael_stacked_card_scale']['size'] : 0;
		$eael_stacked_card_data['blur'] = ! empty( $settings['eael_stacked_card_blur']['size'] ) ? $settings['eael_stacked_card_blur']['size'] : 0;
		$eael_stacked_card_data['opacity'] = ! empty( $settings['eael_stacked_card_opacity']['size'] ) ? $settings['eael_stacked_card_opacity']['size'] : 0;
		$eael_stacked_card_data['grayscale'] = ! empty( $settings['eael_stacked_card_grayscale']['size'] ) ? $settings['eael_stacked_card_grayscale']['size'] : 0;
		$eael_stacked_card_data['sepia'] = ! empty( $settings['eael_stacked_card_sepia']['size'] ) ? $settings['eael_stacked_card_sepia']['size'] : 0;
		$eael_stacked_card_data['start_form'] = ! empty( $settings['eael_stacked_card_start_form']['size'] ) ? $settings['eael_stacked_card_start_form']['size'] : 0;
		$eael_stacked_card_data['marker'] = ! empty( $settings['eael_stacked_card_marker'] ) ? $settings['eael_stacked_card_marker'] : false;
		$eael_stacked_card_data['start'] = ! empty( $settings['eael_stacked_card_start']['size']  ) ? $settings['eael_stacked_card_start']['size']  : 0;
		$eael_stacked_card_data['end_from'] = ! empty( $settings['eael_stacked_card_end_from'] ) ? $settings['eael_stacked_card_end_from'] : 'default';
		$eael_stacked_card_data['end'] = ! empty( $settings['eael_stacked_card_end']['size'] ) ? $settings['eael_stacked_card_end']['size'] : 0;
		$eael_stacked_card_data['item_opacity'] = ! empty( $settings['eael_stacked_card_item_opacity']['size'] ) ? $settings['eael_stacked_card_item_opacity']['size'] : 1.1;
		$eael_stacked_card_data['hr_end'] = ! empty( $settings['eael_stacked_card_hr_end']['size'] ) ? $settings['eael_stacked_card_hr_end']['size'] : 0;
		return $eael_stacked_card_data;
	}

	protected function eael_render_content_card( $settings, $key, $card ) {
		$flex_direction = $card['eael_stacked_card_list_direction'];
		$eael_card_item_height = '';
		if ( !empty( $card['eael_stacked_card_item_height']['size'] ) ) {
			$eael_card_item_height = sprintf(
					'height: %s%s',
					esc_attr($card['eael_stacked_card_item_height']['size']),
					esc_attr($card['eael_stacked_card_item_height']['unit'])
			);
		}
		?>
		<div <?php $this->print_render_attribute_string( 'eael-stacked-card' ); ?> style="flex-direction: <?php esc_attr_e( $flex_direction ); ?>; <?php //esc_attr_e( $eael_card_item_height ); ?>">
			<?php 
			if( $card['eael_stacked_card_list_type'] !== 'none' ) {
				?>
					<div class="eael-stacked-cards__media">
					<?php
					// Get image width with unit, default to empty if not set
					if( $card['eael_stacked_card_list_type'] === 'image' ) {
						printf( '<img class="eael-stacked-cards__image" src="%s" alt="">', esc_url($card['eael_stacked_card_image']['url']) );
					} elseif ( $card['eael_stacked_card_list_type'] === 'video' ) {
						$eael_video_url = $card['eael_stacked_card_video']['url'];
						if ( !empty ( $eael_video_url ) ) {
							?>
							<video playsinline autoplay muted loop src="<?php echo esc_attr( $eael_video_url ); ?>" class="elementor-video"></video>
							<?php
						}
						?>
						<?php
					} elseif ( $card['eael_stacked_card_list_type'] === 'icon' ) {
						\Elementor\Icons_Manager::render_icon( $card['eael_stacked_card_icon'], [ 'aria-hidden' => 'true' ] );
					}
					?>
					</div>
				<?php
			}
			?>
			
			<div class="eael-stacked-cards__content">
				<div class="eael-stacked-cards__body">
						<?php 
						if( $card['eael_stacked_card_item_content_media'] !== 'none' ) {
							?>
								<div class="eael-stacked-cards__content__media">
								<?php 
								if( $card['eael_stacked_card_item_content_media'] === 'image' ) {
									printf( '<img class="eael-stacked-cards__content__image" src="%s" alt="">', esc_url( $card['eael_stacked_card_item_content_image']['url'] ) );
								} elseif ( $card['eael_stacked_card_item_content_media'] === 'icon' ) {
									\Elementor\Icons_Manager::render_icon( $card['eael_stacked_card_item_content_icon'], [ 'aria-hidden' => 'true' ] );
								}
								?>
								</div>
							<?php
						}
						?>
						<<?php echo Helper::eael_validate_html_tag( $card['eael_stacked_card_item_title_tag'] ); ?> class="eael-stacked-cards__title">
							<?php echo esc_html( $card['eael_stacked_card_item_title'] ); ?>
						</<?php echo Helper::eael_validate_html_tag( $card['eael_stacked_card_item_title_tag'] ); ?>>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->parse_text_editor( $card['eael_stacked_card_item_content'] );

						if ($card['eael_stacked_card_item_content_show_button'] === 'yes') {
							$btn_style = [
									'color' => $card['eael_stacked_card_content_btn_normal_color'],
									'background-color' => $card['eael_stacked_card_content_btn_normal_bg_color'],
							];

							$btn_style_hover = [
								'color' => $card['eael_stacked_card_content_btn_hover_color'],
								'background-color' => $card['eael_stacked_card_content_btn_hover_bg_color'],
							];

							$normal_string = '';
							foreach ($btn_style as $property => $value) {
								$normal_string .= "{$property}: {$value}; ";
							}
							$hover_style = '';
							foreach ($btn_style_hover as $property => $value) {
								$hover_style .= "{$property}: {$value}; ";
							}

							$button_key = 'stacked_cards_btn_' . $key;
							$this->add_render_attribute($button_key, [
								'class' => 'eael-stacked-cards__link',
								'style' => trim($normal_string),
								'data-hover-style' => trim($hover_style),
							]);
							
							// Add link attributes if URL exists
							if (!empty($card['eael_stacked_card_item_content_button_link']['url'])) {
								$this->add_link_attributes($button_key, $card['eael_stacked_card_item_content_button_link']);
							}
							
							// Render button with unique attribute key
							echo '<a ' . $this->get_render_attribute_string($button_key) . '>' . esc_html($card['eael_stacked_card_item_content_button_text']) . '</a>';
						}								 
						?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function eael_render_template_card( $card, $key, $page_id ) {
		$current_page_id = get_the_ID();
		$revisions       = wp_get_post_revisions( $current_page_id );
		$revision_ids    = wp_list_pluck( $revisions, 'ID' );

		if ( absint( $card['eael_stacked_card_primary_templates'] ) === $current_page_id || in_array( absint( $card['eael_stacked_card_primary_templates'] ), $revision_ids, true ) ) {
			_e( '<p>The provided Template matches the current page or one of its revisions!</p>' );
		} else {
			?>
			<div <?php $this->print_render_attribute_string( 'eael-stacked-card' ); ?>>
			<?php
			Helper::eael_onpage_edit_template_markup( $page_id, $card['eael_stacked_card_primary_templates'] );
			echo Plugin::$instance->frontend->get_builder_content( $card['eael_stacked_card_primary_templates'], true );
			?>
			</div>
			<?php
		}
	}

	protected function get_scroll_trigger_data( $settings ) {
		$eael_card_data = $this->eael_stacked_card_data( $settings );
		$scroll_trigger_obj = [
			'marker'      => $eael_card_data['marker'],
			'start'       => $eael_card_data['start'],
			'widgetId'    => $this->get_id(),
		];

		if ( 'horizontal' === $settings['eael_stacked_card_style'] ) {
			$scroll_trigger_obj['end'] = $eael_card_data['hr_end'];
		}
		
		if( 'vertical' === $settings['eael_stacked_card_style'] ) {
			if( $eael_card_data['end_from'] === 'custom' ) {
				$scroll_trigger_obj['end'] = $eael_card_data['end'];
			} else {
				$scroll_trigger_obj['end'] = 'default';
			}
		}
		return $scroll_trigger_obj;
	}

	protected function add_container_attributes( $settings ) {
		$scroll_trigger_data = $this->get_scroll_trigger_data( $settings );
		$this->remove_render_attribute( 'eael-stacked-card-scrolltrigger' );
		$this->add_render_attribute( 'eael-stacked-card-scrolltrigger', [
			'data-id'            => $this->get_id(),
			'class'              => 'eael-stacked-cards__container',
			'data-cadr_style'    => $settings['eael_stacked_card_style'],
			'data-scrolltrigger' => json_encode( $scroll_trigger_data ),
		] );
	}

	protected function calculate_scale_x( $total_items, $key ) {
		$sum = 1;
		$sum_values = [];
		for ($i = 0; $i < $total_items; $i++) {
			$sum -= 0.04;
			$sum_values[] = $sum;
		}
		return array_reverse($sum_values)[ $key ] ?? .5;
	}

	protected function get_setting_value($settings, $key, $default = '') {
		return !empty($settings[$key]['size']) ? $settings[$key]['size'] : $default;
	}

	protected function get_filter_effect( $settings ) {
		$filter_type = $settings['eael_stacked_card_filter'];
		$filter_value = [
			'blur'      => "blur({$this->get_setting_value( $settings, 'eael_stacked_card_blur' )}px)",
			'opacity'   => "opacity({$this->get_setting_value( $settings, 'eael_stacked_card_opacity' )}%)",
			'grayscale' => "grayscale({$this->get_setting_value( $settings, 'eael_stacked_card_grayscale' )}%)",
			'sepia'     => "sepia({$this->get_setting_value( $settings, 'eael_stacked_card_sepia' )}%)",
		];
		return $filter_value[ $filter_type ] ?? '';
	}

	protected function add_transform_effects( &$animation, $settings, $key ) {
		$trans_form_type = $settings['eael_stacked_card_transform'];
		switch ($trans_form_type) {
			case 'rotate':
				$rotation = $this->get_setting_value($settings, 'eael_stacked_card_rotation');
				$animation['rotation'] = $key % 2 === 0 ? $rotation : -$rotation;
				break;
			case 'scale':
				$animation['scale'] = $this->get_setting_value( $settings, 'eael_stacked_card_scale' );
				break;
			case 'translate':
				$translate_value = $this->get_setting_value($settings, 'eael_stacked_card_translate');
				$translate_number = (float) filter_var(
					$translate_value, 
					FILTER_SANITIZE_NUMBER_FLOAT, 
					FILTER_FLAG_ALLOW_FRACTION
				);
				$animation['y'] = $translate_number * $key;
            $animation['scaleX'] = $this->calculate_scale_x( count($settings['eael_stacked_card_list']), $key );
				break;
		}
	}

	protected function get_card_animation_settings( $settings, $key ) {
		$eael_card_data = $this->eael_stacked_card_data( $settings );
		$animation = [
			// 'duration'     => 1,
			'ease'         => "none",
			'opacity' => $eael_card_data['item_opacity'],
		];

		$animation['filter'] = $this->get_filter_effect( $settings );
		$this->add_transform_effects( $animation, $settings, $key );

		return $animation;
	}

	protected function add_card_item_attributes( $settings, $card, $key ) {
		$eael_card_data = $this->eael_stacked_card_data( $settings );
		$card_bg_color = $card['eael_stacked_card_bg_color'] ? $card['eael_stacked_card_bg_color'] : '#d7d7fb';
		$animated_card_obj = $this->get_card_animation_settings( $settings, $key );

		// Reset the 'eael-stacked-card' attributes to avoid duplication
		$this->remove_render_attribute( 'eael-stacked-card' );
		if ( 'vertical' === $settings['eael_stacked_card_style'] ) {
			$this->add_render_attribute( 'eael-stacked-card', [
				'class'             => 'eael-stacked-cards__item elementor-repeater-item-' . esc_attr( $card['_id'] ),
				'data-stacked_card' => json_encode( $animated_card_obj ),
				'data-start_form'   => $eael_card_data['start_form'],
				'data-bgColor'      => $card_bg_color,
				'data-yaxis'            => $this->get_setting_value($settings, 'eael_stacked_card_translate'),
			] );
		} elseif ( 'horizontal' === $settings['eael_stacked_card_style'] ) {
			$spacer = 0;
			$animation = [
				'x' => $spacer * ($key + 1),
			];
			if( $settings['eael_stacked_card_transform'] === 'rotate' ) {
				$rotation = $key % 2 === 0 ? $eael_card_data['rotation'] : - $eael_card_data['rotation'];
				$animation['rotation'] = $rotation;
			} elseif( $settings['eael_stacked_card_transform'] === 'scale' ) {
				$animation['scale'] = $eael_card_data['scale'];
			} elseif( $settings['eael_stacked_card_transform'] === 'translate' ) {
				$translate_value = $this->get_setting_value($settings, 'eael_stacked_card_translate');
				$translate_number = (float) filter_var(
					$translate_value, 
					FILTER_SANITIZE_NUMBER_FLOAT, 
					FILTER_FLAG_ALLOW_FRACTION
				);
				$animation['x'] = $translate_number * $key;
			}

			$animated_card_hr = json_encode( $animation );
			$this->add_render_attribute( 'eael-stacked-card', [
				'class'         => 'eael-stacked-cards__item_hr elementor-repeater-item-' . esc_attr( $card['_id'] ),
				'data-stacked_card_hr' => $animated_card_hr,
				'data-bgColor' => $card_bg_color,
				'data-yaxis'    => $this->get_setting_value($settings, 'eael_stacked_card_translate'),
			] );
		}
	}

   protected function render() {
		$settings       = $this->get_settings_for_display();
		$widget_id      = $this->get_id();
		$page_id        = get_the_ID();

		$this->add_container_attributes( $settings );
      ?>
		<div class="eael-stacked-cards" data-widget-id="<?php echo esc_attr($widget_id); ?>">
			<div <?php $this->print_render_attribute_string( 'eael-stacked-card-scrolltrigger' ); ?>>
				<?php 

				foreach ( $settings['eael_stacked_card_list'] as $key => $card ) {
					$this->add_card_item_attributes( $settings, $card, $key );

					if ( 'content' === $card['eael_stacked_card_content_type'] ) {
						$this->eael_render_content_card( $settings, $key, $card );
						
					} elseif ( 'template' === $card['eael_stacked_card_content_type'] ) {
						if ( ! empty( $card['eael_stacked_card_primary_templates'] ) ) {
							$this->eael_render_template_card( $card, $key, $page_id );
						}
					}
				}
				?>
			</div>
		</div>
      <?php
   }
}