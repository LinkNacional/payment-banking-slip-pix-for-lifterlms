<?php
/**
 * Pix Payment Gateway Class.
 *
 * @since 3.0.0
 *
 * @version 6.4.0
 */
defined( 'ABSPATH' ) || exit;

/*
 * Pix Payment Gateway Class.
 *
 * @since 3.0.0
 * @since 3.30.3 Explicitly define class properties.
 */
if (class_exists('LLMS_Payment_Gateway')) {
    final class Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Pix extends LLMS_Payment_Gateway {
        /**
         * A description of the payment proccess.
         *
         * @var string
         *
         * @since 1.0.0
         */
        protected $payment_instructions;

        /**
         * API Key.
         *
         * @var string
         *
         * @since 1.0.0
         */
        protected $Api_key;

        /**
         * Token Key.
         *
         * @var string
         *
         * @since 1.0.0
         */
        protected $Token_key;

        /**
         * Constructor.
         *
         * @since   1.0.0
         *
         * @version 1.0.0
         */
        public function __construct() {
            $this->set_variables();

            add_filter( 'llms_get_gateway_settings_fields', array($this, 'pix_settings_fields'), 10, 2 );
            add_action( 'lifterlms_before_view_order_table', array($this, 'before_view_order_table') );
        }

        /**
         * Output custom settings fields on the LifterLMS Gateways Screen.
         *
         * @since 1.0.0
         *
         * @param array  $default_fields Array of existing fields
         * @param string $gateway_id     Id of the gateway
         *
         * @return array
         */
        public function pix_settings_fields($default_fields, $gateway_id) {
            if ( $this->id === $gateway_id ) {
                require_once LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_DIR . 'admin/lkn-payment-banking-slip-pix-for-lifterlms-pix-settings.php';

                $fields = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Pix_Settings::set_settings();

                $default_fields = array_merge( $default_fields, $fields );
            }

            return $default_fields;
        }

        /**
         * Output payment instructions if the order is pending.
         *
         * @since 1.0.0
         */
        public function before_view_order_table(): void {
            $configs = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_configs('pix');

            $paymentInstruction = $configs['paymentInstruction'];

            $paymentInst = '<div class="llms-notice llms-info"><h3>' . esc_html__( 'Payment Instructions', 'lifterlms' ) . '</h3>' . wpautop( wptexturize( wp_kses_post( $paymentInstruction ) ) ) . '</div>';

            global $wp;

            if ( ! empty( $wp->query_vars['orders'] ) ) {
                $order = new LLMS_Order( (int) $wp->query_vars['orders']  );

                if (
                    $order->get( 'payment_gateway' ) === $this->id
                    && in_array( $order->get( 'status' ), array('llms-pending', 'llms-on-hold', true), true )
                ) {
                    echo apply_filters( 'llms_get_payment_instructions', $paymentInst, $this->id );
                }
            }
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
            $this->log( 'Pix Gateway `handle_pending_order()` started', $order, $plan, $student, $coupon );

            // Validate CPF.
            $cpf_info = $this->get_field_data();

            if ( is_wp_error( $cpf_info ) ) {
                $this->log( 'Pix Gateway `handle_pending_order()` ended with validation errors', $cpf_info );

                return llms_add_notice( $cpf_info->get_error_message(), 'error' );
            }

            // Validate min value.
            $total = $order->get_price( 'total', array(), 'float' );

            if ( $total < 3.00 ) {
                $this->log( 'Pix Gateway `handle_pending_order()` ended with validation errors', 'Less than minimum order amount.' );

                return llms_add_notice( sprintf( __( 'This gateway cannot process transactions for less than R$ 3,00. O valor do pix esta abaixo do minimo permitido de R$ 3,00.', 'min transaction amount error', 'lifterlms-sample-gateway' ) ), 'error' );
            }

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

            $this->paghiper_process_order($order);

            // TODO descomentar depois de testar tudo.
            /*
             * Action triggered when a pix payment is due.
             *
             * @hooked LLMS_Notification: manual_payment_due - 10
             *
             * @since Unknown.
             *
             * @param LLMS_Order                  $order   The order object.
             * @param LLMS_Payment_Gateway_Manual $gateway Manual gateway instance.
            //  */
            // do_action( 'llms_manual_payment_due', $order, $this );

            // /*
            //  * Action triggered when the pending order processing has been completed.
            //  *
            //  * @since 1.0.0.
            //  *
            //  * @param LLMS_Order $order The order object.
            //  */
            // do_action( 'lifterlms_handle_pending_order_complete', $order );

            // llms_redirect_and_exit( $order->get_view_link() );
        }

        /**
         * Process the order.
         *
         * @since 1.0.0
         *
         * @param LLMS_Order $order order object
         */
        public function paghiper_process_order($order): void {
            $configs = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_configs('pix');

            $total = $order->get_price( 'total', array(), 'float' );

            // Payer parameters
            $payerEmail = $order->billing_email;
            $payerName = $order->get_customer_name();
            $payerCpfCnpj = $this->get_field_data()['lkn_cpf_cnpj_input_paghiper'];
            $payerPhone = $order->billing_phone;

            // POST parameters
            $url = $configs['urlPix'];
            $apiKey = $configs['apiKey'];
            $orderId = $order->get( 'id' );
            $daysToDue = $configs['daysDueDate'];
            $notificationUrl = get_home_url() . '/wp-json/lkn-paghiper-pix-listener/v1/notification';
            $itemQtd = '1';
            $itemDesc = $order->product_title . ' | ' . $order->plan_title . ' (ID# ' . $order->get('plan_id') . ')';
            $itemId = $order->product_id;
            $itemPriceCents = number_format($total * 100, 2);
            $mediaType = 'application/json';
            $charSet = 'UTF-8';

            // Body
            $dataBody = json_encode(array(
                'apiKey' => $apiKey,
                'order_id' => $orderId,
                'payer_email' => $payerEmail,
                'payer_name' => $payerName,
                'payer_cpf_cnpj' => $payerCpfCnpj,
                'payer_phone' => $payerPhone,
                'days_due_date' => $daysToDue,
                'notification_url' => $notificationUrl,
                'items' => array(
                    array(
                        'description' => $itemDesc,
                        'quantity' => $itemQtd,
                        'item_id' => $itemId,
                        'price_cents' => $itemPriceCents,
                    ),
                ),
            ));

            // Header
            $dataHeader = array(
                'Accept: ' . $mediaType,
                'Accept-Charset: ' . $charSet,
                'Accept-Encoding: ' . $mediaType,
                'Content-Type: ' . $mediaType . ';charset=' . $charSet,
            );

            // TODO agora saberei qual é o Order_key e poderei operar com isso em mente.
            update_post_meta($orderId, '_llms_order_key', '#' . $orderId);

            // TODO apagar e descomentar depois de testar tudo.
            echo json_encode($order);

            // // Faz a requisição
            // $requestResponse = $this->lkn_paghiper_pix_request($dataBody, $dataHeader, $url . 'invoice/create');

            // $json = json_decode($requestResponse['request'], true);

            // $httpCode = $requestResponse['httpCode'];

            // // echo '| REQUEST | ' . $request;

            // // echo '| HTTPCODE | ' . $httpCode;

            // // echo ' | BODY | ' . $dataBody . ' | DATA | ' . json_encode($dataHeader);

            // // Log request error
            // if ( $httpCode > 201 ) {
            //     $this->log( 'Pix Gateway `handle_pending_order()` ended with api request errors', 'Code: ' . $httpCode );

            //     return llms_add_notice( 'Pix API error: ' . $json['pix_create_request']['response_message'], 'error' );
            // }

            // $orderData = array();

            // $orderData['transaction_id'] = $json['pix_create_request']['transaction_id'];
            // $orderData['url_pix'] = $json['pix_create_request']['pix_code']['qrcode_image_url'];
            // $orderData['requestResponse'] = $json;

            // return $orderData;
        }

        /**
         * PagHiper Pix Request.
         *
         * @since 1.0.0
         *
         * @param mixed $dataBody
         * @param mixed $dataHeader
         * @param mixed $url
         *
         * @return array
         */
        public function lkn_paghiper_pix_request($dataBody, $dataHeader, $url) {
            $requestResponse = array();

            $ch = curl_init();
            curl_setopt($ch, \CURLOPT_URL, $url);
            curl_setopt($ch, \CURLOPT_POST, 1);
            curl_setopt($ch, \CURLOPT_POSTFIELDS, $dataBody);
            curl_setopt($ch, \CURLOPT_HTTPHEADER, $dataHeader);
            curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, false);

            $requestResponse['request'] = curl_exec($ch);
            $requestResponse['httpCode'] = curl_getinfo($ch, \CURLINFO_HTTP_CODE);

            return $requestResponse;
        }

        /**
         * Pix status Listener.
         *
         * @since 1.0.0
         *
         * @param WP_REST_Request $request Request Object
         *
         * @return WP_REST_Response
         */
        public static function get_pix_notification() {
            $configs = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_configs('pix');

            // Tudo de qualquer Post vem para $request, aparentemente.
            // $x = $request->get_body_params();
            $pixNotification = array();

            // Body parameters
            $token = $configs['tokenKey'];
            $apiKey = sanitize_text_field($_POST['apiKey']);
            $transactionId = sanitize_text_field($_POST['transaction_id']);
            $notificationId = sanitize_text_field($_POST['notification_id']);

            // Header parameters
            $mediaType = 'application/json';
            $charSet = 'UTF-8';

            $body = array(
                'token' => $token,
                'apiKey' => $apiKey,
                'transaction_id' => $transactionId,
                'notification_id' => $notificationId,
            );

            $header = array(
                'Accept-Charset: ' . $charSet,
                'Accept-Encoding: ' . $mediaType,
                'Accept: ' . $mediaType,
                'Content-Type: ' . $mediaType . ';charset=' . $charSet,
            );

            // echo json_encode($body) . ' | ' . json_encode($header);
            // $requestResponse = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Pix::lkn_paghiper_pix_request($body, $header, $configs['urlPix'] . 'invoice/notification/');

            // $code = json_decode($requestResponse['request'])->status_request->http_code;
            // $orderId = json_decode($requestResponse['request'])->status_request->order_id;
            // $orderStatus = json_decode($requestResponse['request'])->status_request->status;

            $orderId = sanitize_text_field($_POST['order_id']);
            $orderStatus = sanitize_text_field($_POST['order_status']);

            // // TODO registrar log.

            // Verificar se está pago ou completo
            if ('completed' == $orderStatus || 'paid' == $orderStatus) {
                // TODO problema ao chamar/encontrar $order
                $pixNotification['orderStatus'] = 'paid';
                Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Pix::lkn_order_set_status('paid');
            }
            if ('canceled' == $orderStatus || 'refunded' == $orderStatus) {
                $pixNotification['orderStatus'] = 'canceled';
                Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Pix::lkn_order_set_status('canceled');
            }

            return $pixNotification;
        }

        /**
         * Set the order status.
         *
         * @since 1.0.0
         *
         * @param string $status
         */
        public static function lkn_order_set_status($status) {
            // TODO ver uma forma de encontrar, modificar, etc essa Order_key.
            if ('completed' == $status || 'paid' == $status) {
                llms_get_order_by_key('order-64936f1c74c98')->set('status', 'completed');
            } elseif ('canceled' == $status || 'refunded' == $status) {
                llms_get_order_by_key('order-64936f1c74c98')->set('status', 'failed');
            } else {
                return false;
            }
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
         */
        public function is_enabled() {
            return ( 'yes' === $this->get_enabled() ) ? true : false;
        }

        /**
         * Output gateway's fields on the frontend checkout form.
         *
         * @since 1.0.0
         *
         * @return string
         */
        public function get_fields() {
            ob_start();
            llms_get_template(
                'lkn-payment-banking-slip-pix-for-lifterlms-pix-checkout-fields.php',
                array(
                    'gateway' => $this,
                    'selected' => ( $this->get_id() === LLMS()->payment_gateways()->get_default_gateway() ),
                ),
                '',
                LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_DIR . 'admin/'
            );

            return apply_filters( 'llms_get_gateway_fields', ob_get_clean(), $this->id );
        }

        /**
         * Returns user-submitted fields from the $_POST array.
         *
         * @return array|WP_Error
         */
        protected function get_field_data() {
            $errs = new WP_Error();
            $data = array();

            // Retrieve all checkout fields.
            foreach ( array('lkn_cpf_cnpj_input_paghiper') as $field ) {
                $data[ $field ] = llms_filter_input( \INPUT_POST, $field);

                // In our example, all fields are required.
                if ( empty( $data[ $field ] ) ) {
                    // Translators: %s = field key.
                    $errs->add( 'lkn_pix_checkout_required_field_' . $field, sprintf( __( 'Missing required field: CPF', 'payment-banking-slip-pix-for-lifterlms' ), $field ) );
                }
            }

            /*
             * Performs other validations.
             *
             * // TODO validar cpf.
             */

            if ( $errs->has_errors() ) {
                return $errs;
            }

            return $data;
        }

        protected function set_variables(): void {
            /*
             * The gateway unique ID.
             *
             * @var string
             */
            $this->id = 'pix';

            /*
             * The title of the gateway displayed in admin panel.
             *
             * @var string
             */
            $this->admin_title = __( 'Pix', 'payment-banking-slip-pix-for-lifterlms' );

            /*
             * The description of the gateway displayed in admin panel on settings screens.
             *
             * @var string
             */
            $this->admin_description = __( 'Allow customers to purchase courses and memberships using Pix.', 'payment-banking-slip-pix-for-lifterlms' );

            /*
             * The title of the gateway.
             *
             * @var string
             */
            $this->title = __( 'Pix', 'payment-banking-slip-pix-for-lifterlms' );

            /*
             * The description of the gateway displayed to users.
             *
             * @var string
             */
            $this->description = __( 'Payment via pix', 'payment-banking-slip-pix-for-lifterlms' );

            $this->supports = array(
                'checkout_fields' => true,
                'refunds' => true,
                'single_payments' => false,
                'recurring_payments' => true,
                'test_mode' => true,
            );

            $this->admin_order_fields = wp_parse_args(
                array(
                    'customer' => true,
                    'source' => true,
                    'subscription' => false,
                ),
                $this->admin_order_fields
            );
        }
    }
}
