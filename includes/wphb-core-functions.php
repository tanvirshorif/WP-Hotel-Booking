<?php

/**
 * WP Hotel Booking global core functions.
 *
 * @version     2.0
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Functions
 * @category    Core Functions
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wphb_booking_status_description' ) ) {
	/**
	 * Get booking status description.
	 *
	 * @return array|mixed
	 */
	function wphb_booking_status_description() {
		$descriptions = apply_filters( 'hb_booking_status_description', array(
			'hb-pending'    => __( 'Booking received in case user buy a course but does not finalise the booking.', 'wp-hotel-booking' ),
			'hb-processing' => __( 'Payment received and the booking is awaiting fulfillment.', 'wp-hotel-booking' ),
			'hb-completed'  => __( 'Booking fulfilled and complete.', 'wp-hotel-booking' ),
			'hb-cancelled'  => __( 'The booking is cancelled by an admin or the customer.', 'wp-hotel-booking' )
		) );

		return is_array( $descriptions ) ? $descriptions : array();
	}
}

if ( ! function_exists( 'hotel_booking_set_table_name' ) ) {
	/**
	 * Set tables name.
	 */
	function hotel_booking_set_table_name() {
		global $wpdb;
		$order_item          = 'hotel_booking_order_items';
		$order_itemmeta      = 'hotel_booking_order_itemmeta';
		$hotel_booking_plans = 'hotel_booking_plans';

		$wpdb->hotel_booking_order_items    = $wpdb->prefix . $order_item;
		$wpdb->hotel_booking_order_itemmeta = $wpdb->prefix . $order_itemmeta;
		$wpdb->hotel_booking_plans          = $wpdb->prefix . $hotel_booking_plans;

		$wpdb->tables[] = 'hotel_booking_order_items';
		$wpdb->tables[] = 'hotel_booking_order_itemmeta';
		$wpdb->tables[] = 'hotel_booking_plans';
	}
}

if ( ! function_exists( 'wphb_get_room_available' ) ) {
	/**
	 * Get quantity room available.
	 *
	 * @param null $room_id
	 * @param array $args
	 *
	 * @return mixed|WP_Error
	 */
	function wphb_get_room_available( $room_id = null, $args = array() ) {
		// get by curd
		return WPHB_Room_CURD::get_room_available( $room_id, $args );
	}
}

if ( ! function_exists( 'hotel_booking_get_product_class' ) ) {
	/**
	 * Product class process.
	 *
	 * @param null $product_id
	 * @param array $params
	 *
	 * @return mixed
	 */
	function hotel_booking_get_product_class( $product_id = null, $params = array() ) {

		$post_type = get_post_type( $product_id );

		$product = 'WPHB_Product_' . implode( '_', array_map( 'ucfirst', explode( '_', $post_type ) ) );
		if ( ! class_exists( $product ) ) {
			$product = 'WPHB_Room';
		}

		$product = apply_filters( 'hotel_booking_cart_product_class_name', $product, $product_id );
		$product = new $product( $product_id, $params );

		return apply_filters( 'hotel_booking_get_product_class', $product, $product_id, $params );
	}
}

if ( ! function_exists( 'hb_create_page' ) ) {
	/**
	 * @param $slug
	 * @param string $option
	 * @param string $page_title
	 * @param string $page_content
	 * @param int $post_parent
	 *
	 * @return int|mixed|null|string|WP_Error
	 */
	function hb_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;

		$option_value = get_option( $option );

		if ( $option_value > 0 ) {
			$page_object = get_post( $option_value );

			if ( $page_object && 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array(
					'pending',
					'trash',
					'future',
					'auto-draft'
				) )
			) {
				// Valid page is already in place
				return $page_object->ID;
			}
		}

		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode)
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
		}

		$valid_page_found = apply_filters( 'hotel_booking_create_page_id', $valid_page_found, $slug, $page_content );

		if ( $valid_page_found ) {
			if ( $option ) {
				update_option( $option, $valid_page_found );
			}

			return $valid_page_found;
		}

		// Search for a matching valid trashed page
		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode)
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
		}

		if ( $trashed_page_found ) {
			$page_id   = $trashed_page_found;
			$page_data = array(
				'ID'          => $page_id,
				'post_status' => 'publish',
			);
			wp_update_post( $page_data );
		} else {
			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => $slug,
				'post_title'     => $page_title,
				'post_content'   => $page_content,
				'post_parent'    => $post_parent,
				'comment_status' => 'closed'
			);
			$page_id   = wp_insert_post( $page_data );
		}

		if ( $option ) {
			update_option( $option, $page_id );
		}

		return $page_id;
	}
}

if ( ! function_exists( 'hb_notice_remove_hotel_booking' ) ) {
	/**
	 * Show notice required remove tp hotel booking plugin and add-ons.
	 */
	function hb_notice_remove_hotel_booking() { ?>
        <div class="notice notice-error hb-dismiss-notice is-dismissible">
            <p>
				<?php echo __( wp_kses( '<strong>WP Hotel Booking</strong> plugin version ' . WPHB_VERSION . ' is an upgrade of <strong>TP Hotel Booking</strong> plugin. Please deactivate and delete <strong>TP Hotel Booking/TP Hotel Booking add-ons</strong> and replace by <strong>WP Hotel Booking/WP Hotel Booking add-ons</strong>.', array( 'strong' => array() ) ), 'wp-hotel-booking' ); ?>
            </p>
        </div>
	<?php }
}

if ( ! function_exists( 'hotel_booking_widget_init' ) ) {
	/**
	 * Register widget.
	 */
	function hotel_booking_widget_init() {
		register_widget( 'HB_Widget_Search' );
		register_widget( 'HB_Widget_Room_Carousel' );
		register_widget( 'HB_Widget_Best_Reviews' );
		register_widget( 'HB_Widget_Lastest_Reviews' );
		register_widget( 'HB_Widget_Mini_Cart' );
	}
}

if ( ! function_exists( 'hb_currency_countries' ) ) {
	/**
	 * Get country code, equivalent currency code
	 *
	 * @return array|mixed
	 */
	function hb_currency_countries() {
		$country_currency = apply_filters( 'hb_currency_countries', array(
			"US" => "USD",
			"EN" => "EUR",
			"BE" => "EUR",
			"ES" => "EUR",
			"LU" => "EUR",
			"PT" => "EUR",
			"DE" => "EUR",
			"FR" => "EUR",
			"MT" => "EUR",
			"SI" => "EUR",
			"IE" => "EUR",
			"IT" => "EUR",
			"NL" => "EUR",
			"SK" => "EUR",
			"GR" => "EUR",
			"CY" => "EUR",
			"AT" => "EUR",
			"FI" => "EUR",
			"JP" => "JPY",
			"BG" => "BGN",
			"CZ" => "CZK",
			"DK" => "DKK",
			"EE" => "EEK",
			"GB" => "GBP",
			"HU" => "HUF",
			"LT" => "LTL",
			"LV" => "LVL",
			"PL" => "PLN",
			"RO" => "RON",
			"SE" => "SEK",
			"CH" => "CHF",
			"NO" => "NOK",
			"HR" => "HRK",
			"RU" => "RUB",
			"TR" => "TRY",
			"AU" => "AUD",
			"BR" => "BRL",
			"CA" => "CAD",
			"CN" => "CNY",
			"HK" => "HKD",
			"ID" => "IDR",
			"IN" => "INR",
			"KR" => "KRW",
			"MX" => "MXN",
			"MY" => "MYR",
			"NZ" => "NZD",
			"PH" => "PHP",
			"SG" => "SGD",
			"TH" => "THB",
			"ZA" => "ZAR",
			"VI" => "VND"
		) );

		return is_array( $country_currency ) ? $country_currency : array();
	}
}

if ( ! function_exists( 'is_hb_cart' ) ) {
	/**
	 * @return bool
	 */
	function is_hb_cart() {
		return ( is_page( hb_get_page_id( 'cart' ) ) || hb_get_request( 'hotel-booking' ) === 'cart' );
	}
}

if ( ! function_exists( 'is_hb_checkout' ) ) {
	/**
	 * @return bool
	 */
	function is_hb_checkout() {
		return ( is_page( hb_get_page_id( 'checkout' ) ) || hb_get_request( 'hotel-booking' ) === 'checkout' );
	}
}

if ( ! function_exists( 'is_hb_thank_you' ) ) {
	/**
	 * @return bool
	 */
	function is_hb_thank_you() {
		return ( is_page( hb_get_page_id( 'thankyou' ) ) || hb_get_request( 'hotel-booking' ) === 'thankyou' );
	}
}

if ( ! function_exists( 'hb_extra_types' ) ) {
	/**
	 * @return array|mixed
	 */
	function hb_extra_types() {
		$types = apply_filters( 'hb_extra_type', array(
				'trip'   => __( 'Trip', 'wp-hotel-booking' ),
				'number' => __( 'Number', 'wp-hotel-booking' )
			)
		);

		return is_array( $types ) ? $types : array();
	}
}

if ( ! function_exists( 'hb_room_extra_options' ) ) {
	/**
	 * Get extra package to select when admin create room.
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	function hb_room_extra_options() {

		$extras = WPHB_Extra_CURD::get_extra();

		$hb_extra = array();
		if ( is_array( $extras ) ) {
			foreach ( $extras as $extra ) {
				$hb_extra[ $extra->ID ] = $extra->post_title;
			}
		}

		return $hb_extra;
	}
}

if ( ! function_exists( 'hb_get_return_url' ) ) {
	/**
	 * Get check out return URL.
	 *
	 * @return mixed
	 */
	function hb_get_return_url() {
		$url = hb_get_checkout_url();

		return apply_filters( 'hb_return_url', $url );
	}
}

if ( ! function_exists( 'hb_get_user' ) ) {
	/**
	 * Get user.
	 *
	 * @param null $user
	 *
	 * @return WPHB_User
	 */
	function hb_get_user( $user = null ) {
		return WPHB_User::get_user( $user );
	}
}

if ( ! function_exists( 'hb_get_current_user' ) ) {
	/**
	 * Get current user data.
	 *
	 * @return WPHB_User
	 */
	function hb_get_current_user() {
		global $hb_curent_user;

		if ( ! $hb_curent_user ) {
			$hb_curent_user = WPHB_User::get_current_user();
		}

		return $hb_curent_user;
	}
}

if ( ! function_exists( 'hb_setup_page_content' ) ) {
	/**
	 * @param $content
	 *
	 * @return string
	 */
	function hb_setup_page_content( $content ) {
		global $post;

		$page_id = $post->ID;
		if ( ! $page_id ) {
			return $content;
		}

		if ( hb_get_page_id( 'cart' ) == $page_id ) {
			$content = '[' . apply_filters( 'hotel_booking_cart_shortcode_tag', 'hotel_booking_cart' ) . ']';
		} else if ( hb_get_page_id( 'checkout' ) == $page_id ) {
			$content = '[' . apply_filters( 'hotel_booking_checkout_shortcode_tag', 'hotel_booking_checkout' ) . ']';
		} else if ( hb_get_page_id( 'search' ) == $page_id ) {
			$content = '[' . apply_filters( 'hotel_booking_search_shortcode_tag', 'hotel_booking' ) . ']';
		} else if ( hb_get_page_id( 'account' ) == $page_id ) {
			$content = '[' . apply_filters( 'hotel_booking_account_shortcode_tag', 'hotel_booking_account' ) . ']';
		} else if ( hb_get_page_id( 'thankyou' ) == $page_id ) {
			$content = '[' . apply_filters( 'hotel_booking_thankyou_shortcode_tag', 'hotel_booking_thankyou' ) . ']';
		}

		return do_shortcode( $content );
	}
}

if ( ! function_exists( 'hb_booking_detail_update_meta_box' ) ) {
	/**
     * Update booking.
     *
	 * @param $key
	 * @param $value
	 * @param $post_id
	 */
	function hb_booking_detail_update_meta_box( $key, $value, $post_id ) {
		if ( ! ( get_post_type( $post_id ) == 'hb_booking' && $key == '_hb_booking_status' ) ) {
			return;
		}

		$status = sanitize_text_field( $value );
		remove_action( 'save_post', array( 'WPHB_Metabox_Booking_Actions', 'update' ) );

		$booking = WPHB_Booking::instance( $post_id );
		$booking->update_status( $status );

		add_action( 'save_post', array( 'WPHB_Metabox_Booking_Actions', 'update' ) );
	}
}

if ( ! function_exists( 'hb_update_meta_box_gallery' ) ) {
	/**
     * Update room gallery meta box.
     *
	 * @param $post_id
	 */
	function hb_update_meta_box_gallery( $post_id ) {
		if ( get_post_type() !== 'hb_room' ) {
			return;
		}

		if ( ! $_POST['_hb_gallery'] || empty( $_POST['_hb_gallery'] ) ) {
			update_post_meta( $post_id, '_hb_gallery', array() );
		}
	}
}