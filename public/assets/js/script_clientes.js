document.addEventListener('DOMContentLoaded', function() {
    // --- ELEMENTOS DO DOM ---
    const formCliente = document.getElementById('form-cliente');
    const inputId = document.getElementById('cliente_id');
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

    let clienteSelecionado = null;

    btnCadastrarCliente.disabled = false;
    btnAtualizarCliente.disabled = true;
    btnExcluirCliente.disabled = true;

    // --- FUNÇÃO DE LIMPAR FORMULÁRIO ---
    const limparFormulario = () => {
        formCliente.reset();
        inputId.value = 'Auto';
        clienteSelecionado = null;
        btnCadastrarCliente.disabled = false;
        btnAtualizarCliente.disabled = true;
        btnExcluirCliente.disabled = true;
        tabelaCorpo && tabelaCorpo.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
    };

    // --- FUNÇÃO DE CARREGAR CLIENTES ---
    const carregarClientes = async () => {
        tabelaCorpo.innerHTML = `<tr><td colspan="9" class="text-center">Carregando clientes...</td></tr>`;
        try {
            const response = await fetch('/usuario');
            const clientes = await response.json();
            if (!Array.isArray(clientes) || clientes.length === 0) {
                tabelaCorpo.innerHTML = `<tr><td colspan="9" class="text-center">Nenhum cliente cadastrado.</td></tr>`;
                return;
            }
            // Função utilitária para formatar datas yyyy-MM-dd ou outros formatos para dd/MM/yyyy
            function formatarDataBR(dataStr) {
                if (!dataStr) return '';
                // Aceita yyyy-MM-dd ou yyyy-MM-ddTHH:mm:ss ou Date
                let partes = null;
                if (/^\d{4}-\d{2}-\d{2}/.test(dataStr)) {
                    partes = dataStr.split('T')[0].split('-');
                    if (partes.length === 3) {
                        return `${partes[2]}/${partes[1]}/${partes[0]}`;
                    }
                }
                // Tenta criar Date
                const d = new Date(dataStr);
                if (!isNaN(d.getTime())) {
                    return d.toLocaleDateString('pt-BR');
                }
                return dataStr;
            }

            tabelaCorpo.innerHTML = clientes.map(cliente => {
                let dataFormatada = formatarDataBR(cliente.dataNascimento);
                // Função para mostrar tipo de acesso
                function getTipoAcesso(idNivel) {
                    if (idNivel == 1) return "Administrador";
                    if (idNivel == 2) return "Cliente";
                    return "Desconhecido";
                }
                // Badge de acesso usando classes já existentes
                let badge = '';
                if (cliente.idNivel == 1) {
                    badge = '<span class="status-badge status-danger">• Administrador</span>';
                } else if (cliente.idNivel == 2) {
                    badge = '<span class="status-badge status-sent">• Cliente</span>';
                } else {
                    badge = '<span class="status-badge status-pending">Desconhecido</span>';
                }
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
            document.querySelectorAll('.btn-selecionar-cliente').forEach(btn => {
                btn.addEventListener('click', function() {
                    const linha = this.closest('tr');
                    selecionarCliente(this.dataset.id, linha);
                });
            });
        } catch (error) {
            tabelaCorpo.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Erro ao carregar lista.</td></tr>`;
        }
    };

    // --- FUNÇÃO DE SELECIONAR CLIENTE ---
    const selecionarCliente = async (id, linhaSelecionada) => {
        try {
        const response = await fetch(`/usuario/buscar?idUsuario=${id}`);
            const cliente = await response.json();
            inputId.value = cliente.idUsuario;
            inputNome.value = cliente.nome;
            inputEmail.value = cliente.email;
            inputSenha.value = cliente.senha || '';
            inputEndereco.value = cliente.endereco || '';
            inputTelefone.value = cliente.telefone || '';
            inputCpf.value = cliente.cpf || '';
            inputNascimento.value = cliente.dataNascimento || '';
            selectNivel.value = String(cliente.idNivel || '');
            clienteSelecionado = cliente.idUsuario;
            btnCadastrarCliente.disabled = true; // Desabilita cadastrar
            btnAtualizarCliente.disabled = false; // Habilita atualizar
            btnExcluirCliente.disabled = false; // Habilita excluir
            tabelaCorpo && tabelaCorpo.querySelectorAll('tr').forEach(row => row.classList.remove('table-active'));
            linhaSelecionada.classList.add('table-active');
            formCliente.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (error) {
            alert('Não foi possível carregar os dados do cliente.');
        }
    };

    // --- FUNÇÕES DE AÇÃO (SALVAR, ATUALIZAR, EXCLUIR) ---

    const salvarCliente = async () => {
        const dados = new FormData();
        dados.append('nome', inputNome.value);
        dados.append('email', inputEmail.value);
        dados.append('senha', inputSenha.value);
        dados.append('endereco', inputEndereco.value);
        dados.append('telefone', inputTelefone.value);
        dados.append('cpf', inputCpf.value);
        dados.append('data_nascimento', inputNascimento.value);
        dados.append('id_nivel', selectNivel.value);
        try {
            const response = await fetch('/usuario/salvar', {
                method: 'POST',
                body: dados
            });
            const resultado = await response.json();
            if (!response.ok) throw new Error(resultado.erro || 'Erro ao salvar.');
            alert('Cliente salvo com sucesso!');
            limparFormulario();
            carregarClientes();
            window.dispatchEvent(new Event('clientesAtualizados'));
        } catch (error) {
            alert(error.message);
        }
    };

    const atualizarCliente = async () => {
        const dados = new FormData();
        dados.append('idUsuario', clienteSelecionado);
        dados.append('nome', inputNome.value);
        dados.append('email', inputEmail.value);
        dados.append('senha', inputSenha.value);
        dados.append('endereco', inputEndereco.value);
        dados.append('telefone', inputTelefone.value);
        dados.append('cpf', inputCpf.value);
        dados.append('data_nascimento', inputNascimento.value);
        dados.append('id_nivel', selectNivel.value);
        try {
            const response = await fetch('/usuario/atualizar', {
                method: 'POST',
                body: dados
            });
            const resultado = await response.json();
            if (!response.ok) throw new Error(resultado.erro || 'Erro ao atualizar.');
            alert('Cliente atualizado com sucesso!');
            limparFormulario();
            carregarClientes();
            window.dispatchEvent(new Event('clientesAtualizados'));
        } catch (error) {
            alert(error.message);
        }
    };

    const excluirCliente = async () => {
        const dados = new FormData();
        dados.append('idUsuario', clienteSelecionado);
        try {
            const response = await fetch('/usuario/deletar', {
                method: 'POST',
                body: dados
            });
            const resultado = await response.json();
            if (!response.ok) throw new Error(resultado.erro || 'Erro ao excluir.');
            alert('Cliente excluído com sucesso!');
            limparFormulario();
            carregarClientes();
            window.dispatchEvent(new Event('clientesAtualizados'));
        } catch (error) {
            alert(error.message);
        }
    };

    // --- EVENTOS ---
    formCliente.addEventListener('reset', function() {
        limparFormulario();
    });
    btnCadastrarCliente.addEventListener('click', function(e) {
        e.preventDefault();
        salvarCliente();
    });
    btnAtualizarCliente.addEventListener('click', function(e) {
        e.preventDefault();
        atualizarCliente();
    });
    btnExcluirCliente.addEventListener('click', function(e) {
        e.preventDefault();
        if (clienteSelecionado && confirm(`Tem certeza que deseja excluir o cliente ID ${clienteSelecionado}?`)) {
            excluirCliente();
        }
    });

    // --- INICIALIZAÇÃO ---
    carregarClientes();
});