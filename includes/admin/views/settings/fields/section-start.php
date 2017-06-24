<?php

/**
 * Admin View: Admin setting field - section start.
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

<?php if ( isset( $field['title'] ) ) { ?>

    <h3><?php echo esc_html( $field['title'] ); ?></h3>

	<?php if ( isset( $field['desc'] ) ) { ?>

        <p class="description"><?php echo esc_html( $field['desc'] ) ?></p>

	<?php } ?>

    <table class="form-table">

<?php } ?>