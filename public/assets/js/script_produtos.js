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
    const inputEstoque = document.getElementById('estoque');
    const selectDisponivel = document.getElementById('disponivel');
    const tabelaProdutos = document.querySelector('#produtos-section tbody');
    const btnCadastrarProduto = document.getElementById('btnCadastrarProduto');
    const btnAtualizarProduto = document.getElementById('btnAtualizarProduto');
    const btnExcluirProduto = document.getElementById('btnExcluirProduto');
    // Novos elementos para modais
    const modalNovaMarca = document.getElementById('modalNovaMarca');
    const inputNovaMarca = document.getElementById('inputNovaMarca');
    const btnSalvarNovaMarca = document.getElementById('btnSalvarNovaMarca');
    const modalNovaCategoria = document.getElementById('modalNovaCategoria');
    const inputNovaCategoria = document.getElementById('inputNovaCategoria');
    const btnSalvarNovaCategoria = document.getElementById('btnSalvarNovaCategoria');

    // Mapeamento de promoções por id
    let mapaPromocoes = {};

    // Carregar promoções no select e no mapa
    const carregarPromocoes = async () => {
        try {
            const response = await fetch('/promocoes');
            const promocoes = await response.json();
            selectPromocao.innerHTML = '<option value="" disabled selected>Selecione uma promoção</option>';
            mapaPromocoes = {};
            promocoes.forEach(promocao => {
                const option = document.createElement('option');
                option.value = promocao.idPromocao;
                option.textContent = `${promocao.nome} - ${promocao.valor}%`;
                selectPromocao.appendChild(option);
                mapaPromocoes[promocao.idPromocao] = promocao.nome;
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


    // Carregar marcas no select
    const carregarMarcas = async (selecionarId = null) => {
        try {
            const response = await fetch('/marcas');
            const marcas = await response.json();
            selectMarca.innerHTML = '<option value="" disabled selected>Selecione uma marca</option>';
            marcas.forEach(marca => {
                const option = document.createElement('option');
                option.value = marca.idMarca;
                option.textContent = marca.nome;
                selectMarca.appendChild(option);
            });
            // Adiciona opção para nova marca
            const optNova = document.createElement('option');
            optNova.value = '__nova__';
            optNova.textContent = 'Adicionar nova marca...';
            selectMarca.appendChild(optNova);
            if (selecionarId) selectMarca.value = selecionarId;
        } catch (error) {
            console.error('Erro ao carregar marcas:', error);
        }
    };


    // Carregar categorias no select
    const carregarCategorias = async (selecionarId = null) => {
        try {
            const response = await fetch('/categorias');
            const categorias = await response.json();
            selectCategoria.innerHTML = '<option value="" disabled selected>Selecione uma categoria</option>';
            categorias.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.idCategoria;
                option.textContent = categoria.nome;
                selectCategoria.appendChild(option);
            });
            // Adiciona opção para nova categoria
            const optNova = document.createElement('option');
            optNova.value = '__nova__';
            optNova.textContent = 'Adicionar nova categoria...';
            selectCategoria.appendChild(optNova);
            if (selecionarId) selectCategoria.value = selecionarId;
        } catch (error) {
            console.error('Erro ao carregar categorias:', error);
        }
    };
    // Handler para abrir modal de nova marca
    selectMarca.addEventListener('change', function() {
        if (this.value === '__nova__') {
            inputNovaMarca.value = '';
            const modal = new bootstrap.Modal(modalNovaMarca);
            modal.show();
        }
    });

    // Handler para abrir modal de nova categoria
    selectCategoria.addEventListener('change', function() {
        if (this.value === '__nova__') {
            inputNovaCategoria.value = '';
            const modal = new bootstrap.Modal(modalNovaCategoria);
            modal.show();
        }
    });

    // Salvar nova marca
    btnSalvarNovaMarca.addEventListener('click', async function() {
        const nome = inputNovaMarca.value.trim();
        if (!nome) {
            inputNovaMarca.focus();
            return;
        }
        btnSalvarNovaMarca.disabled = true;
        try {
            const response = await fetch('/marcas/salvar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ nome })
            });
            const result = await response.json();
            if (response.ok && result.idMarca) {
                bootstrap.Modal.getInstance(modalNovaMarca).hide();
                await carregarMarcas(result.idMarca);
            } else {
                alert(result.erro || 'Erro ao salvar marca');
            }
        } catch (e) {
            alert('Erro ao salvar marca');
        }
        btnSalvarNovaMarca.disabled = false;
    });

    // Salvar nova categoria
    btnSalvarNovaCategoria.addEventListener('click', async function() {
        const nome = inputNovaCategoria.value.trim();
        if (!nome) {
            inputNovaCategoria.focus();
            return;
        }
        btnSalvarNovaCategoria.disabled = true;
        try {
            const response = await fetch('/categorias/salvar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ nome })
            });
            const result = await response.json();
            if (response.ok && result.idCategoria) {
                bootstrap.Modal.getInstance(modalNovaCategoria).hide();
                await carregarCategorias(result.idCategoria);
            } else {
                alert(result.erro || 'Erro ao salvar categoria');
            }
        } catch (e) {
            alert('Erro ao salvar categoria');
        }
        btnSalvarNovaCategoria.disabled = false;
    });

    // (Removido: duplicidade, função já declarada acima com o mapa de promoções)

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
                const preco = formatarPreco(p.preco);
                // Badge para disponibilidade
                const disponivelBadge = p.disponivel == 1 
                    ? '<span class="status-badge status-concluído">Sim</span>' 
                    : '<span class="status-badge status-cancelado">Não</span>';
                // Promoção
                let promocao = '-';
                if (p.idPromocao && mapaPromocoes[p.idPromocao]) {
                    promocao = mapaPromocoes[p.idPromocao];
                }
                return '<tr class="border-bottom border-light">'
                    + '<td class="py-4 text-dark">' + p.idProduto + '</td>'
                    + '<td class="py-4 text-dark">' + (p.nome || 'N/A') + '</td>'
                    + '<td class="py-4 text-dark">' + preco + '</td>'
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
            selectCategoria.value = produto.categoria ?? produto.idCategoria ?? produto.id_categoria ?? '';
            selectMarca.value = produto.marca ?? produto.idMarca ?? produto.id_marca ?? '';
            inputPreco.value = produto.preco ? parseFloat(produto.preco).toFixed(2).replace('.', ',') : '';
            selectPromocao.value = produto.idPromocao ?? produto.id_promocao ?? '';
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
        const dados = {
            nome: inputNome.value,
            descricao: inputDescricao.value,
            preco: precoParaNumero(inputPreco.value),
            marca: selectMarca.value,
            categoria: selectCategoria.value,
            idPromocao: selectPromocao.value || '',
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
        const dados = {
            idProduto: inputId.value,
            nome: inputNome.value,
            descricao: inputDescricao.value,
            preco: precoParaNumero(inputPreco.value),
            marca: selectMarca.value,
            categoria: selectCategoria.value,
            idPromocao: selectPromocao.value || '',
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

    // Inicializar
    carregarMarcas();
    carregarCategorias();
    carregarPromocoes();
    carregarDisponibilidade();
    carregarProdutos();
});
