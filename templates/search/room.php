<?php

/**
 * The template for displaying each available room when search room.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/room.php.
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

<?php $single_purchase = hb_get_option( 'single_purchase' ); ?>

<?php foreach ( $results as $room ) { ?>
	<?php
	$gallery       = $room->gallery;
	$featured      = $gallery ? array_shift( $gallery ) : false;
	$extra_product = WPHB_Extra_Product::instance( $room->ID );
	$room_extra    = $extra_product->get_extra();
	?>

    <li class="hb-room clearfix">

        <form name="hb-search-results"
              class="hb-search-room-results <?php echo ( $single_purchase ) ? esc_attr( 'single-purchase' ) : ''; ?>">
			<?php do_action( 'hotel_booking_loop_before_item', $room->ID ); ?>
            <div class="hb-room-content">
                <div class="hb-room-thumbnail">
					<?php if ( $featured ) { ?>
                        <a class="hb-room-gallery" data-lightbox="hb-room-gallery[<?php echo esc_attr( $room->ID ); ?>]"
                           data-title="<?php echo esc_attr( $featured['alt'] ); ?>"
                           href="<?php echo esc_attr( $featured['src'] ); ?>"><?php $room->getImage( 'catalog' ); ?>
                        </a>
					<?php } else {
						$room->getImage( 'catalog' );
					} ?>
                </div>

                <div class="hb-room-info">
                    <h4 class="hb-room-name">
                        <a href="<?php echo get_the_permalink( $room->ID ) ?>">
							<?php echo esc_html( $room->name ); ?><?php $room->capacity_title ? printf( '(%s)', $room->capacity_title ) : ''; ?>
                        </a>
                    </h4>
                    <ul class="hb-room-meta">
                        <li class="hb_search_capacity">
                            <label><?php _e( 'Capacity:', 'wp-hotel-booking' ); ?></label>
                            <div class=""><?php echo esc_html( $room->capacity ); ?></div>
                        </li>
                        <li class="hb_search_max_child">
                            <label><?php _e( 'Max Children:', 'wp-hotel-booking' ); ?></label>
                            <div><?php echo esc_html( $room->max_child ); ?></div>
                        </li>
                        <li class="hb_search_price">
                            <label><?php _e( 'Price:', 'wp-hotel-booking' ); ?></label>
                            <span class="hb_search_item_price">
							<?php echo hb_format_price( $room->amount_singular ); ?></span>
                            <div class="hb_view_price">
                                <a href=""
                                   class="hb-view-booking-room-details"><?php _e( '(View price breakdown)', 'wp-hotel-booking' ); ?></a>
								<?php hb_get_template( 'search/price-breakdown.php', array( 'room' => $room ) ); ?>
                            </div>
                        </li>
						<?php if ( ! $single_purchase ) { ?>
                            <li class="hb_search_quantity">
                                <label><?php _e( 'Quantity: ', 'wp-hotel-booking' ); ?></label>
                                <div>
									<?php hb_dropdown_numbers(
										array(
											'name'             => 'hb-num-of-rooms',
											'min'              => 1,
											'show_option_none' => __( 'Select', 'wp-hotel-booking' ),
											'max'              => $room->post->available_rooms,
											'class'            => 'number_room_select'
										)
									); ?>
                                </div>
                            </li>
						<?php } else { ?>
                            <select name="hb-num-of-rooms" class="number_room_select" style="display: none;">
                                <option value="1">1</option>
                            </select>
						<?php } ?>
                        <li class="hb_search_add_to_cart">
                            <button class="hb_add_to_cart" <?php echo ( $single_purchase ) ? '' : 'disabled="disabled"'; ?>><?php _e( 'Select this room', 'wp-hotel-booking' ) ?></button>
                        </li>
                    </ul>
                </div>
            </div>

			<?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' ); ?>
            <input type="hidden" name="check_in_date"
                   value="<?php echo date( 'm/d/Y', hb_get_request( 'hb_check_in_date' ) ); ?>"/>
            <input type="hidden" name="check_in_time"
                   value="<?php echo hb_get_request( 'hb_check_in_time', '0' ); ?>"/>
            <input type="hidden" name="hb_check_in_time"
                   value="<?php echo hb_get_request( 'check_in_time' ); ?>"/>
            <input type="hidden" name="check_out_date"
                   value="<?php echo date( 'm/d/Y', hb_get_request( 'hb_check_out_date' ) ); ?>">
            <input type="hidden" name="check_out_time"
                   value="<?php echo hb_get_request( 'hb_check_out_time', DAY_IN_SECONDS - 1 ); ?>"/>
            <input type="hidden" name="hb_check_out_time"
                   value="<?php echo hb_get_request( 'check_out_time' ); ?>"/>
            <input type="hidden" name="room-id" value="<?php echo esc_attr( $room->ID ); ?>">
            <input type="hidden" name="hotel-booking" value="cart">
            <input type="hidden" name="action" value="wphb_add_to_cart"/>

			<?php if ( $room_extra ) { ?>
                <div class="hb_addition_package_extra">
                    <div class="hb_addition_package_title">
                        <h5 class="hb_addition_package_title_toggle">
                            <a href="javascript:void(0)" class="hb_package_toggle">
								<?php esc_html_e( 'Optional Extras', 'wp-hotel-booking' ); ?>
                            </a>
                        </h5>
                    </div>
                    <div class="hb_addition_packages">
                        <ul class="hb_addition_packages_ul">
							<?php foreach ( $room_extra as $key => $extra ): ?>
                                <li data-price="<?php echo esc_attr( $extra->amount_singular ); ?>">
                                    <input type="checkbox"
                                           name="hb_optional_quantity_selected[<?php echo esc_attr( $extra->ID ); ?>]"
                                           class="hb_optional_quantity_selected"
                                           id="<?php echo esc_attr( 'hb-ex-room-' . $extra->ID . '-' . $key ) ?>"
                                    />
                                    <div class="hb_package_title">
                                        <label for="<?php echo esc_attr( 'hb-ex-room-' . $extra->ID . '-' . $key ) ?>"><?php printf( '%s', $extra->title ) ?></label>
                                        <div class="hb_extra_detail_price">
											<?php if ( $extra->respondent === 'number' ) { ?>
                                                <input type="number" step="1" min="1"
                                                       name="hb_optional_quantity[<?php echo esc_attr( $extra->ID ); ?>]"
                                                       value="1" class="hb_optional_quantity"/>
											<?php } else { ?>
                                                <input type="hidden" step="1" min="1"
                                                       name="hb_optional_quantity[<?php echo esc_attr( $extra->ID ); ?>]"
                                                       value="1"/>
											<?php } ?>
                                            <label>
                                                <strong><?php echo $extra->price; ?></strong>
                                                <small><?php printf( '/ %s', $extra->respondent_name ? $extra->respondent_name : __( 'Package', 'wp-hotel-booking' ) ) ?></small>
                                            </label>
                                        </div>
                                    </div>
                                    <p class="description"><?php printf( '%s', $extra->description ) ?></p>
                                </li>
							<?php endforeach ?>
                        </ul>
                    </div>
                </div>
			<?php } ?>
        </form>
		<?php if ( ( isset( $atts['gallery'] ) && $atts['gallery'] === 'true' ) || hb_get_option( 'enable_gallery_lightbox' ) ) { ?>
			<?php hb_get_template( 'loop/gallery-lightbox.php', array( 'room' => $room ) ) ?>
		<?php } ?>
    </li>


<?php } ?>