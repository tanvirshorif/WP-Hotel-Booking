<?php

/**
 * WP Hotel Booking custom post types class.
 *
 * @class       WPHB_Post_Types
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Post_Types' ) ) {
	/**
	 * Class WPHB_Post_Types.
	 *
	 * @since 2.0
	 */
	class WPHB_Post_Types {
		/**
		 * @var array
		 */
		protected static $_ordering = array();

		/**
		 * WPHB_Post_Types constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'register_post_types' ) );
			add_action( 'init', array( $this, 'register_post_statues' ) );
			add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
			add_action( 'admin_init', array( $this, 'update_taxonomy' ) );

			add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );
			add_action( 'admin_head-edit-tags.php', array( $this, 'fix_menu_parent_file' ) );

			// update admin room capacity columns
			add_filter( 'manage_edit-hb_room_capacity_columns', array( $this, 'taxonomy_columns' ) );
			add_filter( 'manage_hb_room_capacity_custom_column', array( $this, 'taxonomy_column_content' ), 10, 3 );

			// update create room capacity fields
			add_action( 'create_hb_room_capacity', array( $this, 'save_capacity_fields' ) );
			add_action( 'hb_room_capacity_add_form_fields', array( $this, 'add_capacity_fields' ) );
			add_action( 'hb_room_capacity_edit_form_fields', array( $this, 'edit_capacity_fields' ) );
			add_action( 'edited_hb_room_capacity', array( $this, 'save_capacity_fields' ) );

			// update admin room columns
			add_filter( 'manage_hb_room_posts_columns', array( $this, 'custom_room_columns' ) );
			add_action( 'manage_hb_room_posts_custom_column', array( $this, 'custom_room_columns_filter' ) );

			// update admin booking columns
			add_filter( 'manage_hb_booking_posts_columns', array( $this, 'custom_booking_columns' ) );
			add_action( 'manage_hb_booking_posts_custom_column', array( $this, 'custom_booking_columns_filter' ) );
			add_filter( 'manage_edit-hb_booking_sortable_columns', array( $this, 'custom_booking_sortable_columns' ) );
			add_action( 'pre_get_posts', array( $this, 'custom_booking_sortable_column_handle' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_action( 'delete_term_taxonomy', array( $this, 'delete_term_data' ) );

			add_filter( 'posts_fields', array( $this, 'posts_fields' ) );
			add_filter( 'posts_join_paged', array( $this, 'posts_join_paged' ) );
			add_filter( 'posts_where', array( $this, 'posts_where_paged' ), 999 );
			add_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 999 );

			add_filter( 'get_terms_orderby', array( $this, 'terms_orderby' ), 100, 3 );
			add_filter( 'get_terms_args', array( $this, 'terms_args' ), 100, 2 );

			define( 'WPHB_Room_CTP', 'hb_room' );
			define( 'WPHB_Extra_CPT', 'hb_extra_room' );
		}

		/**
		 * Add capacity field in create room capacity terms page.
		 */
		public function add_capacity_fields() {
			?>
            <div class="form-field">
                <label for="room_capacity"><?php _e( 'Capacity' ); ?></label>
                <input type="number" min="1" name="room_capacity" id="room_capacity" value="" size="25">
                <p class="description"><?php _e( 'Number adult in room capacity.', 'wp-hotel-booking' ); ?></p>
            </div>
			<?php
		}

		/**
		 * Add capacity field in edit room capacity page.
		 *
		 * @param $tag
		 */
		public function edit_capacity_fields( $tag ) {
			$term_id  = $tag->term_id;
			$capacity = get_term_meta( $term_id, 'hb_max_number_of_adults', true );
			?>

            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="capacity"><?php _e( 'Capacity', 'wp-hotel-booking' ); ?></label>
                </th>
                <td>
                    <input type="number" min="1" name="room_capacity" id="room_capacity" size="25"
                           value="<?php echo esc_attr( $capacity ); ?>"><br/>
                    <span class="description"><?php _e( 'Number adult in room capacity.', 'wp-hotel-booking' ); ?></span>
                </td>
            </tr>
			<?php
		}

		/**
		 * Active save room capacity;
		 *
		 * @param $term_id
		 */
		public function save_capacity_fields( $term_id ) {
			if ( ! $term_id ) {
				return;
			}

			if ( $_POST['room_capacity'] ) {
				update_term_meta( $term_id, 'hb_max_number_of_adults', sanitize_title( $_POST['room_capacity'] ) );
			}
		}

		/**
		 * @param $orderby
		 * @param $args
		 * @param $taxonomies
		 *
		 * @return string
		 */
		public function terms_orderby( $orderby, $args, $taxonomies ) {
			if ( in_array( hb_get_request( 'taxonomy' ), array( 'hb_room_type', 'hb_room_capacity' ) ) ) {
				$orderby = 'term_group';
			}

			return $orderby;
		}

		/**
		 * @param $args
		 * @param $taxonomies
		 *
		 * @return mixed
		 */
		public function terms_args( $args, $taxonomies ) {
			if ( in_array( hb_get_request( 'taxonomy' ), array( 'hb_room_type', 'hb_room_capacity' ) ) ) {
				$args['order'] = 'ASC';
			}

			return $args;
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
		 * Enqueue scripts
		 */
		public function enqueue_scripts() {
			if ( in_array( hb_get_request( 'taxonomy' ), array( 'hb_room_type', 'hb_room_capacity' ) ) ) {
				wp_enqueue_script( 'hb-edit-tags', WPHB_PLUGIN_URL . 'assets/js/edit-tags.min.js', array(
					'jquery',
					'jquery-ui-sortable'
				) );
			}
		}

		/**
		 * Add more columns to admin room list table.
		 *
		 * @since 2.0
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function custom_room_columns( $columns ) {
			unset( $columns['title'] );
			unset( $columns['author'] );
			unset( $columns['comments'] );
			$columns['thumb']               = __( '<i class="fa fa-picture-o"></i>', 'wp-hotel-booking' );
			$columns['title']               = __( 'Title', 'wp-hotel-booking' );
			$columns['room_quantity']       = __( 'Quantity', 'wp-hotel-booking' );
			$columns['room_capacity']       = __( 'Capacity', 'wp-hotel-booking' );
			$columns['room_price_plan']     = __( 'Price', 'wp-hotel-booking' );
			$columns['room_average_rating'] = __( 'Average Rating', 'wp-hotel-booking' );
			$columns['comments']            = __( '<i class="dashicons dashicons-admin-comments"></i>', 'wp-hotel-booking' );

			return $columns;
		}

		/**
		 * Display column contents for admin room list table.
		 *
		 * @since 2.0
		 *
		 * @param $column
		 */
		public function custom_room_columns_filter( $column ) {
			global $post;
			switch ( $column ) {
				case 'thumb':
					$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail', false );
					if ( ! empty( $image ) ) {
						$image = $image[0];
					} else {
						$image = WPHB_PLUGIN_URL . '/assets/images/room-thumb.png';
					}
					$number = get_post_meta( $post->ID, '_hb_num_of_rooms', true );
					if ( $number ) {
						$sale_icon = ' <img class="sale-label" src="' . esc_url( WPHB_PLUGIN_URL . '/assets/images/sale.png' ) . '">';
					} else {
						$sale_icon = '';
					}
					echo '<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $post->ID ) . '&action=edit' ) ) . '">' . wp_kses_post( $sale_icon ) .
					     '<img width="50" height="50" class="room-thumbnail" src="' . esc_url( str_replace( '-150x150', '', $image ) ) . '"></a>';
					break;
				case 'room_quantity':
					echo get_post_meta( $post->ID, '_hb_num_of_rooms', true );
					break;
				case 'room_capacity':
					$cap_id = get_post_meta( $post->ID, '_hb_room_capacity', true );
					$cap    = '';
					if ( $cap_id ) {
						$cap = get_term_meta( $cap_id, 'hb_max_number_of_adults', true );
						$cap .= ( $cap > 1 ) ? __( ' Adults', 'wp-hotel-booking' ) : __( ' Adult', 'wp-hotel-booking' );
					}
					$max_child = get_post_meta( $post->ID, '_hb_max_child_per_room', true );
					if ( $max_child ) {
						$cap .= ' - ' . $max_child;
						$cap .= ( $max_child > 1 ) ? __( ' Children', 'wp-hotel-booking' ) : __( ' Child', 'wp-hotel-booking' );
					}
					echo $cap;
					break;
				case 'room_price_plan':
					echo '<a href="' . admin_url( 'admin.php?page=wphb-pricing-table&hb-room=' . $post->ID ) . '">' . __( 'View Price', 'wp-hotel-booking' ) . '</a>';
					break;
				case 'room_average_rating':
					$room   = WPHB_Room::instance( $post->ID );
					$rating = $room->average_rating();
					$html   = array();
					$html[] = '<div class="rating">';
					if ( $rating ) {
						$html[] = '<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="' . ( sprintf( __( 'Rated %d out of 5', 'wp-hotel-booking' ), $rating ) ) . '">';
						$html[] = '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"></span>';
						$html[] = '</div>';
					}
					$html[] = '</div>';
					echo implode( '', $html );
					break;
			}
		}

		/**
		 * Add more columns to admin booking list table.
		 *
		 * @since 2.0
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function custom_booking_columns( $columns ) {
			unset( $columns['author'] );
			unset( $columns['date'] );
			$columns['customer']       = __( 'Customer', 'wp-hotel-booking' );
			$columns['booking_date']   = __( 'Date', 'wp-hotel-booking' );
			$columns['check_in_date']  = __( 'Check in', 'wp-hotel-booking' );
			$columns['check_out_date'] = __( 'Check out', 'wp-hotel-booking' );
			$columns['total']          = __( 'Total', 'wp-hotel-booking' );
			$columns['title']          = __( 'Booking', 'wp-hotel-booking' );
			$columns['status']         = __( 'Status', 'wp-hotel-booking' );

			return $columns;
		}

		/**
		 * Display column contents for admin booking list table.
		 *
		 * @since 2.0
		 *
		 * @param $column
		 */
		public function custom_booking_columns_filter( $column ) {
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
				case 'check_in_date':
					$check_in_date = hb_booking_get_check_in_date( $post_id );
					if ( $check_in_date ) {
						echo date( hb_get_date_format(), $check_in_date );
					}
					break;
				case 'check_out_date':
					$check_out_date = hb_booking_get_check_out_date( $post_id );
					if ( $check_out_date ) {
						echo date( hb_get_date_format(), $check_out_date );
					}
					break;
				case 'status':
					$link   = '<a href="' . esc_attr( get_edit_post_link( $post_id ) ) . '">' . hb_get_booking_status_label( $post_id ) . '</a>';
					$echo[] = '<span class="hb-booking-status ' . $status . '">' . $link . '</span>';
			}
			echo apply_filters( 'hotel_booking_booking_total', sprintf( '%s', implode( '', $echo ) ), $column, $post_id );
		}

		/**
		 * Add booking sortable columns.
		 *
		 * @since 2.0
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function custom_booking_sortable_columns( $columns ) {
			$columns['booking_date'] = 'booking_date';
//			$columns['check_in_date']  = 'check_in_date';
//			$columns['check_out_date'] = 'check_out_date';

			return $columns;
		}

		/*
		 * Handle custom booking sortable columns.
		 *
		 * @since 2.0
		 *
		 * @param $query
		 */
		public function custom_booking_sortable_column_handle( $query ) {
			if ( ! is_admin() ) {
				return '';
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
		 * Update custom fields for taxonomy
		 */
		public function update_taxonomy() {

			if ( ! empty( $_REQUEST['action'] ) && in_array( hb_get_request( 'taxonomy' ), array(
					'hb_room_type',
					'hb_room_capacity'
				) )
			) {
				$taxonomy = ! empty( $_REQUEST['taxonomy'] ) ? sanitize_text_field( $_REQUEST['taxonomy'] ) : '';
				global $wpdb;
				if ( ! empty( $_POST["{$taxonomy}_ordering"] ) ) {
					$when = array();
					$ids  = array();
					foreach ( $_POST["{$taxonomy}_ordering"] as $term_id => $ordering ) {
						$when[] = "WHEN term_id = {$term_id} THEN {$ordering}";
						$ids[]  = absint( $term_id );
					}

					$query = sprintf( "
                    UPDATE {$wpdb->terms}
                    SET term_group = CASE
                       %s
                    END
                    WHERE term_id IN(%s)
                ", join( "\n", $when ), join( ', ', $ids ) );
					$wpdb->query( $query );
				}

				if ( ! empty( $_POST["{$taxonomy}_capacity"] ) ) {
					foreach ( (array) $_POST["{$taxonomy}_capacity"] as $term_id => $capacity ) {
						if ( $capacity ) {
							// update_option( 'hb_taxonomy_capacity_' . $term_id, $capacity );
							update_term_meta( $term_id, 'hb_max_number_of_adults', absint( sanitize_text_field( $capacity ) ) );
						} else {
							// delete_option( 'hb_taxonomy_capacity_' . $term_id );
							delete_term_meta( $term_id, 'hb_max_number_of_adults' );
						}
					}
				}
			}
		}

		/**
		 * @param $term_id
		 */
		public function delete_term_data( $term_id ) {
			delete_option( 'hb_taxonomy_thumbnail_' . $term_id );
		}

		/**
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function taxonomy_columns( $columns ) {
			if ( 'hb_room_type' == sanitize_text_field( $_REQUEST['taxonomy'] ) ) {
				$columns['thumbnail'] = __( 'Gallery', 'wp-hotel-booking' );
			} else {
				$columns['capacity'] = __( 'Capacity', 'wp-hotel-booking' );
			}
			$columns['ordering'] = __( 'Ordering', 'wp-hotel-booking' );
			if ( isset( $columns['description'] ) ) {
				unset( $columns['description'] );
			}
			if ( isset( $columns['posts'] ) ) {
				unset( $columns['posts'] );
			}

			return $columns;
		}

		/**
		 * @param $content
		 * @param $column_name
		 * @param $term_id
		 *
		 * @return string
		 */
		public function taxonomy_column_content( $content, $column_name, $term_id ) {
			$taxonomy = sanitize_text_field( $_REQUEST['taxonomy'] );
			$term     = get_term( $term_id, $taxonomy );
			switch ( $column_name ) {
				case 'ordering':
					$content = sprintf( '<input class="hb-number-field" type="number" name="%s_ordering[%d]" value="%d" size="3" />', $taxonomy, $term_id, $term->term_group );
					break;
				case 'capacity':
					$capacity = get_term_meta( $term_id, 'hb_max_number_of_adults', true );
					$content  = '<input class="hb-number-field" type="number" name="' . $taxonomy . '_capacity[' . $term_id . ']" value="' . $capacity . '" size="2" />';
					break;
				default:
					break;
			}

			return $content;
		}

		/**
		 * Fix menu parent for taxonomy menu item
		 */
		public function fix_menu_parent_file() {
			if ( in_array( hb_get_request( 'taxonomy' ), array( 'hb_room_type', 'hb_room_capacity' ) ) ) {
				$GLOBALS['parent_file'] = 'tp_hotel_booking';
			}
		}

		/**
		 * Remove default meta boxes
		 */
		public function remove_meta_boxes() {
			remove_meta_box( 'hb_room_capacitydiv', 'hb_room', 'side' );
			remove_meta_box( 'tagsdiv-hb_room_capacity', 'hb_room', 'side' );
		}

		/**
		 * Register custom post types for WP Hotel Booking.
		 */
		public function register_post_types() {
			// register room
			$args = array(
				'labels'             => array(
					'name'               => _x( 'Rooms', 'post type general name', 'wp-hotel-booking' ),
					'singular_name'      => _x( 'Room', 'post type singular name', 'wp-hotel-booking' ),
					'menu_name'          => __( 'Rooms', 'wp-hotel-booking' ),
					'parent_item_colon'  => __( 'Parent Item:', 'wp-hotel-booking' ),
					'all_items'          => __( 'Rooms', 'wp-hotel-booking' ),
					'view_item'          => __( 'View Room', 'wp-hotel-booking' ),
					'add_new_item'       => __( 'Add New Room', 'wp-hotel-booking' ),
					'add_new'            => __( 'Add New Room', 'wp-hotel-booking' ),
					'edit_item'          => __( 'Edit Room', 'wp-hotel-booking' ),
					'update_item'        => __( 'Update Room', 'wp-hotel-booking' ),
					'search_items'       => __( 'Search Room', 'wp-hotel-booking' ),
					'not_found'          => __( 'No room found', 'wp-hotel-booking' ),
					'not_found_in_trash' => __( 'No room found in Trash', 'wp-hotel-booking' ),
				),
				'public'             => true,
				'query_var'          => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'has_archive'        => true,
				'capability_type'    => 'hb_room',
				'map_meta_cap'       => true,
				'show_in_menu'       => true,
				'show_in_admin_bar'  => true,
				'show_in_nav_menus'  => true,
				'taxonomies'         => array( 'room_category', 'room_tag' ),
				'supports'           => array( 'title', 'editor', 'thumbnail', 'revisions', 'comments', 'author' ),
				'hierarchical'       => false,
				'rewrite'            => array(
					'slug'       => _x( 'rooms', 'URL slug', 'wp-hotel-booking' ),
					'with_front' => false,
					'feeds'      => true
				),
				'menu_position'      => 3,
				'menu_icon'          => 'dashicons-admin-home'
			);
			$args = apply_filters( 'hotel_booking_register_post_type_room_arg', $args );
			register_post_type( 'hb_room', $args );

			// register room extra package
			$args = array(
				'labels'              => array(
					'name'               => __( 'Extra Room', 'wp-hotel-booking' ),
					'singular_name'      => __( 'Extra Room', 'wp-hotel-booking' ),
					'add_new'            => _x( 'Add New Extra Room', 'wp-hotel-booking', 'wp-hotel-booking' ),
					'add_new_item'       => __( 'Add New Extra Room', 'wp-hotel-booking' ),
					'edit_item'          => __( 'Edit Extra Room', 'wp-hotel-booking' ),
					'new_item'           => __( 'New Extra Room', 'wp-hotel-booking' ),
					'view_item'          => __( 'View Extra Room', 'wp-hotel-booking' ),
					'search_items'       => __( 'Search Extra Room', 'wp-hotel-booking' ),
					'not_found'          => __( 'No Extra Room found', 'wp-hotel-booking' ),
					'not_found_in_trash' => __( 'No Extra Room found in Trash', 'wp-hotel-booking' ),
					'parent_item_colon'  => __( 'Parent Singular Extra Room:', 'wp-hotel-booking' ),
					'menu_name'          => __( 'Extra Room', 'wp-hotel-booking' ),
				),
				'hierarchical'        => false,
				'description'         => __( 'Extra room system booking', 'wp-hotel-booking' ),
				'taxonomies'          => array(),
				'public'              => false,
				'show_ui'             => false,
				'show_in_menu'        => false,
				'show_in_admin_bar'   => false,
				'menu_position'       => null,
				'menu_icon'           => null,
				'show_in_nav_menus'   => false,
				'publicly_queryable'  => true,
				'exclude_from_search' => true,
				'has_archive'         => true,
				'query_var'           => true,
				'rewrite'             => true,
				'capability_type'     => 'hb_room',
				'supports'            => array( 'title', 'editor' )
			);
			$args = apply_filters( 'hotel_booking_register_post_type_extra_room_arg', $args );
			register_post_type( 'hb_extra_room', $args );

			// register booking
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
				'supports'           => array( 'title' ),
				'hierarchical'       => false,
			);
			$args = apply_filters( 'hotel_booking_register_post_type_booking_arg', $args );
			register_post_type( 'hb_booking', $args );
		}

		/**
		 * Register room taxonomies.
		 */
		public static function register_taxonomies() {
			// register room capacity
			$args = array(
				'hierarchical' => false,
				'label'        => __( 'Room Capacity', 'wp-hotel-booking' ),
				'labels'       => array(
					'name'              => __( 'Room Capacities', 'wp-hotel-booking' ),
					'singular_name'     => __( 'Room Capacity', 'wp-hotel-booking' ),
					'menu_name'         => _x( 'Room Capacities', 'Room Capacities', 'wp-hotel-booking' ),
					'search_items'      => __( 'Search Room Capacities', 'wp-hotel-booking' ),
					'all_items'         => __( 'All Room Capacity', 'wp-hotel-booking' ),
					'parent_item'       => __( 'Parent Room Capacity', 'wp-hotel-booking' ),
					'parent_item_colon' => __( 'Parent Room Capacity:', 'wp-hotel-booking' ),
					'edit_item'         => __( 'Edit Room Capacity', 'wp-hotel-booking' ),
					'update_item'       => __( 'Update Room Capacity', 'wp-hotel-booking' ),
					'add_new_item'      => __( 'Add New Room Capacity', 'wp-hotel-booking' ),
					'new_item_name'     => __( 'New Room Type Capacity', 'wp-hotel-booking' )
				),
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array(
					'slug'         => _x( 'room-capacity', 'URL slug', 'wp-hotel-booking' ),
					'with_front'   => false,
					'hierarchical' => true,
				),
				'capabilities' => array(
					'manage_terms' => 'manage_hb_booking',
					'edit_terms'   => 'manage_hb_booking',
					'delete_terms' => 'manage_hb_booking',
					'assign_terms' => 'manage_hb_booking'
				)
			);
			$args = apply_filters( 'hotel_booking_register_tax_capacity_arg', $args );
			register_taxonomy( 'hb_room_capacity', array( 'hb_room' ), $args );

			// register room category
			$args = array(
				'hierarchical' => true,
				'label'        => __( 'Room Type', 'wp-hotel-booking' ),
				'labels'       => array(
					'name'              => _x( 'Room Types', 'taxonomy general name', 'wp-hotel-booking' ),
					'singular_name'     => _x( 'Room Type', 'taxonomy singular name', 'wp-hotel-booking' ),
					'menu_name'         => _x( 'Room Types', 'Room Types', 'wp-hotel-booking' ),
					'search_items'      => __( 'Search Room Types', 'wp-hotel-booking' ),
					'all_items'         => __( 'All Room Types', 'wp-hotel-booking' ),
					'parent_item'       => __( 'Parent Room Type', 'wp-hotel-booking' ),
					'parent_item_colon' => __( 'Parent Room Type:', 'wp-hotel-booking' ),
					'edit_item'         => __( 'Edit Room Type', 'wp-hotel-booking' ),
					'update_item'       => __( 'Update Room Type', 'wp-hotel-booking' ),
					'add_new_item'      => __( 'Add New Room Type', 'wp-hotel-booking' ),
					'new_item_name'     => __( 'New Room Type Name', 'wp-hotel-booking' )
				),
				'public'       => true,
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array( 'slug' => _x( 'room-type', 'URL slug', 'wp-hotel-booking' ) ),
				'capabilities' => array(
					'manage_terms' => 'manage_hb_booking',
					'edit_terms'   => 'manage_hb_booking',
					'delete_terms' => 'manage_hb_booking',
					'assign_terms' => 'manage_hb_booking'
				)
			);
			$args = apply_filters( 'hotel_booking_register_tax_room_type_arg', $args );
			register_taxonomy( 'hb_room_type', array( 'hb_room' ), $args );

			// register room location
			$args = array(
				'hierarchical' => true,
				'label'        => __( 'Locations', 'wp-hotel-booking' ),
				'labels'       => array(
					'name'              => _x( 'Locations', 'taxonomy general name', 'wp-hotel-booking' ),
					'singular_name'     => _x( 'Location', 'taxonomy singular name', 'wp-hotel-booking' ),
					'menu_name'         => _x( 'Locations', 'Room Types', 'wp-hotel-booking' ),
					'search_items'      => __( 'Search Locations', 'wp-hotel-booking' ),
					'all_items'         => __( 'All Locations', 'wp-hotel-booking' ),
					'parent_item'       => __( 'Parent Location', 'wp-hotel-booking' ),
					'parent_item_colon' => __( 'Parent Location:', 'wp-hotel-booking' ),
					'edit_item'         => __( 'Edit Location', 'wp-hotel-booking' ),
					'update_item'       => __( 'Update Location', 'wp-hotel-booking' ),
					'add_new_item'      => __( 'Add New Location', 'wp-hotel-booking' ),
					'new_item_name'     => __( 'New Location Name', 'wp-hotel-booking' )
				),
				'public'       => true,
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array( 'slug' => _x( 'room-locations', 'URL slug', 'wp-hotel-booking' ) ),
				'capabilities' => array(
					'manage_terms' => 'manage_hb_booking',
					'edit_terms'   => 'manage_hb_booking',
					'delete_terms' => 'manage_hb_booking',
					'assign_terms' => 'manage_hb_booking'
				)
			);
			$args = apply_filters( 'hotel_booking_register_tax_room_location_arg', $args );
			register_taxonomy( 'hb_room_location', array( 'hb_room' ), $args );
		}

		/**
		 * Registers booking statues.
		 */
		public function register_post_statues() {
			$statuses = array(
				'cancelled'  => 'Cancelled',
				'pending'    => 'Pending',
				'processing' => 'Processing',
				'completed'  => 'Completed',
			);

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
	}
}

new WPHB_Post_Types();
