<?php

/**
 * The template for displaying room gallery lightbox in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/gallery-lightbox.php.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Templates
 * @category    Templates
 * @author      Thimpress, leehld
 */


/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php $gallery = $room->gallery; ?>

<?php if ( $gallery ) { ?>
    <div class="hb-room-type-gallery">
		<?php foreach ( $gallery as $image ) { ?>
            <a class="hb-room-gallery" data-fancybox-group="hb-room-gallery-<?php echo esc_attr( $room->post->ID ); ?>"
               data-lightbox="hb-room-gallery[<?php echo esc_attr( $room->post->ID ); ?>]"
               data-title="<?php echo esc_attr( $image['alt'] ); ?>" href="<?php echo esc_url( $image['src'] ); ?>">
                <img src="<?php echo esc_url( $image['thumb'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>"
                     data-id="<?php echo esc_attr( $image['id'] ); ?>"/>
            </a>
		<?php } ?>
    </div>
<?php } ?>