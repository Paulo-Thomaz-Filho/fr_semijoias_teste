// =====================================
// INÍCIO - SCRIPTS DO CARRINHO
// =====================================

// ----------- UTILITÁRIOS DE USUÁRIO -----------
function exibirSaudacaoUsuario() {
  try {
    const usuario = JSON.parse(sessionStorage.getItem("usuario"));
    if (usuario && usuario.nome) {
      const primeiroNome = usuario.nome.split(" ")[0];
      $("#user-greeting-text").text(`Olá, ${primeiroNome}`);
    }
  } catch (e) {}
}
// ----------- INICIALIZAÇÃO PRINCIPAL -----------
$(document).ready(function () {
  exibirSaudacaoUsuario();
  // ----------- CARRINHO -----------
  function updateCartCounter() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    let totalItems = 0;
    cart.forEach((item) => {
      totalItems += item.quantity;
    });
    $("#cart-counter").text(totalItems);
  }

  // ----------- FORMATAÇÃO DE MOEDA -----------
  function formatarMoeda(valor) {
    const numero = parseFloat(valor);
    if (isNaN(numero)) {
      return "R$ 0,00";
    }
    return numero.toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    });
  }

  // ----------- RENDERIZAÇÃO DE ITEM DO CARRINHO -----------
  function criarItemHtml(item) {
    return `
            <div class="card mb-3 cart-item rounded-4 border border-1" data-price="${
              item.preco
            }" data-id="${item.id}"> 
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-2 text-center">
                            <img src="assets/images/${
                              item.imagem
                            }" class="img-fluid rounded" alt="${item.nome}" style="max-height: 100px;">
                        </div>
                        <div class="col-12 col-md-6">
                            <h6 class="mb-1">${item.nome}</h6>
                            <div class="input-group" style="max-width: 120px;">
                                <button class="btn btn-outline-secondary btn-decrease rounded-start-pill" type="button">−</button>
                                <input type="number" class="form-control text-center quantity" value="${
                                  item.quantity
                                }" min="1">
                                <button class="btn btn-outline-secondary btn-increase rounded-end-pill" type="button">+</button>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                          <div class="d-flex flex-column align-items-end">
                            <p class="mb-1">Total: <strong class="item-total">${formatarMoeda(
                              item.preco * item.quantity
                            )}</strong></p>
                            <button class="btn btn-link text-danger text-decoration-none p-0 remove-item d-flex align-items-center justify-content-end" type="button" title="Remover">
                              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M18 6L17.1991 18.0129C17.129 19.065 17.0939 19.5911 16.8667 19.99C16.6666 20.3412 16.3648 20.6235 16.0011 20.7998C15.588 21 15.0607 21 14.0062 21H9.99377C8.93927 21 8.41202 21 7.99889 20.7998C7.63517 20.6235 7.33339 20.3412 7.13332 19.99C6.90607 19.5911 6.871 19.065 6.80086 18.0129L6 6M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M14 10V17M10 10V17" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                              </svg>
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
  function updateCartInLocalStorage(itemId, newQuantity) {
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
  function atualizarTotalItem(itemElement) {
    const precoBase = parseFloat(itemElement.getAttribute("data-price"));
    const quantidade = parseInt(itemElement.querySelector(".quantity").value);
    const totalItem = precoBase * quantidade;

    itemElement.querySelector(".item-total").textContent =
      formatarMoeda(totalItem);
    atualizarResumoCarrinho();
  }

  // ----------- ATUALIZAÇÃO DE RESUMO DO CARRINHO -----------
  function atualizarResumoCarrinho() {
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

    const valorFixoDoFrete = 9.25;
    if (subtotal > 0) {
      frete = valorFixoDoFrete;
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
  function carregarCarrinho() {
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
  var cartItemsContainer = document.getElementById("cart-items");
  if (cartItemsContainer) {
    cartItemsContainer.addEventListener("click", function (event) {
      var target = event.target;
      // Aumentar quantidade
      if (target.classList.contains("btn-increase")) {
        var item = target.closest(".cart-item");
        var quantityInput = item.querySelector(".quantity");
        var quantidade = parseInt(quantityInput.value);
        quantidade++;
        quantityInput.value = quantidade;
        atualizarTotalItem(item);
        var itemId = item.getAttribute("data-id");
        updateCartInLocalStorage(itemId, quantidade);
      }
      // Diminuir quantidade
      if (target.classList.contains("btn-decrease")) {
        var item = target.closest(".cart-item");
        var quantityInput = item.querySelector(".quantity");
        var quantidade = parseInt(quantityInput.value);
        if (quantidade > 1) {
          quantidade--;
          quantityInput.value = quantidade;
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
            item.remove();
            atualizarResumoCarrinho();
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
      function (event) {
        var target = event.target;
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
});
