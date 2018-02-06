<?php

/**
 * WP Hotel Booking plugins helper class.
 *
 * @class       WPHB_Helper_Plugins
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Helper_Plugins' ) ) {
	/**
	 * Class WPHB_Helper_Plugins.
	 */
	class WPHB_Helper_Plugins {

		/**
		 * @var WPHB_Background_Query_Items
		 */
		protected static $_background_query_items = null;

		/**
		 * @var array
		 */
		public static $plugins = array(
			'installed' => false,
			'more'      => false
		);

		/**
		 * @var array
		 */
		public static $themes = array();

		/**
		 * Include plugin api.
		 */
		public static function require_plugins_api() {
			global $pagenow;

			if ( ! function_exists( 'plugins_api' ) && 'plugin-install.php' !== $pagenow ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
			}
		}

		/**
		 * Get WPHB plugins.
		 *
		 * @param string $type
		 *
		 * @return array|mixed
		 */
		public static function get_plugins( $type = '' ) {
			self::require_plugins_api();
			$plugins = array();

			if ( ! function_exists( 'get_plugins' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}

			// get installed all plugins
			$all_plugins = get_plugins();

			if ( ! $all_plugins ) {
				return array_key_exists( $type, self::$plugins ) ? self::$plugins['type'] : self::$plugins;
			}

			$wp_plugins   = self::get_plugins_from_wp();
			$wp_installed = array();

			foreach ( $all_plugins as $plugin_file => $plugin_data ) {

				// If there is a tag
				if ( empty( $plugin_data['Tags'] ) ) {
					continue;
				}

				$tags = ( preg_split( '/\s*,\s*/', $plugin_data['Tags'] ) );
				if ( ! in_array( 'wphb', $tags ) ) {
					continue;
				}

				$plugin_slug = dirname( $plugin_file );

				if ( isset( $wp_plugins[ $plugin_file ] ) ) {
					$plugins[ $plugin_file ]           = (array) $wp_plugins[ $plugin_file ];
					$plugins[ $plugin_file ]['source'] = 'wp';

					$wp_installed[ $plugin_file ] = true;
				} else {
					$plugin_data             = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
					$plugins[ $plugin_file ] = array(
						'name'              => $plugin_data['Name'],
						'slug'              => $plugin_slug,
						'version'           => $plugin_data['Version'],
						'author'            => sprintf( '<a href="%s">%s</a>', $plugin_data['AuthorURI'], $plugin_data['Author'] ),
						'author_profile'    => '',
						'contributors'      => array(),
						'homepage'          => $plugin_data['PluginURI'],
						'short_description' => $plugin_data['Description'],
						'icons'             => self::get_add_on_icons( $plugin_data, $plugin_file )
					);
					if ( ! empty( $plugin_data['Requires at least'] ) ) {
						$plugins[ $plugin_file ]['requires'] = $plugin_data['Requires at least'];
					}
					if ( ! empty( $plugin_data['Tested up to'] ) ) {
						$plugins[ $plugin_file ]['tested'] = $plugin_data['Tested up to'];
					}
					if ( ! empty( $plugin_data['Last updated'] ) ) {
						$plugins[ $plugin_file ]['last_updated'] = $plugin_data['Last updated'];
					}
				}
			}
			self::$plugins['installed'] = $plugins;

			if ( is_array( $wp_plugins ) ) {
				self::$plugins['free'] = array_diff_key( $wp_plugins, (array) $wp_installed );
			}

			// Sort plugins
			self::_sort_plugins();

			return array_key_exists( $type, self::$plugins ) ? self::$plugins[ $type ] : self::$plugins;
		}

		/**
		 * Get plugin icon.
		 *
		 * @param $plugin_data
		 * @param $plugin_file
		 *
		 * @return array
		 */
		public static function get_add_on_icons( $plugin_data, $plugin_file ) {
			$plugin_path = ABSPATH . 'wp-content/plugins/' . $plugin_file;
			$icon_path   = dirname( $plugin_path ) . '/assets/images';
			$icons       = array(
				'2x' => '',
				'1x' => ''
			);
			foreach ( array( '2x' => 'icon-256x256', '1x' => 'icon-128x128' ) as $s => $name ) {
				foreach ( array( 'png', 'svg' ) as $t ) {
					if ( file_exists( $icon_path . "/{$name}.{$t}" ) ) {
						$icons[ $s ] = plugins_url( '/', $plugin_path ) . "assets/images/{$name}.{$t}";
						break;
					}
				}
			}

			return $icons;
		}

		/**
		 * Sort plugins.
		 */
		public static function _sort_plugins() {
			foreach ( self::$plugins as $k => $plugin ) {
				if ( is_array( $plugin ) ) {
					ksort( $plugin );
					self::$plugins[ $k ] = $plugin;
				}
			}
		}

		/**
		 * Query the list of add-ons from wordpress.org with keyword 'wphb'
		 * This requires have a keyword named 'wphb' in plugin header Tags
		 *
		 * @return mixed
		 */
		public static function get_plugins_from_wp() {

			if ( ! ( $plugins = get_transient( 'lp_plugins_wp' ) ) ) {
				self::$_background_query_items->push_to_queue(
					array(
						'callback' => array( 'WPHB_Background_Query_Items', 'query_free_addons' )
					)
				);
			}

			return $plugins;
		}

		/**
		 * Get WP Hotel Booking theme from themeforest.net.
		 *
		 * @return mixed
		 */
		public static function get_related_themes() {

			if ( ! $themes = get_transient( 'wphb_related_themes' ) ) {
				self::$_background_query_items->push_to_queue(
					array(
						'callback' => array( 'WPHB_Background_Query_items', 'get_related_themes' )
					)
				);
			}

			return $themes;
		}

		/**
		 * Get installed plugin slugs.
		 *
		 * @return array
		 */
		public static function get_installed_plugin_slugs() {
			$slugs = array();

			$plugin_info = get_site_transient( 'update_plugins' );
			if ( isset( $plugin_info->no_update ) ) {
				foreach ( $plugin_info->no_update as $plugin ) {
					$slugs[] = $plugin->slug;
				}
			}

			if ( isset( $plugin_info->response ) ) {
				foreach ( $plugin_info->response as $plugin ) {
					$slugs[] = $plugin->slug;
				}
			}

			return $slugs;
		}

		/**
		 * Filter plugins have tag contains 'wphb'.
		 *
		 * @param $plugin
		 *
		 * @return bool
		 */
		public static function filter_plugins( $plugin ) {
			return $plugin && preg_match( '!^wphb-.*!', $plugin->slug );
		}

		/**
		 * Register extra headers for WPHB plugins.
		 *
		 * @param $headers
		 *
		 * @return array
		 */
		public static function addons_header( $headers ) {
			$headers['Tags']              = __( 'Tags', 'wp-hotel-booking' );
			$headers['Requires at least'] = __( 'Requires at least', 'wp-hotel-booking' );
			$headers['Tested up to']      = __( 'Tested up to', 'wp-hotel-booking' );
			$headers['Last updated']      = __( 'Last updated', 'wp-hotel-booking' );

			return $headers;
		}

		/**
		 * Init.
		 */
		public static function init() {
			add_filter( 'extra_plugin_headers', array( __CLASS__, 'addons_header' ) );

			self::$_background_query_items = new WPHB_Background_Query_Items();
		}
	}
}

// Init
WPHB_Helper_Plugins::init();