$('#form-concluir-pedido').on('submit', function(event) {
    event.preventDefault();

    const pedido_id = parseInt($('#id_concluir').val());

    $.ajax({
        url: 'app/php/pedido_finsh.php',
        type: 'POST',
        contentType: 'application/json',  // <- importante
        data: JSON.stringify({ pedido_id }),
        success: function(response) {
            console.log('Sucesso:', response);
            if (response.success) {
                alert(response.message);
                $('#form-concluir-pedido')[0].reset();
            } else {
                alert('Erro: ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro:', error);
            alert('Ocorreu um erro ao concluir o pedido.');
        }
    });
});
