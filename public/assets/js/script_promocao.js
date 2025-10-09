// Em: public/assets/js/script_promocao.js

document.addEventListener('DOMContentLoaded', function() {
    // Referências aos elementos do formulário e da tabela
    const form = document.getElementById('formPromocao');
    const inputId = document.getElementById('promocaoId');
    const inputNome = document.getElementById('promocaoNome');
    const inputDataInicio = document.getElementById('promocaoDataInicio');
    const inputDataFim = document.getElementById('promocaoDataFim');
    const inputTipo = document.getElementById('promocaoTipo');
    const inputValor = document.getElementById('promocaoValor');

    const btnSalvar = document.getElementById('btnSalvar');
    const btnAtualizar = document.getElementById('btnAtualizar');
    const btnExcluir = document.getElementById('btnExcluir');
    const btnLimpar = document.getElementById('btnLimpar');
    const tabelaCorpo = document.querySelector('#tabelaPromocoes tbody');

    let idPromocaoSelecionada = null;

    // Reseta o formulário para o estado inicial
    const resetarFormulario = () => {
        form.reset();
        inputId.value = 'Auto';
        idPromocaoSelecionada = null;
        btnSalvar.classList.remove('d-none');
        btnAtualizar.classList.add('d-none');
        btnExcluir.classList.add('d-none');
        document.querySelectorAll('#tabelaPromocoes tbody tr').forEach(row => row.classList.remove('table-active'));
    };

    // Formata a data para exibição na tabela (dd/mm/aaaa)
    const formatarData = (dataISO) => {
        if (!dataISO) return '';
        const data = new Date(dataISO);
        // Adiciona 1 dia para corrigir problemas de fuso horário na exibição
        data.setDate(data.getDate() + 1);
        return data.toLocaleDateString('pt-BR');
    };
    
    // Formata o valor do desconto para exibição
    const formatarValor = (valor, tipo) => {
        if (tipo === 'porcentual') {
            return `${parseFloat(valor).toFixed(2)}%`;
        }
        return parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };

    // Carrega a lista de promoções da API e preenche a tabela
    const carregarPromocoes = async () => {
        tabelaCorpo.innerHTML = `<tr><td colspan="7" class="text-center">Carregando...</td></tr>`;
        try {
            const response = await fetch('promocoes'); // Endpoint do seu routes.json
            if (!response.ok) throw new Error('Falha ao carregar promoções.');
            
            const promocoes = await response.json();
            tabelaCorpo.innerHTML = ''; 

            if (promocoes.length === 0) {
                tabelaCorpo.innerHTML = `<tr><td colspan="7" class="text-center">Nenhuma promoção cadastrada.</td></tr>`;
                return;
            }

            promocoes.forEach(promocao => {
                const linha = `
                    <tr data-id="${promocao.idPromocao}">
                        <td>${promocao.idPromocao}</td>
                        <td>${promocao.nome}</td>
                        <td>${promocao.tipo}</td>
                        <td>${formatarValor(promocao.valor, promocao.tipo)}</td>
                        <td>${formatarData(promocao.dataInicio)}</td>
                        <td>${formatarData(promocao.dataFim)}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-secondary btn-selecionar">Selecionar</button>
                        </td>
                    </tr>
                `;
                tabelaCorpo.insertAdjacentHTML('beforeend', linha);
            });
        } catch (error) {
            console.error(error);
            tabelaCorpo.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Erro ao carregar lista.</td></tr>`;
        }
    };

    // Preenche o formulário com os dados de uma promoção selecionada
    const selecionarPromocao = async (id, linhaSelecionada) => {
        try {
            const response = await fetch(`promocoes/buscar?id=${id}`);
            if (!response.ok) throw new Error('Promoção não encontrada.');
            
            const promocao = await response.json();

            // Formata a data para o formato YYYY-MM-DD que o input[type=date] espera
            const dataInicioFormatada = promocao.dataInicio ? promocao.dataInicio.split('T')[0] : '';
            const dataFimFormatada = promocao.dataFim ? promocao.dataFim.split('T')[0] : '';

            inputId.value = promocao.idPromocao;
            inputNome.value = promocao.nome;
            inputDataInicio.value = dataInicioFormatada;
            inputDataFim.value = dataFimFormatada;
            inputTipo.value = promocao.tipo;
            inputValor.value = promocao.valor;
            idPromocaoSelecionada = promocao.idPromocao;

            btnSalvar.classList.add('d-none');
            btnAtualizar.classList.remove('d-none');
            btnExcluir.classList.remove('d-none');
            
            document.querySelectorAll('#tabelaPromocoes tbody tr').forEach(row => row.classList.remove('table-active'));
            linhaSelecionada.classList.add('table-active');

        } catch (error) {
            console.error(error);
            alert('Não foi possível carregar os dados da promoção.');
        }
    };
    
    // --- EVENT LISTENERS ---

    // Ouve cliques na tabela para o botão "Selecionar"
    tabelaCorpo.addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('btn-selecionar')) {
            const linha = e.target.closest('tr');
            selecionarPromocao(linha.dataset.id, linha);
        }
    });

    // Ações dos botões do formulário
    btnLimpar.addEventListener('click', resetarFormulario);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const dados = new FormData(form);
        const endpoint = idPromocaoSelecionada ? `promocoes/atualizar?idPromocao=${idPromocaoSelecionada}` : 'promocoes/salvar';

        
        try {
            const response = await fetch(endpoint, { method: 'POST', body: dados });
            const resultado = await response.json();
            if (!response.ok) throw new Error(resultado.erro || 'Erro ao salvar a promoção.');
            
            alert(`Promoção ${idPromocaoSelecionada ? 'atualizada' : 'salva'} com sucesso!`);
            resetarFormulario();
            carregarPromocoes();
        } catch (error) {
            alert(error.message);
        }

    });
    
    btnExcluir.addEventListener('click', async () => {
        if (confirm(`Tem certeza que deseja excluir a promoção ID ${idPromocaoSelecionada}?`)) {
            const dados = new FormData();
            dados.append('id', idPromocaoSelecionada);
            try {
                const response = await fetch('promocoes/deletar', { method: 'POST', body: dados });
                const resultado = await response.json();
                if (!response.ok) throw new Error(resultado.erro || 'Erro ao excluir a promoção.');
                
                alert('Promoção excluída com sucesso!');
                resetarFormulario();
                carregarPromocoes();
            } catch (error) {
                alert(error.message);
            }
        }
    });

    // --- INICIALIZAÇÃO ---
    carregarPromocoes();
});