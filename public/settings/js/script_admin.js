$(document).ready(function() {
    
    // --- SELETORES GLOBAIS ---
    const formProduto = $('#form-produto');
    const tabelaProdutosCorpo = $('#tabela-produtos-corpo');
    const tabelaPedidosDashboard = $('#tabela-pedidos-dashboard');
    const produtoIdField = $('#produto_id'); // Campo hidden
    const produtoIdDisplay = $('#produto_id_display'); // Campo visível
    const btnExcluirProduto = $('#btn-excluir-produto');

    // --- FUNÇÕES DE CARREGAMENTO DE DADOS ---

    /**
     * Carrega as estatísticas dos cards do dashboard.
     */
    function carregarStatsDashboard() {
        $.getJSON('api/dashboard/stats')
            .done(function(data) {
                const totalGanhos = parseFloat(data.totalGanhos || 0).toFixed(2).replace('.', ',');
                $('#card-total-ganhos').text(`R$ ${totalGanhos}`);
                $('#card-vendas-mes').text(`${data.vendasNoMes || 0} Vendas`);
                $('#card-total-usuarios').text(`${data.totalUsuarios || 0} Usuários`);
                $('#card-item-mais-vendido').text(data.itemMaisVendido || 'N/A');
            })
            .fail(function() {
                console.error("Erro ao carregar estatísticas do dashboard.");
            });
    }

    /**
     * Carrega os pedidos recentes na tabela do dashboard.
     */
    function carregarPedidosDashboard() {
        $.getJSON('api/pedidos/listarTodos')
            .done(function(pedidos) {
                tabelaPedidosDashboard.empty();
                if (!pedidos || pedidos.length === 0) {
                    tabelaPedidosDashboard.html('<tr><td colspan="5" class="text-center p-4">Nenhum pedido recente.</td></tr>');
                    return;
                }
                pedidos.slice(0, 5).forEach(pedido => { // Mostra apenas os 5 mais recentes
                    const precoFormatado = parseFloat(pedido.valor_total).toFixed(2).replace('.', ',');
                    const dataFormatada = new Date(pedido.data_pedido).toLocaleDateString('pt-BR');
                    const statusClasses = { 'concluido': 'status-success', 'processando': 'status-warning', 'cancelado': 'status-danger' };
                    const statusClass = statusClasses[pedido.status.toLowerCase()] || 'text-muted';

                    const linha = `
                        <tr class="border-bottom border-light">
                            <td class="py-3 text-dark">#${pedido.id_pedido}</td>
                            <td class="py-3 text-dark">${pedido.nome_cliente || 'N/A'}</td>
                            <td class="py-3 text-dark">R$ ${precoFormatado}</td>
                            <td class="py-3 text-dark">${dataFormatada}</td>
                            <td class="py-3"><span class="px-2 py-1 rounded-pill fw-medium small ${statusClass}">• ${pedido.status}</span></td>
                        </tr>`;
                    tabelaPedidosDashboard.append(linha);
                });
            })
            .fail(function() {
                tabelaPedidosDashboard.html('<tr><td colspan="5" class="text-center p-4 text-danger">Erro ao carregar pedidos.</td></tr>');
            });
    }

    /**
     * Busca os produtos na API e popula a tabela da secção de produtos.
     */
    function carregarProdutos() {
        $.getJSON('api/produtos/listar')
            .done(function(produtos) {
                tabelaProdutosCorpo.empty();
                if (!produtos || produtos.length === 0) {
                    tabelaProdutosCorpo.html('<tr><td colspan="9" class="text-center p-4">Nenhum produto encontrado.</td></tr>');
                    return;
                }
                produtos.forEach(produto => {
                    const statusClass = produto.disponivel == 1 ? 'status-success' : 'status-danger';
                    const statusText = produto.disponivel == 1 ? 'Sim' : 'Não';
                    const precoFormatado = parseFloat(produto.preco).toFixed(2).replace('.', ',');
                    const avaliacaoFormatada = parseFloat(produto.avaliacao || 0).toFixed(2);

                    const linha = `
                        <tr class="border-bottom border-light">
                            <td class="py-4 text-dark">${produto.id_produto}</td>
                            <td class="py-4 text-dark">${produto.nome}</td>
                            <td class="py-4 text-dark">R$ ${precoFormatado}</td>
                            <td class="py-4 text-dark">${produto.nome_marca || 'N/A'}</td>
                            <td class="py-4 text-dark">${produto.nome_categoria || 'N/A'}</td>
                            <td class="py-4 text-dark">${produto.quantidade_estoque}</td>
                            <td class="py-4 text-dark">${avaliacaoFormatada}</td>
                            <td class="py-4">
                                <span class="px-2 py-1 rounded-pill fw-medium small ${statusClass}">• ${statusText}</span>
                            </td>
                            <td class="py-3">
                                <button class="btn btn-primary btn-sm rounded-3 btn-selecionar-produto" data-id="${produto.id_produto}">Editar</button>
                            </td>
                        </tr>`;
                    tabelaProdutosCorpo.append(linha);
                });
            })
            .fail(function() {
                tabelaProdutosCorpo.html('<tr><td colspan="9" class="text-center p-4 text-danger">Erro ao carregar produtos.</td></tr>');
            });
    }

    /**
     * Carrega as opções dos selects de Marcas e Categorias no formulário de produtos.
     */
    function carregarOpcoesSelects() {
        $.getJSON('api/categorias/listar').done(function(data) {
            const select = $('#categoria_id');
            select.find('option:gt(0)').remove();
            data.forEach(item => select.append(`<option value="${item.id_categoria}">${item.nome}</option>`));
        });
        $.getJSON('api/marcas/listar').done(function(data) {
            const select = $('#marca_id');
            select.find('option:gt(0)').remove();
            data.forEach(item => select.append(`<option value="${item.id_marca}">${item.nome}</option>`));
        });
    }

    /**
     * Limpa o formulário de produtos e o prepara para um novo cadastro.
     */
    function limparFormularioProduto() {
        formProduto[0].reset();
        produtoIdField.val('');
        produtoIdDisplay.val('Automático');
        btnExcluirProduto.hide();
        formProduto.find('button[type="submit"]').text('Cadastrar').removeClass('btn-primary').addClass('btn-success');
    }

    // --- MANIPULADORES DE EVENTOS ---

    // Envio do formulário (Criar ou Atualizar)
    formProduto.on('submit', function(event) {
        event.preventDefault();
        const produtoId = produtoIdField.val();
        const isUpdating = !!produtoId;
        const url = isUpdating ? 'api/produto/atualizar' : 'api/produto/salvar';

        const dadosProduto = {
            id_produto: produtoId || null,
            nome: $('#nome_produto').val(),
            preco: $('#valor_produto').val(),
            quantidade_estoque: $('#unidade_estoque').val(),
            id_marca: $('#marca_id').val(),
            id_categoria: $('#categoria_id').val(),
            avaliacao: $('#avaliacao').val(),
            disponivel: $('#disponivel').val(),
        };

        $.ajax({
            url: url,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(dadosProduto),
            success: function(response) {
                if (response.success) {
                    alert(`Produto ${isUpdating ? 'atualizado' : 'cadastrado'} com sucesso!`);
                    limparFormularioProduto();
                    carregarProdutos();
                } else {
                    alert('Erro: ' + (response.error || 'Não foi possível salvar o produto.'));
                }
            },
            error: function() {
                alert('Ocorreu um erro de comunicação ao salvar o produto.');
            }
        });
    });

    // Click no botão "Editar" na tabela de produtos
    tabelaProdutosCorpo.on('click', '.btn-selecionar-produto', function() {
        const id = $(this).data('id');
        $.getJSON(`api/produto/buscar?id=${id}`, function(produto) {
            if (produto) {
                produtoIdField.val(produto.id_produto);
                produtoIdDisplay.val(produto.id_produto);
                $('#nome_produto').val(produto.nome);
                $('#valor_produto').val(produto.preco);
                $('#unidade_estoque').val(produto.quantidade_estoque);
                $('#marca_id').val(produto.id_marca);
                $('#categoria_id').val(produto.id_categoria);
                $('#avaliacao').val(produto.avaliacao);
                $('#disponivel').val(produto.disponivel ? '1' : '0');
                
                formProduto.find('button[type="submit"]').text('Atualizar').removeClass('btn-success').addClass('btn-primary');
                btnExcluirProduto.show();

                $('html, body').animate({ scrollTop: formProduto.offset().top - 20 }, 300);
            }
        });
    });

    // Click no botão "Limpar" do formulário de produtos
    formProduto.find('button[type="reset"]').on('click', function() {
        limparFormularioProduto();
    });
    
    // Click no botão "Sair"
    $('#logoutButton, #logoutButtonDropdown').on('click', function(e){
        e.preventDefault();
        // A melhor prática é ter uma rota de logout no backend, mas o redirecionamento funciona para o front-end
        window.location.href = 'login';
    });

    // --- INICIALIZAÇÃO ---
    // Carrega os dados do dashboard assim que a página é carregada
    carregarStatsDashboard();
    carregarPedidosDashboard();

    $('a[onclick="showSection(\'produtos-section\')"]').on('click', function() {
        carregarProdutos();
        carregarOpcoesSelects();
    });

    $('a[onclick="showSection(\'pedidos-section\')"]').on('click', function() {
    carregarStatsDashboard();
    carregarPedidosDashboard();
    });
});

