<?php

/**
 * WP Hotel Booking Coupon custom post type class.
 *
 * @class       WPHB_Coupon_Post_Types
 * @version     2.0
 * @package     WP_Hotel_Booking_Coupon/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Coupon_Post_Types' ) ) {

	/**
	 * Class WPHB_Coupon_Post_Types.
	 *
	 * @since 2.0
	 */
	class WPHB_Coupon_Post_Types {

		/**
		 * WPHB_Coupon_Post_Types constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_post_types' ) );

			// custom coupon columns
			add_filter( 'manage_hb_coupon_posts_columns', array( $this, 'custom_coupon_columns' ) );
			add_action( 'manage_hb_coupon_posts_custom_column', array( $this, 'custom_coupon_columns_filter' ) );
		}

		/**
		 * Register custom post types.
		 *
		 * @since 2.0
		 */
		public function register_post_types() {

			/**
			 * Register coupon custom post type.
			 */
			$args = array(
				'labels'             => array(
					'name'               => _x( 'Coupons', 'Coupons', 'wphb-coupon' ),
					'singular_name'      => _x( 'Coupon', 'Coupon', 'wphb-coupon' ),
					'menu_name'          => __( 'Coupons', 'wphb-coupon' ),
					'parent_item_colon'  => __( 'Parent Item:', 'wphb-coupon' ),
					'all_items'          => __( 'Coupons', 'wphb-coupon' ),
					'view_item'          => __( 'View Coupon', 'wphb-coupon' ),
					'add_new_item'       => __( 'Add New Coupon', 'wphb-coupon' ),
					'add_new'            => __( 'Add New', 'wphb-coupon' ),
					'edit_item'          => __( 'Edit Coupon', 'wphb-coupon' ),
					'update_item'        => __( 'Update Coupon', 'wphb-coupon' ),
					'search_items'       => __( 'Search Coupon', 'wphb-coupon' ),
					'not_found'          => __( 'No coupon found', 'wphb-coupon' ),
					'not_found_in_trash' => __( 'No coupon found in Trash', 'wphb-coupon' ),
				),
				'public'             => false,
				'query_var'          => true,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'has_archive'        => false,
				'capability_type'    => 'hb_room',
				'map_meta_cap'       => true,
				'show_in_menu'       => 'tp_hotel_booking',
				'show_in_admin_bar'  => true,
				'show_in_nav_menus'  => true,
				'supports'           => array( 'title' ),
				'hierarchical'       => false
			);
			$args = apply_filters( 'hotel_booking_register_post_type_coupon_arg', $args );

			register_post_type( 'hb_coupon', $args );
		}

		/**
		 * Custom coupon post type columns.
		 *
		 * @since 2.0
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function custom_coupon_columns( $columns ) {
			$columns['type']             = __( 'Type', 'wphb-coupon' );
			$columns['from']             = __( 'Validate From', 'wphb-coupon' );
			$columns['to']               = __( 'Validate To', 'wphb-coupon' );
			$columns['minimum_spend']    = __( 'Minimum spend', 'wphb-coupon' );
			$columns['maximum_spend']    = __( 'Maximum spend', 'wphb-coupon' );
			$columns['limit_per_coupon'] = __( 'Usage', 'wphb-coupon' );
			unset( $columns['date'] );

			return $columns;
		}

		/**
		 * Custom coupon post type column content.
		 *
		 * @since 2.0
		 *
		 * @param $column
		 */
		public function custom_coupon_columns_filter( $column ) {
			global $post;
			switch ( $column ) {
				case 'type':
					switch ( get_post_meta( $post->ID, '_hb_coupon_discount_type', true ) ) {
						case 'fixed_cart':
							_e( 'Fixed cart', 'wphb-coupon' );
							break;
						case 'percent_cart':
							_e( 'Percent cart', 'wphb-coupon' );
							break;
					}
					break;
				case 'from':
				case 'to':
					if ( $from = get_post_meta( $post->ID, '_hb_coupon_date_' . $column, true ) ) {
						echo date_i18n( hb_get_date_format(), $from );
					} else {
						echo '-';
					}
					break;
				case 'minimum_spend':
				case 'maximum_spend':
					if ( $value = get_post_meta( $post->ID, '_hb_' . $column, true ) ) {
						if ( get_post_meta( $post->ID, '_hb_coupon_discount_type', true ) == 'fixed_cart' ) {
							echo hb_format_price( $value );
						} else {
							echo sprintf( '%s', $value . '%' );
						}
					} else {
						echo '-';
					}
					break;
				case 'limit_per_coupon':
					if ( $value = get_post_meta( $post->ID, '_hb_' . $column, true ) ) {
						echo sprintf( '%s', $value );
					} else {
						echo '-';
					}
			}
		}
	}
}

new WPHB_Coupon_Post_Types();