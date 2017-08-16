<?php

/**
 * WP Hotel Booking admin core functions.
 *
 * @version     2.0
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Functions
 * @category    Core Functions
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! function_exists( 'hb_admin_settings_tabs' ) ) {
	/**
	 * Get admin setting tabs.
	 *
	 * @return mixed
	 */
	function hb_admin_settings_tabs() {
		return apply_filters( 'hb_admin_settings_tabs', array() );
	}
}


if ( ! function_exists( 'hb_admin_i18n' ) ) {
	/**
	 * Print admin scripts.
	 *
	 * @return mixed
	 */
	function hb_admin_i18n() {
		$i18n = array(
			'choose_images'                 => __( 'Choose images', 'wp-hotel-booking' ),
			'confirm_remove_pricing_table'  => __( 'Are you sure you want to remove this pricing table?', 'wp-hotel-booking' ),
			'empty_pricing_plan_start_date' => __( 'Select start date for plan', 'wp-hotel-booking' ),
			'empty_pricing_plan_end_date'   => __( 'Select end date for plan', 'wp-hotel-booking' ),
			'filter_error'                  => __( 'Please select date range and filter type', 'wp-hotel-booking' ),
			'dayNames'                      => hb_day_name_js(),
			'dayNamesShort'                 => hb_day_name_short_js(),
			'dayNamesMin'                   => hb_day_name_min_js(),
			'date_time_format'              => hb_date_format_js(),
			'monthNames'                    => hb_month_name_js(),
			'monthNamesShort'               => hb_month_name_short_js(),
			'select_room'                   => __( 'Enter room name', 'wp-hotel-booking' ),
			'confirm_remove_extra'          => __( 'Remove package. Are you sure?', 'wp-hotel-booking' ),

			'remove_image'            => __( 'Remove this image', 'wp-hotel-booking' ),
			'date_range'              => __( 'Date range', 'wp-hotel-booking' ),
			'select_booking_customer' => __( 'Enter user login', 'wp-hotel-booking' ),
		);

		return apply_filters( 'hb_admin_i18n', $i18n );
	}
}

if ( ! function_exists( 'hb_add_meta_boxes' ) ) {
	function hb_add_meta_boxes() {
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
				'desc'  => __( 'The number of rooms', 'wp-hotel-booking' ),
				'min'   => 0,
				'max'   => 100
			),
			array(
				'name'    => 'room_capacity',
				'label'   => __( 'Number of adults', 'wp-hotel-booking' ),
				'type'    => 'select',
				'options' => hb_get_room_capacities(
					array(
						'map_fields' => array(
							'term_id' => 'value',
							'name'    => 'text'
						)
					)
				),
				'except'  => sprintf( wp_kses( __( '<i>You need create <a href="%s" target="_blank">room capacities</a> to select number of adults</i>', 'wp-hotel-booking' ),
					array(
						'i' => array(),
						'a' => array( 'href' => array(), 'target' => array() )
					) ), admin_url( 'edit-tags.php?taxonomy=hb_room_capacity&post_type=hb_room' ) )
			),
			array(
				'name'  => 'max_child_per_room',
				'label' => __( 'Max children per room', 'wp-hotel-booking' ),
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
				'label'   => __( 'Addition Package', 'wp-hotel-booking' ),
				'type'    => 'multiple',
				'std'     => '',
				'options' => hb_room_extra_options()
			)
		);

		WPHB_Meta_Box::instance(
			'gallery_settings',
			array(
				'title'           => __( 'Gallery Settings', 'wp-hotel-booking' ),
				'post_type'       => 'hb_room',
				'meta_key_prefix' => '_hb_', // meta key prefix,
				'priority'        => 'high'
				// 'callback'  => 'hb_add_meta_boxes_gallery_setings' // callback arg render meta form
			),
			array()
		)->add_field(
			array(
				'name' => 'gallery',
				'type' => 'gallery'
			)
		);
	}
}

add_action( 'admin_init', 'hb_add_meta_boxes', 50 );

add_action( 'admin_init', 'hb_admin_init_metaboxes', 50 );
if ( ! function_exists( 'hb_admin_init_metaboxes' ) ) {
	function hb_admin_init_metaboxes() {
		$metaboxes = array(
			new WPHB_Admin_Metabox_Booking_Details(), // booking details
			new WPHB_Admin_Metabox_Booking_Actions(), // booking actions
			new WPHB_Admin_Metabox_Booking_Customer(), // booking customer
			new WPHB_Admin_Metabox_Room_Price(), // room price
		);

		return apply_filters( 'hb_admin_init_metaboxes', $metaboxes );
	}
}

if ( ! function_exists( 'hb_request_query' ) ) {

	function hb_request_query( $vars = array() ) {
		global $typenow, $wp_query, $wp_post_statuses;

		if ( 'hb_booking' === $typenow ) {
			// Status
			if ( ! isset( $vars['post_status'] ) ) {
				$post_statuses = hb_get_booking_statuses();

				foreach ( $post_statuses as $status => $value ) {
					if ( isset( $wp_post_statuses[ $status ] ) && false === $wp_post_statuses[ $status ]->show_in_admin_all_list ) {
						unset( $post_statuses[ $status ] );
					}
				}

				$vars['post_status'] = array_keys( $post_statuses );
			}
		}

		return $vars;
	}
}

add_filter( 'request', 'hb_request_query' );

if ( ! function_exists( 'hb_edit_post_change_title_in_list' ) ) {
	function hb_edit_post_change_title_in_list() {
		add_filter( 'the_title', 'hb_edit_post_new_title_in_list', 100, 2 );
	}
}

add_action( 'admin_head-edit.php', 'hb_edit_post_change_title_in_list' );

if ( ! function_exists( 'hb_edit_post_new_title_in_list' ) ) {

	function hb_edit_post_new_title_in_list( $title, $post_id ) {
		global $post_type;
		if ( $post_type == 'hb_booking' ) {
			$title = hb_format_order_number( $post_id );
		}

		return $title;
	}
}

if ( ! function_exists( 'hb_admin_js_template' ) ) {

	function hb_admin_js_template() {
		?>
        <script type="text/html" id="tmpl-room-type-gallery">
            <tr id="room-gallery-{{data.id}}" class="room-gallery">
                <td colspan="{{data.colspan}}">
                    <div class="hb-room-gallery">
                        <ul>
                            <# jQuery.each(data.gallery, function(){ var attachment = this; #>
                                <li class="attachment">
                                    <div class="attachment-preview">
                                        <div class="thumbnail">
                                            <div class="centered">
                                                <img src="{{attachment.src}}" alt="">
                                                <input type="hidden" name="hb-gallery[{{data.id}}][gallery][]"
                                                       value="{{attachment.id}}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="dashicons dashicons-trash"
                                       title="<?php _e( 'Remove this image', 'wp-hotel-booking' ); ?>"></a>
                                </li>
                                <# }); #>
                                    <li class="attachment add-new">
                                        <div class="attachment-preview">
                                            <div class="thumbnail">
                                                <div class="dashicons-plus dashicons">
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                        </ul>
                    </div>
                    <input type="hidden" name="hb-gallery[{{data.id}}][id]" value="{{data.id}}"/>
                </td>
            </tr>
        </script>
        <script type="text/html" id="tmpl-room-type-attachment">
            <li class="attachment">
                <div class="attachment-preview">
                    <div class="thumbnail">
                        <div class="centered">
                            <img src="{{data.src}}" alt="">
                            <input type="hidden" name="hb-gallery[{{data.gallery_id}}][gallery][]" value="{{data.id}}"/>
                        </div>
                    </div>
                </div>
                <a class="dashicons dashicons-trash"
                   title="<?php _e( 'Remove this image', 'wp-hotel-booking' ); ?>"></a>
            </li>
        </script>
		<?php
	}
}

add_action( 'admin_print_scripts', 'hb_admin_js_template' );

if ( ! function_exists( 'hb_get_rooms' ) ) {
	/**
	 * get all of post have post type hb_room
	 */
	function hb_get_rooms() {
		$args = array(
			'post_type'      => 'hb_room',
			'posts_per_page' => - 1,
			'order'          => 'ASC',
			'orderby'        => 'title'
		);

		return get_posts( $args );
	}
}

add_action( 'hb_booking_detail_update_meta_box', 'hb_booking_detail_update_meta_box', 10, 3 );
if ( ! function_exists( 'hb_booking_detail_update_meta_box' ) ) {

	function hb_booking_detail_update_meta_box( $key, $value, $post_id ) {
		if ( ! ( get_post_type( $post_id ) == 'hb_booking' && $key == '_hb_booking_status' ) ) {
			return;
		}

		$status = sanitize_text_field( $value );
		remove_action( 'save_post', array( 'WPHB_Admin_Metabox_Booking_Actions', 'update' ) );

		$booking = WPHB_Booking::instance( $post_id );
		$booking->update_status( $status );

		add_action( 'save_post', array( 'WPHB_Admin_Metabox_Booking_Actions', 'update' ) );
	}

	add_action( 'hb_update_meta_box_gallery_settings', 'hb_update_meta_box_gallery' );
	if ( ! function_exists( 'hb_update_meta_box_gallery' ) ) {
		function hb_update_meta_box_gallery( $post_id ) {
			if ( get_post_type() !== 'hb_room' ) {
				return;
			}

			if ( empty( $_POST['_hb_gallery'] ) ) {
				update_post_meta( $post_id, '_hb_gallery', array() );
			}
		}
	}

	if ( is_admin() ) {
		if ( ! function_exists( 'hb_remove_revolution_slider_meta_boxes' ) ) {
		}
		function hb_remove_revolution_slider_meta_boxes() {

			remove_meta_box( 'mymetabox_revslider_0', 'hb_room', 'normal' );
			remove_meta_box( 'mymetabox_revslider_0', 'hb_booking', 'normal' );
			remove_meta_box( 'submitdiv', 'hb_booking', 'side' );
		}
	}

	add_action( 'do_meta_boxes', 'hb_remove_revolution_slider_meta_boxes' );
}
