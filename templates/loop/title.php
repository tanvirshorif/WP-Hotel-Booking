<?php

/**
 * The template for displaying room title in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/title.php.
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

<div class="title">
    <h4>
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h4>
</div>
