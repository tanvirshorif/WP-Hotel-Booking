<?php

/**
 * WP Hotel Booking statistic by price class.
 *
 * @class       WPHB_Statistic_Price
 * @version     2.0
 * @package     WP_Hotel_Booking_Statistic/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Statistic_Price' ) ) {

	/**
	 * Class WPHB_Statistic_Price.
	 *
	 * @since 2.0
	 */
	class WPHB_Statistic_Price extends WPHB_Abstract_Statistic {

		/**
		 * @var string
		 */
		public $_title;

		/**
		 * report type
		 */
		public $_chart_type = 'price';

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
		 * WPHB_Statistic_Price constructor.
		 *
		 * @since 2.0
		 *
		 * @param null $range
		 */
		public function __construct( $range = null ) {

			parent::__construct( $range );

			$this->_title = sprintf( __( 'Chart in %s to %s', 'wphb-statistic' ), $this->_start_in, $this->_end_in );
			$this->calculate_current_range( $this->_range );

			add_filter( 'hotel_booking_sidebar_price_info', array( $this, 'total_earn' ) );

			// print scripts statistic data
			add_action( 'wp_print_scripts', array( $this, 'print_scripts' ) );
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
                    SELECT ( SUM( meta.meta_value ) - COALESCE( SUM( coupon.meta_value ), 0 ) ) AS total, DATE( pm.meta_value ) AS completed_date FROM $wpdb->hotel_booking_order_items AS booking
	                        INNER JOIN $wpdb->posts AS post ON booking.order_id = post.ID AND post.post_status = %s AND post.post_type = %s
	                        INNER JOIN $wpdb->hotel_booking_order_itemmeta AS meta ON meta.hotel_booking_order_item_id = booking.order_item_id AND meta.meta_key = %s
	                        INNER JOIN $wpdb->postmeta AS pm ON pm.post_id = post.ID AND pm.meta_key = %s
	                        LEFT JOIN $wpdb->postmeta AS coupon ON coupon.post_id = booking.order_id AND coupon.meta_key = %s
	                    WHERE
	                        booking.order_item_type IN ( %s, %s )
	                        AND DATE( pm.meta_value ) >= %s AND DATE( pm.meta_value ) <= %s
                ", 'hb-completed', 'hb_booking', 'subtotal', '_hb_booking_payment_completed', '_hb_coupon_value', 'line_item', 'sub_item', $this->_start_in, $this->_end_in );
			} else {
				$query = $wpdb->prepare( "
                    SELECT ( SUM( meta.meta_value ) - COALESCE( SUM( coupon.meta_value ), 0 ) ) AS total, DATE( pm.meta_value ) AS completed_date, MONTH(pm.meta_value) AS completed_month
                    FROM $wpdb->hotel_booking_order_items AS booking
	                        INNER JOIN $wpdb->posts AS post ON booking.order_id = post.ID AND post.post_status = %s AND post.post_type = %s
	                        INNER JOIN $wpdb->hotel_booking_order_itemmeta AS meta ON meta.hotel_booking_order_item_id = booking.order_item_id AND meta.meta_key = %s
	                        INNER JOIN $wpdb->postmeta AS pm ON pm.post_id = post.ID AND pm.meta_key = %s
	                        LEFT JOIN $wpdb->postmeta AS coupon ON coupon.post_id = post.ID AND coupon.meta_key = %s
	                    WHERE
	                        booking.order_item_type IN ( %s, %s )
	                        AND pm.meta_value >= %s AND pm.meta_value <= %s
                ", 'hb-completed', 'hb_booking', 'subtotal', '_hb_booking_payment_completed', '_hb_coupon_value', 'line_item', 'sub_item', $this->_start_in, $this->_end_in );
			}

			return $wpdb->get_results( $query );
		}

		/**
		 * Get series.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		public function series() {

			$transient_name = 'tp_hotel_booking_charts_' . $this->_chart_type . '_' . $this->chart_groupby . '_' . $this->_range . '_' . $this->_start_in . '_' . $this->_end_in;

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
			$results = $this->getOrdersItems();
			if ( ! $results ) {
				return false;
			}

			$label    = array();
			$excerpts = array();
			$datasets = array();
			foreach ( $results as $key => $item ) {
				if ( $this->chart_groupby === 'day' ) {
					$excerpts[ (int) date( 'z', strtotime( $item->completed_date ) ) ] = $item->completed_date;
					$keyr                                                              = strtotime( $item->completed_date ); // timestamp
					//compare 2015-10-30 19:50:50 => 2015-10-30. not use time
					$label[ $keyr ]    = date( 'M.d', strtotime( $item->completed_date ) );
					$datasets[ $keyr ] = (float) $item->total;
				} else {
					$keyr                                                              = strtotime( date( 'Y-m-1', strtotime( $item->completed_date ) ) ); // timestamp of first day month in the loop
					$excerpts[ (int) date( 'm', strtotime( $item->completed_date ) ) ] = date( 'Y-m-d', $keyr );
					$label[ $keyr ]                                                    = date( 'M.Y', strtotime( $item->completed_date ) );
					$datasets[ $keyr ]                                                 = (float) $item->total;
				}
			}

			$range = $this->_range_end - $this->_range_start;
			$cache = $this->_start_in;
			for ( $i = 0; $i <= $range; $i ++ ) {
				$reg = $this->_range_start + $i;

				if ( ! array_key_exists( $reg, $excerpts ) ) {
					if ( $this->chart_groupby === 'day' ) {
						$key              = strtotime( $this->_start_in ) + 24 * 60 * 60 * $i;
						$label[ $key ]    = date( 'M.d', $key );
						$datasets[ $key ] = 0;
					} else {
						$cache                           = date( "Y-$reg-01", strtotime( $cache ) ); // cache current month in the loop
						$label[ strtotime( $cache ) ]    = date( 'M.Y', strtotime( $cache ) );
						$datasets[ strtotime( $cache ) ] = 0;
					}
				}
			}

			ksort( $label );
			ksort( $datasets );

			$results           = new stdClass;
			$results->labels   = array_values( $label );
			$results->datasets = array();

			$data       = new stdClass();
			$data->data = array_values( $datasets );

			$color                      = hb_random_color();
			$data->fillColor            = $color;
			$data->strokeColor          = $color;
			$data->pointColor           = $color;
			$data->pointStrokeColor     = "#fff";
			$data->pointHighlightFill   = "#fff";
			$data->pointHighlightStroke = $color;

			$results->datasets   = array();
			$results->datasets[] = $data;

			return $results;
		}

		/**
		 * For height chart js( license ).
		 *
		 * @since 2.0
		 *
		 * @return array|null
		 */
		public function parseData() {
			$data     = array();
			$excerpts = array();

			$results = $this->_query_results;
			foreach ( $results as $key => $item ) {

				if ( $this->chart_groupby === 'day' ) {
					$excerpts[ (int) date( "z", strtotime( $item->completed_date ) ) ] = $item->completed_date;
					$keyr                                                              = strtotime( $item->completed_date ); // timestamp
					// compare 2015-10-30 19:50:50 => 2015-10-30. not use time
					$data[ $keyr ] = array(
						strtotime( date( 'Y-m-d', $keyr ) ) * 1000,
						(float) $item->total
					);
				} else {
					$keyr                                                              = strtotime( date( 'Y-m-1', strtotime( $item->completed_date ) ) ); // timestamp of first day month in the loop
					$excerpts[ (int) date( "m", strtotime( $item->completed_date ) ) ] = date( 'Y-m-d', $keyr );
					$data[ $keyr ]                                                     = array(
						strtotime( date( 'Y-m-1', $keyr ) ) * 1000,
						(float) $item->total
					);
				}
			}

			$range = $this->_range_end - $this->_range_start;
			$cache = $this->_start_in;
			for ( $i = 0; $i <= $range; $i ++ ) {
				$reg = $this->_range_start + $i;

				if ( ! array_key_exists( $reg, $excerpts ) ) {
					if ( $this->chart_groupby === 'day' ) {
						$key          = strtotime( $this->_start_in ) + 24 * 60 * 60 * $i;
						$data[ $key ] = array(
							(float) strtotime( date( 'Y-m-d', $key ) ) * 1000,
							0
						);
					} else {

						$cache = date( "Y-$reg-01", strtotime( $cache ) ); // cache current month in the loop

						$data[ strtotime( $cache ) ] = array(
							(float) strtotime( date( 'Y-m-1', strtotime( $cache ) ) ) * 1000,
							0
						);
					}
				}
			}

			sort( $data );

			$results = array();

			foreach ( $data as $key => $da ) {
				$results[] = $da;
			}

			return $results;
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

			$filename = 'tp_hotel_export_' . $this->_chart_type . '_' . $this->_start_in . '_to_' . $this->_end_in . '.csv';
			header( 'Content-Type: application/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=' . $filename );

			// create a file pointer connected to the output stream
			$output = fopen( 'php://output', 'w' );

			$column = array(
				__( 'Date/Time', 'wphb-statistic' )
			);
			if ( $this->chart_groupby === 'month' ) {
				$column = array(
					__( 'Month', 'wphb-statistic' )
				);
			}

			$column[] = __( 'Total Earning', 'wphb-statistic' );
			$column   = apply_filters( 'hotel_booking_export_report_price_column', $column );

			// output the column headings
			fputcsv( $output, $column );

			foreach ( $this->_query_results as $key => $item ) {
				$data = array();
				if ( $this->chart_groupby === 'month' ) {
					$data[] = date( 'M. Y', strtotime( date( "Y-{$item->completed_month}-1", strtotime( $item->completed_date ) ) ) );
				} else {
					$data[] = $item->completed_date;
				}
				$data[] = number_format( $item->total, 2, '.', ',' ) . ' ' . hb_get_currency();

				$data = apply_filters( 'hotel_booking_export_report_price_data', $data, $item );

				fputcsv( $output, $data );
			}

			fpassthru( $output );
			die();
		}

		/**
		 * Get date format.
		 *
		 * @since 2.0
		 *
		 * @param string $date
		 *
		 * @return false|string
		 */
		public function date_format( $date = '' ) {
			if ( $this->chart_groupby === 'day' ) {
				if ( $date != (int) $date || is_string( $date ) ) {
					$date = strtotime( $date );
				}

				return date( 'F j, Y', $date );
			} else {
				return date( 'F. Y', strtotime( date( 'Y-' . $date . '-1', time() ) ) );
			}
		}

		/**
		 * Get total.
		 *
		 * @since 2.0
		 *
		 * @param $sidebars
		 *
		 * @return array
		 */
		public function total_earn( $sidebars ) {
			$price                = 0;
			$this->_query_results = $this->getOrdersItems();
			if ( $this->_query_results ) {
				foreach ( $this->_query_results as $key => $item ) {
					$price = $price + $item->total;
				}
				$sidebars[] = array(
					'title'  => sprintf( __( 'Total %s to %s', 'wphb-statistic' ), $this->_start_in, $this->_end_in ),
					'amount' => hb_format_price( $price )
				);
			}

			return $sidebars;
		}

		/**
		 * Print scripts.
		 *
		 * @since 2.0
		 */
		public function print_scripts() {
			if ( wphb_statistic_is_statistic_page() ) {
				?>
                <script type="text/javascript">
                    var wphb_statistic_price = {
                        series: '<?php echo json_encode( $this->series() ); ?>'
                    }
                </script>
			<?php }
		}

		/**
		 * Instance.
		 *
		 * @since 2.0
		 *
		 * @param null $range
		 *
		 * @return mixed|WPHB_Statistic_Price
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

if ( ! isset( $_REQUEST['tab'] ) || sanitize_text_field( $_REQUEST['tab'] ) === 'price' ) {
	$GLOBALS['hb_report'] = WPHB_Statistic_Price::instance();
}