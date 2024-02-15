(function ($) {
  'use strict'

  // Load strings via wp_localize_script.
  const STRINGS = window.bannerStrings.message

  $(window).on('load', () => {
    // Get the url informations for running functions on correct page.
    const urlParams = new URLSearchParams(window.location.search)
    const page = urlParams.get('page')
    const tab = urlParams.get('tab')
    const section = urlParams.get('section')

    if (page === 'llms-settings' && tab === 'checkout' && (section === 'pix' || section === 'bankSlip')) {
      if (section === 'bankSlip') {
        // Setting min value of days to due date for bank slip gateway as 0.
        $('input#llms_gateway_bankSlip_days_due_date').attr({
          step: '1',
          min: '0',
          default: '1'
        })

        // Setting the disabled option for automatic bank slip payment capture input option, because it's only avaliable in the Pro version.
        $('input#llms_gateway_bankSlip_lknpbsp_payment_auto_payment_capture').attr({
          disabled: true
        })
      } else if (section === 'pix') {
        // Setting min value of days to due date for pix gateway as 0.
        $('input#llms_gateway_pix_days_due_date').attr({
          step: '1',
          min: '0',
          default: '1'
        })

        // Setting the disabled option for automatic pix payment capture input option, because it's only avaliable in the Pro version.
        $('input#llms_gateway_pix_lknpbsp_payment_auto_payment_capture').attr({
          disabled: true
        })
      }

      if (section && (section === 'pix' || section === 'bankSlip')) {
        const lifterSettingsForm = $('.llms-setting-group.top')

        // Creating Pro version banner div and setting his attributes.
        const noticeDiv = $('<div></div>')
        noticeDiv.addClass('pro_version_banner')
        noticeDiv.html(STRINGS)
        noticeDiv.attr({
          style: 'padding: 10px 5px;background-color: #fcf9e8;color: #646970;border: solid 1px lightgrey;border-left-color: #dba617;border-left-width: 4px;font-size: 14px;min-width: 625px;margin-top: 10px;'
        })

        // Inserting in the LifterLMS settings form.
        lifterSettingsForm.append(noticeDiv)
      }
    }
  })

// eslint-disable-next-line no-undef
})(jQuery)
