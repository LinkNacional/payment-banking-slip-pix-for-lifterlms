<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @author     Link Nacional
 */
final class Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Admin {
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the ID of this plugin
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the current version of this plugin
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param string $plugin_name the name of this plugin
     * @param string $version     the version of this plugin
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts(): void {
        /*
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lkn-payment-banking-slip-pix-for-lifterlms-admin.js', array('jquery'), $this->version, false );
        
        $bannerStrings = array(
            'message' => sprintf(
                /* translators: %1$s: Open link tag %2$s: Close link tag */
                __('Get new features with %1$sPayment Banking Slip Pix for LifterLMS Pro.%2$s', 'payment-banking-slip-pix-for-lifterlms'),
                '<a href="https://www.linknacional.com/wordpress/plugins/" target="_blank">',
                '</a>'
            )
        );

        wp_localize_script($this->plugin_name, 'bannerStrings', $bannerStrings);
    }
}
