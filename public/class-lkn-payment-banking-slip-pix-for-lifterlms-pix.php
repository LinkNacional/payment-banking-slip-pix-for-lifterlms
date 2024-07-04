<?php

/**
 * Pix Payment Gateway Class.
 *
 * @since 1.0.0
 *
 * @version 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Pix Payment Gateway Class.
 *
 * @since 1.0.0
 */
if (class_exists('LLMS_Payment_Gateway')) {
    final class Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Pix extends LLMS_Payment_Gateway {
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

            require_once LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_DIR . 'admin/lkn-payment-banking-slip-pix-for-lifterlms-pix-settings.php';
            add_filter( 'llms_get_gateway_settings_fields', array('Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Pix_Settings', 'pix_settings_fields'), 10, 2 );
            add_action( 'lifterlms_before_view_order_table', array($this, 'before_view_order_table') );
            add_action( 'lifterlms_after_view_order_table', array($this, 'after_view_order_table') );
            add_action( 'wp_enqueue_scripts', array($this, 'enqueue_tooltip_scripts') );
        }

        /**
         * Enqueue tooltip for using in Payment Area buttons.
         *
         * @since   1.0.0
         */
        public function enqueue_tooltip_scripts(): void {
            wp_enqueue_script('tooltip-js', plugin_dir_url( __FILE__ ) . 'js/lkn-popper-lib.min.js', array('jquery'), '2.11.6', true);
            wp_enqueue_script('tooltip-init', plugin_dir_url( __FILE__ ) . 'js/lkn-bootstrap.min.js', array('jquery', 'tooltip-js'), '5.3.0', true);
        }

        /**
         * Output payment instructions if the order is pending | on-hold.
         *
         * @since 1.0.0
         */
        public function before_view_order_table(): void {
            $configs = Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_configs('pix');

            // Get Payment Instruction value.
            $paymentInstruction = $configs['paymentInstruction'];

            $payInstTitle = esc_html__( 'Payment Instructions', 'payment-banking-slip-pix-for-lifterlms' );

            // Make the HTML for present the Payment Instructions.
            $paymentInst = "
            <div class=\"llms-notice llms-info\">
                <h3>
                {$payInstTitle}
                </h3>
                {$paymentInstruction}
            </div>";

            // Below is the verification of payment of the order, to present or not the Instructions.
            global $wp;

            if ( ! empty( $wp->query_vars['orders'] ) ) {
                $order = new LLMS_Order( (int) $wp->query_vars['orders']  );

                if (
                    $order->get( 'payment_gateway' ) === $this->id
                    && in_array( $order->get( 'status' ), array('llms-pending', 'llms-on-hold', true), true )
                ) {
                    echo wp_kses(
                        apply_filters( 'llms_get_payment_instructions', $paymentInst, $this->id ),
                        array(
                            'div' => array(
                                'h3' => array()
                            )
                        )
                    );
                }
            }
        }
        
        /**
         * Output payment area if the order is pending.
         *
         * @since 1.0.0
         */
        public function after_view_order_table(): void {
            global $wp;

            if ( ! empty( $wp->query_vars['orders'] ) ) {
                $order = new LLMS_Order( (int) $wp->query_vars['orders']  );

                // Verification of the gateway, to not execute in other gateways which has no defined this function.
                if ($order->get( 'payment_gateway' ) === $this->id) {
                    // Getting orderId number.
                    $orderId = $order->get('id');

                    // Getting obj $order from key.
                    $objOrder = llms_get_order_by_key('#' . $orderId);

                    // Getting qrCode and emvCode.
                    $urlQrCode = $objOrder->pix_qrcode_image;
                    $emvCode = $objOrder->pix_emv_code;

                    $title = esc_html__('Payment Area', 'payment-banking-slip-pix-for-lifterlms');
                    $buttonTitle = esc_html__('Copy code', 'payment-banking-slip-pix-for-lifterlms');

                    // Make the HTML for present the Payment Area.
                    $paymentArea = "
                    <h2>{$title}</h2> 
                    <div class=\"lknpbsp_payment_area\">
                        <div class=\"lkn_qrcode_div\"> 
                        <img class=\"lkn_qrcode\" src=\"{$urlQrCode}\" alt=\"Imagem\">
                        </div>
                        <div class=\"lkn_emvcode_div\"> 
                        <textarea id=\"lkn_emvcode\" readonly>{$emvCode}</textarea>
                        <button id=\"lkn_copy_code\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"{$buttonTitle}\">{$buttonTitle}</button>
                        </div>
                    </div>";

                    // Below is the verification of payment of the order, to present or not the Payment Area.
                    global $wp;

                    if ( ! empty( $wp->query_vars['orders'] ) ) {
                        $order = new LLMS_Order( (int) $wp->query_vars['orders']  );

                        if (
                            $order->get( 'payment_gateway' ) === $this->id
                            && in_array( $order->get( 'status' ), array('llms-pending', 'llms-on-hold', true), true )
                        ) {
                            echo wp_kses_post(
                                apply_filters( 'llms_get_payment_instructions', $paymentArea, $this->id ),
                                array(
                                    'h2' => array(),
                                    'div' => array(
                                        'class' => array(),
                                    ),
                                    'div' => array(
                                        'class' => array(),
                                    ),
                                    'img' => array(
                                        'src' => array(),
                                        'alt' => array(),
                                    ),
                                    'textarea' => array(
                                        'id' => array(),
                                        'readonly' => array(),
                                    ),
                                    'button' => array(
                                        'title' => array(),
                                        'id' => array()
                                    )  
                                )
                            );
                        }
                    }
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
         */
        public function handle_payment_source_switch($order, $form_data = array()): void {
            $configs = Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_configs('pix');

            $previous_gateway = $order->get( 'payment_gateway' );

            if ( $this->get_id() === $previous_gateway ) {
                return;
            }

            $order->set( 'payment_gateway', $this->get_id() );
            $order->set( 'gateway_customer_id', '' );
            $order->set( 'gateway_source_id', '' );
            $order->set( 'gateway_subscription_id', '' );

            // Process the switch Pix Order.
            try {
                $this->paghiper_process_order($order);
            } catch (Exception $e) {
                if ('yes' === $configs['logEnabled']) {
                    llms_log('Date: ' . gmdate('d M Y H:i:s') . ' pix gateway - switch payment method process error: ' . $e->getMessage() . \PHP_EOL, 'PagHiper - Pix');
                }
            }

            /* translators: %1$s: Previous payment gateway label %2$s: New paymenta gateway label */
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
            $configs = Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_configs('pix');

            // Make log.
            if ('yes' === $configs['logEnabled']) {
                $this->log( 'Pix Gateway `handle_pending_order()` started', $order, $plan, $student, $coupon );
            }

            // Pre validate CPF/CNPJ field.
            $cpf_cnpj_info = $this->get_field_data();

            // Make error log.
            if ( is_wp_error( $cpf_cnpj_info ) ) {
                if ('yes' === $configs['logEnabled']) {
                    $this->log( 'Pix Gateway `handle_pending_order()` ended with validation errors', $cpf_cnpj_info );
                }

                return llms_add_notice( $cpf_cnpj_info->get_error_message(), 'error' );
            }

            // CPF/CNPJ field validation
            if ($this->cpf_cnpj_valido($this->get_field_data()['lkn_cpf_cnpj_input_paghiper']) != true) {
                return false;
            }

            $total = $order->get_price( 'total', array(), 'float' );

            // Validate min value.
            if ( $total < 3.00 ) {
                if ('yes' === $configs['logEnabled']) {
                    $this->log( 'Pix Gateway `handle_pending_order()` ended with validation errors', 'Less than minimum order amount.' );
                }

                return llms_add_notice( sprintf( __( 'This gateway cannot process transactions for less than R$ 3,00.', 'payment-banking-slip-pix-for-lifterlms' ) ), 'error' );
            }

            // Free orders (no payment is due).
            if ( (float) 0 === $order->get_initial_price( array(), 'float' ) ) {
                // Free access plans do not generate receipts.
                if ( $plan->is_free() ) {
                    $order->set( 'status', 'completed' );

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
                            'payment_type' => 'single'
                        )
                    );
                }

                return $this->complete_transaction( $order );
            }

            // Process Pix Order.
            $this->paghiper_process_order($order);

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
             * @since 1.0.0.
             *
             * @param LLMS_Order $order The order object.
             */
            do_action( 'lifterlms_handle_pending_order_complete', $order );

            llms_redirect_and_exit( $order->get_view_link() );
        }

        /**
         * Process the pix order.
         *
         * @since 1.0.0
         *
         * @param LLMS_Order $order order object
         */
        public function paghiper_process_order($order) {
            $configs = Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_configs('pix');

            // Get the order total price.
            $total = $order->get_price( 'total', array(), 'float' );

            // Payer information
            $payerEmail = $order->billing_email;
            $payerName = $order->get_customer_name();
            $payerCpfCnpj = $this->get_field_data()['lkn_cpf_cnpj_input_paghiper'];
            $payerPhone = $order->billing_phone;
            $payerHStreet = $order->billing_address_1;
            $payerHNumber = $order->billing_address_2;
            $payerHCity = $order->billing_city;
            $payerHState = $order->billing_state;
            $payerHZipCode = $order->billing_zip;

            // POST parameters
            $url = $configs['urlPix'];
            $apiKey = $configs['apiKey'];
            $orderId = $order->get( 'id' );
            $daysToDue = empty($configs['daysDueDate']) ? '1' : $configs['daysDueDate'];
            $notificationUrl = site_url() . '/wp-json/lkn-paghiper-pix-listener/v1/notification';
            $itemQtd = '1';
            $itemDesc = $order->product_title . ' | ' . $order->plan_title . ' (ID# ' . $order->get('plan_id') . ')' ?? $order->plan_title;
            $itemId = $order->product_id;
            $itemPriceCents = number_format($total, 2, '', '');
            $mediaType = 'application/json';
            $charSet = 'UTF-8';

            // Body
            $dataBody = array(
                'apiKey' => $apiKey,
                'order_id' => $orderId,
                'payer_email' => $payerEmail,
                'payer_name' => $payerName,
                'payer_cpf_cnpj' => $payerCpfCnpj,
                'payer_phone' => $payerPhone,
                'payer_street' => $payerHStreet,
                'payer_number' => $payerHNumber,
                'payer_city' => $payerHCity,
                'payer_state' => $payerHState,
                'payer_zip_code' => $payerHZipCode,
                'days_due_date' => $daysToDue,
                'notification_url' => $notificationUrl,
                'partners_id' => '14P9ZE4C',
                'items' => array(
                    array(
                        'description' => $itemDesc,
                        'quantity' => $itemQtd,
                        'item_id' => $itemId,
                        'price_cents' => $itemPriceCents
                    )
                )
            );

            // Header
            $dataHeader = array(
                'Accept' => $mediaType,
                'Accept-Charset' => $charSet,
                'Accept-Encoding' => $mediaType,
                'Content-Type' => $mediaType . ';charset=' . $charSet
            );

            // Reset the order_key of obj $order for further search.
            update_post_meta($orderId, '_llms_order_key', '#' . $orderId);

            // Make the request.
            $requestResponse = $this->lkn_paghiper_pix_request($dataBody, $dataHeader, $url . 'invoice/create/');

            // Log request error if not success.
            if ('success' !== $requestResponse['pix_create_request']['result']) {
                if ('yes' === $configs['logEnabled']) {
                    llms_log( 'Pix Gateway `handle_pending_order()` ended with api request errors', 'PagHiper - Pix');
                }

                return llms_add_notice( 'Pix API error: ' . $requestResponse['pix_create_request']['response_message'], 'error' );
            }

            // If request is success, save the important data for present in payment area.
            if (isset($requestResponse)) {
                if ('reject' !== $requestResponse['pix_create_request']['result']) {
                    $order->set('pix_qrcode_image', $requestResponse['pix_create_request']['pix_code']['qrcode_image_url']);
                    $order->set('pix_emv_code', $requestResponse['pix_create_request']['pix_code']['emv']);
                    $order->set('pix_transaction_id', $requestResponse['pix_create_request']['transaction_id']);
                } else {
                    return llms_add_notice( 'Pix API Error: Operação rejeitada, motivo: ' . $requestResponse['pix_create_request']['response_message'], 'error' );
                }
            }
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
            try {
                $configs = Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_configs('pix');

                // Make the request args.
                $args = array(
                    'headers' => $dataHeader,
                    'body' => wp_json_encode($dataBody),
                    'timeout' => '10',
                    'redirection' => '5',
                    'httpversion' => '1.0'
                );

                // Make the request.
                $request = wp_remote_post($url, $args);

                // Register log.
                if ('yes' === $configs['logEnabled']) {
                    llms_log('Date: ' . gmdate('d M Y H:i:s') . ' pix gateway POST: ' . var_export($request, true) . \PHP_EOL, 'PagHiper - Pix');
                }

                return json_decode(wp_remote_retrieve_body($request), true);
            } catch (Exception $e) {
                if ('yes' === $configs['logEnabled']) {
                    $this->log('Date: ' . gmdate('d M Y H:i:s') . ' pix gateway POST error: ' . $e->getMessage() . \PHP_EOL );
                }

                return array();
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
            $configs = Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_configs('pix');

            // Switch order status to "on hold" if it's a paid order.
            if ( $order->get_price( 'total', array(), 'float' ) > 0 ) {
                // Update status.
                $order->set_status( 'on-hold' );

                try {
                    $this->paghiper_process_order($order);
                } catch (Exception $e) {
                    if ('yes' === $configs['logEnabled']) {
                        llms_log('Date: ' . gmdate('d M Y H:i:s') . ' pix gateway - recurring order process error: ' . $e->getMessage() . \PHP_EOL, 'PagHiper - Pix');
                    }
                }

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
                'lkn-payment-banking-slip-pix-for-lifterlms-checkout-fields.php',
                array(
                    'gateway' => $this,
                    'selected' => ( $this->get_id() === LLMS()->payment_gateways()->get_default_gateway() )
                ),
                '',
                LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_DIR . 'public/partials/'
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
                    $errs->add( 'lkn_pix_checkout_required_field_' . $field, sprintf( __( 'Missing required field: CPF/CNPJ.', 'payment-banking-slip-pix-for-lifterlms' ), $field ) );
                }
            }

            if ( $errs->has_errors() ) {
                return $errs;
            }

            return $data;
        }

        /**
         * Performs CPF/CNPJ validations.
         *
         * @since 1.0.0
         *
         * @param int|string $cpf_cnpj The CPF/CNPJ for validade
         *
         * @return bool|llms_notice
         */
        protected function cpf_cnpj_valido($cpf_cnpj) {
            $cpf_cnpj = preg_replace('/[^0-9]/', '', $cpf_cnpj);

            $cpf_cnpj_len = strlen($cpf_cnpj);

            if ($cpf_cnpj_len < 11 || $cpf_cnpj_len > 14) {
                return llms_add_notice( sprintf( __( 'Incorrect number of CPF/CNPJ digits', 'payment-banking-slip-pix-for-lifterlms' ) ), 'error' );
            }

            $cpf_equal_valid = 0;
            $cnpj_equal_valid = 0;

            if (11 == $cpf_cnpj_len) {
                $cpf_equal_valid = preg_match('/(\d)\1{10}/', $cpf_cnpj);
            } elseif (14 == $cpf_cnpj_len) {
                if (
                    ! is_numeric($cpf_cnpj) ||
                    '00000000000000' == $cpf_cnpj ||
                    '11111111111111' == $cpf_cnpj ||
                    '22222222222222' == $cpf_cnpj ||
                    '33333333333333' == $cpf_cnpj ||
                    '44444444444444' == $cpf_cnpj ||
                    '55555555555555' == $cpf_cnpj ||
                    '66666666666666' == $cpf_cnpj ||
                    '77777777777777' == $cpf_cnpj ||
                    '88888888888888' == $cpf_cnpj ||
                    '99999999999999' == $cpf_cnpj
                ) {
                    $cnpj_equal_valid = 1;
                }
            }

            if ($cpf_equal_valid) {
                return llms_add_notice( sprintf( __( 'Incorrect and invalid CPF', 'payment-banking-slip-pix-for-lifterlms' ) ), 'error' );
            }

            if ($cnpj_equal_valid) {
                return llms_add_notice( sprintf( __( 'Incorrect and invalid CNPJ', 'payment-banking-slip-pix-for-lifterlms' ) ), 'error' );
            }

            if (11 == $cpf_cnpj_len) {
                // Validate the CPF verify digits
                for ($t = 9; $t < 11; ++$t) {
                    for ($d = 0, $c = 0; $c < $t; ++$c) {
                        $d += $cpf_cnpj[$c] * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($cpf_cnpj[$c] != $d) {
                        return llms_add_notice( sprintf( __( 'Invalid CPF', 'payment-banking-slip-pix-for-lifterlms' ) ), 'error' );
                    }
                }
            } elseif (14 == $cpf_cnpj_len) {
                // Validate the CNPJ verify digits
                $tamanho = strlen($cpf_cnpj) - 2;
                $numeros = substr($cpf_cnpj, 0, $tamanho);
                $digitos = substr($cpf_cnpj, $tamanho);
                $soma = 0;
                $pos = $tamanho - 7;

                for ($i = $tamanho; $i >= 1; $i--) {
                    $soma += $numeros[$tamanho - $i] * $pos--;
                    if ($pos < 2) {
                        $pos = 9;
                    }
                }

                $resultado = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
                if ($resultado != $digitos[0]) {
                    return llms_add_notice( sprintf( __( 'Invalid CNPJ', 'payment-banking-slip-pix-for-lifterlms' ) ), 'error' );
                }

                $tamanho += 1;
                $numeros = substr($cpf_cnpj, 0, $tamanho);
                $soma = 0;
                $pos = $tamanho - 7;

                for ($i = $tamanho; $i >= 1; $i--) {
                    $soma += $numeros[$tamanho - $i] * $pos--;
                    if ($pos < 2) {
                        $pos = 9;
                    }
                }

                $resultado = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
                if ($resultado != $digitos[1]) {
                    return llms_add_notice( sprintf( __( 'Invalid CNPJ', 'payment-banking-slip-pix-for-lifterlms' ) ), 'error' );
                }
            } else {
                return llms_add_notice( sprintf( __( 'Invalid CPF/CNPJ', 'payment-banking-slip-pix-for-lifterlms' ) ), 'error' );
            }

            return true;
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
                'refunds' => false, // Significa que compras feitas com esse gateway podem ser reembolsadas, porém, esse gateway não funciona como um método de reembolso.
                'single_payments' => true,
                'recurring_payments' => true,
                'test_mode' => false
            );

            $this->admin_order_fields = wp_parse_args(
                array(
                    'customer' => true,
                    'source' => true,
                    'subscription' => false
                ),
                $this->admin_order_fields
            );
        }
    }
}
