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
		$js = apply_filters( 'hb_admin_js', array(
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
		) );

		return is_array( $js ) ? $js : array();
	}
}

if ( ! function_exists( 'hb_admin_room_meta_boxes' ) ) {
	/**
	 * Add admin meta box.
	 */
	function hb_admin_room_meta_boxes() {
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

if ( ! function_exists( 'hb_admin_init_metaboxes' ) ) {
	/**
	 * @return mixed
	 */
	function hb_admin_init_metaboxes() {
		$metaboxes = array(
			new WPHB_Metabox_Booking_Editor(),
			new WPHB_Metabox_Booking_Actions(), // booking actions
			new WPHB_Metabox_Room_Price(), // room price
		);

		return apply_filters( 'hb_admin_init_metaboxes', $metaboxes );
	}
}

if ( ! function_exists( 'hb_admin_js_template' ) ) {
	/**
	 * Admin js template.
	 */
	function hb_admin_js_template() { ?>
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


