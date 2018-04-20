<?php
/**
 * WP Hotel Booking Extra room custom post type class.
 *
 * @class       WPHB_Custom_Post_Type_Extra
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Custom_Post_Type_Extra' ) ) {
	/**
	 * Class WPHB_Custom_Post_Type_Extra
	 */
	class WPHB_Custom_Post_Type_Extra {

		/**
		 * WPHB_Custom_Post_Type_Extra constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_post_type' ) );
		}

		/**
		 * Register post type.
		 */
		public function register_post_type() {
			// register room extra package
			$args = array(
				'labels'              => array(
					'name'               => __( 'Extra Room', 'wp-hotel-booking' ),
					'singular_name'      => __( 'Extra Room', 'wp-hotel-booking' ),
					'add_new'            => _x( 'Add New Extra Room', 'wp-hotel-booking', 'wp-hotel-booking' ),
					'add_new_item'       => __( 'Add New Extra Room', 'wp-hotel-booking' ),
					'edit_item'          => __( 'Edit Extra Room', 'wp-hotel-booking' ),
					'new_item'           => __( 'New Extra Room', 'wp-hotel-booking' ),
					'view_item'          => __( 'View Extra Room', 'wp-hotel-booking' ),
					'search_items'       => __( 'Search Extra Room', 'wp-hotel-booking' ),
					'not_found'          => __( 'No Extra Room found', 'wp-hotel-booking' ),
					'not_found_in_trash' => __( 'No Extra Room found in Trash', 'wp-hotel-booking' ),
					'parent_item_colon'  => __( 'Parent Singular Extra Room:', 'wp-hotel-booking' ),
					'menu_name'          => __( 'Extra Room', 'wp-hotel-booking' ),
				),
				'hierarchical'        => false,
				'description'         => __( 'Extra room system booking', 'wp-hotel-booking' ),
				'taxonomies'          => array(),
				'public'              => false,
				'show_ui'             => false,
				'show_in_menu'        => false,
				'show_in_admin_bar'   => false,
				'menu_position'       => null,
				'menu_icon'           => null,
				'show_in_nav_menus'   => false,
				'publicly_queryable'  => true,
				'exclude_from_search' => true,
				'has_archive'         => true,
				'query_var'           => true,
				'rewrite'             => true,
				'capability_type'     => 'hb_room',
				'supports'            => array( 'title', 'editor' )
			);

			register_post_type( 'hb_extra_room', apply_filters( 'hb_register_post_type_extra_room_arg', $args ) );
		}
	}
}

new WPHB_Custom_Post_Type_Extra();