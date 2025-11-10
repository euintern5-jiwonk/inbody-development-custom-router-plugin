<?php
namespace Essential_Addons_Elementor\Pro\Extensions;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Controls_Manager;

class Smooth_Animation {
    public function __construct() {
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/frontend/before_render', [ $this, 'before_render' ], 100, 1 );
    }

    public function register_controls( $element ) {
        $element->start_controls_section(
			'eael_smooth_animation_section_controls',
			[
				'label' => __( '<i class="eaicon-logo"></i> Interactive Animations', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

        $element->add_control('eael_smooth_animation_section',
            [
                'label' => __('Enable Interactive Animations', 'essential-addons-elementor'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

		$element->add_control(
			'eael_smooth_animation_event_function',
			[
				'label'   => esc_html__( 'Animation Type', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'to',
				'options' => [
					'to'     => esc_html__( 'Animate To', 'essential-addons-elementor' ),
					'from'   => esc_html__( 'Animate From', 'essential-addons-elementor' ),
				],
				'condition' => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		//Color Setting
		$element->add_control(
			'eael_smooth_animation_event_color_setting',
			[
				'label'              => __( 'Animation Colors', 'essential-addons-elementor' ), 
                'type'               => Controls_Manager::POPOVER_TOGGLE, 
                'return_value'       => 'yes',
				'condition' 		 => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		$element->start_popover();
		$element->add_control(
			'eael_smooth_animation_event_bg_color',
			[
				'label' => esc_html__( 'Bankground Color', 'essential-addons-elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'condition' 		 => [
					'eael_smooth_animation_event_color_setting' => 'yes',
				]
			]
		);
        $element->end_popover();

		//Transformation Setting
		$element->add_control(
			'eael_smooth_animation_event_transform_setting',
			[
				'label'              => __( 'Transform Effects', 'essential-addons-elementor' ), 
                'type'               => Controls_Manager::POPOVER_TOGGLE, 
                'return_value'       => 'yes',
				'condition' 		 => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		$element->start_popover();
        $element->add_control(
			'eael_smooth_animation_event_transform_translatex',
			[
				'label'      => esc_html__( 'TranslateX', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 5,
					],
					'vw' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
					'vh' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 30,
				],
				'ai'    => [ 'active' => false, ],
				'condition' 		 => [
					'eael_smooth_animation_event_transform_setting' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_transform_translatex_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Move the element horizontally by a specified amount', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
				],
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_transform_translatey',
			[
				'label' => esc_html__( 'TranslateY', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw', 'vh' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
					'vw' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
					'vh' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'ai'    => [ 'active' => false, ],
				'condition' 		 => [
					'eael_smooth_animation_event_transform_setting' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_transform_translatey_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Move the element vertically by a specified amount', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
				],
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_transform_opacity',
			[
				'label' => esc_html__( 'Opacity', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => .1,
					],
				],
				'default' => [
					'size' => 1,
				],
				'ai'    => [ 'active' => false, ],
				'condition' 		 => [
					'eael_smooth_animation_event_transform_setting' => 'yes',
				]
			]
		);
		$element->add_control(
			'eael_smooth_animation_event_transform_rotate',
			[
				'label' => esc_html__( 'Rotate', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -360,
						'max' => 360,
						'step' => 1,
					],
				],
				'ai'    => [ 'active' => false, ],
				'condition' 		 => [
					'eael_smooth_animation_event_transform_setting' => 'yes',
				]
			]
		);
        $element->end_popover();

		//Transformation Setting
		$element->add_control(
			'eael_smooth_animation_event_transform_orign_setting',
			[
				'label'              => __( 'Transform Origin Effects', 'essential-addons-elementor' ), 
                'type'               => Controls_Manager::POPOVER_TOGGLE, 
                'return_value'       => 'yes',
				'condition' 		 => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		$element->start_popover();
		//TransformOrigin
		$element->add_control(
			'eael_smooth_animation_transform_originx',
			array(
				'label'       => __( 'Transform Origin X', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [
					''       => __( 'None', 'essential-addons-elementor' ),
					'top'    => __( 'Top', 'essential-addons-elementor' ),
					'left'   => __( 'Left', 'essential-addons-elementor' ),
					'bottom' => __( 'Bottom', 'essential-addons-elementor' ),
					'right'  => __( 'Right', 'essential-addons-elementor' ),
					'custom' => __( 'Custom', 'essential-addons-elementor' ),
				],
				'condition'   => [
					'eael_smooth_animation_section'                 => 'yes',
					'eael_smooth_animation_event_transform_orign_setting' => 'yes',
				]
			)
		);

		$element->add_control(
			'eael_smooth_animation_trans_originx_custom',
			array(
				'label'       => __( 'Custom Origin', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'eael_smooth_animation_transform_originx'       => 'custom',
					'eael_smooth_animation_event_transform_orign_setting' => 'yes',
				),
			)
		);

		$element->add_control(
			'eael_smooth_animation_transform_originx_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Set the horizontal pivot point for transformations', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
				],
			]
		);

		$element->add_control(
			'eael_smooth_animation_transform_originy',
			array(
				'label'       => __( 'Transform Origin Y', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''       => __( 'None', 'essential-addons-elementor' ),
					'top'    => __( 'Top', 'essential-addons-elementor' ),
					'left'   => __( 'Left', 'essential-addons-elementor' ),
					'bottom' => __( 'Bottom', 'essential-addons-elementor' ),
					'right'  => __( 'Right', 'essential-addons-elementor' ),
					'custom' => __( 'Custom', 'essential-addons-elementor' ),
				),
				'label_block' => false,
				'condition'   => [
					'eael_smooth_animation_section'                 => 'yes',
					'eael_smooth_animation_event_transform_orign_setting' => 'yes',
				]
			)
		);
		$element->add_control(
			'eael_smooth_animation_trans_originy_custom',
			array(
				'label'       => __( 'Custom Origin', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'eael_smooth_animation_transform_originy'       => 'custom',
					'eael_smooth_animation_event_transform_orign_setting' => 'yes',
				),
			)
		);

		$element->add_control(
			'eael_smooth_animation_transform_originy_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Set the Vertical pivot point for transformations', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
				],
			]
		);
        $element->end_popover();

		//Scale Setting
		$element->add_control(
			'eael_smooth_animation_event_scale_setting',
			[
				'label'              => __( 'Scaling Options', 'essential-addons-elementor' ), 
                'type'               => Controls_Manager::POPOVER_TOGGLE, 
                'return_value'       => 'yes',
				'condition' 		 => [
					'eael_smooth_animation_section' => 'yes'
				]
			]
		);

		$element->start_popover();

		$element->add_control(
			'eael_smooth_animation_event_scalexy',
			[
				'label'        => esc_html__( 'Keep Proportions', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Off', 'essential-addons-elementor' ),
				'return_value' => 'yes',
			]
		);

        $element->add_control(
			'eael_smooth_animation_event_scale',
			[
				'label' => esc_html__( 'Scale', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => 1.3,
				],
				'ai'    => [ 'active' => false, ],
				'condition' 		 => [
					'eael_smooth_animation_event_scale_setting' => 'yes',
					'eael_smooth_animation_event_scalexy' => '',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_scalex',
			[
				'label' => esc_html__( 'ScaleX', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => 1.3,
				],
				'ai'    => [ 'active' => false, ],
				'condition' 		 => [
					'eael_smooth_animation_event_scale_setting' => 'yes',
					'eael_smooth_animation_event_scalexy' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_scalex_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Scale the width of the element', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_event_scalexy' => 'yes',
				],
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_scaley',
			[
				'label' => esc_html__( 'ScaleY', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => 0.8,
				],
				'ai'    => [ 'active' => false, ],
				'condition' 		 => [
					'eael_smooth_animation_event_scale_setting' => 'yes',
					'eael_smooth_animation_event_scalexy' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_scaley_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Scale the height of the element', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_event_scalexy' => 'yes',
				],
			]
		);

        $element->end_popover();

		//Skew Setting
		$element->add_control(
			'eael_smooth_animation_event_skew_setting',
			[
				'label'              => __( 'Skew Effects', 'essential-addons-elementor' ), 
                'type'               => Controls_Manager::POPOVER_TOGGLE, 
                'return_value'       => 'yes',
				'condition' 		 => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		$element->start_popover();
        // $element->add_control(
		// 	'eael_smooth_animation_event_skew',
		// 	[
		// 		'label' => esc_html__( 'Skew', 'essential-addons-elementor' ),
		// 		'type'  => Controls_Manager::SLIDER,
		// 		'range'      => [
		// 			'px' => [
		// 				'min'  => 0,
		// 				'max'  => 100,
		// 				'step' => 1,
		// 			],
		// 		],
		// 		'ai'    => [ 'active' => false, ],
		// 		'condition' 		 => [
		// 			'eael_smooth_animation_event_skew_setting' => 'yes',
		// 		]
		// 	]
		// );

		$element->add_control(
			'eael_smooth_animation_event_skewx',
			[
				'label' => esc_html__( 'SkewX', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 5,
				],
				'ai'    => [ 'active' => false, ],
				'condition' 		 => [
					'eael_smooth_animation_event_skew_setting' => 'yes',
				]
			]
		);
		$element->add_control(
			'eael_smooth_animation_event_skewy',
			[
				'label' => esc_html__( 'SkewY', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => -5,
				],
				'ai'    => [ 'active' => false, ],
				'condition' 		 => [
					'eael_smooth_animation_event_skew_setting' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_skew_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Skew the element to create an angled effect along the x or y axis.', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
				],
			]
		);

        $element->end_popover();

		//Animation Setting
		$element->add_control(
			'eael_smooth_animation_event_animation_setting',
			[
				'label'              => __( 'Animation Settings', 'essential-addons-elementor' ), 
                'type'               => Controls_Manager::POPOVER_TOGGLE, 
                'return_value'       => 'yes',
				'condition' 		 => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		$element->start_popover();
		$element->add_control(
			'eael_smooth_animation_event_animation_easing',
			[
				'label'   => esc_html__( 'Easing', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'linear',
				'options' => [
					''        => esc_html__( 'Default', 'essential-addons-elementor' ),
					'linear'  => esc_html__( 'Linear', 'essential-addons-elementor' ),
					'back'    => esc_html__( 'Back', 'essential-addons-elementor' ),
					'power'   => esc_html__( 'Power', 'essential-addons-elementor' ),
					'bounce'  => esc_html__( 'Bounce', 'essential-addons-elementor' ),
					'circ'    => esc_html__( 'Circ', 'essential-addons-elementor' ),
					'elastic' => esc_html__( 'Elastic', 'essential-addons-elementor' ),
					'expo'    => esc_html__( 'Expo', 'essential-addons-elementor' ),
					'sine'    => esc_html__( 'Sine', 'essential-addons-elementor' ),
					'steps'   => esc_html__( 'Steps', 'essential-addons-elementor' ),
				],
				'condition' 		 => [
					'eael_smooth_animation_event_animation_setting' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_animation_easing_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Adjust how the animation progresses over time for different effects', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
				],
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_animation_easing_type',
			[
				'label'   => esc_html__( 'Easing Type', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'in',
				'options' => [
					'in'    => esc_html__( 'In', 'essential-addons-elementor' ),
					'out'   => esc_html__( 'Out', 'essential-addons-elementor' ),
					'inOut' => esc_html__( 'InOut', 'essential-addons-elementor' ),
				],
				'condition' => [
					'eael_smooth_animation_event_animation_setting' => 'yes',
					'eael_smooth_animation_event_animation_easing' => ['back', 'power', 'bounce', 'circ', 'elastic', 'expo', 'sine'],
				]
			]
		);
		$element->add_control(
			'eael_smooth_animation_event_animation_yoyo',
			[
				'label'        => esc_html__( 'Yoyo', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'default' => 'true',
				'condition'    => [
					'eael_smooth_animation_event_animation_setting' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_animation_yoyo_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Makes the animation reverse direction on each repeat', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
				],
			]
		);

        $element->add_control(
			'eael_smooth_animation_event_animation_stagger',
			[
				'label'     => esc_html__( 'Stagger', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 10,
				'step'      => 0.1,
				'ai'        => [ 'active'      =>false, ],
				'condition' => [
					'eael_smooth_animation_event_animation_setting' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_animation_stagger_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Add a delay between the start times of animations on multiple elements', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
				],
			]
		);

        $element->end_popover();

		//Manual Setting
		$element->add_control(
			'eael_smooth_animation_event_manual_setting',
			[
				'label'              => __( 'Custom Animation Config', 'essential-addons-elementor' ), 
                'type'               => Controls_Manager::POPOVER_TOGGLE, 
                'return_value'       => 'yes',
				'condition' 		 => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		$element->start_popover();
        $element->add_control(
			'eael_smooth_animation_event_duration',
			[
				'label' => esc_html__( 'Duration', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 3,
				],
				'ai'    => [ 'active' => false, ],
				'condition' 		 => [
					'eael_smooth_animation_event_manual_setting' => 'yes',
				]
			]
		);
		$element->add_control(
			'eael_smooth_animation_event_delay',
			[
				'label' => esc_html__( 'Delay', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'ai'    => [ 'active' => false, ],
				'condition' 		 => [
					'eael_smooth_animation_event_manual_setting' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_loop',
			[
				'label'   => esc_html__( 'Loop Count', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => -1,
				'max'     => 100,
				'step'    => 1,
				'default' => 10,
				'condition' 		 => [
					'eael_smooth_animation_event_manual_setting' => 'yes',
				]
			]
		);

        $element->end_popover();

		//Custom Trigger
		$element->add_control(
			'eael_smooth_animation_event_core_custom_trigger',
			[
				'label'       => esc_html__( 'Target Element', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'ai'          => [ 'active'                 =>false, ],
				'placeholder' => esc_html__( 'my-element', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_core_custom_trigger_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( '(Optional) Enter the CSS selector of the element to animate, like: <strong>my-element</strong>', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
				],
			]
		);

		$element->add_control(
			'eael_smooth_animation_scroll_trigger_before',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
				'condition' => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_scroll_trigger_heading',
			[
				'label'     => esc_html__( 'ScrollTrigger Options', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'condition' => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_scroll_trigger',
			[
				'label'        => esc_html__( 'Enable ScrollTrigger', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'condition'    => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_canvas_start',
			[
				'label' => esc_html__( 'Scroll Start Point', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				]
			]
		);

		$element->start_popover();
        $element->add_control(
			'eael_smooth_animation_event_canvas_element_start',
			array(
				'label'       => __( 'Start Element', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''       => __( 'None', 'essential-addons-elementor' ),
					'top'    => __( 'Top', 'essential-addons-elementor' ),
					'bottom' => __( 'Bottom', 'essential-addons-elementor' ),
					'center'  => __( 'Center', 'essential-addons-elementor' ),
					'custom' => __( 'Custom', 'essential-addons-elementor' ),
				),
				'label_block' => false,
				'condition'   => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_event_canvas_start' => 'yes',
				]
			)
		);
		$element->add_control(
			'eael_smooth_animation_event_canvas_element_start_custom',
			array(
				'label'       => __( 'Custom Value', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'eael_smooth_animation_event_canvas_element_start' => 'custom',
					'eael_smooth_animation_event_canvas_start' => 'yes',
				),
			)
		);

		$element->add_control(
			'eael_smooth_animation_event_canvas_controller_start',
			array(
				'label'       => __( 'Start Controller', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''       => __( 'None', 'essential-addons-elementor' ),
					'top'    => __( 'Top', 'essential-addons-elementor' ),
					'bottom' => __( 'Bottom', 'essential-addons-elementor' ),
					'center'  => __( 'Center', 'essential-addons-elementor' ),
					'custom' => __( 'Custom', 'essential-addons-elementor' ),
				),
				'label_block' => false,
				'condition'   => [
					'eael_smooth_animation_event_canvas_start' => 'yes',
				]
			)
		);

		$element->add_control(
			'eael_smooth_animation_event_canvas_controller_start_custom',
			array(
				'label'       => __( 'Custom Value', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'eael_smooth_animation_event_canvas_controller_start' => 'custom',
					'eael_smooth_animation_event_canvas_start' => 'yes',
				),
			)
		);
        $element->end_popover();

		$element->add_control(
			'eael_smooth_animation_event_canvas_end',
			[
				'label' => esc_html__( 'Start End Point', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				]
			]
		);

		$element->start_popover();
        $element->add_control(
			'eael_smooth_animation_event_canvas_element_end',
			array(
				'label'       => __( 'End Element', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''       => __( 'None', 'essential-addons-elementor' ),
					'top'    => __( 'Top', 'essential-addons-elementor' ),
					'bottom' => __( 'Bottom', 'essential-addons-elementor' ),
					'center' => __( 'Center', 'essential-addons-elementor' ),
					'custom' => __( 'Custom', 'essential-addons-elementor' ),
				),
				'label_block' => false,
				'condition'   => [
					'eael_smooth_animation_event_canvas_end' => 'yes',
				]
			)
		);
		$element->add_control(
			'eael_smooth_animation_event_canvas_element_end_custom',
			array(
				'label'       => __( 'Custom Value', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'eael_smooth_animation_event_canvas_element_end' => 'custom',
					'eael_smooth_animation_event_canvas_end' => 'yes',
				),
			)
		);

		$element->add_control(
			'eael_smooth_animation_event_canvas_controller_end',
			array(
				'label'       => __( 'End Controller', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''       => __( 'None', 'essential-addons-elementor' ),
					'top'    => __( 'Top', 'essential-addons-elementor' ),
					'bottom' => __( 'Bottom', 'essential-addons-elementor' ),
					'center' => __( 'Center', 'essential-addons-elementor' ),
					'custom' => __( 'Custom', 'essential-addons-elementor' ),
				),
				'label_block' => false,
				'condition'   => [
					'eael_smooth_animation_event_canvas_end' => 'yes',
				]
			)
		);

		$element->add_control(
			'eael_smooth_animation_event_canvas_controller_end_custom',
			array(
				'label'       => __( 'Custom Value', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'eael_smooth_animation_event_canvas_controller_end' => 'custom',
					'eael_smooth_animation_event_canvas_end' => 'yes',
				),
			)
		);
        $element->end_popover();

		$element->add_control(
			'eael_smooth_animation_event_markers',
			[
				'label'        => esc_html__( 'Enable Markers', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'condition'    => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_markers_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Show markers during development to see where animations start and end', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				],
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_canvas_scrub',
			[
				'label'        => esc_html__( 'Scrubbing Options', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition'    => [
					'eael_smooth_animation_section'        => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				]
			]
		);

		$element->start_popover();
		$element->add_control(
			'eael_smooth_animation_event_scrub_settings',
			array(
				'label'       => __( 'Select Options', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'true',
				'options'     => array(		
					'true' => __( 'Default', 'essential-addons-elementor' ),
					'custom'  => __( 'Custom', 'essential-addons-elementor' ),
				),
				'condition'   => [
					'eael_smooth_animation_event_canvas_scrub' => 'yes',
				]
			)
		);

		// $element->add_control(
		// 	'eael_smooth_animation_event_scrub_setting_default',
		// 	[
		// 		'label'        => esc_html__( 'Select Default', 'essential-addons-elementor' ),
		// 		'type'         => Controls_Manager::SWITCHER,
		// 		'label_on'     => esc_html__( 'True', 'essential-addons-elementor' ),
		// 		'label_off'    => esc_html__( 'False', 'essential-addons-elementor' ),
		// 		'return_value' => 'true',
		// 		'condition'    => [
		// 			'eael_smooth_animation_event_scrub_settings' => 'default',
		// 			'eael_smooth_animation_event_canvas_scrub' => 'yes',
		// 		]
		// 	]
		// );

		$element->add_control(
			'eael_smooth_animation_event_canvas_scrub_custom',
			[
				'label' => esc_html__( 'Custom Scrub', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => .1,
					],
				],
				'condition' => [
					'eael_smooth_animation_event_scrub_settings' => 'custom',
					'eael_smooth_animation_event_canvas_scrub' => 'yes',
				]
			]
		);
        $element->end_popover();

		$element->add_control(
			'eael_smooth_animation_event_canvas_scrub_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Allow the animation to sync with the scroll position', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				],
			]
		);

		$element->add_control(
			'eael_smooth_animation_toggle_actions',
			[
				'label' => esc_html__( 'ToggleActions', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				]
			]
		);

		$element->start_popover();
        $element->add_control(
			'eael_smooth_animation_toggle_actions_on_enter',
			array(
				'label'       => __( 'On Enter', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => array(
					'none'     => __( 'None', 'essential-addons-elementor' ),
					'restart'  => __( 'Restart', 'essential-addons-elementor' ),
					'resume'   => __( 'Resume', 'essential-addons-elementor' ),
					'reverse'  => __( 'Reverse', 'essential-addons-elementor' ),
					'reset'    => __( 'Reset', 'essential-addons-elementor' ),
					'play'     => __( 'Play', 'essential-addons-elementor' ),
					'pause'    => __( 'Pause', 'essential-addons-elementor' ),
					'complete' => __( 'Complete', 'essential-addons-elementor' ),
				),
				'label_block' => false,
				'condition'   => [
					'eael_smooth_animation_toggle_actions' => 'yes',
				]
			)
		);

		$element->add_control(
			'eael_smooth_animation_toggle_actions_on_leave',
			array(
				'label'       => __( 'On Leave', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => array(
					'none'     => __( 'None', 'essential-addons-elementor' ),
					'restart'  => __( 'Restart', 'essential-addons-elementor' ),
					'resume'   => __( 'Resume', 'essential-addons-elementor' ),
					'reverse'  => __( 'Reverse', 'essential-addons-elementor' ),
					'reset'    => __( 'Reset', 'essential-addons-elementor' ),
					'play'     => __( 'Play', 'essential-addons-elementor' ),
					'pause'    => __( 'Pause', 'essential-addons-elementor' ),
					'complete' => __( 'Complete', 'essential-addons-elementor' ),
				),
				'label_block' => false,
				'condition'   => [
					'eael_smooth_animation_toggle_actions' => 'yes',
				]
			)
		);

		$element->add_control(
			'eael_smooth_animation_toggle_actions_on_enter_back',
			array(
				'label'       => __( 'On Enter Back', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => array(
					'none'     => __( 'None', 'essential-addons-elementor' ),
					'restart'  => __( 'Restart', 'essential-addons-elementor' ),
					'resume'   => __( 'Resume', 'essential-addons-elementor' ),
					'reverse'  => __( 'Reverse', 'essential-addons-elementor' ),
					'reset'    => __( 'Reset', 'essential-addons-elementor' ),
					'play'     => __( 'Play', 'essential-addons-elementor' ),
					'pause'    => __( 'Pause', 'essential-addons-elementor' ),
					'complete' => __( 'Complete', 'essential-addons-elementor' ),
				),
				'label_block' => false,
				'condition'   => [
					'eael_smooth_animation_toggle_actions' => 'yes',
				]
			)
		);

		$element->add_control(
			'eael_smooth_animation_toggle_actions_on_leave_back',
			array(
				'label'       => __( 'On Leave Back', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => array(
					'none'     => __( 'None', 'essential-addons-elementor' ),
					'restart'  => __( 'Restart', 'essential-addons-elementor' ),
					'resume'   => __( 'Resume', 'essential-addons-elementor' ),
					'reverse'  => __( 'Reverse', 'essential-addons-elementor' ),
					'reset'    => __( 'Reset', 'essential-addons-elementor' ),
					'play'     => __( 'Play', 'essential-addons-elementor' ),
					'pause'    => __( 'Pause', 'essential-addons-elementor' ),
					'complete' => __( 'Complete', 'essential-addons-elementor' ),
				),
				'label_block' => false,
				'condition'   => [
					'eael_smooth_animation_toggle_actions' => 'yes',
				]
			)
		);
        $element->end_popover();

		$element->add_control(
			'eael_smooth_animation_toggle_actions_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Define what actions occur at each scroll trigger point', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				],
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_pin',
			[
				'label'        => esc_html__( 'Pin Settings', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition'    => [
					'eael_smooth_animation_section'        => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				]
			]
		);

		$element->start_popover();

		$element->add_control(
			'eael_smooth_animation_event_pin_setting_default',
			[
				'label'        => esc_html__( 'Enable Pin', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'True', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'False', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'condition'    => [
					'eael_smooth_animation_event_pin' => 'yes',
				]
			]
		);

        $element->end_popover();

		$element->add_control(
			'eael_smooth_animation_event_pin_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'Pin elements to the screen during the scroll for a fixed effect', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				],
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_scroll_custom_trigger',
			[
				'label'     => esc_html__( 'Trigger Element', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'ai'        => [ 'active'                 =>false, ],
				'placeholder' => esc_html__( 'my-element', 'essential-addons-elementor' ),
				'condition' => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				]
			]
		);

		$element->add_control(
			'eael_smooth_animation_event_scroll_custom_trigger_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( '(Optional) Enter the CSS selector for the element that triggers the animation when scrolled into view like: <strong>my-element</strong>', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'eael_smooth_animation_section' => 'yes',
					'eael_smooth_animation_scroll_trigger' => 'true',
				],
			]
		);

		$element->add_control(
			'eael_smooth_animation_scroll_trigger_after',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
				'condition' => [
					'eael_smooth_animation_section' => 'yes',
				]
			]
		);

		//
		$element->add_control('eael_smooth_animation_update',
            [
                'label' => '<div class="elementor-update-preview" style="display: block;"><div class="elementor-update-preview-button-wrapper" style="display:block;"><button class="elementor-update-preview-button elementor-button elementor-button-success" style="background: #d30c5c; margin: 0 auto; display:block;">Apply Changes</button></div><div class="elementor-update-preview-title" style="display:block;text-align:center;margin-top: 10px;">Update changes to page</div></div>',
                'type' => Controls_Manager::RAW_HTML,
                'condition' => [
                    'eael_smooth_animation_section' => 'yes',
                ],
            ]
        );

        $element->end_controls_section();
    }

    public function before_render( $element ) {
        $settings = $element->get_settings_for_display();

        if( !empty( $settings['eael_smooth_animation_section'] ) ) {
			
			$transform_attributes = [
				'eael_smooth_animation_event_transform_translatex' => 'translatex',
				'eael_smooth_animation_event_transform_translatey' => 'translatey',
				'eael_smooth_animation_trans_originx_custom' => 'custom_transformoriginx',
				'eael_smooth_animation_trans_originy_custom' => 'custom_transformoriginy',
				'eael_smooth_animation_event_transform_opacity' => 'opacity',
				'eael_smooth_animation_event_transform_rotate' => 'rotation',
				'eael_smooth_animation_event_scale' => 'scale',
				'eael_smooth_animation_event_scalex' => 'scalex',
				'eael_smooth_animation_event_scaley' => 'scaley',
				'eael_smooth_animation_event_skewx' => 'skewx',
				'eael_smooth_animation_event_skewy' => 'skewy',
				'eael_smooth_animation_event_duration' => 'duration',
				'eael_smooth_animation_event_delay' => 'delay',
			];

			foreach ( $transform_attributes as $settings_key => $attributes_key ) {
				if ( ! empty( $settings[ $settings_key ] ) ) {
					$value = [];
					if ( strpos( $settings_key, 'custom' ) !== false ) {
						if ( ! empty( $settings[ $settings_key ]['size'] ) ) {
							$value['size'] = $settings[ $settings_key ]['size'];
							$value['unit'] = $settings[ $settings_key ]['unit'];
						}
					} 
					elseif ( strpos( $settings_key, 'translatex' ) !== false || strpos( $settings_key, 'translatey' ) !== false ) {
						if ( ! empty( $settings[ $settings_key ]['size'] ) ) {
							$value['size'] = $settings[ $settings_key ]['size'];
							$value['unit'] = $settings[ $settings_key ]['unit'];
						}
					} 
					elseif ( strpos( $attributes_key, 'opacity') !== false || strpos( $attributes_key, 'rotation') !== false 
					|| strpos( $attributes_key, 'scale') !== false || strpos( $attributes_key, 'scalex') !== false 
					|| strpos( $attributes_key, 'scaley') !== false || strpos( $attributes_key, 'skewx') !== false 
					|| strpos( $attributes_key, 'skewy') !== false || strpos( $attributes_key, 'duration') !== false 
					|| strpos( $attributes_key, 'delay') !== false ) {
						if ( ! empty( $settings[ $settings_key ]['size'] ) ) {
							$value[ $attributes_key ] = $settings[ $settings_key ]['size'];
						}
					}
					if ( ! empty( $value ) ) {
						$element->add_render_attribute( '_wrapper', 'data-'.$attributes_key, wp_json_encode( $value ) );
					}
				}
			}

			//Tween Function
			if( ! empty( $settings['eael_smooth_animation_event_function'] ) ) {
				$event_function = [
					'tween' => $settings['eael_smooth_animation_event_function']
				];
				$element->add_render_attribute( '_wrapper', 'data-event_function', wp_json_encode( $event_function ) );
			}

			//Transform OriginX
			if( ! empty( $settings['eael_smooth_animation_transform_originx'] ) && "custom" !== $settings['eael_smooth_animation_transform_originx'] ) {
				$transform_origin_x = [
					'transformoriginx' => $settings['eael_smooth_animation_transform_originx']
				];
				$element->add_render_attribute( '_wrapper', 'data-transformoriginx', wp_json_encode( $transform_origin_x ) );
			}
			
			//Transform OriginY
			if( ! empty( $settings['eael_smooth_animation_transform_originy'] ) && "custom" !== $settings['eael_smooth_animation_transform_originy'] ) {
				$transform_origin_y = [
					'transformoriginy' => $settings['eael_smooth_animation_transform_originy']
				];
				$element->add_render_attribute( '_wrapper', 'data-transformoriginy', wp_json_encode( $transform_origin_y ) );
			}

			//Animation Settings
			//Easing
			$ease_type = '';
			if( ! empty( $settings['eael_smooth_animation_event_animation_easing'] ) ) {
				$ease_type = isset( $settings['eael_smooth_animation_event_animation_easing_type'] ) ? '.'.$settings['eael_smooth_animation_event_animation_easing_type'] : '';
				$ease = [
					'ease' => $settings['eael_smooth_animation_event_animation_easing'] . $ease_type,
				];
				$element->add_render_attribute( '_wrapper', 'data-ease', wp_json_encode( $ease ) );
			}

			//ScaleXY
			if( ! empty( $settings['eael_smooth_animation_event_scalexy'] ) ) {
				$scalexy = [
					'scalexy' => $settings['eael_smooth_animation_event_scalexy'],
				];
				$element->add_render_attribute( '_wrapper', 'data-scalexy', wp_json_encode( $scalexy ) );
			}

			//Yoyo
			if( ! empty( $settings['eael_smooth_animation_event_animation_yoyo'] ) ) {
				$yoyo = [
					'yoyo' => $settings['eael_smooth_animation_event_animation_yoyo'],
				];
				$element->add_render_attribute( '_wrapper', 'data-yoyo', wp_json_encode( $yoyo ) );
			}
			//Stagger
			if( ! empty( $settings['eael_smooth_animation_event_animation_stagger'] ) ) {
				$stagger = [
					'stagger' => $settings['eael_smooth_animation_event_animation_stagger']
				];
				$element->add_render_attribute( '_wrapper', 'data-stagger', wp_json_encode( $stagger ) );
			}
			//Loop
			if( ! empty( $settings['eael_smooth_animation_event_loop'] ) ) {
				$repeat = [
					'repeat' => $settings['eael_smooth_animation_event_loop']
				];
				$element->add_render_attribute( '_wrapper', 'data-repeat', wp_json_encode( $repeat ) );
			}
			//BG Color
			if( ! empty( $settings['eael_smooth_animation_event_bg_color'] ) ) {
				$bg_color = [
					'bg_color' => $settings['eael_smooth_animation_event_bg_color']
				];
				$element->add_render_attribute( '_wrapper', 'data-bg_color', wp_json_encode( $bg_color ) );
			}
			//Core Custom Trigger
			if( ! empty( $settings['eael_smooth_animation_event_core_custom_trigger'] ) ) {
				$core_trigger = [
					'core_trigger' => $settings['eael_smooth_animation_event_core_custom_trigger']
				];
				$element->add_render_attribute( '_wrapper', 'data-coretrigger', $core_trigger['core_trigger'] );
			}

			//ScrollTrigger
			if( !empty( $settings['eael_smooth_animation_scroll_trigger'] ) ) {
				//ScrollTrigger Custom Trigger
				if( ! empty( $settings['eael_smooth_animation_event_scroll_custom_trigger'] ) ) {
					$scroll_trigger = [
						'scroll_trigger' => $settings['eael_smooth_animation_event_scroll_custom_trigger']
					];
					$element->add_render_attribute( '_wrapper', 'id', $scroll_trigger['scroll_trigger'] );
				}

				//Start
				if( ! empty( $settings['eael_smooth_animation_event_canvas_element_start'] ) && "custom" !== $settings['eael_smooth_animation_event_canvas_element_start'] ) {
					$element_start = [
						'element_start' => $settings['eael_smooth_animation_event_canvas_element_start']
					];
					$element->add_render_attribute( '_wrapper', 'data-element_start', wp_json_encode( $element_start ) );
				}
				//Custom
				if( ! empty( $settings['eael_smooth_animation_event_canvas_element_start_custom'] ) ) {
					$custom_element_start = [
						'size' => $settings['eael_smooth_animation_event_canvas_element_start_custom']['size'],
						'unit' => $settings['eael_smooth_animation_event_canvas_element_start_custom']['unit']
					];
					$element->add_render_attribute( '_wrapper', 'data-custom_element_start', wp_json_encode( $custom_element_start ) );
				}
				//Controller Start
				if( ! empty( $settings['eael_smooth_animation_event_canvas_controller_start'] ) && "custom" !== $settings['eael_smooth_animation_event_canvas_controller_start'] ) {
					$controller_start = [
						'controller_start' => $settings['eael_smooth_animation_event_canvas_controller_start']
					];
					$element->add_render_attribute( '_wrapper', 'data-controller_start', wp_json_encode( $controller_start ) );
				}
				//Custom
				if( ! empty( $settings['eael_smooth_animation_event_canvas_controller_start_custom'] ) ) {
					$custom_controller_start = [
						'size' => $settings['eael_smooth_animation_event_canvas_controller_start_custom']['size'],
						'unit' => $settings['eael_smooth_animation_event_canvas_controller_start_custom']['unit']
					];
					$element->add_render_attribute( '_wrapper', 'data-custom_controller_start', wp_json_encode( $custom_controller_start ) );
				}

				//End
				if( ! empty( $settings['eael_smooth_animation_event_canvas_element_end'] ) && "custom" !== $settings['eael_smooth_animation_event_canvas_element_end'] ) {
					$element_end = [
						'element_end' => $settings['eael_smooth_animation_event_canvas_element_end']
					];
					$element->add_render_attribute( '_wrapper', 'data-element_end', wp_json_encode( $element_end ) );
				}
				//Custom
				if( ! empty( $settings['eael_smooth_animation_event_canvas_element_end_custom'] ) ) {
					$custom_element_end = [
						'size' => $settings['eael_smooth_animation_event_canvas_element_end_custom']['size'],
						'unit' => $settings['eael_smooth_animation_event_canvas_element_end_custom']['unit']
					];
					$element->add_render_attribute( '_wrapper', 'data-custom_element_end', wp_json_encode( $custom_element_end ) );
				}
				//Controller End
				if( ! empty( $settings['eael_smooth_animation_event_canvas_controller_end'] ) && "custom" !== $settings['eael_smooth_animation_event_canvas_controller_end'] ) {
					$controller_end = [
						'controller_end' => $settings['eael_smooth_animation_event_canvas_controller_end']
					];
					$element->add_render_attribute( '_wrapper', 'data-controller_end', wp_json_encode( $controller_end ) );
				}
				//Custom
				if( ! empty( $settings['eael_smooth_animation_event_canvas_controller_end_custom'] ) ) {
					$custom_controller_end = [
						'size' => $settings['eael_smooth_animation_event_canvas_controller_end_custom']['size'],
						'unit' => $settings['eael_smooth_animation_event_canvas_controller_end_custom']['unit']
					];
					$element->add_render_attribute( '_wrapper', 'data-custom_controller_end', wp_json_encode( $custom_controller_end ) );
				}
				//Markers
				if( ! empty( $settings['eael_smooth_animation_event_markers'] ) ) {
					$markers = [
						'markers' => $settings['eael_smooth_animation_event_markers']
					];
					$element->add_render_attribute( '_wrapper', 'data-markers', wp_json_encode( $markers ) );
				}
				//Scrub
				if( ! empty( $settings['eael_smooth_animation_event_scrub_settings'] ) ) {
					$scrub = [
						'scrub' => $settings['eael_smooth_animation_event_scrub_settings']
					];
					$element->add_render_attribute( '_wrapper', 'data-scrub', wp_json_encode( $scrub ) );
				}
				//Scrub Custom
				if( ! empty( $settings['eael_smooth_animation_event_canvas_scrub_custom'] ) ) {
					$scrub = [
						'scrub' => $settings['eael_smooth_animation_event_canvas_scrub_custom']['size']
					];
					$element->add_render_attribute( '_wrapper', 'data-scrub', wp_json_encode( $scrub ) );
				}
				//Toggle Actions
				if( ! empty( $settings['eael_smooth_animation_toggle_actions_on_enter'] ) ) {
					$toggle_actions_on_enter = [
						'toggle_actions_on_enter' => $settings['eael_smooth_animation_toggle_actions_on_enter']
					];
					$element->add_render_attribute( '_wrapper', 'data-toggle_actions_on_enter', wp_json_encode( $toggle_actions_on_enter ) );
				}
				if( ! empty( $settings['eael_smooth_animation_toggle_actions_on_leave'] ) ) {
					$toggle_actions_on_leave = [
						'toggle_actions_on_leave' => $settings['eael_smooth_animation_toggle_actions_on_leave']
					];
					$element->add_render_attribute( '_wrapper', 'data-toggle_actions_on_leave', wp_json_encode( $toggle_actions_on_leave ) );
				}
				if( ! empty( $settings['eael_smooth_animation_toggle_actions_on_enter_back'] ) ) {
					$toggle_actions_on_enter_back = [
						'toggle_actions_on_enter_back' => $settings['eael_smooth_animation_toggle_actions_on_enter_back']
					];
					$element->add_render_attribute( '_wrapper', 'data-toggle_actions_on_enter_back', wp_json_encode( $toggle_actions_on_enter_back ) );
				}
				if( ! empty( $settings['eael_smooth_animation_toggle_actions_on_leave_back'] ) ) {
					$toggle_actions_on_leave_back = [
						'toggle_actions_on_leave_back' => $settings['eael_smooth_animation_toggle_actions_on_leave_back']
					];
					$element->add_render_attribute( '_wrapper', 'data-toggle_actions_on_leave_back', wp_json_encode( $toggle_actions_on_leave_back ) );
				}
				
				//Pin settings
				if( ! empty( $settings['eael_smooth_animation_event_pin_setting_default'] ) ) {
					$pin = [
						'pin' => $settings['eael_smooth_animation_event_pin_setting_default']
					];
					$element->add_render_attribute( '_wrapper', 'data-pin', wp_json_encode( $pin ) );
				}
				$element->add_render_attribute( '_wrapper', 'data-scrollon', 'on' );
			}
        $element->add_render_attribute( '_wrapper', 'class', 'eael_smooth_animation' );
        }
    }
}