$(document).ready(function() {
    function carregarPedidos() {
        $.ajax({
            url: 'app/php/pedido_read.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    construirTabela(response.data);
                } else {
                    $('#pedidos-container').html('<p class="text-center mt-3">Nenhum pedido encontrado.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar os pedidos:', error);
                $('#pedidos-container').html('<p class="text-center mt-3 text-danger">Ocorreu um erro ao carregar os dados.</p>');
            }
        });
    }

    function construirTabela(pedidos) {
        let tabelaHtml = '<table class="table table-bordered table-striped">';
        tabelaHtml += '<thead class="table-primary">';
        tabelaHtml += '<tr>';
        tabelaHtml += '<th>ID</th>';
        tabelaHtml += '<th>Cliente</th>';
        tabelaHtml += '<th>Valor</th>';
        tabelaHtml += '<th>Endereço</th>';
        tabelaHtml += '<th>Data</th>';
        tabelaHtml += '<th>Status</th>';
        tabelaHtml += '</tr>';
        tabelaHtml += '</thead>';
        tabelaHtml += '<tbody>';

        pedidos.forEach(pedido => {
            tabelaHtml += '<tr>';
            tabelaHtml += `<td>${pedido.id}</td>`;
            tabelaHtml += `<td>${pedido.usuario_id}</td>`;
            tabelaHtml += `<td>${pedido.valor}</td>`;
            tabelaHtml += `<td>${pedido.endereco}</td>`; // cliente/endereco pode ser o mesmo
            tabelaHtml += `<td>${pedido.data}</td>`;
            tabelaHtml += `<td>${pedido.status}</td>`;
            tabelaHtml += '</tr>';
        });

        tabelaHtml += '</tbody></table>';
        $('#pedidos-container').html(tabelaHtml);
    }

    carregarPedidos();
});
