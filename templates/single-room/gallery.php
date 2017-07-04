<?php

/**
 * The template for displaying single room gallery images.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room/gallery.php.
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

global $hb_room;
$galleries = $hb_room->get_galleries( false );
?>

<?php if ( $galleries ) { ?>
    <div class="hb_room_gallery camera_wrap camera_emboss" id="camera_wrap_<?php echo get_the_ID() ?>">
		<?php foreach ( $galleries as $key => $gallery ) { ?>
            <div data-thumb="<?php echo esc_url( $gallery['thumb'] ); ?>"
                 data-src="<?php echo esc_url( $gallery['src'] ); ?>"></div>
		<?php } ?>
    </div>
<?php } ?>