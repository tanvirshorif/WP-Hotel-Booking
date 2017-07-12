<?php
/**
 * Admin View: Addition packages setting page.
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


$settings = apply_filters( 'hotel_booking_addon_menus', array() );
?>

<div class="wrap">
    <h2>
		<?php _e( 'Extra services', 'wp-hotel-booking' ); ?>
        <a href="#" class="page-title-action"
           @click.prevent="add_extra()"><?php _e( 'Add new extra', 'wp-hotel-booking' ); ?></a>
        <span class="hb-add-new spinner"></span>
    </h2>
	<?php
	/**
	 * use hb_settings function get field name
	 * WPHB_Settings save auto
	 * else customize save function
	 */

	$extras      = WPHB_Extra::instance()->get_extra();
	$field_name  = 'tp_hb_extra_room';
	$extra_types = hb_extra_types();
	$respondent  = array();
	foreach ( $extra_types as $key => $value ) {
		$respondent[] = array( 'text' => $value, 'value' => $key );
	}
	?>

	<?php if ( $extras ) { ?>

        <table id="list-extra-services">
            <thead class="list-extra-header">
            <tr>
                <th class="title"><?php echo __( 'Name', 'wp-hotel-booking' ); ?></th>
                <th class="description"><?php echo __( 'Description', 'wp-hotel-booking' ); ?></th>
                <th class="price"><?php echo __( 'Price', 'wp-hotel-booking' ); ?></th>
                <th class="type"><?php echo __( 'Type', 'wp-hotel-booking' ); ?></th>
                <!--                <th class="room">--><?php //echo __( 'Room', 'wp-hotel-booking' ); ?><!--</th>-->
                <th class="actions"><?php echo __( 'Actions', 'wp-hotel-booking' ); ?></th>
            </tr>
            </thead>
            <tbody>

            <tr v-for="extra in extras" :class="{edit : isEdit}">
                <td class="title">
                    {{extra.title}}
                    <input type="text" name="extra-name" value='extra.title'/>
                </td>
                <td class="description">{{extra.description}}</td>
                <td class="price">{{extra.price}} / {{extra.respondent_name}}</td>
                <td class="type">{{extra.respondent}}</td>
                <!--                <td class="room"></td>-->
                <td class="actions">
                    <a href="#" title="Edit" class="dashicons dashicons-edit"
                       @click.prevent="edit_extra()"></a>
                    <a href="#" title="Delete" class="dashicons dashicons-trash" @click="delete_extra(extra.id)"></a>
                </td>
            </tr>
            </tbody>
        </table>

	<?php } else { ?>
        <p><?php echo __( 'No extra services have been created yet.', 'wp-hotel-booking' ); ?></p>
	<?php } ?>


    <script type="text/html" id="tmpl-tp-hb-extra-room">
        <div class="tp_extra_form_fields">
            <div class="name">
                <h4><?php _e( 'Name', 'wp-hotel-booking' ); ?></h4>
                <input type="text" name="<?php echo esc_attr( $field_name ); ?>[{{ data.id }}][name]" value=""
                       placeholder="<?php echo esc_attr( 'Package name' ) ?>"/>
            </div>
            <div class="desc">
                <h4><?php _e( 'Description', 'wp-hotel-booking' ); ?></h4>
                <textarea name="<?php echo esc_attr( $field_name ) ?>[{{ data.id }}][desc]"
                          placeholder="<?php esc_attr_e( 'Enter description here', 'wp-hotel-booking' ) ?>"></textarea>
            </div>
            <div class="price">
                <h4><?php _e( 'Price', 'wp-hotel-booking' ); ?></h4>
                <input type="number" step="any" name="<?php echo esc_attr( $field_name ); ?>[{{ data.id }}][price]"
                       value="" placeholder="<?php echo esc_attr( '10' ) ?>"/>
                <span>/</span>
                <input type="text" name="<?php echo esc_attr( $field_name ); ?>[{{ data.id }}][respondent_name]"
                       value="" placeholder="<?php esc_attr_e( 'Package', 'wp-hotel-booking' ) ?>"/>
            </div>
            <div class="type">
                <h4><?php _e( 'Price Type', 'wp-hotel-booking' ); ?></h4>
				<?php hb_extra_select( $field_name . '[{{ data.id }}][respondent]', array( 'options' => $respondent ), '' ); ?>
            </div>
            <div class="remove">
                <a data-id="{{ data.id }}"
                   class="button remove_button"><?php esc_attr_e( 'Remove', 'wp-hotel-booking' ); ?></a>
            </div>
        </div>
    </script>
</div>