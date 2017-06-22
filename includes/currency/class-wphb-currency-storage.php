<?php

/**
 * WP Hotel Booking currency storage class.
 *
 * @class       WPHB_Currency_Storage
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Currency_Storage' ) ) {

	/**
	 * Class WPHB_Currency_Storage.
	 *
	 * @since 2.0
	 */
	class WPHB_Currency_Storage {

		/**
		 * @var string
		 */
		protected $_rate = null;

		/**
		 * @var string
		 */
		protected $_storage_name = null;

		/**
		 * @var null
		 */
		static $_instance = null;

		/**
		 * WPHB_Currency_Storage constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->_storage_name = 'tp_hb_sw_currency';
			add_action( 'hotel_booking_currencies_switcher', array( $this, 'get_currency' ) );
			add_filter( 'hotel_booking_payment_currency_rate', array( $this, 'get_rate' ), 10, 3 );
		}

		/**
		 * Get currency.
		 *
		 * @since 2.0
		 *
		 * @param $currency
		 *
		 * @return mixed
		 */
		public function get_currency( $currency ) {
			return $currency;
		}

		/**
		 * Curl get.
		 *
		 * @since 2.0
		 *
		 * @param $url
		 *
		 * @return bool|mixed|string
		 */
		public function curl_get( $url ) {
			if ( function_exists( 'curl_init' ) ) {
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_URL, $url );
				@curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

				$data = curl_exec( $ch );
				curl_close( $ch );

				return $data;
			} else {
				return file_get_contents( $url );
			}
		}

		/**
		 * Get rate.
		 *
		 * @since 2.0
		 *
		 * @param string $from
		 * @param string $to
		 *
		 * @return float|mixed|string
		 */
		public function get_rate( $from = 'USD', $to = 'USD' ) {

			if ( $from === $to ) {
				return 1;
			}

			$name = $this->_rate . '_' . $from . '_' . $to;
			$rate = get_transient( $name );

			if ( ! $rate ) {
				$settings = hb_settings();
				$type     = $settings->get( 'aggregator', 'yahoo' );
				switch ( $type ) {
					case 'yahoo':
						$query         = "SELECT * FROM yahoo.finance.xchange WHERE pair IN ($from. $to)";
						$yql_query_url = 'http://query.yahooapis.com/v1/public/yql?q=' . urlencode( $query ) . '&format=json';

						$res    = $this->curl_get( $yql_query_url );
						$result = json_decode( $res, true );
						$rate   = (float) $result['query']['results']['rate']['Rate'];

						break;
					case 'google':
						$amount        = urlencode( 1 );
						$from_Currency = urlencode( $from );
						$to_Currency   = urlencode( $to );

						$url    = "http://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";
						$result = $this->curl_get( $url );

						preg_match_all( '/<span class=bld(.*?)</span>/s', $result, $matches );
						if ( isset( $matches[1][0] ) ) {
							$rate = floatval( $matches[1][0] );
						} else {
							$rate = sprintf( __( "no data for %s", 'wp-hotel-booking' ), $to );
						}

						break;
					default:
						break;
				}
				set_transient( $name, $rate, 24 * HOUR_IN_SECONDS );
			}

			return $rate;
		}

		/**
		 * Currency gets value.
		 *
		 * @since 2.0
		 *
		 * @param null $name
		 * @param null $value
		 *
		 * @return mixed|null
		 */
		public function get( $name = null, $value = null ) {

			if ( ! $name ) {
				return $value;
			}

			$settings = hb_settings();
			$type     = $settings->get( 'currencies_storage' );

			switch ( $type ) {
				case 'session':
					if ( isset( $_SESSION[ $this->_storage_name ], $_SESSION[ $this->_storage_name ][ $name ] ) ) {
						$value = $_SESSION[ $this->_storage_name ][ $name ];
					}
					break;
				case 'transient':
					$storage = get_transient( $this->_storage_name );
					if ( $storage && isset( $storage[ $name ] ) ) {
						$value = $storage[ $name ];
					}
					break;
				default:
					if ( isset( $_COOKIE[ $this->_storage_name ], $_COOKIE[ $this->_storage_name ][ $name ] ) ) {
						$value = $_COOKIE[ $this->_storage_name ][ $name ];
					}
					break;
			}

			if ( ! $value ) {
				$value = $settings->get( 'currency', 'USD' );
			}

			return $value;
		}

		/**
		 * Currency sets value.
		 *
		 * @since 2.0
		 *
		 * @param null $name
		 * @param null $value
		 */
		public function set( $name = null, $value = null ) {

			if ( ! $name ) {
				return;
			}

			$settings = hb_settings();
			$type     = $settings->get( 'storage' );

			switch ( $type ) {
				case 'session':
					if ( ! isset( $_SESSION[ $this->_storage_name ] ) ) {
						$_SESSION[ $this->_storage_name ] = array();
					}
					$_SESSION[ $this->_storage_name ][ $name ] = $value;
					break;
				case 'transient':
					$storage = get_transient( $this->_storage_name );
					if ( false === $storage ) {
						$storage = array();
					}
					$storage[ $name ] = $value;
					set_transient( $this->_storage_name, $storage, 24 * HOUR_IN_SECONDS );
					break;
				default:
					setcookie( $this->_storage_name . '[' . $name . ']', $value, time() + 60 * 60 * 24, '/' );
					break;
			}
		}

		/**
		 * Instance class.
		 *
		 * @since 2.0
		 *
		 * @return null|WPHB_Currency_Storage
		 */
		public static function instance() {
			if ( empty( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

}

new WPHB_Currency_Storage();