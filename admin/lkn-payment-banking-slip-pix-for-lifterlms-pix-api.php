<?php

/**
 * Make requests to the PagHiper Gateway API.
 *
 * @see        https://www.linknacional.com/
 * @since      1.0.0
 *
 * @version    1.0.0
 */
defined( 'ABSPATH' ) || exit;

if (class_exists('LLMS_Payment_Gateway')) {
    final class Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Pix_Api extends LLMS_Abstract_API_Handler
    {
        /**
         * Parse the body of the response and set a success/error.
         *
         * @since 1.0.0
         *
         * @param array $response raw API response
         *
         * @return array
         */
        protected function parse_response($response)
        {
            $code = wp_remote_retrieve_response_code( $response );
            $body = json_decode( wp_remote_retrieve_body( $response ), true );

            // API Responded with an error.
            if ( $code > 201 ) {
                return $this->set_error(
                    ! empty( $body['message'] ) ? $body['message'] : __( 'Unknown Error', 'payment-banking-slip-pix-for-lifterlms' ),
                    ! empty( $body['code'] ) ? $body['code'] : 'unknown-error',
                    $response
                );
            }

            // Success.
            return $this->set_result( $body );
        }

        /**
         * Set request body.
         *
         * @since 1.0.0
         *
         * @param array  $data     request body
         * @param string $method   request method
         * @param string $resource requested resource
         *
         * @return array
         */
        protected function set_request_body($data, $method, $resource)
        {
            $configs = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_configs();

            $data = array(
                'apiKey' => $configs['apiKey'],
                'order_id' => /* Num sei ainda */
                'payer_email' => /* Não tem esse campo no forms de pedido */
                'payer_name' => /* Num sei ainda */ 
                'payer_cpf_cnpj' => /* Num sei ainda */
                'payer_phone' => /* Num sei ainda */
                'notification_url' => /* Num sei ainda */
                'discount_cents' => /* Num sei ainda */
                'shipping_price_cents' => /* Num sei ainda */
                'shipping_methods' => /* Num sei ainda */
                'number_ntfiscal' => /* Num sei ainda */
                'fixed_description' => /* Num sei ainda */
                'days_due_date' => $configs['daysDueDate'],
                'items' => array(
                    array ('description' => 'piscina de bolinha',
                    'quantity' => '1',
              'item_id' => '1',
              'price_cents' => '1012'), // em centavos
              array ('description' => 'pula pula',
              'quantity' => '2',
              'item_id' => '1',
              'price_cents' => '2000'), // em centavos
              array ('description' => 'mala de viagem',
              'quantity' => '3',
              'item_id' => '1',
              'price_cents' => '4000'), // em centavos
              ),
              );

            return $data;
        }

        /**
         * Set request headers.
         *
         * @since 2020-09-04
         *
         * @param array  $headers  default request headers
         * @param string $resource requested resource
         * @param string $method   request method
         *
         * @return array
         */
        protected function set_request_headers($headers, $resource, $method)
        {
            $headers['X-API-KEY'] = llms_sample_gateway()->get_gateway()->get_api_key();

            return parent::set_request_headers( $headers, $resource, $method );
        }

        /**
         * Set the request URL.
         *
         * @since 2020-09-04
         *
         * @param string $resource requested resource
         * @param string $method   request method
         *
         * @return string
         */
        protected function set_request_url($resource, $method)
        {
            return rest_url( sprintf( 'sg-mock/v1%s', $resource ) );
        }
    }
}
