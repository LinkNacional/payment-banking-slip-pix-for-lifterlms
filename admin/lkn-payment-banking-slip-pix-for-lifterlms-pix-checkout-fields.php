<?php
/**
 * Add checkout fields.
 *
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 *
 * @version    1.0.0
 */
defined( 'ABSPATH' ) || exit;

llms_form_field(
    array(
        'columns' => 7,
        'disabled' => $selected ? false : true,
        'id' => 'lkn_pix_cpf',
        'label' => __( 'CPF', 'payment-banking-slip-pix-for-lifterlms' ),
        'last_column' => false,
        'max_length' => 11,
        'placeholder' => 'XXX.XXX.XXX-XX',
        'required' => true,
        'type' => 'text',
    )
);
