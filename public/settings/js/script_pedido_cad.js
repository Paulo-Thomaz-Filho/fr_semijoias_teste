$(document).ready(function() {
    $('#form-cadastro').on('submit', function(event) {
        event.preventDefault();

        const dadosFormulario = {
            nome: $('#nome_cadastro').val(),
            quantidade: parseInt($('#quantidade_cadastro').val()),
            valor: parseFloat($('#valor_cadastro').val()),
            cliente: $('#cliente_cadastro').val(),
            descricao: $('#descricao_cadastro').val(),
            data: $('#data_cadastro').val(),
        };

        $.ajax({
            url: 'app/php/pedido_cad.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(dadosFormulario),
            success: function(response) {
                console.log('Sucesso:', response);
                if(response.success){
                    alert(response.message);
                    $('#form-cadastro')[0].reset();
                    carregarPedidos(); // opcional: atualiza a tabela automaticamente
                } else {
                    alert('Erro: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro:', error);
                alert('Ocorreu um erro ao cadastrar o pedido.');
            }
        });
    });
});
