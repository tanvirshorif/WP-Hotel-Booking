<?php

/**
 * The template for displaying room thumbnail in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/thumbnail.php.
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

<?php
global $hb_room;

$gallery  = $hb_room->gallery;
$featured = $gallery ? array_shift( $gallery ) : false;
?>

<div class="media">
    <a href="<?php the_permalink(); ?>">
		<?php $hb_room->getImage( 'catalog' ); ?>
    </a>
</div>