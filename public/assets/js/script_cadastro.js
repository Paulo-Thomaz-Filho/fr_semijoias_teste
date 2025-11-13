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
        const nome = inputNome.value;
        const email = inputEmail.value;
        const senha = inputSenha.value;
        const telefone = inputTelefone.value;
        const cpf = inputCpf.value;
        const endereco = inputEndereco.value;
        
        // Verificar se todos os campos estão preenchidos
        if (!nome || !email || !senha || !telefone || !cpf || !endereco) {
            alert('Por favor, preencha todos os campos de cadastro');
            return false;
        }
        
        // Validação de senha
        if (senha.length < 6) {
            alert('A senha deve ter no mínimo 6 caracteres');
            return false;
        }
        
        return true;
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
            console.log('Enviando dados do cadastro via JSON');
            
            const response = await fetch('usuario/salvar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(cadastroData)
            });
            
            const data = await response.json();
            console.log('Resposta do servidor:', data);
            
            if (data.sucesso) {
                alert('Cadastro realizado com sucesso! Você já pode fazer login.');
                
                // Limpar formulário
                formCadastro.reset();
                
                // Voltar para o login
                if (btnSignIn) {
                    btnSignIn.click();
                }
            } else {
                alert('Erro ao cadastrar: ' + (data.erro || 'Verifique os dados e tente novamente.'));
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
            
            // Preparar dados para envio
            const cadastroData = {
                nome: inputNome.value,
                email: inputEmail.value,
                senha: inputSenha.value,
                telefone: inputTelefone.value,
                cpf: inputCpf.value,
                endereco: inputEndereco.value
            };
            
            // Realizar cadastro
            realizarCadastro(cadastroData);
        });
    }
    
});
