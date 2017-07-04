<?php

/**
 * Abstract WP Hotel Booking product class.
 *
 * @class       WPHB_Abstract_Product
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Abstract Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Abstract_Product' ) ) {

	/**
	 * Class WPHB_Abstract_Product.
	 *
	 * @since 2.0
	 */
	abstract class WPHB_Abstract_Product {

		/**
		 * @var int
		 */
		public $quantity = 1;

		/**
		 * @var null
		 */
		protected $check_in_date = null;

		/**
		 * @var null
		 */
		protected $check_out_date = null;

		/**
		 * @var null
		 */
		public $_plans = null;

		/**
		 * @var null|WP_Post
		 */
		public $post = null;

		/**
		 * @var array
		 */
		public $_external_data = array();

		/**
		 * @var int
		 */
		public $_room_details_total = 0;

		/**
		 * @var mixed
		 */
		public $_settings;

		/**
		 * @return null or array
		 */
		public $_review_details = null;

		/**
		 * WPHB_Abstract_Product constructor.
		 *
		 * @since 2.0
		 *
		 * @param $post
		 * @param null $params
		 */
		public function __construct( $post, $params = null ) {
			if ( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_room' ) {
				$this->post = get_post( $post );
			} else if ( $post instanceof WP_Post || is_object( $post ) ) {
				$this->post = $post;
			}
			if ( empty( $this->post ) ) {
				$this->post = hb_create_empty_post();
			}
			global $hb_settings;
			if ( ! $this->_settings ) {
				$this->_settings = $hb_settings;
			}

			if ( $params ) {
				$this->set_data( $params );
			}
		}

		/**
		 * Set extra data form room.
		 *
		 * @since 2.0
		 *
		 * @param $key
		 * @param null $value
		 *
		 * @return $this
		 */
		public function set_data( $key, $value = null ) {
			if ( is_array( $key ) ) {
				foreach ( $key as $k => $v ) {
					$this->set_data( $k, $v );
				}
			} else {
				$this->_external_data[ $key ] = $value;
			}

			return $this;
		}

		/**
		 * Get product data.
		 *
		 * @since 2.0
		 *
		 * @param $key
		 *
		 * @return array|bool|mixed
		 */
		public function get_data( $key ) {
			return ! empty( $this->_external_data[ $key ] ) ? $this->_external_data[ $key ] : ( $key === false ? $this->_external_data : false );
		}

		/**
		 * Magic function to get a variable of room.
		 *
		 * @since 2.0
		 *
		 * @param $key
		 *
		 * @return mixed
		 */
		public function __get( $key ) {
			$return = '';
			switch ( $key ) {
				case 'ID':
					$return = $this->get_data( 'id' ) ? $this->get_data( 'id' ) : $this->post->ID;
					break;
				case 'room_type':
					$terms  = get_the_terms( $this->post->ID, 'hb_room_type' );
					$return = array();
					if ( $terms ) {
						foreach ( $terms as $key => $term ) {
							$return[] = $term->term_id;
						}
					}
					break;
				case 'name':
					$return = get_the_title( $this->ID );
					break;
				case 'capacity':
					$term_id = get_post_meta( $this->post->ID, '_hb_room_capacity', true );
					$return  = get_term_meta( $term_id, 'hb_max_number_of_adults', true );
					if ( ! $return ) {
						$return = (int) get_option( 'hb_taxonomy_capacity_' . $term_id );
					}
					break;
				case 'capacity_title':
					$term_id = get_post_meta( $this->ID, '_hb_room_capacity', true );
					if ( $key == 'capacity_title' ) {
						$term = get_term( $term_id, 'hb_room_capacity' );
						if ( isset( $term->name ) ) {
							$return = $term->name;
						}
					} else {
						$return = get_term_meta( $term_id, 'hb_max_number_of_adults', true );
						if ( ! $return ) {
							$return = (int) get_option( 'hb_taxonomy_capacity_' . $term_id );
						}
					}
					break;
				case 'capacity_id':
					$return = get_post_meta( $this->post->ID, '_hb_room_capacity', true );
					break;
				case 'addition_information':
					$return = get_post_meta( $this->ID, '_hb_room_addition_information', true );
					break;
				case 'thumbnail':
					if ( has_post_thumbnail( $this->ID ) ) {
						$return = get_the_post_thumbnail( $this->ID, 'thumbnail' );
					} else {
						$gallery = get_post_meta( $this->ID, '_hb_gallery', true );
						if ( $gallery ) {
							$attachment_id = array_shift( $gallery );
							$return        = wp_get_attachment_image( $attachment_id, 'thumbnail' );
						} else {
							$return = '<img src="' . esc_url( WPHB_PLUGIN_URL . '/includes/libraries/carousel/default.png' ) . '" alt="' . $this->post->post_title . '"/>';
						}
					}
					break;
				case 'gallery':
					$return = $this->get_galleries();
					break;
				case 'max_child':
					$return = get_post_meta( $this->ID, '_hb_max_child_per_room', true );
					break;

				case 'dropdown_room':
					$max_rooms = get_post_meta( $this->post->ID, '_hb_num_of_rooms', true );
					$return    = '<select name="hb-num-of-rooms[' . $this->post->ID . ']">';
					$return    .= '<option value="0">' . __( 'Select', 'wp-hotel-booking' ) . '</option>';
					for ( $i = 1; $i <= $max_rooms; $i ++ ) {
						$return .= sprintf( '<option value="%1$d">%1$d</option>', $i );
					}
					$return .= '</select>';
					break;
				case 'num_of_rooms':
					$return = get_post_meta( $this->post->ID, '_hb_num_of_rooms', true );
					break;
				case 'room_details_total':
					$return = $this->_room_details_total;
					break;
				case 'price_table':
					$return = __( 'why i am here?', 'wp-hotel-booking' );
					break;
				case 'check_in_date':
					$return = $this->get_data( 'check_in_date' );
					break;
				case 'check_out_date':
					$return = $this->get_data( 'check_out_date' );
					break;
				case 'in_to_out':
					$return = strtotime( $this->get_data( 'check_in_date' ) ) . '_' . strtotime( $this->get_data( 'check_out_date' ) );
					break;
				case 'quantity':
					$return = $this->get_data( 'quantity' );
					break;
				case 'total':
					$return = $this->get_total( $this->get_data( 'check_in_date' ), $this->get_data( 'check_out_date' ), $this->get_data( 'quantity' ), false );
					break;
				case 'total_tax':
					$return = $this->get_total( $this->get_data( 'check_in_date' ), $this->get_data( 'check_out_date' ), $this->get_data( 'quantity' ), true );
					break;
				case 'amount_singular_exclude_tax':
					$return = $this->get_total( $this->get_data( 'check_in_date' ), $this->get_data( 'check_out_date' ), 1, false );
					break;
				case 'amount_singular_include_tax':
					$return = $this->get_total( $this->get_data( 'check_in_date' ), $this->get_data( 'check_out_date' ), 1, true );
					break;
				case 'amount_singular':
					$return = $this->amount_singular();
					break;
				case 'search_key':
					$return = $this->get_data( 'search_key' );
					break;
				case 'extra_packages':
					$return = $this->get_data( 'extra_packages' );
					break;
			}

			return apply_filters( 'hotel_booking_room_get_data', $return, $key, $this );
		}

		/**
		 * Get product galleries.
		 *
		 * @since 2.0
		 *
		 * @param bool $with_featured
		 *
		 * @return array
		 */
		public function get_galleries( $with_featured = true ) {
			$gallery = array();
			if ( $with_featured && $thumb_id = get_post_thumbnail_id( $this->post->ID ) ) {
				$featured_thumb = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
				$featured_full  = wp_get_attachment_image_src( $thumb_id, 'full' );
				$alt            = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
				$gallery[]      = array(
					'id'    => $thumb_id,
					'src'   => $featured_full[0],
					'thumb' => $featured_thumb[0],
					'alt'   => $alt ? $alt : get_the_title( $thumb_id )
				);
			}

			$galleries = get_post_meta( $this->post->ID, '_hb_gallery', true );
			if ( ! $galleries ) {
				return $gallery;
			}

			foreach ( $galleries as $thumb_id ) {

				$w = $this->_settings->get( 'room_thumbnail_width', 150 );
				$h = $this->_settings->get( 'room_thumbnail_height', 150 );

				$size  = apply_filters( 'hotel_booking_room_thumbnail_size', array( 'width' => $w, 'height' => $h ) );
				$thumb = $this->renderImage( $thumb_id, $size, true, 'thumbnail' );
				if ( ! $thumb ) {
					$thumb_src = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
					$thumb     = $thumb_src[0];
				}

				$w    = $this->_settings->get( 'room_image_gallery_width', 1000 );
				$h    = $this->_settings->get( 'room_image_gallery_height', 667 );
				$size = apply_filters( 'hotel_booking_room_gallery_size', array( 'width' => $w, 'height' => $h ) );

				$full = $this->renderImage( $thumb_id, $size, true, 'full' );
				if ( ! $full ) {
					$full_src = wp_get_attachment_image_src( $thumb_id, 'full' );
					$full     = $full_src[0];
				}
				$alt       = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
				$gallery[] = array(
					'id'    => $thumb_id,
					'src'   => $full,
					'thumb' => $thumb,
					'alt'   => $alt ? $alt : get_the_title( $thumb_id )
				);
			}

			return $gallery;
		}

		/**
		 * Get product booking room details.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function get_booking_room_details() {
			$details            = array();
			$room_details_total = 0;
			$start_date         = $this->get_data( 'check_in_date' );
			$end_date           = $this->get_data( 'check_out_date' );

			$start_date_to_time = strtotime( $start_date );

			$tax = false;
			if ( hb_price_including_tax() ) {
				$tax = true;
			}

			$nights = hb_count_nights_two_dates( $end_date, $start_date );
			for ( $i = 0; $i < $nights; $i ++ ) {
				$start_date = $start_date_to_time + $i * DAY_IN_SECONDS;
				$date       = date( 'w', $start_date );
				if ( ! isset( $details[ $date ] ) ) {
					$details[ $date ] = array(
						'count' => 0,
						'price' => 0
					);
				}
				$details[ $date ]['count'] ++;
				$details[ $date ]['price'] += $this->get_total( $start_date, 1, 1, $tax );
				$room_details_total        += $details[ $date ]['price'];
			}
			$this->_room_details_total = $room_details_total;

			return apply_filters( 'hotel_booking_get_booking_room_details', $details, $this->post->ID );
		}

		/**
		 * Get product price.
		 *
		 * @since 2.0
		 *
		 * @param null $date
		 * @param bool $including_tax
		 *
		 * @return float
		 */
		public function get_price( $date = null, $including_tax = true ) {
			$tax = 0;
			if ( $including_tax ) {
				if ( $this->_settings->get( 'price_including_tax' ) ) {
					$tax = $this->_settings->get( 'tax' );
					$tax = (float) $tax / 100;
				}
			}

			if ( ! $date ) {
				$date = time();
			} elseif ( is_string( $date ) ) {
				$date = @strtotime( $date );
			}

			$return        = 0;
			$selected_plan = hb_room_get_selected_plan( $this->post->ID, $date );

			if ( $selected_plan ) {
				$prices = $selected_plan->prices;
				if ( $prices && isset( $prices[ date( 'w', $date ) ] ) ) {
					$return = $prices[ date( 'w', $date ) ];
					$return = $return + $return * $tax;
				}
			}

			return floatval( $return );
		}

		/**
		 * Get product total.
		 *
		 * @since 2.0
		 *
		 * @param null $from
		 * @param null $to
		 * @param int $num_of_rooms
		 * @param bool $including_tax
		 *
		 * @return float|int|mixed
		 */
		public function get_total( $from = null, $to = null, $num_of_rooms = 1, $including_tax = true ) {
			$nights  = 0;
			$total   = 0;
			$to_time = '';
			if ( is_null( $from ) && is_null( $to ) ) {
				$to_time   = (int) $this->check_out_date;
				$from_time = (int) $this->check_in_date;
			} else {
				if ( ! is_numeric( $from ) ) {
					$from_time = strtotime( $from );
				} else {
					$from_time = $from;
				}
				if ( ! is_numeric( $to ) ) {
					$to_time = strtotime( $to );
				} else {
					// if $to is date => calculate normally
					if ( $to >= DAY_IN_SECONDS ) {
						$to_time = $to;
					} else {
						// if set $to is integer => $to is nights
						$nights = $to;
					}
				}
			}

			if ( ! $num_of_rooms ) {
				$num_of_rooms = intval( $this->get_data( 'quantity' ) );
			}

			if ( ! $nights ) {
				$nights = hb_count_nights_two_dates( $to_time, $from_time );
			}

			$from = mktime( 0, 0, 0, date( 'm', $from_time ), date( 'd', $from_time ), date( 'Y', $from_time ) );
			for ( $i = 0; $i < $nights; $i ++ ) {
				$total_per_night = $this->get_price( $from + $i * DAY_IN_SECONDS, false );
				$total           += $total_per_night * $num_of_rooms;
			}

			$total = apply_filters( 'hotel_booking_room_total_price_excl_tax', $total, $this );

			// room price include tax
			if ( $including_tax ) {
				$tax_price = $total * hb_get_tax_settings();
				$tax_price = apply_filters( 'hotel_booking_room_total_price_incl_tax', $tax_price, $this );
				$total     = $total + $tax_price;
			}

			return $total;
		}

		/**
		 * Get product pricing plan.
		 *
		 * @since 2.0
		 *
		 * @return mixed|null
		 */
		public function get_pricing_plans() {
			if ( ! $this->_plans ) {
				$this->_plans = hb_room_get_pricing_plans( $this->post->ID );
			}

			return $this->_plans;
		}

		/**
		 * Get related product.
		 *
		 * @since 2.0
		 *
		 * @return WP_Query
		 */
		public function get_related_rooms() {
			$room_types         = get_the_terms( $this->post->ID, 'hb_room_type' );
			$room_capacity      = (int) get_post_meta( $this->post->ID, '_hb_room_capacity', true );
			$max_child_per_room = (int) get_post_meta( $this->post->ID, '_hb_max_child_per_room', true );

			$taxonomis = array();
			if ( $room_types ) {
				foreach ( $room_types as $key => $tax ) {
					$taxonomis[] = $tax->term_id;
				}
			} else {
				$terms = get_terms( 'hb_room_type' );
				foreach ( $terms as $key => $term ) {
					$taxonomis[] = $term->term_id;
				}
			}

			$args  = array(
				'post_type'    => 'hb_room',
				'status'       => 'publish',
				'meta_query'   => array(
					array(
						'key'     => '_hb_max_child_per_room',
						'value'   => $max_child_per_room,
						'compare' => '<='
					),
				),
				'tax_query'    => array(
					array(
						'taxonomy' => 'hb_room_type',
						'field'    => 'term_id',
						'terms'    => $taxonomis
					),
				),
				'post__not_in' => array( $this->post->ID )
			);
			$query = new WP_Query( $args );
			wp_reset_postdata();

			return $query;
		}

		/**
		 * Count product review.
		 *
		 * @since 2.0
		 *
		 * @param string $content
		 *
		 * @return mixed
		 */
		public function get_review_count( $content = 'view' ) {

			$transient_name = rand() . 'hb_review_count_' . $this->post->ID;
			if ( false === ( $count = get_transient( $transient_name ) ) ) {
				$count = count( $this->get_review_details() );
				set_transient( $transient_name, $count, DAY_IN_SECONDS * 30 );
			}

			return apply_filters( 'hb_room_review_count', $count, $this );
		}

		/**
		 * Get product review details.
		 *
		 * @since 2.0
		 *
		 * @return array|int|null
		 */
		public function get_review_details() {
			if ( ! $this->_review_details ) {
				return get_comments( array( 'post_id' => $this->post->ID, 'status' => 'approve' ) );
			}

			return $this->_review_details;
		}

		/**
		 * Get product image.
		 *
		 * @since 2.0
		 *
		 * @param string $type
		 * @param bool $attachID
		 * @param bool $echo
		 *
		 * @return array|bool|false|string
		 */
		public function getImage( $type = 'catalog', $attachID = false, $echo = true ) {
			if ( $type === 'catalog' ) {
				return $this->get_catalog( $attachID = false, $echo = true );
			}

			return $this->get_thumbnail( $attachID = false, $echo = true );
		}

		/**
		 * Get product average rating.
		 *
		 * @since 2.0
		 *
		 * @return float|int|null
		 */
		public function average_rating() {
			$comments = $this->get_review_details();
			$total    = 0;
			$i        = 0;
			foreach ( $comments as $key => $comment ) {
				$rating = get_comment_meta( $comment->comment_ID, 'rating', true );
				if ( $rating ) {
					$total = $total + $rating;
					$i ++;
				}
			}
			if ( $comments && $i ) {
				return $total / $i;
			}

			return null;
		}

		/**
		 * Get product thumbnail.
		 *
		 * @since 2.0
		 *
		 * @param bool $attachID
		 * @param bool $echo
		 *
		 * @return array|bool|false|string
		 */
		public function get_thumbnail( $attachID = false, $echo = true ) {
			$w = $this->_settings->get( 'room_thumbnail_width', 150 );
			$h = $this->_settings->get( 'room_thumbnail_height', 150 );

			$size = apply_filters( 'hotel_booking_room_thumbnail_size', array( 'width' => $w, 'height' => $h ) );

			if ( $attachID == false ) {
				$attachID = get_post_thumbnail_id( $this->post->ID );
			}

			$alt   = get_post_meta( $attachID, '_wp_attachment_image_alt', true );
			$image = $this->renderImage( $attachID, $size, false, 'thumbnail' );

			if ( $echo && $image ) {
				if ( is_array( $image ) ) {
					echo sprintf( '<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s"/>', esc_url( $image[0] ), esc_attr( $image[1] ), esc_attr( $image[2] ), esc_attr( $alt ) );
				} else {
					sprintf( '<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s"/>', esc_url( $image ), esc_attr( $w ), esc_attr( $h ), esc_attr( $alt ) );
				}
			}

			return $image;
		}

		/**
		 * Get product catalog.
		 *
		 * @since 2.0
		 *
		 * @param bool $attachID
		 * @param bool $echo
		 *
		 * @return array|bool|false|string
		 */
		public function get_catalog( $attachID = false, $echo = true ) {
			$w = $this->_settings->get( 'catalog_image_width', 270 );
			$h = $this->_settings->get( 'catalog_image_height', 270 );

			$size = apply_filters( 'hotel_booking_room_gallery_size', array( 'width' => $w, 'height' => $h ) );

			if ( $attachID == false ) {
				$attachID = get_post_thumbnail_id( $this->post->ID );
			}

			$alt = get_post_meta( $attachID, '_wp_attachment_image_alt', true );

			$image = $this->renderImage( $attachID, $size, false, 'large' );
			if ( $echo && $image[0] ) {
				if ( is_array( $image ) ) {
					echo sprintf( '<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s"/>', esc_url( $image[0] ), esc_attr( $image[1] ), esc_attr( $image[2] ), esc_attr( $alt ) );
				} else {
					echo sprintf( '<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s"/>', esc_url( $image ), esc_attr( $w ), esc_attr( $h ), esc_attr( $alt ) );
				}
			}

			if ( ! $attachID ) {
				$image = WPHB_PLUGIN_URL . '/assets/images/room-thumb.png';
				echo sprintf( '<img src="%1$s" width="%2$s" height="%3$s"/>', esc_url( $image ), esc_attr( $w ), esc_attr( $h ) );
			}

			return $image;
		}

		/**
		 * Render product image.
		 *
		 * @since 2.0
		 *
		 * @param null $attachID
		 * @param array $size
		 * @param bool $src
		 * @param string $default
		 *
		 * @return array|bool|false|string
		 */
		public function renderImage( $attachID = null, $size = array(), $src = true, $default = 'thumbnail' ) {
			$resizer = WPHB_Reizer::getInstance();

			$image = $resizer->process( $attachID, $size, $src );
			if ( $image ) {
				return $image;
			} else {
				$image = wp_get_attachment_image_src( $attachID, $default );
				if ( $src ) {
					return $image[0];
				} else {
					return array(
						$image[0],
						$image[1],
						$image[2]
					);
				}
			}
		}

		/**
		 * Get product pricing plan.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function pricing_plan() {
			$prices = hb_room_get_pricing_plans( get_the_ID() );
			if ( $prices ) {
				sort( $prices );
			}

			$sort          = $prices;
			$prices['min'] = current( $sort );
			$prices['max'] = end( $sort );

			return $prices;
		}

		/**
		 * Get product amount include tax.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function amount_include_tax() {
			return apply_filters( 'hotel_booking_room_item_total_include_tax', $this->total_tax, $this );
		}

		/**
		 * Get product amount exclude tax.
		 *
		 * @return mixed
		 */
		public function amount_exclude_tax() {
			return apply_filters( 'hotel_booking_room_item_total_exclude_tax', $this->total, $this );
		}

		/**
		 * Get product amount.
		 *
		 * @since 2.0
		 *
		 * @param bool $cart
		 *
		 * @return mixed
		 */
		public function amount( $cart = false ) {
			$amount = hb_price_including_tax( $cart ) ? $this->amount_include_tax() : $this->amount_exclude_tax();

			return apply_filters( 'hotel_booking_room_item_amount', $amount, $this );
		}

		/**
		 * Get product singular amount exclude tax.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function amount_singular_exclude_tax() {
			return apply_filters( 'hotel_booking_room_singular_total_exclude_tax', $this->amount_singular_exclude_tax, $this );
		}

		/**
		 * Get product singular amount include tax.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function amount_singular_include_tax() {
			return apply_filters( 'hotel_booking_room_singular_total_include_tax', $this->amount_singular_include_tax, $this );
		}

		/**
		 * Get product singular amount.
		 *
		 * @since 2.0
		 *
		 * @param bool $cart
		 *
		 * @return mixed
		 */
		public function amount_singular( $cart = false ) {
			$amount = hb_price_including_tax( $cart ) ? $this->amount_singular_include_tax() : $this->amount_singular_exclude_tax();

			return apply_filters( 'hotel_booking_room_amount_singular', $amount, $this );
		}

		/**
		 * Check tax enable.
		 *
		 * @since 2.0
		 *
		 * @param string $content
		 *
		 * @return bool
		 */
		public function is_taxable( $content = 'view' ) {
			return true;
		}

		/**
		 * Get tax class.
		 *
		 * @since 2.0
		 *
		 * @param string $content
		 *
		 * @return string
		 */
		public function get_tax_class( $content = 'view' ) {
			return '';
		}

		/**
		 * Check product in stock.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		public function is_in_stock() {
			return true;
		}

	}

}