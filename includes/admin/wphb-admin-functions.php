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


if ( ! function_exists( 'hb_get_admin_view' ) ) {
	/**
	 * @param $name
	 * @param null $plugin_file
	 *
	 * @return mixed
	 */
	function hb_get_admin_view( $name, $plugin_file = null ) {
		if ( ! preg_match( '/\.(.*)$/', $name ) ) {
			$name .= '.php';
		}
		if ( $plugin_file ) {
			$view = dirname( $plugin_file ) . '/includes/admin/views/' . $name;
		} else {
			$view = WPHB_PLUGIN_PATH . '/includes/admin/views/' . $name;
		}

		return apply_filters( 'hb_admin_view', $view, $name );
	}
}

if ( ! function_exists( 'hb_admin_view' ) ) {
	/**
	 * @param $name
	 * @param array $args
	 * @param bool $include_once
	 * @param bool $return
	 *
	 * @return bool|string
	 */
	function hb_admin_view( $name, $args = array(), $include_once = false, $return = false ) {
		$view = hb_get_admin_view( $name, ! empty( $args['plugin_file'] ) ? $args['plugin_file'] : null );
		if ( file_exists( $view ) ) {
			ob_start();
			// extract parameters as local variables if passed
			is_array( $args ) && extract( $args );
			do_action( 'hb_before_display_admin_view', $name, $args );
			if ( $include_once ) {
				include_once $view;
			} else {
				include $view;
			}
			do_action( 'hb_after_display_admin_view', $name, $args );
			$output = ob_get_clean();
			if ( ! $return ) {
				echo $output;
			}

			return $return ? $output : true;
		}

		return false;
	}
}

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

if ( ! function_exists( 'wphb_get_admin_tools_tabs' ) ) {
	/**
	 * Get admin tool tabs.
	 *
	 * @return mixed
	 */
	function wphb_get_admin_tools_tabs() {
		return apply_filters( 'wphb/admin/tool-tabs', array() );
	}
}

if ( ! function_exists( 'hb_admin_js' ) ) {
	/**
	 * Print admin scripts.
	 *
	 * @return mixed
	 */
	function hb_admin_js() {
		$js = array(
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
			'remove_image'                  => __( 'Remove this image', 'wp-hotel-booking' ),
			'date_range'                    => __( 'Date range', 'wp-hotel-booking' ),
			'select_extra_placeholder'      => __( 'Select package', 'wp-hotel-booking' )
		);

		return apply_filters( 'hb_admin_js', $js );
	}
}

add_action( 'admin_footer', 'hb_admin_footer_advertisement', - 10 );

if ( ! function_exists( 'hb_admin_footer_advertisement' ) ) {

	function hb_admin_footer_advertisement() {

		$post_types = apply_filters( 'hb_post_types_footer_advertisement', array(
			'hb_room',
			'hb_booking'
		) );

		$pages = apply_filters( 'hb_pages_footer_advertisement', array(
			'wp-hotel-booking_page_wphb-settings',
			'wp-hotel-booking_page_wphb-about',
			'wp-hotel-booking_page_wphb-about',
			'wp-hotel-booking_page_wphb-addition-packages',
			'wp-hotel-booking_page_wphb-pricing-table'
		) );

		if ( ! $screen = get_current_screen() ) {
			return;
		}

		if ( ! ( ( in_array( $screen->post_type, $post_types ) && $screen->base === 'edit' ) || ( in_array( $screen->id, $pages ) ) ) ) {
			return;
		}

		$current_theme = wp_get_theme();

		// Get items
		$list_themes = (array) WPHB_Helper_Plugins::get_related_themes();
		if ( empty ( $list_themes ) ) {
			return;
		}

		if ( false !== ( $key = array_search( $current_theme->name, array_keys( $list_themes ), true ) ) ) {
			unset( $list_themes[ $key ] );
		}

		shuffle( $list_themes ); ?>

		<?php if ( $list_themes ) { ?>
            <div id="wphb-advertisement" class="wphb-advertisement-slider">
				<?php foreach ( $list_themes as $theme ) {
					if ( empty( $theme['url'] ) ) {
						continue;
					}
					$full_description  = hb_trim_content( $theme['description'] );
					$short_description = hb_trim_content( $theme['description'], 75 );
					$url_demo          = $theme['attributes'][4]['value']; ?>

                    <div id="thimpress-<?php echo esc_attr( $theme['id'] ); ?>" class="slide-item">
                        <div class="slide-thumbnail">
                            <a target="_blank" href="<?php echo esc_url( $theme['url'] ); ?>">
                                <img src="<?php echo esc_url( $theme['previews']['landscape_preview']['landscape_url'] ) ?>"/>
                            </a>
                        </div>

                        <div class="slide-detail">
                            <h2><a href="<?php echo esc_url( $theme['url'] ); ?>"><?php echo $theme['name']; ?></a></h2>
                            <p class="slide-description description-full">
								<?php echo wp_kses_post( $full_description ); ?>
                            </p>
                            <p class="slide-description description-short">
								<?php echo wp_kses_post( $short_description ); ?>
                            </p>
                            <p class="slide-controls">
                                <a href="<?php echo esc_url( $theme['url'] ); ?>" class="button button-primary"
                                   target="_blank"><?php _e( 'Get it now', 'wp-hotel-booking' ); ?></a>
                                <a href="<?php echo esc_url( $url_demo ); ?>" class="button"
                                   target="_blank"><?php _e( 'View Demo', 'wp-hotel-booking' ); ?></a>
                            </p>
                        </div>

                    </div>
				<?php } ?>
            </div>
		<?php }
	}
}

if ( ! function_exists( 'hb_trim_content' ) ) {
	/**
	 * @param $content
	 * @param int $count
	 *
	 * @return array|mixed|null|string|string[]
	 */
	function hb_trim_content( $content, $count = 0 ) {
		$content = preg_replace( '/(?<=\S,)(?=\S)/', ' ', $content );
		$content = str_replace( "\n", ' ', $content );
		$content = explode( " ", $content );

		$count = $count > 0 ? $count : sizeof( $content ) - 1;
		$full  = $count >= sizeof( $content ) - 1;

		$content = array_slice( $content, 0, $count );
		$content = implode( " ", $content );
		if ( ! $full ) {
			$content .= '...';
		}

		return $content;
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
				'except'  => sprintf( wp_kses( __( '<i>You need create <a href="%s" target="_blank">room capacities</a> to select number of adults</i>', 'wp-hotel-booking' ),
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
				'label'   => __( 'Addition Package', 'wp-hotel-booking' ),
				'desc'    => __( 'Room extra services', 'wp-hotel-booking' ),
				'type'    => 'multiple',
				'std'     => '',
				'options' => hb_room_extra_options()
			),
			array(
				'name'  => 'gallery',
				'label' => __( 'Gallery Images', 'wp-hotel-booking' ),
				'desc'  => __( 'Room gallery images', 'wp-hotel-booking' ),
				'type'  => 'gallery'
			)
		);
	}
}

add_action( 'admin_init', 'hb_add_meta_boxes', 50 );

add_action( 'admin_init', 'hb_admin_init_metaboxes', 50 );
if ( ! function_exists( 'hb_admin_init_metaboxes' ) ) {
	function hb_admin_init_metaboxes() {
		$metaboxes = array(
			new WPHB_Metabox_Booking_Editor(),
			new WPHB_Metabox_Booking_Actions(), // booking actions
			new WPHB_Metabox_Room_Price(), // room price
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
		remove_action( 'save_post', array( 'WPHB_Metabox_Booking_Actions', 'update' ) );

		$booking = WPHB_Booking::instance( $post_id );
		$booking->update_status( $status );

		add_action( 'save_post', array( 'WPHB_Metabox_Booking_Actions', 'update' ) );
	}

	add_action( 'hb_update_meta_box_gallery_settings', 'hb_update_meta_box_gallery' );
	if ( ! function_exists( 'hb_update_meta_box_gallery' ) ) {
		function hb_update_meta_box_gallery( $post_id ) {
			if ( get_post_type() !== 'hb_room' ) {
				return;
			}

			if ( ! $_POST['_hb_gallery'] || empty( $_POST['_hb_gallery'] ) ) {
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

if ( ! function_exists( 'wphb_get_screen_ids' ) ) {
	/**
     * Get all screen ids.
     *
	 * @return mixed
	 */
	function wphb_get_screen_ids() {
		$wphb_screen_id = sanitize_title( __( 'WP-Hotel-Booking', 'wp-hotel-booking' ) );
		$screen_id      = array(
			'toplevel_page_' . $wphb_screen_id,
			$wphb_screen_id . '_page_wphb-addition-packages',
			$wphb_screen_id . '_page_wphb-pricing-table',
			$wphb_screen_id . '_page_wphb-addons',
			$wphb_screen_id . '_page_wphb-settings',
			$wphb_screen_id . '_page_wphb-about',
			$wphb_screen_id . '_page_wphb-about',
			$wphb_screen_id . '_page_wphb-tools',
			WPHB_Room_CPT,
			'hb_room',
			'edit-hb_room',
			'hb_booking',
			'edit-hb_booking',
			'edit-hb_room_capacity',
			'edit-hb_room_type',
			'edit-hb_room_location'
		);

		return apply_filters( 'wphb_screen_ids', $screen_id );
	}
}
