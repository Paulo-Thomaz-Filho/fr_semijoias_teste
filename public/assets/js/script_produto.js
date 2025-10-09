// Em: public/assets/js/script_produtos.js

document.addEventListener('DOMContentLoaded', function() {
    // Referências aos elementos do DOM
    const form = document.getElementById('formProduto');
    const inputId = document.getElementById('produtoId');
    const inputNome = document.getElementById('produtoNome');
    const inputDescricao = document.getElementById('produtoDescricao');
    const inputCategoria = document.getElementById('produtoCategoria');
    const inputMarca = document.getElementById('produtoMarca');
    const inputValor = document.getElementById('produtoValor');
    const selectPromocao = document.getElementById('produtoPromocao'); // Select de promoções

    const btnSalvar = document.getElementById('btnSalvar');
    const btnAtualizar = document.getElementById('btnAtualizar');
    const btnExcluir = document.getElementById('btnExcluir');
    const btnLimpar = document.getElementById('btnLimpar');
    const tabelaCorpo = document.querySelector('#tabelaProdutos tbody');

    let idProdutoSelecionado = null;

    // Reseta o formulário para o estado inicial de cadastro
    const resetarFormulario = () => {
        form.reset();
        inputId.value = 'Auto';
        idProdutoSelecionado = null;
        btnSalvar.classList.remove('d-none');
        btnAtualizar.classList.add('d-none');
        btnExcluir.classList.add('d-none');
        document.querySelectorAll('#tabelaProdutos tbody tr').forEach(row => row.classList.remove('table-active'));
    };

    // Formata um número para o padrão de moeda BRL (R$)
    const formatarValorBRL = (valor) => {
        return parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };

    // Carrega a lista de produtos da API e preenche a tabela
    const carregarProdutos = async () => {
        tabelaCorpo.innerHTML = `<tr><td colspan="6" class="text-center">Carregando...</td></tr>`;
        try {
            const response = await fetch('produtos'); // Endpoint para listar produtos
            if (!response.ok) throw new Error('Falha ao carregar produtos.');
            
            const produtos = await response.json();
            tabelaCorpo.innerHTML = ''; 

            if (produtos.length === 0) {
                tabelaCorpo.innerHTML = `<tr><td colspan="6" class="text-center">Nenhum produto cadastrado.</td></tr>`;
                return;
            }

            produtos.forEach(produto => {
                const linha = `
                    <tr data-id="${produto.IdProduto}">
                        <td>${produto.IdProduto}</td>
                        <td>${produto.nome}</td>
                        <td>${produto.categoria}</td>
                        <td>${produto.marca}</td>
                        <td>${formatarValorBRL(produto.valor)}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-secondary btn-selecionar">Selecionar</button>
                        </td>
                    </tr>
                `;
                tabelaCorpo.insertAdjacentHTML('beforeend', linha);
            });
        } catch (error) {
            console.error(error);
            tabelaCorpo.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Erro ao carregar lista de produtos.</td></tr>`;
        }
    };

    // Carrega as promoções para preencher o <select>
    const carregarPromocoesSelect = async () => {
        try {
            const response = await fetch('promocoes');
            if (!response.ok) throw new Error('Falha ao carregar promoções.');
            const promocoes = await response.json();

            // Limpa opções antigas, mantendo a primeira
            selectPromocao.innerHTML = '<option selected value="">Selecione uma promoção...</option>';

            promocoes.forEach(promocao => {
                const option = `<option value="${promocao.idPromocao}">${promocao.nome}</option>`;
                selectPromocao.insertAdjacentHTML('beforeend', option);
            });
        } catch (error) {
            console.error('Erro ao carregar promoções no select:', error);
        }
    };

    // Preenche o formulário com dados de um produto selecionado
    const selecionarProduto = async (id, linhaSelecionada) => {
        try {
            const response = await fetch(`produtos/buscar?id=${id}`); // Endpoint para buscar um produto
            if (!response.ok) throw new Error('Produto não encontrado.');
            
            const produto = await response.json();

            inputId.value = produto.IdProduto;
            inputNome.value = produto.nome;
            inputDescricao.value = produto.descricao;
            inputCategoria.value = produto.categoria;
            inputMarca.value = produto.marca;
            inputValor.value = produto.valor;
            selectPromocao.value = produto.idPromocao || ""; // Define a promoção ou deixa em branco se for nula
            idProdutoSelecionado = produto.IdProduto;

            btnSalvar.classList.add('d-none');
            btnAtualizar.classList.remove('d-none');
            btnExcluir.classList.remove('d-none');
            
            document.querySelectorAll('#tabelaProdutos tbody tr').forEach(row => row.classList.remove('table-active'));
            linhaSelecionada.classList.add('table-active');
        } catch (error) {
            console.error(error);
            alert('Não foi possível carregar os dados do produto.');
        }
    };
    
    // --- EVENT LISTENERS ---

    // Ouve cliques na tabela para o botão "Selecionar"
    tabelaCorpo.addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('btn-selecionar')) {
            const linha = e.target.closest('tr');
            selecionarProduto(linha.dataset.id, linha);
        }
    });

    // Ação do botão Limpar
    btnLimpar.addEventListener('click', resetarFormulario);

    // Ações de Salvar (novo) e Atualizar (existente)
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const dados = new FormData(form);
        const endpoint = idProdutoSelecionado ? `produtos/atualizar` : 'produtos/salvar';

        if (idProdutoSelecionado) {
            dados.append('IdProduto', idProdutoSelecionado);
        }

        try {
            const response = await fetch(endpoint, { method: 'POST', body: dados });
            const resultado = await response.json();
            if (!response.ok) throw new Error(resultado.erro || 'Erro ao salvar o produto.');
            
            alert(`Produto ${idProdutoSelecionado ? 'atualizado' : 'salvo'} com sucesso!`);
            resetarFormulario();
            carregarProdutos();
        } catch (error) {
            alert(error.message);
        }
    });
    
    // Ação de Excluir
    btnExcluir.addEventListener('click', async () => {
        if (confirm(`Tem certeza que deseja excluir o produto ID ${idProdutoSelecionado}?`)) {
            const dados = new FormData();
            dados.append('id', idProdutoSelecionado);
            try {
                const response = await fetch('produtos/deletar', { method: 'POST', body: dados });
                const resultado = await response.json();
                if (!response.ok) throw new Error(resultado.erro || 'Erro ao excluir o produto.');
                
                alert('Produto excluído com sucesso!');
                resetarFormulario();
                carregarProdutos();
            } catch (error) {
                alert(error.message);
            }
        }
    });

    // --- INICIALIZAÇÃO ---
    carregarProdutos();
    carregarPromocoesSelect(); // Carrega as promoções no dropdown ao iniciar a página
});