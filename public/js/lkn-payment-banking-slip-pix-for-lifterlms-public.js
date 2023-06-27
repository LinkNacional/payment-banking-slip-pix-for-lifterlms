(function ($) {
  'use strict'

  $(document).ready(function () {
    $('#lkn_copy_code').click(function () {
      const codigoEmv = $('#lkn_emvcode').val()

      navigator.clipboard.writeText(codigoEmv)
        .then(function () {
          // TODO retirar alert, usar mensagem HTML ou tooltip.
          alert('Código copiado!')
        })
        .catch(function (error) {
          alert('Falha ao copiar código: ', error)
        })
    })
  })

  // TODO arrumar mascara CPF depois.
  //   $(window).load(function () {
  //     const cpfCnpjInput = $('#lkn_cpf_cnpj_input_paghiper')[0]

  //     $(cpfCnpjInput).on('click', function () {
  //       formatarCampo(cpfCnpjInput)
  //       retirarFormatacao(cpfCnpjInput)

  //       console.log(cpfCnpjInput.value)
  //     })
  //   })

  //   function retirarFormatacao (campoTexto) {
  //     campoTexto.value = campoTexto.value.replace('/(\\.|\\/|\\-)/g', '')
  //   }

  //   // faz a formatação do campo CPF/CNPJ
  //   function formatarCampo (campoTexto) {
  //     if (campoTexto.value.length <= 11) {
  //       campoTexto.value = mascaraCpf(campoTexto.value)
  //     } else {
  //       campoTexto.value = mascaraCnpj(campoTexto.value)
  //     }
  //   }

  //   // insere uma máscara de acordo com a quantidade de caracteres
  //   function mascaraCpf (valor) {
  //     // eslint-disable-next-line no-useless-escape
  //     return valor.replace('/(\\d{3})(\\d{3})(\\d{3})(\\d{2})/g', '\$1.\$2.\$3\\-\$4')
  //   }

  //   function mascaraCnpj (valor) {
  //     // eslint-disable-next-line no-useless-escape
  //     return valor.replace('/(\\d{2})(\\d{3})(\\d{3})(\\d{4})(\\d{2})/g', '\$1.\$2.\$3\\/\$4\\-\$5')
  //   }

// eslint-disable-next-line no-undef
})(jQuery)
