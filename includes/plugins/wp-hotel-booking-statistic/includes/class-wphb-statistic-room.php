<?php

/**
 * WP Hotel Booking statistic by room class.
 *
 * @class       WPHB_Statistic_Room
 * @version     2.0
 * @package     WP_Hotel_Booking_Statistic/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Statistic_Room' ) ) {

	/**
	 * Class WPHB_Statistic_Room.
	 *
	 * @since 2.0
	 */
	class WPHB_Statistic_Room extends WPHB_Abstract_Statistic {

		/**
		 * @var string
		 */
		public $_title;

		/**
		 * report type
		 */
		public $_chart_type = 'room';

		/**
		 * @var array
		 */
		public $_rooms = array();

		/**
		 * input start check
		 */
		public $_start_in;

		/**
		 * input end check
		 */
		public $_end_in;

		/**
		 * group by month, day
		 */
		public $chart_groupby;

		/**
		 * @var array
		 */
		public $_axis_x = array();

		/**
		 * @var array
		 */
		public $_axis_y = array();

		/**
		 * @var
		 */
		public $_range_start;

		/**
		 * @var
		 */
		public $_range_end;

		/**
		 * @var null
		 */
		public $_range;

		/**
		 * @var null
		 */
		public $_query_results = null;

		/**
		 * data generate sidebar price
		 */
		public $_sidebar_date = array();

		/**
		 * @var array
		 */
		static $_instance = array();

		/**
		 * WPHB_Statistic_Room constructor.
		 *
		 * @since 2.0
		 *
		 * @param null $range
		 */
		public function __construct( $range = null ) {

			parent::__construct( $range );

			$this->_title = sprintf( __( 'Chart in %s to %s', 'wphb-statistic' ), $this->_start_in, $this->_end_in );
			$this->calculate_current_range( $this->_range );
		}

		/**
		 * Get all rooms.
		 *
		 * @return array|null|object
		 */
		public function get_rooms() {
			global $wpdb;
			$query = $wpdb->prepare( "
				(
					SELECT ID, post_title FROM {$wpdb->posts}
					WHERE
						`post_type` = %s
						AND `post_status` = %s
				)
			", 'hb_room', 'publish' );

			return $wpdb->get_results( $query );
		}

		/**
		 * Get all bookings start < completed  < end.
		 *
		 * @since 2.0
		 *
		 * @return array|null|object
		 */
		public function getOrdersItems() {

			global $wpdb;

			if ( $this->chart_groupby === 'day' ) {

				$query = $wpdb->prepare( "
					SELECT DATE( from_unixtime( check_in.meta_value ) ) AS checkindate, DATE( from_unixtime( check_out.meta_value ) ) as checkoutdate, product.meta_value AS room_ID, max_room.meta_key AS total
						FROM $wpdb->hotel_booking_order_items AS order_items
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS check_in ON check_in.hotel_booking_order_item_id = order_items.order_item_id AND check_in.meta_key = %s
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS check_out ON order_items.order_item_id = check_out.hotel_booking_order_item_id AND check_out.meta_key = %s
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS product ON order_items.order_item_id = product.hotel_booking_order_item_id AND product.meta_key = %s
						LEFT JOIN $wpdb->posts AS booking ON booking.ID = order_items.order_id
						LEFT JOIN $wpdb->posts AS room ON room.ID = product.meta_value
						LEFT JOIN $wpdb->postmeta AS max_room ON max_room.post_id = room.ID AND max_room.meta_key = %s
					WHERE
						booking.post_status = %s
						AND room.post_status = %s
						AND room.post_type = %s
						AND room.ID IN ( %s )
						HAVING ( checkindate <= %s AND checkoutdate >= %s )
							OR ( checkindate >= %s AND checkindate <= %s )
							OR ( checkoutdate > %s AND checkoutdate <= %s )
				", 'check_in_date', 'check_out_date', 'product_id', '_hb_num_of_rooms', 'hb-completed', 'publish', 'hb_room', implode( ',', $this->_rooms ), $this->_start_in, $this->_end_in, $this->_start_in, $this->_end_in, $this->_start_in, $this->_end_in
				);
			} else {

				$query = $wpdb->prepare( "
					SELECT from_unixtime( check_in.meta_value ) AS checkindate, from_unixtime( check_out.meta_value ) as checkoutdate, product.meta_value AS room_ID, max_room.meta_key AS total
						FROM $wpdb->hotel_booking_order_items AS order_items
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS check_in ON check_in.hotel_booking_order_item_id = order_items.order_item_id AND check_in.meta_key = %s
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS check_out ON order_items.order_item_id = check_out.hotel_booking_order_item_id AND check_out.meta_key = %s
						LEFT JOIN $wpdb->hotel_booking_order_itemmeta AS product ON order_items.order_item_id = product.hotel_booking_order_item_id AND product.meta_key = %s
						LEFT JOIN $wpdb->posts AS booking ON booking.ID = order_items.order_id
						LEFT JOIN $wpdb->posts AS room ON room.ID = product.meta_value
						LEFT JOIN $wpdb->postmeta AS max_room ON max_room.post_id = room.ID AND max_room.meta_key = %s
					WHERE
						booking.post_status = %s
						AND room.post_status = %s
						AND room.post_type = %s
						AND room.ID IN ( %s )
						AND ( check_in.meta_value <= %s AND check_out.meta_value <= %s )
							OR ( check_in.meta_value >= %s AND check_in.meta_value <= %s )
							OR ( check_out.meta_value > %s AND check_out.meta_value <= %s )
				", 'check_in_date', 'check_out_date', 'product_id', '_hb_num_of_rooms', 'hb-completed', 'publish', 'hb_room', implode( ',', $this->_rooms ), $this->_start_in, $this->_end_in, $this->_start_in, $this->_end_in, $this->_start_in, $this->_end_in
				);
			}

			$results = $wpdb->get_results( $query );

			return $results;
		}

		/**
		 * Get series.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		public function series() {

			if ( ! $this->_rooms ) {
				return false;
			}

			$transient_name = 'tp_hotel_booking_charts_' . $this->_chart_type . '_' . $this->chart_groupby . '_' . $this->_range . '_' . $this->_start_in . '_' . $this->_end_in;
			delete_transient( $transient_name );
			if ( false === ( $chart_results = get_transient( $transient_name ) ) ) {
				$chart_results = $this->js_data();
				set_transient( $transient_name, $chart_results, 12 * HOUR_IN_SECONDS );
			}

			return apply_filters( 'hotel_booking_charts', $chart_results );
		}

		/**
		 * Render label x for canvas charts.
		 *
		 * @since 2.0
		 *
		 * @return array|bool|object|stdClass
		 */
		public function js_data() {
			$results            = $this->getOrdersItems();
			$series             = array();
			$series['labels']   = array();
			$series['datasets'] = array();

			$ids = array();
			if ( ! $results ) {
				return $series;
			}

			foreach ( $results as $key => $value ) {
				if ( ! isset( $ids[ $value->room_ID ] ) ) {
					$ids[ $value->room_ID ] = $value->total;
				}
			}

			$label = array();
			foreach ( $ids as $id => $total ) {
				$range       = $this->_range_end - $this->_range_start;
				$cache       = $this->_start_in;
				$data_recode = array();
				for ( $i = 0; $i <= $range; $i ++ ) {
					$unavailable = 0;
					if ( $this->chart_groupby === 'day' ) {
						$current_time           = strtotime( $this->_start_in ) + 24 * 60 * 60 * $i;
						$label[ $current_time ] = date( 'M.d', $current_time );
					} else {
						$reg                    = $this->_range_start + $i;
						$cache                  = date( "Y-$reg-01", strtotime( $cache ) );
						$current_time           = strtotime( date( "Y-$reg-01", strtotime( $cache ) ) );
						$label[ $current_time ] = date( 'M.Y', $current_time );
					}

					foreach ( $results as $k => $v ) {

						if ( (int) $v->room_ID !== (int) $id ) {
							continue;
						}

						if ( $this->chart_groupby === 'day' ) {
							$_in  = strtotime( date( 'Y-m-d', strtotime( $v->checkindate ) ) );
							$_out = strtotime( date( 'Y-m-d', strtotime( $v->checkoutdate ) ) );

							if ( $current_time >= $_in && $current_time < $_out ) {
								$unavailable ++;
							}
						} else {
							$_in  = strtotime( date( 'Y-m-1', strtotime( $v->checkindate ) ) );
							$_out = strtotime( date( 'Y-m-1', strtotime( $v->checkoutdate ) ) );

							if ( $current_time >= $_in && $current_time <= $_out ) {
								$unavailable ++;
							}
						}
					}

					$data_recode[ $current_time ] = $unavailable;
				}

				ksort( $data_recode );
				// random color
				$color = hb_random_color();

				$data                       = new stdClass();
				$data->fillColor            = $color;
				$data->strokeColor          = $color;
				$data->pointColor           = $color;
				$data->pointStrokeColor     = "#fff";
				$data->pointHighlightFill   = "#fff";
				$data->pointHighlightStroke = $color;

				$data->data           = array_values( $data_recode );
				$series['datasets'][] = $data;
			}
			ksort( $label );
			$series['labels'] = array_values( $label );

			return $series;
		}

		/**
		 * For height chart js( license ).
		 *
		 * @since 2.0
		 *
		 * @return array|null
		 */
		public function parseData() {
			$results = $this->_query_results;
			$series  = array();
			$ids     = array();
			$prepare = $unavailable = array();
			foreach ( $results as $key => $value ) {
				if ( ! isset( $ids[ $value->room_ID ] ) ) {
					$ids[ $value->room_ID ] = $value->total;
				}
			}

			foreach ( $ids as $id => $total ) {
				if ( ! isset( $series[ $id ] ) ) {
					$prepare = array(
						'name'  => sprintf( __( '%s unavaiable', 'wphb-statistic' ), get_the_title( $id ) ),
						'data'  => array(),
						'stack' => $id
					);

					if ( $this->chart_groupby === 'day' ) {
						$unavailable = array(
							'name'  => sprintf( __( '%s avaiable', 'wphb-statistic' ), get_the_title( $id ) ),
							'data'  => array(),
							'stack' => $id
						);
					} else {
						$unavailable = array(
							'name'  => sprintf( __( '%s quantity of room', 'wphb-statistic' ), get_the_title( $id ) ),
							'data'  => array(),
							'stack' => $id
						);
					}
				}

				$range = $this->_range_end - $this->_range_start;
				$cache = $this->_start_in;
				for ( $i = 0; $i <= $range; $i ++ ) {
					$available = 0;
					if ( $this->chart_groupby === 'day' ) {
						$current_time = strtotime( $this->_start_in ) + 24 * 60 * 60 * $i;
					} else {
						$reg          = $this->_range_start + $i;
						$cache        = date( "Y-$reg-01", strtotime( $cache ) );
						$current_time = strtotime( date( "Y-$reg-01", strtotime( $cache ) ) );
					}

					foreach ( $results as $k => $v ) {

						if ( (int) $v->room_ID !== (int) $id ) {
							continue;
						}

						if ( $this->chart_groupby === 'day' ) {
							$_in  = strtotime( date( 'Y-m-d', $v->checkindate ) );
							$_out = strtotime( date( 'Y-m-d', $v->checkoutdate ) );

							if ( $current_time >= $_in && $current_time < $_out ) {
								$available ++;
							}
						} else {
							$_in  = strtotime( date( 'Y-m-1', $v->checkindate ) );
							$_out = strtotime( date( 'Y-m-1', $v->checkoutdate ) );

							if ( $current_time >= $_in && $current_time <= $_out ) {
								$available ++;
							}
						}
					}

					$prepare['data'][] = array(
						$current_time * 1000,
						$available
					);

					if ( $this->chart_groupby === 'day' ) {
						$unavailable['data'][] = array(
							$current_time * 1000,
							$total - $available
						);
					} else {
						$unavailable['data'][] = array(
							$current_time * 1000,
							(int) $total
						);
					}
				}

				$series[] = $prepare;
				$series[] = $unavailable;
			}

			return $series;
		}

		/**
		 * Export statistic to CSV.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		public function export_csv() {
			$this->_query_results = $this->getOrdersItems();
			if ( ! isset( $_POST ) ) {
				return false;
			}

			if ( ! isset( $_POST['tp-hotel-booking-report-export'] ) ||
			     ! wp_verify_nonce( sanitize_text_field( $_POST['tp-hotel-booking-report-export'] ), 'tp-hotel-booking-report-export' )
			) {
				return false;
			}

			if ( ! isset( $_POST['tab'] ) || sanitize_file_name( $_POST['tab'] ) !== $this->_chart_type ) {
				return false;
			}

			$inputs = $this->parseData();

			if ( ! $inputs ) {
				return false;
			}

			$rooms = array();
			foreach ( $inputs as $key => $input ) {
				if ( ! isset( $rooms[ $input['stack'] ] ) ) {
					$rooms[ $input['stack'] ] = array();
				}

				$rooms[ $input['stack'] ][] = $input;
			}

			$filename = 'tp_hotel_export_' . $this->_chart_type . '_' . $this->_start_in . '_to_' . $this->_end_in . '.csv';
			header( 'Content-Type: application/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			// create a file pointer connected to the output stream
			$output = fopen( 'php://output', 'w' );

			foreach ( $rooms as $id => $params ) {

				// output the column headings
				fputcsv( $output, array( sprintf( '%s', get_the_title( $id ) ) ) );

				$column = array(
					__( 'Date/Time', 'wphb-statistic' )
				);

				$available_data = false;
				$excerpt        = array();
				$time           = 1;
				if ( isset( $params[0] ) ) {
					$available = $params[0];

					$available_data = array(
						__( 'Unavailable', 'wphb-statistic' )
					);
					foreach ( $available['data'] as $key => $_available ) {
						if ( (int) $_available[1] === 0 ) {
							$excerpt[] = $key;
							continue;
						}
						if ( $this->chart_groupby === 'day' ) {
							if ( isset( $_available[0], $_available[1] ) ) {
								$time = $_available[0] / 1000;
							}

							$column[]         = date( 'Y-m-d', $time );
							$available_data[] = $_available[1];
						} else {
							if ( isset( $_available[0], $_available[1] ) ) {
								$time = $_available[0] / 1000;
							}

							$column[]         = date( 'F. Y', $time );
							$available_data[] = $_available[1];
						}
					}
				}

				if ( $available_data ) {
					// heading and avaiable
					fputcsv( $output, $column );
					fputcsv( $output, $available_data );
				}

				if ( isset( $params[1] ) ) {
					$unavailable = $params[1];

					if ( $this->chart_groupby === 'day' ) {
						$unavailable_data = array(
							__( 'Available', 'wphb-statistic' )
						);
					} else {
						$unavailable_data = array(
							__( 'Room Quantity', 'wphb-statistic' )
						);
					}
					foreach ( $unavailable['data'] as $key => $_available ) {
						if ( in_array( $key, $excerpt ) ) {
							continue;
						}

						if ( $this->chart_groupby === 'day' ) {
							if ( isset( $_available[0], $_available[1] ) ) {
								$time = $_available[0] / 1000;
							}

							$column[]           = date( 'Y-m-d', $time );
							$unavailable_data[] = $_available[1];
						} else {
							if ( isset( $_available[0], $_available[1] ) ) {
								$time = $_available[0] / 1000;
							}

							$column[]           = date( 'F. Y', $time );
							$unavailable_data[] = $_available[1];
						}
					}
					fputcsv( $output, $unavailable_data );
					fputcsv( $output, array() );
				}
			}

			fpassthru( $output );
			die();
		}

		/**
		 * Instance.
		 *
		 * @since 2.0
		 *
		 * @param null $range
		 *
		 * @return mixed|WPHB_Statistic_Room
		 */
		public static function instance( $range = null ) {
			if ( ! $range && ! isset( $_GET['range'] ) ) {
				$range = '7day';
			}

			if ( ! $range && isset( $_GET['range'] ) ) {
				$range = sanitize_text_field( $_GET['range'] );
			}

			if ( ! empty( self::$_instance[ $range ] ) ) {
				return self::$_instance[ $range ];
			}

			return self::$_instance[ $range ] = new self( $range );
		}

	}

}

if ( ! isset( $_REQUEST['tab'] ) || sanitize_text_field( $_REQUEST['tab'] ) === 'room' ) {
	$GLOBALS['hb_report'] = WPHB_Statistic_Room::instance();
}