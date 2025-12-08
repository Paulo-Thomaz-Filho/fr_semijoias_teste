// =============================================================================
// SCRIPT DE CADASTRO DE USUÁRIOS
// =============================================================================

// =============================================================================
// INICIALIZAÇÃO
// =============================================================================

document.addEventListener('DOMContentLoaded', function() {
    
    // -------------------------------------------------------------------------
    // ELEMENTOS DO DOM
    // -------------------------------------------------------------------------
    
    const formCadastro = document.getElementById('formCadastro');
    const inputNome = document.getElementById('cadastroNome');
    const inputEmail = document.getElementById('cadastroEmail');
    const inputSenha = document.getElementById('cadastroSenha');
    const inputTelefone = document.getElementById('cadastroTelefone');
    const inputNascimento = document.getElementById('cadastroNascimento');
    const inputCpf = document.getElementById('cadastroCpf');
    const inputEndereco = document.getElementById('cadastroEndereco');
    const btnSignIn = document.getElementById('signIn');
    
    // -------------------------------------------------------------------------
    // FUNÇÕES DE VALIDAÇÃO
    // -------------------------------------------------------------------------
    
    /**
     * Valida os campos do formulário de cadastro
     * @returns {boolean} - True se válido, false se inválido
     */
    const validarCamposCadastro = () => {
        const nome = inputNome.value.trim();
        const email = inputEmail.value.trim();
        const senha = inputSenha.value;
        const telefone = inputTelefone.value.trim();
        const nascimento = inputNascimento.value;
        const cpf = inputCpf.value.trim();
        const endereco = inputEndereco.value.trim();

        // Funções de validação
        function validarEmail(email) {
            // Regex simples para email
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
        function validarTelefone(telefone) {
            // Aceita formatos (00) 00000-0000 ou (00) 0000-0000
            return /^\(?\d{2}\)?[\s-]?\d{4,5}-?\d{4}$/.test(telefone);
        }
        function validarCPF(cpf) {
            // Remove caracteres não numéricos
            cpf = cpf.replace(/\D/g, '');
            if (cpf.length !== 11 || /^([0-9])\1+$/.test(cpf)) return false;
            let soma = 0, resto;
            for (let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i-1, i)) * (11 - i);
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.substring(9, 10))) return false;
            soma = 0;
            for (let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i-1, i)) * (12 - i);
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.substring(10, 11))) return false;
            return true;
        }

        // Mensagem de erro
        let errorMsg = '';
        if (!nome || !email || !senha || !telefone || !nascimento || !cpf || !endereco) {
            errorMsg = 'Por favor, preencha todos os campos de cadastro.';
        } else if (!validarEmail(email)) {
            errorMsg = 'E-mail inválido.';
        } else if (!validarTelefone(telefone)) {
            errorMsg = 'Telefone inválido. Use o formato (00) 00000-0000.';
        } else if (!validarCPF(cpf)) {
            errorMsg = 'CPF inválido.';
        } else if (senha.length < 6) {
            errorMsg = 'A senha deve ter no mínimo 6 caracteres.';
        }

        const errorDiv = document.getElementById('cadastroErrorMsg');
        if (errorMsg) {
            if (errorDiv) {
                errorDiv.textContent = errorMsg;
                errorDiv.style.display = 'block';
            } else {
                alert(errorMsg);
            }
            return false;
        } else {
            if (errorDiv) errorDiv.style.display = 'none';
            return true;
        }
    };
    
    // -------------------------------------------------------------------------
    // FUNÇÕES DE CADASTRO
    // -------------------------------------------------------------------------
    
    /**
     * Realiza o cadastro do usuário
     * @param {Object} cadastroData - Objeto contendo os dados do cadastro
     */
    const realizarCadastro = async (cadastroData) => {
        try {
            const formData = new FormData();
            formData.append('nome', cadastroData.nome);
            formData.append('email', cadastroData.email);
            formData.append('senha', cadastroData.senha);
            formData.append('telefone', cadastroData.telefone);
            formData.append('data_nascimento', cadastroData.data_nascimento);
            formData.append('cpf', cadastroData.cpf);
            formData.append('endereco', cadastroData.endereco);
            
            const response = await fetch('usuario/salvar', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            console.log('Resposta do servidor:', data);
            
            if (data.sucesso) {
                let mensagem = data.mensagem;
                if (!mensagem) {
                    mensagem = 'Enviamos um email para ' + cadastroData.email + ' com as instruções para ativar sua conta.';
                }
                alert(data.sucesso + "\n" + mensagem);
                // Limpar formulário
                formCadastro.reset();
                // Voltar para o login
                if (btnSignIn) {
                    btnSignIn.click();
                }
            } else {
                const errorDiv = document.getElementById('cadastroErrorMsg');
                let errorMsg = data.erro || 'Verifique os dados e tente novamente.';
                if (errorDiv) {
                    errorDiv.textContent = errorMsg;
                    errorDiv.style.display = 'block';
                    setTimeout(() => {
                        errorDiv.style.display = 'none';
                        errorDiv.textContent = '';
                    }, 4000);
                } else {
                    alert('Erro ao cadastrar: ' + errorMsg);
                }
            }
            
        } catch (error) {
            console.error('Erro ao realizar cadastro:', error);
            alert('Ocorreu um erro de comunicação. Tente novamente.');
        }
    };
    
    // -------------------------------------------------------------------------
    // EVENTOS
    // -------------------------------------------------------------------------
    
    // Evento de submit do formulário de cadastro
    if (formCadastro) {
        formCadastro.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Validar campos
            if (!validarCamposCadastro()) {
                return;
            }
            
            // Remover máscara de telefone e CPF antes de enviar
            const telefoneLimpo = inputTelefone.value.replace(/\D/g, '');
            const cpfLimpo = inputCpf.value.replace(/\D/g, '');
            // Preparar dados para envio
            const cadastroData = {
                nome: inputNome.value,
                email: inputEmail.value,
                senha: inputSenha.value,
                telefone: telefoneLimpo,
                data_nascimento: inputNascimento.value,
                cpf: cpfLimpo,
                endereco: inputEndereco.value
            };
            // Realizar cadastro
            realizarCadastro(cadastroData);
        });
    }
    
});
