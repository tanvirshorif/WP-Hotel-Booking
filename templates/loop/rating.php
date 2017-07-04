<?php

/**
 * The template for displaying room rating in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/rating.php.
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
$rating = $hb_room->average_rating();
?>

<?php if ( comments_open( $hb_room->ID ) ) { ?>

    <div class="rating">
		<?php if ( $rating ) { ?>
            <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating"
                 title="<?php echo sprintf( __( 'Rated %d out of 5', 'wp-hotel-booking' ), $rating ) ?>">
                <span style="width:<?php echo ( $rating / 5 ) * 100; ?>%"></span>
            </div>
		<?php } ?>
    </div>

<?php } ?>