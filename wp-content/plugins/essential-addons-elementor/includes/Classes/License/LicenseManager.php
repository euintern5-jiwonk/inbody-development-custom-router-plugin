<?php

namespace Essential_Addons_Elementor\Pro\Classes\License;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;
use WP_Error;

/**
 * @property int             $item_id
 * @property string          $version
 * @property string          $storeURL
 * @property string          $db_prefix
 * @property string          $textdomain
 * @property string          $item_name
 * @property string          $plugin_file
 * @property string          $page_slug
 * @property string|string[] $screen_id
 * @property string          $scripts_handle
 * @property bool            $dev_mode
 * @property string          $api
 * @property string          $namespace
 */
#[\AllowDynamicProperties]
class LicenseManager {
	private        $_version     = '2.0.0';
	private static $_instance    = null;
	protected      $license      = '';
	protected      $license_data = null;

	/**
	 * @var array
	 */
	protected $args = [
		'version'        => '',
		// 'author'         => '',
		// 'beta'           => '',
		// 'activation_notice' => '',
		// 'revalidation_notice' => '',
		'plugin_file'    => '',
		'item_id'        => 0,
		'item_name'      => '',
		'item_slug'      => '',
		'storeURL'       => '',
		'textdomain'     => '',
		'db_prefix'      => '',
		'scripts_handle' => '',
		'screen_id'      => '',
		'page_slug'      => '',
		'api'            => ''
	];

	/**
	 * @var array
	 */
	private $error = [];

	/**
	 * @throws Exception
	 */
	public static function get_instance( $args ) {
		if ( self::$_instance === null ) {
			self::$_instance = new self( $args );
		}

		return self::$_instance;
	}

	public function __get( $name ) {
		if ( property_exists( $this, $name ) ) {
			return $this->$name;
		}

		if ( isset( $this->args[ $name ] ) ) {
			return $this->args[ $name ];
		}

		return null;
	}

	public function __isset( $name ) {
		return isset( $this->args[ $name ] );
	}

	/**
	 * @throws Exception
	 */
	public function __construct( $args ) {
		foreach ( $this->args as $property => $value ) {
			if ( ! array_key_exists( $property, $args ) ) {
				throw new Exception( "$property is missing in licensing." );
			}
		}

		$this->args = wp_parse_args( $args, $this->args );

		if ( $this->dev_mode === true ) {
			add_filter( 'http_request_host_is_external', '__return_true' );
		}

		$this->license_data = $this->get_license_data();

		add_action( 'admin_notices', [ $this, 'admin_notices' ] );


		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ], 999 );

		if ( isset( $this->args['api'] ) ) {
			switch ( strtolower( $this->args['api'] ) ) {
				case 'rest':
					if ( ! isset( $this->args['rest'] ) ) {
						throw new Exception( "rest is missing in licensing." );
					}
					new RESTApi( $this );
					break;
				case 'ajax':
					if ( ! isset( $this->args['ajax'] ) ) {
						throw new Exception( "ajax is missing in licensing." );
					}
					new AJAXApi( $this );
					break;
			}
		}

		add_action( 'init', [ $this, 'plugin_updater' ] );
	}

	public function admin_notices() {
		$this->error = $this->get_error();

		if ( ! empty( $this->error ) ) {
			$notice = sprintf( '<div style="padding: 10px;" class="%1$s-notice wpdeveloper-licensing-notice notice notice-error">%2$s</div>', $this->textdomain, $this->error['message'] );

			echo wp_kses_post( $notice );

			return;
		}

		if ( ! ( ( empty( $this->license_data ) ) && current_user_can( 'activate_plugins' ) ) ) {
			return;
		}

		$message = sprintf( __( '%1$sActivate your %3$s License Key%2$s to receive regular updates and secure your WordPress website.', 'essential-addons-elementor' ), '<a style="text-decoration: underline; font-weight: bold;" href="' . admin_url( 'admin.php?page=' . $this->page_slug ) . '">', '</a>', $this->item_name );

		if ( isset( $this->args['activation_notice'] ) ) {
			$message = $this->args['activation_notice'];
		}

		$notice = sprintf( '<div style="padding: 10px;" class="%1$s-notice wpdeveloper-licensing-notice notice notice-error">%2$s</div>', $this->textdomain, $message );

		echo wp_kses_post( $notice );
	}

	public function plugin_updater() {
		$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;

		if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
			return;
		}

		$_license = $this->get_license();

		new PluginUpdater( $this->storeURL, $this->plugin_file, [
			'sdk_version' => $this->_version,
			'version'     => $this->version, // current version number
			'license'     => $_license, // license key (used get_option above to retrieve from DB)
			'item_id'     => $this->item_id, // ID of the product
			'author'      => empty( $this->author ) ? 'WPDeveloper' : $this->author, // author of this plugin
			'beta'        => isset( $this->beta ) ? $this->beta : false
		] );
	}

	public function get_args( $name = '' ) {
		return empty( $name ) ? $this->args : $this->args[ $name ];
	}

	public function enqueue( $hook ) {
		if ( is_array( $this->screen_id ) && ! in_array( $hook, $this->screen_id ) ) {
			return;
		}

		if ( ! is_array( $this->screen_id ) && $this->screen_id !== $hook ) {
			return;
		}

		wp_localize_script( $this->scripts_handle, 'wpdeveloperLicenseData', $this->get_license_data() );
	}

	public function get_license_data() {
		$_license        = $this->get_license();
		$_license_status = $this->get_status();
		$_license_data   = $this->get_license_data_raw();

		if ( $_license_data !== false ) {
			$_license_data = (array) $_license_data;
		}

		if ( empty( $_license_data ) ) {
			$response = $this->check();
			if ( is_wp_error( $response ) ) {
				return [];
			}

			$_license_data = (array) $response;
		}

		return array_merge( [
			'license_key'        => $_license,
			'hidden_license_key' => $this->hide_license_key( $_license ),
			'license_status'     => $_license_status
		], $_license_data );
	}

	public function hide_license_key( $_license ) {
		$length = mb_strlen( $_license ) - 10;

		return substr_replace( $_license, mb_substr( preg_replace( '/\S/', '*', $_license ), 5, $length ), 5, $length );
	}

	public function activate( $args = [] ) {
		$this->license = sanitize_text_field( isset( $args['license_key'] ) ? trim( $args['license_key'] ) : '' );
		$response      = $this->remote_post( 'activate_license' );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		/**
		 * Return if license required OTP to activate.
		 */
		if ( isset( $response->license ) && $response->license == 'required_otp' ) {
			return $response;
		}

		$this->remove_error();

		$this->addData( $response );

		return $response;
	}

	public function deactivate() {
		$this->license = $this->get_license();
		$response      = $this->remote_post( 'deactivate_license' );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$this->removeData();

		return $response;
	}

	public function submit_otp( $args = [] ) {
		$this->license = sanitize_text_field( isset( $args['license_key'] ) ? trim( $args['license_key'] ) : '' );
		$response      = $this->remote_post( 'activate_license_by_otp', $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$this->addData( $response );

		return $response;
	}

	public function resend_otp( $args ) {
		$this->license = sanitize_text_field( isset( $args['license_key'] ) ? trim( $args['license_key'] ) : '' );

		return $this->remote_post( 'resend_otp_for_license', $args );
	}

	public function check() {
		$this->license = $this->get_license();
		$_license_data = $this->get_license_data_raw();

		if ( $_license_data !== false ) {
			$_license_data = (array) $_license_data;
		}

		if ( ! empty( $_license_data ) ) {
			return $_license_data;
		}

		$response = $this->remote_post( 'check_license' );

		if ( is_wp_error( $response ) ) {
			$this->remove_license_data();

			return $response;
		}

		$this->set_license_data( $response );
		$this->remove_error();

		return $response;
	}

	/**
	 * 'activate_license'
	 *
	 * @param       $action
	 * @param mixed $args
	 *
	 * @return mixed
	 */
	public function remote_post( $action, $args = [] ) {
		if ( empty( $this->license ) ) {
			return new WP_Error( 'empty_license', __( 'Please provide a valid license.', $this->textdomain ) );
		}

		$defaults = [
			'sdk_version' => $this->_version,
			'edd_action'  => $action,
			'license'     => $this->license,
			'item_id'     => $this->item_id,
			'item_name'   => rawurlencode( $this->item_name ), // the name of our product in EDD
			'url'         => home_url(),
			'version'     => $this->version,
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production'
		];

		$args = wp_parse_args( $args, $defaults );

		$response = wp_safe_remote_post( $this->storeURL, [
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $args
		] );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$this->set_error( [
					'code'    => $response->get_error_code(),
					'message' => $response->get_error_message()
				] );

				return $response;
			}

			$this->set_error( [
				'code'    => 'unknown',
				'message' => __( 'An error occurred, please try again.', $this->textdomain )
			] );

			return new WP_Error( 'unknown', __( 'An error occurred, please try again.', $this->textdomain ) );
		}

		$license_data = $this->maybe_error( json_decode( wp_remote_retrieve_body( $response ) ) );

		if ( is_wp_error( $license_data ) ) {
			$this->set_error( [
				'code'    => $license_data->get_error_code(),
				'message' => $license_data->get_error_message()
			] );
		} else {
			$license_data->license_key = $this->hide_license_key( $this->license );
			$this->remove_error();
		}

		return $license_data;
	}

	private function maybe_error( $license_data ) {
		if ( false === $license_data->success ) {
			$error_code = 'unknown';

			if ( isset( $license_data->error ) ) {
				$error_code = $license_data->error;
			} elseif ( isset( $license_data->license ) ) {
				$error_code = $license_data->license;
			}

			switch ( $error_code ) {
				case 'expired':
					$message = sprintf( /* translators: the license key expiration date */ __( 'Your license key expired on %s.', $this->textdomain ), date_i18n( get_option( 'date_format' ), $license_data->expires ) );
					break;

				case 'invalid_otp':
					$message = __( 'Your license confirmation code is invalid.', $this->textdomain );
					break;

				case 'expired_otp':
					$message = __( 'Your license confirmation code has been expired.', $this->textdomain );
					break;

				case 'revalidate_license':
					$message = sprintf( __( '%1$sAttention:%2$s Please %3$sVerify your %5$s License Key%4$s to get regular updates & secure your WordPress website.', 'essential-addons-elementor' ), '<strong>', '</strong>', '<a style="text-decoration: underline; font-weight: bold;" href="' . admin_url( 'admin.php?page=' . $this->page_slug ) . '">', '</a>', $this->item_name );
					break;

				case 'disabled':
				case 'revoked':
					$message = sprintf( __( 'Your %s license key has been disabled.', $this->textdomain ), $this->item_name );
					break;

				case 'missing':
					$message = __( 'Invalid license.', $this->textdomain );
					break;

				case 'invalid':
				case 'site_inactive':
					$message = __( 'Your license is not active for this URL.', $this->textdomain );
					break;

				case 'item_name_mismatch':
					/* translators: the plugin name */ $message = sprintf( __( 'This appears to be an invalid license key for %s.', $this->textdomain ), $this->item_name );
					break;

				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', $this->textdomain );
					break;

				case 'custom':
					$message = ! empty( $license_data->message ) ? $license_data->message : __( 'Something went wrong.', $this->textdomain );
					break;

				default:
					$message = __( 'An error occurred, please try again.', $this->textdomain );
					break;
			}

			return new WP_Error( $error_code, wp_kses( $message, 'post' ) );
		}

		return $license_data;
	}

	public function get_license( $default = '' ) {
		return get_option( "{$this->db_prefix}-license-key", $default );
	}

	public function set_license() {
		return update_option( "{$this->db_prefix}-license-key", $this->license, 'no' );
	}

	public function get_license_data_raw() {
		return get_transient( "{$this->db_prefix}-license_data" );
	}

	public function set_license_data( $response, $expiration = null ) {
		if ( null === $expiration ) {
			$expiration = MONTH_IN_SECONDS * 3;
		}

		set_transient( "{$this->db_prefix}-license_data", $response, $expiration );
	}

	public function remove_license_data() {
		return delete_transient( "{$this->db_prefix}-license_data" );
	}

	private function set_error( $error ) {
		update_option( "{$this->db_prefix}license_data_error", $error );
	}

	private function get_error() {
		if ( $this->license_data ) {
			$this->remove_error();

			return '';
		}

		return get_option( "{$this->db_prefix}license_data_error", '' );
	}

	private function remove_error() {
		delete_option( "{$this->db_prefix}license_data_error" );
	}

	public function get_status() {
		return get_option( "{$this->db_prefix}-license-status" );
	}

	public function set_status( $status = 'valid' ) {
		return update_option( "{$this->db_prefix}-license-status", $status, 'no' );
	}

	public function removeData( $withError = true ) {
		delete_option( "{$this->db_prefix}-license-key" );
		delete_option( "{$this->db_prefix}-license-status" );

		$this->remove_license_data();

		if ( $withError ) {
			$this->remove_error();
		}
	}

	public function addData( $response ) {
		$this->set_license();
		$this->set_status( $response->license );
		$this->set_license_data( $response );
	}

	private function get( $key, $default = false ) {
		$option_key = $this->db_prefix . '_' . $key;

		return get_option( $option_key, $default );
	}

	private function set( $key, $value ) {
		$option = "{$this->db_prefix}_{$key}";

		return update_option( $option, $value, 'no' );
	}

	private function delete( $key ) {
		$option = "{$this->db_prefix}_{$key}";

		return delete_option( $option );
	}
}
