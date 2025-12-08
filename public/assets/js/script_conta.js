// ...existing code...
// ================= MODAL DE EDIÇÃO DE PERFIL =====================
document.addEventListener("DOMContentLoaded", function () {
  // Função para abrir modal e preencher campos
  const btnEditar = document.getElementById("btn-editar");
  if (btnEditar) {
    btnEditar.addEventListener("click", function () {
      document.getElementById("modal-nome").value =
        document.getElementById("nome").value;
      document.getElementById("modal-email").value =
        document.getElementById("email").value;
      document.getElementById("modal-senha").value = "";
      document.getElementById("modal-telefone").value =
        document.getElementById("telefone").value;
      document.getElementById("modal-endereco").value =
        document.getElementById("endereco").value;
      document.getElementById("modal-nascimento").value =
        document.getElementById("nascimento").value;
      document.getElementById("modal-cpf").value =
        document.getElementById("cpf").value;
      // Aplica máscaras nos campos do modal
      aplicarMascarasModal();
      // Abre o modal
      var modal = new bootstrap.Modal(
        document.getElementById("modalEditarPerfil")
      );
      modal.show();
    });
  }

  // Função para validar email
  function validarEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  // Função para validar telefone (mínimo 10 dígitos)
  function validarTelefone(telefone) {
    return telefone.length >= 10 && telefone.length <= 11;
  }

  // Função para validar CPF
  function validarCPF(cpf) {
    cpf = cpf.replace(/\D/g, "");
    if (cpf.length !== 11 || /^([0-9])\1+$/.test(cpf)) return false;
    let soma = 0, resto;
    for (let i = 1; i <= 9; i++) soma += parseInt(cpf[i - 1]) * (11 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf[9])) return false;
    soma = 0;
    for (let i = 1; i <= 10; i++) soma += parseInt(cpf[i - 1]) * (12 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf[10])) return false;
    return true;
  }

  // Função para salvar alterações do modal
  const btnSalvarModal = document.getElementById("btn-salvar-modal");
  if (btnSalvarModal) {
    btnSalvarModal.addEventListener("click", async function () {
      const nome = document.getElementById("modal-nome").value.trim();
      const email = document.getElementById("modal-email").value.trim();
      const senha = document.getElementById("modal-senha").value;
      const telefone = document.getElementById("modal-telefone").value.replace(/\D/g, "");
      const endereco = document.getElementById("modal-endereco").value.trim();
      const nascimento = document.getElementById("modal-nascimento").value;
      const cpf = document.getElementById("modal-cpf").value.replace(/\D/g, "");

      const msgDiv = document.getElementById("modal-msg");
      msgDiv.style.display = "none";
      msgDiv.textContent = "";

      // Validação dos campos
      if (!nome) {
        msgDiv.textContent = "O nome é obrigatório.";
        msgDiv.className = "text-danger text-center small mt-2";
        msgDiv.style.display = "block";
        return;
      }
      if (!validarEmail(email)) {
        msgDiv.textContent = "Informe um email válido.";
        msgDiv.className = "text-danger text-center small mt-2";
        msgDiv.style.display = "block";
        return;
      }
      if (!validarTelefone(telefone)) {
        msgDiv.textContent = "Informe um telefone válido (com DDD).";
        msgDiv.className = "text-danger text-center small mt-2";
        msgDiv.style.display = "block";
        return;
      }
      if (!validarCPF(cpf)) {
        msgDiv.textContent = "Informe um CPF válido.";
        msgDiv.className = "text-danger text-center small mt-2";
        msgDiv.style.display = "block";
        return;
      }

      // Monta dados para enviar (só envia senha se preenchida)
      const dados = { nome, email, telefone, endereco, nascimento, cpf };
      if (senha) dados.senha = senha;

      try {
        const response = await fetch("/usuario/atualizar-dados", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams(dados),
        });
        if (response.ok) {
          // Atualiza os campos da página
          document.getElementById("nome").value = nome;
          document.getElementById("email").value = email;
          document.getElementById("telefone").value = telefone;
          document.getElementById("endereco").value = endereco;
          document.getElementById("nascimento").value = nascimento;
          document.getElementById("cpf").value = cpf;
          aplicarMascarasPrincipais();
          document.getElementById("user-nome").textContent = nome;
          document.getElementById("user-email").textContent = email;
          if (typeof atualizarAvatar === "function") atualizarAvatar(nome);
          msgDiv.textContent = "Dados atualizados com sucesso!";
          msgDiv.className = "text-success text-center small mt-2";
          msgDiv.style.display = "block";
          setTimeout(() => {
            msgDiv.style.display = "none";
            msgDiv.textContent = "";
            bootstrap.Modal.getInstance(
              document.getElementById("modalEditarPerfil")
            ).hide();
          }, 1800);
        } else {
          const erro = await response.text();
          msgDiv.textContent = erro || "Erro ao atualizar os dados. Tente novamente.";
          msgDiv.className = "text-danger text-center small mt-2";
          msgDiv.style.display = "block";
        }
      } catch (error) {
        msgDiv.textContent = "Erro ao atualizar os dados: " + error.message;
        msgDiv.className = "text-danger text-center small mt-2";
        msgDiv.style.display = "block";
      }
    });
  }
});
// =============================================================================
// SCRIPT DO PERFIL DO USUÁRIO
// =============================================================================

// Logout global
document.addEventListener("DOMContentLoaded", function () {
  // Inicializar navegação de abas
  inicializarAbas();

  // Carregar dados do usuário
  carregarDadosUsuario();

  // Adicionar evento ao botão de sair
  const btnSair = document.getElementById("btn-sair");
  if (btnSair) {
    btnSair.addEventListener("click", function (e) {
      e.preventDefault();
      fetch("/api/usuario/logout")
        .then((res) => res.json())
        .then((json) => {
          if (json.success) {
            localStorage.clear();
            sessionStorage.clear();
            window.location.href = "/inicio";
          }
        });
    });
  }
});

// =============================================================================
// FUNÇÕES DE NAVEGAÇÃO
// =============================================================================

// Troca de abas com navegação real
const inicializarAbas = () => {
  const linksCategoria = document.querySelectorAll(".categoria-link");
  linksCategoria.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const href = this.getAttribute("href");
      if (href) {
        window.location.href = href;
      }
    });
  });
};

// =============================================================================
// FUNÇÕES DE AVATAR
// =============================================================================

// Gerar cor de fundo aleatória
const getRandomColor = () => {
  const colors = [
    "6c757d",
    "007bff",
    "28a745",
    "dc3545",
    "ffc107",
    "17a2b8",
    "6610f2",
    "fd7e14",
    "20c997",
    "e83e8c",
  ];
  return colors[Math.floor(Math.random() * colors.length)];
};

// Pegar iniciais do nome (primeira letra do primeiro nome e do último nome não-preposição ou, se não houver, da segunda parte)
const getInitials = (nome) => {
  if (!nome) return "US";
  nome = nome.trim();
  const preposicoes = ["da", "de", "do", "das", "dos", "e"];
  const partes = nome.split(" ").filter(Boolean);
  if (partes.length === 1) {
    return (
      partes[0][0].toUpperCase() +
      (partes[0][1] ? partes[0][1].toUpperCase() : "")
    );
  }
  // Encontrar o último nome que não seja preposição e não seja igual ao primeiro nome
  let ultimo = "";
  for (let i = partes.length - 1; i > 0; i--) {
    if (
      !preposicoes.includes(partes[i].toLowerCase()) &&
      partes[i].toLowerCase() !== partes[0].toLowerCase()
    ) {
      ultimo = partes[i];
      break;
    }
  }
  // Se não encontrar, tenta usar a segunda parte do nome
  if (!ultimo && partes.length > 1) {
    ultimo = partes[1];
  }
  return partes[0][0].toUpperCase() + (ultimo ? ultimo[0].toUpperCase() : "");
};

// Atualizar avatar do usuário
window.atualizarAvatar = function (nome) {
  const initials = getInitials(nome);
  let bg = sessionStorage.getItem("avatarBgColor");
  if (!bg) {
    bg = getRandomColor();
    sessionStorage.setItem("avatarBgColor", bg);
  }
  const url = `https://ui-avatars.com/api/?name=${initials}&background=${bg}&color=fff&size=128`;

  const avatarImg = document.getElementById("avatar-img");
  if (avatarImg) {
    avatarImg.src = url;
  }
};

// =============================================================================
// FUNÇÕES DE CARREGAMENTO DE DADOS
// =============================================================================

// Carregar dados do usuário
const carregarDadosUsuario = async () => {
  try {
    const response = await fetch("usuario/buscar-dados", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    });

    const result = await response.json();
    const dados = result.dados;

    // Preencher os campos do formulário
    const campoNome = document.getElementById("nome");
    const campoEmail = document.getElementById("email");
    const campoCPF = document.getElementById("cpf");
    const campoNascimento = document.getElementById("nascimento");
    const campoTelefone = document.getElementById("telefone");
    const campoEndereco = document.getElementById("endereco");

    if (campoNome) campoNome.value = dados.nome || "";
    if (campoEmail) campoEmail.value = dados.email || "";
    if (campoCPF) campoCPF.value = dados.cpf || "";
    if (campoNascimento) campoNascimento.value = dados.nascimento || "";
    if (campoTelefone) campoTelefone.value = dados.telefone || "";
    if (campoEndereco) campoEndereco.value = dados.endereco || "";

    // Aplica máscaras nos campos principais após carregar dados
    aplicarMascarasPrincipais();

    // Preencher elementos de perfil no topo
    const userNome = document.getElementById("user-nome");
    const userEmail = document.getElementById("user-email");

    if (dados.nome && userNome) {
      userNome.textContent = dados.nome;
    }
    if (dados.email && userEmail) {
      userEmail.textContent = dados.email;
    }

    // Atualizar avatar
    if (typeof atualizarAvatar === "function") {
      atualizarAvatar(dados.nome);
    }
  } catch (error) {
    console.error("Erro ao carregar dados do usuário:", error);
    alert("Erro ao carregar os dados do usuário: " + error.message);
  }
};

// =============================================================================
// FUNÇÕES DE ATUALIZAÇÃO
// =============================================================================

// Aplica máscaras nos campos principais do formulário
function aplicarMascarasPrincipais() {
  var tel = document.getElementById("telefone");
  if (tel && typeof maskTelefone === "function") {
    maskTelefone(tel);
    tel.addEventListener("input", function () {
      maskTelefone(this);
    });
  }
  var cpf = document.getElementById("cpf");
  if (cpf && typeof maskCPF === "function") {
    maskCPF(cpf);
    cpf.addEventListener("input", function () {
      maskCPF(this);
    });
  }
  var nasc = document.getElementById("nascimento");
  if (nasc && typeof maskData === "function") {
    maskData(nasc);
    nasc.addEventListener("input", function () {
      maskData(this);
    });
  }
}

// Aplica máscaras nos campos do modal de edição de perfil
function aplicarMascarasModal() {
  var telModal = document.getElementById("modal-telefone");
  if (telModal && typeof maskTelefone === "function") {
    maskTelefone(telModal);
    telModal.addEventListener("input", function () {
      maskTelefone(this);
    });
  }
  var cpfModal = document.getElementById("modal-cpf");
  if (cpfModal && typeof maskCPF === "function") {
    maskCPF(cpfModal);
    cpfModal.addEventListener("input", function () {
      maskCPF(this);
    });
  }
  var nascModal = document.getElementById("modal-nascimento");
  if (nascModal && typeof maskData === "function") {
    maskData(nascModal);
    nascModal.addEventListener("input", function () {
      maskData(this);
    });
  }
}

// Salvar alterações do usuário
const salvarAlteracoes = async () => {
  const campoNome = document.getElementById("nome");
  const campoEmail = document.getElementById("email");
  const campoCPF = document.getElementById("cpf");
  const campoNascimento = document.getElementById("nascimento");
  const campoTelefone = document.getElementById("telefone");
  const campoEndereco = document.getElementById("endereco");

  const cpf = campoCPF ? campoCPF.value : "";
  const nome = campoNome ? campoNome.value : "";
  const email = campoEmail ? campoEmail.value : "";
  const nascimento = campoNascimento ? campoNascimento.value : "";
  const telefone = campoTelefone ? campoTelefone.value : "";
  const endereco = campoEndereco ? campoEndereco.value : "";

  try {
    const response = await fetch("/usuario/atualizar-dados", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        nome: nome,
        email: email,
        cpf: cpf,
        nascimento: nascimento,
        telefone: telefone,
        endereco: endereco,
      }),
    });

    if (response.ok) {
      alert("Dados atualizados com sucesso!");
    } else {
      throw new Error("Erro na resposta do servidor");
    }
  } catch (error) {
    console.error("Erro ao atualizar dados:", error);
    alert("Erro ao atualizar os dados: " + error.message);
  }
};

// =============================================================================
