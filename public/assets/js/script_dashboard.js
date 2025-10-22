﻿// Carregar informações do usuário logado
const carregarUsuarioLogado = () => {
    const nomeCompleto = sessionStorage.getItem('usuario_nome') || 'Usuário';
    const primeiroNome = nomeCompleto.split(' ')[0];
    
    // Atualizar o nome completo no dropdown
    const elementoNomeCompleto = document.getElementById('usuario-nome-completo');
    if (elementoNomeCompleto) {
        elementoNomeCompleto.textContent = nomeCompleto;
    }
    
    // Atualizar o primeiro nome no header
    const elementoPrimeiroNome = document.getElementById('usuario-primeiro-nome');
    if (elementoPrimeiroNome) {
        elementoPrimeiroNome.textContent = primeiroNome;
    }
};

window.atualizarDashboard = async function() {
    await carregarEstatisticas();
    await carregarPedidosRecentes();
};

const carregarEstatisticas = async () => {
    try {
        const response = await fetch('/dashboard/estatisticas');
        const dados = await response.json();
        
        // Card 1: Total de Ganhos
        const totalGanhos = document.getElementById('total-ganhos');
        if (totalGanhos) {
            const ganhos = parseFloat(dados.total_ganhos || 0);
            totalGanhos.textContent = ganhos.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }
        
        // Card 2: Total de Usuários Cadastrados
        const totalUsuarios = document.getElementById('total-usuarios');
        if (totalUsuarios) {
            const usuarios = parseInt(dados.total_usuarios || 0);
            totalUsuarios.textContent = usuarios + (usuarios === 1 ? ' Usuário' : ' Usuários');
        }
        
        // Card 3: Vendas do Mês
        const vendasMes = document.getElementById('vendas-mes');
        if (vendasMes) {
            const vendas = parseInt(dados.vendas_mes || 0);
            vendasMes.textContent = vendas + (vendas === 1 ? ' Venda' : ' Vendas');
        }
        
        // Card 4: Produto Mais Vendido
        const produtoMaisVendido = document.getElementById('produto-mais-vendido');
        if (produtoMaisVendido) {
            produtoMaisVendido.textContent = dados.produto_mais_pedido || 'N/A';
        }
        
    } catch (error) {
        console.error('Erro estatísticas:', error);
    }
};

const carregarPedidosRecentes = async () => {
    try {
        const tbody = document.querySelector('#tabelaPedidosRecentes tbody');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3">Carregando...</td></tr>';
        // Carregar lista de status para mapear idStatus -> nome
        let statusMap = {};
        try {
            const statusResp = await fetch('/status');
            const statusList = await statusResp.json();
            if (Array.isArray(statusList)) {
                statusList.forEach(s => { statusMap[s.id_status] = s.nome; });
            }
        } catch {}
        // Carregar lista de clientes para mapear idCliente -> nome
        let clientesMap = {};
        try {
            const clientesResp = await fetch('/usuario');
            const clientesList = await clientesResp.json();
            if (Array.isArray(clientesList)) {
                clientesList.forEach(c => { clientesMap[c.idUsuario] = c.nome; });
            }
        } catch {}
        const response = await fetch('/pedidos');
        let pedidos = await response.json();
        // Verificar se houve erro no servidor
        if (!response.ok || !Array.isArray(pedidos)) {
            console.error('Erro ao carregar pedidos:', pedidos);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-danger">Erro ao carregar pedidos</td></tr>';
            return;
        }
        // Limita aos 10 mais recentes (mais novos primeiro) e filtra por status textual
        pedidos = pedidos.filter(p => {
            const statusNome = statusMap[p.idStatus] ? statusMap[p.idStatus].toLowerCase() : '';
            return statusNome === 'pendente';
        }).reverse().slice(0, 10);
        if (pedidos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted">Nenhum pedido pendente</td></tr>';
            return;
        }
        tbody.innerHTML = pedidos.map(p => {
            // Calcular valor total (preco * quantidade)
            const valorTotal = (parseFloat(p.preco) || 0) * (parseInt(p.quantidade) || 1);
            const valor = valorTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            const data = p.dataPedido ? new Date(p.dataPedido).toLocaleDateString('pt-BR') : 'N/A';
            const status = statusMap[p.idStatus] || 'N/A';
            const endereco = p.endereco || '-';
            const clienteNome = clientesMap[p.idCliente] || 'N/A';
            // Mapear status para classe CSS dos badges
            let statusClass = status.toLowerCase().replace(/\s+/g, '-');
            if (statusClass === 'pendente') statusClass = 'pending';
            if (statusClass === 'enviado') statusClass = 'sent';
            if (statusClass === 'concluído' || statusClass === 'concluido') statusClass = 'green';
            if (statusClass === 'cancelado') statusClass = 'danger';
            const statusBadge = '<span class="status-badge status-' + statusClass + '">• ' + status + '</span>';
            return '<tr class="border-bottom border-light"><td class="py-3 text-dark">' + (p.produtoNome || 'N/A') + '</td><td class="py-3 text-dark">' + clienteNome + '</td><td class="py-3 text-dark">' + endereco + '</td><td class="py-3 text-dark">' + valor + '</td><td class="py-3 text-dark">' + data + '</td><td class="py-3">' + statusBadge + '</td></tr>';
        }).join('');
    } catch (error) {
        console.error('Erro pedidos recentes:', error);
        const tbody = document.querySelector('#tabelaPedidosRecentes tbody');
        if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-danger">Erro ao carregar</td></tr>';
    }
};

document.addEventListener('DOMContentLoaded', async function() {
    carregarUsuarioLogado(); // Carregar nome do usuário primeiro
    await atualizarDashboard();
});
