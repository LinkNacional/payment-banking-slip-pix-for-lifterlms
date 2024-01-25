<?php

/**
 * Add CPF/CNPJ checkout field.
 *
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

llms_form_field(
    array(
        'columns' => 7,
        'disabled' => $selected ? false : true,
        'id' => 'lkn_cpf_cnpj_input_paghiper',
        'label' => __( 'CPF / CNPJ', 'payment-banking-slip-pix-for-lifterlms' ),
        'last_column' => false,
        'placeholder' => 'CPF / CNPJ',
        'required' => true,
        'type' => 'tel',
        'autocomplete' => 'off',
        'class' => 'lifterLMS-input required',
        'aria-required' => true,
        'onfocus' => 'javascript: retirarFormatacao(this);',
        'onblur' => 'javascript: formatarCampo(this);',
        'max_length' => 19
    )
);
