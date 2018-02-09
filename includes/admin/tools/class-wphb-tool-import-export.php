<?php
/**
 * WP Hotel Booking admin import/export class.
 *
 * @class       WPHB_Admin_Tool_Import_Export
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();


if ( ! class_exists( 'WPHB_Admin_Tool_Import_Export' ) ) {

	/**
	 * Class WPHB_Admin_Tool_Import_Export.
	 */
	class WPHB_Admin_Tool_Import_Export extends WPHB_Abstract_Tool {

		/**
		 * @var string
		 */
		protected $title = '';

		/**
		 * @var string
		 */
		protected $id = 'import_export';

		/**
		 * WPHB_Admin_Tool_Import_Export constructor.
		 */
		public function __construct() {
			$this->title = __( 'Import/Export', 'wp-hotel-booking' );
			parent::__construct();
		}

		/**
		 * Output.
		 */
		public function output() {
			echo 'zzz';
		}

	}

}

return new WPHB_Admin_Tool_Import_Export();
