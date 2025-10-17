$(document).ready(function() {
  // Máscara telefone
  $('#numero_cliente').on('input', function() {
    let v = $(this).val().replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    let formatted = v;
    if (v.length > 2) {
      formatted = '(' + v.slice(0,2) + ') ' + v.slice(2);
    }
    if (v.length > 7) {
      formatted = '(' + v.slice(0,2) + ') ' + v.slice(2,7) + '-' + v.slice(7);
    }
    $(this).val(formatted);
  });

  // Máscara CPF
  $('#cpf_cliente').on('input', function() {
    let v = $(this).val().replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
    v = v.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
    $(this).val(v);
  });
});
