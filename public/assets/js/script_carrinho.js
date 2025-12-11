// =============================================================================
// SCRIPTS DO CARRINHO
// =============================================================================

// -----------------------------------------------------------------------------
// UTILITÁRIOS DE USUÁRIO
// -----------------------------------------------------------------------------
const exibirSaudacaoUsuario = () => {
  try {
    const usuario = JSON.parse(sessionStorage.getItem("usuario"));
    if (usuario && usuario.nome) {
      const primeiroNome = usuario.nome.split(" ")[0];
      const el = document.getElementById("user-greeting-text");
      if (el) el.textContent = `Olá, ${primeiroNome}`;
    }
  } catch (e) {}
};

// -----------------------------------------------------------------------------
// FORMATAÇÃO DE MOEDA
// -----------------------------------------------------------------------------
const formatarMoeda = (valor) => {
  const numero = parseFloat(valor);
  if (isNaN(numero)) return "R$ 0,00";
  return numero.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });
};

// -----------------------------------------------------------------------------
// ATUALIZA CONTADOR DO CARRINHO NA NAVBAR
// -----------------------------------------------------------------------------
const updateCartCounter = () => {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  let totalItems = 0;
  cart.forEach((item) => { totalItems += item.quantity; });
  const el1 = document.getElementById("cart-counter");
  const el2 = document.getElementById("cart-counter-desktop");
  if (el1) el1.textContent = totalItems;
  if (el2) el2.textContent = totalItems;
};

// ----------- INICIALIZAÇÃO PRINCIPAL -----------
// -----------------------------------------------------------------------------
// INICIALIZAÇÃO PRINCIPAL
// -----------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", async () => {
  // ---------------------------------------------------------------------------
  // CARREGA ENDEREÇO DO USUÁRIO (PADRÃO NOVO)
  // ---------------------------------------------------------------------------
  const usuario = JSON.parse(sessionStorage.getItem("usuario"));
  const addressEl = document.getElementById("user-address");
  const boxEl = document.getElementById("user-address-box");
  if (usuario && usuario.idUsuario && addressEl && boxEl) {
    try {
      const res = await fetch(`/usuario/buscar?id=${usuario.idUsuario}`);
      const data = await res.json();
      if (data && data.endereco) {
        addressEl.textContent = data.endereco;
        boxEl.style.display = "";
      } else {
        addressEl.textContent = "Endereço não cadastrado.";
        boxEl.style.display = "";
      }
    } catch {
      addressEl.textContent = "Erro ao buscar endereço.";
      boxEl.style.display = "";
    }
  }
  // Redirecionamento do link de saudação (igual ao index)
  // ---------------------------------------------------------------------------
  // REDIRECIONAMENTO DE SAUDAÇÃO
  // ---------------------------------------------------------------------------
  const handleUserGreetingClick = (usuario) => {
    if (usuario && usuario.nivel == 1) {
      window.location.href = "/dashboard";
    } else if (usuario) {
      window.location.href = "/conta";
    } else {
      window.location.href = "/login";
    }
  };
  ["user-greeting", "user-greeting-desktop"].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener("click", (e) => {
        e.preventDefault();
        try {
          const usuario = JSON.parse(sessionStorage.getItem("usuario"));
          handleUserGreetingClick(usuario);
        } catch {
          window.location.href = "/login";
        }
      });
    }
  });

  exibirSaudacaoUsuario();

  // ----------- CARRINHO -----------
  // updateCartCounter já está no topo como função utilitária

  // ----------- FORMATAÇÃO DE MOEDA -----------
  // formatarMoeda já está no topo como função utilitária

  // ----------- RENDERIZAÇÃO DE ITEM DO CARRINHO -----------
  // ---------------------------------------------------------------------------
  // RENDERIZAÇÃO DE ITEM DO CARRINHO
  // ---------------------------------------------------------------------------
  const criarItemHtml = (item) => {
    return `
            <div class="card mb-3 cart-item rounded-4 border border-1" data-price="${
              item.preco
            }" data-id="${item.id}"> 
                <div class="card-body p-3">
                    <!-- Layout Mobile -->
                    <div class="d-md-none">
                        <div class="d-flex gap-3 mb-3 align-items-center">
                            <img src="../assets/images/${
                              item.caminhoImagem &&
                              item.caminhoImagem !== "undefined" &&
                              item.caminhoImagem !== ""
                                ? item.caminhoImagem
                                : "placeholder-image.svg"
                            }" class="rounded" style="width: 100px; height: 100px; object-fit: cover;" alt="${item.nome}">
                            <div class="flex-grow-1">
                                <h6 class="fw-semibold mb-2">${item.nome}</h6>
                                <p class="text-muted mb-2 small">${formatarMoeda(
                                  item.preco
                                )}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="input-group" style="max-width: 120px;">
                                <button class="btn btn-outline-secondary btn-decrease rounded-start-pill" type="button">−</button>
                                <input type="number" class="form-control text-center quantity" value="${
                                  item.quantity
                                }" min="1">
                                <button class="btn btn-outline-secondary btn-increase rounded-end-pill" type="button">+</button>
                            </div>
                            <p class="mb-0 fw-bold">Total: <span class="item-total">${formatarMoeda(
                              item.preco * item.quantity
                            )}</span></p>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-link text-danger text-decoration-none p-0 remove-item d-flex align-items-center justify-content-center mx-auto" type="button" title="Remover">
                              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M18 6L17.1991 18.0129C17.129 19.065 17.0939 19.5911 16.8667 19.99C16.6666 20.3412 16.3648 20.6235 16.0011 20.7998C15.588 21 15.0607 21 14.0062 21H9.99377C8.93927 21 8.41202 21 7.99889 20.7998C7.63517 20.6235 7.33339 20.3412 7.13332 19.99C6.90607 19.5911 6.871 19.065 6.80086 18.0129L6 6M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M14 10V17M10 10V17" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                              </svg>
                              <span class="ms-1 fw-medium">Remover</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Layout Desktop -->
                    <div class="d-none d-md-block">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-2 text-center">
                                <img src="../assets/images/${
                                  item.caminhoImagem &&
                                  item.caminhoImagem !== "undefined" &&
                                  item.caminhoImagem !== ""
                                    ? item.caminhoImagem
                                    : "placeholder-image.svg"
                                }" class="img-fluid rounded" alt="${item.nome}">
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-2">${item.nome}</h6>
                                <p class="text-muted mb-3 small">${formatarMoeda(
                                  item.preco
                                )}</p>
                                <div class="input-group" style="max-width: 120px;">
                                    <button class="btn btn-outline-secondary btn-decrease rounded-start-pill" type="button">−</button>
                                    <input type="number" class="form-control text-center quantity" value="${
                                      item.quantity
                                    }" min="1">
                                    <button class="btn btn-outline-secondary btn-increase rounded-end-pill" type="button">+</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                              <div class="d-flex flex-column align-items-end">
                                <p class="mb-2">Total: <strong class="item-total">${formatarMoeda(
                                  item.preco * item.quantity
                                )}</strong></p>
                                <button class="btn btn-link text-danger text-decoration-none p-0 remove-item d-flex align-items-center justify-content-end" type="button" title="Remover">
                                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M18 6L17.1991 18.0129C17.129 19.065 17.0939 19.5911 16.8667 19.99C16.6666 20.3412 16.3648 20.6235 16.0011 20.7998C15.588 21 15.0607 21 14.0062 21H9.99377C8.93927 21 8.41202 21 7.99889 20.7998C7.63517 20.6235 7.33339 20.3412 7.13332 19.99C6.90607 19.5911 6.871 19.065 6.80086 18.0129L6 6M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M14 10V17M10 10V17" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                  </svg>
                                  <span class="ms-1 fw-medium">Remover</span>
                                </button>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
  }

  // ----------- FUNÇÕES DO CARRINHO -----------

  // Uma função central para salvar mudanças de quantidade no localStorage
  // ---------------------------------------------------------------------------
  // ATUALIZA QUANTIDADE NO LOCALSTORAGE
  // ---------------------------------------------------------------------------
  const updateCartInLocalStorage = (itemId, newQuantity) => {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    // Encontra o item no array do carrinho
    let itemToUpdate = cart.find((item) => item.id == itemId);

    if (itemToUpdate) {
      itemToUpdate.quantity = newQuantity; // Atualiza a quantidade
      localStorage.setItem("cart", JSON.stringify(cart)); // Salva o array inteiro de volta
    }
    // Atualiza o contador da navbar também
    updateCartCounter();
  }

  // ----------- ATUALIZAÇÃO DE TOTAL DE ITEM -----------
  // ---------------------------------------------------------------------------
  // ATUALIZA TOTAL DE UM ITEM
  // ---------------------------------------------------------------------------
const atualizarTotalItem = (itemElement) => {
    const precoBase = parseFloat(itemElement.getAttribute("data-price"));
    
    // Pega o valor de qualquer um dos inputs (já que vamos sincronizá-los)
    const inputReferencia = itemElement.querySelector(".quantity");
    const quantidade = inputReferencia ? parseInt(inputReferencia.value) : 1;
    
    const totalItem = precoBase * quantidade;

    // CORREÇÃO: Seleciona TODOS os elementos de total (mobile e desktop) e atualiza
    const totalElements = itemElement.querySelectorAll(".item-total");
    totalElements.forEach((el) => {
        el.textContent = formatarMoeda(totalItem);
    });

    atualizarResumoCarrinho();
}

  // ----------- ATUALIZAÇÃO DE RESUMO DO CARRINHO -----------
  // ---------------------------------------------------------------------------ev
  // ATUALIZA RESUMO DO CARRINHO (SUBTOTAL, FRETE, TOTAL)
  // ---------------------------------------------------------------------------
  const atualizarResumoCarrinho = () => {
    let subtotal = 0;
    let frete = 0;

    // obter desconto se houver
    const descontoElem = document.getElementById("desconto");
    let desconto = descontoElem
      ? parseFloat(descontoElem.getAttribute("data-discount")) || 0
      : 0;

    const cartItems = document.querySelectorAll(".cart-item");
    cartItems.forEach(function (item) {
      const precoBase = parseFloat(item.getAttribute("data-price"));
      const quantidade = parseInt(item.querySelector(".quantity").value);
      subtotal += precoBase * quantidade;
    });

    // aplicar desconto se houver
    subtotal -= desconto;

    // Frete por faixa de valor
    if (subtotal > 0 && subtotal <= 100) {
      frete = 15;
    } else if (subtotal > 100 && subtotal <= 300) {
      frete = 18;
    } else if (subtotal > 300) {
      frete = 20;
    } else {
      frete = 0;
    }

    const total = subtotal + frete;

    const subtotalElem = document.getElementById("subtotal");
    const freteElem = document.getElementById("frete");
    const totalElem = document.getElementById("total");
    if (subtotalElem) subtotalElem.textContent = formatarMoeda(subtotal);
    if (freteElem) freteElem.textContent = formatarMoeda(frete);
    if (totalElem) totalElem.textContent = formatarMoeda(total);

    // Atualiza o contador da navbar
    updateCartCounter();
  }

  // ----------- CARREGAR CARRINHO -----------
  // ---------------------------------------------------------------------------
  // CARREGA ITENS DO CARRINHO NA TELA
  // ---------------------------------------------------------------------------
  const carregarCarrinho = () => {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const container = document.getElementById("cart-items");
    if (!container) return;

    container.innerHTML = "";

    if (cart.length === 0) {
      container.innerHTML =
        '<div class="alert alert-info">Seu carrinho está vazio.</div>';
    } else {
      cart.forEach((item) => {
        const itemHtml = criarItemHtml(item);
        // itemHtml é uma string, então precisamos converter para elemento DOM
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = itemHtml.trim();
        // Adiciona o primeiro filho (o .cart-item) ao container
        container.appendChild(tempDiv.firstElementChild);
      });
    }

    atualizarResumoCarrinho();
  }

  // EVENTOS DO CARRINHO (DOM PURO)
  const cartItemsContainer = document.getElementById("cart-items");
  if (cartItemsContainer) {
    cartItemsContainer.addEventListener("click", (event) => {
      const target = event.target;
      // AUMENTAR QUANTIDADE
      if (target.classList.contains("btn-increase")) {
        var item = target.closest(".cart-item");
        
        // 1. Pega o input que está DO LADO do botão clicado para saber o valor atual
        var inputAtual = target.parentElement.querySelector(".quantity");
        var quantidade = parseInt(inputAtual.value);
        quantidade++;

        // 2. Seleciona TODOS os inputs (mobile e desktop) e atualiza todos eles
        var todosInputs = item.querySelectorAll(".quantity");
        todosInputs.forEach(function(input) {
            input.value = quantidade;
        });

        atualizarTotalItem(item);
        var itemId = item.getAttribute("data-id");
        updateCartInLocalStorage(itemId, quantidade);
      }

      // DIMINUIR QUANTIDADE
      if (target.classList.contains("btn-decrease")) {
        var item = target.closest(".cart-item");
        
        // 1. Pega o input vizinho ao botão
        var inputAtual = target.parentElement.querySelector(".quantity");
        var quantidade = parseInt(inputAtual.value);

        if (quantidade > 1) {
          quantidade--;
          
          // 2. Atualiza TODOS os inputs (mobile e desktop)
          var todosInputs = item.querySelectorAll(".quantity");
          todosInputs.forEach(function(input) {
              input.value = quantidade;
          });

          atualizarTotalItem(item);
          var itemId = item.getAttribute("data-id");
          updateCartInLocalStorage(itemId, quantidade);
        }
      }
      // Remover item (botão ou SVG interno)
      var removeBtn = target.closest(".remove-item");
      if (removeBtn) {
        var item = removeBtn.closest(".cart-item");
        var itemId = item.getAttribute("data-id");
        Swal.fire({
          title: "Tem certeza?",
          text: "Você não poderá reverter isso!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#198754",
          cancelButtonColor: "#d33",
          confirmButtonText: "Sim, remover",
          cancelButtonText: "Cancelar",
        }).then(function (result) {
          if (result.isConfirmed) {
            let cart = JSON.parse(localStorage.getItem("cart")) || [];
            cart = cart.filter(function (item) {
              return item.id != itemId;
            });
            localStorage.setItem("cart", JSON.stringify(cart));
            carregarCarrinho();
            Swal.fire({
              title: "Removido!",
              text: "O item foi removido do seu carrinho.",
              icon: "success",
            });
          }
        });
      }
    });
    // Validando a digitação
    cartItemsContainer.addEventListener(
      "blur",
      (event) => {
        const target = event.target;
        if (target.classList.contains("quantity")) {
          var item = target.closest(".cart-item");
          var itemId = item.getAttribute("data-id");
          var qty = 1;
          try {
            qty = parseInt(target.value);
            if (isNaN(qty) || qty < 1) {
              qty = 1;
            }
          } catch (e) {
            qty = 1;
          }
          target.value = qty;
          atualizarTotalItem(item);
          updateCartInLocalStorage(itemId, qty);
        }
      },
      true
    );
  }

  carregarCarrinho();

  // ----------- FINALIZAR COMPRA -----------
  // ---------------------------------------------------------------------------
  // FINALIZAR COMPRA
  // ---------------------------------------------------------------------------
  const btnFinalizar = document.getElementById("btn-finalizar-compra");
  if (btnFinalizar) {
    btnFinalizar.addEventListener("click", () => {
    // Coleta produtos do carrinho
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    if (cart.length === 0) {
      Swal.fire({
        icon: "warning",
        title: "Carrinho vazio",
        text: "Adicione produtos ao carrinho antes de finalizar a compra.",
      });
      return;
    }


    // Coleta cliente logado do sessionStorage
    const usuario = JSON.parse(sessionStorage.getItem("usuario"));
    if (!usuario || !usuario.idUsuario) {
      Swal.fire({
        icon: "warning",
        title: "Usuário não identificado",
        text: "Faça login para finalizar a compra.",
      });
      return;
    }

    // Buscar dados completos do usuário pelo id
    fetch(`/usuario/buscar?id=${usuario.idUsuario}`)
      .then((res) => res.json())
      .then((userData) => {
        if (!userData || !userData.endereco) {
          Swal.fire({
            icon: "error",
            title: "Endereço não encontrado",
            text: "Não foi possível obter o endereço do usuário. Atualize seus dados na conta.",
          });
          return;
        }

        // Monta payload para o backend
        const produtos = cart.map((item) => ({
          produto_nome: item.nome,
          preco: item.preco,
          quantidade: item.quantity,
        }));
        const formData = new FormData();
        formData.append("id_cliente", usuario.idUsuario);
        formData.append("produtos", JSON.stringify(produtos));
        formData.append("endereco", userData.endereco);
        // Adiciona status padrão para o pedido
        formData.append("status", "Pendente");

        fetch("/pedidos/salvar", {
          method: "POST",
          body: formData,
        })
          .then(async (response) => {
            let data;
            try {
              data = await response.json();
            } catch (e) {
              data = { erro: "Resposta inválida do servidor." };
            }
            if ((response.ok || response.status === 201) && data.sucesso && data.ids && Array.isArray(data.ids) && data.ids.length > 0) {
              // Pedido criado com sucesso, agora criar preferência de pagamento Mercado Pago
              const idPedido = data.ids[0];
              const items = cart.map((item) => ({
                title: item.nome,
                quantity: item.quantity,
                unit_price: parseFloat(item.preco),
              }));
              // Mostra loading
              Swal.fire({
                icon: "info",
                title: "Redirecionando para o pagamento",
                text: "Aguarde, você será direcionado ao Mercado Pago para finalizar sua compra.",
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
              });
              fetch("/api/pagamento/payment_preference.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ items: items, id_pedido: idPedido })
              })
                .then((res) => res.json())
                .then((mpData) => {
                  if (mpData && mpData.init_point) {
                    localStorage.removeItem("cart");
                    carregarCarrinho();
                    window.location.href = mpData.init_point;
                  } else {
                    Swal.fire({
                      icon: "error",
                      title: "Erro ao redirecionar",
                      text: mpData.error || "Não foi possível criar a preferência de pagamento.",
                    });
                  }
                })
                .catch(() => {
                  Swal.fire({
                    icon: "error",
                    title: "Erro ao redirecionar",
                    text: "Falha ao criar preferência de pagamento.",
                  });
                });
            } else if (data.sucesso) {
              Swal.fire({
                icon: "success",
                title: "Pedido realizado!",
                text: data.sucesso,
              });
              localStorage.removeItem("cart");
              carregarCarrinho();
            } else {
              Swal.fire({
                icon: "error",
                title: "Erro ao finalizar",
                text:
                  data.erro ||
                  "Ocorreu um erro ao finalizar a compra. Tente novamente.",
              });
            }
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: "Erro de conexão",
              text: "Não foi possível conectar ao servidor. Tente novamente.",
            });
          });
      })
      .catch(() => {
        Swal.fire({
          icon: "error",
          title: "Erro ao buscar endereço",
          text: "Não foi possível buscar o endereço do usuário. Tente novamente.",
        });
      });
  });
}
});