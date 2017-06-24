<?php

/**
 * WP Hotel Booking logger class.
 *
 * @class       WPHB_Logger
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Logger' ) ) {

	/**
	 * Class WPHB_Logger.
	 *
	 * @since 2.0
	 */
	class WPHB_Logger {

		/**
		 * @var array Stores open file _handles.
		 *
		 * @access private
		 */
		private $_handles;

		/**
		 * WPHB_Logger constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			$this->_handles = array();
		}

		/**
		 * WPHB_Logger destruct.
		 *
		 * @since 2.0
		 */
		public function __destruct() {
			foreach ( $this->_handles as $handle ) {
				@fclose( $handle );
			}
		}

		/**
		 * Open log file for writing.
		 *
		 * @since 2.0
		 *
		 * @access private
		 *
		 * @param mixed $handle
		 *
		 * @return bool success
		 */
		private function open( $handle ) {
			if ( isset( $this->_handles[ $handle ] ) ) {
				return true;
			}

			if ( $this->_handles[ $handle ] = @fopen( hb_get_log_file_path( $handle ), 'a' ) ) {
				return true;
			}

			return false;
		}


		/**
		 * Add a log entry to chosen file.
		 *
		 * @since 2.0
		 *
		 * @param string $handle
		 * @param string $message
		 */
		public function add( $handle, $message ) {
			if ( $this->open( $handle ) && is_resource( $this->_handles[ $handle ] ) ) {
				$time = date_i18n( 'm-d-Y @ H:i:s -' ); // Grab Time
				@fwrite( $this->_handles[ $handle ], $time . " " . $message . "\n" );
			}

			do_action( 'hotel_booking_log_add', $handle, $message );
		}


		/**
		 * Clear entries from chosen file.
		 *
		 * @since 2.0
		 *
		 * @param mixed $handle
		 */
		public function clear( $handle ) {
			if ( $this->open( $handle ) && is_resource( $this->_handles[ $handle ] ) ) {
				@ftruncate( $this->_handles[ $handle ], 0 );
			}

			do_action( 'hotel_booking_log_clear', $handle );
		}

	}

}
