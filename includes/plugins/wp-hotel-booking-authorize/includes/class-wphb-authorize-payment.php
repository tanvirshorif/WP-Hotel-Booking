<?php

/**
 * WP Hotel Booking Authorize payment class.
 *
 * @class       WPHB_Payment_Gateway_Authorize
 * @version     2.0
 * @package     WP_Hotel_Booking_Authorize/Classes
 * @category    Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Payment_Gateway_Authorize' ) ) {

	/**
	 * Class WPHB_Payment_Gateway_Authorize.
	 *
	 * @since 2.0
	 */
	class WPHB_Payment_Gateway_Authorize extends WPHB_Abstract_Payment_Gateway {

		/**
		 * @var string
		 */
		protected $_slug = 'authorize';

		/**
		 * @var null
		 */
		protected $_production_authorize_url = null;

		/**
		 * @var null
		 */
		protected $_sandbox_authorize_url = null;

		/**
		 * Current Authorize using
         *
		 * @var null
		 */
		protected $_authorize_url = null;

		/**
		 * API Login ID
		 *
		 * @var null
		 */
		protected $_api_login_id = null;

		/**
		 * Transaction key
		 *
		 * @var null
		 */
		protected $_transaction_key = null;

		/**
		 * Secret key
         *
		 * @var null
		 */
		protected $_secret_key = null;

		/**
		 * @var array|null
		 */
		protected $_messages = null;

		/**
		 * @var array
		 */
		protected $_settings = array();

		/**
		 * WPHB_Payment_Gateway_Authorize constructor.
		 *
		 * @since 2.0
		 */
		public function __construct() {
			parent::__construct();

			$this->_title       = __( 'Authorize', 'wphb-authorize-payment' );
			$this->_description = __( 'Pay with Authorize.net', 'wphb-authorize-payment' );
			$this->_settings    = WPHB_Settings::instance()->get( 'authorize' );

			$this->_api_login_id    = isset( $this->_settings['api_login_id'] ) ? $this->_settings['api_login_id'] : '8u33RVeK';
			$this->_transaction_key = isset( $this->_settings['transaction_key'] ) ? $this->_settings['transaction_key'] : '36zHT3e446Hha7X8';
			$this->_secret_key      = isset( $this->_settings['secret_key'] ) ? $this->_settings['secret_key'] : '';

			$this->_production_authorize_url = 'https://secure.authorize.net/gateway/transact.dll';
			$this->_sandbox_authorize_url    = 'https://test.authorize.net/gateway/transact.dll';

			if ( $this->_settings['sandbox'] === 'on' ) {
				$this->_authorize_url = $this->_sandbox_authorize_url;
			} else {
				$this->_authorize_url = $this->_production_authorize_url;
			}

			$this->_messages = array(
				1 => __( 'This transaction has been approved.', 'wphb-authorize-payment' ),
				2 => __( 'This transaction has been declined.', 'wphb-authorize-payment' ),
				3 => __( 'There has been an error processing this transaction.', 'wphb-authorize-payment' ),
				4 => __( ' This transaction is being held for review.', 'wphb-authorize-payment' )
			);

			$this->init();
			// checkout authorize hook template
			add_filter( 'hotel_booking_checkout_tpl', array( $this, 'checkout_order_pay' ) );
			// template args hook
			add_filter( 'hotel_booking_checkout_tpl_template_args', array( $this, 'checkout_order_pay_args' ) );
			// order-pay confirm. only authorize
			add_action( 'hotel_booking_order_pay_after', array( $this, 'authorize_form' ) );
		}

		/**
		 * Init hooks.
		 *
		 * @since 2.0
		 */
		public function init() {
			// settings form, frontend payment select form
			add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
			$this->payment_callback();
		}

		/**
		 * Payment callback.
		 *
		 * @since 2.0
		 */
		public function payment_callback() {
			ob_start();
			if ( ! isset( $_POST ) ) {
				return;
			}

			if ( ! isset( $_POST['x_response_code'] ) ) {
				return;
			}

			if ( isset( $_POST['x_response_reason_text'] ) ) {
				hb_add_message( $_POST['x_response_reason_text'] );
			}

			$code = 0;
			if ( isset( $_POST['x_response_code'] ) && array_key_exists( (int) $_POST['x_response_code'], $this->_messages ) ) {
				$code = (int) $_POST['x_response_code'];
			}

			$amount = 0;
			if ( isset( $_POST['x_amount'] ) ) {
				$amount = (float) $_POST['x_amount'];
			}

			if ( ! isset( $_POST['x_invoice_num'] ) ) {
				return;
			}

			$id   = (int) $_POST['x_invoice_num'];
			$book = WPHB_Booking::instance( $id );

			if ( $code === 1 ) {
				if ( (float) $book->total === (float) $amount ) {
					$status = 'completed';
				} else {
					$status = 'processing';
				}
			} else {
				$status = 'pending';
			}

			$book->update_status( $status );
			WPHB_Cart::instance()->empty_cart();
			wp_redirect( hb_get_checkout_url() );
			exit();
		}

		/**
		 * Get checkout template.
		 *
		 * @since 2.0
		 *
		 * @return string
		 */
		public function checkout_order_pay() {
			if ( ! empty( $_GET['hb-order-pay'] ) &&
			     ! empty( $_GET['hb-order-pay-nonce'] ) &&
			     wp_verify_nonce( $_GET['hb-order-pay-nonce'], 'hb-order-pay-nonce' )
			) {
				$tpl = 'checkout/order-pay.php';
			} else {
				$tpl = 'checkout/checkout.php';
			}

			return $tpl;
		}

		/**
		 * Checkout template args.
		 *
		 * @since 2.0
		 *
		 * @param $args
		 *
		 * @return array
		 */
		public function checkout_order_pay_args( $args ) {
			if ( ! empty( $_GET['hb-order-pay'] ) &&
			     ! empty( $_GET['hb-order-pay-nonce'] ) &&
			     wp_verify_nonce( sanitize_text_field( $_GET['hb-order-pay-nonce'] ), 'hb-order-pay-nonce' )
			) {
				$args = array( 'booking_id' => absint( $_GET['hb-order-pay'] ) );
			}

			return $args;
		}

		/**
		 * Html submit authorize form.
		 *
		 * @since 2.0
		 */
		public function authorize_form() {
			if ( empty( $_GET['hb-order-pay'] ) ||
			     empty( $_GET['hb-order-pay-nonce'] ) ||
			     ! wp_verify_nonce( sanitize_text_field( $_GET['hb-order-pay-nonce'] ), 'hb-order-pay-nonce' )
			) {
				return;
			}

			$book_id = absint( $_GET['hb-order-pay'] );
			$book    = WPHB_Booking::instance( $book_id );

			$time  = time();
			$nonce = wp_create_nonce( 'replay-pay-nonce' );


			// hb_get_currency() is requirement to generate $fingerprint variable

			if ( function_exists( 'hash_hmac' ) ) {
				$fingerprint = hash_hmac(
					"md5", $this->_api_login_id . "^" . $book_id . "^" . $time . "^" . $book->advance_payment . "^" . hb_get_currency(), $this->_transaction_key
				);
			} else {
				$fingerprint = bin2hex( mhash( MHASH_MD5, $this->_api_login_id . "^" . $book_id . "^" . $time . "^" . $book->advance_payment . "^" . hb_get_currency(), $this->_transaction_key ) );
			}
			// 4007000000027
			$authorize_args = array(
				'x_login'               => $this->_api_login_id,
				'x_amount'              => $book->advance_payment,
				'x_currency_code'       => hb_get_currency(),
				'x_invoice_num'         => $book_id,
				'x_relay_response'      => 'FALSE',
				'x_relay_url'           => add_query_arg(
					array( 'replay-pay' => $book_id, 'replay-pay-nonce' => $nonce ), hb_get_return_url()
				),
				'x_fp_sequence'         => $book_id,
				'x_fp_hash'             => $fingerprint,
				'x_show_form'           => 'PAYMENT_FORM',
				'x_version'             => '3.1',
				'x_fp_timestamp'        => $time,
				'x_first_name'          => $book->customer_first_name,
				'x_last_name'           => $book->customer_last_name,
				'x_address'             => $book->customer_address,
				'x_country'             => $book->customer_country,
				'x_state'               => $book->customer_state,
				'x_city'                => $book->customer_city,
				'x_zip'                 => $book->customer_postal_code,
				'x_phone'               => $book->customer_phone,
				'x_email'               => $book->customer_email,
				'x_type'                => 'AUTH_CAPTURE',
				'x_cancel_url'          => hb_get_return_url(),
				'x_email_customer'      => 'TRUE',
				'x_cancel_url_text'     => __( 'Cancel Payment', 'wphb-authorize-payment' ),
				'x_receipt_link_method' => 'POST',
				'x_receipt_link_text'   => __( 'Click here to return our homepage', 'wphb-authorize-payment' ),
				'x_receipt_link_URL'    => hb_get_return_url(),
			);

			if ( $this->_settings['sandbox'] === 'on' ) {
				$authorize_args['x_test_request'] = 'TRUE';
			} else {
				$authorize_args['x_test_request'] = 'FALSE';
			}
			?>
            <form id="tp_hotel_booking_order_pay" action="<?php echo esc_url( $this->_authorize_url ); ?>"
                  method="POST">
				<?php foreach ( $authorize_args as $name => $val ): ?>
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>"
                           value="<?php echo esc_attr( $val ) ?>"/>
				<?php endforeach; ?>
                <button type="submit"><?php _e( 'Pay with Authorize.net', 'wphb-authorize-payment' ) ?></button>
            </form>
            <script type="text/javascript">
                (function ($) {
                    $('#tp_hotel_booking_order_pay').submit();
                })(jQuery);
            </script>
			<?php
		}

		/**
		 * Get payment method title.
		 *
		 * @since 2.0
		 *
		 * @return mixed
		 */
		public function payment_method_title() {
			return $this->_description;
		}

		/**
		 * Display when selected Authorize in checkout page.
		 *
		 * @since 2.0
		 */
		public function form() {
			echo __( 'Pay with Authorize', 'wphb-authorize-payment' );
		}

		/**
		 * Get Authorize checkout url.
		 *
		 * @since 2.0
		 *
		 * @param $booking_id
		 *
		 * @return string
		 */
		protected function _get_authorize_basic_checkout_url( $booking_id ) {
			$nonce = wp_create_nonce( 'hb-order-pay-nonce' );

			return add_query_arg(
				array(
					'hb-order-pay'       => $booking_id,
					'hb-order-pay-nonce' => $nonce
				), hb_get_page_permalink( 'checkout' ) );
		}

		/**
		 * Process checkout.
		 *
		 * @since 2.0
		 *
		 * @param null $booking_id
		 *
		 * @return array
		 */
		public function process_checkout( $booking_id = null ) {
			return array(
				'result'   => 'success',
				'redirect' => $this->_get_authorize_basic_checkout_url( $booking_id )
			);
		}

		/**
		 * Admin setting page.
		 *
		 * @since 2.0
		 */
		public function admin_settings() {
			include_once WPHB_AUTHORIZE_PAYMENT_ABSPATH . '/includes/views/settings.php';
		}

		/**
		 * Check payment gateway enable.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		public function is_enable() {
			return empty( $this->_settings['enable'] ) || $this->_settings['enable'] == 'on';
		}

	}
}