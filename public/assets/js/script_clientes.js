// =============================================================================
// SCRIPT DE GERENCIAMENTO DE CLIENTES
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
    // Carregar nome do usuário
    carregarUsuarioLogado();
    
    // -------------------------------------------------------------------------
    // ELEMENTOS DO DOM
    // -------------------------------------------------------------------------
    const formCliente = document.getElementById('form-cliente');
    const inputId = document.getElementById('cliente_id');
    const inputStatus = document.getElementById('status_cliente');
    const inputNome = document.getElementById('nome_cliente');
    const inputEmail = document.getElementById('email_cliente');
    const inputSenha = document.getElementById('senha_cliente');
    const inputEndereco = document.getElementById('endereco_cliente');
    const inputTelefone = document.getElementById('numero_cliente');
    const inputCpf = document.getElementById('cpf_cliente');
    const inputNascimento = document.getElementById('data_nascimento');
    const selectNivel = document.getElementById('nivel_cliente');
    const btnCadastrarCliente = document.getElementById('btnCadastrarCliente');
    const btnAtualizarCliente = document.getElementById('btnAtualizarCliente');
    const btnExcluirCliente = document.getElementById('btnExcluirCliente');
    const tabelaCorpo = document.querySelector('#tabelaClientes tbody');

    // -------------------------------------------------------------------------
    // VARIÁVEIS DE ESTADO
    // -------------------------------------------------------------------------
    
    let clienteSelecionado = null;

    // Estado inicial dos botões
    btnCadastrarCliente.disabled = false;
    btnAtualizarCliente.disabled = true;
    btnExcluirCliente.disabled = true;

    // -------------------------------------------------------------------------
    // FUNÇÕES UTILITÁRIAS
    // -------------------------------------------------------------------------
    
    // Formatar data para padrão brasileiro
    function formatarDataBR(dataStr) {
        if (!dataStr) return '';
        
        if (/^\d{4}-\d{2}-\d{2}/.test(dataStr)) {
            const partes = dataStr.split('T')[0].split('-');
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

    // Gerar badge de nível de acesso
    function gerarBadgeNivel(idNivel) {
        if (idNivel == 1) {
            return '<span class="status-badge status-danger">• Administrador</span>';
        } else if (idNivel == 2) {
            return '<span class="status-badge status-sent">• Cliente</span>';
        }
        return '<span class="status-badge status-pending">Desconhecido</span>';
    }

    // -------------------------------------------------------------------------
    // FUNÇÕES DE INTERFACE
    // -------------------------------------------------------------------------
    
    // Limpar formulário
    const limparFormulario = () => {
        formCliente.reset();
        inputId.value = 'Auto';
        inputStatus.value = 'Pendente';
        clienteSelecionado = null;
        // Atualizar estado dos botões
        btnCadastrarCliente.disabled = false;
        btnAtualizarCliente.disabled = true;
        btnExcluirCliente.disabled = true;
        // Remover seleção da tabela
        tabelaCorpo && tabelaCorpo.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
        // Esconder mensagem
        const msgDiv = document.getElementById('clienteMsg');
        if (msgDiv) {
            msgDiv.style.display = 'none';
            msgDiv.textContent = '';
            msgDiv.className = 'text-center mt-3';
        }
    };

    // -------------------------------------------------------------------------
    // FUNÇÕES DE CARREGAMENTO DE DADOS
    // -------------------------------------------------------------------------
    
    // Carregar lista de clientes
    const carregarClientes = async () => {
        tabelaCorpo.innerHTML = `<tr><td colspan="9" class="text-center">Carregando clientes...</td></tr>`;
        
        try {
            const response = await fetch('/usuario');
            const clientes = await response.json();
            if (!Array.isArray(clientes) || clientes.length === 0) {
                tabelaCorpo.innerHTML = `<tr><td colspan="9" class="text-center">Nenhum cliente cadastrado.</td></tr>`;
                return;
            }
            tabelaCorpo.innerHTML = clientes.map(cliente => {
                const dataFormatada = formatarDataBR(cliente.dataNascimento);
                const badge = gerarBadgeNivel(cliente.idNivel);
                return `
                    <tr class="border-bottom border-light" data-id="${cliente.idUsuario}">
                        <td class="py-4 text-dark">${cliente.idUsuario ?? ''}</td>
                        <td class="py-4 text-dark">${cliente.nome ?? ''}</td>
                        <td class="py-4 text-dark">${cliente.email ?? ''}</td>
                        <td class="py-4 text-dark">${cliente.endereco ?? ''}</td>
                        <td class="py-4 text-dark">${cliente.telefone ?? ''}</td>
                        <td class="py-4 text-dark">${dataFormatada}</td>
                        <td class="py-4">${badge}</td>
                        <td class="py-4">
                            <button class="btn btn-sm btn-success px-3 py-2 fw-medium rounded-4 btn-selecionar-cliente" data-id="${cliente.idUsuario}">Selecionar</button>
                        </td>
                    </tr>
                `;
            }).join('');
            // Adicionar eventos aos botões de seleção
            document.querySelectorAll('.btn-selecionar-cliente').forEach(btn => {
                btn.addEventListener('click', function() {
                    const linha = this.closest('tr');
                    selecionarCliente(this.dataset.id, linha);
                });
            });
        } catch (error) {
            const msgDiv = document.getElementById('clienteMsg');
            if (msgDiv) {
                msgDiv.textContent = 'Erro ao carregar lista de clientes.';
                msgDiv.className = 'text-danger text-center mt-3';
                msgDiv.style.display = 'block';
            }
            tabelaCorpo.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Erro ao carregar lista.</td></tr>`;
        }
    };

    // Selecionar cliente para edição/exclusão
    const selecionarCliente = async (id, linhaSelecionada) => {
        try {
            const response = await fetch(`/usuario/buscar?idUsuario=${id}`);
            const cliente = await response.json();
            // Preencher formulário
            inputId.value = cliente.idUsuario;
            inputStatus.value = cliente.status ? cliente.status.charAt(0).toUpperCase() + cliente.status.slice(1) : 'Pendente';
            inputNome.value = cliente.nome;
            inputEmail.value = cliente.email;
            inputSenha.value = cliente.senha || '';
            inputEndereco.value = cliente.endereco || '';
            inputTelefone.value = cliente.telefone || '';
            inputCpf.value = cliente.cpf || '';
            inputNascimento.value = cliente.dataNascimento || '';
            selectNivel.value = String(cliente.idNivel || '');
            // Atualizar estado
            clienteSelecionado = cliente.idUsuario;
            btnCadastrarCliente.disabled = true;
            btnAtualizarCliente.disabled = false;
            btnExcluirCliente.disabled = false;
            // Destacar linha selecionada
            tabelaCorpo && tabelaCorpo.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
            linhaSelecionada.classList.add('table-active');
            // Scroll para o formulário
            formCliente.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (error) {
            const msgDiv = document.getElementById('clienteMsg');
            if (msgDiv) {
                msgDiv.textContent = 'Não foi possível carregar os dados do cliente.';
                msgDiv.className = 'text-danger text-center mt-3';
                msgDiv.style.display = 'block';
            }
        }
    };

    // -------------------------------------------------------------------------
    // FUNÇÕES DE CRUD
    // -------------------------------------------------------------------------
    
    // Salvar novo cliente
    const salvarCliente = async () => {
    // 1. Criar um objeto JS simples
    const dadosCliente = {
        nome: inputNome.value,
        email: inputEmail.value,
        senha: inputSenha.value,
        endereco: inputEndereco.value,
        telefone: inputTelefone.value,
        cpf: inputCpf.value,
        data_nascimento: inputNascimento.value,
        id_nivel: selectNivel.value
    };

    try {
        const response = await fetch('/usuario/salvarAdmin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dadosCliente)
        });
        const resultado = await response.json();
        const msgDiv = document.getElementById('clienteMsg');
        if (!response.ok) {
            if (msgDiv) {
                msgDiv.textContent = resultado.erro || 'Erro ao salvar.';
                msgDiv.className = 'text-danger text-center mt-3';
                msgDiv.style.display = 'block';
            }
            return;
        }
        if (msgDiv) {
            msgDiv.textContent = 'Cliente salvo com sucesso!';
            msgDiv.className = 'text-success text-center mt-3';
            msgDiv.style.display = 'block';
        }
        setTimeout(() => {
            formCliente.reset();
            inputId.value = 'Auto';
            inputStatus.value = 'Pendente';
            clienteSelecionado = null;
            btnCadastrarCliente.disabled = false;
            btnAtualizarCliente.disabled = true;
            btnExcluirCliente.disabled = true;
            tabelaCorpo && tabelaCorpo.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
            if (msgDiv) {
                msgDiv.style.display = 'none';
                msgDiv.textContent = '';
                msgDiv.className = 'text-center mt-3';
            }
        }, 1500);
        carregarClientes();
        window.dispatchEvent(new Event('clientesAtualizados'));
    } catch (error) {
        const msgDiv = document.getElementById('clienteMsg');
        if (msgDiv) {
            msgDiv.textContent = error.message || 'Erro ao salvar cliente.';
            msgDiv.className = 'text-danger text-center mt-3';
            msgDiv.style.display = 'block';
        }
    }
};

// Atualizar cliente existente
const atualizarCliente = async () => {
    // 1. Criar um objeto JS simples
    const dadosCliente = {
        idUsuario: clienteSelecionado, // Incluir o ID
        nome: inputNome.value,
        email: inputEmail.value,
        senha: inputSenha.value, // (O back-end deve tratar se a senha vier vazia)
        endereco: inputEndereco.value,
        telefone: inputTelefone.value,
        cpf: inputCpf.value,
        data_nascimento: inputNascimento.value,
        id_nivel: selectNivel.value
    };

    try {
        const response = await fetch('/usuario/atualizar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dadosCliente)
        });
        const resultado = await response.json();
        const msgDiv = document.getElementById('clienteMsg');
        if (!response.ok) {
            if (msgDiv) {
                msgDiv.textContent = resultado.erro || 'Erro ao atualizar.';
                msgDiv.className = 'text-danger text-center mt-3';
                msgDiv.style.display = 'block';
            }
            return;
        }
        if (msgDiv) {
            msgDiv.textContent = 'Cliente atualizado com sucesso!';
            msgDiv.className = 'text-success text-center mt-3';
            msgDiv.style.display = 'block';
        }
        setTimeout(() => {
            limparFormulario();
            if (msgDiv) {
                msgDiv.style.display = 'none';
                msgDiv.textContent = '';
                msgDiv.className = 'text-center mt-3';
            }
        }, 1500);
        carregarClientes();
        window.dispatchEvent(new Event('clientesAtualizados'));
    } catch (error) {
        const msgDiv = document.getElementById('clienteMsg');
        if (msgDiv) {
            msgDiv.textContent = error.message || 'Erro ao atualizar cliente.';
            msgDiv.className = 'text-danger text-center mt-3';
            msgDiv.style.display = 'block';
        }
    }
};

// Excluir cliente
const excluirCliente = async () => {
    // 1. Criar um objeto JS simples
    const dadosCliente = {
        idUsuario: clienteSelecionado
    };

    try {
        const response = await fetch('/usuario/deletar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dadosCliente)
        });
        const resultado = await response.json();
        const msgDiv = document.getElementById('clienteMsg');
        if (!response.ok) {
            if (msgDiv) {
                msgDiv.textContent = resultado.erro || 'Erro ao excluir.';
                msgDiv.className = 'text-danger text-center mt-3';
                msgDiv.style.display = 'block';
            }
            return;
        }
        if (msgDiv) {
            msgDiv.textContent = 'Cliente excluído com sucesso!';
            msgDiv.className = 'text-success text-center mt-3';
            msgDiv.style.display = 'block';
        }
        setTimeout(() => {
            limparFormulario();
            if (msgDiv) {
                msgDiv.style.display = 'none';
                msgDiv.textContent = '';
                msgDiv.className = 'text-center mt-3';
            }
        }, 1500);
        carregarClientes();
        window.dispatchEvent(new Event('clientesAtualizados'));
    } catch (error) {
        const msgDiv = document.getElementById('clienteMsg');
        if (msgDiv) {
            msgDiv.textContent = error.message || 'Erro ao excluir cliente.';
            msgDiv.className = 'text-danger text-center mt-3';
            msgDiv.style.display = 'block';
        }
    }
};

    // -------------------------------------------------------------------------
    // EVENTOS
    // -------------------------------------------------------------------------
    
    // Evento de reset do formulário
    formCliente.addEventListener('reset', function() {
        limparFormulario();
    });
    
    // Evento de cadastrar cliente
    btnCadastrarCliente.addEventListener('click', function(e) {
        e.preventDefault();
        salvarCliente();
    });
    
    // Evento de atualizar cliente
    btnAtualizarCliente.addEventListener('click', function(e) {
        e.preventDefault();
        atualizarCliente();
    });
    
    // Evento de excluir cliente
    btnExcluirCliente.addEventListener('click', function(e) {
        e.preventDefault();
        if (clienteSelecionado && confirm(`Tem certeza que deseja excluir o cliente ID ${clienteSelecionado}?`)) {
            excluirCliente();
        }
    });

    // -------------------------------------------------------------------------
    // CARREGAMENTO INICIAL
    // -------------------------------------------------------------------------
    
    carregarClientes();
});