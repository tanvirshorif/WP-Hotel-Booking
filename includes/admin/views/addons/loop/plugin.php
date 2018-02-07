<?php
/**
 * Admin View: Loop WP Hotel Booking add-on.
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

<li class="plugin-card" id="learn-press-plugin-<?php echo $add_on['slug']; ?>">
    <div class="plugin-card-top">
            <span class="plugin-icon">
	            <?php if ( ! is_array( $add_on['icons'] ) && $add_on['icons'] ) { ?>
                    <a href="<?php echo esc_url( $add_on['permarklink'] ); ?>">
                        <img src="<?php echo esc_url( $add_on['icons'] ); ?>">
                    </a>
	            <?php } else { ?>
                    <img src="<?php echo WPHB_Helper_Plugins::get_add_on_icon( $add_on['icons'] ); ?>">
	            <?php } ?>
            </span>
        <div class="name column-name">
            <h3 class="item-title"><?php echo $add_on['name']; ?></h3>
        </div>
        <div class="action-links">

        </div>
        <div class="desc column-description">
            <p><?php echo strip_tags( $add_on['short_description'] ); ?></p>
            <p class="authors"><?php printf( __( '<cite>By %s</cite>', 'wp-hotel-booking' ), $add_on['author'] ); ?></p>
        </div>
    </div>

    <div class="plugin-card-bottom">
        <div class="plugin-version">
			<?php echo __( 'Version: ', 'wp-hotel-booking' );
			if ( isset( $add_on['version'] ) ) {
				echo $add_on['version'];
			} else {
				echo '2.0';
			}
			?>
        </div>
        <div class="column-compatibility">
			<?php
			if ( ! empty( $add_on['tested'] ) && version_compare( substr( $GLOBALS['wp_version'], 0, strlen( $add_on['tested'] ) ), $add_on['tested'], '>' ) ) {
				echo '<span class="compatibility-untested">' . __( 'Untested with your version of WordPress', 'wp-hotel-booking' ) . '</span>';
			} elseif ( ! empty( $plugin['requires'] ) && version_compare( substr( $GLOBALS['wp_version'], 0, strlen( $add_on['requires'] ) ), $add_on['requires'], '<' ) ) {
				echo '<span class="compatibility-incompatible">' . wp_kses( __( '<strong>Incompatible</strong> with your version of WordPress', 'wp-hotel-booking' ), array( 'strong' => array() ) ) . '</span>';
			} else {
				echo '<span class="compatibility-compatible">' . wp_kses( __( '<strong>Compatible</strong> with your version of WordPress', 'wp-hotel-booking' ), array( 'strong' => array() ) ) . '</span>';
			}
			?>
        </div>
    </div>
</li>