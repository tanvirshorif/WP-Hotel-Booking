<?php

/**
 * WP Hotel Booking currencies class.
 *
 * @class       WPHB_Currencies
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Currencies' ) ) {

	/**
	 * Class WPHB_Currencies.
	 *
	 * @since 2.0
	 */
	class WPHB_Currencies {

		/**
		 * @var bool
		 */
		public $_is_multi = false;


		/**
		 * WPHB_Currencies constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_filter( 'hb_currency', array( $this, 'switch_currencies' ), 99 );
			add_filter( 'hotel_booking_price_switcher', array( $this, 'switch_price' ) );
			add_action( 'plugins_loaded', array( $this, 'set_currency' ) );
			add_action( 'qtranslate_init_language', array( $this, 'qtranslate_switcher' ) );
			add_filter( 'icl_current_language', array( $this, 'wpml_switcher' ) );
			add_filter( 'hotel_booking_checkout_booking_info', array( $this, 'generate_booking_info' ) );
		}

		/**
		 * Switch currencies.
		 *
		 * @since 2.0
		 *
		 * @param $currency
		 *
		 * @return mixed
		 */
		public function switch_currencies( $currency ) {
			$settings = hb_settings();
			$storage  = WPHB_Currency_Storage::instance();

			if ( $this->_is_multi = $settings->get( 'currencies_multiple_allowed', false ) ) {

				do_action( 'hb_before_currencies_switcher' );
				$currency = apply_filters( 'hb_currencies_switcher', $storage->get( 'currency' ) );
				do_action( 'hb_after_currencies_switcher' );

			}

			return $currency;
		}

		/**
		 * Switch price.
		 *
		 * @since 2.0
		 *
		 * @param $price
		 *
		 * @return float
		 */
		public function switch_price( $price ) {

			$settings = hb_settings();
			$storage  = HB_SW_Curreny_Storage::instance();

			$default_currency = $settings->get( 'currency', 'USD' );
			$current_currency = $storage->get( 'currency' );

			$rate = $storage->get_rate( $default_currency, $current_currency );

			return (float) $price * $rate;
		}

		/**
		 * Set currency.
		 *
		 * @since 2.0
		 */
		public function set_currency() {
			$storage = WPHB_Currency_Storage::instance();

			if ( isset( $_GET['currency'] ) && $_GET['currency'] ) {
				$storage->set( 'currency', sanitize_text_field( $_GET['currency'] ) );
			}
		}

		/**
		 * qTranslate switcher.
		 *
		 * @since 2.0
		 *
		 * @param $params
		 */
		public function qtranslate_switcher( $params ) {
			if ( ! isset( $params['language'] ) ) {
				return;
			}

			$currency_countries = hb_currency_countries();

			$lang = strtoupper( $params['language'] );
			if ( array_key_exists( $lang, $currency_countries ) ) {
				$currency = strtoupper( $currency_countries[ $lang ] );
				if ( $currency ) {
					$storage = WPHB_Currency_Storage::instance();
					$storage->set( 'currency', $currency );
				}
			}
		}

		/**
		 * WPMP switcher.
		 *
		 * @since 2.0
		 *
		 * @param $lag
		 *
		 * @return mixed
		 */
		public function wpml_switcher( $lag ) {
			$currency_countries = hb_currency_countries();
			$country            = strtoupper( $lag );
			if ( array_key_exists( $country, $currency_countries ) ) {
				$currency = strtoupper( $currency_countries[ $country ] );
				if ( $currency ) {
					$storage = WPHB_Currency_Storage::instance();
					$storage->set( 'currency', $currency );
				}
			}

			return $lag;
		}

		/**
		 * Update currency booking info.
		 *
		 * @since 2.0
		 *
		 * @param $booking_info
		 *
		 * @return mixed
		 */
		public function generate_booking_info( $booking_info ) {
			$settings = hb_settings();

			$default_currency = $settings->get( 'currency', 'USD' );
			$payment_currency = hb_get_currency();
			// booking meta data
			$booking_info['_hb_payment_currency']      = apply_filters( 'hotel_booking_payment_current_currency', $payment_currency );
			$booking_info['_hb_payment_currency_rate'] = (float) apply_filters( 'hotel_booking_payment_currency_rate', $default_currency, $payment_currency );

			return $booking_info;
		}

	}

}

new WPHB_Currencies();
