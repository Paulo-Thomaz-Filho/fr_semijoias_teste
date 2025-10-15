document.addEventListener('DOMContentLoaded', function() {
    // Referências aos elementos
    const form = document.getElementById('formCliente');
    const inputId = document.getElementById('clienteId');
    const inputNome = document.getElementById('clienteNome');
    const inputEmail = document.getElementById('clienteEmail');
    const btnSalvar = document.getElementById('btnSalvar');
    const btnAtualizar = document.getElementById('btnAtualizar');
    const btnExcluir = document.getElementById('btnExcluir');
    const btnLimpar = document.getElementById('btnLimpar');
    const tabelaCorpo = document.querySelector('#tabelaUsuarios tbody');
    let idUsuarioSelecionado = null;

    // Reseta o formulário para o estado inicial
    const resetarFormulario = () => {
        form.reset();
        inputId.value = 'Auto';
        idUsuarioSelecionado = null;
        btnSalvar.classList.remove('d-none');
        btnAtualizar.classList.add('d-none');
        btnExcluir.classList.add('d-none');
        document.querySelectorAll('#tabelaUsuarios tbody tr').forEach(row => row.classList.remove('table-active'));
    };

    // Carrega a lista de usuários e preenche a tabela
    const carregarUsuarios = async () => {
        tabelaCorpo.innerHTML = `<tr><td colspan="4" class="text-center">Carregando...</td></tr>`;
        try {
            const response = await fetch('/usuarios');
            if (!response.ok) throw new Error('Falha ao carregar usuários.');
            const usuarios = await response.json();
            tabelaCorpo.innerHTML = '';
            if (usuarios.length === 0) {
                tabelaCorpo.innerHTML = `<tr><td colspan="5" class="text-center">Nenhum usuário cadastrado.</td></tr>`;
                return;
            }
            usuarios.forEach(usuario => {
                const linha = `
                    <tr data-id="${usuario.id}">
                        <td>${usuario.id}</td>
                        <td>${usuario.nome}</td>
                        <td>${usuario.email}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-secondary btn-selecionar">Selecionar</button>
                        </td>
                    </tr>`;
                tabelaCorpo.insertAdjacentHTML('beforeend', linha);
            });
        } catch (error) {
            console.error(error);
            tabelaCorpo.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Erro ao carregar lista.</td></tr>`;
        }
    };

    // Preenche o formulário com dados de um usuário selecionado
    const selecionarUsuario = async (id, linhaSelecionada) => {
        try {
            const response = await fetch(`/usuarios/buscar?idUsuario=${id}`);
            if (!response.ok) throw new Error('Usuário não encontrado.');
            const usuario = await response.json();
            inputId.value = usuario.id;
            inputNome.value = usuario.nome;
            inputEmail.value = usuario.email;
            idUsuarioSelecionado = usuario.id;
            btnSalvar.classList.add('d-none');
            btnAtualizar.classList.remove('d-none');
            btnExcluir.classList.remove('d-none');
            document.querySelectorAll('#tabelaUsuarios tbody tr').forEach(row => row.classList.remove('table-active'));
            linhaSelecionada.classList.add('table-active');
        } catch (error) {
            console.error(error);
            alert('Não foi possível carregar os dados do usuário.');
        }
    };

    // --- FUNÇÕES DE AÇÃO (SALVAR, ATUALIZAR, EXCLUIR) ---

    const salvarUsuario = async () => {
        const dados = new FormData(form);
        try {
            const response = await fetch('/usuarios/salvar', {
                method: 'POST',
                body: dados
            });
            const resultado = await response.json();
            if (!response.ok) throw new Error(resultado.erro || 'Erro ao salvar.');
            alert('Usuário salvo com sucesso!');
            resetarFormulario();
            carregarUsuarios();
        } catch (error) {
            alert(error.message);
        }
    };

    const atualizarUsuario = async () => {
        const dados = new FormData(form);
        dados.append('id', idUsuarioSelecionado); 
        try {
            const response = await fetch('/usuarios/atualizar', {
                method: 'POST',
                body: dados
            });
            const resultado = await response.json();
            if (!response.ok) throw new Error(resultado.erro || 'Erro ao atualizar.');
            alert('Usuário atualizado com sucesso!');
            resetarFormulario();
            carregarUsuarios();
        } catch (error) {
            alert(error.message);
        }
    };

    const excluirUsuario = async () => {
        const dados = new FormData();
        dados.append('id', idUsuarioSelecionado);
        try {
            const response = await fetch('/usuarios/deletar', {
                method: 'POST',
                body: dados
            });
            const resultado = await response.json();
            if (!response.ok) throw new Error(resultado.erro || 'Erro ao excluir.');
            alert('Usuário excluído com sucesso!');
            resetarFormulario();
            carregarUsuarios();
        } catch (error) {
            alert(error.message);
        }
    };

    // --- EVENT LISTENERS ---

    tabelaCorpo.addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('btn-selecionar')) {
            const linha = e.target.closest('tr');
            selecionarUsuario(linha.dataset.id, linha);
        }
    });

    btnLimpar.addEventListener('click', resetarFormulario);
    btnSalvar.addEventListener('click', salvarUsuario);
    btnAtualizar.addEventListener('click', atualizarUsuario);
    btnExcluir.addEventListener('click', () => {
        if (confirm(`Tem certeza que deseja excluir o usuário ID ${idUsuarioSelecionado}?`)) {
            excluirUsuario();
        }
    });

    // --- INICIALIZAÇÃO ---
    carregarUsuarios();
});