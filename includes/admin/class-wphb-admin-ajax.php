<?php

/**
 * WP Hotel Booking admin class.
 *
 * @class       WPHB_Admin_Ajax
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Admin_Ajax' ) ) {
	/**
	 * Class WPHB_Admin_Ajax.
	 */
	class WPHB_Admin_Ajax {

		/**
		 * Init.
		 */
		public function init() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$actions = array(
				'extra_panel'
			);

			foreach ( $actions as $action ) {
				add_action( "wp_ajax_wphb_{$action}", array( __CLASS__, $action ) );
			}
		}

		/**
		 * Handle extra panel actions.
		 */
		public static function extra_panel() {
			check_ajax_referer( 'wphb_admin_extra_nonce', 'nonce' );

			$args = wp_parse_args( $_REQUEST, array( 'action' => '', 'type' => '' ) );

			// curd
			$curd = new WPHB_Extra_CURD();
			// response
			$result = false;

			switch ( $args['type'] ) {
				case 'new-extra':
					$extra = json_decode( wp_unslash( $args['extra'] ), true );
					// create new extra
					$result = $curd->create( $extra );

					break;
				case 'update-extra':
					$extra  = json_decode( wp_unslash( $args['extra'] ), true );
					$result = $curd->update( $extra );
					break;
				case 'delete-extra':
					$id = $args['extra_id'] ? $args['extra_id'] : '';

					if ( $id && get_post_type( $id ) == WPHB_Extra_CPT ) {
						wp_delete_post( $id, true );
						$result = true;
					}
					break;
			}

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( $result->get_error_message() );
			}

			wp_send_json_success( $result );
		}
	}

}

add_action( 'init', array( 'WPHB_Admin_Ajax', 'init' ) );