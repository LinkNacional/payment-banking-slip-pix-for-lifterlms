<?php

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 *
 * @author     Link Nacional
 */
final class Lkn_Payment_Banking_Slip_Pix_For_Lifterlms {
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     *
     * @var Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Loader maintains and registers all hooks for the plugin
     */
    private $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the string used to uniquely identify this plugin
     */
    private $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     *
     * @var string the current version of the plugin
     */
    private $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_VERSION' ) ) {
            $this->version = LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'payment-banking-slip-pix-for-lifterlms';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::verify_plugin_dependencies();
        $this->init_gateways();
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run(): void {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     *
     * @return string the name of the plugin
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     *
     * @return Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Loader orchestrates the hooks of the plugin
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     *
     * @return string the version number of the plugin
     */
    public function get_version() {
        return $this->version;
    }

    public function init_gateways(): void {
        $all_activateds = get_option( 'active_plugins' );
        $activeted_plugin = in_array('lifterlms/lifterlms.php', $all_activateds, true);

        if ($activeted_plugin) {
            add_filter( 'lifterlms_payment_gateways', array($this, 'add_gateways') );
        } else {
            deactivate_plugins(LKN_PAYMENT_BANKING_SLIP_PIX_FOR_LIFTERLMS_FILE);
            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
        }
    }

    /**
     * Add the PagHiper Payment gateways to the list of available gateways.
     *
     * @param array
     * @param mixed $gateways
     */
    public static function add_gateways($gateways) {
        $gateways[] = 'Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Pix';

        return $gateways;
    }

    /**
     * Returns an instance of the gateway.
     *
     * @since 1.0.0
     *
     * @param string $gateway_id
     *
     * @return Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Pix
     */
    public static function get_gateways($gateway_id) {
        return llms()->payment_gateways()->get_gateway_by_id( $gateway_id );
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Loader. Orchestrates the hooks of the plugin.
     * - Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_i18n. Defines internationalization functionality.
     * - Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Admin. Defines all hooks for the admin area.
     * - Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     */
    private function load_dependencies(): void {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( __DIR__ ) . 'includes/class-lkn-payment-banking-slip-pix-for-lifterlms-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( __DIR__ ) . 'includes/class-lkn-payment-banking-slip-pix-for-lifterlms-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( __DIR__ ) . 'admin/class-lkn-payment-banking-slip-pix-for-lifterlms-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( __DIR__ ) . 'public/class-lkn-payment-banking-slip-pix-for-lifterlms-public.php';

        /**
         * The class responsible for useful functions of plugin.
         */
        require_once plugin_dir_path( __DIR__ ) . 'includes/class-lkn-payment-banking-slip-pix-for-lifterlms-helper.php';

        /**
         * The class responsible for pix gateway.
         */
        require_once plugin_dir_path( __DIR__ ) . 'includes/class-lkn-payment-banking-slip-pix-for-lifterlms-pix.php';

        $this->loader = new Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     */
    private function set_locale(): void {
        $plugin_i18n = new Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     */
    private function define_admin_hooks(): void {
        $plugin_admin = new Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     */
    private function define_public_hooks(): void {
        $plugin_public = new Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
    }
}
