<?php
/**
 * WP Hotel Booking admin system status class.
 *
 * @class       WPHB_Admin_Tool_System_Status
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Tool_System_Status' ) ) {

	/**
	 * Class WPHB_Admin_Tool_System_Status.
	 */
	class WPHB_Admin_Tool_System_Status extends WPHB_Abstract_Tool {

		/**
		 * @var string
		 */
		protected $title = '';

		/**
		 * @var string
		 */
		protected $id = 'system_status';

		/**
		 * WPHB_Admin_Tool_System_Status constructor.
		 */
		public function __construct() {
			$this->title = __( 'System Status', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Output.
		 */
		public function output() { ?>
            <div id="wphb-developer-access" class="wphb-admin-panel">
                <h2><?php _e( 'Developer access', 'wp-hotel-booking' ); ?></h2>
                <form method="post">
					<?php wp_nonce_field( 'wphb_developer_access', 'wphb_developer_access' ); ?>
					<?php if ( WPHB_Helper_For_Developer::is_granted() ) { ?>
						<?php $link_access = WPHB_Helper_For_Developer::get_link_access(); ?>
                        <input type="hidden" name="wphb-revoke-developer-access" value="1" title="revoke">
                        <button class="button button-secondary" type="submit">
							<?php esc_html_e( 'Revoke developer access', 'wp-hotel-booking' ); ?>
                        </button>
                        <button class="button button-primary copy-developer-access-link">
							<?php esc_html_e( 'Copy link', 'wp-hotel-booking' ); ?>
                        </button>
                        <div class="link"><textarea id="wpbh-link-developer-access" class="widefat" title="link"
                                                    rows="1" readonly><?php echo esc_url( $link_access ); ?></textarea>
                        </div>
					<?php } else { ?>
                        <input type="hidden" name="wphb-grant-developer-access" value="1" title="grant">
                        <button class="button button-primary"
                                type="submit"><?php esc_html_e( 'Allow developer access', 'wp-hotel-booking' ); ?></button>
					<?php } ?>
                </form>
            </div>

            <div id="wphb-db-checker" class="wphb-admin-panel">
                <h2><?php _e( 'Database', 'wp-hotel-booking' ); ?></h2>
                <button class="button button-secondary" id="check_db_status" type="submit">
					<?php esc_html_e( 'Force Database', 'wp-hotel-booking' ); ?></button>
                <p><i><?php _e( 'Check up to date and force update database.' ); ?></i></p>
            </div>

		<?php }
	}
}

return new WPHB_Admin_Tool_System_Status();
