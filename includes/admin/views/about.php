<?php
/**
 * Admin View: Addition packages setting page.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<div id="wphb-admin-about-wrapper">
    <div class="heading">
        <div class="about-text">
            <h1><?php echo __( 'Welcome to WP Hotel Booking ', 'wp-hotel-booking' ) . WPHB_VERSION; ?></h1>
            <p class="description">
				<?php _e( 'WP Hotel Booking is a complete hotel booking plugin for WordPress with full of professional features for a booking room system.', 'wp-hotel-booking' ); ?>
            </p>
        </div>
        <div class="badge">
            <img src="<?php echo esc_attr( WPHB_PLUGIN_URL . '/assets/images/badge.png' ); ?>"/>
        </div>
    </div>
    <div class="content">
        <div class="feature-section">
            <div class="changelog postbox">
                <h2 class="hndle"><?php _e( 'Changelog', 'wp-hotel-booking' ); ?></h2>
                <div class="inside">
					<?php include_once( WPHB_PLUGIN_PATH . '/changelog.html' ); ?>
                </div>
            </div>
        </div>
        <div class="feature-section">
            <div class="news">
                <h3><?php _e( 'What\'s news', 'wp-hotel-booking' ); ?></h3>
                <p><?php echo wp_kses( __( 'Always stay up-to-date with the latest version of WP Hotel Booking by checking our <a href="https://wordpress.org/plugins/wp-hotel-booking/#developers" target="_blank">change log</a> regularly.', 'wp-hotel-booking' ), array(
						'a' => array(
							'href'   => array(),
							'target' => array()
						)
					) ); ?></p>
            </div>
            <div class="documentation">
                <h3><?php _e( 'How to use', 'wp-hotel-booking' ); ?></h3>
                <p><?php echo wp_kses( __( 'Check out the plugin\'s <a href="https://thimpress.com/forums/forum/plugins/wp-hotel-booking/" target="_blank">documentation</a> if you need more information on how to use WP Hotel Booking.', 'wp-hotel-booking' ), array(
						'a' => array(
							'href'   => array(),
							'target' => array()
						)
					) ); ?></p>
            </div>
            <div class="get-support">
                <h3><?php _e( 'Get Support', 'wp-hotel-booking' ); ?></h3>
                <p><?php _e( 'Find help in our forum and get free updates when purchasing the PRO version which boats a lot more advanced features.', 'wp-hotel-booking' ); ?></p>
                <p>
                    <a class="button-primary" href="https://thimpress.com/forums/forum/plugins/wp-hotel-booking/"
                       target="_blank"><?php _e( 'Go to support forum', 'wp-hotel-booking' ); ?></a>
                </p>
            </div>
        </div>
    </div>
</div>
