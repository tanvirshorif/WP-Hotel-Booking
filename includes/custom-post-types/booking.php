<?php
/**
 * WP Hotel Booking Booking custom post type class.
 *
 * @class       WPHB_Custom_Post_Type_Booking
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Custom_Post_Type_Booking' ) ) {
	/**
	 * Class WPHB_Custom_Post_Type_Booking
	 */
	class WPHB_Custom_Post_Type_Booking {

		/**
		 * WPHB_Custom_Post_Type_Booking constructor.
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'register_post_type' ) );
			add_action( 'init', array( $this, 'register_post_statues' ) );

			// update admin booking columns
			add_filter( 'manage_hb_booking_posts_columns', array( $this, 'booking_columns' ) );
			add_action( 'manage_hb_booking_posts_custom_column', array( $this, 'booking_columns_content' ) );

			add_filter( 'posts_fields', array( $this, 'posts_fields' ) );
			add_filter( 'posts_join_paged', array( $this, 'posts_join_paged' ) );
			add_filter( 'posts_where', array( $this, 'posts_where_paged' ), 999 );
			add_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 999 );

			// update sortable columns
			add_filter( 'manage_edit-hb_booking_sortable_columns', array( $this, 'sortable_columns' ) );

//			add_filter( 'post_row_actions', array( $this, 'admin_booking_row_actions' ), 10, 2 );

			add_filter( 'views_edit-hb_booking', array( $this, 'views_edit_booking' ) );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );

			add_action( 'before_delete_post', array( $this, 'before_delete' ) );
		}

		/**
		 * Register post type.
		 */
		public function register_post_type() {
			$args = array(
				'labels'             => array(
					'name'               => _x( 'Bookings', 'post type general name', 'wp-hotel-booking' ),
					'singular_name'      => _x( 'Booking', 'post type singular name', 'wp-hotel-booking' ),
					'menu_name'          => __( 'Bookings', 'wp-hotel-booking' ),
					'parent_item_colon'  => __( 'Parent Item:', 'wp-hotel-booking' ),
					'all_items'          => __( 'Bookings', 'wp-hotel-booking' ),
					'view_item'          => __( 'View Booking', 'wp-hotel-booking' ),
					'add_new_item'       => __( 'Add New Booking', 'wp-hotel-booking' ),
					'add_new'            => __( 'Add New', 'wp-hotel-booking' ),
					'edit_item'          => __( 'Edit Booking', 'wp-hotel-booking' ),
					'update_item'        => __( 'Update Booking', 'wp-hotel-booking' ),
					'search_items'       => __( 'Search Booking', 'wp-hotel-booking' ),
					'not_found'          => __( 'No booking found', 'wp-hotel-booking' ),
					'not_found_in_trash' => __( 'No booking found in Trash', 'wp-hotel-booking' ),
				),
				'public'             => false,
				'query_var'          => true,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'has_archive'        => false,
				'capability_type'    => 'hb_booking',
				'map_meta_cap'       => true,
				'show_in_menu'       => 'tp_hotel_booking',
				'show_in_admin_bar'  => true,
				'show_in_nav_menus'  => true,
				'supports'           => array( 'custom-fields' ),
				'hierarchical'       => false,
			);

			register_post_type( 'hb_booking', apply_filters( 'hb_register_post_type_booking_arg', $args ) );
		}

		/**
		 * Registers booking statues.
		 */
		public function register_post_statues() {
			$statuses = hb_get_booking_statuses();

			foreach ( $statuses as $key => $status ) {
				register_post_status( 'hb_' . $key,
					array(
						'label'                     => _x( $status, 'Booking status', 'wp-hotel-booking' ),
						'public'                    => false,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,
						'label_count'               => _n_noop( $status . ' <span class="count">(%s)</span>', $status . ' <span class="count">(%s)</span>', 'wp-hotel-booking' )
					)
				);
			}
		}

		/**
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function booking_columns( $columns ) {
			unset( $columns['author'] );
			unset( $columns['date'] );
			$columns['title']        = __( 'ID', 'wp-hotel-booking' );
			$columns['customer']     = __( 'Customer', 'wp-hotel-booking' );
			$columns['booking_date'] = __( 'Date', 'wp-hotel-booking' );
			$columns['detail']       = __( 'Detail', 'wp-hotel-booking' );
			$columns['total']        = __( 'Total', 'wp-hotel-booking' );
			$columns['status']       = __( 'Status', 'wp-hotel-booking' );
			$columns['actions']      = __( 'Actions', 'wp-hotel-booking' );

			return $columns;
		}

		/**
		 * @param $column
		 */
		public function booking_columns_content( $column ) {
			global $post;
			$post_id = $post->ID;
			$booking = WPHB_Booking::instance( $post_id );
			$echo    = array();
			$status  = get_post_status( $post_id );
			switch ( $column ) {
				case 'booking_id':
					$echo[] = hb_format_order_number( $post_id );
					break;
				case 'customer':
					$echo[] = hb_get_customer_fullname( $post_id, true );
					$echo[] = $booking->user_id && ( $user = get_userdata( $booking->user_id ) ) ? sprintf( wp_kses( '<strong>[<a href="%s">%s</a>]</strong>', array(
						'strong' => array(),
						'a'      => array( 'href' => array() )
					) ), get_edit_user_link( $booking->user_id ), $user->user_login ) : __( '[Guest]', 'wp-hotel-booking' );
					break;
				case 'total':
					global $hb_settings;
					$total    = $booking->total();
					$currency = $booking->payment_currency;
					if ( ! $currency ) {
						$currency = $booking->currency;
					}
					$total_with_currency = hb_format_price( $total, hb_get_currency_symbol( $currency ) );

					$echo[] = $total_with_currency;
					if ( $method = hb_get_user_payment_method( $booking->method ) ) {
						$echo[] = ' ( ' . esc_html( $method->description ) . ' )';
					}
					if ( $status === 'hb-processing' ) {
						$advance_payment  = $booking->advance_payment;
						$advance_settings = $booking->advance_payment_setting;
						if ( ! $advance_settings ) {
							$advance_settings = $hb_settings->get( 'advance_payment', 50 );
						}

						if ( floatval( $total ) !== floatval( $advance_payment ) ) {
							$echo[] = sprintf(
								__( '<br />(<small class="hb_advance_payment">Charged %s = %s</small>)', 'wp-hotel-booking' ),
								$advance_settings . '%',
								hb_format_price( $advance_payment, hb_get_currency_symbol( $currency ) )
							);
						}
					}
					do_action( 'hb_manage_booing_column_total', $post_id, $total, $total_with_currency );
					break;
				case 'booking_date':
					echo date( hb_get_date_format(), strtotime( get_post_field( 'post_date', $post_id ) ) );
					break;
				case 'detail':
					$rooms = hb_get_booking_items( $post_id, 'line_item', null, true );
					foreach ( $rooms as $room ) { ?>
                        <div>
                            <a class="room-name" href="<?php echo get_the_permalink( $room['id'] ); ?>"
                               target="_blank"><?php echo esc_html( $room['order_item_name'] ); ?></a>
                            <span><?php echo $room['check_in_date'] . ' - ' . $room['check_in_date']; ?></span>
                        </div>
					<?php }
					break;
				case 'status': ?>
                    <span class="hb-booking-status <?php echo esc_attr( $status ); ?>">
                        <a href="<?php echo esc_attr( get_edit_post_link( $post_id ) ); ?>"><?php echo hb_get_booking_status_label( $post_id ); ?></a>
                    </span>
					<?php
					break;
				case 'actions':
					switch ( $status ) {
						case 'hb-completed':
							break;
						case 'hb-cancelled':
							break;
					} ?>
                    <a class="delete-booking" href="<?php echo get_delete_post_link( $post_id ); ?>"><i
                                class="dashicons dashicons-trash"></i></a>
					<?php break;
				default:
					break;
			}
			echo apply_filters( 'hotel_booking_booking_total', sprintf( '%s', implode( '', $echo ) ), $column, $post_id );
		}

		/**
		 * @param $fields
		 *
		 * @return string
		 */
		public function posts_fields( $fields ) {
			if ( is_admin() && hb_get_request( 'post_type' ) == 'hb_booking' ) {
				$from   = hb_get_request( 'date-from-timestamp' );
				$to     = hb_get_request( 'date-to-timestamp' );
				$filter = hb_get_request( 'filter-type' );
				if ( $from && $to && $filter == 'booking-date' ) {
					$fields .= ", DATE_FORMAT(`post_date`,'%Y%m%d') AS post_date_timestamp";
				}
			}

			return $fields;
		}

		/**
		 * Join with postmeta to enable search by customer meta such as first name, last name, email, etc...
		 *
		 * @param $join
		 *
		 * @return string
		 */
		public function posts_join_paged( $join ) {
			global $wpdb;
			$result = $wpdb->get_col( "SELECT order_item_id FROM {$wpdb->hotel_booking_order_items} WHERE `order_item_id` IS NOT NULL" );
			if ( ! $this->is_search( 'booking' ) || ! $result ) {
				return $join;
			}
			if ( is_admin() && $this->is_search( 'booking' ) ) {
				$join .= "
                INNER JOIN {$wpdb->hotel_booking_order_items} AS ord_item ON ord_item.order_id = {$wpdb->posts}.ID
            ";
			}

			if ( is_admin() && hb_get_request( 'post_type' ) == 'hb_booking' ) {
				$from   = hb_get_request( 'date-from-timestamp' );
				$to     = hb_get_request( 'date-to-timestamp' );
				$filter = hb_get_request( 'filter-type' );
				if ( $from && $to & $filter ) {
					switch ( $filter ) {
						case 'booking-date':
							break;
						case 'check-in-date':
							$join .= "
                            INNER JOIN {$wpdb->hotel_booking_order_itemmeta} AS pm_check_in ON ord_item.order_item_id = pm_check_in.hotel_booking_order_item_id and pm_check_in.meta_key='check_in_date'
                        ";
							break;
						case 'check-out-date':
							$join .= "
                            INNER JOIN {$wpdb->hotel_booking_order_itemmeta} AS pm_check_out ON ord_item.order_item_id = pm_check_out.hotel_booking_order_item_id and pm_check_out.meta_key='check_out_date'
                        ";
							break;
					}
				}
			}

			return $join;
		}

		/**
		 * Conditions to filter customer by meta value such as first name, last name, email, etc...
		 *
		 * @param $where
		 *
		 * @return string
		 */
		public function posts_where_paged( $where ) {
			if ( is_admin() && hb_get_request( 'post_type' ) == 'hb_booking' ) {
				$from   = hb_get_request( 'date-from-timestamp' );
				$to     = hb_get_request( 'date-to-timestamp' );
				$filter = hb_get_request( 'filter-type' );
				if ( $from && $to & $filter ) {
					$from = absint( $from );
					$to   = absint( $to );
					switch ( $filter ) {
						case 'booking-date':
							break;
						case 'check-in-date':
							$where .= "
                            AND ( pm_check_in.meta_value >= {$from} AND pm_check_in.meta_value <= {$to} )
                        ";
							break;
						case 'check-out-date':
							$where .= "
                            AND ( pm_check_out.meta_value >= {$from} AND pm_check_out.meta_value <= {$to} )
                        ";
							break;
					}
				}
			}

			return $where;
		}

		/**
		 * @param $groupby
		 *
		 * @return string
		 */
		public function posts_groupby( $groupby ) {
			if ( is_admin() && hb_get_request( 'post_type' ) == 'hb_booking' ) {
				global $wpdb;
				$groupby .= " {$wpdb->posts}.ID ";
				$filter  = hb_get_request( 'filter-type' );
				if ( $filter === 'booking-date' ) {
					$from = date( 'Ymd', hb_get_request( 'date-from-timestamp' ) );
					$to   = date( 'Ymd', hb_get_request( 'date-to-timestamp' ) );
					if ( $from == $to ) {
						$groupby .= "
                        HAVING post_date_timestamp = {$from}
                    ";
					} else {
						$groupby .= "
                        HAVING post_date_timestamp >= {$from} AND post_date_timestamp <= {$to}
                    ";
					}
				}
			}

			return $groupby;
		}

		/**
		 * @param $type
		 *
		 * @return bool
		 */
		public function is_search( $type ) {
			$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
			if ( is_admin() && $post_type === "hb_{$type}" ) {
				return true;
			}

			return false;
		}

		/**
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function sortable_columns( $columns ) {
			$columns['booking_date'] = 'booking_date';

			return $columns;
		}

		/**
		 * Remove admin booking row actions link.
		 *
		 * @param $actions
		 * @param $post
		 *
		 * @return array
		 */
		public function admin_booking_row_actions( $actions, $post ) {
			if ( 'hb_booking' == $post->post_type ) {
				return array();
			}

			return $actions;
		}

		/**
		 * @param $views
		 *
		 * @return mixed
		 */
		public function views_edit_booking( $views ) {
			if ( 'hb_booking' != get_post_type() ) {
				return $views;
			}
			unset( $views['all'] );

			$query  = array( 'post_type' => 'hb_booking' );
			$status = ( isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] ) ? $_REQUEST['post_status'] : 'hb-completed';

			$new_views = hb_get_booking_statuses();
			foreach ( $new_views as $view => $name ) {
				$class = '';
				switch ( $view ) {
					case 'hb-completed':
					case 'hb-processing':
					case 'hb-pending':
					case 'hb-cancelled':
						$class                = $status == $view ? ' class="current" ' : '';
						$query['post_status'] = $view;
						break;
					default:
						$query = apply_filters( 'hb_booking_query_post', '' );
						break;
				}

				$result = new WP_Query( array( 'post_type' => 'hb_booking', 'post_status' => 'hb-pending' ) );

				$views[ $view ] = sprintf( '<a href="%s"' . $class . '>' . esc_html( $name ) . '<span class="count"> (%d)</span></a>', esc_url( add_query_arg( $query, 'edit.php' ) ), $result->found_posts );
			}

			return $views;
		}

		/**
		 * Pre get posts for filter by status, booking date, check in date, checkout data.
		 *
		 * @param $query
		 *
		 * @return WP_Query
		 */
		public function pre_get_posts( $query ) {
			$status = $_REQUEST['post_status'] ? $_REQUEST['post_status'] : 'hb-completed';

			/**
			 * @var $query WP_Query
			 */
			if ( isset( $status ) && $status ) {
				$query->set( 'post_status', $status );
			}

			$orderby = $query->get( 'orderby' );
			switch ( $orderby ) {
				case 'booking_date':
					$query->set( 'orderby', 'date' );
					break;
				case 'check_in_date':
					break;
				case 'check_out_date':
					break;
				default:
					break;
			}

			return $query;
		}

		/**
		 * Delete booking data in order_items and order_itemmeta table.
		 *
		 * @param $post_id
		 */
		public function before_delete( $post_id ) {
			if ( 'hb_booking' == get_post_type( $post_id ) ) {
				$curd = new WPHB_Booking_CURD();
				$curd->delete( $post_id );
			}
		}
	}
}

new WPHB_Custom_Post_Type_Booking();