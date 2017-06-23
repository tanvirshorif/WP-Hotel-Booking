<?php

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !defined( 'WPHB_PLUGIN_PATH' ) )
	return;

if ( !defined( 'TP_HB_EXTRA' ) )
	define( 'TP_HB_EXTRA', dirname( __FILE__ ) );

if ( !defined( 'TP_HB_EXTRA_URI' ) )
	define( 'TP_HB_EXTRA_URI', WPHB_PLUGIN_URL . '/includes/plugins/wp-hotel-booking-extra' );

if ( !defined( 'TP_HB_EXTRA_INC' ) )
	define( 'TP_HB_EXTRA_INC', TP_HB_EXTRA . '/inc' );

if ( !defined( 'TP_HB_EXTRA_TPL' ) )
	define( 'TP_HB_EXTRA_TPL', TP_HB_EXTRA . '/templates' );

if ( !defined( 'TP_HB_OPTION_NAME' ) )
	define( 'TP_HB_OPTION_NAME', 'tp_hb_extra_room' );

class WPHB_Extra_Factory {

	static $_self = null;

	function __construct() {

		$this->init();

		add_filter( 'hotel_booking_get_product_class', array( $this, 'product_class' ), 10, 3 );
	}


	/**
	 * initialize addon plugin
	 * @return null
	 */
	protected function init() {

		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra.php' );
		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra-settings.php' );
		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra-post-type.php' );
		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra-room.php' );
		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra-cart.php' );
		$this->_include( TP_HB_EXTRA_INC . '/classes/class-hb-extra-room.php' );
	}

	/**
	 * _include function
	 *
	 * @param  $file as @string or @array
	 *
	 * @return null
	 */
	public function _include( $file ) {
		if ( is_array( $file ) ) {
			foreach ( $file as $key => $f ) {
				if ( file_exists( $f ) )
					require_once $f;
				else if ( file_exists( untrailingslashit( TP_HB_EXTRA ) . '/' . $f ) )
					require_once untrailingslashit( TP_HB_EXTRA ) . '/' . $f;
			}
		} else {
			if ( file_exists( $file ) )
				require_once $file;
			else if ( file_exists( untrailingslashit( TP_HB_EXTRA ) . '/' . $file ) )
				require_once untrailingslashit( TP_HB_EXTRA ) . '/' . $file;
		}
	}

	public function product_class( $product = null, $product_id = null, $params = array() ) {
		if ( !$product_id || get_post_type( $product_id ) !== 'hb_extra_room' ) {
			return $product;
		}
		$parent_quantity = 1;
		if ( isset( $params['order_item_id'] ) ) {
			$parent_quantity = hb_get_order_item_meta( hb_get_parent_order_item( $params['order_item_id'] ), 'quantity', true );
		} else if ( !is_admin() && isset( $params['parent_id'] ) && WP_Hotel_Booking::instance()->cart ) {
			$parent = WP_Hotel_Booking::instance()->cart->get_cart_item( $params['parent_id'] );
			if ( $parent ) {
				$parent_quantity = $parent->quantity;
			}
		}

		return new WPHB_Extra_Package( $product_id, array(
			'check_in_date'  => isset( $params['check_in_date'] ) ? $params['check_in_date'] : '',
			'check_out_date' => isset( $params['check_out_date'] ) ? $params['check_out_date'] : '',
			'room_quantity'  => $parent_quantity,
			'quantity'       => isset( $params['quantity'] ) ? $params['quantity'] : 1
		) );
	}


	static function instance() {
		if ( self::$_self ) {
			return self::$_self;
		}

		return new self();
	}

}

new WPHB_Extra_Factory();
