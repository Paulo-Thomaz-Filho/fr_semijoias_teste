// =============================================================================
// SCRIPT DE TOGGLE DE SENHA (LOGIN E CADASTRO)
// =============================================================================

// =============================================================================
// INICIALIZAÇÃO
// =============================================================================

document.addEventListener("DOMContentLoaded", function () {
  // ---------------------------------------------------------------------------
  // ELEMENTOS DO DOM - LOGIN
  // ---------------------------------------------------------------------------
  const loginPasswordInput = document.getElementById("loginPassword");
  const loginToggleBtn = document.getElementById("toggleLoginPassword");
  const loginEyeOpen = document.getElementById("loginEyeOpen");
  const loginEyeClosed = document.getElementById("loginEyeClosed");

  // ---------------------------------------------------------------------------
  // ELEMENTOS DO DOM - CADASTRO
  // ---------------------------------------------------------------------------
  const cadastroPasswordInput = document.getElementById("cadastroSenha");
  const cadastroToggleBtn = document.getElementById("toggleCadastroPassword");
  const cadastroEyeOpen = document.getElementById("cadastroEyeOpen");
  const cadastroEyeClosed = document.getElementById("cadastroEyeClosed");

  // ---------------------------------------------------------------------------
  // FUNÇÕES AUXILIARES
  // ---------------------------------------------------------------------------

  /**
   * Alterna a visibilidade da senha
   * @param {HTMLInputElement} input - Campo de senha
   * @param {HTMLElement} eyeOpen - Ícone olho aberto
   * @param {HTMLElement} eyeClosed - Ícone olho fechado
   */
  const togglePasswordVisibility = (input, eyeOpen, eyeClosed) => {
    const isPassword = input.type === "password";
    input.type = isPassword ? "text" : "password";
    eyeOpen.style.display = isPassword ? "block" : "none";
    eyeClosed.style.display = isPassword ? "none" : "block";
  };

  /**
   * Mostra ou esconde o botão de toggle baseado no valor do input
   * @param {HTMLInputElement} input - Campo de senha
   * @param {HTMLElement} toggleBtn - Botão de toggle
   */
  const handlePasswordInput = (input, toggleBtn) => {
    toggleBtn.style.visibility = input.value.length > 0 ? "visible" : "hidden";
  };

  // ---------------------------------------------------------------------------
  // CONFIGURAÇÃO INICIAL
  // ---------------------------------------------------------------------------

  // Esconder botões inicialmente
  if (loginToggleBtn) {
    loginToggleBtn.style.visibility = "hidden";
  }

  if (cadastroToggleBtn) {
    cadastroToggleBtn.style.visibility = "hidden";
  }

  // ---------------------------------------------------------------------------
  // EVENT LISTENERS - LOGIN
  // ---------------------------------------------------------------------------

  if (loginPasswordInput && loginToggleBtn) {
    loginPasswordInput.addEventListener("input", function () {
      handlePasswordInput(this, loginToggleBtn);
    });

    loginToggleBtn.addEventListener("click", function () {
      togglePasswordVisibility(
        loginPasswordInput,
        loginEyeOpen,
        loginEyeClosed
      );
    });
  }

  // ---------------------------------------------------------------------------
  // EVENT LISTENERS - CADASTRO
  // ---------------------------------------------------------------------------

  if (cadastroPasswordInput && cadastroToggleBtn) {
    cadastroPasswordInput.addEventListener("input", function () {
      handlePasswordInput(this, cadastroToggleBtn);
    });

    cadastroToggleBtn.addEventListener("click", function () {
      togglePasswordVisibility(
        cadastroPasswordInput,
        cadastroEyeOpen,
        cadastroEyeClosed
      );
    });
  }
});
