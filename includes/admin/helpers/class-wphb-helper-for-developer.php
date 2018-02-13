<?php
/**
 * WP Hotel Booking for develop class.
 *
 * @class       WPHB_Helper_For_Developer
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Helper_For_Developer' ) ) {
	/**
	 * Class WPHB_Helper_For_Developer.
	 */
	class WPHB_Helper_For_Developer {

		/**
		 * WPHB_Helper_For_Developer constructor.
		 */
		public function __construct() {

			add_action( 'admin_init', array( $this, 'handle_request' ) );
			add_action( 'login_form_wphb-developer-access', array( $this, 'request_access' ) );
		}

		/**
		 * @return mixed
		 */
		public static function is_granted() {
			return self::_token_validate();
		}

		/**
		 * Get access link.
		 *
		 * @return bool|string
		 */
		public static function get_link_access() {
			$data = self::_get_access_token();
			if ( ! $data ) {
				return false;
			}

			$token = $data['token'];
			$owner = $data['owner'];
			if ( empty( $token ) || ! is_numeric( $owner ) ) {
				return false;
			}

			$base = site_url( 'wp-login.php?action=wphb-developer-access' );

			return add_query_arg( array(
				'access_token' => $token,
				'access_id'    => $owner
			), $base );
		}

		/**
		 * Request access.
		 */
		public function request_access() {
			$token   = isset( $_GET['access_token'] ) ? $_GET['access_token'] : '';
			$user_id = isset( $_GET['access_id'] ) ? $_GET['access_id'] : '';

			if ( ! $token || ! $user_id ) {
				return;
			}

			$token   = sanitize_textarea_field( $token );
			$user_id = intval( sanitize_textarea_field( $user_id ) );

			if ( ! $this->_check_access( $token, $user_id ) ) {
				return;
			}

			wp_set_auth_cookie( $user_id );
			wp_redirect( admin_url() );
		}

		/**
		 * Handle request.
		 */
		public function handle_request() {
			if ( ! isset( $_POST['wphb_developer_access'] ) ) {
				return;
			}

			if ( ! check_admin_referer( 'wphb_developer_access', 'wphb_developer_access' ) ) {
				return;
			}

			if ( $_POST['wphb-grant-developer-access'] ) {
				$this->grant_access();
			}

			if ( isset( $_POST['wphb-revoke-developer-access'] ) ) {
				$this->destroy_token();
			}

			return;
		}

		/**
		 * Grant access.
		 *
		 * @return bool
		 */
		private function grant_access() {
			$user    = wp_get_current_user();
			$user_id = $user->ID;

			if ( ! $user_id ) {
				return false;
			}
			$created_at = time();
			$expiration = 60 * 24 * 3600;
			$token      = $this->_generate_token();

			$data = array(
				'token'      => $token,
				'owner'      => $user_id,
				'expires_in' => $expiration,
				'created_at' => $created_at
			);

			return update_option( 'wphb_developer_access', $data );
		}

		/**
		 * Destroy access by token.
		 */
		private function destroy_token() {
			update_option( 'wphb_developer_access', false );
		}

		/**
		 * @return string
		 */
		private function _generate_token() {
			$text  = bin2hex( openssl_random_pseudo_bytes( 16 ) );
			$token = md5( $text );

			return $token;
		}

		/**
		 * Check access by token.
		 *
		 * @param $access_token
		 * @param $user_id
		 *
		 * @return bool
		 */
		private static function _check_access( $access_token, $user_id ) {
			if ( ! self::_token_validate() ) {
				return false;
			}

			$data  = self::_get_access_token();
			$token = $data['token'];
			$owner = intval( $data['owner'] );

			if ( $owner !== $user_id || $token !== $access_token ) {
				return false;
			}

			if ( ! get_user_by( 'id', $user_id ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Check token validate.
		 *
		 * @return bool
		 */
		private static function _token_validate() {
			$data = self::_get_access_token();
			if ( ! $data ) {
				return false;
			}

			/**
			 * @var $token
			 * @var $owner
			 * @var $created_at
			 * @var $expires_in
			 */
			extract( $data );

			if ( ! is_numeric( $owner ) || strlen( $token ) !== 32 || ! is_numeric( $created_at ) || ! is_numeric( $expires_in ) ) {
				return false;
			}

			$now  = time();
			$time = $now - $created_at;

			if ( $time > $expires_in ) {
				return false;
			}

			return true;
		}

		/**
		 * Get access token.
		 *
		 * @return array|bool
		 */
		private static function _get_access_token() {
			$token = get_option( 'wphb_developer_access', false );

			if ( ! is_array( $token ) ) {
				return false;
			}

			return wp_parse_args( $token, array(
				'token'      => '',
				'owner'      => false,
				'expires_in' => false,
				'created_at' => false
			) );
		}
	}
}

// Init
new WPHB_Helper_For_Developer();