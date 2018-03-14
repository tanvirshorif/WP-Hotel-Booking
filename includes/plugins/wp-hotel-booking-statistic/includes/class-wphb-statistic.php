<?php
/**
 * WP Hotel Booking statistic class.
 *
 * @class       WPHB_Statistic
 * @version     2.0
 * @package     WP_Hotel_Booking_Statistic/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPHB_Statistic' ) ) {

	class WPHB_Statistic {

		/**
		 * @var null
		 */
		public $current_section = null;

		/**
		 * Class WPHB_Statistic.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			// admin sections
			add_action( 'wphb_statistic_admin_settings_sections', array( $this, 'admin_sections' ) );
			// admin date filter
			add_action( 'wphb_statistic_admin_range_filter', array( $this, 'range_filters' ) );
			// show chart
			add_action( 'wphb_statistic_charts', array( $this, 'statistic_chart' ) );
		}

		/**
		 * Get admin sections.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function get_sections() {
			$sections = array(
				'price' => __( 'Booking Price', 'wphb-statistic' ),
				'room'  => __( 'Room availability', 'wphb-statistic' )
			);

			return apply_filters( 'wphb_statistic_tabs', $sections );
		}

		/**
		 * Get date filters.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function get_ranges() {
			$ranges = array(
				'year'          => __( 'Year', 'wphb-statistic' ),
				'last_month'    => __( 'Last Month', 'wphb-statistic' ),
				'current_month' => __( 'This Month', 'wphb-statistic' ),
				'7day'          => __( 'Last 7 Days', 'wphb-statistic' )
			);

			return apply_filters( 'wphb_statistic_ranges', $ranges );
		}

		/**
		 * Show admin sections in admin statistic page.
		 *
		 * @since 2.0
		 */
		public function admin_sections() {

			$sections = $this->get_sections();
			if ( count( $sections ) === 1 ) {
				return;
			}

			$this->current_section = null;

			if ( isset( $_REQUEST['tab'] ) ) {
				$this->current_section = sanitize_text_field( $_REQUEST['tab'] );
			} else if ( $sections ) {
				$this->current_section = sanitize_text_field( array_keys( $sections )[0] );
			} ?>


            <ul class="statistic-sections">
				<?php foreach ( $sections as $section => $title ) { ?>
                    <li>
                        <a href="?page=wphb-statistic&tab=<?php echo esc_attr( $section ); ?>"
                           class="<?php echo( $this->current_section === $section ? 'current' : '' ); ?>"><?php echo esc_html( $title ); ?></a>
						<?php $this->get_sub_sections( $section ); ?>
                    </li>
				<?php } ?>
            </ul>
			<?php
		}

		/**
		 * Get sub sections.
		 *
		 * @param $section
		 */
		public function get_sub_sections( $section ) {
			if ( $section == 'room' ) {
				$rooms = WPHB_Room_CURD::get_rooms();
				if ( is_array( $rooms ) ) { ?>
                    <ul class="list-rooms">
						<?php foreach ( $rooms as $room ) { ?>
                            <li value="<?php echo esc_attr( $room->ID ); ?>">
                                <a href="#"><?php echo esc_html( $room->post_title ); ?></a>
                            </li>
							<?php
						} ?>
                    </ul>
				<?php }
			}
		}

		/**
		 * Show admin range filter in admin statistic page.
		 *
		 * @since 2.0
		 */
		public function range_filters() {

			$ranges = $this->get_ranges();

			if ( count( $ranges ) === 1 ) {
				return;
			}

			$current_range = '7day';
			if ( isset( $_REQUEST['range'] ) && $_REQUEST['range'] ) {
				$current_range = sanitize_text_field( $_REQUEST['range'] );
			} ?>

            <ul>
				<?php foreach ( $ranges as $key => $title ) { ?>
                    <li <?php echo sprintf( '%s', $key === $current_range ? 'class="active"' : '' ) ?>>
                        <a href="<?php echo admin_url( 'admin.php?page=wphb-statistic&tab=' . $this->current_section . '&range=' . $key ); ?> ">
							<?php printf( '%s', $title ); ?>
                        </a>
                    </li>
				<?php } ?>
            </ul>

		<?php }

		/**
		 * Show charts.
		 *
		 * @since 2.0
		 *
		 * @param $tab
		 */
		public function statistic_chart( $tab ) {
			if ( $tab === 'price' ) {
				require_once WPHB_STATISTIC_ABSPATH . 'includes/admin/views/booking-price.php';
			} else if ( $tab === 'room' ) {
				require_once WPHB_STATISTIC_ABSPATH . 'includes/admin/views/room-availability.php';
			}
		}
	}
}

new WPHB_Statistic();