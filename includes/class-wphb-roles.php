<?php

/**
 * WP Hotel Booking role class.
 *
 * @class       WPHB_Roles
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Roles' ) ) {

	/**
	 * Class WPHB_Roles.
	 *
	 * @since 2.0
	 */
	class WPHB_Roles {

		/**
		 * WPHB_Roles constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'add_roles' ) );
		}


		/**
		 * Add user system roles.
		 *
		 * @since 2.0
		 */
		public static function add_roles() {

			add_role(
				'wphb_hotel_manager',
				__( 'Hotel Manager', 'wp-hotel-booking' ),
				array()
			);

			$room_cap    = 'hb_rooms';
			$booking_cap = 'hb_bookings';

			/**
			 * Add cap for hotel manager.
			 */
			$hotel_manager = get_role( 'wphb_hotel_manager' );

			$hotel_manager->add_cap( 'read' );
			$hotel_manager->add_cap( 'edit_posts' );
			$hotel_manager->add_cap( 'delete_' . $room_cap );
			$hotel_manager->add_cap( 'edit_posts' );
			$hotel_manager->add_cap( 'publish_' . $room_cap );
			$hotel_manager->add_cap( 'delete_published_' . $room_cap );
			$hotel_manager->add_cap( 'delete_private_' . $room_cap );
			$hotel_manager->add_cap( 'delete_others_' . $room_cap );
			$hotel_manager->add_cap( 'edit_' . $room_cap );
			$hotel_manager->add_cap( 'edit_published_' . $room_cap );
			$hotel_manager->add_cap( 'edit_private_' . $room_cap );
			$hotel_manager->add_cap( 'edit_others_' . $room_cap );

			$hotel_manager->add_cap( 'publish_' . $booking_cap );
			$hotel_manager->add_cap( 'delete_' . $booking_cap );
			$hotel_manager->add_cap( 'delete_published_' . $booking_cap );
			$hotel_manager->add_cap( 'delete_other_' . $booking_cap );
			$hotel_manager->add_cap( 'delete_private_' . $booking_cap );
			$hotel_manager->add_cap( 'delete_others_' . $booking_cap );
			$hotel_manager->add_cap( 'edit_' . $booking_cap );
			$hotel_manager->add_cap( 'edit_published_' . $booking_cap );
			$hotel_manager->add_cap( 'edit_private_' . $booking_cap );
			$hotel_manager->add_cap( 'edit_others_' . $booking_cap );

			$hotel_manager->add_cap( 'upload_files' );
			$hotel_manager->add_cap( 'manage_hb_booking' );

			add_role(
				'wphb_booking_editor',
				__( 'Booking Editor', 'wp-hotel-booking' ),
				array()
			);

			/**
			 * Add cap for booking editor.
			 */
			$booking_editor = get_role( 'wphb_booking_editor' );

			$booking_editor->add_cap( 'read' );
			$booking_editor->add_cap( 'edit_posts' );
			$booking_editor->add_cap( 'publish_' . $room_cap );
			$booking_editor->add_cap( 'delete_' . $room_cap );
			$booking_editor->add_cap( 'delete_published_' . $room_cap );
			$booking_editor->add_cap( 'delete_private_' . $room_cap );
			$booking_editor->add_cap( 'delete_others_' . $room_cap );
			$booking_editor->add_cap( 'edit_' . $room_cap );
			$booking_editor->add_cap( 'edit_published_' . $room_cap );
			$booking_editor->add_cap( 'edit_private_' . $room_cap );
			$booking_editor->add_cap( 'edit_others_' . $room_cap );

			$booking_editor->add_cap( 'publish_' . $booking_cap );
			$booking_editor->add_cap( 'delete_' . $booking_cap );
			$booking_editor->add_cap( 'delete_published_' . $booking_cap );
			$booking_editor->add_cap( 'delete_private_' . $booking_cap );
			$booking_editor->add_cap( 'delete_others_' . $booking_cap );
			$booking_editor->add_cap( 'edit_' . $booking_cap );
			$booking_editor->add_cap( 'edit_published_' . $booking_cap );
			$booking_editor->add_cap( 'edit_private_' . $booking_cap );
			$booking_editor->add_cap( 'edit_others_' . $booking_cap );

			$booking_editor->add_cap( 'upload_files' );


			/**
			 * Add cap for  administrator.
			 */
			$admin = get_role( 'administrator' );

			$admin->add_cap( 'publish_' . $room_cap );
			$admin->add_cap( 'delete_' . $room_cap );
			$admin->add_cap( 'delete_published_' . $room_cap );
			$admin->add_cap( 'delete_private_' . $room_cap );
			$admin->add_cap( 'delete_others_' . $room_cap );
			$admin->add_cap( 'edit_' . $room_cap );
			$admin->add_cap( 'edit_published_' . $room_cap );
			$admin->add_cap( 'edit_private_' . $room_cap );
			$admin->add_cap( 'edit_others_' . $room_cap );

			$admin->add_cap( 'publish_' . $booking_cap );
			$admin->add_cap( 'delete_' . $booking_cap );
			$admin->add_cap( 'delete_published_' . $booking_cap );
			$admin->add_cap( 'delete_others_' . $booking_cap );
			$admin->add_cap( 'delete_private_' . $booking_cap );
			$admin->add_cap( 'delete_others_' . $booking_cap );
			$admin->add_cap( 'edit_' . $booking_cap );
			$admin->add_cap( 'edit_published_' . $booking_cap );
			$admin->add_cap( 'edit_private_' . $booking_cap );
			$admin->add_cap( 'edit_others_' . $booking_cap );

			$admin->add_cap( 'manage_hb_booking' );

		}

	}

}

new WPHB_Roles();