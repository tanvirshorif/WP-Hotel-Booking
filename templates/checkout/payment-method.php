<?php

/**
 * The template for displaying payment methods form in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/checkout/payment-method.php.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Templates
 * @category    Templates
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php $payment_gateways = hb_get_payment_gateways( array( 'enable' => true ) ); ?>

<div class="hb-payment-form">
    <div class="hb-col-padding hb-col-border">
        <h4><?php _e( 'Payment Method', 'wp-hotel-booking' ); ?></h4>
		<?php if ( $payment_gateways ) { ?>
            <ul class="hb-payment-methods">
				<?php $i = 0; ?>
				<?php foreach ( $payment_gateways as $gateway ) { ?>
                    <li>
                        <label>
                            <input type="radio" name="hb-payment-method"
                                   value="<?php echo esc_attr( $gateway->slug ); ?>"<?php echo ( $i === 0 ) ? ' checked' : '' ?>/>
							<?php echo esc_html( $gateway->title ); ?>
                        </label>
						<?php if ( has_action( 'hb_payment_gateway_form_' . $gateway->slug ) ) { ?>
                            <div class="hb-payment-method-form <?php echo esc_attr( $gateway->slug ); ?>">
								<?php do_action( 'hb_payment_gateway_form_' . $gateway->slug ); ?>
                            </div>
						<?php } ?>
                    </li>
					<?php $i ++; ?>
				<?php } ?>
            </ul>
		<?php } else { ?>
            <p><?php _e( 'There is no payment method has been established.', 'wp-hotel-booking' ); ?></p>
		<?php } ?>
    </div>
</div>
