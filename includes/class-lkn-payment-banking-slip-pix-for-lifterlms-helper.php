<?php

/**
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 *
 * @author     Link Nacional
 */
final class Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper {
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

        $lkn_pay_bank_for_lifterLMS_path = ABSPATH . '/wp-content/plugins/payment-banking-slip-pix-for-lifterlms/lkn-payment-banking-slip-pix-for-lifterlms.php';

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
            // TODO criar uma funçãozinha para pegar a versão, já que não tem uma variável global disponível.
            $LLMS_VERSION = $is_installed['Version'];

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
}
