<?php
/**
 * WP Hotel Booking Custom post type taxonomies class.
 *
 * @class       WPHB_Taxonomies_Custom_Post_Type
 * @version     2.0
 * @package     WP_Hotel_Booking/Classes
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Taxonomies_Custom_Post_Type' ) ) {
	/**
	 * Class WPHB_Taxonomies_Custom_Post_Type
	 */
	class WPHB_Taxonomies_Custom_Post_Type {

		/**
		 * WPHB_Taxonomies_Custom_Post_Type constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_taxonomies' ) );

			// update admin room capacity columns
			add_filter( 'manage_edit-hb_room_capacity_columns', array( $this, 'capacity_columns' ) );
			add_filter( 'manage_hb_room_capacity_custom_column', array( $this, 'capacity_column_content' ), 10, 3 );

			// add create room capacity fields
			add_action( 'hb_room_capacity_add_form_fields', array( $this, 'add_capacity_fields' ) );
			add_action( 'hb_room_capacity_edit_form_fields', array( $this, 'edit_capacity_fields' ) );
			// update create room capacity fields
			add_action( 'create_hb_room_capacity', array( $this, 'save_capacity_fields' ) );
			add_action( 'edited_hb_room_capacity', array( $this, 'save_capacity_fields' ) );

			add_action( 'admin_init', array( $this, 'update_taxonomy' ) );
			add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );

			add_filter( 'get_terms_orderby', array( $this, 'terms_orderby' ), 100, 3 );
			add_filter( 'get_terms_args', array( $this, 'terms_args' ), 100, 2 );
		}

		/**
		 * Register room taxonomies.
		 */
		public static function register_taxonomies() {
			// register room capacity
			$args = array(
				'hierarchical' => false,
				'label'        => __( 'Room Capacity', 'wp-hotel-booking' ),
				'labels'       => array(
					'name'              => __( 'Room Capacities', 'wp-hotel-booking' ),
					'singular_name'     => __( 'Room Capacity', 'wp-hotel-booking' ),
					'menu_name'         => _x( 'Room Capacities', 'Room Capacities', 'wp-hotel-booking' ),
					'search_items'      => __( 'Search Room Capacities', 'wp-hotel-booking' ),
					'all_items'         => __( 'All Room Capacity', 'wp-hotel-booking' ),
					'parent_item'       => __( 'Parent Room Capacity', 'wp-hotel-booking' ),
					'parent_item_colon' => __( 'Parent Room Capacity:', 'wp-hotel-booking' ),
					'edit_item'         => __( 'Edit Room Capacity', 'wp-hotel-booking' ),
					'update_item'       => __( 'Update Room Capacity', 'wp-hotel-booking' ),
					'add_new_item'      => __( 'Add New Room Capacity', 'wp-hotel-booking' ),
					'new_item_name'     => __( 'New Room Type Capacity', 'wp-hotel-booking' )
				),
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array(
					'slug'         => _x( 'room-capacity', 'URL slug', 'wp-hotel-booking' ),
					'with_front'   => false,
					'hierarchical' => true,
				),
				'capabilities' => array(
					'manage_terms' => 'manage_hb_booking',
					'edit_terms'   => 'manage_hb_booking',
					'delete_terms' => 'manage_hb_booking',
					'assign_terms' => 'manage_hb_booking'
				)
			);

			register_taxonomy( 'hb_room_capacity', array( 'hb_room' ), apply_filters( 'hb_register_tax_capacity_arg', $args ) );

			// register room category
			$args = array(
				'hierarchical' => true,
				'label'        => __( 'Room Type', 'wp-hotel-booking' ),
				'labels'       => array(
					'name'              => _x( 'Room Types', 'taxonomy general name', 'wp-hotel-booking' ),
					'singular_name'     => _x( 'Room Type', 'taxonomy singular name', 'wp-hotel-booking' ),
					'menu_name'         => _x( 'Room Types', 'Room Types', 'wp-hotel-booking' ),
					'search_items'      => __( 'Search Room Types', 'wp-hotel-booking' ),
					'all_items'         => __( 'All Room Types', 'wp-hotel-booking' ),
					'parent_item'       => __( 'Parent Room Type', 'wp-hotel-booking' ),
					'parent_item_colon' => __( 'Parent Room Type:', 'wp-hotel-booking' ),
					'edit_item'         => __( 'Edit Room Type', 'wp-hotel-booking' ),
					'update_item'       => __( 'Update Room Type', 'wp-hotel-booking' ),
					'add_new_item'      => __( 'Add New Room Type', 'wp-hotel-booking' ),
					'new_item_name'     => __( 'New Room Type Name', 'wp-hotel-booking' )
				),
				'public'       => true,
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array( 'slug' => _x( 'room-type', 'URL slug', 'wp-hotel-booking' ) ),
				'capabilities' => array(
					'manage_terms' => 'manage_hb_booking',
					'edit_terms'   => 'manage_hb_booking',
					'delete_terms' => 'manage_hb_booking',
					'assign_terms' => 'manage_hb_booking'
				)
			);

			register_taxonomy( 'hb_room_type', array( 'hb_room' ), apply_filters( 'hb_register_tax_room_type_arg', $args ) );
		}

		/**
		 * @param $orderby
		 * @param $args
		 * @param $taxonomies
		 *
		 * @return string
		 */
		public function terms_orderby( $orderby, $args, $taxonomies ) {
			if ( in_array( hb_get_request( 'taxonomy' ), array( 'hb_room_type', 'hb_room_capacity' ) ) ) {
				$orderby = 'term_group';
			}

			return $orderby;
		}

		/**
		 * @param $args
		 * @param $taxonomies
		 *
		 * @return mixed
		 */
		public function terms_args( $args, $taxonomies ) {
			if ( in_array( hb_get_request( 'taxonomy' ), array( 'hb_room_type', 'hb_room_capacity' ) ) ) {
				$args['order'] = 'ASC';
			}

			return $args;
		}

		/**
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function capacity_columns( $columns ) {
			$columns['capacity'] = __( 'Capacity', 'wp-hotel-booking' );
			$columns['ordering'] = __( 'Ordering', 'wp-hotel-booking' );
			if ( isset( $columns['description'] ) ) {
				unset( $columns['description'] );
			}
			if ( isset( $columns['posts'] ) ) {
				unset( $columns['posts'] );
			}

			return $columns;
		}

		/**
		 * @param $content
		 * @param $column_name
		 * @param $term_id
		 *
		 * @return string
		 */
		public function capacity_column_content( $content, $column_name, $term_id ) {
			$taxonomy = sanitize_text_field( $_REQUEST['taxonomy'] );
			$term     = get_term( $term_id, $taxonomy );
			switch ( $column_name ) {
				case 'capacity':
					$capacity = get_term_meta( $term_id, 'hb_max_number_of_adults', true );
					$content  = '<input class="hb-number-field" type="number" name="' . $taxonomy . '_capacity[' . $term_id . ']" value="' . $capacity . '" size="2" />';
					break;
				case 'ordering':
					$content = sprintf( '<input class="hb-number-field" type="number" name="%s_ordering[%d]" value="%d" size="3" />', $taxonomy, $term_id, $term->term_group );
					break;
				default:
					break;
			}

			return $content;
		}

		/**
		 * Add capacity field in create room capacity terms page.
		 */
		public function add_capacity_fields() { ?>
            <div class="form-field">
                <label for="room_capacity"><?php _e( 'Capacity' ); ?></label>
                <input type="number" min="1" name="room_capacity" id="room_capacity" value="" size="25">
                <p class="description"><?php _e( 'Number adult in room capacity.', 'wp-hotel-booking' ); ?></p>
            </div>
			<?php
		}

		/**
		 * Add capacity field in edit room capacity page.
		 *
		 * @param $tag
		 */
		public function edit_capacity_fields( $tag ) {
			$term_id  = $tag->term_id;
			$capacity = get_term_meta( $term_id, 'hb_max_number_of_adults', true );
			?>

            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="capacity"><?php _e( 'Capacity', 'wp-hotel-booking' ); ?></label>
                </th>
                <td>
                    <input type="number" min="1" name="room_capacity" id="room_capacity" size="25"
                           value="<?php echo esc_attr( $capacity ); ?>"><br/>
                    <span class="description"><?php _e( 'Number adult in room capacity.', 'wp-hotel-booking' ); ?></span>
                </td>
            </tr>
			<?php
		}

		/**
		 * Save capacity;
		 *
		 * @param $term_id
		 */
		public function save_capacity_fields( $term_id ) {
			if ( ! $term_id ) {
				return;
			}

			$cap = $_POST['room_capacity'] ? sanitize_title( $_POST['room_capacity'] ) : 0;
			update_term_meta( $term_id, 'hb_max_number_of_adults', $cap );
		}

		/**
		 * Update custom fields for taxonomy
		 */
		public function update_taxonomy() {

			if ( ! empty( $_REQUEST['action'] ) && in_array( hb_get_request( 'taxonomy' ), array(
					'hb_room_type',
					'hb_room_capacity'
				) )
			) {
				$taxonomy = ! empty( $_REQUEST['taxonomy'] ) ? sanitize_text_field( $_REQUEST['taxonomy'] ) : '';
				global $wpdb;
				if ( ! empty( $_POST["{$taxonomy}_ordering"] ) ) {
					$when = array();
					$ids  = array();
					foreach ( $_POST["{$taxonomy}_ordering"] as $term_id => $ordering ) {
						$when[] = "WHEN term_id = {$term_id} THEN {$ordering}";
						$ids[]  = absint( $term_id );
					}

					$query = sprintf( "
                    UPDATE {$wpdb->terms}
                    SET term_group = CASE
                       %s
                    END
                    WHERE term_id IN(%s)
                ", join( "\n", $when ), join( ', ', $ids ) );
					$wpdb->query( $query );
				}

				if ( ! empty( $_POST["{$taxonomy}_capacity"] ) ) {
					foreach ( (array) $_POST["{$taxonomy}_capacity"] as $term_id => $capacity ) {
						if ( $capacity ) {
							// update_option( 'hb_taxonomy_capacity_' . $term_id, $capacity );
							update_term_meta( $term_id, 'hb_max_number_of_adults', absint( sanitize_text_field( $capacity ) ) );
						} else {
							// delete_option( 'hb_taxonomy_capacity_' . $term_id );
							delete_term_meta( $term_id, 'hb_max_number_of_adults' );
						}
					}
				}
			}
		}

		/**
		 * Remove default meta boxes
		 */
		public function remove_meta_boxes() {
			remove_meta_box( 'hb_room_capacitydiv', 'hb_room', 'side' );
			remove_meta_box( 'tagsdiv-hb_room_capacity', 'hb_room', 'side' );
		}
	}
}

new WPHB_Taxonomies_Custom_Post_Type();