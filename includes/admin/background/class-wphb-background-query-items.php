<?php

/**
 * WP Hotel Booking background query items class.
 *
 * @class       WPHB_Background_Query_Items
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WP_Async_Request', false ) ) {
	include_once WPHB_INCLUDES . 'libraries/wp-async-request.php';
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
	include_once WPHB_INCLUDES . 'libraries/wp-background-process.php';
}

if ( ! class_exists( 'WPHB_Background_Query_Items' ) ) {
	/**
	 * Class WPHB_Background_Query_Items.
	 */
	class WPHB_Background_Query_Items extends WP_Background_Process {

		/**
		 * @var int
		 */
		protected $queue_lock_time = 60;

		/**
		 * @var string
		 */
		protected $action = 'wphb_background';

		/**
		 * WPHB_Background_Query_Items constructor.
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'shutdown', array( $this, 'dispatch_queue' ) );
		}

		/**
		 * Dispatch.
		 */
		public function dispatch_queue() {
			if ( ! empty( $this->data ) ) {

				$this->save()->dispatch();
			}
		}

		/**
		 * @param mixed $data
		 *
		 * @return bool|mixed
		 */
		protected function task( $data ) {

			if ( ! isset( $data['callback'] ) || ! is_callable( $data['callback'] ) ) {
				return false;
			}

			call_user_func( $data['callback'] );

			return false;
		}

		/**
		 * Query WPHB free add-ons.
		 *
		 * @return array
		 */
		public static function query_free_addons() {
			WPHB_Helper_Plugins::require_plugins_api();
			// query plugin args
			$args    = array(
				'page'              => 1,
				'per_page'          => 20,
				'fields'            => array(
					'last_updated'    => true,
					'icons'           => true,
					'active_installs' => true
				),
				'locale'            => get_locale(),
				'installed_plugins' => WPHB_Helper_Plugins::get_installed_plugin_slugs(),
				'author'            => 'thimpress'
			);
			$plugins = array();
			try {
				$query = plugins_api( 'query_plugins', $args );

				if ( is_wp_error( $query ) ) {
					throw new Exception( __( 'WP Query plugins error', 'wp-hotel-booking' ) );
				}
				if ( ! is_array( $query->plugins ) ) {
					throw new Exception( __( 'WP Query plugins empty', 'wp-hotel-booking' ) );
				}
				$all_plugins = get_plugins();

				// Filter plugins with tag contains 'wphb'
				$_plugins = array_filter( $query->plugins, array( 'WPHB_Helper_Plugins', 'filter_plugins' ) );
				// Ensure that the array is indexed from 0
				$_plugins = array_values( $_plugins );
				for ( $total = sizeof( $_plugins ), $i = $total - 1; $i >= 0; $i -- ) {
					$plugin = $_plugins[ $i ];
					$key    = $plugin->slug;
					foreach ( $all_plugins as $file => $_plugin ) {
						if ( strpos( $file, $plugin->slug ) !== false ) {
							$key = $file;
							break;
						}
					}
					$plugin->source  = 'wp';
					$plugins[ $key ] = (array) $plugin;
				}

				// Cache in a half of day
				set_transient( 'wphb_plugins_wp', $plugins, DAY_IN_SECONDS / 2 );
			} catch ( Exception $exception ) {
			}

			return $plugins;
		}

		/**
		 * Query WPHB themes on themeforest.net.
		 *
		 * @return array|bool
		 */
		public static function get_related_themes() {
			$themes   = array();
			$url      = 'https://api.envato.com/v1/discovery/search/search/item?site=themeforest.net&username=thimpress';
			$args     = array(
				'headers' => array(
					"Authorization" => "Bearer BmYcBsYXlSoVe0FekueDxqNGz2o3JRaP"
				)
			);
			$response = wp_remote_request( $url, $args );

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, true );

			if ( ! empty( $response ) && ! empty( $response['matches'] ) ) {
				$all_themes = array();
				foreach ( $response['matches'] as $theme ) {
					$all_themes[ $theme['id'] ] = $theme;
				}


				if ( $wphb_themes = self::wphb_themeforest_themes() ) {
					foreach ( $all_themes as $theme ) {
						if ( in_array( $theme['id'], array_keys( $wphb_themes ) ) ) {
							$related[] = $theme;
						}
					}
				} else {
					$related = $all_themes;
				}
				set_transient( 'wphb_related_themes', $related, DAY_IN_SECONDS / 2 );
			}

			return $themes;
		}

		/**
		 * Schedule event
		 */
		protected function schedule_event() {
			if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
				wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
			}
		}


		/**
		 * Get all WP Hotel Booking Themeforest.net themes, do not allow third-party hook.
		 *
		 * @return array
		 */
		private static function wphb_themeforest_themes() {
			return array(
				'18828322' => 'hotel-wp',
				'13321455' => 'sailing',
				'21070438' => 'magazette'
			);
		}
	}
}