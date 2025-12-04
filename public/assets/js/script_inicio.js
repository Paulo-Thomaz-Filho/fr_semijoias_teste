// =====================================
// INÍCIO - SCRIPTS DE CATÁLOGO E MODAL
// =====================================

// ----------- UTILITÁRIOS DE USUÁRIO -----------
function exibirSaudacaoUsuario() {
  try {
    const usuario = JSON.parse(sessionStorage.getItem("usuario"));
    if (usuario && usuario.nome) {
      const primeiroNome = usuario.nome.split(" ")[0];
      var greetingEl = document.getElementById("user-greeting-text");
      if (greetingEl) greetingEl.textContent = `Olá, ${primeiroNome}`;
    }
  } catch (e) {}
}
// ----------- VARIÁVEIS GLOBAIS -----------
let globalProductDatabase = [];
let categoriaAtual = "inicio";

// ----------- CARRINHO -----------
function atualizarContadorCarrinho() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  let totalItems = 0;
  cart.forEach((item) => {
    totalItems += item.quantity;
  });
  var cartCounterEl = document.getElementById("cart-counter");
  if (cartCounterEl) cartCounterEl.textContent = totalItems;
}

// ----------- RENDERIZAÇÃO DE CARDS -----------
function criarCardCatalogoHtml(product) {
  let precoFormatado = product.preco;
  if (!isNaN(product.preco)) {
    precoFormatado = parseFloat(product.preco).toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    });
  }
  const imagemSrc = "assets/images/" + product.caminhoImagem;
  return `
    <div class="col-lg-3 col-md-4 col-6 mb-4 d-flex align-items-stretch">
      <div class="w-100 d-flex flex-column rounded-4 bg-white border border-1">
        <div class="position-relative overflow-hidden" style="padding-top: 100%;">
          <img src="${imagemSrc}" alt="${product.nome}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
        </div>
        <div class="p-3 d-flex flex-column flex-grow-1">
          <h5 class="product-card-title fs-6 fw-semibold mb-2">${product.nome}</h5>
          <p class="product-card-price fs-5 fw-bold text-dark">${precoFormatado}</p>
          <div class="mt-auto pt-2">
            <button class="btn btn-outline-dark rounded-4 fw-medium px-5 py-2 w-100 border-2"
              data-bs-toggle="modal"
              data-bs-target="#productModal"
              data-product-id="${product.idProduto}">
              Ver Detalhes
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
}
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
          alert("Erro: Produto não encontrado.");
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
        imagem: product.caminhoImagem,
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

    let precoFormatado = product.preco;
    if (!isNaN(product.preco)) {
      precoFormatado = parseFloat(product.preco).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
      });
    }
    if (this.modalTitle) this.modalTitle.textContent = product.nome;
    if (this.modalImage)
      this.modalImage.setAttribute(
        "src",
        "assets/images/" + product.caminhoImagem
      );
    if (this.modalDescription)
      this.modalDescription.textContent = product.descricao;
    if (this.modalPrice) this.modalPrice.textContent = precoFormatado;
    if (this.modalMaterial) this.modalMaterial.textContent = product.marca;
  }
}

// ----------- CATEGORIAS -----------
function carregarCategorias() {
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
      console.error("Erro ao carregar categorias:", xhr.status);
    }
  };
  xhr.onerror = function () {
    console.error("Erro ao carregar categorias:", xhr.status);
  };
  xhr.send();
}

// ----------- FILTRO DE CATEGORIA -----------
function renderizarPorCategoria(categoria) {
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
}

// ----------- RENDERIZAÇÃO DE CATÁLOGO -----------
function renderizarCatalogo(produtos, titulo = "Catálogo Completo") {
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
}

// Lógica AJAX para carregar produtos

// ----------- INICIALIZAÇÃO PRINCIPAL -----------
document.addEventListener("DOMContentLoaded", function () {
  // Inicialização do catálogo
  exibirSaudacaoUsuario();
  atualizarContadorCarrinho();
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
      var swiperWrapper = document.querySelector(".swiper-wrapper");
      if (swiperWrapper) {
        swiperWrapper.innerHTML =
          "<p class='text-center text-danger p-5'>Não foi possível carregar os produtos.</p>";
      }
      console.error("Erro fatal ao buscar produtos: ", xhr.status);
    }
  };
  xhr.onerror = function () {
    var swiperWrapper = document.querySelector(".swiper-wrapper");
    if (swiperWrapper) {
      swiperWrapper.innerHTML =
        "<p class='text-center text-danger p-5'>Não foi possível carregar os produtos.</p>";
    }
    console.error("Erro fatal ao buscar produtos: ", xhr.status);
  };
  xhr.send();
});
