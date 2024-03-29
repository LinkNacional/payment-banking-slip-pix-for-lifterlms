<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @author     Link Nacional
 */
final class Lknpbsp_Payment_Banking_Slip_Pix_For_Lifterlms_Public {
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
     * @param string $plugin_name the name of the plugin
     * @param string $version     the version of this plugin
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles(): void {
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

        wp_enqueue_style( 'lkn-payment-banking-slip-pix-for-lifterlms', plugin_dir_url( __FILE__ ) . 'css/lkn-payment-banking-slip-pix-for-lifterlms-public.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lkn-payment-banking-slip-pix-for-lifterlms-public.js', array('jquery'), $this->version, false );
    
        $localizedStrings = array(
            'success' => __('Code copied', 'payment-banking-slip-pix-for-lifterlms'),
            'failure' => __('Fail on copy code: ', 'payment-banking-slip-pix-for-lifterlms')
        );

        wp_localize_script($this->plugin_name, 'localizedStrings', $localizedStrings);
    }
}
