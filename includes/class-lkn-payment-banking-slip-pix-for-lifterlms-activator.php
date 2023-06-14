<?php

/**
 * Fired during plugin activation.
 *
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 *
 * @author     Link Nacional
 */
final class Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Activator {
    /**
     * Constructor.
     *
     * @since   1.0.0
     */
    public function __construct() {
        // Endpoint confirmations payments
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Short Description. (use period).
     *
     * Long Description.
     *
     * @since   1.0.0
     */
    public static function activate(): void {
    }

    /**
     * Routes register.
     *
     * @since   1.0.0
     */
    public function register_routes(): void {
        require_once LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_DIR . 'includes/class-lkn-payment-banking-slip-pix-for-lifterlms-pix.php';

        register_rest_route('lkn-paghiper-pix-listener/v1', '/notification', array(
            'methods' => 'POST',
            'callback' => Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Pix::get_pix_notification($request),
            'permission_callback' => array($this, 'permission_callback'),
        ));

        // Para boleto.
        // register_rest_route('lkn-slip-status-listener/v1', '/notification', array(
        // 'methods' => 'POST',
        // 'callback' => array($this, 'get_notification'),
        // 'permission_callback' => array( $this, 'permission_callback' ),
        // ));
    }
}
