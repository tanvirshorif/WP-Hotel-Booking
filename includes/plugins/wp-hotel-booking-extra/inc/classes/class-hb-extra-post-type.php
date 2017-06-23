<?php

class HB_Extra_Post_Type {

	static $_instance = null;

	/**
	 * initialize class register post type, insert, update post
	 * with post_type = 'hb_extra_room'
	 */
	function __construct() {

	}


	function add_extra( $post_id, $post = array() ) {
		global $wpdb;
		$query = $wpdb->prepare( "
				SELECT * FROM $wpdb->posts WHERE `ID` = %d AND `post_type` = %s
			", $post_id, 'hb_extra_room' );

		$results = $wpdb->get_results( $query, OBJECT );

		$args = array(
			'post_title'   => isset( $post['name'] ) ? $post['name'] : '',
			'post_content' => isset( $post['desc'] ) ? $post['desc'] : '',
			'post_type'    => 'hb_extra_room',
			'post_status'  => 'publish'
		);

		if ( ! $results ) {
			$post_id = wp_insert_post( $args );
		} else {
			$args['ID'] = $post_id;
			wp_update_post( $args );
		}

		if ( isset( $post['price'] ) ) {
			$price = (float) $post['price'];
		} else {
			$price = 0;
		}

		if ( get_post_meta( $post_id, 'tp_hb_extra_room_price', true ) || get_post_meta( $post_id, 'tp_hb_extra_room_price', true ) == 0 ) {
			update_post_meta( $post_id, 'tp_hb_extra_room_price', $price );
		} else {
			add_post_meta( $post_id, 'tp_hb_extra_room_price', $price );
		}

		unset( $post['name'] );
		unset( $post['desc'] );
		unset( $post['price'] );

		foreach ( $post as $key => $value ) {
			if ( get_post_meta( $post_id, 'tp_hb_extra_room_' . $key, true )
			     || get_post_meta( $post_id, 'tp_hb_extra_room_' . $key, true ) === ''
			     || get_post_meta( $post_id, 'tp_hb_extra_room_' . $key, true ) == 0
			) {
				update_post_meta( $post_id, 'tp_hb_extra_room_' . $key, $value );
			} else {
				add_post_meta( $post_id, 'tp_hb_extra_room_' . $key, $value );
			}
		}

		return $post_id;
	}


	/**
	 * get instance return self instead of new Class()
	 * @return object class
	 */
	static function instance() {
		if ( self::$_instance ) {
			return self::$_instance;
		}

		return new self();
	}


}

HB_Extra_Post_Type::instance();