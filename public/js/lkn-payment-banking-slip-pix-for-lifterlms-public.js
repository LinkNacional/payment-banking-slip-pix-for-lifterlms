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

  $(window).on('load', () => {
    let cpf_cnpj_input = $('input#lkn_cpf_cnpj_input_paghiper')
    
    cpf_cnpj_input.attr({
      autocomplete: 'off',
      'aria-required': true,
    })

    cpf_cnpj_input.focus(function() {
      retirarFormatacao(this)
    });

    cpf_cnpj_input.blur(function() {
      retirarFormatacao(this)
      formatarCampo(this)
    });
  })
  
  // Remove unwanted characters.
  function retirarFormatacao(campoTexto){
    campoTexto.value = campoTexto.value.replace(/[.\-/]/g, '')
  }

  // Formating CPF/CNPJ field.
  function formatarCampo (campoTexto) {
    if (campoTexto.value.length <= 11) {
    campoTexto.value = mascaraCpf(campoTexto.value)
    } else {
    campoTexto.value = mascaraCnpj(campoTexto.value)
    }
  }

  // Insert a mask.
  function mascaraCpf (valor) {
    return valor.replace(/(\\d{3})(\\d{3})(\\d{3})(\\d{2})/g, '\$1.\$2.\$3\\-\$4')
  }
  function mascaraCnpj (valor) {
    return valor.replace(/(\\d{2})(\\d{3})(\\d{3})(\\d{4})(\\d{2})/g, '\$1.\$2.\$3\\/\$4\\-\$5')
  }

// eslint-disable-next-line no-undef
})(jQuery)
