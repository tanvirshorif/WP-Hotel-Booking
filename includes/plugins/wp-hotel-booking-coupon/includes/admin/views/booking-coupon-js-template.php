<?php

/**
 * Admin View: Booking coupon template js.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking_Coupon/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<!--Coupons-->
<script type="text/html" id="tmpl-hb-coupons">
    <div class="hb_modal">
        <form name="booking-room-item" class="booking-room-item">
            <div class="form_head">
                <h1>
                    <# if ( typeof data.coupon_code !== 'undefined' ) { #>

                        {{{ data.coupon_code }}}

                        <# } else { #>

							<?php _e( 'Add new coupon', 'wphb-coupon' ) ?>

                            <# } #>
                </h1>
                <button class="modal_close dashicons dashicons-no-alt"></button>
            </div>

            <div class="section_line">
                <# if ( typeof data.post_type === 'undefined' || data.post_type === 'hb_room' ) { #>
                    <div class="section">
                        <select name="coupon_id" class="booking_coupon_code">
                            <# if ( typeof data.room !== 'undefined' ) { #>

                                <option value="{{ data.room.ID }}" selected>{{ data.room.post_title }}</option>

                                <# } #>
                        </select>
                    </div>
                    <# } #>
            </div>

            <# if ( typeof data.extras !== 'undefined' && Object.keys( data.extras ).length() != 0 ) { #>

                <div class="section_line">

                    <# console.debug( data.extras ) #>

                </div>

                <# } #>

                    <div class="form_footer">
						<?php wp_nonce_field( 'hotel_admin_get_coupon_available', 'hotel-admin-get-coupon-available' ); ?>
                        <input type="hidden" name="order_id" value="{{ data.order_id }}"/>
                        <!-- <input type="hidden" name="coupon_id" value="{{ data.coupon_id }}" /> -->
                        <input type="hidden" name="action" value="wphb_coupon_add_booking_coupon"/>
                        <button type="reset"
                                class="button modal_close"><?php _e( 'Close', 'wphb-coupon' ) ?></button>
                        <button type="submit"
                                class="button button-primary form_submit"><?php _e( 'Add', 'wphb-coupon' ); ?></button>
                    </div>
        </form>
    </div>
    <div class="modal_overlay"></div>
</script>
<!--Coupons-->
