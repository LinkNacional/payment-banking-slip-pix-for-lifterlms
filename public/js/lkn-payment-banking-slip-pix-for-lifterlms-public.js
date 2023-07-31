(function ($) {
  'use strict'

  // Load strings via wp_localize_script.
  const SUCCESS = window.localizedStrings.success
  const FAILURE = window.localizedStrings.failure

  // Function to copy emv code and slip code in payment area.
  $(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip()

    $('#lkn_copy_code').click(function () {
      const codigoEmv = $('#lkn_emvcode').val()
      const buttonTitle = $('#lkn_copy_code')

      navigator.clipboard.writeText(codigoEmv)
        .then(function () {
          buttonTitle.tooltip('dispose')
          buttonTitle.tooltip({
            title: SUCCESS
          }).tooltip('show')
        })
        .catch(function (error) {
          buttonTitle.tooltip('dispose')
          buttonTitle.tooltip({
            title: FAILURE, error
          }).tooltip('show')
        })
    })
  })

// eslint-disable-next-line no-undef
})(jQuery)
