// Script completo para gerenciamento de pedidos
document.addEventListener('DOMContentLoaded', function() {
    // Referências aos elementos do DOM
    const formPedido = document.getElementById('form-pedido');
    const tabelaPedidos = document.querySelector('#pedidos-section table tbody');
    const inputId = document.getElementById('pedido_id');
    const selectProduto = document.getElementById('produto_pedido');
    const inputCliente = document.getElementById('cliente_pedido');
    const inputEndereco = document.getElementById('endereco_pedido');
    const inputQuantidade = document.getElementById('quantidade_pedido');
    const selectStatus = document.getElementById('status_pedido');
    const inputValor = document.getElementById('valor_total_pedido');
    const inputData = document.getElementById('data_pedido_pedido');
    const inputDescricao = document.getElementById('descricao_pedido');
    const btnCadastrar = document.getElementById('btn-cadastrar-pedido');
    const btnAtualizar = document.getElementById('btn-atualizar-pedido');
    const btnExcluir = document.getElementById('btn-excluir-pedido');
    
    let pedidoSelecionado = null;

    // Formatar input de preço enquanto digita (padrão brasileiro com vírgula automática)
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
    
    // Função para converter valor formatado (com vírgula) para número
    const precoParaNumero = (valorFormatado) => {
        if (!valorFormatado) return 0;
        return parseFloat(valorFormatado.replace(',', '.'));
    };

    // Carregar produtos no select
    const carregarProdutos = async () => {
        try {
            const response = await fetch('/app/controls/Produto/listar.php');
            const produtos = await response.json();
            selectProduto.innerHTML = '<option value="" disabled selected>Selecione um produto</option>';
            
            if (Array.isArray(produtos)) {
                produtos.forEach(produto => {
                    const option = document.createElement('option');
                    option.value = produto.idProduto;
                    option.textContent = produto.nome;
                    option.dataset.preco = produto.preco;
                    selectProduto.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Erro ao carregar produtos:', error);
        }
    };

    // Carregar status no select
    const carregarStatus = async () => {
        try {
            const response = await fetch('/app/controls/Pedido/listar.php?only_status=true');
            const statusList = await response.json();
            selectStatus.innerHTML = '<option value="" disabled selected>Selecione um status</option>';
            
            if (Array.isArray(statusList)) {
                statusList.forEach(status => {
                    const option = document.createElement('option');
                    option.value = status;
                    option.textContent = status;
                    selectStatus.appendChild(option);
                });
            } else {
                // Fallback: adicionar status padrão
                selectStatus.innerHTML = '<option value="" disabled selected>Selecione um status</option><option value="Pendente">Pendente</option><option value="Enviado">Enviado</option><option value="Cancelado">Cancelado</option>';
            }
        } catch (error) {
            console.error('Erro ao carregar status:', error);
            // Fallback: adicionar status padrão em caso de erro
            selectStatus.innerHTML = '<option value="" disabled selected>Selecione um status</option><option value="Pendente">Pendente</option><option value="Enviado">Enviado</option><option value="Cancelado">Cancelado</option>';
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
        tabelaPedidos.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Carregando pedidos...</td></tr>';
        
        try {
            const response = await fetch('/app/controls/Pedido/listar.php');
            const pedidos = await response.json();
            if (!Array.isArray(pedidos) || pedidos.length === 0) {
                tabelaPedidos.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">Nenhum pedido cadastrado</td></tr>';
                return;
            }
            tabelaPedidos.innerHTML = pedidos.map(p => {
                // Calcular valor total (preco * quantidade)
                const valorTotal = (parseFloat(p.preco) || 0) * (parseInt(p.quantidade) || 1);
                const valor = valorTotal.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
                const data = p.dataPedido ? new Date(p.dataPedido).toLocaleDateString('pt-BR') : 'Sem data';
                const status = p.status || 'Pendente';
                const quantidade = p.quantidade || 0;
                
                // Mapear status para classe CSS
                const statusClass = status.toLowerCase().replace(/\s+/g, '-');
                const statusBadge = '<span class="status-badge status-' + statusClass + '">' + status + '</span>';
                
                return '<tr class="border-bottom border-light"><td class="py-4 text-dark">' + p.idPedido + '</td><td class="py-4 text-dark">' + (p.produtoNome || 'N/A') + '</td><td class="py-4 text-dark">' + (p.clienteNome || 'N/A') + '</td><td class="py-4 text-dark">' + valor + '</td><td class="py-4 text-dark">' + (p.endereco || '-') + '</td><td class="py-4 text-dark">' + data + '</td><td class="py-4">' + statusBadge + '</td><td class="py-4 text-dark">' + quantidade + '</td><td class="py-4"><button class="btn btn-sm btn-success px-3 py-2 fw-medium rounded-4 btn-selecionar-pedido" data-id="' + p.idPedido + '">Selecionar</button></td></tr>';
            }).join('');
            
            // Adicionar evento de clique aos botões de selecionar
            document.querySelectorAll('.btn-selecionar-pedido').forEach(btn => {
                btn.addEventListener('click', function() {
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
            const response = await fetch('/app/controls/Pedido/salvar.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams(dados)
            });
            console.log('Response status:', response.status); // Debug
            const resultado = await response.json();
            console.log('Resultado:', resultado); // Debug
            if (resultado.sucesso || resultado.success || resultado.message) {
                alert('Pedido cadastrado com sucesso!');
                limparFormulario();
                carregarPedidos();
                if (typeof window.atualizarDashboard === 'function') window.atualizarDashboard();
            } else {
                alert('Erro: ' + (resultado.erro || resultado.error || JSON.stringify(resultado)));
            }
        } catch (error) {
            console.error('Erro completo:', error);
            alert('Erro ao cadastrar pedido: ' + error.message);
        }
    };

    // Atualizar pedido
    const atualizarPedido = async (dados) => {
        try {
            const response = await fetch('/app/controls/Pedido/atualizar.php', {
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
            const response = await fetch('/app/controls/Pedido/deletar.php', {
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
            const response = await fetch(`/app/controls/Pedido/buscarPorId.php?idPedido=${id}`);
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
            
            inputCliente.value = pedido.clienteNome || '';
            inputEndereco.value = pedido.endereco || '';
            inputQuantidade.value = pedido.quantidade || '';
            selectStatus.value = pedido.status || 'Pendente';
            // Calcular valor total
            const valorTotal = (parseFloat(pedido.preco) || 0) * (parseInt(pedido.quantidade) || 1);
            inputValor.value = valorTotal.toFixed(2).replace('.', ',');
            inputData.value = pedido.dataPedido ? pedido.dataPedido.split(' ')[0] : '';
            inputDescricao.value = pedido.descricao || '';
            pedidoSelecionado = id;
            btnCadastrar.disabled = true;
            btnAtualizar.disabled = false;
            btnExcluir.disabled = false;
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
        btnCadastrar.disabled = false;
        btnAtualizar.disabled = true;
        btnExcluir.disabled = true;
    };

    // Event listener para o formulário
    if (formPedido) {
        formPedido.addEventListener('submit', function(e) {
            e.preventDefault();
            // Pegar o nome do produto selecionado
            const produtoNome = selectProduto.options[selectProduto.selectedIndex]?.text || '';
            const dados = {
                produto_nome: produtoNome,
                cliente_nome: inputCliente.value.trim(),
                endereco: inputEndereco.value.trim(),
                quantidade: inputQuantidade.value,
                status: selectStatus.value,
                preco: precoParaNumero(inputValor.value),
                data_pedido: inputData.value,
                descricao: inputDescricao.value.trim()
            };
            if (pedidoSelecionado) {
                dados.idPedido = pedidoSelecionado;
                atualizarPedido(dados);
            } else {
                cadastrarPedido(dados);
            }
        });
    }

    // Event listeners dos botões
    if (btnAtualizar) {
        btnAtualizar.addEventListener('click', function() {
            if (!pedidoSelecionado) {
                alert('Selecione um pedido primeiro');
                return;
            }
            // Pegar o nome do produto selecionado
            const produtoNome = selectProduto.options[selectProduto.selectedIndex]?.text || '';
            const dados = {
                idPedido: pedidoSelecionado,
                produto_nome: produtoNome,
                cliente_nome: inputCliente.value.trim(),
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

    if (btnExcluir) {
        btnExcluir.addEventListener('click', function() {
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

    // Desabilitar/Habilitar botões inicialmente
    if (btnCadastrar) btnCadastrar.disabled = false;
    if (btnAtualizar) btnAtualizar.disabled = true;
    if (btnExcluir) btnExcluir.disabled = true;

    // Inicializar
    if (selectProduto) carregarProdutos();
    if (selectStatus) carregarStatus();
    if (tabelaPedidos) carregarPedidos();

    // Exportar funções globais para os botões onclick do HTML
    window.editarPedido = selecionarPedido;
    window.deletarPedidoDireto = deletarPedido;
});
