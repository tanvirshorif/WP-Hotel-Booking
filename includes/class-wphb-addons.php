<?php

/**
 * WP Hotel Booking add-ons class.
 *
 * @class       WPHB_Ajax
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Addons' ) ) {
	/**
	 * Class WPHB_Addons.
	 */
	class WPHB_Addons {

		/**
		 * Get add-ons tabs.
		 *
		 * @return mixed
		 */
		public static function get_addon_tabs() {
			$tabs = array(
				'free'    => __( 'Free', 'wp-hotel-booking' ),
				'premium' => __( 'Premium', 'wp-hotel-booking' ),
				'themes'  => __( 'Themes', 'wp-hotel-booking' )
			);

			return apply_filters( 'wphb_admin_add_on_tabs', $tabs );
		}

		/**
		 * Output add-ons page.
		 */
		public static function output() {
			$tabs         = self::get_addon_tabs();
			$selected_tab = ! empty( $_REQUEST['tab'] ) ? sanitize_text_field( $_REQUEST['tab'] ) : '';

			if ( ! array_key_exists( $selected_tab, $tabs ) ) {
				$tab_keys     = array_keys( $tabs );
				$selected_tab = reset( $tab_keys );
			} ?>

            <div id='wphb-admin-addons-wrapper' class="wrap">
                <h2 class="nav-tab-wrapper">
					<?php if ( is_array( $tabs ) && $tabs ) { ?>
						<?php foreach ( $tabs as $slug => $title ) { ?>
                            <a class="nav-tab<?php echo sprintf( '%s', $selected_tab == $slug ? ' nav-tab-active' : '' ); ?>"
                               href="?page=wphb-addonss&tab=<?php echo esc_attr( $slug ); ?>"
                               data-tab="<?php echo esc_attr( $slug ); ?>">
								<?php echo esc_html( $title ); ?>
                            </a>
						<?php } ?>
					<?php } ?>
                </h2>
				<?php if ( is_array( $tabs ) && $tabs ) { ?>
					<?php foreach ( $tabs as $slug => $title ) { ?>
                        <div class="admin-addons-tab-content" id="addons-<?php echo esc_attr( $slug ); ?>"
                             style="<?php echo $selected_tab !== $slug ? 'display: none' : ''; ?>">
							<?php hb_admin_view( 'addons/' . $slug ); ?>
                        </div>
					<?php } ?>
				<?php } ?>
            </div>
			<?php
		}
	}
}