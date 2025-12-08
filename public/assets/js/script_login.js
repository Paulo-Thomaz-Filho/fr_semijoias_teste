// =============================================================================
// SCRIPT DE LOGIN E AUTENTICAÇÃO
// =============================================================================

// =============================================================================
// INICIALIZAÇÃO
// =============================================================================

document.addEventListener('DOMContentLoaded', function() {
            // Corrige problema de aria-hidden no modal Bootstrap
            const forgotPasswordModal = document.getElementById('forgotPasswordModal');
            if (forgotPasswordModal) {
                forgotPasswordModal.addEventListener('shown.bs.modal', function () {
                    forgotPasswordModal.removeAttribute('aria-hidden');
                });
            }
        // Modal de redefinição de senha
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        const forgotEmailInput = document.getElementById('forgotEmail');
        const forgotPasswordMsg = document.getElementById('forgotPasswordMsg');
        if (forgotPasswordForm) {
            forgotPasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = forgotEmailInput.value.trim();
                forgotPasswordMsg.style.display = 'none';
                forgotPasswordMsg.className = 'small text-center mb-2';
                if (!email || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
                    forgotPasswordMsg.textContent = 'Digite um e-mail válido.';
                    forgotPasswordMsg.classList.add('text-danger');
                    forgotPasswordMsg.style.display = 'block';
                    return;
                }
                // Desabilita botão
                const btn = forgotPasswordForm.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'Enviando...';
                fetch('/public/api/usuario/solicitarRedefinicaoSenha.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success || data.sucesso) {
                        forgotPasswordMsg.textContent = data.mensagem || 'Solicitação enviada!';
                        forgotPasswordMsg.classList.remove('text-danger');
                        forgotPasswordMsg.classList.add('text-success');
                        forgotPasswordForm.reset();
                    } else {
                        forgotPasswordMsg.textContent = (data.error || data.erro || 'Erro ao solicitar redefinição de senha.') + (data.debug ? ' [' + data.debug + ']' : '');
                        forgotPasswordMsg.classList.remove('text-success');
                        forgotPasswordMsg.classList.add('text-danger');
                    }
                    forgotPasswordMsg.style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = 'Enviar instruções';
                })
                .catch((err) => {
                    forgotPasswordMsg.textContent = 'Erro ao solicitar redefinição de senha.';
                    forgotPasswordMsg.classList.add('text-danger');
                    forgotPasswordMsg.style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = 'Enviar instruções';
                });
            });
        }
    
    // -------------------------------------------------------------------------
    // ELEMENTOS DO DOM
    // -------------------------------------------------------------------------
    
    const formLogin = document.getElementById('loginForm');
    const inputEmail = document.getElementById('loginEmail');
    const inputSenha = document.getElementById('loginPassword');
    const forgotPasswordLink = document.getElementById('forgotPasswordLink');
    
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
                                const usuario = {
                                    nome: data.usuario_nome,
                                    idUsuario: data.usuario_id || data.idUsuario,
                                    acesso: data.usuario_acesso || data.acesso,
                                    nivel: data.usuario_nivel || data.nivel
                                };
                                sessionStorage.setItem('usuario', JSON.stringify(usuario));
                                sessionStorage.setItem('usuario_nome', data.usuario_nome || 'Usuário');
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
                    setTimeout(() => {
                        emailError.style.display = 'none';
                        emailError.textContent = '';
                    }, 4000);
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

    // O link agora apenas abre o modal (Bootstrap data-bs-toggle)
    
});