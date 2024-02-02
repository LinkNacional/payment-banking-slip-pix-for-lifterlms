<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 *
 * @author     Link Nacional
 */
final class Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_i18n {
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain(): void {
        load_plugin_textdomain(
            'payment-banking-slip-pix-for-lifterlms',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );
    }
}
