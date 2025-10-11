document.addEventListener('DOMContentLoaded', function() {
    const tabelaCorpo = document.querySelector('#tabelaPedidos tbody');

    function carregarPedidos() {
        tabelaCorpo.innerHTML = `<tr><td colspan="6" class="text-center">Carregando pedidos...</td></tr>`;

        fetch('pedidos/detalhados')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Falha na rede ou erro no servidor.');
                }
                return response.json();
            })
            .then(data => {
                tabelaCorpo.innerHTML = '';

                if (data.length === 0) {
                    tabelaCorpo.innerHTML = `<tr><td colspan="6" class="text-center">Sem novos pedidos.</td></tr>`;
                    return;
                }

                data.forEach(pedido => {
                    const dataFormatada = new Date(pedido.dataPedido).toLocaleDateString('pt-BR');
                    const valorFormatado = parseFloat(pedido.valorTotal).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

                    const linha = `
                        <tr>
                            <td>${pedido.idPedido}</td>
                            <td>${pedido.nomeCliente}</td>
                            <td>${pedido.enderecoCompleto}</td>
                            <td>${valorFormatado}</td>
                            <td>${dataFormatada}</td>
                            <td><span class="badge bg-warning">${pedido.status}</span></td>
                        </tr>
                    `;
                    tabelaCorpo.insertAdjacentHTML('beforeend', linha);
                });
            })
            .catch(error => {
                console.error('Erro ao buscar pedidos:', error);
                tabelaCorpo.innerHTML = `<tr><td colspan="6" class="text-center error-message">Erro ao carregar pedidos. Tente novamente mais tarde.</td></tr>`;
            });
    }

    function carregarEstatisticas() {
        fetch('dashboard/estatisticas')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Falha na rede ou erro no servidor.');
                }
                return response.json();
            })
            .then(stats => {
                // Preenche os 3 cards com a resposta da única API
                document.getElementById('totalGanhos').textContent = stats.totalGanhos.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                document.getElementById('totalCadastrados').textContent = stats.totalCadastrados;
                document.getElementById('totalVendidos').textContent = stats.totalVendidos;
            })
            .catch(error => {
                console.error('Erro ao buscar estatísticas:', error);
                document.getElementById('totalGanhos').textContent = 'Erro';
                document.getElementById('totalCadastrados').textContent = 'Erro';
                document.getElementById('totalVendidos').textContent = 'Erro';
            });
    }

    // Inicializa as duas funções principais
    carregarPedidos();
    carregarEstatisticas();
});