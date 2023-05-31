<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 */
defined( 'ABSPATH' ) || exit;
// TODO transformar em objeto.

$gateway = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms::get_gateways( 'pix' );

$fields = array();

$fields[] = array(
    'id' => $this->get_option_name( 'payment_instructions' ),
    'desc' => '<br>' . __( 'Displayed to the user when this gateway is selected during checkout. Add information here instructing the student on how to send payment.', 'lifterlms' ),
    'title' => __( 'Payment Instructions', 'lifterlms' ),
    'type' => 'textarea',
);

$fields[] = array(
    'id' => $gateway->get_option_name( 'api_key' ),
    'title' => __( 'API Key', 'payment-banking-slip-pix-for-lifterlms' ),
    'desc' => '<br>' . sprintf(
        // TODO ver como fiz no give antispam para colocar o link de forma traduzível.
        // Translators: %1$s = opening anchor tag; %2$s = closing anchor tag.
        __( 'Descrição API Key. %1$sLearn how finding your API key.%2$s', 'payment-banking-slip-pix-for-lifterlms' ),
        '<a href="https://dev.paghiper.com/reference/pr%C3%A9-requisitos-e-neg%C3%B3cio">',
        '</a>'
    ),
    'type' => 'password',
    // 'secure_option' => 'LKN_PIX_GATEWAY_API_KEY',
);

$fields[] = array(
    'id' => $gateway->get_option_name( 'token_key' ),
    'title' => __( 'Token Key', 'payment-banking-slip-pix-for-lifterlms' ),
    'desc' => '<br>' . sprintf(
        __( 'Descrição Token Key. %1$sLearn how finding your Token key.%2$s', 'payment-banking-slip-pix-for-lifterlms' ),
        '<a href="https://dev.paghiper.com/reference/pr%C3%A9-requisitos-e-neg%C3%B3cio">',
        '</a>'
    ),
    'type' => 'password',
    // 'secure_option' => 'LKN_PIX_GATEWAY_TOKEN_KEY',
);

/*
 * Checkbox settings will automatically save "yes" when the box is checked and "no" when the box is not checked.
 *
 * You can check whether or not the setting is enabled using `llms_parse_bool()`.
 */
// $fields[] = array(
//     'id' => $gateway->get_option_name( 'checkbox_option' ),
//     'title' => __( 'Toggleable Gateway Setting', 'payment-banking-slip-pix-for-lifterlms' ),
//     'desc' => __( 'Enable an optional gateway feature with a checkbox', 'payment-banking-slip-pix-for-lifterlms' ),
//     'type' => 'checkbox',
// );

// Add a select option
// $fields[] = array(
//     'id' => $gateway->get_option_name( 'select_option' ),
//     'title' => __( 'Multiple Option Setting', 'payment-banking-slip-pix-for-lifterlms' ),
//     'desc' => '<br>' . __( 'Add a gateway option with a dropdown.', 'payment-banking-slip-pix-for-lifterlms' ),
//     'type' => 'select',
//     'options' => array(
//         'one' => esc_html__( 'Option One', 'payment-banking-slip-pix-for-lifterlms' ),
//         'two' => esc_html__( 'Option Two', 'payment-banking-slip-pix-for-lifterlms' ),
//         'three' => esc_html__( 'Option Three', 'payment-banking-slip-pix-for-lifterlms' ),
//     ),
// );

return $fields;
