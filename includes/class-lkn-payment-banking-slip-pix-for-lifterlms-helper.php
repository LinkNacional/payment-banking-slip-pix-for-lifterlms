<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 * @author     Link Nacional
 */
final class Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Helper {
    /**
     * Show plugin dependency notice.
     *
     * @since 1.0.0
     */
    final public static function verify_plugin_dependencies(): bool {
        // Load plugin helper functions.
        if ( ! function_exists('deactivate_plugins') || ! function_exists('is_plugin_active')) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }
        
        // Flag to check whether deactivate plugin or not.
        $is_deactivate_plugin = null;

        // Verify minimum LifterLMS plugin version.
        if (
            defined('LLMS_VERSION')
            && version_compare(LLMS_VERSION, LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_MIN_LIFTERLMS_VERSION, '<')
        ) {
            // Show admin notice.
            Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::dependency_alert();

            $is_deactivate_plugin = true;
        }
        
        // LifterLMS don't have BASENAME constant.
        $LLMS_BASENAME = defined('LLMS_PLUGIN_FILE') ? plugin_basename(LLMS_PLUGIN_FILE) : '';

        $is_Lifter_active = ('' !== $LLMS_BASENAME) ? is_plugin_active($LLMS_BASENAME) : false;

        // Verify if LifterLMS plugin is actived.
        if ( ! $is_Lifter_active) {
            // Show admin notice.
            Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::inactive_alert();

            $is_deactivate_plugin = true;
        }

        // Deactivate plugin.
        if ($is_deactivate_plugin) {
            deactivate_plugins(LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_BASENAME);

            return false;
        }

        return true;
    }

    /**
     * Notice for lifterLMS dependecy.
     *
     * @since 1.0.0
     */
    final public static function dependency_notice(): void {
        // Admin notice.
        $message = sprintf(
            '<div class="notice notice-error"><p><strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a>  %5$s %6$s+ %7$s</p></div>',
            __('Activation Error:', 'payment-banking-slip-pix-for-lifterlms'),
            __('You must have', 'payment-banking-slip-pix-for-lifterlms'),
            'https://lifterlms.com',
            __('LifterLMS', 'payment-banking-slip-pix-for-lifterlms'),
            __('version', 'payment-banking-slip-pix-for-lifterlms'),
            LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_MIN_LIFTERLMS_VERSION,
            __('for the Payment Banking Slip Pix for LifterLMS to activate.', 'payment-banking-slip-pix-for-lifterlms')
        );

        echo wp_kses(
            $message,
            array(
                'div' => array(
                    'p' => array(
                        'strong' => array(),
                        'a' => array()
                    )
                )
            )
        );
    }

    /**
     * Notice for No Core Activation.
     *
     * @since 1.0.0
     */
    final public static function inactive_notice(): void {
        // Admin notice.
        $message = sprintf(
            '<div class="notice notice-error"><p><strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a> %5$s</p></div>',
            __('Activation Error:', 'payment-banking-slip-pix-for-lifterlms'),
            __('You must have', 'payment-banking-slip-pix-for-lifterlms'),
            'https://lifterlms.com',
            __('LifterLMS', 'payment-banking-slip-pix-for-lifterlms'),
            __('plugin installed and activated for the Payment Banking Slip Pix for LifterLMS.', 'payment-banking-slip-pix-for-lifterlms')
        );

        echo wp_kses(
            $message,
            array(
                'div' => array(
                    'p' => array(
                        'strong' => array(),
                        'a' => array()
                    )
                )
            )
        );
    }

    final public static function dependency_alert(): void {
        add_action('admin_notices', array('Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Helper', 'dependency_notice'));
    }

    final public static function inactive_alert(): void {
        add_action('admin_notices', array('Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Helper', 'inactive_notice'));
    }

    /**
     * Array for pick the data of the gateways settings in LifterLMS.
     *
     * @since 1.0.0
     *
     * @param string $gateway_id
     * @return array $configs
     */
    final public static function get_configs($gateway_id) {
        $configs = array();

        $configs['logEnabled'] = get_option(sprintf('llms_gateway_%s_logging_enabled', $gateway_id), 'no');
        $configs['baseLog'] = LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_DIR . 'includes/logs/' . gmdate('d.m.Y-H.i.s') . '.log';

        $configs['paymentInstruction'] = get_option(sprintf('llms_gateway_%s_lknpbsp_payment_payment_instructions', $gateway_id), __('Check the payment area below.', 'payment-banking-slip-pix-for-lifterlms'));
        $configs['apiKey'] = get_option(sprintf('llms_gateway_%s_lknpbsp_payment_api_key', $gateway_id));
        $configs['tokenKey'] = get_option(sprintf('llms_gateway_%s_lknpbsp_payment_token_key', $gateway_id));
        $configs['daysDueDate'] = get_option(sprintf('llms_gateway_%s_lknpbsp_payment_days_due_date', $gateway_id));

        $configs['urlPix'] = 'https://pix.paghiper.com/';
        $configs['urlSlip'] = 'https://api.paghiper.com/';

        return $configs;
    }

    /**
     * Returns an instance of an gateway.
     *
     * @since 1.0.0
     *
     * @param string $gateway_id
     *
     * @return object gateway
     */
    public static function get_gateways($gateway_id) {
        return llms()->payment_gateways()->get_gateway_by_id( $gateway_id );
    }
}
