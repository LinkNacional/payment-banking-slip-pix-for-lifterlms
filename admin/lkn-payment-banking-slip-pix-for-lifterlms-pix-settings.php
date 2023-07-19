<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 */
defined( 'ABSPATH' ) || exit;

if (class_exists('LLMS_Payment_Gateway')) {
    final class Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Pix_Settings {
        public static function set_settings() {
            $gateway = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms::get_gateways( 'pix' );

            $fields = array();

            $fields[] = array(
                'id' => $gateway->get_option_name( 'payment_instructions' ),
                'desc' => '<br>' . __( 'Displayed to the user when this gateway is selected during checkout. Add information here instructing the student on how to send payment.', 'lifterlms' ),
                'title' => __( 'Payment Instructions', 'lifterlms' ),
                'type' => 'textarea',
            );

            $fields[] = array(
                'id' => $gateway->get_option_name( 'api_key' ),
                'title' => __( 'API Key', 'payment-banking-slip-pix-for-lifterlms' ),
                'desc' => '<br>' . sprintf(
                    __( 'API key is a unique code used to authenticate access to PagHiper API, ensuring secure interactions between applications and the API. %1$sLearn how finding your API key.%2$s', 'payment-banking-slip-pix-for-lifterlms' ),
                    '<a href="https://dev.paghiper.com/reference/pr%C3%A9-requisitos-e-neg%C3%B3cio">',
                    '</a>'
                ),
                'type' => 'password',
            );

            $fields[] = array(
                'id' => $gateway->get_option_name( 'token_key' ),
                'title' => __( 'Token Key', 'payment-banking-slip-pix-for-lifterlms' ),
                'desc' => '<br>' . sprintf(
                    __( 'Token Key is a digital authentication credential that grants access to the PagHiper service, often used to verify the identity of the user and ensure secure communication between parties. %1$sLearn how finding your Token key.%2$s', 'payment-banking-slip-pix-for-lifterlms' ),
                    '<a href="https://dev.paghiper.com/reference/pr%C3%A9-requisitos-e-neg%C3%B3cio">',
                    '</a>'
                ),
                'type' => 'password',
            );

            $fields[] = array(
                'id' => $gateway->get_option_name( 'days_due_date' ),
                'title' => __( 'Days to due date', 'payment-banking-slip-pix-for-lifterlms' ),
                'desc' => '<br>' . __( 'Defines the number of days until the Pix expiration (minimum value: 0).', 'payment-banking-slip-pix-for-lifterlms' ),
                'type' => 'number',
            );

            return $fields;
        }
    }
}
