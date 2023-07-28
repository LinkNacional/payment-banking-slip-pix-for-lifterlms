<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @author     Link Nacional
 */
final class Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Admin {
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
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles(): void {
        /*
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lkn-payment-banking-slip-pix-for-lifterlms-admin.css', array(), $this->version, 'all' );
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
         * defined in Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lkn-payment-banking-slip-pix-for-lifterlms-admin.js', array('jquery', 'payment-banking-slip-pix-for-lifterlms'), $this->version, false );
        
        $bannerStrings = array(
            'message' => sprintf(
                __('Get new features with %1$sPayment Banking Slip Pix for LifterLMS Pro.%2$s', 'payment-banking-slip-pix-for-lifterlms' ),
                '<a href="https://www.linknacional.com/wordpress/plugins/" target="_blank">',
                '</a>'
            )
        );

        // TODO continuar daqui.
        wp_localize_script('payment-banking-slip-pix-for-lifterlms', 'bannerStrings', $bannerStrings);
    }
}
