<?php
/**
 * Pix Payment Gateway Class.
 *
 * @since 3.0.0
 *
 * @version 6.4.0
 */
defined( 'ABSPATH' ) || exit;

/**
 * Pix Payment Gateway Class.
 *
 * @since 3.0.0
 * @since 3.30.3 Explicitly define class properties.
 */
final class Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Pix extends LLMS_Payment_Gateway {
    /**
     * @var string
     *
     * @since 3.0.0
     */
    public $payment_instructions;

    /**
     * Constructor.
     *
     * @since   3.0.0
     *
     * @version 3.10.0
     */
    public function __construct() {
        $this->id = 'pix';
        $this->admin_description = __( 'Collect pix payments. Also handles any free orders during checkout.', 'payment-banking-slip-pix-for-lifterlms' );
        $this->admin_title = __( 'Pix', 'payment-banking-slip-pix-for-lifterlms' );
        $this->title = __( 'Pix', 'payment-banking-slip-pix-for-lifterlms' );
        $this->description = __( 'Payment via pix', 'payment-banking-slip-pix-for-lifterlms' );
        $this->payment_instructions = ''; // Fields.

        $this->supports = array(
            'checkout_fields' => true,
            'refunds' => false,
            'single_payments' => true,
            'recurring_payments' => true,
            'test_mode' => false,
        );

        add_filter( 'llms_get_gateway_settings_fields', array($this, 'get_settings_fields'), 10, 2 );
        add_action( 'lifterlms_before_view_order_table', array($this, 'before_view_order_table') );
    }

    /**
     * Output payment instructions if the order is pending.
     *
     * @since 3.0.0
     * @since 6.4.0 Allowed classes extended from this pix payment gateway class to display payment instructions.
     */
    public function before_view_order_table(): void {
        global $wp;

        if ( ! empty( $wp->query_vars['orders'] ) ) {
            $order = new LLMS_Order( (int) $wp->query_vars['orders']  );

            if (
                $order->get( 'payment_gateway' ) === $this->id
                && in_array( $order->get( 'status' ), array('llms-pending', 'llms-on-hold', true), true )
            ) {
                echo $this->get_payment_instructions();
            }
        }
    }

    /**
     * Get fields displayed on the checkout form.
     *
     * @return string
     *
     * @since    3.0.0
     *
     * @version  3.7.5
     */
    public function get_payment_instructions() {
        $opt = $this->get_option( 'payment_instructions' );
        if ( $opt ) {
            $fields = '<div class="llms-notice llms-info"><h3>' . esc_html__( 'Payment Instructions', 'lifterlms' ) . '</h3>' . wpautop( wptexturize( wp_kses_post( $opt ) ) ) . '</div>';
        } else {
            $fields = '';
        }

        return apply_filters( 'llms_get_payment_instructions', $fields, $this->id );
    }

    /**
     * Get admin setting fields.
     *
     * @param array  $fields     default fields
     * @param string $gateway_id gateway ID
     *
     * @return array
     *
     * @since    3.0.0
     *
     * @version  3.0.0
     */
    public function get_settings_fields($fields, $gateway_id) {
        if ( $this->id !== $gateway_id ) {
            return $fields;
        }

        $fields[] = array(
            'id' => $this->get_option_name( 'payment_instructions' ),
            'desc' => '<br>' . __( 'Displayed to the user when this gateway is selected during checkout. Add information here instructing the student on how to send payment.', 'lifterlms' ),
            'title' => __( 'Payment Instructions', 'lifterlms' ),
            'type' => 'textarea',
        );

        return $fields;
    }

    /**
     * Called when the Update Payment Method form is submitted from a single order view on the student dashboard.
     *
     * Gateways should do whatever the gateway needs to do to validate the new payment method and save it to the order
     * so that future payments on the order will use this new source
     *
     * @param obj   $order     Instance of the LLMS_Order
     * @param array $form_data Additional data passed from the submitted form (EG $_POST)
     *
     * @since    3.10.0
     *
     * @version  3.10.0
     */
    public function handle_payment_source_switch($order, $form_data = array()): void {
        $previous_gateway = $order->get( 'payment_gateway' );

        if ( $this->get_id() === $previous_gateway ) {
            return;
        }

        $order->set( 'payment_gateway', $this->get_id() );
        $order->set( 'gateway_customer_id', '' );
        $order->set( 'gateway_source_id', '' );
        $order->set( 'gateway_subscription_id', '' );

        $order->add_note( sprintf( __( 'Payment method switched from "%1$s" to "%2$s"', 'lifterlms' ), $previous_gateway, $this->get_admin_title() ) );
    }

    /**
     * Handle a Pending Order.
     *
     * @since 3.0.0
     * @since 3.10.0 Unknown.
     * @since 6.4.0 Use `llms_redirect_and_exit()` in favor of `wp_redirect()` and `exit()`.
     *
     * @param LLMS_Order       $order   order object
     * @param LLMS_Access_Plan $plan    access plan object
     * @param LLMS_Student     $student student object
     * @param LLMS_Coupon|bool $coupon  coupon object or `false` when no coupon is being used for the order
     */
    public function handle_pending_order($order, $plan, $student, $coupon = false) {
        // Free orders (no payment is due).
        if ( (float) 0 === $order->get_initial_price( array(), 'float' ) ) {
            // Free access plans do not generate receipts.
            if ( $plan->is_free() ) {
                $order->set( 'status', 'llms-completed' );

            // Free trial, reduced to free via coupon, etc....
            // We do want to record a transaction and then generate a receipt.
            } else {
                // Record a $0.00 transaction to ensure a receipt is sent.
                $order->record_transaction(
                    array(
                        'amount' => (float) 0,
                        'source_description' => __( 'Free', 'lifterlms' ),
                        'transaction_id' => uniqid(),
                        'status' => 'llms-txn-succeeded',
                        'payment_gateway' => 'pix',
                        'payment_type' => 'single',
                    )
                );
            }

            return $this->complete_transaction( $order );
        }

        /*
         * Action triggered when a pix payment is due.
         *
         * @hooked LLMS_Notification: manual_payment_due - 10
         *
         * @since Unknown.
         *
         * @param LLMS_Order                  $order   The order object.
         * @param LLMS_Payment_Gateway_Manual $gateway Manual gateway instance.
         */
        do_action( 'llms_manual_payment_due', $order, $this );

        /*
         * Action triggered when the pending order processing has been completed.
         *
         * @since Unknown.
         *
         * @param LLMS_Order $order The order object.
         */
        do_action( 'lifterlms_handle_pending_order_complete', $order );

        llms_redirect_and_exit( $order->get_view_link() );
    }

    /**
     * Called by scheduled actions to charge an order for a scheduled recurring transaction
     * This function must be defined by gateways which support recurring transactions.
     *
     * @param obj $order Instance LLMS_Order for the order being processed
     *
     * @return mixed
     *
     * @since    3.10.0
     *
     * @version  3.10.0
     */
    public function handle_recurring_transaction($order) {
        // Switch to order on hold if it's a paid order.
        if ( $order->get_price( 'total', array(), 'float' ) > 0 ) {
            // Update status.
            $order->set_status( 'on-hold' );

            // @hooked LLMS_Notification: manual_payment_due - 10
            do_action( 'llms_manual_payment_due', $order, $this );
        }
    }

    /**
     * Determine if the gateway is enabled according to admin settings checkbox.
     *
     * @return bool
     *
     * @since    3.0.0
     *
     * @version  3.0.0
     */
    public function is_enabled() {
        return ( 'yes' === $this->get_enabled() ) ? true : false;
    }
}
