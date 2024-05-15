<h2><?php echo esc_html($title); ?></h2>
<div class="lknpbsp_payment_slip_area">
    <div class="lkn_barcode_div">
        <a id="lkn_slip" href="<?php echo esc_attr($urlSlipPdf); ?>" target="_blank">
            <button id="lkn_slip_pdf" data-toggle="tooltip" data-placement="top" title="<?php echo esc_attr($downloadTitle); ?>">
                <?php echo esc_attr($downloadButton); ?>
            </button>
        </a>
    </div>
    <div class="lkn_copyline_div">
        <textarea id="lkn_emvcode" readonly><?php echo $copyableLine; ?></textarea><br>
        <div style="margin:auto;"> <?php echo $image; ?></div>

        <button id="lkn_copy_code" data-toggle="tooltip" data-placement="top" title="<?php echo $buttonTitle; ?>">
            <?php echo $buttonTitle; ?>
        </button>
    </div>
</div>