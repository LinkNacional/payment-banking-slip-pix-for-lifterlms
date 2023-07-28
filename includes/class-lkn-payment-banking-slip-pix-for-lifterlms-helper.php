<?php

/**
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 * @author     Link Nacional
 */
final class Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper {
    /**
     * Get the LifterLMS version (LifterLMS doesn't have an global variable for this).
     *
     * @since
     */
    final public static function get_llms_version() {
        $pluginPath = ABSPATH . 'wp-content/plugins/lifterlms/lifterlms.php';
        $plugin_data = get_plugin_data($pluginPath);

        if ($plugin_data) {
            return $plugin_data['Version'];
        }
    }

    /**
     * Show plugin dependency notice.
     *
     * @since
     */
    final public static function verify_plugin_dependencies(): void {
        // Load plugin helper functions.
        if ( ! function_exists('deactivate_plugins') || ! function_exists('is_plugin_active')) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        // Flag to check whether deactivate plugin or not.
        $is_deactivate_plugin = null;

        $lkn_pay_bank_for_lifterLMS_path = LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_FILE;

        $is_installed = false;

        // Check if the LifterLMS plugin is installed and activated.
        if (function_exists('get_plugins')) {
            $all_plugins = get_plugins();
            $is_installed = ! empty($all_plugins['lifterlms/lifterlms.php']);

            $all_activateds = get_option( 'active_plugins' );
            $activeted_plugin = in_array('lifterlms/lifterlms.php', $all_activateds, true);
        }

        // Check the minimum version of LifterLMS and if it is enabled.
        if ($is_installed) {
            $LLMS_VERSION = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_llms_version();

            require_once ABSPATH . '/wp-content/plugins/lifterlms/lifterlms.php';

            if ($activeted_plugin && version_compare($LLMS_VERSION, LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_MIN_LIFTERLMS_VERSION, '<')) {
                $is_deactivate_plugin = true;
                Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::dependency_alert();
            } elseif ($activeted_plugin && version_compare($LLMS_VERSION, LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_MIN_LIFTERLMS_VERSION, '>')) {
                $is_deactivate_plugin = false;
            } elseif ( ! $activeted_plugin) {
                $is_deactivate_plugin = true;
                Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::inactive_alert();
            }
        } elseif ( ! $is_installed) {
            $is_deactivate_plugin = true;
            Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::dependency_alert();
        }

        // Deactivate plugin.
        if ($is_deactivate_plugin) {
            deactivate_plugins($lkn_pay_bank_for_lifterLMS_path);

            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
        }
    }

    /**
     * Notice for lifterLMS dependecy.
     *
     * @since
     */
    final public static function dependency_notice(): void {
        $LLMS_VERSION = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_llms_version();

        // Admin notice.
        $message = sprintf(
            '<div class="notice notice-error"><p><strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a>  %5$s %6$s+ %7$s.</p></div>',
            __('Activation Error:', 'payment-banking-slip-pix-for-lifterlms'),
            __('You must have', 'payment-banking-slip-pix-for-lifterlms'),
            'https://lifterlms.com',
            __('LifterLMS', 'payment-banking-slip-pix-for-lifterlms'),
            __('version', 'payment-banking-slip-pix-for-lifterlms'),
            $LLMS_VERSION,
            __('for the Payment Banking Slip Pix for LifterLMS to activate', 'payment-banking-slip-pix-for-lifterlms')
        );

        echo $message;
    }

    /**
     * Notice for No Core Activation.
     *
     * @since
     */
    final public static function inactive_notice(): void {
        // Admin notice.
        $message = sprintf(
            '<div class="notice notice-error"><p><strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a> %5$s.</p></div>',
            __('Activation Error:', 'payment-banking-slip-pix-for-lifterlms'),
            __('You must have', 'payment-banking-slip-pix-for-lifterlms'),
            'https://lifterlms.com',
            __('LifterLMS', 'payment-banking-slip-pix-for-lifterlms'),
            __('plugin installed and activated for the Payment Banking Slip Pix for LifterLMS', 'payment-banking-slip-pix-for-lifterlms')
        );

        echo $message;
    }

    final public static function dependency_alert(): void {
        add_action('admin_notices', array('Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper', 'dependency_notice'));
    }

    final public static function inactive_alert(): void {
        add_action('admin_notices', array('Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper', 'inactive_notice'));
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
        $configs['baseLog'] = LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_DIR . 'includes/logs/' . date('d.m.Y-H.i.s') . '.log';

        $configs['paymentInstruction'] = get_option(sprintf('llms_gateway_%s_payment_instructions', $gateway_id), __('Check the payment area below.', 'payment-banking-slip-pix-for-lifterlms'));
        $configs['apiKey'] = get_option(sprintf('llms_gateway_%s_api_key', $gateway_id));
        $configs['tokenKey'] = get_option(sprintf('llms_gateway_%s_token_key', $gateway_id));
        $configs['daysDueDate'] = get_option(sprintf('llms_gateway_%s_days_due_date', $gateway_id));

        $configs['urlPix'] = 'https://pix.paghiper.com/';
        $configs['urlSlip'] = 'https://api.paghiper.com/';

        return $configs;
    }

    /**
     * Catch the url of the current page.
     *
     * @since 1.0.0
     *
     * @return string $page_url
     */
    final public static function get_current_url() {
        $page_url = 'http';
        if ('on' === $_SERVER['HTTPS']) {
            $page_url .= 's';
        }
        $page_url .= '://';
        if ('80' !== $_SERVER['SERVER_PORT']) {
            $page_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        } else {
            $page_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        }

        return sanitize_url($page_url);
    }

    /**
     * Catch an number in the midle of bars.
     *
     * @since 1.0.0
     *
     * @param string $url
     *
     * @return int $number
     */
    final public static function get_number_in_url($url) {
        $number = preg_match('/\/(\d+)\//', $url, $matches);
        if (isset($matches[1])) {
            $number = $matches[1];
        } else {
            return false;
        }

        return $number;
    }
}
