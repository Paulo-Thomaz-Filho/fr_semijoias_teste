$(document).ready(function() {
    $('#form-update').on('submit', function(event) {
        event.preventDefault();

        // 1. Defina a variável para o ID do usuário.
        // Pegue esse valor do local storage, de um campo oculto no HTML, etc.
        // Aqui, usamos um valor fixo apenas como exemplo.
        const usuarioLogadoId = 1; // Substitua '1' pelo ID do usuário logado.

        // 2. Validação no lado do cliente
        // Verifique se o valor é um número válido.
        if (!usuarioLogadoId || isNaN(usuarioLogadoId) || usuarioLogadoId <= 0) {
            alert('Erro: ID do usuário inválido. Por favor, faça login novamente.');
            console.error('ID do usuário logado é inválido.');
            return; // Interrompe o envio do formulário
        }

        const dadosFormulario = {
            // Mapeamento corrigido para corresponder às chaves do PHP
            pedido_id: parseInt($('#id_update').val()),
            usuario_id: usuarioLogadoId, // Agora usamos a variável validada
            data_pedido: $('#data_update').val(),
            status: 'pendente', 
            valor_total: parseFloat($('#valor_update').val()),
            endereco_entrega: $('#cliente_update').val(), 
        };

        $.ajax({
            url: 'app/php/pedido_update.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(dadosFormulario),
            success: function(response) {
                console.log('Sucesso:', response);
                if (response.success) {
                    alert(response.message || 'Pedido atualizado com sucesso!');
                    $('#form-update')[0].reset();
                    if (typeof carregarPedidos === 'function') {
                        carregarPedidos(); 
                    }
                } else {
                    alert('Erro: ' + (response.error || 'Não foi possível atualizar o pedido.'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro:', error);
                alert('Ocorreu um erro ao atualizar o pedido.');
            }
        });
    });
});