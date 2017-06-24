<?php

/**
 * Abstract WP Hotel Booking admin setting class.
 *
 * @class       WPHB_Abstract_Setting
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Abstract Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Abstract_Setting' ) ) {

	/**
	 * Class WPHB_Abstract_Setting.
	 *
	 * @since 2.0
	 */
	abstract class WPHB_Abstract_Setting {

		/**
		 * Setting tab id.
		 *
		 * @var null
		 */
		protected $id = null;

		/**
		 * Setting tab title.
		 *
		 * @var null
		 */
		protected $title = null;

		/**
		 * WPHB_Abstract_Setting constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_filter( 'hb_admin_settings_tabs', array( $this, 'setting_tabs' ) );
			add_action( 'hb_admin_settings_sections_' . $this->id, array( $this, 'setting_sections' ) );
			add_action( 'hb_admin_settings_tab_' . $this->id, array( $this, 'output' ) );
		}

		/**
		 * Get setting fields.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function get_settings() {
			return apply_filters( 'hotel_booking_admin_setting_fields_' . $this->id, array() );
		}

		/**
		 * Get setting sections.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function get_sections() {
			return apply_filters( 'hotel_booking_admin_setting_sections_' . $this->id, array() );
		}

		/**
		 * Get setting tabs.
		 *
		 * @since 2.0
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function setting_tabs( $tabs ) {
			$tabs[ $this->id ] = $this->title;

			return $tabs;
		}

		/**
		 * Output setting page.
		 *
		 * @since 2.0
		 */
		public function output() {

			$settings = $this->get_settings();

			if ( empty( $settings ) ) {
				return;
			}
			foreach ( $settings as $k => $field ) {
				$field = wp_parse_args( $field, array(
					'id'          => '',
					'class'       => '',
					'title'       => '',
					'desc'        => '',
					'default'     => '',
					'type'        => '',
					'placeholder' => '',
					'options'     => '',
					'atts'        => array()
				) );

				$custom_attr = '';
				if ( ! empty( $field['atts'] ) ) {
					foreach ( $field['atts'] as $key => $val ) {
						$custom_attr .= $key . '="' . $val . '"';
					}
				}
				switch ( $field['type'] ) {
					case 'section_start':
						include( WPHB_ABSPATH . 'includes/admin/views/settings/fields/section-start.php' );
						break;
					case 'section_end':
						include( WPHB_ABSPATH . 'includes/admin/views/settings/fields/section-end.php' );
						break;
					case 'select':
					case 'multiselect':
						include( WPHB_ABSPATH . 'includes/admin/views/settings/fields/select.php' );
						break;
					case 'text':
					case 'number':
					case 'email':
					case 'password':
						include( WPHB_ABSPATH . 'includes/admin/views/settings/fields/text.php' );
						break;
					case 'checkbox':
						include( WPHB_ABSPATH . 'includes/admin/views/settings/fields/checkbox.php' );
						break;
					case 'radio':
						include( WPHB_ABSPATH . 'includes/admin/views/settings/fields/radio.php' );
						break;
					case 'image_size':
						include( WPHB_ABSPATH . 'includes/admin/views/settings/fields/image-size.php' );
						break;
					case 'textarea':
						include( WPHB_ABSPATH . 'includes/admin/views/settings/fields/textarea.php' );
						break;
					case 'select_page':
						include( WPHB_ABSPATH . 'includes/admin/views/settings/fields/select-page.php' );
						break;
					default:
						do_action( 'hotel_booking_setting_field_' . $field['id'], $field );
						break;
				}
			}
		}

		/**
		 * Filter section in tab id.
		 *
		 * @since 2.0
		 */
		public function setting_sections() {
			$sections = $this->get_sections();

			if ( count( $sections ) === 1 ) {
				return;
			}

			$current_section = null;

			if ( isset( $_REQUEST['section'] ) ) {
				$current_section = sanitize_text_field( $_REQUEST['section'] );
			}

			$html = array();

			$html[] = '<ul class="hb-admin-sub-tab subsubsub">';
			$sub    = array();
			foreach ( $sections as $id => $text ) {
				$sub[] = '<li>
						<a href="?page=wphb-settings&tab=' . $this->id . '&section=' . $id . '"' . ( $current_section === $id ? ' class="current"' : '' ) . '>' . esc_html( $text ) . '</a>
					</li>';
			}
			$html[] = implode( '&nbsp;|&nbsp;', $sub );
			$html[] = '</ul><br />';

			echo implode( '', $html );
		}

	}

}