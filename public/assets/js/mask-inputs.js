$(document).ready(function() {
  // Máscara telefone - Clientes
  $('#numero_cliente').on('input', function() {
    maskTelefone(this);
  });

  // Máscara CPF - Clientes
  $('#cpf_cliente').on('input', function() {
    maskCPF(this);
  });

  // Máscara telefone - Cadastro Login
  $('#cadastroTelefone').on('input', function() {
    maskTelefone(this);
  });

  // Máscara CPF - Cadastro Login
  $('#cadastroCpf').on('input', function() {
    maskCPF(this);
  });
});

// Função global para máscara de telefone
function maskTelefone(input) {
  let v = (input.value || '').replace(/\D/g, '');
  if (v.length > 11) v = v.slice(0, 11);
  let formatted = v;
  if (v.length > 2) {
    formatted = '(' + v.slice(0,2) + ') ' + v.slice(2);
  }
  if (v.length > 7) {
    formatted = '(' + v.slice(0,2) + ') ' + v.slice(2,7) + '-' + v.slice(7);
  }
  input.value = formatted;
}

// Função global para máscara de CPF
function maskCPF(input) {
  let v = (input.value || '').replace(/\D/g, '');
  if (v.length > 11) v = v.slice(0, 11);
  v = v.replace(/(\d{3})(\d)/, '$1.$2');
  v = v.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
  v = v.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
  input.value = v;
}

// Função global para máscara de data (opcional, se quiser customizar)
function maskData(input) {
  // Se quiser customizar, adicione aqui
  // Por padrão, o input type="date" já mascara
}
