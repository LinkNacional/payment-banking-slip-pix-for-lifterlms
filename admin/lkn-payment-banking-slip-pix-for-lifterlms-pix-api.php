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
            $configs = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_configs('pix');

            $fields = Lkn_Payment_Banking_Slip_Pix_For_Lifterlms_Helper::get_fields();

            return json_encode(array(
                'apiKey' => $configs['apiKey'],
                'order_id' => $configs['orderId'],
                'payer_email' => $fields['payerEmail'],
                'payer_name' => $fields['payerName'],
                'payer_cpf_cnpj' => $fields['payerCpf'],
                'payer_phone' => $fields['payerPhone'],
                'days_due_date' => $configs['daysDueDate'],
                'items' => array(
                    array(
                        // TODO ver como pegar essas informações.
                        'description' => $configs['itemDescription'],
                        'quantity' => '1',
                        'item_id' => $configs['itemId'],
                        'price_cents' => $configs['itemPriceCents'],
                    ),
                ),
            ));
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
            $mediaType = 'application/json'; // formato da requisição
            $charSet = 'UTF-8';

            $headers = array();
            $headers[] = 'Accept: ' . $mediaType;
            $headers[] = 'Accept-Charset: ' . $charSet;
            $headers[] = 'Accept-Encoding: ' . $mediaType;
            $headers[] = 'Content-Type: ' . $mediaType . ';charset=' . $charSet;

            return $headers;
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
            return 'https://pix.paghiper.com/invoice/create/';
            // return rest_url( sprintf( 'https://pix.paghiper.com/invoice/create/' ) );
        }
    }
}
