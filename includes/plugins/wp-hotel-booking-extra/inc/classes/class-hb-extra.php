<?php

class HB_Extra_Field {

	protected $_extras_type = null;

	function __construct() {

		/**
		 * add package details booking
		 */
		add_action( 'hotel_booking_room_details_quantity', array( $this, 'admin_booking_room_details' ), 10, 3 );
	}


	protected function extra_fields() {
		global $hb_extra_settings;
		$options = array();
		$extras  = $hb_extra_settings->get_extra();
		foreach ( $extras as $key => $ex ) {
			$opt        = new stdClass();
			$opt->text  = $ex->post_title;
			$opt->value = $ex->ID;
			$options[]  = $opt;
		}
		return $options;
	}


	function admin_booking_room_details( $booking_params, $search_key, $room_id ) {
		if ( !isset( $booking_params[$search_key] ) ||
			!isset( $booking_params[$search_key][$room_id] ) ||
			!isset( $booking_params[$search_key][$room_id]['extra_packages_details'] )
		) {
			return;
		}

		$packages = $booking_params[$search_key][$room_id]['extra_packages_details'];
		?>
		<ul>
			<?php foreach ( $packages as $id => $package ): ?>
				<li>
					<small><?php printf( '%s (x%s)', $package['package_title'], $package['package_quantity'] ) ?></small>
				</li>
			<?php endforeach ?>
		</ul>
		<?php
	}



}

new HB_Extra_Field();
