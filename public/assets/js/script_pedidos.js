// Logout global para pedidos
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
// SCRIPT DE GERENCIAMENTO DE PEDIDOS
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

// =============================================================================
// INICIALIZAÇÃO
// =============================================================================

document.addEventListener('DOMContentLoaded', function() {
        // Função para exibir mensagem
        function exibirMensagemPedido(msg, tipo = 'success') {
            const msgDiv = document.getElementById('pedidoMsg');
            if (msgDiv) {
                msgDiv.textContent = msg;
                msgDiv.className = `text-center mt-3 text-${tipo === 'success' ? 'success' : 'danger'}`;
                msgDiv.style.display = 'block';
            }
        }
        function limparMensagemPedido() {
            const msgDiv = document.getElementById('pedidoMsg');
            if (msgDiv) {
                msgDiv.textContent = '';
                msgDiv.style.display = 'none';
            }
        }
    carregarUsuarioLogado();
    
    // Atualizar clientes quando houver alteração global
    window.addEventListener('clientesAtualizados', function() {
        carregarClientesPedido();
    });
    
    // -------------------------------------------------------------------------
    // ELEMENTOS DO DOM
    // -------------------------------------------------------------------------
    const formPedido = document.getElementById('form-pedido');
    // Seleciona o tbody da tabela principal de pedidos pelo id
    const tabelaPedidos = document.querySelector('#tabelaPedidos tbody');
    const inputId = document.getElementById('pedido_id');
    const selectProduto = document.getElementById('produto_pedido');
    const inputCliente = document.getElementById('cliente_pedido');
    const inputEndereco = document.getElementById('endereco_pedido');
    const inputQuantidade = document.getElementById('quantidade_pedido');
    const selectStatus = document.getElementById('status_pedido');
    const inputValor = document.getElementById('valor_total_pedido');
    const inputData = document.getElementById('data_pedido');
    const inputDescricao = document.getElementById('descricao_pedido');
    const btnCadastrarPedido = document.getElementById('btnCadastrarPedido');
    const btnAtualizarPedido = document.getElementById('btnAtualizarPedido');
    const btnExcluirPedido = document.getElementById('btnExcluirPedido');
    
    // -------------------------------------------------------------------------
    // VARIÁVEIS DE ESTADO
    // -------------------------------------------------------------------------
    
    let pedidoSelecionado = null;
    let clientesMap = {};
    let promocoesArray = [];
    
    // -------------------------------------------------------------------------
    // FUNÇÕES UTILITÁRIAS
    // -------------------------------------------------------------------------
    
    // Converter valor formatado (com vírgula) para número
    const precoParaNumero = (valorFormatado) => {
        if (!valorFormatado) return 0;
        return parseFloat(valorFormatado.replace(',', '.'));
    };
    
    // -------------------------------------------------------------------------
    // EVENTOS DE FORMATAÇÃO
    // -------------------------------------------------------------------------
    
    // Formatar input de preço enquanto digita
    inputValor.addEventListener('input', function(e) {
        let valor = e.target.value;
        
        // Remove tudo que não é número
        valor = valor.replace(/\D/g, '');
        
        // Se não tiver nada, limpa o campo
        if (!valor) {
            e.target.value = '';
            return;
        }
        
        // Converte para número e divide por 100 para ter 2 casas decimais
        let numero = parseInt(valor) / 100;
        
        // Formata com 2 casas decimais e vírgula
        e.target.value = numero.toFixed(2).replace('.', ',');
    });
    
    // -------------------------------------------------------------------------
    // FUNÇÕES DE CARREGAMENTO DE DADOS
    // -------------------------------------------------------------------------
    
    // Carregar promoções
    const carregarPromocoes = async () => {
        try {
            const response = await fetch('/promocoes');
            promocoesArray = await response.json();
        } catch (error) {
            console.error('Erro ao carregar promoções:', error);
            promocoesArray = [];
        }
    };
    
    // Calcular preço com desconto de promoção
    const calcularPrecoComPromocao = (precoOriginal, idPromocao) => {
        if (!idPromocao) return precoOriginal;
        
        const promocao = promocoesArray.find(p => p.idPromocao == idPromocao);
        if (!promocao) return precoOriginal;
        
        // Verifica se a promoção está ativa e dentro do período
        const hoje = new Date();
        const inicio = new Date(promocao.dataInicio);
        const fim = new Date(promocao.dataFim);
        
        if (promocao.status != 1 || inicio > hoje || fim < hoje) {
            return precoOriginal;
        }
        
        let precoFinal = precoOriginal;
        const desconto = parseFloat(promocao.desconto);
        
        if (promocao.tipo_desconto === 'percentual') {
            precoFinal = precoOriginal * (1 - desconto / 100);
        } else if (promocao.tipo_desconto === 'valor') {
            precoFinal = precoOriginal - desconto;
        }
        
        return precoFinal < 0 ? 0 : precoFinal;
    };
    
    // Carregar produtos no select
    const carregarProdutos = async () => {
        try {
            // Garantir que as promoções estejam carregadas
            if (promocoesArray.length === 0) {
                await carregarPromocoes();
            }
            
            const response = await fetch('/produtos');
            const produtos = await response.json();
            selectProduto.innerHTML = '<option value="" disabled selected>Selecione um produto</option>';
            
            if (Array.isArray(produtos)) {
                produtos.forEach(produto => {
                    const option = document.createElement('option');
                    option.value = produto.idProduto;
                    option.textContent = produto.nome;
                    
                    // Calcular preço com promoção se houver
                    const precoOriginal = parseFloat(produto.preco);
                    const precoFinal = calcularPrecoComPromocao(precoOriginal, produto.id_promocao);
                    option.dataset.preco = precoFinal;
                    
                    selectProduto.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Erro ao carregar produtos:', error);
        }
    };

    const carregarClientesPedido = async () => {
        if (!inputCliente) return;
        try {
            const response = await fetch('/usuario');
            const clientes = await response.json();
            inputCliente.innerHTML = '<option value="" disabled selected>Selecione um cliente</option>';
            clientesMap = {};
            clientes.forEach(cliente => {
                inputCliente.innerHTML += `<option value="${cliente.idUsuario}">${cliente.nome}</option>`;
                clientesMap[cliente.idUsuario] = cliente.nome;
            });
        } catch (e) {
            inputCliente.innerHTML = '<option value="">Erro ao carregar clientes</option>';
        }
    };

    // Carregar status do pedido no select
    const carregarStatus = async () => {
        try {
            const response = await fetch('/status');
            const statusList = await response.json();
            window.statusMap = {};
            if (Array.isArray(statusList) && statusList.length > 0) {
                selectStatus.innerHTML = '<option value="" disabled selected>Selecione um status</option>';
                statusList.forEach(status => {
                    window.statusMap[status.idStatus] = status.nome;
                    const option = document.createElement('option');
                    option.value = status.idStatus;
                    option.textContent = status.nome;
                    selectStatus.appendChild(option);
                });
            } else {
                selectStatus.innerHTML = '<option value="" disabled selected>Nenhum status disponível - cadastre pedidos primeiro</option>';
            }
        } catch (error) {
            console.error('Erro ao carregar status:', error);
            selectStatus.innerHTML = '<option value="" disabled selected>Erro ao carregar status - tente novamente</option>';
        }
    };

    // Calcular valor total
    const calcularValorTotal = () => {
        const option = selectProduto.options[selectProduto.selectedIndex];
        const preco = option?.dataset?.preco || 0;
        const qtd = parseInt(inputQuantidade.value) || 0;
        inputValor.value = (parseFloat(preco) * qtd).toFixed(2).replace('.', ',');
    };

    // Carregar pedidos na tabela
    const carregarPedidos = async () => {
        const cardsContainer = document.getElementById('cardsPedidos');
        
        tabelaPedidos.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted">Carregando pedidos...</td></tr>';
        if (cardsContainer) {
            cardsContainer.innerHTML = '<div class="text-center py-4 text-muted">Carregando pedidos...</div>';
        }
        
        // Garantir que os clientes estejam carregados antes de exibir os pedidos
        if (Object.keys(clientesMap).length === 0) {
            await carregarClientesPedido();
        }
        
        try {
            // Corrige para buscar da porta correta
            const response = await fetch('/pedidos');
            const pedidos = await response.json();
            
            if (!Array.isArray(pedidos) || pedidos.length === 0) {
                tabelaPedidos.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">Nenhum pedido cadastrado</td></tr>';
                if (cardsContainer) {
                    cardsContainer.innerHTML = '<div class="text-center py-4 text-muted">Nenhum pedido cadastrado</div>';
                }
                return;
            }
            
            // Renderizar tabela
            tabelaPedidos.innerHTML = pedidos.map(p => {
                // Calcular valor total (preco * quantidade)
                const valorTotal = (parseFloat(p.preco) || 0) * (parseInt(p.quantidade) || 1);
                const valor = valorTotal.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                function formatarDataBR(dataStr) {
                    if (!dataStr) return '';
                    let partes = null;
                    if (/^\d{4}-\d{2}-\d{2}/.test(dataStr)) {
                        partes = dataStr.split('T')[0].split('-');
                        if (partes.length === 3) {
                            return `${partes[2]}/${partes[1]}/${partes[0]}`;
                        }
                    }
                    const d = new Date(dataStr);
                    if (!isNaN(d.getTime())) {
                        return d.toLocaleDateString('pt-BR');
                    }
                    return dataStr;
                }
                const data = p.dataPedido ? formatarDataBR(p.dataPedido) : 'Sem data';
                const statusNome = window.statusMap && window.statusMap[p.idStatus] ? window.statusMap[p.idStatus] : 'N/A';
                const quantidade = p.quantidade || 0;
                let statusClass = statusNome.toLowerCase().replace(/\s+/g, '-');
                if (statusClass === 'pendente') statusClass = 'pending';
                if (statusClass === 'enviado') statusClass = 'sent';
                if (statusClass === 'aprovado') statusClass = 'green';
                if (statusClass === 'entregue') statusClass = 'green';
                if (statusClass === 'cancelado') statusClass = 'danger';
                const statusBadge = '<span class="status-badge status-' + statusClass + '">• ' + statusNome + '</span>';
                const clienteNome = clientesMap && p.idCliente ? (clientesMap[p.idCliente] || 'N/A') : 'N/A';
                return '<tr class="border-bottom border-light"><td class="py-4 text-dark">' + p.idPedido + '</td><td class="py-4 text-dark">' + (p.produtoNome || 'N/A') + '</td><td class="py-4 text-dark">' + clienteNome + '</td><td class="py-4 text-dark">' + valor + '</td><td class="py-4 text-dark">' + (p.endereco || '-') + '</td><td class="py-4 text-dark">' + data + '</td><td class="py-4">' + statusBadge + '</td><td class="py-4 text-dark">' + quantidade + '</td><td class="py-4"><button class="btn btn-sm btn-success px-3 py-2 fw-medium rounded-4 btn-selecionar-pedido" data-id="' + p.idPedido + '">Selecionar</button></td></tr>';
            }).join('');
            
            // Renderizar cards para mobile
            if (cardsContainer) {
                cardsContainer.innerHTML = pedidos.map(p => {
                    const valorTotal = (parseFloat(p.preco) || 0) * (parseInt(p.quantidade) || 1);
                    const valor = valorTotal.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                    
                    function formatarDataBR(dataStr) {
                        if (!dataStr) return '';
                        let partes = null;
                        if (/^\d{4}-\d{2}-\d{2}/.test(dataStr)) {
                            partes = dataStr.split('T')[0].split('-');
                            if (partes.length === 3) {
                                return `${partes[2]}/${partes[1]}/${partes[0]}`;
                            }
                        }
                        const d = new Date(dataStr);
                        if (!isNaN(d.getTime())) {
                            return d.toLocaleDateString('pt-BR');
                        }
                        return dataStr;
                    }
                    
                    const data = p.dataPedido ? formatarDataBR(p.dataPedido) : 'Sem data';
                    const statusNome = window.statusMap && window.statusMap[p.idStatus] ? window.statusMap[p.idStatus] : 'N/A';
                    const quantidade = p.quantidade || 0;
                    
                    let statusClass = statusNome.toLowerCase().replace(/\s+/g, '-');
                    if (statusClass === 'pendente') statusClass = 'pending';
                    if (statusClass === 'enviado') statusClass = 'sent';
                    if (statusClass === 'aprovado') statusClass = 'green';
                    if (statusClass === 'cancelado') statusClass = 'danger';
                    const statusBadge = '<span class="status-badge status-' + statusClass + '">• ' + statusNome + '</span>';
                    const clienteNome = clientesMap && p.idCliente ? (clientesMap[p.idCliente] || 'N/A') : 'N/A';
                    
                    return `
                        <div class="card border-0 bg-white mb-3 shadow-sm rounded-4">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-medium mb-0 text-dark">${p.produtoNome || 'N/A'}</h6>
                                    ${statusBadge}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>ID:</strong> ${p.idPedido}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Cliente:</strong> ${clienteNome}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Endereço:</strong> ${p.endereco || '-'}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Quantidade:</strong> ${quantidade}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Data:</strong> ${data}
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                    <div class="fw-medium text-dark">${valor}</div>
                                    <button class="btn btn-sm btn-success px-3 py-2 fw-medium rounded-4 btn-selecionar-pedido-mobile" data-id="${p.idPedido}">
                                        Selecionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            // Adicionar evento de clique aos botões de selecionar (tabela)
            document.querySelectorAll('.btn-selecionar-pedido').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remover destaque de todas as linhas
                    tabelaPedidos.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
                    // Adicionar destaque na linha selecionada
                    this.closest('tr').classList.add('table-active');
                    selecionarPedido(this.dataset.id);
                });
            });
            
            // Adicionar evento de clique aos botões de selecionar (cards mobile)
            document.querySelectorAll('.btn-selecionar-pedido-mobile').forEach(btn => {
                btn.addEventListener('click', function() {
                    selecionarPedido(this.dataset.id);
                });
            });
        } catch (error) {
            console.error('Erro:', error);
            tabelaPedidos.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Erro ao carregar os pedidos</td></tr>';
        }
    };

    // Cadastrar pedido
    const cadastrarPedido = async (dados) => {
        try {
            const response = await fetch('/pedidos/salvar', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams(dados)
            });
            const resultado = await response.json();
            const msgDiv = document.getElementById('pedidoMsg');
            if (response.status === 201 && resultado.sucesso) {
                limparFormulario();
                if (msgDiv) {
                    msgDiv.textContent = 'Pedido cadastrado com sucesso!';
                    msgDiv.className = 'text-success text-center mt-3';
                    msgDiv.style.display = 'block';
                    setTimeout(() => {
                        msgDiv.style.display = 'none';
                        msgDiv.textContent = '';
                    }, 1500);
                }
                return { sucesso: resultado.sucesso, id: resultado.id };
            } else {
                if (msgDiv) {
                    msgDiv.textContent = resultado.erro || resultado.error || 'Erro desconhecido';
                    msgDiv.className = 'text-danger text-center mt-3';
                    msgDiv.style.display = 'block';
                }
                return { erro: resultado.erro || resultado.error || 'Erro desconhecido' };
            }
        } catch (error) {
            const msgDiv = document.getElementById('pedidoMsg');
            if (msgDiv) {
                msgDiv.textContent = 'Erro ao cadastrar pedido: ' + error.message;
                msgDiv.className = 'text-danger text-center mt-3';
                msgDiv.style.display = 'block';
            }
            return { erro: 'Erro ao cadastrar pedido: ' + error.message };
        }
    };

    // Atualizar pedido
    const atualizarPedido = async (dados) => {
        try {
            const response = await fetch('/pedidos/atualizar', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams(dados)
            });
            const resultado = await response.json();
            const msgDiv = document.getElementById('pedidoMsg');
            if (resultado.sucesso) {
                limparFormulario();
                if (msgDiv) {
                    msgDiv.textContent = 'Pedido atualizado com sucesso!';
                    msgDiv.className = 'text-success text-center mt-3';
                    msgDiv.style.display = 'block';
                    setTimeout(() => {
                        msgDiv.style.display = 'none';
                        msgDiv.textContent = '';
                    }, 1500);
                }
                carregarPedidos();
                if (typeof window.atualizarDashboard === 'function') window.atualizarDashboard();
                if (typeof window.carregarGraficoBarras === 'function') window.carregarGraficoBarras();
                if (typeof window.carregarGraficoPizza === 'function') window.carregarGraficoPizza();
            } else {
                exibirMensagemPedido('Erro: ' + (resultado.erro || 'Desconhecido'), 'danger');
            }
        } catch (error) {
            const msgDiv = document.getElementById('pedidoMsg');
            if (msgDiv) {
                msgDiv.textContent = 'Erro ao atualizar pedido';
                msgDiv.className = 'text-danger text-center mt-3';
                msgDiv.style.display = 'block';
            }
            console.error('Erro:', error);
        }
    };

    // Deletar pedido
    const deletarPedido = async (id) => {
        if (!confirm('Tem certeza que deseja excluir este pedido?')) return;
        try {
            const response = await fetch('/pedidos/deletar', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({idPedido: id})
            });
            const resultado = await response.json();
            const msgDiv = document.getElementById('pedidoMsg');
            if (resultado.sucesso) {
                // Limpa apenas os campos do formulário, mas mantém a mensagem visível
                limparFormulario();
                if (msgDiv) {
                    msgDiv.textContent = 'Pedido excluído com sucesso!';
                    msgDiv.className = 'text-success text-center mt-3';
                    msgDiv.style.display = 'block';
                    setTimeout(() => {
                        msgDiv.style.display = 'none';
                        msgDiv.textContent = '';
                        msgDiv.className = 'text-center mt-3';
                    }, 1500);
                }
                carregarPedidos();
                if (typeof window.atualizarDashboard === 'function') window.atualizarDashboard();
                if (typeof window.carregarGraficoBarras === 'function') window.carregarGraficoBarras();
                if (typeof window.carregarGraficoPizza === 'function') window.carregarGraficoPizza();
            } else {
                exibirMensagemPedido('Erro: ' + (resultado.erro || 'Desconhecido'), 'danger');
            }
        } catch (error) {
            const msgDiv = document.getElementById('pedidoMsg');
            if (msgDiv) {
                msgDiv.textContent = 'Erro ao deletar pedido';
                msgDiv.className = 'text-danger text-center mt-3';
                msgDiv.style.display = 'block';
            }
        }
    };

    // Selecionar pedido para edição
    const selecionarPedido = async (id) => {
        try {
            const response = await fetch(`/pedidos/buscar?idPedido=${id}`);
            const pedido = await response.json();
            console.log('Pedido recebido:', pedido); // Debug
            inputId.value = pedido.idPedido;
            
            // Buscar opção do select que corresponde ao produtoNome
            if (pedido.produtoNome) {
                const options = Array.from(selectProduto.options);
                const optionMatch = options.find(opt => opt.text === pedido.produtoNome);
                if (optionMatch) {
                    selectProduto.value = optionMatch.value;
                }
            }
            
            // Verifica se o pedido tem propriedades esperadas
            if (!pedido || Object.keys(pedido).length === 0) {
                return;
            }
            inputCliente.value = pedido.idCliente || '';
            inputEndereco.value = pedido.endereco || '';
            inputQuantidade.value = pedido.quantidade || '';
            // Seleciona o status pelo nome ou id
            if (pedido.idStatus) {
                selectStatus.value = pedido.idStatus;
                if (selectStatus.value !== pedido.idStatus && pedido.statusPedido) {
                    selectStatus.value = pedido.statusPedido;
                }
            } else if (pedido.statusPedido) {
                selectStatus.value = pedido.statusPedido;
            } else {
                selectStatus.value = '';
            }
            const valorTotal = (parseFloat(pedido.preco) || 0) * (parseInt(pedido.quantidade) || 1);
            inputValor.value = valorTotal.toFixed(2).replace('.', ',');
            inputData.value = pedido.dataPedido ? pedido.dataPedido.split(' ')[0] : '';
            inputDescricao.value = pedido.descricao || '';
            pedidoSelecionado = id;
            btnCadastrarPedido.disabled = true;
            btnAtualizarPedido.disabled = false;
            btnExcluirPedido.disabled = false;
            formPedido.scrollIntoView({behavior: 'smooth', block: 'start'});
        } catch (error) {
            console.error('Erro ao selecionar pedido:', error);
            exibirMensagemPedido('Erro ao carregar dados do pedido', 'danger');
        }
    };

    // Limpar formulário
    const limparFormulario = () => {
        formPedido.reset();
        selectStatus.value = ''; // Resetar para a opção padrão "Selecione um status"
        pedidoSelecionado = null;
        btnCadastrarPedido.disabled = false;
        btnAtualizarPedido.disabled = true;
        btnExcluirPedido.disabled = true;
        // Remove destaque de linha selecionada
        tabelaPedidos && tabelaPedidos.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
    };

    // Event listener para o formulário
    // (Removido: submit handler que envia apenas um item. O submit agora é tratado abaixo, enviando todos os itens da tabela dinâmica)

    // Event listeners dos botões
    if (btnAtualizarPedido) {
        btnAtualizarPedido.addEventListener('click', function() {
            if (!pedidoSelecionado) {
                exibirMensagemPedido('Selecione um pedido primeiro', 'danger');
                return;
            }
            // Pegar o nome do produto selecionado
            const produtoNome = selectProduto.options[selectProduto.selectedIndex]?.text || '';
            const dados = {
                id_pedido: pedidoSelecionado,
                produto_nome: produtoNome,
                id_cliente: inputCliente.value,
                endereco: inputEndereco.value.trim(),
                quantidade: inputQuantidade.value,
                status: selectStatus.value,
                preco: precoParaNumero(inputValor.value),
                data_pedido: inputData.value,
                descricao: inputDescricao.value.trim()
            };
            atualizarPedido(dados);
        });
    }

    if (btnExcluirPedido) {
        btnExcluirPedido.addEventListener('click', function() {
            if (!pedidoSelecionado) {
                exibirMensagemPedido('Selecione um pedido primeiro', 'danger');
                return;
            }
            deletarPedido(pedidoSelecionado);
        });
    }

    if (formPedido) {
        formPedido.addEventListener('reset', function() {
            limparFormulario();
        });
    }

    // Event listeners para cálculo automático
    if (selectProduto) selectProduto.addEventListener('change', calcularValorTotal);
    if (inputQuantidade) inputQuantidade.addEventListener('input', calcularValorTotal);
    if (inputQuantidade) {
        inputQuantidade.addEventListener('input', calcularValorTotal);
        inputQuantidade.addEventListener('change', function() {
            if (parseInt(this.value) < 0 || isNaN(parseInt(this.value))) {
                this.value = 0;
                calcularValorTotal();
            }
        });
    }

    // Desabilitar/Habilitar botões inicialmente
    if (btnCadastrarPedido) btnCadastrarPedido.disabled = false;
    if (btnAtualizarPedido) btnAtualizarPedido.disabled = true;
    if (btnExcluirPedido) btnExcluirPedido.disabled = true;

    // Inicializar
    if (selectProduto) carregarProdutos();
    if (selectStatus) carregarStatus();
    
    // Carregar clientes primeiro, depois pedidos
    async function inicializar() {
        if (inputCliente) await carregarClientesPedido();
        if (tabelaPedidos) await carregarPedidos();
    }
    inicializar();

    // Exportar funções globais para os botões onclick do HTML
    window.editarPedido = selecionarPedido;
    window.deletarPedidoDireto = deletarPedido;
        // --- ITENS DO PEDIDO ---
    // Tabela dinâmica de itens do pedido EM CRIAÇÃO
    const tabelaItensPedido = document.getElementById('tabelaItensPedido');
    const btnAdicionarItem = document.getElementById('btnAdicionarItem');
    let itensPedido = [];

        // Adicionar item à tabela dinâmica
        if (btnAdicionarItem) {
            btnAdicionarItem.addEventListener('click', function() {
                const produtoId = selectProduto.value;
                const produtoNome = selectProduto.options[selectProduto.selectedIndex]?.text || '';
                const quantidade = parseInt(inputQuantidade.value);
                const valorTotal = precoParaNumero(inputValor.value);
                
                // Calcular preço unitário
                const precoUnitario = valorTotal / quantidade;
                
                // Só adiciona se todos os campos forem válidos
                if (!produtoId || produtoId === '' || produtoNome === 'Selecione um produto' || !quantidade || quantidade <= 0 || !valorTotal || valorTotal <= 0) {
                    exibirMensagemPedido('Selecione um produto, quantidade e preço válidos.', 'danger');
                    return;
                }
                // Garante que nunca será adicionado item inválido
                itensPedido.push({ produtoId, produtoNome, quantidade, preco: precoUnitario });
                // Remove qualquer item inválido do array imediatamente
                itensPedido = itensPedido.filter(item => item.produtoNome !== 'Selecione um produto' && item.quantidade > 0 && item.preco > 0);
                atualizarTabelaItensPedido();
                // Limpar campos de produto, quantidade e preço
                selectProduto.value = '';
                inputQuantidade.value = '';
                inputValor.value = '';
            });
        }

        // Atualizar tabela de itens
        function atualizarTabelaItensPedido() {
            if (!tabelaItensPedido) return;
            const tbody = tabelaItensPedido.querySelector('tbody');
            tbody.innerHTML = '';
            const cardsContainer = document.getElementById('cardsItensPedido');
            if (cardsContainer) cardsContainer.innerHTML = '';
            if (itensPedido.length === 0) {
                tbody.innerHTML = '<tr id="linhaVaziaItensPedido"><td colspan="4" class="text-center py-3 text-muted">Nenhum item adicionado.</td></tr>';
                if (cardsContainer) {
                    cardsContainer.innerHTML = '<div class="text-center py-3 text-muted">Nenhum item adicionado.</div>';
                }
                atualizarValorTotalItens();
                return;
            }
            itensPedido.forEach((item, idx) => {
                tbody.innerHTML += `<tr>
                    <td class="py-2 text-dark">${item.produtoNome}</td>
                    <td class="py-2 text-dark">${item.quantidade}</td>
                    <td class="py-2 text-dark">${item.preco.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                    <td class="py-2">
                        <button class="btn btn-sm px-3 py-2 fw-medium rounded-4 btn-selecionar-pedido bg-danger text-white border-0" data-idx="${idx}">Remover</button>
                    </td>
                </tr>`;
                // Cards para mobile
                if (cardsContainer) {
                    cardsContainer.innerHTML += `
                        <div class="card border-0 bg-white mb-3 shadow-sm rounded-4">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-medium mb-0 text-dark">${item.produtoNome}</h6>
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Quantidade:</strong> ${item.quantidade}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Preço (R$):</strong> ${item.preco.toLocaleString('pt-BR', {minimumFractionDigits: 2})}
                                </div>
                                <div class="d-flex justify-content-end align-items-center mt-2 pt-2 border-top">
                                    <button class="btn btn-sm btn-danger px-3 py-2 fw-medium rounded-4 btn-remover-item-mobile" data-idx="${idx}">Remover</button>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
            // Adicionar evento de remover na tabela
            tbody.querySelectorAll('button[data-idx]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.idx);
                    itensPedido.splice(idx, 1);
                    atualizarTabelaItensPedido();
                });
            });
            // Adicionar evento de remover nos cards mobile
            if (cardsContainer) {
                cardsContainer.querySelectorAll('button.btn-remover-item-mobile').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const idx = parseInt(this.dataset.idx);
                        itensPedido.splice(idx, 1);
                        atualizarTabelaItensPedido();
                    });
                });
            }
            atualizarValorTotalItens();
        }

        // Função para atualizar o valor total dos itens
        function atualizarValorTotalItens() {
            const inputValorTotal = document.getElementById('valor_total_itens');
            if (!inputValorTotal) return;
            let total = 0;
            itensPedido.forEach(item => {
                total += (item.preco * item.quantidade);
            });
            inputValorTotal.value = total.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        // Sobrescrever submit do formulário para enviar todos os itens
        if (formPedido) {
            formPedido.addEventListener('submit', function(e) {
                e.preventDefault();
                limparMensagemPedido();

                // Validação detalhada dos campos principais
                if (!inputCliente.value) {
                    exibirMensagemPedido('Por favor, selecione um cliente.', 'danger');
                    inputCliente.focus();
                    return;
                }

                if (!inputEndereco.value.trim()) {
                    exibirMensagemPedido('Por favor, preencha o endereço de entrega.', 'danger');
                    inputEndereco.focus();
                    return;
                }

                if (!selectStatus.value) {
                    exibirMensagemPedido('Por favor, selecione o status do pedido.', 'danger');
                    selectStatus.focus();
                    return;
                }

                if (!inputData.value) {
                    exibirMensagemPedido('Por favor, selecione a data do pedido.', 'danger');
                    inputData.focus();
                    return;
                }

                // Limpa o array de itens antes do envio
                itensPedido = itensPedido.filter(item => {
                    return item.produtoNome && item.produtoNome !== 'Selecione um produto' && item.quantidade && item.quantidade > 0 && item.preco && item.preco > 0;
                });
                if (itensPedido.length === 0) {
                    exibirMensagemPedido('Adicione pelo menos um item válido ao pedido.', 'danger');
                    return;
                }

                // Pega o nome do status selecionado
                const statusNome = selectStatus.options[selectStatus.selectedIndex]?.text || selectStatus.value;
                // Monta o objeto do pedido com todos os itens
                const pedidoData = {
                    id_cliente: inputCliente.value,
                    endereco: inputEndereco.value.trim(),
                    data_pedido: inputData.value,
                    descricao: inputDescricao.value.trim(),
                    status: statusNome,
                    produtos: itensPedido.map(item => ({
                        produto_nome: item.produtoNome,
                        preco: item.preco,
                        quantidade: item.quantidade
                    }))
                };
                const formData = new FormData();
                formData.append('id_cliente', pedidoData.id_cliente);
                formData.append('endereco', pedidoData.endereco);
                formData.append('data_pedido', pedidoData.data_pedido);
                formData.append('descricao', pedidoData.descricao);
                formData.append('status', pedidoData.status);
                formData.append('produtos', JSON.stringify(pedidoData.produtos));

                fetch('/pedidos/salvar', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        exibirMensagemPedido('Pedido cadastrado com sucesso!', 'success');
                        itensPedido = [];
                        atualizarTabelaItensPedido();
                        carregarPedidos();
                    } else {
                        exibirMensagemPedido(data.erro || 'Erro ao cadastrar pedido.', 'danger');
                    }
                })
                .catch(() => {
                    exibirMensagemPedido('Erro ao cadastrar pedido.', 'danger');
                });
            });
        }
});
