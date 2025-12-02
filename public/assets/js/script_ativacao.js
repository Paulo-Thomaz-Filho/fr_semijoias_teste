// =============================================================================
// SCRIPT DE ATIVAÇÃO DE CONTA
// =============================================================================

// =============================================================================
// INICIALIZAÇÃO
// =============================================================================

document.addEventListener('DOMContentLoaded', function() {
    
    // -------------------------------------------------------------------------
    // ELEMENTOS DO DOM
    // -------------------------------------------------------------------------
    
    const divLoading = document.getElementById('loading');
    const divSuccess = document.getElementById('success');
    const divError = document.getElementById('error');
    const divFormToken = document.getElementById('form-token');
    const formAtivacao = document.getElementById('formAtivacao');
    const inputToken = document.getElementById('token');
    const errorMessage = document.getElementById('error-message');
    
    // -------------------------------------------------------------------------
    // FUNÇÕES DE EXIBIÇÃO
    // -------------------------------------------------------------------------
    
    /**
     * Mostra a tela de loading
     */
    const mostrarLoading = () => {
        divLoading.classList.remove('d-none');
        divSuccess.classList.add('d-none');
        divError.classList.add('d-none');
        divFormToken.classList.add('d-none');
    };
    
    /**
     * Mostra a tela de sucesso
     */
    const mostrarSucesso = () => {
        divLoading.classList.add('d-none');
        divSuccess.classList.remove('d-none');
        divError.classList.add('d-none');
        divFormToken.classList.add('d-none');
    };
    
    /**
     * Mostra a tela de erro
     * @param {string} mensagem - Mensagem de erro a ser exibida
     */
    const mostrarErro = (mensagem) => {
        divLoading.classList.add('d-none');
        divSuccess.classList.add('d-none');
        divError.classList.remove('d-none');
        divFormToken.classList.add('d-none');
        
        if (mensagem) {
            errorMessage.textContent = mensagem;
        }
    };
    
    /**
     * Mostra o formulário de token manual
     */
    const mostrarFormulario = () => {
        divLoading.classList.add('d-none');
        divSuccess.classList.add('d-none');
        divError.classList.add('d-none');
        divFormToken.classList.remove('d-none');
    };
    
    /**
     * Mostra o formulário de reenviar email
     */
    const mostrarReenviarEmail = () => {
        divLoading.classList.add('d-none');
        divSuccess.classList.add('d-none');
        divError.classList.add('d-none');
        divFormToken.classList.add('d-none');
        document.getElementById('form-reenviar').classList.remove('d-none');
    };
    
    /**
     * Volta para a tela de erro
     */
    const voltarParaErro = () => {
        document.getElementById('form-reenviar').classList.add('d-none');
        mostrarErro();
    };
    
    // Tornar funções globais para uso no HTML
    window.mostrarFormulario = mostrarFormulario;
    window.mostrarReenviarEmail = mostrarReenviarEmail;
    window.voltarParaErro = voltarParaErro;
    
    // -------------------------------------------------------------------------
    // FUNÇÕES DE ATIVAÇÃO
    // -------------------------------------------------------------------------
    
    /**
     * Ativa a conta do usuário
     * @param {string} tokenAtivacao - Token de ativação da conta
     */
    const ativarConta = async (tokenAtivacao) => {
        mostrarLoading();
        
        try {
            const response = await fetch('/usuario/ativar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ token: tokenAtivacao })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                mostrarSucesso();
                
                // Redirecionar após 3 segundos
                setTimeout(() => {
                    window.location.href = '/login';
                }, 3000);
            } else {
                mostrarErro(data.erro || 'Token inválido ou expirado.');
            }
            
        } catch (error) {
            console.error('Erro ao ativar conta:', error);
            mostrarErro('Erro ao processar a ativação. Tente novamente mais tarde.');
        }
    };
    
    // -------------------------------------------------------------------------
    // VERIFICAÇÃO DE TOKEN NA URL
    // -------------------------------------------------------------------------
    
    // Verificar se tem token na URL
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    
    if (token && token.length === 6) {
        // Se tem token na URL, ativar automaticamente
        ativarConta(token);
    } else {
        // Senão, mostrar formulário
        mostrarFormulario();
    }
    
    // -------------------------------------------------------------------------
    // EVENTOS
    // -------------------------------------------------------------------------
    
    // Handler do formulário manual
    if (formAtivacao) {
        formAtivacao.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const tokenInput = inputToken.value.trim().toUpperCase();
            
            if (tokenInput.length !== 6) {
                alert('O código deve ter 6 caracteres.');
                return;
            }
            
            ativarConta(tokenInput);
        });
    }
    
    // Converter input para maiúsculas automaticamente
    if (inputToken) {
        inputToken.addEventListener('input', (e) => {
            e.target.value = e.target.value.toUpperCase();
        });
    }
    
    // Handler do formulário de reenviar email
    const formReenviar = document.getElementById('formReenviar');
    if (formReenviar) {
        formReenviar.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('emailReenviar').value.trim();
            const messageDiv = document.getElementById('reenviar-message');
            
            if (!email) {
                messageDiv.innerHTML = '<div class="alert alert-danger rounded-4">Por favor, digite seu e-mail.</div>';
                return;
            }
            
            try {
                messageDiv.innerHTML = '<div class="alert alert-info rounded-4">Enviando...</div>';
                
                const response = await fetch('/usuario/reenviar-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    messageDiv.innerHTML = '<div class="alert alert-success rounded-4">' + data.mensagem + '</div>';
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 3000);
                } else {
                    messageDiv.innerHTML = '<div class="alert alert-danger rounded-4">' + (data.erro || 'Erro ao reenviar email.') + '</div>';
                }
                
            } catch (error) {
                console.error('Erro ao reenviar email:', error);
                messageDiv.innerHTML = '<div class="alert alert-danger rounded-4">Erro ao processar solicitação.</div>';
            }
        });
    }
    
});
