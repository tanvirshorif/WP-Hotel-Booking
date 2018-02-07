<?php

/**
 * WP Hotel Booking WPML Support class.
 *
 * @class       WPHB_WPML_Support
 * @version     2.0
 * @package     WP_Hotel_Booking_WPML_Support/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_WPML_Support' ) ) {

	/**
	 * Class WPHB_WPML_Support.
	 *
	 * @since 2.0
	 */
	class WPHB_WPML_Support {

		/**
		 * sitepress object
		 *
		 * @var null
		 */
		public $sitepress = null;

		/**
		 * default language wpml setup
		 *
		 * @var null
		 */
		public $default_language_code = null;

		/**
		 * wpml current language code
		 *
		 * @var null
		 */
		public $current_language_code = null;

		/**
		 * WPHB_WPML_Support constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {

			global $sitepress;

			// sitepress object instance
			$this->sitepress = $sitepress;
			// default language setup
			$this->default_language_code = $this->sitepress->get_default_language();
			// current language
			$this->current_language_code = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : null;
			// filter dropdown rooms
			add_filter( 'hotel_booking_rooms_dropdown', array( $this, 'hotel_booking_rooms_dropdown' ) );

			add_action( 'init', array( $this, 'init' ), 999 );

			// disable change some room attributes in other post languages
			add_filter( 'hb_metabox_room_settings', array( $this, 'disable_change_room_attributes' ) );
			// disable change some coupon attributes in other post languages
			add_filter( 'hb_metabox_coupon_settings', array( $this, 'disable_change_coupon_attributes' ) );

			// capacity
			add_filter( 'manage_hb_room_capacity_custom_column', array( $this, 'capacity_order_attr' ), 20, 3 );

			// get page fillter
			add_filter( 'hb_get_pages', array( $this, 'hb_get_pages' ), 10, 1 );
			// get page id return page in current language
			add_filter( 'hb_get_page_id', array( $this, 'hb_get_page_id' ), 10, 1 );
			// set default page of sytem in other language page
			add_filter( 'the_content', array( $this, 'the_content' ), 10, 1 );
			// setup query search room
			add_filter( 'hb_search_query', array( $this, 'hb_search_query' ), 99, 2 );
			// get pricing plan
			add_filter( 'hb_room_get_pricing_plans', array( $this, 'hotel_booking_pricing_plans' ), 10, 2 );
			// cart generate transaction
			add_filter( 'hb_generate_transaction_object', array( $this, 'hb_generate_transaction_object' ), 10, 2 );
			add_filter( 'hb_generate_transaction_object_room', array(
				$this,
				'hb_generate_transaction_object_room'
			), 10, 2 );

			add_filter( 'get_max_capacity_of_rooms', array( $this, 'max_capacity_of_rooms' ) );

			add_filter( 'hotel_booking_query_search_parser', array( $this, 'parse_available_rooms' ) );

			// update translate room capacity
			add_action( 'icl_make_duplicate', array( $this, 'update_capacity_meta' ), 10, 4 );
		}

		/**
		 * Init.
		 *
		 * @since 2.0
		 */
		public function init() {
			$cart     = WPHB_Cart::instance();
			$sessions = $cart->sessions;
			if ( ! $sessions->session ) {
				return;
			}

			foreach ( $sessions->session as $cart_id => $param ) {
				if ( $id = $this->get_object_default_language( $param['product_id'], get_post_type( $param['product_id'] ), true, $this->current_language_code ) ) {
					$param['product_id'] = $id;
				}
				$qty = isset( $param['quantity'] ) ? absint( $param['quantity'] ) : 1;
				unset( $param['quantity'] );
				$cart->remove_cart_item( $cart_id );
				$cart->add_to_cart( $param['product_id'], $param, $qty );
			}
		}

		/**
		 * Get default post_id, capacity, room_type by origin post_ID || term_ID.
		 *
		 * @since 2.0
		 *
		 * @param null $id
		 * @param string $type
		 * @param bool $default
		 * @param bool $lang
		 *
		 * @return bool
		 */
		public function get_object_default_language( $id = null, $type = 'hb_room', $default = false, $lang = false ) {
			if ( ! $id ) {
				return false;
			}
			if ( ! $lang ) {
				$lang = $this->default_language_code;
			}

			return icl_object_id( $id, $type, $default, $lang );
		}

		/**
		 * Disable some attributes of room setting in other language post.
		 *
		 * @since 2.0
		 *
		 * @param $fields
		 *
		 * @return mixed
		 */
		public function disable_change_room_attributes( $fields ) {
			if ( $this->current_language_code === $this->default_language_code ) {
				return $fields;
			}
			foreach ( $fields as $k => $field ) {
				if ( in_array( $field['name'], array( 'num_of_rooms', 'room_capacity', 'max_child_per_room' ) ) ) {
					$fields[ $k ]['attr']['disabled'] = 'disabled';
				}
			}

			return $fields;
		}

		/**
		 * Update cap.
		 *
		 * @param $master_post_id
		 * @param $lang
		 * @param $post_array
		 * @param $id
		 */
		public function update_capacity_meta( $master_post_id, $lang, $post_array, $id ) {
			if ( ( get_post_type( $master_post_id ) == 'hb_room' ) && $cap_id = get_post_meta( $master_post_id, '_hb_room_capacity', true ) ) {
				$cap = $this->get_room_capacity( $cap_id, $this->default_language_code, $lang );
				update_post_meta( $id, '_hb_room_capacity', $cap );
				update_post_meta( $id, '_hb_room_origin_capacity', $this->get_room_capacity( $cap_id, $this->default_language_code ) );
			}
		}

		/**
		 * Get cap current lang from default lang.
		 *
		 * @param $default_cap
		 * @param $default_lang
		 * @param $current_lang
		 *
		 * @return null|string
		 */
		private function get_room_capacity( $default_cap, $default_lang, $current_lang = null ) {
			global $wpdb;

			if ( ! $current_lang ) {
				return $default_cap;
			}

			$current_cap = $wpdb->get_var(
				$wpdb->prepare( "
				SELECT cap_current.element_id FROM {$wpdb->prefix}icl_translations cap_current
			    INNER JOIN {$wpdb->prefix}icl_translations cap_default ON cap_default.trid = cap_current.trid
			    WHERE 
			    cap_default.element_id = %d AND cap_default.language_code = %s AND cap_current.language_code = %s AND cap_current.element_type = %s
			    ", (int) $default_cap, $default_lang, $current_lang, 'tax_hb_room_capacity' ) );

			return $current_cap ? $current_cap : $default_cap;
		}

		/**
		 * Disable some attributes of coupon setting in other language post.
		 *
		 * @since 2.0
		 *
		 * @param $fields
		 *
		 * @return mixed
		 */
		public function disable_change_coupon_attributes( $fields ) {
			if ( $this->current_language_code === $this->default_language_code ) {
				return $fields;
			}
			foreach ( $fields as $k => $field ) {
				if ( $field['name'] !== 'coupon_description' ) {
					$fields[ $k ]['attr']['disabled'] = 'disabled';
				}
			}

			return $fields;
		}

		/**
		 * Filter drop down rooms.
		 *
		 * @since 2.0
		 *
		 * @param $posts
		 *
		 * @return array
		 */
		public function hotel_booking_rooms_dropdown( $posts ) {

			$rooms = array();
			foreach ( $posts as $post ) {
				$id      = $post->ID;
				$room_id = $this->get_object_default_language( $id );
				if ( $room_id && ! isset( $rooms[ $room_id ] ) ) {
					$rooms[ $room_id ] = get_post( $room_id );
				}
			}

			return $rooms;
		}

		/**
		 * Capacity ordering.
		 *
		 * @since 2.0
		 *
		 * @param $content
		 * @param $column_name
		 * @param $term_id
		 *
		 * @return string
		 */
		public function capacity_order_attr( $content, $column_name, $term_id ) {
			if ( $this->current_language_code === $this->default_language_code ) {
				return $content;
			}
			$taxonomy = sanitize_text_field( $_REQUEST['taxonomy'] );
			$term_id  = $this->get_object_default_language( $term_id, 'hb_room_capacity' );
			$term     = get_term( $term_id, $taxonomy );
			switch ( $column_name ) {
				case 'ordering':
					$content = sprintf( '<input class="hb-number-field" type="number" name="%s_ordering[%d]" value="%d" size="3" disabled />', $taxonomy, $term_id, $term->term_group );
					break;
				case 'capacity':
					$capacity = get_term_meta( $term_id, 'hb_max_number_of_adults', true );
					$content  = '<input class="hb-number-field" type="number" name="' . $taxonomy . '_capacity[' . $term_id . ']" value="' . $capacity . '" size="2" disabled />';
					break;
				default:
					break;
			}

			return $content;
		}

		/**
		 * Get pages.
		 *
		 * @since 2.0
		 *
		 * @param $pages
		 *
		 * @return array|null|object
		 */
		public function hb_get_pages( $pages ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT DISTINCT page.ID, page.post_title FROM $wpdb->posts as page
				INNER JOIN {$wpdb->prefix}icl_translations as wpml_translation ON page.ID = wpml_translation.element_id AND wpml_translation.language_code = %s
				WHERE page.post_type = %s
					AND page.post_status = %s
			", $this->default_language_code, 'page', 'publish' );

			return $wpdb->get_results( $sql );
		}

		/**
		 * Get page id.
		 *
		 * @since 2.0
		 *
		 * @param $page_id
		 *
		 * @return int
		 */
		public function hb_get_page_id( $page_id ) {
			return $this->get_object_default_language( $page_id, 'page', true, $this->current_language_code );
		}

		/**
		 * The content setup shortcode atts check available.
		 *
		 * @since 2.0
		 *
		 * @param $content
		 *
		 * @return string
		 */
		public function the_content( $content ) {
			global $post;
			if ( is_page() && ( $this->get_object_default_language( $post->ID, 'page', true ) == hb_get_page_id( 'search' ) || has_shortcode( $content, 'hotel_booking' ) ) ) {

				// params search result
				$page       = hb_get_request( 'hotel-booking' );
				$start_date = hb_get_request( 'check_in_date' );
				$end_date   = hb_get_request( 'check_out_date' );
				$adults     = hb_get_request( 'adults' );
				$max_child  = hb_get_request( 'max_child' );

				$content = '[hotel_booking page="' . $page . '" check_in_date="' . $start_date . '" check_in_date="' . $end_date . '" adults="' . $adults . '" max_child="' . $max_child . '"]';
			}

			return $content;
		}

		/**
		 * Search room query.
		 *
		 * @since 2.0
		 *
		 * @param $query
		 * @param $args
		 *
		 * @return string
		 */
		public function hb_search_query( $query, $args ) {
			global $wpdb;
			/**
			 * blocked
			 * @var
			 */
			$blocked = $wpdb->prepare( "
				SELECT COALESCE( COUNT( blocked_time.meta_value ), 0 )
				FROM $wpdb->postmeta AS blocked_post
				INNER JOIN $wpdb->posts AS calendar ON calendar.ID = blocked_post.meta_value
				INNER JOIN $wpdb->postmeta AS blocked_time ON blocked_time.post_id = calendar.ID
				WHERE
					blocked_post.post_id = rooms.ID
					AND calendar.post_type = %s
					AND calendar.post_status = %s
					AND blocked_post.meta_key = %s
					AND blocked_time.meta_key = %s
					AND blocked_time.meta_value >= %d
					AND blocked_time.meta_value <= %d
			", 'hb_blocked', 'publish', 'hb_blocked_id', 'hb_blocked_time', $args['check_in'], $args['check_out'] );
			$not     = $wpdb->prepare( "
				(
					SELECT COALESCE( SUM( meta.meta_value ), 0 ) FROM {$wpdb->hotel_booking_order_itemmeta} AS meta
						LEFT JOIN {$wpdb->hotel_booking_order_items} AS order_item ON order_item.order_item_id = meta.hotel_booking_order_item_id AND meta.meta_key = %s
						LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS itemmeta ON order_item.order_item_id = itemmeta.hotel_booking_order_item_id AND itemmeta.meta_key = %s
						LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkin ON order_item.order_item_id = checkin.hotel_booking_order_item_id AND checkin.meta_key = %s
						LEFT JOIN {$wpdb->hotel_booking_order_itemmeta} AS checkout ON order_item.order_item_id = checkout.hotel_booking_order_item_id AND checkout.meta_key = %s
						LEFT JOIN {$wpdb->posts} AS booking ON booking.ID = order_item.order_id
					WHERE
							itemmeta.meta_value = rooms.ID
						AND (
								( checkin.meta_value >= %d AND checkin.meta_value <= %d )
							OR 	( checkout.meta_value >= %d AND checkout.meta_value <= %d )
							OR 	( checkin.meta_value <= %d AND checkout.meta_value > %d )
						)
						AND booking.post_type = %s
						AND booking.post_status IN ( %s, %s, %s )
				)
			", 'qty', 'product_id', 'check_in_date', 'check_out_date', $args['check_in'], $args['check_out'], $args['check_in'], $args['check_out'], $args['check_in'], $args['check_out'], 'hb_booking', 'hb-completed', 'hb-processing', 'hb-pending'
			);

			$query = $wpdb->prepare( "
				SELECT rooms.*, ( number.meta_value - {$not} ) AS available_rooms, ($blocked) AS blocked FROM $wpdb->posts AS rooms
					LEFT JOIN $wpdb->postmeta AS number ON rooms.ID = number.post_id AND number.meta_key = %s
					LEFT JOIN {$wpdb->postmeta} AS pm1 ON pm1.post_id = rooms.ID AND pm1.meta_key = %s
					LEFT JOIN {$wpdb->termmeta} AS term_cap ON term_cap.term_id = pm1.meta_value AND term_cap.meta_key = %s
					LEFT JOIN {$wpdb->postmeta} AS pm2 ON pm2.post_id = rooms.ID AND pm2.meta_key = %s
					LEFT JOIN {$wpdb->prefix}icl_translations AS wpml_translation ON rooms.ID = wpml_translation.element_id
				WHERE
					rooms.post_type = %s
					AND rooms.post_status = %s
					AND term_cap.meta_value >= %d
					AND pm2.meta_value >= %d
					AND wpml_translation.language_code = %s
				GROUP BY rooms.post_name
				HAVING ( available_rooms > 0 AND blocked = 0 )
				ORDER BY term_cap.meta_value ASC
			", '_hb_num_of_rooms', '_hb_room_origin_capacity', 'hb_max_number_of_adults', '_hb_max_child_per_room', 'hb_room', 'publish', $args['adults'], $args['child'], $this->current_language_code );

			return $query;
		}

		/**
		 * Parse number rooms available.
		 *
		 * @param $room
		 *
		 * @return mixed
		 */
		public function parse_available_rooms( $room ) {
			global $wpdb;
			$id = $room->ID;

			$check_in_date  = strtotime( $room->get_data( 'check_in_date' ) );
			$check_out_date = strtotime( $room->get_data( 'check_out_date' ) );

			$trid = $wpdb->get_results(
				$wpdb->prepare( "
				SELECT room_lang.element_id FROM {$wpdb->prefix}icl_translations room_lang
			    INNER JOIN {$wpdb->prefix}icl_translations room_sourse ON room_sourse.trid = room_lang.trid
			    WHERE 
			    room_sourse.element_id = %d AND room_lang.element_id != %d
			    ", $id, $id ),
				ARRAY_N );

			$except_ids = '';
			if ( $trid ) {
				foreach ( $trid as $id ) {
					$except_ids .= implode( ", ", $id );
				}
			}

			$booked = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT( DISTINCT product.hotel_booking_order_item_id ) FROM $wpdb->hotel_booking_order_itemmeta AS product
			LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS check_in ON product.hotel_booking_order_item_id = check_in.hotel_booking_order_item_id
			LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS check_out ON product.hotel_booking_order_item_id = check_out.hotel_booking_order_item_id
			LEFT JOIN $wpdb->hotel_booking_order_items AS items ON product.hotel_booking_order_item_id = items.order_item_id
			LEFT JOIN {$wpdb->posts} AS booking ON booking.ID = items.order_id
			WHERE 
			(product.meta_key = 'product_id' AND product.meta_value IN ('%d'))
			AND ( ( check_in.meta_key = 'check_in_date' AND check_out.meta_key = 'check_out_date' AND check_out.meta_value >= %d AND check_in.meta_value <= %d ) 
					OR ( check_in.meta_key = 'check_in_date' AND check_in.meta_key >= %d AND check_in.meta_key <= %d  )
			)
			AND booking.post_status IN ( %s, %s, %s )
		", $except_ids, $check_in_date, $check_in_date, $check_in_date, $check_out_date, 'hb-completed', 'hb-processing', 'hb-pending' ) );

			$room->post->available_rooms = $room->post->available_rooms - $booked;

			return $room;
		}

		/**
		 * Get pricing plans default room language.
		 *
		 * @since 2.0
		 *
		 * @param $plans
		 * @param $id
		 *
		 * @return mixed
		 */
		public function hotel_booking_pricing_plans( $plans, $id ) {
			remove_filter( 'hb_room_get_pricing_plans', array( $this, 'hotel_booking_pricing_plans' ), 10 );
			if ( ( $primary_room_id = $this->get_object_default_language( $id, 'hb_room' ) ) && $primary_room_id != $id ) {
				$plans = hb_room_get_pricing_plans( $primary_room_id );
			}
			add_filter( 'hb_room_get_pricing_plans', array( $this, 'hotel_booking_pricing_plans' ), 10, 2 );

			return $plans;
		}


		/**
		 * Generate transaction object.
		 *
		 * @since 2.0
		 *
		 * @param $transaction
		 *
		 * @return mixed
		 */
		public function hb_generate_transaction_object( $transaction ) {
			$transaction->booking_info['_hb_wpml_language'] = $this->current_language_code;

			return $transaction;
		}

		/**
		 * Cart generate booking item params.
		 *
		 * @since 2.0
		 *
		 * @param $params
		 * @param $product
		 *
		 * @return mixed
		 */
		public function hb_generate_transaction_object_room( $params, $product ) {
			$params['product_id'] = $this->get_object_default_language( $params['product_id'], $product->post->post_type );

			return $params;
		}


		/**
		 * Get maximum capacity of rooms.
		 *
		 * @since 2.0
		 *
		 * @param $max
		 *
		 * @return mixed
		 */
		public function max_capacity_of_rooms( $max ) {
			$terms = get_terms( 'hb_room_capacity', array( 'hide_empty' => false ) );
			if ( $terms ) {
				foreach ( $terms as $term ) {
					$default_term = $this->get_object_default_language( $term->term_id, 'hb_room_capacity', true );
					$cap          = get_term_meta( $default_term, 'hb_max_number_of_adults', true );
					// use term meta, since 1.1.2
					if ( ! $cap ) {
						$cap = get_option( "hb_taxonomy_capacity_{$default_term}" );
					}
					if ( intval( $cap ) > $max ) {
						$max = $cap;
					}
				}
			}

			return $max;
		}
	}
}

new WPHB_WPML_Support();
