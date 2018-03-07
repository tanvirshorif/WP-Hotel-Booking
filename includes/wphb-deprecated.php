<?php

/**
 * WP Hotel Booking deprecated functions.
 *
 * @version     2.0
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Functions
 * @category    Core Functions
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


//========================================== Booking Functions ===========================================//

function hb_get_order_items() {
	_deprecated_function( __FUNCTION__, '2.0', 'hb_get_booking_items' );
}

function hb_add_order_item() {
	_deprecated_function( __FUNCTION__, '2.0', 'hb_add_booking_item' );
}

function hb_update_order_item() {
	_deprecated_function( __FUNCTION__, '2.0', 'hb_update_booking_item' );
}

function hb_remove_order_item() {
	_deprecated_function( __FUNCTION__, '2.0', 'hb_remove_booking_item' );
}

function hb_get_parent_order_item() {
	_deprecated_function( __FUNCTION__, '2.0', 'hb_get_parent_booking_item' );
}

function hb_get_sub_item_order_item_id() {
	_deprecated_function( __FUNCTION__, '2.0', 'hb_get_sub_item_booking_item_id' );
}

function hb_empty_order_items() {
	_deprecated_function( __FUNCTION__, '2.0', 'hb_empty_booking_items' );
}

function hb_add_order_item_meta() {
	_deprecated_function( __FUNCTION__, '2.0', 'hb_add_booking_item_meta' );
}

function hb_update_order_item_meta() {
	_deprecated_function( __FUNCTION__, '2.0', 'hb_update_booking_item_meta' );
}

function hb_delete_order_item_meta() {
	_deprecated_function( __FUNCTION__, '2.0', 'hb_delete_booking_item_meta' );
}

function hb_get_order_item_meta() {
	_deprecated_function( __FUNCTION__, '2.0', 'hb_get_booking_item_meta' );
}