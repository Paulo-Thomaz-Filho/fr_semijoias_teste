// =============================================================================
// SCRIPTS DE COMPRAS
// =============================================================================

// -----------------------------------------------------------------------------
// UTILITÁRIOS DE USUÁRIO
// -----------------------------------------------------------------------------
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
  if (!ultimo && partes.length > 1) {
    ultimo = partes[1];
  }
  return partes[0][0].toUpperCase() + (ultimo ? ultimo[0].toUpperCase() : "");
};

// -----------------------------------------------------------------------------
// COR ALEATÓRIA PARA AVATAR
// -----------------------------------------------------------------------------
const getRandomColor = () => {
  const colors = [
    "6c757d", "007bff", "28a745", "dc3545", "ffc107",
    "17a2b8", "6610f2", "fd7e14", "20c997", "e83e8c",
  ];
  return colors[Math.floor(Math.random() * colors.length)];
};

// -----------------------------------------------------------------------------
// CARREGA HEADER DO USUÁRIO
// -----------------------------------------------------------------------------
const carregarHeaderUsuario = async () => {
  try {
    const response = await fetch("/usuario/buscar-dados", {
      method: "GET",
      headers: { "Content-Type": "application/json" },
    });
    if (!response.ok) return;
    const result = await response.json();
    const dados = result.dados;
    if (!dados) return;
    const userNome = document.getElementById("user-nome");
    const userEmail = document.getElementById("user-email");
    const avatarImg = document.getElementById("avatar-img");
    if (userNome) userNome.textContent = dados.nome || "Usuário";
    if (userEmail) userEmail.textContent = dados.email || "-";
    if (avatarImg) {
      const initials = getInitials(dados.nome);
      let bg = sessionStorage.getItem("avatarBgColor");
      if (!bg) {
        bg = getRandomColor();
        sessionStorage.setItem("avatarBgColor", bg);
      }
      avatarImg.src = `https://ui-avatars.com/api/?name=${initials}&background=${bg}&color=fff&size=128`;
    }
  } catch (e) {}
};

// -----------------------------------------------------------------------------
// CLASSE DE BADGE DE STATUS
// -----------------------------------------------------------------------------
const getStatusBadgeClass = (status) => {
  if (!status) return "status-badge";
  const s = status.toLowerCase();
  if (s.includes("pago") || s.includes("aprovado")) return "status-badge status-green";
  if (s.includes("pendente")) return "status-badge status-pending";
  if (s.includes("cancelado") || s.includes("recusado")) return "status-badge status-danger";
  if (s.includes("enviado")) return "status-badge status-sent";
  if (s.includes("entregue")) return "status-badge status-green";
  return "status-badge";
};

// -----------------------------------------------------------------------------
// INICIALIZAÇÃO PRINCIPAL
// -----------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", async () => {
  await carregarHeaderUsuario();
  const pedidosContainer = document.getElementById("compras-lista");
  let usuarioId = null;

  try {
    // Busca dados do usuário autenticado
    const response = await fetch("/usuario/buscar-dados", {
      method: "GET",
      headers: { "Content-Type": "application/json" },
    });
    if (!response.ok) throw new Error();
    const result = await response.json();
    if (!result.dados || !result.dados.idUsuario) throw new Error();
    usuarioId = result.dados.idUsuario;
  } catch {
    pedidosContainer.innerHTML = `
      <div class="alert alert-warning rounded-4">
        Usuário não autenticado.
      </div>
    `;
    return;
  }

  fetch("/pedidos")
    .then((res) => res.json())
    .then((pedidos) => {
      if (!Array.isArray(pedidos) || pedidos.length === 0) {
        pedidosContainer.innerHTML = `
          <div class="card border-0">
            <div class="card-body text-center py-5">
              <svg width="80" height="80" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted mb-3">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.50035 9.3C5.487 8.31988 6.27024 7.51426 7.25035 7.5H17.7503C18.7305 7.51426 19.5137 8.31988 19.5004 9.3V17.4C19.5276 19.3605 17.9608 20.972 16.0004 21H9.00035C7.03989 20.972 5.4731 19.3605 5.50035 17.4V9.3Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.0004 10.2V6.6C16.0276 4.63953 14.4608 3.02797 12.5004 3C10.5399 3.02797 8.9731 4.63953 9.00035 6.6V10.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <h5 class="fw-bold text-muted mb-2">Nenhum pedido encontrado</h5>
              <p class="text-muted mb-4">Não há pedidos no sistema.</p>
            </div>
          </div>
        `;
        return;
      }

      // Filtra pedidos do usuário logado
      const pedidosUsuario = pedidos.filter(
        (p) => String(p.idCliente) === String(usuarioId)
      );

      if (pedidosUsuario.length === 0) {
        pedidosContainer.innerHTML = `
          <div class="card border-0">
            <div class="card-body text-center py-5">
              <svg width="80" height="80" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-dark mb-3">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.50035 9.3C5.487 8.31988 6.27024 7.51426 7.25035 7.5H17.7503C18.7305 7.51426 19.5137 8.31988 19.5004 9.3V17.4C19.5276 19.3605 17.9608 20.972 16.0004 21H9.00035C7.03989 20.972 5.4731 19.3605 5.50035 17.4V9.3Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.0004 10.2V6.6C16.0276 4.63953 14.4608 3.02797 12.5004 3C10.5399 3.02797 8.9731 4.63953 9.00035 6.6V10.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <h5 class="fw-bold text-muted mb-2">Nenhum pedido ainda</h5>
              <p class="text-muted mb-4">Você ainda não fez nenhuma compra.</p>
              <a href="/" class="btn btn-dark rounded-pill px-4">Começar a comprar</a>
            </div>
          </div>
        `;
        return;
      }

      // Gera os cards de pedidos com a nova estilização
      pedidosContainer.innerHTML = pedidosUsuario
        .map((p) => {
          const valorTotal =
            (parseFloat(p.preco) || 0) * (parseInt(p.quantidade) || 1);
          const valor = valorTotal.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL",
          });
          const data = p.dataPedido
            ? new Date(p.dataPedido).toLocaleDateString("pt-BR", {
                day: "2-digit",
                month: "long",
                year: "numeric",
              })
            : "-";

          const statusBadgeClass = getStatusBadgeClass(p.status);
          const imagemSrc =
            p.caminhoImagem &&
            p.caminhoImagem !== "undefined" &&
            p.caminhoImagem !== ""
              ? `../assets/images/${p.caminhoImagem}`
              : "../assets/images/placeholder-image.svg";

          return `
            <div class="card border-1 border shadow-sm rounded-3 mb-3">
              <div class="card-body p-3 p-sm-4">
                <div class="d-flex flex-row justify-content-between align-items-center mb-3">
                  <div>
                    <h5 class="fw-bold mb-1 text-dark">Pedido #${p.idPedido || "-"}</h5>
                    <p class="text-muted small mb-0">${data}</p>
                  </div>
                  <span class="${statusBadgeClass}">${
            p.status || "Status"
          }</span>
                </div>
                
                <div class="mb-3">
                  <div class="d-flex align-items-center gap-3 mb-2">
                    <img 
                      src="${imagemSrc}" 
                      alt="${p.produtoNome || "Produto"}" 
                      class="rounded"
                      style="height: 80px; object-fit: cover;"
                      onerror="this.src='../assets/images/placeholder-image.svg'"
                    />
                    <div class="flex-grow-1">
                      <p class="mb-1 fw-medium">${
                        p.produtoNome || "Produto"
                      }</p>
                      <p class="text-muted small mb-0">${p.quantidade || 1}x</p>
                    </div>
                  </div>
                </div>
                
                ${
                  p.endereco
                    ? `
                  <div class="d-flex border-top pt-2 justify-content-between align-items-center">
                    <div>
                      <p class="text-muted small mb-1">Endereço de entrega</p>
                      <p class="mb-0 small">${p.endereco}</p>
                    </div>
                    <div class="text-end pt-3">
                      <span class="text-muted small">Total:</span>
                      <span class="fw-bold fs-5 ms-2">${valor}</span>
                    </div>
                  </div>
                `
                    : ""
                }
              </div>
            </div>
          `;
        })
        .join("");
    })
    .catch(() => {
      pedidosContainer.innerHTML = `
        <div class="alert alert-danger rounded-4">
          Erro ao carregar pedidos. Tente novamente mais tarde.
        </div>
      `;
    });

  // Evento de logout para o botão sair
  const btnSair = document.getElementById("btn-sair");
  if (btnSair) {
    btnSair.addEventListener("click", async (e) => {
      e.preventDefault();
      try {
        const res = await fetch("/api/usuario/logout");
        const json = await res.json();
        if (json.success) {
          localStorage.clear();
          sessionStorage.clear();
          window.location.href = "/inicio";
        } else {
          alert("Erro ao sair. Tente novamente.");
        }
      } catch {
        alert("Erro ao sair. Tente novamente.");
      }
    });
  }

  // Evento para o botão Editar perfil
  const btnEditar = document.getElementById("btn-editar");
  if (btnEditar) {
    btnEditar.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href = "/conta";
    });
  }
});
