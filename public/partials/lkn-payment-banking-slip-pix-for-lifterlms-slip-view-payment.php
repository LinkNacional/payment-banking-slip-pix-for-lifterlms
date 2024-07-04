<?php

$html = "<h2>{$title}</h2>
    <div class=\"lknpbsp_payment_slip_area\">
        <div class=\"lkn_barcode_div\">
            <a id=\"lkn_slip\" href=\"{$urlSlipPdf}\" target=\"_blank\">
                <button id=\"lkn_slip_pdf\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"{$downloadTitle}\">
                    {$downloadButton}
                </button>
            </a>
        </div>
        <div class=\"lkn_copyline_div\">
            <div id=\"lkn_codbarra\">{$image}</div>
            <textarea id=\"lkn_emvcode\" readonly>{$copyableLine}</textarea><br>
            <button id=\"lkn_copy_code\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"{$buttonTitle}\">
                {$buttonTitle}
            </button>
        </div>
    </div>";

echo wp_kses_post(
    apply_filters('llms_get_payment_instructions', $html, $this->id),
    array(
        'h2' => array(),
        'div' => array(
            'class' => array(),
        ),
        'img' => array(
            'src' => array(),
            'alt' => array(),
        ),
        'textarea' => array(
            'id' => array(),
            'readonly' => array(),
        ),
        'button' => array(
            'title' => array(),
            'id' => array(),
        ),
        'a' => array(
            'id' => array(),
            'href' => array(),
            'target' => array(),
        ),
    )
);
?>
