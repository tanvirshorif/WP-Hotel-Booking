<?php
/**
 * WP Hotel Booking admin report author class.
 *
 * @class       WPHB_Admin_Tool_Feedback
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Tool_Feedback' ) ) {

	/**
	 * Class WPHB_Admin_Tool_Feedback.
	 */
	class WPHB_Admin_Tool_Feedback extends WPHB_Abstract_Tool {

		/**
		 * @var string
		 */
		protected $title = '';

		/**
		 * @var string
		 */
		protected $id = 'feedback';

		/**
		 * WPHB_Admin_Tool_Feedback constructor.
		 */
		public function __construct() {
			$this->title = __( 'Feedback', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Output.
		 */
		public function output() { ?>
            <form id="wphb-customer-feedback-form" name="wphb-customer-feedback-form" method="POST">
                <div class="header">
                    <h2><?php _e( 'Customer Feedback', 'wp-hotel-booking' ); ?></h2>
                    <p class="description"><?php _e( 'Send feedback to author plugin to improve product quality', 'wp-hotel-booking' ); ?></p>
                </div>
                <div class="main">
                    <div class="name">
                        <label for="customer-name"><?php _e( 'Full Name:', 'wp-hotel-booking' ); ?></label>
                        <input type="text" name="name" id="customer-name">
                    </div>
                    <div class="email">
                        <label for="customer-email"><?php _e( 'Email:', 'wp-hotel-booking' ); ?></label>
                        <input type="text" name="email" id="customer-email">
                    </div>
                    <div class="type">
                        <label for="feedback-type"><?php _e( 'Type:' ); ?></label>
                        <select type="text" name="type" id="feedback-type">
                            <option value="improve-process"><?php _e( 'Improve Feature', 'wp-hotel-booking' ); ?></option>
                            <option value="request-feature"><?php _e( 'Customization Request', 'wp-hotel-booking' ); ?></option>
                        </select>
                    </div>
                    <div class="content">
                        <label for="feedback-content"><?php _e( 'Message:' ); ?></label>
                        <textarea name="content" id="feedback-content" cols="30" rows="10"
                                  placeholder="<?php _e( 'Detail of Your feedback', 'wp-hotel-booking' ); ?>"></textarea>
                    </div>
                </div>
                <div class="footer">
                    <button class="button button-primary"
                            type="submit"><?php _e( 'Send feedback', 'wp-hotel-booking' ); ?></button>
                </div>
            </form>
		<?php }

	}

}

return new WPHB_Admin_Tool_Feedback();
