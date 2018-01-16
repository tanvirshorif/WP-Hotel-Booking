<?php
/**
 * Admin View: Installed plugins.
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

<?php
// get all plugins installed
$add_ons = WPHB_Helper_Plugins::get_plugins( 'installed' );

if ( ! $add_ons ) {
	echo __( 'There is no WP Hotel Booking add-ons has installed', 'wp-hotel-booking' );
} else { ?>
	<?php if ( is_array( $add_ons ) ) { ?>
        <h2><?php echo __( 'Installed add-ons', 'wp-hotel-booking' ); ?></h2>

        <ul>
			<?php foreach ( $add_ons as $file => $add_on ) {
				hb_admin_view( 'addons/loop/plugin' );
			} ?>
        </ul>
		<?php
	}
}