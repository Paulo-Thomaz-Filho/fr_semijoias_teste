// Script completo para gerenciamento de produtos
document.addEventListener('DOMContentLoaded', function() {
    // Referências aos elementos do DOM
    const formProduto = document.getElementById('form-produto');
    const inputId = document.getElementById('produto_id');
    const inputNome = document.getElementById('nome_produto');
    const inputDescricao = document.getElementById('descricao_produto');
    const selectCategoria = document.getElementById('categoria_id');
    const selectMarca = document.getElementById('marca_id');
    const inputPreco = document.getElementById('preco_produto');
    const selectPromocao = document.getElementById('promocao_id');
    const inputEstoque = document.getElementById('unidade_estoque');
    const selectDisponivel = document.getElementById('disponivel');
    const tabelaProdutos = document.querySelector('#produtos-section table tbody');
    
    // Botões
    const btnCadastrar = document.getElementById('btn-cadastrar-produto');
    const btnAtualizar = document.getElementById('btn-atualizar-produto');
    const btnExcluir = document.getElementById('btn-excluir-produto');
    
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

    // Carregar marcas no select
    const carregarMarcas = async () => {
        try {
            const response = await fetch('/app/controls/Marca/listar.php');
            const marcas = await response.json();
            selectMarca.innerHTML = '<option value="" disabled selected>Selecione uma marca</option>';
            marcas.forEach(marca => {
                const option = document.createElement('option');
                option.value = marca.idMarca;
                option.textContent = marca.nome;
                selectMarca.appendChild(option);
            });
        } catch (error) {
            console.error('Erro ao carregar marcas:', error);
        }
    };

    // Carregar categorias no select
    const carregarCategorias = async () => {
        try {
            const response = await fetch('/app/controls/Categoria/listar.php');
            const categorias = await response.json();
            selectCategoria.innerHTML = '<option value="" disabled selected>Selecione uma categoria</option>';
            categorias.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.idCategoria;
                option.textContent = categoria.nome;
                selectCategoria.appendChild(option);
            });
        } catch (error) {
            console.error('Erro ao carregar categorias:', error);
        }
    };

    // Carregar promoções no select
    const carregarPromocoes = async () => {
        try {
            const response = await fetch('/app/controls/Promocao/listar.php');
            const promocoes = await response.json();
            selectPromocao.innerHTML = '<option value="" disabled selected>Selecione uma promoção</option>';
            promocoes.forEach(promocao => {
                const option = document.createElement('option');
                option.value = promocao.idPromocao;
                option.textContent = `${promocao.nome} - ${promocao.desconto}%`;
                selectPromocao.appendChild(option);
            });
        } catch (error) {
            console.error('Erro ao carregar promoções:', error);
        }
    };

    // Carregar opções de disponibilidade
    const carregarDisponibilidade = () => {
        selectDisponivel.innerHTML = '<option value="" disabled selected>Selecione uma disponibilidade</option>';
        selectDisponivel.innerHTML += '<option value="1">Sim</option>';
        selectDisponivel.innerHTML += '<option value="0">Não</option>';
    };

    // Carregar produtos na tabela
    const carregarProdutos = async () => {
        tabelaProdutos.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Carregando produtos...</td></tr>';
        
        try {
            const response = await fetch('/app/controls/Produto/listar.php');
            const produtos = await response.json();
            if (!Array.isArray(produtos) || produtos.length === 0) {
                tabelaProdutos.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Nenhum produto cadastrado</td></tr>';
                return;
            }
            tabelaProdutos.innerHTML = produtos.map(p => {
                const preco = formatarPreco(p.preco);
                
                // Badge para disponibilidade
                const disponivelBadge = p.disponivel == 1 
                    ? '<span class="status-badge status-concluído">Sim</span>' 
                    : '<span class="status-badge status-cancelado">Não</span>';
                
                return '<tr class="border-bottom border-light"><td class="py-4 text-dark">' + p.idProduto + '</td><td class="py-4 text-dark">' + (p.nome || 'N/A') + '</td><td class="py-4 text-dark">' + preco + '</td><td class="py-4 text-dark">' + (p.marca || 'N/A') + '</td><td class="py-4 text-dark">' + (p.categoria || 'N/A') + '</td><td class="py-4 text-dark">' + (p.unidadeEstoque || 0) + '</td><td class="py-4">' + disponivelBadge + '</td><td class="py-4"><button class="btn btn-sm btn-success px-3 py-2 fw-medium rounded-4 btn-selecionar-produto" data-id="' + p.idProduto + '">Selecionar</button></td></tr>';
            }).join('');
            document.querySelectorAll('.btn-selecionar-produto').forEach(btn => {
                btn.addEventListener('click', function() {
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
            const response = await fetch(`/app/controls/Produto/buscarPorId.php?idProduto=${id}`);
            const produto = await response.json();
            inputId.value = produto.idProduto;
            inputNome.value = produto.nome;
            inputDescricao.value = produto.descricao || '';
            selectCategoria.value = produto.categoria || '';
            selectMarca.value = produto.marca || '';
            inputPreco.value = parseFloat(produto.preco).toFixed(2).replace('.', ',');
            selectPromocao.value = produto.idPromocao || '';
            inputEstoque.value = produto.unidadeEstoque || 0;
            selectDisponivel.value = produto.disponivel || 1;
            produtoSelecionado = produto;
            btnCadastrar.disabled = true;
            btnAtualizar.disabled = false;
            btnExcluir.disabled = false;
            formProduto.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (error) {
            console.error('Erro ao selecionar produto:', error);
            alert('Erro ao carregar dados do produto');
        }
    };

    // Cadastrar produto
    const cadastrarProduto = async (dados) => {
        try {
            const response = await fetch('/app/controls/Produto/salvar.php', {
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
            const response = await fetch('/app/controls/Produto/atualizar.php', {
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
            const response = await fetch('/app/controls/Produto/deletar.php', {
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
        btnCadastrar.disabled = false;
        btnAtualizar.disabled = true;
        btnExcluir.disabled = true;
    };

    // Event listener para o formulário
    formProduto.addEventListener('submit', function(e) {
        e.preventDefault();
        const dados = {
            nome: inputNome.value,
            descricao: inputDescricao.value,
            preco: precoParaNumero(inputPreco.value),
            marca: selectMarca.value,
            categoria: selectCategoria.value,
            idPromocao: selectPromocao.value || '',
            unidade_estoque: inputEstoque.value || 0,
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
    btnAtualizar.addEventListener('click', function() {
        if (!produtoSelecionado) {
            alert('Selecione um produto primeiro');
            return;
        }
        const dados = {
            idProduto: inputId.value,
            nome: inputNome.value,
            descricao: inputDescricao.value,
            preco: precoParaNumero(inputPreco.value),
            marca: selectMarca.value,
            categoria: selectCategoria.value,
            idPromocao: selectPromocao.value || '',
            unidade_estoque: inputEstoque.value || 0,
            disponivel: selectDisponivel.value
        };
        atualizarProduto(dados);
    });

    // Event listener do Botão Excluir
    btnExcluir.addEventListener('click', function() {
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
    btnCadastrar.disabled = false;
    btnAtualizar.disabled = true;
    btnExcluir.disabled = true;

    // Inicializar
    carregarMarcas();
    carregarCategorias();
    carregarPromocoes();
    carregarDisponibilidade();
    carregarProdutos();
});
