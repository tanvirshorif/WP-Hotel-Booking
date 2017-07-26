<?php

/**
 * Admin View: Booking details.
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

global $post;
$booking = WPHB_Booking::instance( $post->ID );
?>

<div id="booking-customer">

    <div class="customer-details">
        <ul class="hb-form-table">

            <li class="hb-form-field">
                <label for="_hb_customer_title"><?php echo __( 'Title:', 'wp-hotel-booking' ); ?></label>
				<?php hb_dropdown_titles( array(
					'name'     => '_hb_customer_title',
					'class'    => 'normal',
					'selected' => $booking->customer_title
				) ); ?>
            </li>

            <li class="hb-form-field">
                <label for="_hb_customer_postal_code"><?php echo __( 'Postal Code:', 'wp-hotel-booking' ); ?></label>
                <input type="text" name="_hb_customer_postal_code" id="_hb_customer_postal_code"
                       value="<?php echo esc_attr( $booking->customer_postal_code ) ?>"/>
            </li>

            <li class="hb-form-field">
                <label for="_hb_customer_first_name"><?php echo __( 'First Name:', 'wp-hotel-booking' ); ?></label>
                <input type="text" name="_hb_customer_first_name" id="_hb_customer_first_name"
                       value="<?php echo esc_attr( $booking->customer_first_name ) ?>"/>
            </li>

            <li class="hb-form-field">
                <label for="_hb_customer_country"><?php echo __( 'Country:', 'wp-hotel-booking' ); ?></label>
				<?php hb_dropdown_countries( array(
					'name'             => '_hb_customer_country',
					'class'            => 'normal',
					'show_option_none' => __( 'Country', 'wp-hotel-booking' ),
					'selected'         => $booking->customer_country
				) ); ?>
            </li>

            <li class="hb-form-field">
                <label for="_hb_customer_last_name"><?php echo __( 'Last Name:', 'wp-hotel-booking' ); ?></label>
                <input type="text" name="_hb_customer_last_name" id="_hb_customer_last_name"
                       value="<?php echo esc_attr( $booking->customer_last_name ) ?>"/>
            </li>

            <li class="hb-form-field">
                <label for="_hb_customer_phone"><?php echo __( 'Phone:', 'wp-hotel-booking' ); ?></label>
                <input type="text" name="_hb_customer_phone" id="_hb_customer_phone"
                       value="<?php echo esc_attr( $booking->customer_phone ) ?>"/>
            </li>

            <li class="hb-form-field">
                <label for="_hb_customer_address"><?php echo __( 'Address:', 'wp-hotel-booking' ); ?></label>
                <input type="text" name="_hb_customer_address" id="_hb_customer_address"
                       value="<?php echo esc_attr( $booking->customer_address ) ?>"/>
            </li>

            <li class="hb-form-field">
                <label for="_hb_customer_email"><?php echo __( 'Email:', 'wp-hotel-booking' ); ?></label>
                <input type="email" name="_hb_customer_email" id="_hb_customer_email"
                       value="<?php echo esc_attr( $booking->customer_email ) ?>"/>
            </li>

            <li class="hb-form-field">
                <label for="_hb_customer_city"><?php echo __( 'City:', 'wp-hotel-booking' ); ?></label>
                <input type="text" name="_hb_customer_city" id="_hb_customer_city"
                       value="<?php echo esc_attr( $booking->customer_city ) ?>"/>
            </li>

            <li class="hb-form-field">
                <label for="_hb_customer_fax"><?php echo __( 'Fax:', 'wp-hotel-booking' ); ?></label>
                <input type="text" name="_hb_customer_fax" id="_hb_customer_fax"
                       value="<?php echo esc_attr( $booking->customer_tax ) ?>"/>
            </li>

            <li class="hb-form-field">
                <label for="_hb_customer_state"><?php echo __( 'State:', 'wp-hotel-booking' ); ?></label>
                <input type="text" name="_hb_customer_state" id="_hb_customer_state"
                       value="<?php echo esc_attr( $booking->customer_state ) ?>"/>
            </li>

        </ul>
    </div>

    <div class="booking-notes">
        <label for="_hb_customer_notes"><?php echo __( 'Booking Notes:', 'wp-hotel-booking' ); ?></label>
        <textarea name="content" id="_hb_customer_notes" rows="5" cols="10"><?php echo esc_html( $booking->post->post_content ) ?></textarea>
    </div>

</div>
