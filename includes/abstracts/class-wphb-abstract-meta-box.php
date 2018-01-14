<?php

/**
 * Abstract WP Hotel Booking meta box class.
 *
 * @class       WPHB_Abstract_Meta_Box
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Abstract Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Abstract_Meta_Box' ) ) {
	/**
	 * Class WPHB_Abstract_Meta_Box.
	 *
	 * @since 2.0
	 */
	abstract class WPHB_Abstract_Meta_Box {
		/**
		 * @var null
		 */
		protected $id = null;

		/**
		 * @var string|void
		 */
		public $title = '';

		/**
		 * @var string
		 */
		protected $context = 'advanced';

		/**
		 * @var string
		 */
		protected $screen = '';

		/**
		 * @var string
		 */
		public $priority = 'high';

		/**
		 * @var null
		 */
		protected $view = null;

		/**
		 * @var null
		 */
		public $callback_args = null;

		/**
		 * WPHB_Abstract_Meta_Box constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		}

		/**
		 * Add meta box.
		 *
		 * @since 2.0
		 */
		public function add_meta_box() {
			if ( ! $this->id || ! $this->screen || ! $this->view ) {
				return;
			}
			add_meta_box( $this->id, $this->title, array(
				$this,
				'meta_box_view'
			), $this->screen, $this->context, $this->priority, $this->callback_args );
		}

		/**
		 * Get meta box view.
		 *
		 * @since 2.0
		 *
		 * @param $post
		 */
		public function meta_box_view( $post ) {
			if ( is_array( $this->view ) ) {
				foreach ( $this->view as $view ) {
					hb_admin_view( "metaboxes/{$view}", array(), true );
				}
			} else {
				hb_admin_view( "metaboxes/$this->view", array(), true );
			}
		}
	}
}