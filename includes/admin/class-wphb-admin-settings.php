<?php

/**
 * WP Hotel Booking admin settings class.
 *
 * @class       WPHB_Admin_Settings
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Admin_Settings' ) ) {

	/**
	 * Class WPHB_Admin_Settings.
	 *
	 * @since 2.0
	 */
	class WPHB_Admin_Settings {

		/**
		 * Get admin setting page tabs.
		 *
		 * @since 2.0
		 *
		 * @return array
		 */
		public static function get_settings_pages() {

			$tabs = array();

			// use WP_Hotel_Booking::instance() return null active hook
			$tabs[] = include 'settings/class-wphb-admin-setting-general.php';
			$tabs[] = include 'settings/class-wphb-admin-setting-hotel-info.php';
			$tabs[] = include 'settings/class-wphb-admin-setting-lightboxs.php';
			$tabs[] = include 'settings/class-wphb-admin-setting-emails.php';
			$tabs[] = include 'settings/class-wphb-admin-setting-payments.php';
			$tabs[] = include 'settings/class-wphb-admin-setting-room.php';
			$tabs[] = include 'settings/class-wphb-admin-setting-currencies.php';

			return apply_filters( 'hotel_booking_admin_setting_pages', $tabs );
		}

		// output page settings
		public static function output() {
			self::get_settings_pages();
			$tabs         = hb_admin_settings_tabs();
			$selected_tab = ! empty( $_REQUEST['tab'] ) ? sanitize_text_field( $_REQUEST['tab'] ) : '';

			if ( ! array_key_exists( $selected_tab, $tabs ) ) {
				$tab_keys     = array_keys( $tabs );
				$selected_tab = reset( $tab_keys );
			}

			?>
            <div class="wrap">
                <h2 class="nav-tab-wrapper">
					<?php if ( $tabs ) :
						foreach ( $tabs as $slug => $title ) { ?>
                            <a class="nav-tab<?php echo sprintf( '%s', $selected_tab == $slug ? ' nav-tab-active' : '' ); ?>"
                               href="?page=wphb-settings&tab=<?php echo esc_attr( $slug ); ?>">
								<?php echo esc_html( $title ); ?>
                            </a>
						<?php } endif; ?>
                </h2>
                <form method="post" action="" enctype="multipart/form-data" name="hb-admin-settings-form">
					<?php do_action( 'hb_admin_settings_tab_before', $selected_tab ); ?>
					<?php do_action( 'hb_admin_settings_sections_' . $selected_tab ); ?>
					<?php do_action( 'hb_admin_settings_tab_' . $selected_tab ); ?>
					<?php wp_nonce_field( 'hb_admin_settings_tab_' . $selected_tab, 'hb_admin_settings_tab_' . $selected_tab . '_field' ); ?>
					<?php do_action( 'hb_admin_settings_tab_after', $selected_tab ); ?>
                    <div class="clearfix"></div>
                    <p class="clearfix">
                        <button class="button button-primary"><?php _e( 'Update', 'wp-hotel-booking' ); ?></button>
                    </p>

                </form>
            </div>
			<?php
		}

	}

}