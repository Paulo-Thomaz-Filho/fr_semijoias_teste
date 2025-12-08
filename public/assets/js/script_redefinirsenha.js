// =============================================================================
// SCRIPT DE REDEFINIÇÃO DE SENHA
// =============================================================================

// =============================================================================
// INICIALIZAÇÃO
// =============================================================================
document.addEventListener("DOMContentLoaded", function () {
  // -------------------------------------------------------------------------
  // ELEMENTOS DO DOM
  // -------------------------------------------------------------------------
  const formReset = document.getElementById("resetPasswordForm");
  const inputToken = document.getElementById("token");
  const inputNewPassword = document.getElementById("newPassword");
  const inputConfirmPassword = document.getElementById("confirmPassword");
  const errorDiv = document.getElementById("resetError");
  const successDiv = document.getElementById("resetSuccess");

  // -------------------------------------------------------------------------
  // UTILITÁRIOS
  // -------------------------------------------------------------------------
  function exibirMensagem(msg, tipo = "error") {
    if (tipo === "success") {
      successDiv.textContent = msg;
      successDiv.style.display = "block";
      errorDiv.style.display = "none";
    } else {
      errorDiv.textContent = msg;
      errorDiv.style.display = "block";
      successDiv.style.display = "none";
    }
  }
  function limparMensagens() {
    errorDiv.textContent = "";
    errorDiv.style.display = "none";
    successDiv.textContent = "";
    successDiv.style.display = "none";
  }

  // -------------------------------------------------------------------------
  // INICIALIZA TOKEN
  // -------------------------------------------------------------------------
  const urlParams = new URLSearchParams(window.location.search);
  const token = urlParams.get("token");
  inputToken.value = token || "";

  // -------------------------------------------------------------------------
  // EVENTO DE SUBMISSÃO DO FORMULÁRIO
  // -------------------------------------------------------------------------
  if (formReset) {
    formReset.addEventListener("submit", async function (e) {
      e.preventDefault();
      limparMensagens();
      const newPassword = inputNewPassword.value;
      const confirmPassword = inputConfirmPassword.value;
      const token = inputToken.value;
      if (newPassword.length < 6) {
        exibirMensagem("A senha deve ter pelo menos 6 caracteres.");
        return;
      }
      if (newPassword !== confirmPassword) {
        exibirMensagem("As senhas não coincidem.");
        return;
      }
      try {
        const res = await fetch("/api/usuario/redefinir-senha", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ token, password: newPassword }),
        });
        const data = await res.json();
        if (data.success) {
          exibirMensagem("Senha redefinida com sucesso!", "success");
          setTimeout(function() {
            window.location.href = "/login";
          }, 1200);
        } else {
          exibirMensagem(data.error || "Erro ao redefinir senha.");
        }
      } catch (err) {
        exibirMensagem("Erro ao redefinir senha.");
      }
    });
  }
});
