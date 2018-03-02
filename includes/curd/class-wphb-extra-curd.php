<?php

/**
 * WP Hotel Booking Room extra CURD class.
 *
 * @class       WPHB_Extra_CURD
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Extra_CURD' ) ) {
	/**
	 * Class WPHB_Extra_CURD.
	 *
	 * @since 2.0
	 */
	class WPHB_Extra_CURD extends WPHB_Abstract_CURD implements WPHB_Interface_CURD {

		/**
		 * @var array
		 */
		protected $_args = array();

		/**
		 * WPHB_Extra_CURD constructor.
		 */
		public function __construct() {
			$this->_args = array(
				'title'       => '',
				'description' => '',
				'price'       => '',
				'unit'        => '',
				'type'        => ''
			);
		}

		/**
		 * Create extra.
		 *
		 * @param object $extra
		 *
		 * @return bool
		 */
		public function create( &$extra ) {
			$extra = wp_parse_args( $extra, $this->_args );

			$id = wp_insert_post( array(
				'post_title'   => $extra['title'],
				'post_content' => $extra['description'],
				'post_type'    => WPHB_Extra_CPT,
				'post_status'  => 'publish'
			) );

			if ( $id ) {
				// update meta
				$this->_update_meta( $id, $extra );

				return $id;
			} else {
				return false;
			}
		}

		public function load( &$object ) {
			// TODO: Implement load() method.
		}

		public function delete( &$object ) {
			// TODO: Implement delete() method.
		}

		/**
		 * Update extra.
		 *
		 * @param object $extra
		 *
		 * @return bool|int|WP_Error
		 */
		public function update( &$extra ) {
			$extra = wp_parse_args( $extra, $this->_args );

			$id = $extra['id'];
			if ( $id && get_post_type( $id ) == WPHB_Extra_CPT ) {
				$update = wp_update_post(
					array(
						'ID'           => $id,
						'post_title'   => $extra['title'],
						'post_content' => $extra['description']
					), true );
				$this->_update_meta( $id, $extra );

				return $update;
			} else {
				return false;
			}
		}

		/**
		 * Get all room extra.
		 *
		 * @return array|null|object
		 */
		public static function get_extra() {
			global $wpdb;
			$query = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE `post_type` = %s ORDER BY %s ASC", WPHB_Extra_CPT, 'id' );

			return $wpdb->get_results( $query, OBJECT );
		}

		/**
		 * Update extra meta.
		 *
		 * @param $id
		 * @param array $meta
		 */
		private function _update_meta( $id, $meta = array() ) {
			$prefix = 'tp_hb_extra_room_';
			update_post_meta( $id, $prefix . 'price', $meta['price'] );
			update_post_meta( $id, $prefix . 'respondent_name', $meta['unit'] );
			update_post_meta( $id, $prefix . 'respondent', $meta['type'] );
		}
	}
}

new WPHB_Extra_CURD();