// Logout global para dashboard
document.addEventListener('DOMContentLoaded', function() {
    var btnsLogout = document.querySelectorAll('.btn-logout-dashboard');
    btnsLogout.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            fetch('/api/usuario/logout')
                .then(function() {
                    localStorage.clear();
                    sessionStorage.clear();
                    window.location.href = '/login';
                });
        });
    });
});
// =============================================================================
// SCRIPT DO DASHBOARD
// =============================================================================

// Carregar informações do usuário logado
const carregarUsuarioLogado = () => {
    const nomeCompleto = sessionStorage.getItem('usuario_nome') || 'Usuário';
    const primeiroNome = nomeCompleto.split(' ')[0];
    
    const elementoNomeCompleto = document.getElementById('usuario-nome-completo');
    if (elementoNomeCompleto) {
        elementoNomeCompleto.textContent = nomeCompleto;
    }
    
    const elementoPrimeiroNome = document.getElementById('usuario-primeiro-nome');
    if (elementoPrimeiroNome) {
        elementoPrimeiroNome.textContent = primeiroNome;
    }
};

// Função global para atualizar dashboard
window.atualizarDashboard = async function() {
    await carregarEstatisticas();
    await carregarPedidosRecentes();
};

// =============================================================================
// FUNÇÕES DE CARREGAMENTO DE DADOS
// =============================================================================

// Carregar estatísticas dos cards
const carregarEstatisticas = async () => {
    try {
        const response = await fetch('/dashboard/estatisticas');
        const dados = await response.json();
        
        // Card 1: Total de Ganhos
        const totalGanhos = document.getElementById('total-ganhos');
        if (totalGanhos) {
            const ganhos = parseFloat(dados.total_ganhos || 0);
            totalGanhos.textContent = ganhos.toLocaleString('pt-BR', { 
                style: 'currency', 
                currency: 'BRL' 
            });
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
        
        // Card 4: Categoria Mais Vendida
        const categoriaMaisVendida = document.getElementById('categoria-mais-vendida');
        if (categoriaMaisVendida) {
            categoriaMaisVendida.textContent = dados.categoria_mais_vendida || 'N/A';
            categoriaMaisVendida.title = dados.categoria_mais_vendida || 'N/A';
        }
        
    } catch (error) {
        // ...
    }
};

// Carregar pedidos recentes pendentes
const carregarPedidosRecentes = async () => {
    try {
        const tbody = document.querySelector('#tabelaPedidosRecentes tbody');
        const cardsContainer = document.getElementById('cardsPedidosRecentes');
        
        if (!tbody || !cardsContainer) return;
        
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3">Carregando...</td></tr>';
        cardsContainer.innerHTML = '<div class="text-center py-4 text-muted">Carregando pedidos...</div>';
        
        // Carregar mapeamento de status
        let statusMap = {};
        try {
            const statusResp = await fetch('/status');
            const statusList = await statusResp.json();
            if (Array.isArray(statusList)) {
                statusList.forEach(s => { 
                    statusMap[s.idStatus] = s.nome; 
                });
            }
        } catch (error) {
            // ...
        }
        
        // Carregar mapeamento de clientes
        let clientesMap = {};
        try {
            const clientesResp = await fetch('/usuario');
            const clientesList = await clientesResp.json();
            if (Array.isArray(clientesList)) {
                clientesList.forEach(c => { 
                    clientesMap[c.idUsuario] = c.nome; 
                });
            }
        } catch (error) {
            // ...
        }
        // Carregar pedidos
        const response = await fetch('/pedidos');
        let pedidos = await response.json();
        
        if (!response.ok || !Array.isArray(pedidos)) {
            // ...
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-danger">Erro ao carregar pedidos</td></tr>';
            cardsContainer.innerHTML = '<div class="text-center py-4 text-danger">Erro ao carregar pedidos</div>';
            return;
        }
        
        // Filtrar apenas pedidos pendentes e mostrar todos
        pedidos = pedidos
            .filter(p => {
                const statusNome = statusMap[p.idStatus] ? statusMap[p.idStatus].toLowerCase() : '';
                return statusNome === 'pendente';
            });
        
        if (pedidos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted">Nenhum pedido pendente</td></tr>';
            cardsContainer.innerHTML = '<div class="text-center py-4 text-muted">Nenhum pedido pendente</div>';
            return;
        }
        // Renderizar tabela
        tbody.innerHTML = pedidos.map(p => {
            const valorTotal = (parseFloat(p.preco) || 0) * (parseInt(p.quantidade) || 1);
            const valor = valorTotal.toLocaleString('pt-BR', { 
                style: 'currency', 
                currency: 'BRL' 
            });
            const data = p.dataPedido ? new Date(p.dataPedido).toLocaleDateString('pt-BR') : 'N/A';
            const status = statusMap[p.idStatus] || 'N/A';
            const endereco = p.endereco || '-';
            const clienteNome = clientesMap[p.idCliente] || 'N/A';
            
            // Mapear status para classe CSS
            let statusClass = status.toLowerCase().replace(/\s+/g, '-');
            if (statusClass === 'pendente') statusClass = 'pending';
            if (statusClass === 'enviado') statusClass = 'sent';
            if (statusClass === 'aprovado') statusClass = 'green';
            if (statusClass === 'entregue') statusClass = 'green';
            if (statusClass === 'cancelado') statusClass = 'danger';
            
            const statusBadge = `<span class="status-badge status-${statusClass}">${status}</span>`;
            
            return `
                <tr class="border-bottom border-light">
                    <td class="py-3 text-dark">${p.produtoNome || 'N/A'}</td>
                    <td class="py-3 text-dark">${clienteNome}</td>
                    <td class="py-3 text-dark">${endereco}</td>
                    <td class="py-3 text-dark">${valor}</td>
                    <td class="py-3 text-dark">${data}</td>
                    <td class="py-3">${statusBadge}</td>
                </tr>
            `;
        }).join('');

        // Renderizar cards para mobile
        cardsContainer.innerHTML = pedidos.map(p => {
            const valorTotal = (parseFloat(p.preco) || 0) * (parseInt(p.quantidade) || 1);
            const valor = valorTotal.toLocaleString('pt-BR', { 
                style: 'currency', 
                currency: 'BRL' 
            });
            const data = p.dataPedido ? new Date(p.dataPedido).toLocaleDateString('pt-BR') : 'N/A';
            const status = statusMap[p.idStatus] || 'N/A';
            const endereco = p.endereco || '-';
            const clienteNome = clientesMap[p.idCliente] || 'N/A';
            
            // Mapear status para classe CSS
            let statusClass = status.toLowerCase().replace(/\s+/g, '-');
            if (statusClass === 'pendente') statusClass = 'pending';
            if (statusClass === 'enviado') statusClass = 'sent';
            if (statusClass === 'aprovado') statusClass = 'green';
            if (statusClass === 'cancelado') statusClass = 'danger';
            
            const statusBadge = `<span class="status-badge status-${statusClass}">${status}</span>`;
            
            return `
                <div class="card border-0 bg-white mb-3 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-medium mb-0 text-dark">${p.produtoNome || 'N/A'}</h6>
                            ${statusBadge}
                        </div>
                        <div class="small text-muted mb-1">
                            <strong>Cliente:</strong> ${clienteNome}
                        </div>
                        <div class="small text-muted mb-1">
                            <strong>Endereço:</strong> ${endereco}
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <div class="small text-muted">
                                <strong>Data:</strong> ${data}
                            </div>
                            <div class="fw-medium text-dark">${valor}</div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
    } catch (error) {
        // ...
        const tbody = document.querySelector('#tabelaPedidosRecentes tbody');
        const cardsContainer = document.getElementById('cardsPedidosRecentes');
        
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-danger">Erro ao carregar</td></tr>';
        }
        if (cardsContainer) {
            cardsContainer.innerHTML = '<div class="text-center py-4 text-danger">Erro ao carregar pedidos</div>';
        }
    }
};

// =============================================================================
// INICIALIZAÇÃO
// =============================================================================

document.addEventListener('DOMContentLoaded', async function() {
    carregarUsuarioLogado();
    await atualizarDashboard();
});
