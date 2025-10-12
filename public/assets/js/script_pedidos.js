document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPedido');
    const inputId = document.getElementById('pedidoId');
    const selectUsuario = document.getElementById('pedidoUsuario');
    const selectEndereco = document.getElementById('pedidoEndereco');
    const inputData = document.getElementById('pedidoData');
    const selectStatus = document.getElementById('pedidoStatus');
    const inputValorTotal = document.getElementById('pedidoValorTotal');

    const selectItemProduto = document.getElementById('itemProduto');
    const inputItemQuantidade = document.getElementById('itemQuantidade');
    const inputItemValorUnitario = document.getElementById('itemValorUnitario');
    const btnAdicionarItem = document.getElementById('btnAdicionarItem');
    const tabelaItensCorpo = document.querySelector('#tabelaItensPedido tbody');

    const btnSalvar = document.getElementById('btnSalvar');
    const btnAtualizar = document.getElementById('btnAtualizar');
    const btnExcluir = document.getElementById('btnExcluir');
    const btnLimpar = document.getElementById('btnLimpar');
    
    const tabelaPedidosCorpo = document.querySelector('#tabelaPedidos tbody');

    let idPedidoSelecionado = null;
    let itensDoPedido = []; 

    // --- FUNÇÕES AUXILIARES ---
    const formatarValorBRL = (valor) => parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

    // Renderiza a tabela de itens dentro do formulário
    const renderizarTabelaItens = () => {
        tabelaItensCorpo.innerHTML = '';
        let valorTotal = 0;
        itensDoPedido.forEach((item, index) => {
            const subtotal = item.quantidade * item.valor_unitario;
            valorTotal += subtotal;
            const linha = `
                <tr data-index="${index}">
                    <td>${item.nome_produto}</td>
                    <td>${item.quantidade}</td>
                    <td>${formatarValorBRL(item.valor_unitario)}</td>
                    <td>${formatarValorBRL(subtotal)}</td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger btn-remover-item">Remover</button></td>
                </tr>`;
            tabelaItensCorpo.insertAdjacentHTML('beforeend', linha);
        });
        inputValorTotal.value = formatarValorBRL(valorTotal);
    };

    // Reseta o formulário para o estado inicial
    const resetarFormulario = () => {
        form.reset();
        inputId.value = 'Auto';
        idPedidoSelecionado = null;
        itensDoPedido = [];
        renderizarTabelaItens();
        btnSalvar.classList.remove('d-none');
        btnAtualizar.classList.add('d-none');
        btnExcluir.classList.add('d-none');
        document.querySelectorAll('#tabelaPedidos tbody tr').forEach(row => row.classList.remove('table-active'));
    };

    // --- FUNÇÕES DE CARREGAMENTO DE DADOS (SELECTS E TABELA PRINCIPAL) ---

    // Carrega clientes no select
    const carregarClientesSelect = async () => {
        try {
            const response = await fetch('usuarios');
            const clientes = await response.json();
            clientes.forEach(cliente => {
                selectUsuario.insertAdjacentHTML('beforeend', `<option value="${cliente.id}">${cliente.nome}</option>`);
            });
        } catch (error) { console.error('Erro ao carregar clientes:', error); }
    };

    // Carrega endereços de um cliente específico
    const carregarEnderecosSelect = async (usuarioId) => {
        selectEndereco.innerHTML = '<option selected disabled value="">Carregando...</option>';
        try {
            const response = await fetch(`enderecos/buscarPorUsuario?id=${usuarioId}`);
            const enderecos = await response.json();
            selectEndereco.innerHTML = '<option selected disabled value="">Selecione...</option>';
            enderecos.forEach(end => {
                const texto = `${end.logradouro}, ${end.numero} - ${end.bairro}`;
                selectEndereco.insertAdjacentHTML('beforeend', `<option value="${end.idEnderecos}">${texto}</option>`);
            });
        } catch (error) { 
            console.error('Erro ao carregar endereços:', error);
            selectEndereco.innerHTML = '<option selected disabled value="">Cliente sem endereço</option>';
        }
    };
    
    // Carrega produtos no select de itens
    const carregarProdutosSelect = async () => {
        try {
            const response = await fetch('produtos');
            const produtos = await response.json();
            produtos.forEach(produto => {
                selectItemProduto.insertAdjacentHTML('beforeend', `<option value="${produto.IdProduto}" data-valor="${produto.valor}">${produto.nome}</option>`);
            });
        } catch (error) { console.error('Erro ao carregar produtos:', error); }
    };

    // Carrega a tabela principal com o histórico de pedidos
    const carregarPedidos = async () => {
        tabelaPedidosCorpo.innerHTML = `<tr><td colspan="6" class="text-center">Carregando...</td></tr>`;
        try {
            const response = await fetch('pedidos');
            if (!response.ok) throw new Error('Falha ao carregar pedidos.');
            const pedidos = await response.json();
            tabelaPedidosCorpo.innerHTML = '';
            pedidos.forEach(pedido => {
                const linha = `
                    <tr data-id="${pedido.idPedido}">
                        <td>${pedido.idPedido}</td>
                        <td>${pedido.nome_cliente || 'N/A'}</td>
                        <td>${formatarValorBRL(pedido.valor_total)}</td>
                        <td>${new Date(pedido.data_pedido).toLocaleDateString('pt-BR')}</td>
                        <td><span class="badge bg-warning text text-black">${pedido.status}</span></td>
                        <td class="text-center"><button class="btn btn-sm btn-outline-secondary btn-selecionar">Ver Detalhes</button></td>
                    </tr>`;
                tabelaPedidosCorpo.insertAdjacentHTML('beforeend', linha);
            });
        } catch (error) {
            console.error(error);
            tabelaPedidosCorpo.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Erro ao carregar pedidos.</td></tr>`;
        }
    };

    // Preenche o formulário com os dados de um pedido selecionado da lista
    const selecionarPedido = async (id) => {
        // Reseta o formulário antes de preencher
        resetarFormulario();

        try {
            // 1. Busca os dados principais do pedido
            const responsePedido = await fetch(`pedidos/buscar?id=${id}`);
            if (!responsePedido.ok) throw new Error('Pedido não encontrado.');
            const pedido = await responsePedido.json();

            // 2. Busca os itens associados a esse pedido
            const responseItens = await fetch(`itens-pedido/buscarPorPedido?id=${id}`);
            if (!responseItens.ok) throw new Error('Itens do pedido não encontrados.');
            const itens = await responseItens.json();

            // 3. Preenche o formulário com os dados do pedido
            inputId.value = pedido.idPedido;
            inputData.value = pedido.data_pedido.split(' ')[0]; // Pega apenas a data (YYYY-MM-DD)
            selectStatus.value = pedido.status;
            selectUsuario.value = pedido.usuario_id;
            
            // Carrega os endereços e depois seleciona o correto
            await carregarEnderecosSelect(pedido.usuario_id);
            selectEndereco.value = pedido.endereco_id;

            // 4. Preenche a lista de itens do pedido (em memória) e renderiza a tabela de itens
            itensDoPedido = itens.map(item => ({
                produto_id: item.produto_id,
                nome_produto: item.nome_produto, 
                quantidade: item.quantidade,
                valor_unitario: item.valor_unitario
            }));
            renderizarTabelaItens();

            // 5. Ajusta o estado da página para o modo de edição
            idPedidoSelecionado = id;
            btnSalvar.classList.add('d-none');
            btnAtualizar.classList.remove('d-none');
            btnExcluir.classList.remove('d-none');
            
            // Destaca a linha selecionada na tabela principal
            document.querySelectorAll('#tabelaPedidos tbody tr').forEach(row => {
                row.classList.remove('table-active');
                if (row.dataset.id === id) {
                    row.classList.add('table-active');
                }
            });

        } catch (error) {
            console.error(error);
            alert('Não foi possível carregar os detalhes do pedido.');
        }
    };

    // --- EVENT LISTENERS ---
    
    tabelaPedidosCorpo.addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('btn-selecionar')) {
            const linha = e.target.closest('tr');
            selecionarPedido(linha.dataset.id);
        }
    });


    // Adiciona um item ao pedido atual (em memória)
    btnAdicionarItem.addEventListener('click', () => {
        const produtoSelecionado = selectItemProduto.options[selectItemProduto.selectedIndex];
        const produtoId = parseInt(produtoSelecionado.value);
        const quantidade = parseInt(inputItemQuantidade.value);

        if (isNaN(produtoId) || isNaN(quantidade) || quantidade < 1) {
            alert('Selecione um produto e informe uma quantidade válida.');
            return;
        }

        const itemExistente = itensDoPedido.find(item => item.produto_id === produtoId);
        if (itemExistente) {
            itemExistente.quantidade += quantidade;
        } else {
            itensDoPedido.push({
                produto_id: produtoId,
                nome_produto: produtoSelecionado.text,
                quantidade: quantidade,
                valor_unitario: parseFloat(produtoSelecionado.dataset.valor)
            });
        }
        renderizarTabelaItens();
    });

    // Remove um item do pedido (em memória)
    tabelaItensCorpo.addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('btn-remover-item')) {
            const index = e.target.closest('tr').dataset.index;
            itensDoPedido.splice(index, 1);
            renderizarTabelaItens();
        }
    });

    // Atualiza o valor unitário ao selecionar um produto
    selectItemProduto.addEventListener('change', () => {
        const valor = selectItemProduto.options[selectItemProduto.selectedIndex].dataset.valor;
        inputItemValorUnitario.value = formatarValorBRL(valor || 0);
    });

    // Carrega endereços quando um cliente é selecionado
    selectUsuario.addEventListener('change', () => {
        carregarEnderecosSelect(selectUsuario.value);
    });

    // 
    btnLimpar.addEventListener('click', resetarFormulario);

    // Ação principal de Salvar/Atualizar do formulário
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (itensDoPedido.length === 0) {
            alert('Adicione pelo menos um item ao pedido antes de salvar.');
            return;
        }

        // 1. Coleta os dados do pedido
        const dadosPedido = {
            usuario_id: selectUsuario.value,
            endereco_id: selectEndereco.value,
            valor_total: itensDoPedido.reduce((total, item) => total + (item.quantidade * item.valor_unitario), 0),
            status: selectStatus.value,
            data_pedido: inputData.value // <-- LINHA ADICIONADA
        };

        try {
            // 2. Salva o pedido principal
            const responsePedido = await fetch('pedidos/salvar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dadosPedido)
            });
            const resultadoPedido = await responsePedido.json();
            if (!responsePedido.ok) throw new Error(resultadoPedido.erro || 'Erro ao criar pedido.');
            
            const novoPedidoId = resultadoPedido.id;

            // 3. Salva cada item do pedido
            for (const item of itensDoPedido) {
                const dadosItem = { ...item, pedido_id: novoPedidoId };
                await fetch('itens-pedido/salvar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dadosItem)
                });
                // Adicionar tratamento de erro por item se necessário
            }

            alert('Pedido salvo com sucesso!');
            resetarFormulario();
            carregarPedidos();

        } catch (error) {
            alert(error.message);
        }
    });

    // --- INICIALIZAÇÃO ---
    carregarPedidos();
    carregarClientesSelect();
    carregarProdutosSelect();
});