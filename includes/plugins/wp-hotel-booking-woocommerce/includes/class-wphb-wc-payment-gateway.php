<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WPHB_Payment_Gateway_Paypal
 */
class WPHB_Payment_Gateway_Woocommerce extends WPHB_Payment_Gateway_Base {

	/**
	 * Construction
	 */
	function __construct() {
		parent::__construct();
		$this->_slug        = 'woo-payment';
		$this->_title       = __( 'Woocommerce', 'wp-hotel-booking' );
		$this->_description = __( 'Woocommerce payment gateways', 'wp-hotel-booking' );
	}

	/**
	 * Get payment method title
	 *
	 * @return mixed
	 */
	function payment_method_title() {
		return $this->_description;
	}

}

add_filter( 'hb_payment_gateways', 'hotel_booking_payment_woo' );
if ( ! function_exists( 'hotel_booking_payment_woo' ) ) {
	function hotel_booking_payment_woo( $payments ) {
		if ( array_key_exists( 'woo-payment', $payments ) ) {
			return $payments;
		}

		$payments['woo-payment'] = new WPHB_Payment_Gateway_Woocommerce();

		return $payments;
	}
}