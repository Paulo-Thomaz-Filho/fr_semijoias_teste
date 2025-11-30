// =============================================================================
// SCRIPT DE GERENCIAMENTO DE PROMOÇÕES
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
        function exibirMensagemPromocao(msg, tipo = 'success') {
            const msgDiv = document.getElementById('promocaoMsg');
            if (msgDiv) {
                msgDiv.textContent = msg;
                msgDiv.className = `text-center mt-3 text-${tipo === 'success' ? 'success' : 'danger'}`;
                msgDiv.style.display = 'block';
            }
        }
        function limparMensagemPromocao() {
            const msgDiv = document.getElementById('promocaoMsg');
            if (msgDiv) {
                msgDiv.textContent = '';
                msgDiv.style.display = 'none';
            }
        }
    carregarUsuarioLogado();
    
    // -------------------------------------------------------------------------
    // ELEMENTOS DO DOM
    // -------------------------------------------------------------------------
    const formPromocao = document.getElementById('form-promocoes');
    const inputId = document.getElementById('promocao_id');
    const inputNome = document.getElementById('nome_promocao');
    const inputDataInicio = document.getElementById('data_inicio');
    const inputDataFim = document.getElementById('data_fim');
    const inputDesconto = document.getElementById('desconto_promocao');
    const selectStatus = document.getElementById('status_promocao');
    const inputDescricao = document.getElementById('descricao_promocao');
    const selectTipoDesconto = document.getElementById('tipo_desconto_promocao');
    const btnCadastrar = document.getElementById('btnCadastrarPromocao');
    const btnAtualizar = document.getElementById('btnAtualizarPromocao');
    const btnExcluir = document.getElementById('btnExcluirPromocao');
    const tabelaCorpo = document.querySelector('#tabelaPromocoes tbody');
    
    // -------------------------------------------------------------------------
    // VARIÁVEIS DE ESTADO
    // -------------------------------------------------------------------------
    
    let promocaoSelecionada = null;
    
    // -------------------------------------------------------------------------
    // FUNÇÕES UTILITÁRIAS
    // -------------------------------------------------------------------------
    
    // Preencher select de tipo de desconto
    function carregarTipoDesconto() {
        const tipos = [
            { value: 'percentual', label: '%' },
            { value: 'valor', label: 'R$' }
        ];
        selectTipoDesconto.innerHTML = '<option value="" selected disabled>Selecione um tipo de desconto</option>';
        tipos.forEach(tipo => {
            const option = document.createElement('option');
            option.value = tipo.value;
            option.textContent = tipo.label;
            selectTipoDesconto.appendChild(option);
        });
    }
    
    // Carregar status possíveis
    function carregarStatus() {
        selectStatus.innerHTML = '<option value="" disabled selected>Selecione um status</option>';
        selectStatus.innerHTML += '<option value="1">Ativo</option>';
        selectStatus.innerHTML += '<option value="0">Inativo</option>';
    }
    
    // -------------------------------------------------------------------------
    // FUNÇÕES DE INTERFACE
    // -------------------------------------------------------------------------
    
    // Limpar e resetar o formulário
    function limparFormulario() {
        formPromocao.reset();
        inputId.value = 'Auto';
        promocaoSelecionada = null;
        btnCadastrar.disabled = false;
        btnAtualizar.disabled = true;
        btnExcluir.disabled = true;
        tabelaCorpo && tabelaCorpo.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
        carregarTipoDesconto();
    }
    
    // -------------------------------------------------------------------------
    // FUNÇÕES DE CARREGAMENTO DE DADOS
    // -------------------------------------------------------------------------
    
    // Carregar promoções na tabela
    async function carregarPromocoes() {
        tabelaCorpo.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Carregando promoções...</td></tr>';
        try {
            const response = await fetch('/promocoes');
            const promocoes = await response.json();
            if (!Array.isArray(promocoes) || promocoes.length === 0) {
                tabelaCorpo.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Nenhuma promoção cadastrada.</td></tr>';
                return;
            }
            tabelaCorpo.innerHTML = promocoes.map(p => {
                let descontoFormatado = '';
                if (p.desconto !== undefined && p.desconto !== null && p.desconto !== '') {
                    if (p.tipo_desconto === 'valor') {
                        descontoFormatado = 'R$ ' + parseInt(p.desconto);
                    } else {
                        descontoFormatado = parseInt(p.desconto) + '%';
                    }
                }
                let statusBadge = p.status == 1
                    ? '<span class="status-badge status-green">• Ativo</span>'
                    : '<span class="status-badge status-danger">• Inativo</span>';
                return `<tr class="border-bottom border-light">
                    <td class="py-4 text-dark">${p.idPromocao}</td>
                    <td class="py-4 text-dark">${p.nome || ''}</td>
                    <td class="py-4 text-dark">${descontoFormatado}</td>
                    <td class="py-4 text-dark">${formatarDataBR(p.dataInicio)}</td>
                    <td class="py-4 text-dark">${formatarDataBR(p.dataFim)}</td>
                    <td class="py-4">${statusBadge}</td>
                    <td class="py-4 text-dark">${p.descricao || ''}</td>
                    <td class="py-4"><button class="btn btn-sm btn-success px-3 py-2 fw-medium rounded-4 btn-selecionar-promocao" data-id="${p.idPromocao}">Selecionar</button></td>
                </tr>`;
            }).join('');
            document.querySelectorAll('.btn-selecionar-promocao').forEach(btn => {
                btn.addEventListener('click', function() {
                    selecionarPromocao(this.dataset.id, this.closest('tr'));
                });
            });
        } catch (error) {
            tabelaCorpo.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Erro ao carregar promoções.</td></tr>';
        }
    }

    // Formata data para dd/MM/yyyy
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

    // Seleciona promoção para edição
    async function selecionarPromocao(id, linha) {
        try {
            const response = await fetch(`/promocoes/buscar?idPromocao=${id}`);
            if (!response.ok) throw new Error('Promoção não encontrada.');
            const p = await response.json();
            inputId.value = p.idPromocao;
            inputNome.value = p.nome;
            inputDataInicio.value = p.dataInicio ? p.dataInicio.split('T')[0] : '';
            inputDataFim.value = p.dataFim ? p.dataFim.split('T')[0] : '';
            inputDesconto.value = p.desconto !== undefined && p.desconto !== null ? parseInt(p.desconto) : '';
            if (selectTipoDesconto && p.tipo_desconto) selectTipoDesconto.value = p.tipo_desconto;
            selectStatus.value = p.status;
            inputDescricao.value = p.descricao;
            promocaoSelecionada = p.idPromocao;
            btnCadastrar.disabled = true;
            btnAtualizar.disabled = false;
            btnExcluir.disabled = false;
            tabelaCorpo && tabelaCorpo.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
            linha && linha.classList.add('table-active');
        } catch (error) {
            alert('Não foi possível carregar os dados da promoção.');
        }
    }

    // SUBMIT cadastrar
    formPromocao.addEventListener('submit', async function(e) {
        e.preventDefault();
        limparMensagemPromocao();
        // Garante que todos os campos obrigatórios sejam enviados
        const dados = new FormData();
        dados.append('nome', inputNome.value.trim());
        dados.append('data_inicio', inputDataInicio.value);
        dados.append('data_fim', inputDataFim.value);
        dados.append('desconto', inputDesconto.value);
        dados.append('tipo_desconto', selectTipoDesconto.value);
        dados.append('status', selectStatus.value);
        dados.append('descricao', inputDescricao.value);
        if (promocaoSelecionada) {
            dados.append('idPromocao', promocaoSelecionada);
        }
        let endpoint = '/promocoes/salvar';
        if (promocaoSelecionada) {
            endpoint = '/promocoes/atualizar';
        }
        try {
            const response = await fetch(endpoint, { method: 'POST', body: dados });
            const resultado = await response.json();
            if (!response.ok) throw new Error(resultado.erro || 'Erro ao salvar promoção.');
            exibirMensagemPromocao(`Promoção ${promocaoSelecionada ? 'atualizada' : 'cadastrada'} com sucesso!`, 'success');
            setTimeout(() => {
                limparFormulario();
                limparMensagemPromocao();
            }, 1500);
            carregarPromocoes();
            window.dispatchEvent(new Event('promocaoAtualizada'));
        } catch (error) {
            exibirMensagemPromocao(error.message, 'danger');
        }
    });

    // Atualizar
    btnAtualizar.addEventListener('click', function() {
        if (promocaoSelecionada) {
            formPromocao.dispatchEvent(new Event('submit'));
        }
    });

    // Excluir
    btnExcluir.addEventListener('click', async function() {
        if (!promocaoSelecionada) return;
        if (!confirm(`Tem certeza que deseja excluir a promoção ID ${promocaoSelecionada}?`)) return;
        const dados = new FormData();
        dados.append('id', promocaoSelecionada);
        try {
            const response = await fetch('/promocoes/deletar', { method: 'POST', body: dados });
            const resultado = await response.json();
            if (!response.ok) throw new Error(resultado.erro || 'Erro ao excluir promoção.');
            exibirMensagemPromocao('Promoção excluída com sucesso!', 'success');
            setTimeout(() => {
                limparFormulario();
                limparMensagemPromocao();
            }, 1500);
            carregarPromocoes();
            window.dispatchEvent(new Event('promocaoAtualizada'));
        } catch (error) {
            exibirMensagemPromocao(error.message, 'danger');
        }
    });

    // Limpar
    formPromocao.addEventListener('reset', limparFormulario);

    // Inicialização
    carregarStatus();
    limparFormulario();
    carregarPromocoes();
});