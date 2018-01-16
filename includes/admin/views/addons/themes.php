<?php
/**
 * Admin View: WP Hotel Booking themes.
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
$themes = WPHB_Helper_Plugins::get_related_themes();

if ( ! $themes ) {
	echo __( 'There is no WP Hotel Booking themes available.', 'wp-hotel-booking' );
} else { ?>
	<?php if ( is_array( $themes ) ) { ?>
        <h2><?php echo __( 'Themes', 'wp-hotel-booking' ); ?></h2>

        <ul>
			<?php foreach ( $themes as $file => $theme ) {
				hb_admin_view( 'addons/loop/theme' );
			} ?>
        </ul>
		<?php
	}
}