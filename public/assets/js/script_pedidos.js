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
        tabelaPedidos.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted">Carregando pedidos...</td></tr>';
        
        // Garantir que os clientes estejam carregados antes de exibir os pedidos
        if (Object.keys(clientesMap).length === 0) {
            await carregarClientesPedido();
        }
        
        try {
            // Corrige para buscar da porta correta
            const response = await fetch('http://localhost:8000/pedidos');
            const pedidos = await response.json();
            if (!Array.isArray(pedidos) || pedidos.length === 0) {
                tabelaPedidos.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">Nenhum pedido cadastrado</td></tr>';
                return;
            }
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
                if (statusClass === 'concluído' || statusClass === 'concluido') statusClass = 'green';
                if (statusClass === 'cancelado') statusClass = 'danger';
                const statusBadge = '<span class="status-badge status-' + statusClass + '">• ' + statusNome + '</span>';
                const clienteNome = clientesMap && p.idCliente ? (clientesMap[p.idCliente] || 'N/A') : 'N/A';
                return '<tr class="border-bottom border-light"><td class="py-4 text-dark">' + p.idPedido + '</td><td class="py-4 text-dark">' + (p.produtoNome || 'N/A') + '</td><td class="py-4 text-dark">' + clienteNome + '</td><td class="py-4 text-dark">' + valor + '</td><td class="py-4 text-dark">' + (p.endereco || '-') + '</td><td class="py-4 text-dark">' + data + '</td><td class="py-4">' + statusBadge + '</td><td class="py-4 text-dark">' + quantidade + '</td><td class="py-4"><button class="btn btn-sm btn-success px-3 py-2 fw-medium rounded-4 btn-selecionar-pedido" data-id="' + p.idPedido + '">Selecionar</button></td></tr>';
            }).join('');
            
            // Adicionar evento de clique aos botões de selecionar
            document.querySelectorAll('.btn-selecionar-pedido').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remover destaque de todas as linhas
                    tabelaPedidos.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
                    // Adicionar destaque na linha selecionada
                    this.closest('tr').classList.add('table-active');
                    selecionarPedido(this.dataset.id);
                });
            });
        } catch (error) {
            console.error('Erro:', error);
            tabelaPedidos.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Erro ao carregar pedidos</td></tr>';
        }
    };

    // Cadastrar pedido
    const cadastrarPedido = async (dados) => {
        try {
            console.log('Enviando dados:', dados); // Debug
            const response = await fetch('/pedidos/salvar', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams(dados)
            });
            console.log('Response status:', response.status); // Debug
            const resultado = await response.json();
            console.log('Resultado:', resultado); // Debug
            if (response.status === 201 && resultado.sucesso) {
                // Sucesso: nunca mostra erro
                return { sucesso: resultado.sucesso, id: resultado.id };
            } else {
                // Só mostra erro se realmente não cadastrou
                return { erro: resultado.erro || resultado.error || 'Erro desconhecido' };
            }
        } catch (error) {
            console.error('Erro completo:', error);
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
            if (resultado.sucesso) {
                alert('Pedido atualizado com sucesso!');
                limparFormulario();
                carregarPedidos();
                if (typeof window.atualizarDashboard === 'function') window.atualizarDashboard();
                if (typeof window.carregarGraficoBarras === 'function') window.carregarGraficoBarras();
                if (typeof window.carregarGraficoPizza === 'function') window.carregarGraficoPizza();
            } else {
                alert('Erro: ' + (resultado.erro || 'Desconhecido'));
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao atualizar pedido');
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
            if (resultado.sucesso) {
                alert('Pedido excluído com sucesso!');
                limparFormulario();
                carregarPedidos();
                if (typeof window.atualizarDashboard === 'function') window.atualizarDashboard();
                if (typeof window.carregarGraficoBarras === 'function') window.carregarGraficoBarras();
                if (typeof window.carregarGraficoPizza === 'function') window.carregarGraficoPizza();
            } else {
                alert('Erro: ' + (resultado.erro || 'Desconhecido'));
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao deletar pedido');
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
            alert('Erro ao carregar dados do pedido');
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
                alert('Selecione um pedido primeiro');
                return;
            }
            // Pegar o nome do produto selecionado
            const produtoNome = selectProduto.options[selectProduto.selectedIndex]?.text || '';
            const dados = {
                idPedido: pedidoSelecionado,
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
                alert('Selecione um pedido primeiro');
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
                    alert('Selecione um produto, quantidade e preço válidos.');
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
            if (itensPedido.length === 0) {
                tbody.innerHTML = '<tr id="linhaVaziaItensPedido"><td colspan="4" class="text-center py-3 text-muted">Nenhum item adicionado.</td></tr>';
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
            });
            // Adicionar evento de remover
            tbody.querySelectorAll('button[data-idx]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.idx);
                    itensPedido.splice(idx, 1);
                    atualizarTabelaItensPedido();
                });
            });
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
                // Validação dos campos principais
                if (!inputCliente.value || !inputEndereco.value.trim() || !selectStatus.value || !inputData.value) {
                    alert('Preencha todos os campos obrigatórios do pedido.');
                    return;
                }
                // Limpa o array de itens antes do envio
                itensPedido = itensPedido.filter(item => {
                    return item.produtoNome && item.produtoNome !== 'Selecione um produto' && item.quantidade && item.quantidade > 0 && item.preco && item.preco > 0;
                });
                if (itensPedido.length === 0) {
                    alert('Adicione pelo menos um item válido ao pedido.');
                    return;
                }
                // Pega o nome do status selecionado
                const statusNome = selectStatus.options[selectStatus.selectedIndex]?.text || selectStatus.value;
                // Envia cada item da tabela dinâmica como pedido separado
                Promise.all(itensPedido.map(item => {
                    const dados = {
                        produto_nome: item.produtoNome,
                        id_cliente: inputCliente.value,
                        preco: item.preco,
                        endereco: inputEndereco.value.trim(),
                        quantidade: item.quantidade,
                        data_pedido: inputData.value,
                        descricao: inputDescricao.value.trim(),
                        status: statusNome
                    };
                    return cadastrarPedido(dados);
                })).then((resultados) => {
                    // Verifica o resultado de cada item
                    const sucessoCount = resultados.filter(r => r && typeof r.sucesso === 'string' && r.sucesso.toLowerCase().includes('sucesso')).length;
                    if (sucessoCount > 0) {
                        alert('Pedido cadastrado com sucesso!');
                    } else {
                        alert('Erro: Dados incompletos. Produto, Cliente, Quantidade e Preço são obrigatórios.');
                    }
                    // Limpa o array de itens para garantir que não fique resíduo
                    itensPedido = [];
                    atualizarTabelaItensPedido();
                    carregarPedidos();
                });
            });
        }
});
