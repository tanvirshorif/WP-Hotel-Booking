<?php

/**
 * The template for extra packages of available room.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-room/extra.php.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking_Room/Templates
 * @category    Templates
 * @author      Thimpress, leehld
 */


/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
$extra_product = WPHB_Extra_Product::instance( $post->id );
$room_extra    = $extra_product->get_extra();
?>

<?php if ( $room_extra ) { ?>

    <div class="hb-booking-room-form-group hb_addition_package_extra">
        <div class="hb_addition_package_title">
            <h5 class="hb_addition_package_title_toggle">
                <a href="javascript:void(0)" class="hb_package_toggle">
					<?php esc_html_e( 'Optional Extras', 'wp-hb-extra' ); ?>
                </a>
            </h5>
        </div>
        <div class="hb_addition_packages">
            <ul class="hb_addition_packages_ul">
				<?php foreach ( $room_extra as $key => $extra ) { ?>
                    <li data-price="<?php echo esc_attr( $extra->amount_singular ); ?>">
                        <div class="hb_extra_optional_right">
                            <input type="checkbox"
                                   name="hb_optional_quantity_selected[<?php echo esc_attr( $extra->ID ); ?>]"
                                   class="hb_optional_quantity_selected"
                                   id="<?php echo esc_attr( 'hb-ex-room-' . $post->id . '-' . $key ) ?>"
                            />
                        </div>
                        <div class="hb_extra_optional_left">
                            <div class="hb_extra_title">
                                <div class="hb_package_title">
                                    <label for="<?php echo esc_attr( 'hb-ex-room-' . $post->id . '-' . $key ) ?>"><?php printf( '%s', $extra->title ) ?></label>
                                </div>
                                <p><?php printf( '%s', $extra->description ) ?></p>
                            </div>
                            <div class="hb_extra_detail_price">
								<?php if ( $extra->respondent === 'number' ) { ?>
                                    <input type="number" step="1" min="1"
                                           name="hb_optional_quantity[<?php echo esc_attr( $extra->ID ); ?>]" value="1"
                                           class="hb_optional_quantity<?php echo ( $extra->respondent !== 'number' ) ? ' tp_hb_readonly' : '' ?>"
										<?php echo ( $extra->respondent !== 'number' ) ? ' readonly="readonly"' : '' ?>
                                    />
								<?php } else { ?>
                                    <input type="hidden" step="1" min="1"
                                           name="hb_optional_quantity[<?php echo esc_attr( $extra->ID ); ?>]"
                                           value="1"/>
								<?php } ?>
                                <label>
                                    <strong><?php printf( '%s', hb_format_price( $extra->amount_singular ) ) ?></strong>
                                    <small><?php printf( '/ %s', $extra->respondent_name ? $extra->respondent_name : __( 'Package', 'wp-hb-extra' ) ) ?></small>
                                </label>
                            </div>
                        </div>
                    </li>
				<?php } ?>
            </ul>
        </div>
    </div>

<?php } ?>