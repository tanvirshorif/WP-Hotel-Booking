<?php

/**
 * Interface CURD.
 *
 * @interface   WPHB_Interface_CURD
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * Interface WPHB_Interface_CURD
 */
interface WPHB_Interface_CURD {

	/**
	 * Create item and insert to database.
	 *
	 * @param object $object
	 */
	public function create( &$object );

	/**
	 * Load data from database.
	 *
	 * @param object $object
	 */
	public function load( &$object );

	/**
	 * Update data into database.
	 *
	 * @param object $object
	 */
	public function update( &$object );

	/**
	 * Delete data from database.
	 *
	 * @param object $object
	 */
	public function delete( &$object );

	/**
	 * Add new meta data.
	 *
	 * @param $object
	 * @param $meta
	 */
	public function add_meta( &$object, $meta );

	/**
	 * Read meta data for passed object.
	 *
	 * @param $object
	 */
	public function read_meta( &$object );

	/**
	 * Update meta data.
	 *
	 * @param $object
	 * @param $meta
	 */
	public function update_meta( &$object, $meta );

	/**
	 * Delete meta data.
	 *
	 * @param $object
	 * @param $meta
	 */
	public function delete_meta( &$object, $meta );
}