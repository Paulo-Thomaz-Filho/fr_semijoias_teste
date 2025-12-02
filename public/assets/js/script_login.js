// =============================================================================
// SCRIPT DE LOGIN E AUTENTICAÇÃO
// =============================================================================

// =============================================================================
// INICIALIZAÇÃO
// =============================================================================

document.addEventListener('DOMContentLoaded', function() {
    
    // -------------------------------------------------------------------------
    // ELEMENTOS DO DOM
    // -------------------------------------------------------------------------
    
    const formLogin = document.getElementById('loginForm');
    const inputEmail = document.getElementById('loginEmail');
    const inputSenha = document.getElementById('loginPassword');
    
    // -------------------------------------------------------------------------
    // FUNÇÕES DE AUTENTICAÇÃO
    // -------------------------------------------------------------------------
    
    /**
     * Realiza o login do usuário
     * @param {Object} loginData - Objeto contendo email e senha
     */
    const realizarLogin = async (loginData) => {
        try {
            const response = await fetch('/api/usuario/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(loginData)
            });
            
            // Esconde mensagem de erro antes de processar
            const emailError = document.getElementById('loginEmailError');
            if (emailError) {
                emailError.style.display = 'none';
                emailError.textContent = '';
            }

            const data = await response.json();

            if (data.sucesso) {
                // Salvar dados do usuário na sessionStorage
                if (data.usuario_nome) {
                    sessionStorage.setItem('usuario_nome', data.usuario_nome);
                }
                // Redirecionar conforme tipo de usuário
                if (data.isAdmin) {
                    window.location.href = '/dashboard'; 
                } else {
                    window.location.href = '/inicio'; 
                }
            } else {
                // Exibe mensagem de erro
                if (emailError) {
                    emailError.textContent = data.erro || 'Credenciais inválidas.';
                    emailError.style.display = 'block';
                }
            }
            
        } catch (error) {
            console.error('Erro ao realizar login:', error);
            alert('Ocorreu um erro de comunicação. Tente novamente.');
        }
    };
    
    /**
     * Valida os campos de login
     * @returns {boolean} - True se válido, false se inválido
     */
    const validarCamposLogin = () => {
        const email = inputEmail.value;
        const senha = inputSenha.value;
        
        if (!email || !senha) {
            alert('Por favor, digite seu e-mail e senha.');
            return false;
        }
        
        return true;
    };
    
    // -------------------------------------------------------------------------
    // EVENTOS
    // -------------------------------------------------------------------------
    
    // Evento de submit do formulário de login
    if (formLogin) {
        formLogin.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Validar campos
            if (!validarCamposLogin()) {
                return;
            }
            
            // Preparar dados para envio
            const loginData = {
                email: inputEmail.value,
                senha: inputSenha.value
            };
            
            // Realizar login
            realizarLogin(loginData);
        });
    }
    
});