<?php

/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @see               https://www.linknacional.com/
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Payment Banking Slip Pix for LifterLMS
 * Plugin URI:        https://www.linknacional.com/wordpress/plugins/
 * Description:       Enable bank slip and pix payment for LifterLMS.
 * Version:           1.0.7
 * Author:            Link Nacional
 * Author URI:        https://www.linknacional.com/
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       payment-banking-slip-pix-for-lifterlms
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Currently plugin version.
 */
define( 'LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_VERSION', '1.0.7' );

define( 'LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_MIN_LIFTERLMS_VERSION', '7.1.4' );

define( 'LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_FILE', __FILE__ );

define( 'LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_DIR', plugin_dir_path(LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_FILE) );

define( 'LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_URL', plugin_dir_url(LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_FILE) );

define( 'LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_BASENAME', plugin_basename(LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_FILE) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lkn-payment-banking-slip-pix-for-lifterlms-activator.php.
 */
function lknpbsp_payment_activate_payment_banking_slip_pix_for_lifterlms(): void {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-lkn-payment-banking-slip-pix-for-lifterlms-activator.php';
    Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lkn-payment-banking-slip-pix-for-lifterlms-deactivator.php.
 */
function lknpbsp_payment_deactivate_payment_banking_slip_pix_for_lifterlms(): void {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-lkn-payment-banking-slip-pix-for-lifterlms-deactivator.php';
    Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'lknpbsp_payment_activate_payment_banking_slip_pix_for_lifterlms' );
register_deactivation_hook( __FILE__, 'lknpbsp_payment_deactivate_payment_banking_slip_pix_for_lifterlms' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lkn-payment-banking-slip-pix-for-lifterlms.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function lknpbsp_payment_run_payment_banking_slip_pix_for_lifterlms(): void {
    $plugin = new Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms();
    $plugin->run();
}
lknpbsp_payment_run_payment_banking_slip_pix_for_lifterlms();
