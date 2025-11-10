<?php

namespace Essential_Addons_Elementor\Pro\Extensions;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Repeater;
use \Elementor\Plugin;
use \Essential_Addons_Elementor\Classes\Helper as ControlsHelper;
use Essential_Addons_Elementor\Pro\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Conditional_Display {

	/**
	 * Initialize hooks
	 */
	public function __construct() {
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'register_controls' ] );
		add_filter( 'elementor/frontend/widget/should_render', [ $this, 'content_render' ], 10, 2 );
		add_filter( 'elementor/frontend/column/should_render', [ $this, 'content_render' ], 10, 2 );
		add_filter( 'elementor/frontend/section/should_render', [ $this, 'content_render' ], 10, 2 );
		add_filter( 'elementor/frontend/container/should_render', [ $this, 'content_render' ], 10, 2 );
	}

	public function register_controls( $element ) {
		$element->start_controls_section(
			'eael_conditional_logic_section',
			[
				'label' => __( '<i class="eaicon-logo"></i> Conditional Display', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_ADVANCED
			]
		);

		$element->add_control(
			'eael_cl_notice',
			[
				'raw'             => esc_html__( 'Conditional Display will take effect only on preview or live page, and not while editing in Elementor.', 'essential-addons-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition'       => [
					'eael_cl_enable' => 'yes'
				]
			]
		);

		$element->add_control(
			'eael_cl_enable',
			[
				'label'          => __( 'Enable Conditional Display', 'essential-addons-elementor' ),
				'type'           => Controls_Manager::SWITCHER,
				'default'        => '',
				'label_on'       => __( 'Yes', 'essential-addons-elementor' ),
				'label_off'      => __( 'No', 'essential-addons-elementor' ),
				'return_value'   => 'yes',
				'style_transfer' => false
			]
		);

		$element->add_control(
			'eael_cl_visibility_action',
			[
				'label'          => __( 'Visibility Action', 'essential-addons-elementor' ),
				'type'           => Controls_Manager::CHOOSE,
				'options'        => [
					'show'            => [
						'title' => esc_html__( 'Show', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-eye-solid',
					],
					'hide'            => [
						'title' => esc_html__( 'Hide', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-eye-slash-solid',
					],
					'forcefully_hide' => [
						'title' => esc_html__( 'Hide Without Condition', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-ban-solid',
					],
				],
				'default'        => 'show',
				'toggle'         => false,
				'condition'      => [
					'eael_cl_enable' => 'yes',
				],
				'style_transfer' => false
			]
		);

		$element->add_control(
			'eael_cl_action_apply_if',
			[
				'label'          => __( 'Action Applicable if', 'essential-addons-elementor' ),
				'type'           => Controls_Manager::CHOOSE,
				'options'        => [
					'all' => [
						'title' => esc_html__( 'True All Logic', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-dice-six-solid',
					],
					'any' => [
						'title' => esc_html__( 'True Any Logic', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-dice-one-solid',
					],
				],
				'default'        => 'all',
				'toggle'         => false,
				'condition'      => [
					'eael_cl_enable'             => 'yes',
					'eael_cl_visibility_action!' => 'forcefully_hide',
				],
				'style_transfer' => false
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'logic_type',
			[
				'label'   => __( 'Type', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'login_status',
				'options' => [
					'login_status'  => __( 'User Status', 'essential-addons-elementor' ),
					'post_type'     => __( 'Post Type', 'essential-addons-elementor' ),
					'browser'       => __( 'Browser', 'essential-addons-elementor' ),
					'date_time'     => __( 'Date & Time', 'essential-addons-elementor' ),
					'recurring_day' => __( 'Recurring Day', 'essential-addons-elementor' ),
					'dynamic'       => __( 'Dynamic Field', 'essential-addons-elementor' ),
					'query_string'  => __( 'Query String', 'essential-addons-elementor' ),
					'visit_count'   => __( 'Visit Count', 'essential-addons-elementor' ),
					'url_contains'  => __( 'URL Contains', 'essential-addons-elementor' ),
					'archive'       => __( 'Archive', 'essential-addons-elementor' ),
					'woo_products'  => __( 'Woo Products', 'essential-addons-elementor' ),
					'woo_cart'      => __( 'Woo Cart', 'essential-addons-elementor' ),
					'woo_orders'    => __( 'Woo Orders', 'essential-addons-elementor' ),
				],
			]
		);

		$repeater->add_control(
			'logic_operator_between',
			[
				'label'      => __( 'Logic Operator', 'essential-addons-elementor' ),
				'show_label' => false,
				'type'       => Controls_Manager::CHOOSE,
				'options'    => [
					'between'     => [
						'title' => esc_html__( 'Include', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-check-solid',
					],
					'not_between' => [
						'title' => esc_html__( 'Exclude', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-xmark-solid',
					],
				],
				'default'    => 'between',
				'toggle'     => false,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'browser',
						],
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'post_type',
						],
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'query_string',
						],
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'archive',
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'login_status',
								],
								[
									'name'     => 'login_status_operand',
									'operator' => '===',
									'value'    => 'logged_in',
								],
								[
									'name'     => 'user_and_role',
									'operator' => '!==',
									'value'    => '',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_products',
								],
								[
									'name'     => 'woo_cart_item_type',
									'operator' => '!in',
									'value'    => [ 'amount', 'count' ],
								],
							],
						]
					],
				]
			]
		);

		//Logic Type: User Status
		$this->user_status_controls( $repeater );

		//Logic Type: Post Type
		$this->post_types_controls( $repeater );

		//Logic Type: Browser
		$this->browser_logics_controls( $repeater );

		//Logic Type: Dynamic Field
		$this->dynamic_logic_controls( $repeater );

		//Logic Type: Date & Time and Recurring Day
		$this->date_time_controls( $repeater );

		//Logic Type: Query String
		$this->query_string_controls( $repeater );

		//Logic Type: URL Contains
		$this->url_contains_controls( $repeater );

		//Logic Type: Archive
		$this->archive_controls( $repeater );

		//Logic Type: Woo Cart & Woo Products
		$this->woocommerce_controls( $repeater );

		$repeater->add_control(
			'user_visit_count_type',
			[
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'less' => [
						'title' => esc_html__( 'Less Than', 'essential-addons-elementor' ),
						'icon' => 'eicon-chevron-left',
					],
					'greater' => [
						'title' => esc_html__( 'Greater Than', 'essential-addons-elementor' ),
						'icon' => 'eicon-chevron-right',
					],
				],
				'default' => 'less',
				'condition'   => [
					'logic_type' => 'visit_count',
				]
			]
		);

		$repeater->add_control(
			'user_visit_count',
			[
				'label'       => esc_html__( 'Visit Count', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 3,
				'condition'   => [
					'logic_type' => 'visit_count',
				]
			]
		);

		$element->add_control(
			'eael_cl_logics',
			[
				'label'          => __( 'Logics', 'essential-addons-elementor' ),
				'type'           => Controls_Manager::REPEATER,
				'fields'         => $repeater->get_controls(),
				'default'        => [
					[
						'logic_type'           => 'login_status',
						'login_status_operand' => 'logged_in',
					],
				],
				'style_transfer' => false,
				'title_field'    => '{{{ ea_conditional_logic_type_title(logic_type) }}}',
				'condition'      => [
					'eael_cl_enable'             => 'yes',
					'eael_cl_visibility_action!' => 'forcefully_hide',
				]
			]
		);

		$element->add_control(
			'eael_cl_fallback',
			[
				'label'     => esc_html__( 'Fallback', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'content'  => [
						'title' => esc_html__( 'Content', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-area',
					],
					'template' => [
						'title' => esc_html__( 'Template', 'essential-addons-elementor' ),
						'icon'  => 'eicon-folder-o',
					],
					'none'     => [
						'title' => esc_html__( 'No Fallback', 'essential-addons-elementor' ),
						'icon'  => 'eicon-ban',
					],
				],
				'default'   => 'none',
				'separator' => 'before',
				'condition' => [
					'eael_cl_enable' => 'yes'
				]
			]
		);

		$element->add_control(
			'eael_cl_fallback_content',
			[
				'label'       => esc_html__( 'Content', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => esc_html__( 'The content is hidden', 'essential-addons-elementor' ),
				'placeholder' => esc_html__( 'Type your content here', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_cl_fallback' => 'content',
					'eael_cl_enable'   => 'yes'
				]
			]
		);

		$element->add_control(
			'eael_cl_fallback_template',
			[
				'label'       => __( 'Choose Template', 'essential-addons-elementor' ),
				'type'        => 'eael-select2',
				'source_name' => 'post_type',
				'source_type' => 'elementor_library',
				'label_block' => true,
				'condition'   => [
					'eael_cl_fallback' => 'template',
					'eael_cl_enable'   => 'yes'
				],
			]
		);

		$element->end_controls_section();
	}

	/**
	 * Get All editable roles and return array with simple slug|name pare
	 *
	 * @param $first_index
	 * @param $output
	 *
	 * @return array|string
	 */
	public function get_editable_roles() {
		$wp_roles       = [ '' => __( 'Select', 'essential-addons-elementor' ) ];
		$all_roles      = wp_roles()->roles;
		$editable_roles = apply_filters( 'editable_roles', $all_roles );

		foreach ( $editable_roles as $slug => $editable_role ) {
			$wp_roles[ $slug ] = $editable_role['name'];
		}

		return $wp_roles;
	}

	/**
	 * Get all browser list and return array with simple slug|name pare
	 *
	 * @return array
	 */
	public function get_browser_list() {
		return [
			'chrome'    => __( 'Google Chrome', 'essential-addons-elementor' ),
			'firefox'   => __( 'Mozilla Firefox', 'essential-addons-elementor' ),
			'safari'    => __( 'Safari', 'essential-addons-elementor' ),
			'i_safari'  => __( 'Iphone Safari', 'essential-addons-elementor' ),
			'opera'     => __( 'Opera', 'essential-addons-elementor' ),
			'edge'      => __( 'Edge', 'essential-addons-elementor' ),
			'ie'        => __( 'Internet Explorer', 'essential-addons-elementor' ),
			'mac_ie'    => __( 'Internet Explorer for Mac OS X', 'essential-addons-elementor' ),
			'netscape4' => __( 'Netscape 4', 'essential-addons-elementor' ),
			'lynx'      => __( 'Lynx', 'essential-addons-elementor' ),
			'others'    => __( 'Others', 'essential-addons-elementor' ),
		];
	}

	/**
	 * Get all days list of a week and return array with simple slug|name pare
	 *
	 * @return array
	 */
	public function get_days_list() {
		return [
			'sun' => __( 'Sunday', 'essential-addons-elementor' ),
			'mon' => __( 'Monday', 'essential-addons-elementor' ),
			'tue' => __( 'Tuesday', 'essential-addons-elementor' ),
			'wed' => __( 'Wednesday', 'essential-addons-elementor' ),
			'thu' => __( 'Thursday', 'essential-addons-elementor' ),
			'fri' => __( 'Friday', 'essential-addons-elementor' ),
			'sat' => __( 'Saturday', 'essential-addons-elementor' )
		];
	}

	/**
	 * Get current browser
	 *
	 * @return string
	 */
	public function get_current_browser() {
		global $is_lynx, $is_gecko, $is_winIE, $is_macIE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone, $is_edge;

		$browser = 'others';

		switch ( true ) {
			case $is_chrome:
				$browser = 'chrome';
				break;
			case $is_gecko:
				$browser = 'firefox';
				break;
			case $is_safari:
				$browser = 'safari';
				break;
			case $is_iphone:
				$browser = 'i_safari';
				break;
			case $is_opera:
				$browser = 'opera';
				break;
			case $is_edge:
				$browser = 'edge';
				break;
			case $is_winIE:
				$browser = 'ie';
				break;
			case $is_macIE:
				$browser = 'mac_ie';
				break;
			case $is_NS4:
				$browser = 'netscape4';
				break;
			case $is_lynx:
				$browser = 'lynx';
				break;

		}

		return $browser;
	}

	public function parse_arg( $arg ) {
		$arg = wp_parse_args( $arg, [
			'eael_cl_enable'            => '',
			'eael_cl_visibility_action' => '',
			'eael_cl_logics'            => [],
			'eael_cl_action_apply_if'   => '',
		] );

		return $arg;
	}

	/**
	 * Check all logics and return the final result
	 *
	 * @param $settings
	 *
	 * @return bool
	 */
	public function check_logics( $settings ) {
		$return                = false;
		$needed_any_logic_true = $settings['eael_cl_action_apply_if'] === 'any';
		$needed_all_logic_true = $settings['eael_cl_action_apply_if'] === 'all';

		foreach ( $settings['eael_cl_logics'] as $cl_logic ) {
			switch ( $cl_logic['logic_type'] ) {
				case 'login_status':
					$return = $cl_logic['login_status_operand'] === 'logged_in' ? is_user_logged_in() : ! is_user_logged_in();

					if ( is_user_logged_in() && $cl_logic['user_and_role'] !== '' ) {
						if ( $cl_logic['user_and_role'] === 'user_role' ) {
							$user_roles = get_userdata( get_current_user_id() )->roles;
							$operand    = $cl_logic['user_role_operand_multi'];
							$result     = array_intersect( $user_roles, $operand );
							$return     = ( $cl_logic['logic_operator_between'] === 'between' ) ? count( $result ) > 0 : count( $result ) == 0;
						} elseif ( $cl_logic['user_and_role'] === 'user' ) {
							$user    = get_current_user_id();
							$operand = array_map( 'intval', (array) $cl_logic['user_operand'] );
							$return  = $cl_logic['logic_operator_between'] === 'between' ? in_array( $user, $operand ) : ! in_array( $user, $operand );
						}
					}

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;
				case 'post_type':
					$ID                = get_the_ID();
					$post_type_operand = $cl_logic['post_type_operand'];
					$operand           = empty( $post_type_operand ) ? (array) $cl_logic['post_operand'] : (array) $cl_logic["post_operand_{$post_type_operand}"];

					if ( count( $operand ) ) {
						$return = $cl_logic['logic_operator_between'] === 'between' ? in_array( $ID, $operand ) : ! in_array( $ID, $operand );
					} else {
						$post_type = get_post_type( $ID );
						$return    = $cl_logic['logic_operator_between'] === 'between' ? $post_type === $post_type_operand : $post_type !== $post_type_operand;
					}

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;
				case 'browser':
					$browser = $this->get_current_browser();
					$operand = (array) $cl_logic['browser_operand'];
					$return  = $cl_logic['logic_operator_between'] === 'between' ? in_array( $browser, $operand ) : ! in_array( $browser, $operand );

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;
				case 'date_time':
					$current_time = current_time( 'U' );
					$from         = ( $cl_logic['date_time_logic'] === 'equal' || $cl_logic['date_time_logic'] === 'not_equal' ) ? strtotime( "{$cl_logic['single_date']} 00:00:00" ) : strtotime( $cl_logic['from_date'] );
					$to           = ( $cl_logic['date_time_logic'] === 'equal' || $cl_logic['date_time_logic'] === 'not_equal' ) ? strtotime( "{$cl_logic['single_date']} 23:59:59" ) : strtotime( $cl_logic['to_date'] );
					$return       = $cl_logic['date_time_logic'] === 'equal' || $cl_logic['date_time_logic'] === 'between' ? $from <= $current_time && $current_time <= $to : $from >= $current_time || $current_time >= $to;

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;
				case 'recurring_day':
					$current_time = current_time( 'U' );
					$is_today     = isset( $cl_logic['recurring_days_all'] ) && $cl_logic['recurring_days_all'] === 'yes' || in_array(strtolower(gmdate('D')), $cl_logic['recurring_days']);
					$return 	  = $is_today;

					if( isset( $cl_logic['recurring_days_duration_from'] ) && ! empty( $cl_logic['recurring_days_duration_from'] ) ){
						$from_date = strtotime( "{$cl_logic['recurring_days_duration_from']} 00:00:00" );
						$return    = $return && $from_date < $current_time;
					}
					
					if( isset( $cl_logic['recurring_days_duration_to'] ) && ! empty( $cl_logic['recurring_days_duration_to'] ) ){
						$to_date = strtotime( "{$cl_logic['recurring_days_duration_to']} 23:59:59" );
						$return  = $return && $to_date > $current_time;
					}
					
					if( isset( $cl_logic['from_time'] ) && ! empty( $cl_logic['from_time'] ) ){
						$from_time = strtotime( $cl_logic['from_time'] );
						$return    = $return && $from_time < $current_time;
					}

					if( isset( $cl_logic['to_time'] ) && ! empty( $cl_logic['to_time'] ) ){
						$to_time = strtotime( $cl_logic['to_time'] );
						$return  = $return && $to_time > $current_time;
					}

					$return = $cl_logic['recurring_day_logic'] === 'between' ? $return : ! $return;

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;
				case 'dynamic':
					if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
						$dynamic_field = strip_tags( $cl_logic['dynamic_field'] );
						$dynamic_field = strtolower( $dynamic_field );

						$separator = '|';
						if( 'yes' === $cl_logic['enable_dynamic_field_custom_separator'] && ! empty( $cl_logic['dynamic_field_custom_separator'] )){
							$_separator = $cl_logic['dynamic_field_custom_separator'];
							$_separator = preg_replace('/\s+/', '', $_separator);
							$separator  = ! empty( $_separator ) ? $_separator : $separator;
						}

						$dynamic_field = explode( $separator, $dynamic_field );
						$dynamic_field = array_map( 'trim', $dynamic_field );
						$value         = explode( '|', strtolower( $cl_logic['dynamic_operand'] ) );
						$value         = array_map( 'trim', $value );
						$result        = array_intersect( $dynamic_field, $value );
						$return        = ( $cl_logic['logic_operator_dynamic'] === 'between' ) ? count( $result ) > 0 : count( $result ) == 0;

						if ( $needed_any_logic_true && $return ) {
							break( 2 );
						}

						if ( $needed_all_logic_true && ! $return ) {
							break( 2 );
						}
					}

					break;
				case 'query_string':

					$return = $cl_logic['query_key'] && isset( $_GET[ $cl_logic['query_key'] ] ) && sanitize_text_field( $_GET[ $cl_logic['query_key'] ] ) === $cl_logic['query_value'];
					$return = $cl_logic['logic_operator_between'] === 'between' ? $return : ! $return;

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;

				case 'visit_count':
					$cookie_key  = 'eael_' . md5( 'visit_count_' . $cl_logic['_id'] );
					$visit_count = isset( $_COOKIE[ $cookie_key ] ) ? $_COOKIE[ $cookie_key ]: 0;
					$return      = $cl_logic['user_visit_count'] && absint( $cl_logic['user_visit_count'] ) > $visit_count;
					$return      = $cl_logic['user_visit_count_type'] === 'less' ? $return: ! $return;
					$visit_count++;
					?>
					<script>
						const d = new Date();
						d.setTime(d.getTime() + (1*24*60*60*1000));
						let expires = "expires="+ d.toUTCString();
						document.cookie = "<?php echo esc_html( $cookie_key ); ?>=" + <?php echo esc_html( $visit_count ); ?> + ";" + expires + ";path=/";
					</script>
					<?php
					
					break;

				case 'url_contains':

					$url = 'current' === $cl_logic['url_contains_url_type'] ? $_SERVER['REQUEST_URI'] : $_SERVER['HTTP_REFERER'];
					$string = trim( $cl_logic['url_contains_string'] );
					if( $string ){
						$return = str_contains( $url, $string );
						$return = 'in' === $cl_logic['url_contains_logic_operator'] ? $return : ! $return;
					}

					if ( $needed_any_logic_true && $return ) {
						break( 2 );
					}

					if ( $needed_all_logic_true && ! $return ) {
						break( 2 );
					}

					break;

				case 'archive':

					if( 'search' === $cl_logic['archive_type'] ){
						$return = is_search();

					} else if( 'post' === $cl_logic['archive_type'] ) {

						if( 'post' === $cl_logic['archive_post_type'] ) {
							$return = is_home();
						} else if ( function_exists( 'WC' ) && 'product' === $cl_logic['archive_post_type'] ){
							$return = is_shop(); 
						} else if ( '' !== $cl_logic['archive_post_type'] ){
							$return = is_post_type_archive( $cl_logic['archive_post_type'] );
						}
						
					} else if( 'taxonomy' === $cl_logic['archive_type'] ){

						if( 'category' === $cl_logic['archive_taxonomies'] ){
							$return = is_category();

						} else if ( 'post_tag' === $cl_logic['archive_taxonomies'] ) {
							$return = is_tag();

						} else if ( 'post_format' === $cl_logic['archive_taxonomies'] ) {
							$return = has_post_format();

						} else if ( '' !== $cl_logic['archive_taxonomies'] ){
							$return = is_tax( $cl_logic['archive_taxonomies'] );

						}
					} else if( 'terms' === $cl_logic['archive_type']  ){
						if( 'category' === $cl_logic['archive_taxonomies'] && isset( $cl_logic['archive_terms_for_category'] ) && ! empty( $cl_logic['archive_terms_for_category'] ) ){
							$return = is_category( $cl_logic['archive_terms_for_category'] );

						} else if ( 'post_tag' === $cl_logic['archive_taxonomies'] && isset( $cl_logic['archive_terms_for_post_tag'] ) && ! empty( $cl_logic['archive_terms_for_post_tag'] ) ) {
							$return = is_tag( $cl_logic['archive_terms_for_post_tag'] );

						} else if ( 'post_format' === $cl_logic['archive_taxonomies'] && isset( $cl_logic['archive_terms_for_post_format'] ) && ! empty( $cl_logic['archive_terms_for_post_format'] ) ) {
							foreach( $cl_logic['archive_terms_for_post_format'] as $post_format ){
								if( has_post_format( $post_format ) ){
									$return = has_post_format( $post_format );
								}
							}

						} else if ( '' !== $cl_logic['archive_taxonomies'] && isset( $cl_logic['archive_terms_for_' . $cl_logic['archive_taxonomies'] ] ) && ! empty( isset( $cl_logic['archive_terms_for_' . $cl_logic['archive_taxonomies'] ] ) ) ){
							$return = is_tax( $cl_logic['archive_taxonomies'], $cl_logic['archive_terms_for_' . $cl_logic['archive_taxonomies'] ] );
						}
					} else if( 'author' === $cl_logic['archive_type'] && is_author() ){
						if( "user" === $cl_logic['archive_author_type'] ){
							if( empty( $cl_logic['archive_users'] ) ){
								$return = is_author();

							} else if( ! empty( $cl_logic['archive_users'] ) ){
								$author_id = get_queried_object_id();
								$return = in_array( $author_id, $cl_logic['archive_users'] );
							}
							
						} else if( "user_role" === $cl_logic['archive_author_type'] ){
							if( empty( $cl_logic['archive_user_role'] ) ){
								$return = is_author();

							} else if( ! empty( $cl_logic['archive_user_role'] ) ){
								$author_id = get_queried_object_id();
								$author    = get_user_by( 'ID', $author_id );
								$has_role  = array_intersect( $cl_logic['archive_user_role'], $author->roles );
								$return    = count( $has_role ) > 0;
							}
						}
					} else if ( 'date' === $cl_logic['archive_type'] ){
						if( "" === $cl_logic['archive_date_from'] || "" === $cl_logic['archive_date_to'] ){
							$return = is_date();

						} else if ( is_year() ) {
							$start_year   = date( 'Y', strtotime( $cl_logic['archive_date_from'] ) );
							$end_year     = date( 'Y', strtotime( $cl_logic['archive_date_to'] ) );
							$archive_year = get_query_var( 'year' );
							$return       = $start_year <= $archive_year && $archive_year <= $end_year;

						} elseif ( is_month() ) {
							$start         = strtotime( $cl_logic['archive_date_from'] );
							$end           = strtotime( $cl_logic['archive_date_to'] );
							$archive_year  = get_query_var( 'year' );
							$archive_month = get_query_var( 'monthnum' );
							$time_str      = strtotime("$archive_year-$archive_month-01");
							$month_first   = strtotime( date('Y-m-01', $time_str ) );
							$month_last    = strtotime( date('Y-m-t', $time_str ) );
							$return        = $start <= $month_first && $month_last <= $end;

						} elseif ( is_day() ) {
							$start         = strtotime( $cl_logic['archive_date_from'] );
							$end           = strtotime( $cl_logic['archive_date_to'] );
							$archive_year  = get_query_var( 'year' );
							$archive_month = get_query_var( 'monthnum' );
							$archive_day   = get_query_var( 'day' );
							$archive_date  = strtotime("$archive_year-$archive_month-$archive_day");
							$return        = $start <= $archive_date && $archive_date <= $end;
							
						}
					}

					if ( $cl_logic['logic_operator_between'] === 'not_between' ) {
						$return = ! $return;
					}

					if ( ( $needed_any_logic_true && $return ) || ( $needed_all_logic_true && ! $return ) ) {
						break( 2 );
					}
					
					
					break;

				case 'woo_cart':

					if ( ! function_exists( 'WC' ) ) {
						break;
					}

					$is_cart_empty = WC()->cart->is_empty();

					if ( $cl_logic['woo_cart_logic_operator'] === 'empty' ) {
						$return = $is_cart_empty;
					} else if ( ! $is_cart_empty ) {
						$cart_items = WC()->cart->get_cart_contents();
						$compare_to = $compare_with = [];

						if ( $cl_logic['woo_cart_item_type'] === 'product' && ! empty( $cl_logic['product_ids'] ) ) {
							$cart_item_ids = [];
							foreach ( $cart_items as $item ) {
								$cart_item_ids[] = $item['product_id'];
							}

							$compare_to   = array_unique( $cart_item_ids );
							$compare_with = $cl_logic['product_ids'];
						} else if ( $cl_logic['woo_cart_item_type'] === 'ptype' && ! empty( $cl_logic['product_types'] ) ) {
							$cart_item_types = [];
							foreach ( $cart_items as $item ) {
								$cart_item_types[] = $item['data']->get_type();
							}

							$compare_to   = array_unique( $cart_item_types );
							$compare_with = $cl_logic['product_types'];

						} else if ( $cl_logic['woo_cart_item_type'] === 'category' && ! empty( $cl_logic['product_categories'] ) ) {
							$product_category_ids = [];
							foreach ( $cart_items as $item ) {
								$product_category_ids = array_merge( $product_category_ids, $item['data']->get_category_ids() );
							}

							$compare_to   = array_unique( $product_category_ids );
							$compare_with = $cl_logic['product_categories'];

						} else if ( $cl_logic['woo_cart_item_type'] === 'count' && $cl_logic['cart_item_count'] > 0 ){
							$item_count = WC()->cart->get_cart_contents_count();

							if ( $cl_logic['woo_cart_logic_operator_2'] === 'less' ) {
								$return = $item_count < absint( $cl_logic['cart_item_count'] );
							} else if ( $cl_logic['woo_cart_logic_operator_2'] === 'greater' ) {
								$return = $item_count > absint( $cl_logic['cart_item_count'] );
							}

						} else if ( $cl_logic['woo_cart_item_type'] === 'amount' && $cl_logic['cart_item_amount'] > 0 ){
							$item_total = WC()->cart->subtotal;

							if ( $cl_logic['woo_cart_logic_operator_2'] === 'less' ) {
								$return = $item_total < floatval( $cl_logic['cart_item_amount'] );
							} else if ( $cl_logic['woo_cart_logic_operator_2'] === 'greater' ) {
								$return = $item_total > floatval( $cl_logic['cart_item_amount'] );
							}
						}

						if ( ! empty( $compare_to ) && ! empty( $compare_with ) ) {
							$has_items = array_intersect( $compare_with, $compare_to );

							if ( $cl_logic['woo_cart_logic_operator'] === 'in' ) {
								$return = count( $has_items ) > 0;
							} else if ( $cl_logic['woo_cart_logic_operator'] === 'not_in' ) {
								$return = count( $has_items ) < 1;
							}
						}
					}

					if ( ( $needed_any_logic_true && $return ) || ( $needed_all_logic_true && ! $return ) ) {
						break( 2 );
					}

				case 'woo_products':

					if ( ! function_exists( 'WC' ) ) {
						break;
					}

					$product_id = get_the_ID();
					$has_items  = false;

					if ( get_post_type( $product_id ) !== 'product' ) {
						break;
					}

					if (  'product' === $cl_logic['woo_cart_item_type'] && ! empty( $cl_logic['product_ids'] ) ) {
						$has_items = true;
						$return    = in_array( $product_id, $cl_logic['product_ids'] );
					} else if ( 'ptype' === $cl_logic['woo_cart_item_type'] && ! empty( $cl_logic['product_types'] ) ) {
						$has_items = true;
						$product   = wc_get_product( $product_id );
						$return    = in_array( $product->get_type(), $cl_logic['product_types'] );
					} else if ( 'category' === $cl_logic['woo_cart_item_type'] && ! empty( $cl_logic['product_categories'] ) ) {
						$has_items      = true;
						$product        = wc_get_product( $product_id );
						$has_categories = array_intersect( $cl_logic['product_categories'], $product->get_category_ids() );
						$return         = count( $has_categories ) > 0;

					} else if ( $cl_logic['woo_cart_item_type'] === 'count' ){
						$product         = wc_get_product( $product_id );
						$is_manage_stock = $product->get_manage_stock();

						if( $is_manage_stock ){
							if ( $cl_logic['woo_cart_logic_operator_2'] === 'less' ) {
								$return = $product->get_stock_quantity() < absint( $cl_logic['product_stock_count'] );

							} else if ( $cl_logic['woo_cart_logic_operator_2'] === 'greater' ) {
								$return = $product->get_stock_quantity() > absint( $cl_logic['product_stock_count'] );

							}

						} else if ( ! $is_manage_stock && $cl_logic['product_stock_count'] > 0 ){
							$status = $product->get_stock_status();
							$return = 'instock' === $status;
						}
						

					} else if ( $cl_logic['woo_cart_item_type'] === 'amount' ){
						$product = wc_get_product( $product_id );
						$price 	 = $product->get_regular_price();
						if ( $cl_logic['woo_cart_logic_operator_2'] === 'less' ) {
							$return = $price < $cl_logic['product_price'];

						} else if ( $cl_logic['woo_cart_logic_operator_2'] === 'greater' ) {
							$return = $price > $cl_logic['product_price'];

						}
					}

					if ( $has_items && $cl_logic['logic_operator_between'] === 'not_between' ) {
						$return = ! $return;
					}

					if ( ( $needed_any_logic_true && $return ) || ( $needed_all_logic_true && ! $return ) ) {
						break( 2 );
					}

					break;

				case 'woo_orders':

					if ( ! function_exists( 'WC' ) || ! is_user_logged_in() ) {
						break;
					}

					$user_id = get_current_user_id();

					if( 'no_order' === $cl_logic['woo_purchase_type'] ){
						$order_count = wc_get_customer_order_count( $user_id );
						$return      = 'no_order' === $cl_logic['woo_purchase_type'] && $order_count < 1;

					} else {
						if ( 'product' === $cl_logic['woo_cart_item_type'] && ! empty( $cl_logic['product_ids'] ) ){

							$product_ids = $this->get_ordered_products( 'ids', $cl_logic );
							$has_items 	 = array_intersect( $cl_logic['product_ids'], $product_ids );
							$return      = count( $has_items ) > 0;

						} else if ( 'ptype' === $cl_logic['woo_cart_item_type'] && ! empty( $cl_logic['product_types'] ) ){

							$product_types = $this->get_ordered_products( 'types', $cl_logic );
							$has_items 	   = array_intersect( $cl_logic['product_types'], $product_types );
							$return        = count( $has_items ) > 0;

						} else if ( 'category' === $cl_logic['woo_cart_item_type'] && ! empty( $cl_logic['product_categories'] ) ){

							$product_cats = $this->get_ordered_products( 'categories', $cl_logic );
							$has_items 	  = array_intersect( $cl_logic['product_categories'], $product_cats );
							$return       = count( $has_items ) > 0;
						}

						if ( $has_items && $cl_logic['woo_products_logic_oparator'] === 'exclude' ) {
							$return = ! $return;
						}
	
						if ( ( $needed_any_logic_true && $return ) || ( $needed_all_logic_true && ! $return ) ) {
							break( 2 );
						}
					}
			}
		}

		return $return;
	}

	public function content_render( $should_render, Element_Base $element ) {
		$settings = $element->get_settings_for_display();
		$settings = $this->parse_arg( $settings );

		if ( $settings['eael_cl_enable'] === 'yes' ) {
			switch ( $settings['eael_cl_visibility_action'] ) {
				case 'show':
					$should_render = $this->check_logics( $settings ) ? true : false;
					break;
				case 'hide':
					$should_render = $this->check_logics( $settings ) ? false : true;
					break;
				case 'forcefully_hide':
					$should_render = false;
			}

			if ( ! $should_render && $settings['eael_cl_fallback'] !== 'none' ) {
				if ( $settings['eael_cl_fallback'] === 'content' ) {
					echo wp_kses( $settings['eael_cl_fallback_content'], Helper::eael_allowed_tags() );
				} else if ( $settings['eael_cl_fallback'] === 'template' ) {
					echo Plugin::$instance->frontend->get_builder_content( $settings['eael_cl_fallback_template'] );
				}
			}
		}

		return $should_render;
	}

	private function dynamic_logic_controls( $repeater ) {

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {

			$repeater->add_control(
				'dynamic_field',
				[
					'label'       => esc_html__( 'Dynamic Field', 'essential-addons-elementor' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'description' => esc_html__( 'Please remove Before and After field texts from Advanced tab.', 'essential-addons-elementor' ),
					'default'     => '',
					'separator'   => 'after',
					'dynamic'     => [
						'active' => true,
					],
					'condition'   => [
						'logic_type' => 'dynamic'
					],
					'ai' => [
						'active' => false,
					],
				]
			);

			$repeater->add_control(
				'enable_dynamic_field_custom_separator',
				[
					'label'        => esc_html__( 'Use Custom Separator', 'essential-addons-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
					'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
					'description'  => esc_html__( 'If the Dynamic Field has multiple values and the output is not separated by ( | ) Pipeline, enable this to input the value separator.', 'essential-addons-elementor' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => [
						'logic_type' => 'dynamic'
					],
				]
			);

			$repeater->add_control(
				'dynamic_field_custom_separator',
				[
					'label'     => esc_html__( 'Separator', 'essential-addons-elementor' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => esc_html__( '|', 'essential-addons-elementor' ),
					'ai' 		=> [ 'active' => false ],
					'separator'   => 'after',
					'condition' => [
						'logic_type' => 'dynamic',
						'enable_dynamic_field_custom_separator' => 'yes'
					],
				]
			);

			$repeater->add_control(
				'logic_operator_dynamic',
				[
					'label'      => __( 'Logic Operator', 'essential-addons-elementor' ),
					'show_label' => false,
					'type'       => Controls_Manager::CHOOSE,
					'options'    => [
						'between'     => [
							'title' => esc_html__( 'Include', 'essential-addons-elementor' ),
							'icon'  => 'eaicon-check-solid',
						],
						'not_between' => [
							'title' => esc_html__( 'Exclude', 'essential-addons-elementor' ),
							'icon'  => 'eaicon-xmark-solid',
						],
					],
					'default'    => 'between',
					'toggle'     => false,
					'condition'  => [
						'logic_type' => 'dynamic',
					]
				]
			);

			$repeater->add_control(
				'dynamic_operand',
				[
					'label'       => esc_html__( 'Value', 'essential-addons-elementor' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'default'     => '',
					'separator'   => 'before',
					'condition'   => [
						'logic_type' => 'dynamic'
					],
					'ai'          => [
						'active' => false,
					],
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$repeater->add_control(
				'eael_cl_dynamic_notice',
				[
					'raw'             => __( 'Separate multiple value with the | (pipe) character. (e.g. <strong>value 1 | value 2</strong>)', 'essential-addons-elementor' ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-descriptor',
					'condition'       => [
						'logic_type' => 'dynamic',
					]
				]
			);
		}
	}

	private function user_status_controls( $repeater ) {
		$repeater->add_control(
			'login_status_operand',
			[
				'label'     => __( 'Login Status', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'logged_in'     => [
						'title' => esc_html__( 'Logged In', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-user-solid',
					],
					'not_logged_in' => [
						'title' => esc_html__( 'Not Logged In', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-user-slash-solid',
					],
				],
				'default'   => 'logged_in',
				'toggle'    => false,
				'condition' => [
					'logic_type' => 'login_status',
				]
			]
		);

		$repeater->add_control(
			'user_and_role',
			[
				'label'     => __( 'Select User Type', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'user_role' => [
						'title' => esc_html__( 'User Role', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-users-solid',
					],
					'user'      => [
						'title' => esc_html__( 'User', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-user-plus-solid',
					],
				],
				'default'   => '',
				'condition' => [
					'logic_type'           => 'login_status',
					'login_status_operand' => 'logged_in',
				]
			]
		);

		$roles = $this->get_editable_roles();

		$repeater->add_control(
			'user_role_operand_multi',
			[
				'label'       => __( 'Select User Roles', 'essential-addons-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $roles,
				'default'     => [],
				'condition'   => [
					'logic_type'           => 'login_status',
					'login_status_operand' => 'logged_in',
					'user_and_role'        => 'user_role'
				]
			]
		);

		$repeater->add_control(
			'user_operand',
			[
				'label'       => esc_html__( 'Select Users', 'essential-addons-elementor' ),
				'type'        => 'eael-select2',
				'source_name' => 'user',
				'source_type' => 'all',
				'label_block' => true,
				'multiple'    => true,
				'condition'   => [
					'logic_type'           => 'login_status',
					'login_status_operand' => 'logged_in',
					'user_and_role'        => 'user'
				]
			]
		);
	}

	private function post_types_controls( $repeater ) {
		$_post_types = ControlsHelper::get_post_types();
		$post_types  = array_merge( [ '' => 'All' ], $_post_types );

		$repeater->add_control(
			'post_type_operand',
			[
				'label'       => esc_html__( 'Select Post Types', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => $post_types,
				'default'     => key( $post_types ),
				'condition'   => [
					'logic_type' => 'post_type',
				]
			]
		);

		$repeater->add_control(
			'post_operand',
			[
				'label'       => esc_html__( 'Select Any Post', 'essential-addons-elementor' ),
				'type'        => 'eael-select2',
				'source_name' => 'post_type',
				'source_type' => 'any',
				'label_block' => true,
				'multiple'    => true,
				'condition'   => [
					'logic_type'        => 'post_type',
					'post_type_operand' => ''
				]
			]
		);

		foreach ( $_post_types as $post_type_slug => $post_type_name ) {
			$repeater->add_control(
				"post_operand_{$post_type_slug}",
				[
					'label'       => esc_html__( 'Select ', 'essential-addons-elementor' ) . $post_type_name,
					'type'        => 'eael-select2',
					'source_name' => 'post_type',
					'source_type' => $post_type_slug,
					'label_block' => true,
					'multiple'    => true,
					'condition'   => [
						'logic_type'        => 'post_type',
						'post_type_operand' => $post_type_slug
					]
				]
			);
		}
	}

	private function browser_logics_controls( $repeater ) {
		$repeater->add_control(
			'browser_operand',
			[
				'label'       => __( 'Select Browser', 'essential-addons-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->get_browser_list(),
				'default'     => key( $this->get_browser_list() ),
				'condition'   => [
					'logic_type' => 'browser',
				]
			]
		);
	}

	private function date_time_controls( $repeater ) {
		$repeater->add_control(
			'date_time_logic',
			[
				'label'      => __( 'Date and time', 'essential-addons-elementor' ),
				'show_label' => false,
				'type'       => Controls_Manager::CHOOSE,
				'options'    => [
					'equal'       => [
						'title' => esc_html__( 'Is', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-equals-solid',
					],
					'not_equal'   => [
						'title' => esc_html__( 'Is Not', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-not-equal-solid',
					],
					'between'     => [
						'title' => esc_html__( 'Between', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-check-solid',
					],
					'not_between' => [
						'title' => esc_html__( 'Not Between', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-xmark-solid',
					],
				],
				'default'    => 'equal',
				'toggle'     => false,
				'condition'  => [
					'logic_type' => 'date_time',
				]
			]
		);

		$repeater->add_control(
			'single_date',
			[
				'label'          => esc_html__( 'Date', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'enableTime' => false,
					'altInput'   => true,
					'altFormat'  => 'M j, Y',
					'dateFormat' => 'Y-m-d'
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'date_time',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'between',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'not_between',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'from_date',
			[
				'label'          => esc_html__( 'From', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'altInput'   => true,
					'altFormat'  => 'M j, Y h:i K',
					'dateFormat' => 'Y-m-d H:i:S'
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'date_time',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'equal',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'not_equal',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'to_date',
			[
				'label'          => esc_html__( 'To', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'altInput'   => true,
					'altFormat'  => 'M j, Y h:i K',
					'dateFormat' => 'Y-m-d H:i:S'
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'date_time',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'equal',
						],
						[
							'name'     => 'date_time_logic',
							'operator' => '!==',
							'value'    => 'not_equal',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'recurring_day_logic',
			[
				'label'      => __( 'Recurring Day', 'essential-addons-elementor' ),
				'show_label' => false,
				'type'       => Controls_Manager::CHOOSE,
				'options'    => [
					'between'     => [
						'title' => esc_html__( 'Between', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-check-solid',
					],
					'not_between' => [
						'title' => esc_html__( 'Not Between', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-xmark-solid',
					],
				],
				'default'    => 'between',
				'toggle'     => false,
				'condition'  => [
					'logic_type' => 'recurring_day',
				]
			]
		);

		$repeater->add_control(
			'recurring_days_all',
			[
				'label'        => __( 'All Days', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'essential-addons-elementor' ),
				'label_off'    => __( 'No', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'logic_type' => 'recurring_day',
				]
			]
		);

		$repeater->add_control(
			'recurring_days',
			[
				'label'       => __( 'Recurring Days', 'essential-addons-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->get_days_list(),
				'default'     => [ key( $this->get_days_list() ) ],
				'condition'   => [
					'logic_type'          => 'recurring_day',
					'recurring_days_all!' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'recurring_days_heading',
			[
				'label'     => __( 'Date Duration', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'logic_type' => 'recurring_day',
				]
			]
		);

		$repeater->add_control(
			'recurring_days_duration_from',
			[
				'label'          => esc_html__( 'From', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'altInput'   => true,
					'altFormat'  => 'M j, Y',
					'dateFormat' => 'Y-m-d',
					'enableTime' => false,
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'recurring_day',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'recurring_days_duration_to',
			[
				'label'          => esc_html__( 'To', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'altInput'   => true,
					'altFormat'  => 'M j, Y',
					'dateFormat' => 'Y-m-d',
					'enableTime' => false,
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'recurring_day',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'recurring_days_heading2',
			[
				'label'     => __( 'Time Duration', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'logic_type' => 'recurring_day',
				]
			]
		);

		$repeater->add_control(
			'from_time',
			[
				'label'          => esc_html__( 'From', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'altInput'   => true,
					'altFormat'  => 'h:i K',
					'enableTime' => true,
					'noCalendar' => true,
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'recurring_day',
						],
					],
				]
			]
		);

		$repeater->add_control(
			'to_time',
			[
				'label'          => esc_html__( 'To', 'essential-addons-elementor' ),
				'label_block'    => false,
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'altInput'   => true,
					'altFormat'  => 'h:i K',
					'enableTime' => true,
					'noCalendar' => true,
				],
				'conditions'     => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'logic_type',
							'operator' => '===',
							'value'    => 'recurring_day',
						],
					],
				]
			]
		);
	}

	private function query_string_controls( $repeater ) {
		$repeater->add_control(
			'query_key',
			[
				'label'       => esc_html__( 'Key', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Query Key', 'essential-addons-elementor' ),
				'condition'   => [
					'logic_type' => 'query_string',
				]
			]
		);

		$repeater->add_control(
			'query_value',
			[
				'label'       => esc_html__( 'Value', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Query Value', 'essential-addons-elementor' ),
				'condition'   => [
					'logic_type' => 'query_string',
				]
			]
		);
	}

	private function url_contains_controls( $repeater ) {

		$repeater->add_control(
			'url_contains_url_type',
			[
				'label'      => __( 'URL Type', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'default'    => 'current',
				'options'    => [
					'current'     => [
						'title' => esc_html__( 'Current', 'essential-addons-elementor' ),
						'icon'  => 'eicon-editor-link',
					],
					'refferer' => [
						'title' => esc_html__( 'Refferer', 'essential-addons-elementor' ),
						'icon'  => 'eicon-editor-external-link',
					],
				],
				'condition'  => [
					'logic_type' => 'url_contains',
				],
			]
		);

		$repeater->add_control(
			'url_contains_logic_operator',
			[
				'label'      => __( 'Logic Operator', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'default'    => 'in',
				'options'    => [
					'in'     => [
						'title' => esc_html__( 'In', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-check-solid',
					],
					'not_in' => [
						'title' => esc_html__( 'Not In', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-xmark-solid',
					],
				],
				'condition'  => [
					'logic_type' => 'url_contains',
				],
			]
		);

		$repeater->add_control(
			'url_contains_string',
			[
				'label'      => __( 'String', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::TEXT,
				'ai' 		 => [ 'active' => false ],
				'condition'       => [
					'logic_type' => 'url_contains',
				]
			]
		);
	}

	private function archive_controls( $repeater ){
		$repeater->add_control(
			'archive_type',
			[
				'label'   => esc_html__( 'Archive Type', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => [
					'post'     => esc_html__( 'Post Type', 'essential-addons-elementor' ),
					'taxonomy' => esc_html__( 'Taxonomy', 'essential-addons-elementor' ),
					'terms'    => esc_html__( 'Terms', 'essential-addons-elementor' ),
					'author'   => esc_html__( 'Author', 'essential-addons-elementor' ),
					'date'     => esc_html__( 'Date', 'essential-addons-elementor' ),
					'search'   => esc_html__( 'Search', 'essential-addons-elementor' ),
				],
				'condition' => [
					'logic_type'  => 'archive',
				]
			]
		);

		$_post_types = ControlsHelper::get_post_types();
		$post_types  = array_merge( [ 'all' => 'All' ], $_post_types );

		$repeater->add_control(
			'archive_post_type',
			[
				'label'       => esc_html__( 'Select Post Type', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => $post_types,
				'default'     => 'post',
				'condition'   => [
					'logic_type'    => 'archive',
					'archive_type'  => 'post',
				]
			]
		);

		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
		$taxon_options = [];
		foreach( $taxonomies as $taxonomy ){
			$taxon_options[ $taxonomy->name ] = $taxonomy->label;
		}

		$repeater->add_control(
			'archive_taxonomies',
			[
				'label'       => esc_html__( 'Select Taxonomoies', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $taxon_options,
				'default'     => 'category',
				'condition'   => [
					'logic_type'   => 'archive',
					'archive_type' => [ 'taxonomy', 'terms' ],
				]
			]
		);

		foreach( $taxonomies as $taxonomy ){
			$repeater->add_control(
				"archive_terms_for_{$taxonomy->name}",
				[
					'label'       => esc_html__( 'Select ', 'essential-addons-elementor' ) . $taxonomy->label,
					'type'        => 'eael-select2',
					'source_name' => 'taxonomy',
					'source_type' => $taxonomy->name,
					'label_block' => true,
					'multiple'    => true,
					'condition'   => [
						'logic_type'         => 'archive',
						'archive_taxonomies' => $taxonomy->name,
						'archive_type'       => 'terms',
					]
				]
			);
		}

		$repeater->add_control(
			'archive_author_type',
			[
				'label'   => esc_html__( 'Author Type', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'user'      => [
						'title' => esc_html__( 'User', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-user-plus-solid',
					],
					'user_role' => [
						'title' => esc_html__( 'User Role', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-users-solid',
					],
				],
				'default'   => 'user',
				'toggle'    => false,
				'condition' => [
					'logic_type'         => 'archive',
					'archive_type'       => 'author',
				]
			]
		);

		$roles = $this->get_editable_roles();

		$repeater->add_control(
			'archive_user_role',
			[
				'label'       => __( 'Select User Roles', 'essential-addons-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $roles,
				'default'     => [],
				'condition'   => [
					'logic_type'          => 'archive',
					'archive_type'        => 'author',
					'archive_author_type' => 'user_role',
				]
			]
		);

		$repeater->add_control(
			'archive_users',
			[
				'label'       => esc_html__( 'Select Users', 'essential-addons-elementor' ),
				'type'        => 'eael-select2',
				'source_name' => 'user',
				'source_type' => 'all',
				'label_block' => true,
				'multiple'    => true,
				'condition'   => [
					'logic_type'          => 'archive',
					'archive_type'        => 'author',
					'archive_author_type' => 'user',
				]
			]
		);

		$repeater->add_control(
			'archive_date_from',
			[
				'label' => esc_html__( 'Archive Date From', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'enableTime' => false,
					'altFormat'  => 'M j, Y',
					'dateFormat' => 'Y-m-d',
				],
				'condition' => [
					'logic_type'   => 'archive',
					'archive_type' => 'date',
				]
			]
		);

		$repeater->add_control(
			'archive_date_to',
			[
				'label' => esc_html__( 'Archive Date To', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'enableTime' => false,
					'altFormat'  => 'M j, Y',
					'dateFormat' => 'Y-m-d',
				],
				'condition' => [
					'logic_type'   => 'archive',
					'archive_type' => 'date',
				]
			]
		);

	}

	private function woocommerce_controls( $repeater ) {

		if ( ! function_exists( 'WC' ) ) {
			$repeater->add_control(
				'woo_installation_notice_for_cart',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( '<strong>WooCommerce</strong> is not installed/activated on your site. Please install and activate <a href="plugin-install.php?s=woocommerce&tab=search&type=term" target="_blank">WooCommerce</a> first.', 'essential-addons-elementor' ),
					'content_classes' => 'eael-warning',
					'condition'       => [
						'logic_type' => 'woo_cart',
					]
				]
			);

			return;
		}

		$repeater->add_control(
			'woo_purchase_type',
			[
				'label'   => esc_html__( 'Purchase Type', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'last_order',
				'options' => [
					'no_order'   => esc_html__( 'Has No Order', 'essential-addons-elementor' ),
					'last_order' => esc_html__( 'From Last Purchase', 'essential-addons-elementor' ),
					'all_orders' => esc_html__( 'Between All Orders', 'essential-addons-elementor' ),
					'date_range' => esc_html__( 'Between Date Period', 'essential-addons-elementor' ),
				],
				'condition' => [
					'logic_type'  => 'woo_orders',
				]
			]
		);

		$repeater->add_control(
			'woo_order_date_from',
			[
				'label' => esc_html__( 'Purchased Date From', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'enableTime' => false,
					'altFormat'  => 'M j, Y',
					'dateFormat' => 'Y-m-d',
				],
				'condition' => [
					'logic_type'        => 'woo_orders',
					'woo_purchase_type' => 'date_range',
				]
			]
		);

		$repeater->add_control(
			'woo_order_date_to',
			[
				'label' => esc_html__( 'Purchased Date To', 'essential-addons-elementor' ),
				'type'  => Controls_Manager::DATE_TIME,
				'picker_options' => [
					'enableTime' => false,
					'altFormat'  => 'M j, Y',
					'dateFormat' => 'Y-m-d',
				],
				'condition' => [
					'logic_type'        => 'woo_orders',
					'woo_purchase_type' => 'date_range',
				]
			]
		);

		$repeater->add_control(
			'woo_products_logic_oparator',
			[
				'label'   => esc_html__( 'Oparator', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'include' => [
						'title'  => esc_html__( 'Include', 'essential-addons-elementor' ),
						'icon'   => 'eaicon-check-solid',
					],
					'exclude' => [
						'title'  => esc_html__( 'Exclude', 'essential-addons-elementor' ),
						'icon'   => 'eaicon-xmark-solid',
					],
				],
				'default'   => 'include',
				'toggle'    => false,
				'condition' => [
					'logic_type'         => 'woo_orders',
					'woo_purchase_type!' => 'no_order',
				]
			]
		);

		$repeater->add_control(
			'woo_cart_logic_operator',
			[
				'label'     => __( 'Cart items', 'essential-addons-elementor' ),
				// 'show_label' => false,
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'empty'  => [
						'title' => esc_html__( 'Empty', 'essential-addons-elementor' ),
						'icon'  => 'eicon-trash',
					],
					'in'     => [
						'title' => esc_html__( 'IN', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-check-solid',
					],
					'not_in' => [
						'title' => esc_html__( 'Not IN', 'essential-addons-elementor' ),
						'icon'  => 'eaicon-xmark-solid',
					],
				],
				'default'   => 'in',
				'toggle'    => false,
				'condition' => [
					'logic_type'          => 'woo_cart',
					'woo_cart_item_type!' => [ 'count', 'amount' ]
				]
			]
		);

		$repeater->add_control(
			'woo_cart_logic_operator_2',
			[
				'label'     => __( 'Compare Oparator', 'essential-addons-elementor' ),
				// 'show_label' => false,
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'less'  => [
						'title' => esc_html__( 'Less Than', 'essential-addons-elementor' ),
						'icon'  => 'eicon-chevron-left',
					],
					'greater'     => [
						'title' => esc_html__( 'Greater Than', 'essential-addons-elementor' ),
						'icon'  => 'eicon-chevron-right',
					],
				],
				'toggle'    => false,
				'default'   => 'greater',
				'condition' => [
					'logic_type'         => [ 'woo_cart', 'woo_products' ],
					'woo_cart_item_type' => [ 'count', 'amount' ]
				]
			]
		);

		$repeater->add_control(
			'woo_cart_item_type',
			[
				'label'      => __( 'Products By', 'essential-addons-elementor' ),
				// 'show_label' => false,
				'type'       => Controls_Manager::CHOOSE,
				'options'    => [
					'product'  => [
						'title' => esc_html__( 'Product', 'essential-addons-elementor' ),
						'icon'  => 'eicon-menu-toggle',
					],
					'ptype'    => [
						'title' => esc_html__( 'Type', 'essential-addons-elementor' ),
						'icon'  => 'eicon-flow',
					],
					'category' => [
						'title' => esc_html__( 'Category', 'essential-addons-elementor' ),
						'icon'  => 'eicon-sitemap',
					],
					'count' => [
						'title' => esc_html__( 'Count', 'essential-addons-elementor' ),
						'icon'  => 'eicon-woo-cart',
					],
					'amount' => [
						'title' => esc_html__( 'Total', 'essential-addons-elementor' ),
						'icon'  => 'eicon-product-price',
					],
				],
				'toggle'     => false,
				'default'    => 'product',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_cart',
								],
								[
									'name'     => 'woo_cart_logic_operator',
									'operator' => '!==',
									'value'    => 'empty',
								]
							]
						],
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_products',
								]
							]
						],
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_orders',
								],
								[
									'name'     => 'woo_purchase_type',
									'operator' => '!==',
									'value'    => 'no_order',
								],
							]
						],
					]
				]
			]
		);

		$repeater->add_control(
			'product_ids',
			[
				'label'       => __( 'Search & Select Products', 'essential-addons-elementor' ),
				'type'        => 'eael-select2',
				'options'     => ControlsHelper::get_post_list( 'product' ),
				'label_block' => true,
				'multiple'    => true,
				'source_name' => 'post_type',
				'source_type' => 'product',
				'conditions'  => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_cart',
								],
								[
									'name'     => 'woo_cart_logic_operator',
									'operator' => '!==',
									'value'    => 'empty',
								],
								[
									'name'     => 'woo_cart_item_type',
									'operator' => '===',
									'value'    => 'product',
								]
							]
						],
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_products',
								],
								[
									'name'     => 'woo_cart_item_type',
									'operator' => '===',
									'value'    => 'product',
								]
							]
						],
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_orders',
								],
								[
									'name'     => 'woo_purchase_type',
									'operator' => '!==',
									'value'    => 'no_order',
								],
								[
									'name'     => 'woo_cart_item_type',
									'operator' => '===',
									'value'    => 'product',
								]
							]
						],
					]
				]
			]
		);

		$repeater->add_control(
			'product_types',
			[
				'label'       => esc_html__( 'Select Product Types', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => wc_get_product_types(),
				'conditions'  => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_cart',
								],
								[
									'name'     => 'woo_cart_logic_operator',
									'operator' => '!==',
									'value'    => 'empty',
								],
								[
									'name'     => 'woo_cart_item_type',
									'operator' => '===',
									'value'    => 'ptype',
								]
							]
						],
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_products',
								],
								[
									'name'     => 'woo_cart_item_type',
									'operator' => '===',
									'value'    => 'ptype',
								]
							]
						],
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_orders',
								],
								[
									'name'     => 'woo_purchase_type',
									'operator' => '!==',
									'value'    => 'no_order',
								],
								[
									'name'     => 'woo_cart_item_type',
									'operator' => '===',
									'value'    => 'ptype',
								]
							]
						],
					]
				]
			]
		);

		$repeater->add_control(
			'product_categories',
			[
				'label'       => __( 'Search & Select Categories', 'essential-addons-elementor' ),
				'type'        => 'eael-select2',
				'options'     => Helper::get_taxonomies_by_post( [ 'object_type' => 'product' ] ),
				'label_block' => true,
				'multiple'    => true,
				'source_name' => 'taxonomy',
				'source_type' => 'product_cat',
				'conditions'  => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_cart',
								],
								[
									'name'     => 'woo_cart_logic_operator',
									'operator' => '!==',
									'value'    => 'empty',
								],
								[
									'name'     => 'woo_cart_item_type',
									'operator' => '===',
									'value'    => 'category',
								]
							]
						],
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_products',
								],
								[
									'name'     => 'woo_cart_item_type',
									'operator' => '===',
									'value'    => 'category',
								]
							]
						],
						[
							'terms' => [
								[
									'name'     => 'logic_type',
									'operator' => '===',
									'value'    => 'woo_orders',
								],
								[
									'name'     => 'woo_purchase_type',
									'operator' => '!==',
									'value'    => 'no_order',
								],
								[
									'name'     => 'woo_cart_item_type',
									'operator' => '===',
									'value'    => 'category',
								]
							]
						],
					]
				]
			]
		);

		$repeater->add_control(
			'cart_item_count',
			[
				'label'      => esc_html__( 'Cart item Count', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::NUMBER,
				'min'        => 1,
				'step'       => 1,
				'default'    => 2,
				'condition'  => [
					'logic_type'         => 'woo_cart',
					'woo_cart_item_type' => 'count'
				]
			]
		);

		$currency = get_woocommerce_currency_symbol();
		$repeater->add_control(
			'cart_item_amount',
			[
				'label'      => sprintf( esc_html__( 'Cart total (%s)', 'essential-addons-elementor' ), $currency ),
				'type'       => Controls_Manager::NUMBER,
				'min'        => 1,
				'step'       => 1,
				'default'    => 20,
				'condition'  => [
					'logic_type'         => 'woo_cart',
					'woo_cart_item_type' => 'amount'
				]
			]
		);

		$repeater->add_control(
			'product_stock_count',
			[
				'label'      => esc_html__( 'Stock Count', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::NUMBER,
				'min'        => 0,
				'step'       => 1,
				'default'    => 2,
				'condition'  => [
					'logic_type'         => 'woo_products',
					'woo_cart_item_type' => 'count'
				]
			]
		);

		$repeater->add_control(
			'product_price',
			[
				'label'      => sprintf( esc_html__( 'Product Price (%s)', 'essential-addons-elementor' ), $currency ),
				'type'       => Controls_Manager::NUMBER,
				'min'        => 0,
				'step'       => 1,
				'default'    => 20,
				'condition'  => [
					'logic_type'         => 'woo_products',
					'woo_cart_item_type' => 'amount'
				]
			]
		);

		$repeater->add_control(
			'cart_item_count_note',
			[
				'label'           => esc_html__( 'Important Note about item count', 'essential-addons-elementor' ),
				'show_label' 	  => false,
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'This is only applicable for Logic type Woo Cart & Woo Products', 'essential-addons-elementor' ),
				'content_classes' => 'eael-warning',
				'condition'       => [
					'logic_type'          => [ 'woo_orders' ],
					'woo_cart_item_type'  => [ 'count', 'amount' ]
				]
			]
		);

	}

	private function get_ordered_products( $data_type, $cl_logic ){

		$user_id = get_current_user_id();
		$args    = [
			'customer_id' => $user_id,
			'limit'       => -1,
		];

		if( 'last_order' === $cl_logic['woo_purchase_type'] ){
			$args['limit'] = 1;

		} else if ( 'date_range' === $cl_logic['woo_purchase_type'] && "" !== $cl_logic['woo_order_date_from'] && "" !== $cl_logic['woo_order_date_to'] ) {
			
			$args['date_after']  = $cl_logic['woo_order_date_from'];
			$args['date_before'] = $cl_logic['woo_order_date_to'];
		}

		$orders = wc_get_orders( $args );
		
		if( empty( $orders ) ){
			return [];
		}

		$data = [];
		if( 'ids' === $data_type ){
			foreach( $orders as $order ){
				foreach ( $order->get_items() as $item_id => $item ) {
					$product = $this->get_product_from_order_item( $item );
					$data[]  = $product->get_id();
				}
			}
		} else if( 'types' === $data_type ){
			foreach( $orders as $order ){
				foreach ( $order->get_items() as $item_id => $item ) {
					$product = $this->get_product_from_order_item( $item );
					$data[]  = $product->get_type();
				}
			}
		} else if( 'categories' === $data_type ){
			foreach( $orders as $order ){
				foreach ( $order->get_items() as $item_id => $item ) {
					$product = $this->get_product_from_order_item( $item );
					$data    = array_merge( $data, $product->get_category_ids() );
				}
			}
		}
		
		return array_unique( $data );
	}

	private function get_product_from_order_item( $item ){
		$product      = $item->get_product();
		$variation_id = $item->get_variation_id();

		if( $variation_id ){
			$parent_id = $product->get_parent_id();
			$product   = wc_get_product( $parent_id );
		}

		return $product;
	}
}
