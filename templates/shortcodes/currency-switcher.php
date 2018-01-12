<?php

/**
 * The template display shortcode for currency switcher.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/shortcodes/currency-switcher.php.
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

<?php $hb_currencies = hb_payment_currencies(); ?>
<?php $storage = WPHB_Currency_Storage::instance(); ?>
<form method="POST" class="hb_form_currencies_switcher">
	<?php wp_nonce_field( 'hb_sw_currencies', 'hb_sw_currencies' ); ?>
    <select name="hb_form_currencies_switcher_select" class="hb_form_currencies_switcher_select">
		<?php foreach ( $currencies as $currency ) { ?>
			<?php if ( array_key_exists( $currency, $hb_currencies ) ) { ?>
                <option value="<?php echo esc_attr( $currency ) ?>" <?php selected( $storage->get( 'currency' ), $currency ) ?>><?php printf( '%s', $hb_currencies[ $currency ] ) ?></option>
			<?php } ?>
		<?php } ?>
    </select>
</form>
