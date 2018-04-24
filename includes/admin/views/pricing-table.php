<?php

/**
 * Admin View: Pricing table for single room.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
$room_id      = hb_get_request( 'hb-room' );
$week_names   = hb_date_names();
$plans        = hb_room_get_pricing_plans( $room_id );
$regular_plan = null;

foreach ( $plans as $k => $plan ) {
	if ( ! $plan->start && ! $plan->end ) {
		$regular_plan = $plan;
		unset( $plans[ $k ] );
	}
}
$count_plants = count( $plans );
$date_order   = hb_start_of_week_order();
?>

<div class="wrap" id="wp_hotel_booking_pricing">
    <h2><?php _e( 'Pricing Plans', 'wp-hotel-booking' ); ?></h2>
    <form method="post" name="pricing-table-form">
        <p>
            <strong><?php _e( 'Select name of room', 'wp-hotel-booking' ); ?></strong>
            &nbsp;&nbsp;<?php echo hb_dropdown_rooms( array( 'selected' => $room_id ) ); ?>
        </p>
		<?php if ( $room_id ) { ?>
            <div class="hb-pricing-table regular-price">
                <input type="hidden" class="datepicker"
                       name="date-start[<?php echo sprintf( '%s', $regular_plan ? $regular_plan->ID : '__INDEX__' ); ?>]"
                       size="10" readonly="readonly"/>
                <input type="hidden"
                       name="date-start-timestamp[<?php echo sprintf( '%s', $regular_plan ? $regular_plan->ID : '__INDEX__' ); ?>]"/>

                <input type="hidden" class="datepicker"
                       name="date-end[<?php echo sprintf( '%s', $regular_plan ? $regular_plan->ID : '__INDEX__' ); ?>]"
                       size="10" readonly="readonly"/>
                <input type="hidden"
                       name="date-end-timestamp[<?php echo sprintf( '%s', $regular_plan ? $regular_plan->ID : '__INDEX__' ); ?>]"/>
                <div class="hb-pricing-list">
                    <table>
                        <thead>
                        <tr>
							<?php foreach ( $date_order as $i ) { ?>
                                <th><?php echo esc_html( $week_names[ $i ] ); ?></th>
							<?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
							<?php
							$prices  = isset( $regular_plan->prices ) ? $regular_plan->prices : array();
							$plan_id = isset( $regular_plan->ID ) ? $regular_plan->ID : 0;
							?>
							<?php foreach ( $date_order as $i ) { ?>
                                <td>
									<?php $price = ! empty( $prices[ $i ] ) ? $prices[ $i ] : ''; ?>
                                    <input class="hb-pricing-price" type="number" min="0" step="any"
                                           name="price[<?php echo sprintf( '%s', $plan_id ? $plan_id : '__INDEX__' ); ?>][<?php echo esc_attr( $i ); ?>]"
                                           value="<?php echo esc_attr( $price ); ?>" size="10"/>
                                </td>
							<?php } ?>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <h3>
				<?php _e( 'Other plan', 'wp-hotel-booking' ); ?>
                <span class="count"><?php printf( _n( '(%d plan)', '(%d plans)', $count_plants, 'wp-hotel-booking' ), $count_plants ); ?></span>
            </h3>

            <div id="hb-pricing-plan-list" class="hb-pricing-list">
				<?php if ( $plans ) { ?>
					<?php foreach ( $plans as $plan ) { ?>
						<?php
						$start = strtotime( $plan->start );
						$end   = strtotime( $plan->end );
						?>
                        <div class="hb-pricing-table">
                            <h3 class="hb-pricing-table-title">
                                <label><?php _e( 'From', 'wp-hotel-booking' ); ?></label>
                                <input type="text" class="datepicker"
                                       name="date-start[<?php echo esc_attr( $plan->ID ); ?>]" size="10"
                                       value="<?php printf( '%s', date_i18n( hb_get_date_format(), $start ) ); ?>"/>
                                <input type="hidden" name="date-start-timestamp[<?php echo esc_attr( $plan->ID ); ?>]"
                                       value="<?php echo esc_attr( $start ); ?>"/>
                                <label><?php _e( 'To', 'wp-hotel-booking' ); ?></label>
                                <input type="text" class="datepicker"
                                       name="date-end[<?php echo esc_attr( $plan->ID ); ?>]" size="10"
                                       value="<?php printf( '%s', date_i18n( hb_get_date_format(), $end ) ); ?>"/>
                                <input type="hidden" name="date-end-timestamp[<?php echo esc_attr( $plan->ID ); ?>]"
                                       value="<?php echo esc_attr( $end ); ?>"/>
                            </h3>
                            <div class="hb-pricing-list">
                                <table>
                                    <tbody>
                                    <tr>
										<?php $prices = $plan->prices; ?>
										<?php foreach ( $date_order as $i ) { ?>
                                            <td class="item">
                                                <div class="date-name"><?php echo esc_html( $week_names[ $i ] ); ?></div>
												<?php $price = ! empty( $prices[ $i ] ) ? $prices[ $i ] : 0; ?>
                                                <input class="hb-pricing-price" type="number" min="0" step="any"
                                                       name="price[<?php echo esc_attr( $plan->ID ); ?>][<?php echo esc_attr( $i ); ?>]"
                                                       value="<?php echo esc_attr( $price ); ?>" size="10"/>
                                            </td>
										<?php } ?>
                                    </tr>
                                </table>
                            </div>
                            <div class="hb-pricing-controls">
                                <a href="" class="dashicons dashicons-trash"
                                   title="<?php _e( 'Remove', 'wp-hotel-booking' ); ?>"></a>
                            </div>
                        </div>
					<?php } ?>

				<?php } else { ?>
                    <p id="hb-no-plan-message"> <?php _e( 'No addition plans', 'wp-hotel-booking' ); ?></p>
				<?php } ?>

            </div>
            <p>
                <input type="hidden" name="room_id" value="<?php echo esc_attr( $room_id ) ?>"/>
                <button class="button add_new_plan"><?php _e( 'Add New Plan', 'wp-hotel-booking' ); ?></button>
                <button class="button button-primary update_plan"><?php _e( 'Update', 'wp-hotel-booking' ); ?></button>
            </p>
			<?php wp_nonce_field( 'hb-update-pricing-plan', 'hb-update-pricing-plan-field' ); ?>
		<?php } ?>
    </form>
</div>

<script type="text/html" id="tmpl-hb-pricing-table">
    <div class="hb-pricing-table">
        <h3 class="hb-pricing-table-title">
            <label><?php _e( 'From', 'wp-hotel-booking' ); ?></label>
            <input type="text" class="datepicker" name="date-start[__INDEX__]" size="10" readonly="readonly"/>
            <input type="hidden" name="date-start-timestamp[__INDEX__]"/>
            <label><?php _e( 'To', 'wp-hotel-booking' ); ?></label>
            <input type="text" class="datepicker" name="date-end[__INDEX__]" size="10" readonly="readonly"/>
            <input type="hidden" name="date-end-timestamp[__INDEX__]"/>
        </h3>
        <div class="hb-pricing-list">
            <table>
                <tbody>
                <tr>
					<?php foreach ( $date_order as $i ) { ?>
                        <td class="item">
                            <div class="date-name"><?php echo esc_html( $week_names[ $i ] ); ?></div>
                            <input class="hb-pricing-price" type="number" min="0" step="any"
                                   name="price[__INDEX__][<?php echo esc_attr( $i ); ?>]" value="" size="10"/>
                        </td>
					<?php } ?>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="hb-pricing-controls">
            <a href="" class="dashicons dashicons-trash" title="<?php _e( 'Remove', 'wp-hotel-booking' ); ?>"></a>
        </div>
    </div>
</script>

<div id="update_pricing_popup" class="magnific-popup"></div>
<!--Single search form-->
<script type="text/html" id="tmpl-hb-update-pricing-form">
    <form method="POST" name="hb-add-calendar-pricing">
        <div class="header">
            <h2><?php _e( 'Set price', 'wp-hotel-booking' ); ?></h2>
        </div>
        <div class="main">
            <div class="room-name"><input type="hidden" name="room-id" value="{{data.id}}">{{data.name}}</div>
            <div>
				<?php _e( 'From ', 'wp-hotel-booking' ); ?><strong>{{data.from}}</strong>
				<?php _e( ' to ', 'wp-hotel-booking' ); ?><strong>{{data.to}}</strong>:
                <input type="hidden" name="from" value="{{data.from}}"/>
                <input type="hidden" name="to" value="{{data.to}}"/>
                <div class="all-day">
                    <input type="radio" id="all-day" name="all-day" value="1" checked>
                    <label for="all-day"><?php _e( 'All day:', 'wp-hotel-booking' ); ?></label>
                    <input type="number" name="all-day-price" value="100"/>
                    <code><?php echo hb_get_currency_symbol( hb_get_currency() ) . '/night'; ?></code>
                </div>
                <div class="single-date">
                    <input type="radio" name="all-day" value="0" id="single-date">
                    <label for="single-date"><?php _e( 'Single date:', 'wp-hotel-booking' ); ?></label>
                    <table>
                        <thead>
                        <tr>
							<?php foreach ( $date_order as $i ) { ?>
                                <th><?php echo esc_html( $week_names[ $i ] ); ?></th>
							<?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
							<?php foreach ( $date_order as $i ) { ?>
                                <td>
                                    <input class="hb-pricing-price" type="number" min="0" step="any"
                                           name="price-date[<?php echo esc_attr( $i ); ?>]" value=""/>
                                </td>
							<?php } ?>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="footer">
            <button type="submit" id="add_pricing_plan" class="button button-primary">
				<?php _e( 'Save', 'wp-hotel-booking' ); ?></button>
        </div>
    </form>

</script>

<?php if ( $room_id ) { ?>
    <div class="hotel-booking-fullcalendar"
         data-room-id="<?php echo esc_attr( $room_id ); ?>"
         data-room-name="<?php echo get_the_title( $room_id ); ?>"
         data-events="<?php echo esc_attr( hotel_booking_print_pricing_json( $room_id, date( 'm/d/Y' ) ) ) ?>"></div>
<?php } ?>
