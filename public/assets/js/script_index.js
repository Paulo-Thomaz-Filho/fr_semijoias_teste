// =============================================================================
// SCRIPTS DE CATÁLOGO E MODAL
// =============================================================================

// -----------------------------------------------------------------------------
// MODAL QUEM SOMOS
// -----------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
  const quemSomosLink = document.getElementById("quem-somos-link");
  if (quemSomosLink) {
    quemSomosLink.addEventListener("click", (e) => {
      e.preventDefault();
      const modal = new bootstrap.Modal(
        document.getElementById("quemSomosModal")
      );
      modal.show();
    });
  }
});

// -----------------------------------------------------------------------------
// UTILITÁRIOS DE USUÁRIO
// -----------------------------------------------------------------------------
const exibirSaudacaoUsuario = () => {
  try {
    const usuario = JSON.parse(sessionStorage.getItem("usuario"));
    if (usuario && usuario.nome) {
      const primeiroNome = usuario.nome.split(" ")[0];
      const greetingEl = document.getElementById("user-greeting-text");
      if (greetingEl) greetingEl.textContent = `Olá, ${primeiroNome}`;
    }
  } catch (e) {}
};
// ----------- VARIÁVEIS GLOBAIS -----------
let globalProductDatabase = [];
let categoriaAtual = "inicio";

// -----------------------------------------------------------------------------
// ATUALIZA CONTADOR DO CARRINHO NA NAVBAR
// -----------------------------------------------------------------------------
const atualizarContadorCarrinho = () => {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  let totalItems = 0;
  cart.forEach((item) => {
    totalItems += item.quantity;
  });
  const cartCounterEl = document.getElementById("cart-counter");
  if (cartCounterEl) cartCounterEl.textContent = totalItems;
  const cartCounterDesktopEl = document.getElementById("cart-counter-desktop");
  if (cartCounterDesktopEl) cartCounterDesktopEl.textContent = totalItems;
};

// -----------------------------------------------------------------------------
// RENDERIZAÇÃO DE CARDS DO CATÁLOGO
// -----------------------------------------------------------------------------
const criarCardCatalogoHtml = (product) => {
  let precoOriginal = parseFloat(product.preco);
  let precoFinal = precoOriginal;

  // Aplica desconto se houver promoção ativa
  if (product.promocao && product.promocao.valor) {
    if (product.promocao.tipo === "percent") {
      precoFinal = precoOriginal * (1 - product.promocao.valor / 100);
    } else if (product.promocao.tipo === "currency") {
      precoFinal = precoOriginal - parseFloat(product.promocao.valor);
    }
    if (precoFinal < 0) precoFinal = 0;
  }

  let precoFormatado = precoFinal.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });

  // No card, mostrar apenas o valor atual (com promoção, se houver)
  let precoHtml = `<span class="fs-5 fw-bold text-dark">${precoFormatado}</span>`;

  const imagemSrc = "assets/images/" + product.caminhoImagem;

  // Promo badge logic
  let promoBadgeHtml = "";
  if (product.promocao && product.promocao.valor) {
    let valor = product.promocao.valor;
    let tipo = product.promocao.tipo;
    let badgeText = "";
    if (tipo === "percent") {
      badgeText = `${valor}% OFF`;
    } else if (tipo === "currency") {
      badgeText = `R$ ${parseInt(valor)} OFF`;
    }
    promoBadgeHtml = `<span class="promo-badge">${badgeText}</span>`;
  }

  return `
    <div class="col-lg-3 col-md-4 col-6 mb-4 d-flex align-items-stretch">
      <div class="w-100 d-flex flex-column p-3 rounded-4 bg-white border border-1">
        <div class="position-relative overflow-hidden mb-3" style="padding-top: 100%;">
          <img src="${imagemSrc}" alt="${product.nome}" class="position-absolute rounded-3 top-0 start-0 w-100 h-100 object-fit-cover">
          ${promoBadgeHtml}
        </div>
        <div class="d-flex flex-column flex-grow-1">
          <h5 class="product-card-title fs-6 fw-semibold mb-2">${product.nome}</h5>
          <p class="product-card-price mb-2">${precoHtml}</p>
          <button class="btn btn-outline-dark rounded-4 fw-medium py-2 w-100 border-2 mt-2 text-nowrap d-flex align-items-center justify-content-center"
            style="padding-left:1rem;padding-right:1rem;white-space:nowrap;min-height:40px;"
            data-bs-toggle="modal"
            data-bs-target="#productModal"
            data-product-id="${product.idProduto}">
            Ver Detalhes
          </button>
        </div>
      </div>
    </div>
  `;
};
// ----------- CLASSE MODAL PRODUTO -----------
class CatalogPage {
  constructor(modalSelector, buttonSelector, database) {
    this.modal = document.querySelector(modalSelector);
    this.buttonSelector = buttonSelector;
    this.database = database;
    this.modalTitle = this.modal.querySelector("#modal-product-title");
    this.modalImage = this.modal.querySelector("#modal-product-image");
    this.modalDescription = this.modal.querySelector(
      "#modal-product-description"
    );
    this.modalPrice = this.modal.querySelector("#modal-product-price");
    this.modalMaterial = this.modal.querySelector("#modal-product-material");
    this.modalBtnAddToCart = this.modal.querySelector(".btn-add-to-cart");
    this.currentProduct = null;
    this.modalQtyInput = this.modal.querySelector("#modal-quantity");
    this.modalBtnIncrease = this.modal.querySelector(".modal-btn-increase");
    this.modalBtnDecrease = this.modal.querySelector(".modal-btn-decrease");
    this.initEvents();
  }

  initEvents() {
    // Evento "Ver Detalhes" (funciona para clique em qualquer parte do botão)
    document.addEventListener("click", (event) => {
      const button = event.target.closest(this.buttonSelector);
      if (button) {
        const productId = button.getAttribute("data-product-id");
        const product = this.findProductById(productId);
        if (product) {
          this.populateModal(product);
        } else {
        }
      }
    });

    // Evento "Adicionar ao Carrinho"
    if (this.modalBtnAddToCart) {
      this.modalBtnAddToCart.addEventListener("click", () => {
        if (this.currentProduct) {
          this.addToCart(this.currentProduct);
        }
      });
    }

    // Evento "Comprar Agora"
    const btnComprarAgora = this.modal.querySelector(".btn-dark");
    if (btnComprarAgora) {
      btnComprarAgora.addEventListener("click", () => {
        if (this.currentProduct) {
          this.addToCart(this.currentProduct, true);
        }
      });
    }

    // Eventos de quantidade +/- e blur
    if (this.modalBtnIncrease) {
      this.modalBtnIncrease.addEventListener("click", () => {
        let qty = parseInt(this.modalQtyInput.value);
        qty++;
        this.modalQtyInput.value = qty;
      });
    }
    if (this.modalBtnDecrease) {
      this.modalBtnDecrease.addEventListener("click", () => {
        let qty = parseInt(this.modalQtyInput.value);
        if (qty > 1) {
          qty--;
          this.modalQtyInput.value = qty;
        }
      });
    }
    if (this.modalQtyInput) {
      this.modalQtyInput.addEventListener("blur", () => {
        try {
          let qty = parseInt(this.modalQtyInput.value);
          if (isNaN(qty) || qty < 1) {
            this.modalQtyInput.value = 1;
          } else {
            this.modalQtyInput.value = qty;
          }
        } catch (e) {
          this.modalQtyInput.value = 1;
        }
      });
    }
  }

  // adicionar ao carrinho
  addToCart(product, goToCart = false) {
    const quantityToAdd = parseInt(this.modalQtyInput.value);

    if (isNaN(quantityToAdd) || quantityToAdd <= 0) {
      Swal.fire({
        title: "Quantidade Inválida",
        text: "Por favor, insira um número maior que zero.",
        icon: "error",
      });
      if (this.modalQtyInput) this.modalQtyInput.value = 1;
      return;
    }

    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let existingItem = cart.find((item) => item.id == product.idProduto);

    if (existingItem) {
      existingItem.quantity += quantityToAdd;
    } else {
      cart.push({
        id: product.idProduto,
        nome: product.nome,
        preco: product.preco,
        caminhoImagem: product.caminhoImagem,
        quantity: quantityToAdd,
      });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    atualizarContadorCarrinho();
    var modalInstance = bootstrap.Modal.getInstance(this.modal);
    modalInstance.hide();

    if (goToCart) {
      window.location.href = "carrinho";
    } else {
      Swal.fire({
        title: "Adicionado!",
        text: `(x${quantityToAdd}) ${product.nome} foi adicionado ao carrinho.`,
        icon: "success",
        timer: 2000,
        showConfirmButton: false,
      });
    }
  }

  findProductById(id) {
    return this.database.find((product) => product.idProduto == id);
  }

  populateModal(product) {
    this.currentProduct = product;
    if (this.modalQtyInput) this.modalQtyInput.value = 1;

    let precoOriginal = parseFloat(product.preco);
    let precoFinal = precoOriginal;
    if (product.promocao && product.promocao.valor) {
      if (product.promocao.tipo === "percent") {
        precoFinal = precoOriginal * (1 - product.promocao.valor / 100);
      } else if (product.promocao.tipo === "currency") {
        precoFinal = precoOriginal - parseFloat(product.promocao.valor);
      }
      if (precoFinal < 0) precoFinal = 0;
    }
    let precoOriginalFormatado = precoOriginal.toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    });
    let precoFinalFormatado = precoFinal.toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    });

    if (this.modalTitle) this.modalTitle.textContent = product.nome;
    if (this.modalImage)
      this.modalImage.setAttribute(
        "src",
        "assets/images/" + product.caminhoImagem
      );
    if (this.modalDescription)
      this.modalDescription.textContent = product.descricao;
    if (this.modalMaterial) this.modalMaterial.textContent = product.marca;

    // Preço no modal: se houver promoção, mostra original riscado + promocional
    if (this.modalPrice) {
      if (precoFinal !== precoOriginal) {
        this.modalPrice.innerHTML = `<span class='text-muted text-decoration-line-through me-2 small'>${precoOriginalFormatado}</span><span class='text-dark fw-bold'>${precoFinalFormatado}</span>`;
      } else {
        this.modalPrice.textContent = precoOriginalFormatado;
      }
    }

    // Promo badge in modal
    var badgeEl = document.getElementById("modal-promo-badge");
    if (badgeEl) {
      if (product.promocao && product.promocao.valor) {
        let valor = product.promocao.valor;
        let tipo = product.promocao.tipo;
        let badgeText = "";
        if (tipo === "percent") {
          badgeText = `${valor}% OFF`;
        } else if (tipo === "currency") {
          badgeText = `R$ ${parseInt(valor)} OFF`;
        }
        badgeEl.className = "promo-badge";
        badgeEl.textContent = badgeText;
        badgeEl.style.display = "inline-block";
      } else {
        badgeEl.style.display = "none";
        badgeEl.textContent = "";
      }
    }
  }
}

// -----------------------------------------------------------------------------
// CATEGORIAS
// -----------------------------------------------------------------------------
const carregarCategorias = () => {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "produtos/categorias", true);
  xhr.responseType = "json";
  xhr.onload = function () {
    if (xhr.status === 200) {
      var categorias = xhr.response;
      var menu = document.getElementById("categorias-menu");
      if (menu) {
        categorias.forEach(function (categoria, index) {
          var link = document.createElement("a");
          link.href = "#";
          link.className =
            "text-decoration-none text-dark fw-semibold categoria-link px-3 py-2 fs-6 d-inline-block position-relative";
          link.setAttribute("data-categoria", categoria);
          link.textContent = categoria;
          menu.appendChild(link);
          if (index < categorias.length - 1) {
            var sep = document.createElement("span");
            sep.className = "text-muted";
            menu.appendChild(sep);
          }
        });
        var categoriaLinks = document.querySelectorAll(".categoria-link");
        categoriaLinks.forEach(function (link) {
          link.addEventListener("click", function (e) {
            e.preventDefault();
            var categoria = link.getAttribute("data-categoria");
            categoriaLinks.forEach(function (l) {
              l.classList.remove("active");
            });
            link.classList.add("active");
            categoriaAtual = categoria;
            renderizarPorCategoria(categoria);
          });
        });
      }
    } else {
      // ...
    }
  };
  xhr.onerror = function () {
    // ...
  };
  xhr.send();
};

// -----------------------------------------------------------------------------
// FILTRO DE CATEGORIA
// -----------------------------------------------------------------------------
const renderizarPorCategoria = (categoria) => {
  if (categoria === "inicio") {
    // Mostrar catálogo completo
    renderizarCatalogo(globalProductDatabase, "Catálogo Completo");
  } else {
    // Filtrar por categoria
    const produtosFiltrados = globalProductDatabase.filter(
      (p) => p.categoria === categoria
    );
    renderizarCatalogo(produtosFiltrados, categoria);
  }
};

// -----------------------------------------------------------------------------
// RENDERIZAÇÃO DE CATÁLOGO
// -----------------------------------------------------------------------------
const renderizarCatalogo = (produtos, titulo = "Catálogo Completo") => {
  var catalogoWrapper = document.getElementById("catalogo-completo-wrapper");
  var tituloH2 = document.querySelector(
    "#secao-catalogo .container .text-center h2"
  );
  if (catalogoWrapper) catalogoWrapper.innerHTML = "";
  if (tituloH2) tituloH2.textContent = titulo;
  if (produtos.length > 0) {
    produtos.forEach(function (product) {
      if (catalogoWrapper)
        catalogoWrapper.insertAdjacentHTML(
          "beforeend",
          criarCardCatalogoHtml(product)
        );
    });
  } else {
    if (catalogoWrapper)
      catalogoWrapper.innerHTML = `<div class="col-12"><p class="text-center fs-5">Nenhum produto encontrado.</p></div>`;
  }
};

// Lógica AJAX para carregar produtos

// -----------------------------------------------------------------------------
// INICIALIZAÇÃO PRINCIPAL
// -----------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
  // Inicialização do catálogo
  exibirSaudacaoUsuario();
  atualizarContadorCarrinho();

  // Redirecionamento dos links de saudação (mobile e desktop)
  const handleUserGreetingClick = (usuario) => {
    if (usuario && usuario.nivel == 1) {
      window.location.href = "/dashboard";
    } else if (usuario) {
      window.location.href = "/conta";
    } else {
      window.location.href = "/login";
    }
  };
  ["user-greeting", "user-greeting-desktop"].forEach((id) => {
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
  // Lógica AJAX para carregar produtos
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "produtos", true);
  xhr.responseType = "json";
  xhr.onload = function () {
    if (xhr.status === 200) {
      var productDatabase = xhr.response;
      globalProductDatabase = productDatabase;
      renderizarCatalogo(productDatabase, "Catálogo Completo");
      new Swiper(".banner-carousel", {
        slidesPerView: 1,
        spaceBetween: 0,
        loop: true,
        autoplay: {
          delay: 4000,
          disableOnInteraction: false,
        },
        speed: 800,
        effect: "slide",
        navigation: {
          nextEl: ".banner-carousel .swiper-button-next",
          prevEl: ".banner-carousel .swiper-button-prev",
        },
        pagination: {
          el: ".banner-carousel .swiper-pagination",
          clickable: true,
        },
      });
      const catalogPage = new CatalogPage(
        "#productModal",
        '[data-bs-toggle="modal"]',
        productDatabase
      );
      carregarCategorias();
      var forms = document.querySelectorAll("form");
      forms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
          event.preventDefault();
          var searchInput = form.querySelector('input[type="search"]');
          var termoBuscaBruto = searchInput ? searchInput.value : "";
          var termoBusca = termoBuscaBruto.trim().toLowerCase();
          if (termoBusca.length === 0) {
            renderizarPorCategoria(categoriaAtual);
            return;
          }
          let resultados = globalProductDatabase;
          if (categoriaAtual !== "inicio") {
            resultados = resultados.filter(
              (p) => p.categoria === categoriaAtual
            );
          }
          resultados = resultados.filter((product) =>
            product.nome.toLowerCase().includes(termoBusca)
          );
          renderizarCatalogo(
            resultados,
            `Resultados da Busca: "${termoBuscaBruto}"`
          );
        });
      });
    } else {
      // ...
      var catalogoWrapper = document.getElementById(
        "catalogo-completo-wrapper"
      );
      if (catalogoWrapper) {
        catalogoWrapper.innerHTML =
          "<div class='d-flex justify-content-center align-items-center w-100' style='min-height:300px;'><p class='text-center text-danger fs-5'>Não foi possível carregar os produtos.</p></div>";
      }
    }
  };
  xhr.onerror = function () {
    // ...
    var catalogoWrapper = document.getElementById("catalogo-completo-wrapper");
    if (catalogoWrapper) {
      catalogoWrapper.innerHTML =
        "<div class='d-flex justify-content-center align-items-center w-100' style='min-height:300px;'><p class='text-center text-danger fs-5'>Não foi possível carregar os produtos.</p></div>";
    }
  };
  xhr.send();

  // ----------- EVENTO DE CLIQUE NAS CATEGORIAS -----------
  document.querySelectorAll(".categoria-link").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      document
        .querySelectorAll(".categoria-link")
        .forEach((l) => l.classList.remove("active"));
      this.classList.add("active");
    });
  });
});
