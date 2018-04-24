<?php

/**
 * The template for displaying search room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/search-page.php.
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

if ( hb_get_request( 'page' ) == 'select-room-extra' ) {

	hb_get_template( 'search/select-extra.php' );

} else {

	hb_get_template( 'search/search-form.php' );

}