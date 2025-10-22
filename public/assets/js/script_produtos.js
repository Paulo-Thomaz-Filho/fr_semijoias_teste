// Script completo para gerenciamento de produtos
document.addEventListener('DOMContentLoaded', function() {
    // Atualiza promoções/produtos ao receber evento global
    window.addEventListener('promocaoAtualizada', async function() {
        await carregarPromocoes();
        await carregarProdutos();
    });
    // Referências aos elementos do DOM
    const formProduto = document.getElementById('form-produto');
    const inputId = document.getElementById('produto_id');
    const inputNome = document.getElementById('nome_produto');
    const inputDescricao = document.getElementById('descricao_produto');
    const inputCategoria = document.getElementById('categoria_produto');
    const inputMarca = document.getElementById('marca_produto');
    const inputPreco = document.getElementById('preco_produto');
    const selectPromocao = document.getElementById('promocao_produto');
    const inputEstoque = document.getElementById('estoque');
    const selectDisponivel = document.getElementById('disponivel');
    const tabelaProdutos = document.querySelector('#produtos-section tbody');
    const btnCadastrarProduto = document.getElementById('btnCadastrarProduto');
    const btnAtualizarProduto = document.getElementById('btnAtualizarProduto');
    const btnExcluirProduto = document.getElementById('btnExcluirProduto');

    // Mapeamento de promoções por id
    let mapaPromocoes = {};

    // Carregar promoções no select e no mapa
    const carregarPromocoes = async () => {
        try {
            const response = await fetch('/promocoes');
            const promocoes = await response.json();
            // Salva array global para uso no cálculo de preço
            window.promocoesArray = promocoes;
            selectPromocao.innerHTML = '<option value="" disabled selected>Selecione uma promoção</option>';
            selectPromocao.innerHTML += '<option value="sem">Sem promoção</option>';
            mapaPromocoes = {};
            promocoes.forEach(promocao => {
                // Só adiciona se ativa e dentro do período (backend já filtra, mas reforça no frontend)
                const hoje = new Date();
                const inicio = new Date(promocao.dataInicio);
                const fim = new Date(promocao.dataFim);
                if (promocao.status == 1 && inicio <= hoje && fim >= hoje) {
                    const option = document.createElement('option');
                    option.value = promocao.idPromocao;
                    let desconto = promocao.desconto !== undefined && promocao.desconto !== null ? parseInt(promocao.desconto) : '';
                    let tipo = (promocao.tipo_desconto === 'valor') ? 'R$' : '%';
                    let descontoFormatado = (tipo === 'R$') ? `R$ ${desconto}` : `${desconto}%`;
                    option.textContent = `${promocao.nome} - ${descontoFormatado}`;
                    selectPromocao.appendChild(option);
                    mapaPromocoes[promocao.idPromocao] = `${promocao.nome} - ${descontoFormatado}`;
                }
            });
        } catch (error) {
            console.error('Erro ao carregar promoções:', error);
        }
    };

    let produtoSelecionado = null;

    // Função para formatar preço em Real
    const formatarPreco = (preco) => {
        return parseFloat(preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };

    // Formatar input de preço enquanto digita (padrão brasileiro com vírgula automática)
    inputPreco.addEventListener('input', function(e) {
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

    // Carregar opções de disponibilidade
    const carregarDisponibilidade = () => {
        selectDisponivel.innerHTML = '<option value="" disabled selected>Selecione uma disponibilidade</option>';
        selectDisponivel.innerHTML += '<option value="1">Sim</option>';
        selectDisponivel.innerHTML += '<option value="0">Não</option>';
    };

    // Carregar produtos na tabela
    const carregarProdutos = async () => {
        tabelaProdutos.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted">Carregando produtos...</td></tr>';
        // Garante que o mapa de promoções está carregado
        if (Object.keys(mapaPromocoes).length === 0) {
            await carregarPromocoes();
        }
        
        try {
            const response = await fetch('/produtos');
            const produtos = await response.json();
            if (!Array.isArray(produtos) || produtos.length === 0) {
                tabelaProdutos.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted">Nenhum produto cadastrado</td></tr>';
                return;
            }
            tabelaProdutos.innerHTML = produtos.map(p => {
                let precoOriginal = parseFloat(p.preco);
                let precoFinal = precoOriginal;
                let promocao = 'N/A';
                // Busca promoção pelo id
                let promocaoObj = null;
                if (p.idPromocao) {
                    promocaoObj = window.promocoesArray?.find(pr => pr.idPromocao == p.idPromocao);
                }
                if (promocaoObj) {
                    let descontoFormatado = '';
                    if (promocaoObj.tipo_desconto === 'valor') {
                        descontoFormatado = 'R$ ' + parseInt(promocaoObj.desconto);
                    } else {
                        descontoFormatado = parseInt(promocaoObj.desconto) + '%';
                    }
                    promocao = promocaoObj.nome + ' - ' + descontoFormatado;
                    if (promocaoObj.tipo_desconto === 'percentual') {
                        precoFinal = precoOriginal * (1 - promocaoObj.desconto / 100);
                    } else if (promocaoObj.tipo_desconto === 'valor') {
                        precoFinal = precoOriginal - promocaoObj.desconto;
                    }
                    if (precoFinal < 0) precoFinal = 0;
                }
                const precoFormatado = formatarPreco(precoFinal);
                // Badge para disponibilidade
                const disponivelBadge = p.disponivel == 1 
                    ? '<span class="status-badge status-green">• Sim</span>' 
                    : '<span class="status-badge status-danger">• Não</span>';
                return '<tr class="border-bottom border-light">'
                    + '<td class="py-4 text-dark">' + p.idProduto + '</td>'
                    + '<td class="py-4 text-dark">' + (p.nome || 'N/A') + '</td>'
                    + '<td class="py-4 text-dark">' + precoFormatado + '</td>'
                    + '<td class="py-4 text-dark">' + (p.marca || 'N/A') + '</td>'
                    + '<td class="py-4 text-dark">' + (p.categoria || 'N/A') + '</td>'
                    + '<td class="py-4 text-dark">' + (p.estoque || 0) + '</td>'
                    + '<td class="py-4">' + disponivelBadge + '</td>'
                    + '<td class="py-4 text-dark">' + promocao + '</td>'
                    + '<td class="py-4"><button class="btn btn-sm btn-success px-3 py-2 fw-medium rounded-4 btn-selecionar-produto" data-id="' + p.idProduto + '">Selecionar</button></td>'
                    + '</tr>';
            }).join('');
                document.querySelectorAll('.btn-selecionar-produto').forEach(btnSelecionarProduto => {
                btnSelecionarProduto.addEventListener('click', function() {
                    // Remover destaque de todas as linhas
                    tabelaProdutos.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
                    // Adicionar destaque na linha selecionada
                    this.closest('tr').classList.add('table-active');
                    selecionarProduto(this.dataset.id);
                });
            });
        } catch (error) {
            console.error('Erro:', error);
            tabelaProdutos.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Erro ao carregar produtos</td></tr>';
        }
    };

    // Selecionar produto para edição
    const selecionarProduto = async (id) => {
        try {
            const response = await fetch(`/produtos/buscar?idProduto=${id}`);
            const produto = await response.json();
            console.log('Produto retornado:', produto);
            inputId.value = produto.idProduto ?? produto.id_produto ?? '';
            inputNome.value = produto.nome ?? produto.nome_produto ?? '';
            inputDescricao.value = produto.descricao ?? produto.descricao_produto ?? '';
            inputCategoria.value = produto.categoria ?? '';
            inputMarca.value = produto.marca ?? '';
            // Aplica desconto se houver promoção válida
            let precoOriginal = parseFloat(produto.preco);
            let precoFinal = precoOriginal;
            let promocaoId = produto.idPromocao ?? produto.id_promocao ?? '';
            let promocaoValida = false;
            if (promocaoId && mapaPromocoes[promocaoId]) {
                // Checa se a promoção ainda está válida
                const promocoesValidas = Object.keys(mapaPromocoes);
                if (promocoesValidas.includes(promocaoId.toString())) {
                    promocaoValida = true;
                    const texto = mapaPromocoes[promocaoId];
                    const match = texto.match(/([\d]+)(%|R\$)/);
                    if (match) {
                        const valorDesconto = parseInt(match[1]);
                        const tipo = match[2];
                        if (tipo === '%') {
                            precoFinal = precoOriginal * (1 - valorDesconto / 100);
                        } else if (tipo === 'R$') {
                            precoFinal = precoOriginal - valorDesconto;
                        }
                        if (precoFinal < 0) precoFinal = 0;
                    }
                }
            }
            // Se a promoção não for válida, seleciona 'sem promoção' e volta ao preço original
            if (!promocaoValida) {
                selectPromocao.value = 'sem';
                inputPreco.value = precoOriginal ? precoOriginal.toFixed(2).replace('.', ',') : '';
            } else {
                selectPromocao.value = promocaoId;
                inputPreco.value = precoFinal ? precoFinal.toFixed(2).replace('.', ',') : '';
            }
            inputEstoque.value = produto.estoque ?? produto.qtd_estoque ?? 0;
            selectDisponivel.value = produto.disponivel ?? produto.status ?? 1;
            produtoSelecionado = produto;
            btnCadastrarProduto.disabled = true;
            btnAtualizarProduto.disabled = false;
            btnExcluirProduto.disabled = false;
            formProduto.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (error) {
            console.error('Erro ao selecionar produto:', error);
            alert('Erro ao carregar dados do produto');
        }
    };

    // Cadastrar produto
    const cadastrarProduto = async (dados) => {
        try {
            const response = await fetch('/produtos/salvar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(dados)
            });
            const result = await response.json();
            if (response.ok) {
                alert('Produto cadastrado com sucesso!');
                limparFormulario();
                carregarProdutos();
                if (typeof window.carregarGraficoPizza === 'function') window.carregarGraficoPizza();
                if (typeof window.atualizarDashboard === 'function') window.atualizarDashboard();
            } else {
                alert(result.erro || 'Erro ao cadastrar produto');
            }
        } catch (error) {
            console.error('Erro ao cadastrar produto:', error);
            alert('Erro ao cadastrar produto');
        }
    };

    // Atualizar produto
    const atualizarProduto = async (dados) => {
        try {
            const response = await fetch('/produtos/atualizar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(dados)
            });
            const result = await response.json();
            if (response.ok) {
                alert('Produto atualizado com sucesso!');
                limparFormulario();
                carregarProdutos();
                if (typeof window.carregarGraficoPizza === 'function') window.carregarGraficoPizza();
                if (typeof window.atualizarDashboard === 'function') window.atualizarDashboard();
            } else {
                alert(result.erro || 'Erro ao atualizar produto');
            }
        } catch (error) {
            console.error('Erro ao atualizar produto:', error);
            alert('Erro ao atualizar produto');
        }
    };

    // Deletar produto
    const deletarProduto = async (id) => {
        if (!confirm('Tem certeza que deseja excluir este produto?')) return;
        try {
            const response = await fetch('/produtos/deletar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ idProduto: id })
            });
            const result = await response.json();
            if (response.ok) {
                alert('Produto excluído com sucesso!');
                limparFormulario();
                carregarProdutos();
                if (typeof window.carregarGraficoPizza === 'function') window.carregarGraficoPizza();
                if (typeof window.atualizarDashboard === 'function') window.atualizarDashboard();
            } else {
                alert(result.erro || 'Erro ao excluir produto');
            }
        } catch (error) {
            console.error('Erro ao deletar produto:', error);
            alert('Erro ao deletar produto');
        }
    };

    // Limpar formulário
    const limparFormulario = () => {
        formProduto.reset();
        inputId.value = 'Auto';
        selectDisponivel.value = '';
        produtoSelecionado = null;
        btnCadastrarProduto.disabled = false;
        btnAtualizarProduto.disabled = true;
        btnExcluirProduto.disabled = true;
        // Remove destaque de linha selecionada
        tabelaProdutos && tabelaProdutos.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
    };

    // Event listener para o formulário
    formProduto.addEventListener('submit', function(e) {
        e.preventDefault();
        let idPromocaoValue = selectPromocao.value;
        if (idPromocaoValue === 'sem') idPromocaoValue = '';
        const dados = {
            nome: inputNome.value,
            descricao: inputDescricao.value,
            preco: precoParaNumero(inputPreco.value),
            marca: inputMarca.value,
            categoria: inputCategoria.value,
            idPromocao: idPromocaoValue,
            estoque: inputEstoque.value || 0,
            disponivel: selectDisponivel.value
        };
        if (produtoSelecionado) {
            dados.idProduto = inputId.value;
            atualizarProduto(dados);
        } else {
            cadastrarProduto(dados);
        }
    });

    // Event listener do Botão Atualizar
    btnAtualizarProduto.addEventListener('click', function() {
        if (!produtoSelecionado) {
            alert('Selecione um produto primeiro');
            return;
        }
        let idPromocaoValue = selectPromocao.value;
        if (idPromocaoValue === 'sem') idPromocaoValue = '';
        const dados = {
            idProduto: inputId.value,
            nome: inputNome.value,
            descricao: inputDescricao.value,
            preco: precoParaNumero(inputPreco.value),
            marca: inputMarca.value,
            categoria: inputCategoria.value,
            idPromocao: idPromocaoValue,
            estoque: inputEstoque.value || 0,
            disponivel: selectDisponivel.value
        };
        atualizarProduto(dados);
    });

    // Event listener do Botão Excluir
    btnExcluirProduto.addEventListener('click', function() {
        if (!produtoSelecionado) {
            alert('Selecione um produto primeiro');
            return;
        }
        deletarProduto(inputId.value);
    });

    // Botão Limpar
    formProduto.addEventListener('reset', function() {
        limparFormulario();
    });

    // Desabilitar/Habilitar botões inicialmente
    btnCadastrarProduto.disabled = false;
    btnAtualizarProduto.disabled = true;
    btnExcluirProduto.disabled = true;

    carregarPromocoes();
    carregarDisponibilidade();
    carregarProdutos();
});
