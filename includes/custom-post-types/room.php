<?php
/**
 * WP Hotel Booking Room custom post type class.
 *
 * @class       WPHB_Custom_Post_Type_Room
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Custom_Post_Type_Room' ) ) {
	/**
	 * Class WPHB_Custom_Post_Type_Room
	 */
	class WPHB_Custom_Post_Type_Room {

		/**
		 * WPHB_Custom_Post_Type_Room constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_post_type' ) );

			// update admin room columns
			add_filter( 'manage_hb_room_posts_columns', array( $this, 'room_columns' ) );
			add_action( 'manage_hb_room_posts_custom_column', array( $this, 'room_columns_content' ) );

			// add room meta box
			add_action( 'admin_init', array( $this, 'general_meta_box' ) );
			add_action( 'admin_init', array( $this, 'pricing_meta_box' ) );

			add_action( 'before_delete_post', array( $this, 'before_delete' ) );
		}

		/**
		 * Register post type.
		 */
		public function register_post_type() {
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
				'supports'           => array(
					'title',
					'editor',
					'thumbnail',
					'revisions',
					'comments',
					'author',
					'custom-fields'
				),
				'hierarchical'       => false,
				'rewrite'            => array(
					'slug'       => _x( 'rooms', 'URL slug', 'wp-hotel-booking' ),
					'with_front' => false,
					'feeds'      => true
				),
				'menu_position'      => 3,
				'menu_icon'          => 'dashicons-admin-home'
			);

			register_post_type( 'hb_room', apply_filters( 'hotel_booking_register_post_type_room_arg', $args ) );
		}

		/**
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function room_columns( $columns ) {
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
		 * @param $column
		 */
		public function room_columns_content( $column ) {
			global $post;
			switch ( $column ) {
				case 'thumb':
					$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail', false );
					if ( ! empty( $image ) ) {
						$image = $image[0];
					} else {
						$image = WPHB_PLUGIN_URL . '/assets/images/room-thumb.png';
					}
					echo '<img width="50" height="50" class="room-thumbnail" src="' . esc_url( str_replace( '-150x150', '', $image ) ) . '">';
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
		 * General room meta boxes.
		 */
		public function general_meta_box() {
			WPHB_Meta_Box::instance(
				'room_settings',
				array(
					'title'           => __( 'Room Settings', 'wp-hotel-booking' ),
					'post_type'       => 'hb_room',
					'meta_key_prefix' => '_hb_',
					'priority'        => 'high'
				),
				array()
			)->add_field(
				array(
					'name'  => 'num_of_rooms',
					'label' => __( 'Quantity', 'wp-hotel-booking' ),
					'type'  => 'number',
					'std'   => '10',
					'desc'  => __( 'Number of the room', 'wp-hotel-booking' ),
					'min'   => 0,
					'max'   => 100
				),
				array(
					'name'    => 'room_capacity',
					'label'   => __( 'Adults', 'wp-hotel-booking' ),
					'desc'    => __( 'Room capacity', 'wp-hotel-booking' ),
					'type'    => 'select',
					'options' => hb_get_room_capacities(
						array(
							'map_fields' => array(
								'term_id' => 'value',
								'name'    => 'text'
							)
						)
					),
					'except'  => sprintf( wp_kses( __( '<i>You need to create <a href="%s" target="_blank">room capacities</a> to select number of adults</i>', 'wp-hotel-booking' ),
						array(
							'i' => array(),
							'a' => array( 'href' => array(), 'target' => array() )
						) ), admin_url( 'edit-tags.php?taxonomy=hb_room_capacity&post_type=hb_room' ) )
				),
				array(
					'name'  => 'max_child_per_room',
					'label' => __( 'Children', 'wp-hotel-booking' ),
					'desc'  => __( 'Max children per room', 'wp-hotel-booking' ),
					'type'  => 'number',
					'std'   => 0,
					'min'   => 0,
					'max'   => 100
				),
				array(
					'name'   => 'room_addition_information',
					'label'  => __( 'Addition Information', 'wp-hotel-booking' ),
					'type'   => 'textarea',
					'std'    => '',
					'editor' => true
				),
				array(
					'name'    => 'room_extra',
					'label'   => __( 'Extra Options', 'wp-hotel-booking' ),
					'desc'    => __( 'Room extra options', 'wp-hotel-booking' ),
					'type'    => 'multiple',
					'std'     => '',
					'options' => hb_room_extra_options(),
					'except'  => sprintf( wp_kses( __( '<i>There are no extra options. Create <a href="%s" target="_blank">here</a></i>', 'wp-hotel-booking' ),
						array(
							'i' => array(),
							'a' => array( 'href' => array(), 'target' => array() )
						) ), admin_url( 'admin.php?page=wphb-addition-packages' ) )
				),
				array(
					'name'  => 'gallery',
					'label' => __( 'Gallery Images', 'wp-hotel-booking' ),
					'desc'  => __( 'Room gallery images', 'wp-hotel-booking' ),
					'type'  => 'gallery'
				)
			);
		}

		/**
		 * Pricing room meta boxes.
		 */
		public function pricing_meta_box() {
			new WPHB_Metabox_Room_Price();
		}

		/**
		 * @param $post_id
		 */
		public function before_delete( $post_id ) {
			if ( 'hb_room' == get_post_type( $post_id ) ) {
				$curd = new WPHB_Room_CURD();
				$curd->delete( $post_id );
			}
		}
	}
}

new WPHB_Custom_Post_Type_Room();