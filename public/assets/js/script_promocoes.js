// Logout global para promoções
document.addEventListener('DOMContentLoaded', function() {
    var btnsLogout = document.querySelectorAll('.btn-logout-dashboard');
    btnsLogout.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            fetch('/api/usuario/logout')
                .then(function() {
                    localStorage.clear();
                    sessionStorage.clear();
                    window.location.href = '/login';
                });
        });
    });
});
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
        const cardsContainer = document.getElementById('cardsPromocoes');
        
        tabelaCorpo.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Carregando promoções...</td></tr>';
        if (cardsContainer) {
            cardsContainer.innerHTML = '<div class="text-center py-4 text-muted">Carregando promoções...</div>';
        }
        
        try {
            const response = await fetch('/promocoes');
            const promocoes = await response.json();
            
            if (!Array.isArray(promocoes) || promocoes.length === 0) {
                tabelaCorpo.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Nenhuma promoção cadastrada.</td></tr>';
                if (cardsContainer) {
                    cardsContainer.innerHTML = '<div class="text-center py-4 text-muted">Nenhuma promoção cadastrada.</div>';
                }
                return;
            }
            
            // Renderizar tabela
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
                    ? '<span class="status-badge status-green">Ativo</span>'
                    : '<span class="status-badge status-danger">Inativo</span>';
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
            
            // Renderizar cards para mobile
            if (cardsContainer) {
                cardsContainer.innerHTML = promocoes.map(p => {
                    let descontoFormatado = '';
                    if (p.desconto !== undefined && p.desconto !== null && p.desconto !== '') {
                        if (p.tipo_desconto === 'valor') {
                            descontoFormatado = 'R$ ' + parseInt(p.desconto);
                        } else {
                            descontoFormatado = parseInt(p.desconto) + '%';
                        }
                    }
                    
                    let statusBadge = p.status == 1
                        ? '<span class="status-badge status-green">Ativo</span>'
                        : '<span class="status-badge status-danger">Inativo</span>';
                    
                    return `
                        <div class="card border-0 bg-white mb-3 shadow-sm rounded-4">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-medium mb-0 text-dark">${p.nome || ''}</h6>
                                    ${statusBadge}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>ID:</strong> ${p.idPromocao}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Desconto:</strong> ${descontoFormatado}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Data de Início:</strong> ${formatarDataBR(p.dataInicio)}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Data de Fim:</strong> ${formatarDataBR(p.dataFim)}
                                </div>
                                <div class="small text-muted mb-2">
                                    <strong>Descrição:</strong> ${p.descricao || ''}
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    <button class="btn btn-sm btn-success px-3 py-2 fw-medium rounded-4 w-100 btn-selecionar-promocao-mobile" data-id="${p.idPromocao}">
                                        Selecionar Promoção
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            // Adicionar eventos aos botões de seleção (tabela)
            document.querySelectorAll('.btn-selecionar-promocao').forEach(btn => {
                btn.addEventListener('click', function() {
                    const linha = this.closest('tr');
                    tabelaCorpo && tabelaCorpo.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
                    linha.classList.add('table-active');
                    selecionarPromocao(this.dataset.id);
                });
            });
            
            // Adicionar eventos aos botões de seleção (cards mobile)
            document.querySelectorAll('.btn-selecionar-promocao-mobile').forEach(btn => {
                btn.addEventListener('click', function() {
                    selecionarPromocao(this.dataset.id);
                });
            });
            
        } catch (error) {
            tabelaCorpo.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Erro ao carregar as promoções</td></tr>';
            if (cardsContainer) {
                cardsContainer.innerHTML = '<div class="text-center py-4 text-danger">Erro ao carregar as promoções</div>';
            }
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
    async function selecionarPromocao(id) {
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
            // Scroll para o formulário
            formPromocao.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (error) {
            exibirMensagemPromocao('Não foi possível carregar os dados da promoção.');
        }
    }

    // SUBMIT cadastrar
    formPromocao.addEventListener('submit', async function(e) {
        e.preventDefault();
        limparMensagemPromocao();
        
        // Validações de campos obrigatórios
        if (!inputNome.value.trim()) {
            exibirMensagemPromocao('Por favor, preencha o nome da promoção.', 'danger');
            inputNome.focus();
            return;
        }

        if (!inputDataInicio.value) {
            exibirMensagemPromocao('Por favor, preencha a data de início.', 'danger');
            inputDataInicio.focus();
            return;
        }

        if (!inputDataFim.value) {
            exibirMensagemPromocao('Por favor, preencha a data de término.', 'danger');
            inputDataFim.focus();
            return;
        }

        // Validação extra: só aceita número
        if (!inputDesconto.value || isNaN(inputDesconto.value) || parseFloat(inputDesconto.value) <= 0) {
            exibirMensagemPromocao('Por favor, preencha um valor de desconto válido (apenas números).', 'danger');
            inputDesconto.focus();
            return;
        }

        if (!selectTipoDesconto.value) {
            exibirMensagemPromocao('Por favor, selecione o tipo de desconto.', 'danger');
            selectTipoDesconto.focus();
            return;
        }
        
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
            limparFormulario();
            exibirMensagemPromocao(`Promoção ${promocaoSelecionada ? 'atualizada' : 'cadastrada'} com sucesso!`, 'success');
            setTimeout(() => {
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
        try {
            const response = await fetch(`/promocoes/deletar?id=${promocaoSelecionada}`, { method: 'DELETE' });
            const resultado = await response.json();
            if (!response.ok) throw new Error(resultado.erro || 'Erro ao excluir promoção.');
            // Limpa apenas os campos do formulário, mas mantém a mensagem visível
            limparFormulario();
            exibirMensagemPromocao('Promoção excluída com sucesso!', 'success');
            setTimeout(() => {
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