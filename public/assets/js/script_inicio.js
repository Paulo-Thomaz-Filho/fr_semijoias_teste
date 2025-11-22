$(document).ready(function() {

    // fução atualizar carrinho
    function updateCartCounter() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        let totalItems = 0;
        cart.forEach(item => { totalItems += item.quantity; });
        $('#cart-counter').text(totalItems);
    }

    // criação do card em grid
    function criarCardCatalogoHtml(product) {
        let precoFormatado = product.preco;
        if (!isNaN(product.preco)) {
            precoFormatado = parseFloat(product.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }
        
        const imagemSrc = 'assets/images/' + product.caminho_imagem; 

        return `
            <div class="col-lg-3 col-md-4 col-6 mb-4 d-flex align-items-stretch">
                <div class="product-card-link w-100 d-flex flex-column" style="background: #fff; border: 1px solid #e9ecef; border-radius: 12px; overflow: hidden; transition: 0.3s;">
                    
                    <div style="position: relative; padding-top: 100%; overflow: hidden;">
                        <img src="${imagemSrc}" alt="${product.nome}" 
                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h5 class="product-card-title" style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">
                            ${product.nome}
                        </h5>
                        <p class="product-card-price" style="font-size: 1.1rem; font-weight: 700; color: #212529;">
                            ${precoFormatado}
                        </p>
                        
                        <div class="mt-auto pt-2">
                            <button class="btn btn-outline-dark w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#productModal"
                                    data-product-id="${product.id_produto}">
                                Ver Detalhes
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        `;
    }

    // criação do card carrossel
    function criarCardHtml(product) {
        let precoFormatado = product.preco;
        if (!isNaN(product.preco)) {
            precoFormatado = parseFloat(product.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }
        
        const imagemSrc = 'assets/images/' + product.caminho_imagem;

        return `
            <li class="swiper-slide d-flex flex-column">
                <div class="product-card-link">
                    <img src="${imagemSrc}" alt="${product.nome}" class="product-card-image">
                    <h5 class="product-card-title">${product.nome}</h5>
                    <p class="product-card-price">${precoFormatado}</p>
                    <div class="p-3 pt-0">
                        <button class="btn btn-outline-dark w-100"
                                data-bs-toggle="modal"
                                data-bs-target="#productModal"
                                data-product-id="${product.id_produto}">
                            Ver Detalhes
                        </button>
                    </div>
                </div>
            </li>
        `;
    }

    // Modal    
    class CatalogPage {
        constructor(modalSelector, buttonSelector, database) {
            this.modal = $(modalSelector);
            this.buttonSelector = buttonSelector;
            this.database = database; 
            this.modalTitle = this.modal.find('#modal-product-title');
            this.modalImage = this.modal.find('#modal-product-image');
            this.modalDescription = this.modal.find('#modal-product-description');
            this.modalPrice = this.modal.find('#modal-product-price');
            this.modalMaterial = this.modal.find('#modal-product-material');
            this.modalBtnAddToCart = this.modal.find('.btn-add-to-cart');
            this.currentProduct = null; 
            this.modalQtyInput = this.modal.find('#modal-quantity');
            this.modalBtnIncrease = this.modal.find('.modal-btn-increase');
            this.modalBtnDecrease = this.modal.find('.modal-btn-decrease');

            this.initEvents();
        }

        initEvents() {
            // Evento "Ver Detalhes"
            $(document).on('click', this.buttonSelector, (event) => {
                const button = $(event.currentTarget);
                const productId = button.data('product-id');
                const product = this.findProductById(productId);
                if (product) {
                    this.populateModal(product);
                } else {
                    alert('Erro: Produto não encontrado.');
                }
            });

            // Evento "Adicionar ao Carrinho"
            this.modalBtnAddToCart.on('click', () => {
                if (this.currentProduct) {
                    this.addToCart(this.currentProduct);
                }
            });

            // Eventos de quantidade +/- e blur
            this.modalBtnIncrease.on('click', () => {
                let qty = parseInt(this.modalQtyInput.val());
                qty++;
                this.modalQtyInput.val(qty);
            });

            this.modalBtnDecrease.on('click', () => {
                let qty = parseInt(this.modalQtyInput.val());
                if (qty > 1) { 
                    qty--;
                    this.modalQtyInput.val(qty);
                }
            });

            this.modalQtyInput.on('blur', () => {
                try {
                    let qty = parseInt(this.modalQtyInput.val());
                    if (isNaN(qty) || qty < 1) {
                        this.modalQtyInput.val(1);
                    } else {
                        this.modalQtyInput.val(qty);
                    }
                } catch (e) {
                    this.modalQtyInput.val(1);
                }
            });
        }
        
        // adicionar ao carrinho
        addToCart(product) {
            const quantityToAdd = parseInt(this.modalQtyInput.val());

            if (isNaN(quantityToAdd) || quantityToAdd <= 0) {
                Swal.fire({ title: "Quantidade Inválida", text: "Por favor, insira um número maior que zero.", icon: "error" });
                this.modalQtyInput.val(1);
                return; 
            }

            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let existingItem = cart.find(item => item.id == product.id_produto); 

            if (existingItem) {
                existingItem.quantity += quantityToAdd;
            } else {
                cart.push({
                    id: product.id_produto,
                    nome: product.nome,
                    preco: product.preco,
                    imagem: product.caminho_imagem,
                    quantity: quantityToAdd
                });
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCounter(); 
            var modalInstance = bootstrap.Modal.getInstance(this.modal[0]);
            modalInstance.hide();

            Swal.fire({
                title: "Adicionado!",
                text: `(x${quantityToAdd}) ${product.nome} foi adicionado ao carrinho.`,
                icon: "success",
                timer: 2000, 
                showConfirmButton: false
            });
        }
        
        findProductById(id) {
            return this.database.find(product => product.id_produto == id);
        }

        populateModal(product) {
            this.currentProduct = product; 
            this.modalQtyInput.val(1);

            let precoFormatado = product.preco;
            if (!isNaN(product.preco)) {
                precoFormatado = parseFloat(product.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            }
            this.modalTitle.text(product.nome);
            this.modalImage.attr('src', 'assets/images/' + product.caminho_imagem); 
            this.modalDescription.text(product.descricao); 
            this.modalPrice.text(precoFormatado);
            this.modalMaterial.text(product.marca); 
        }
    }

    // Lógica AJAX
    
    let globalProductDatabase = []; // Variável para armazenar os produtos globalmente no escopo do ready

    $.ajax({
        url: 'produtos', 
        type: 'GET',
        dataType: 'json',
        success: function(productDatabase) {
            
            globalProductDatabase = productDatabase; // ⬅️ Armazena o banco de dados carregado
            
            // ... (Seu código existente para filtrar promoções/lançamentos) ...
            const produtosPromocoes = productDatabase.filter(p => p.id_promocao > 0); 
            const $promocoesWrapper = $('#promocoes-wrapper');
            const $catalogoWrapper = $('#catalogo-completo-wrapper');
            
            $promocoesWrapper.empty();
            $catalogoWrapper.empty();

            const produtosLancamentos = productDatabase.filter(p => p.categoria_id == 1); 
            const $lancamentosWrapper = $('#lancamentos-wrapper'); 

            $lancamentosWrapper.empty();
            if (produtosLancamentos.length > 0) {
                produtosLancamentos.forEach(product => { 
                    $lancamentosWrapper.append(criarCardHtml(product)); 
                });
            } else { 
                $lancamentosWrapper.html("<p class='text-center p-3'>Nenhum lançamento encontrado.</p>"); 
            }
            
            //Promoções (CARROSSEL)
            if (produtosPromocoes.length > 0) {
                produtosPromocoes.forEach(product => { 
                    $promocoesWrapper.append(criarCardHtml(product));
                });
            } else { 
                $promocoesWrapper.html("<p class='text-center p-3'>Nenhuma promoção encontrada.</p>"); 
            }
            
            // Popula Catálogo Completo (GRID)
            // ⬇️ Esta função será reutilizada para exibir os resultados da busca
            function renderizarCatalogo(produtos, termoBusca = null) {
                $catalogoWrapper.empty();
                
                const tituloCatalogo = $('.container h2:contains("Catálogo Completo")');
                const tituloResultados = $(`#titulo-resultados`);

                if (termoBusca) {
                    // Esconde o catálogo original e mostra a seção de busca
                    $('.carousel-container').hide();
                    tituloCatalogo.text(`Resultados da Busca para: "${termoBusca}"`).attr('id', 'titulo-resultados');
                } else {
                    // Retorna ao estado original
                    $('.carousel-container').show();
                    if (tituloResultados.length) tituloResultados.text('Catálogo Completo').removeAttr('id');
                }

                if (produtos.length > 0) {
                    produtos.forEach(product => { 
                        $catalogoWrapper.append(criarCardCatalogoHtml(product)); 
                    });
                } else { 
                    $catalogoWrapper.html(`<div class="col-12"><p class="text-center fs-5">Nenhum produto encontrado${termoBusca ? ` com o nome "${termoBusca}"` : ' no catálogo'}.</p></div>`); 
                }
            }

            // Renderiza o catálogo completo ao carregar
            renderizarCatalogo(productDatabase);

            // Inicia o Swiper (carrossel)
            $('.product-carousel').each(function() {
                // ... (seu código de inicialização do Swiper) ...
                const $carousel = $(this);
                const slidesCount = $carousel.find('.swiper-slide').length;
                new Swiper(this, { 
                    slidesPerView: 1, spaceBetween: 20, loop: slidesCount > 4, 
                    navigation: {
                        nextEl: $carousel.siblings('.swiper-button-next')[0],
                        prevEl: $carousel.siblings('.swiper-button-prev')[0],
                    },
                    breakpoints: { 576: { slidesPerView: 2 }, 768: { slidesPerView: 3 }, 1200: { slidesPerView: 4 } }
                });
            });

            // Inicia o gerenciador do modal 
            const catalogPage = new CatalogPage('#productModal', '[data-bs-toggle="modal"]', productDatabase);
            
            // --- NOVO EVENTO DE BUSCA ---
            
            // 1. Interceptar o envio do formulário de busca
            $('form.d-flex').submit(function(event) {
                event.preventDefault(); // Impede o recarregamento da página
                
                // 2. Coletar o termo de busca (assumindo que o input não tem ID, buscamos por tipo)
                const termoBuscaBruto = $(this).find('input[type="search"]').val();
                const termoBusca = termoBuscaBruto.trim().toLowerCase();
                
                if (termoBusca.length === 0) {
                    // Se o termo estiver vazio, exibe o catálogo completo novamente
                    renderizarCatalogo(globalProductDatabase); 
                    return;
                }
                
                // 3. Filtrar o array globalProductDatabase
                const resultados = globalProductDatabase.filter(product => 
                    product.nome.toLowerCase().includes(termoBusca)
                );
                
                // 4. Renderizar os resultados
                renderizarCatalogo(resultados, termoBuscaBruto);
            });
            
            // ---------------------------

        },
        error: function(xhr, status, error) {
            console.error("Erro fatal ao buscar produtos: ", status, error);
            $('.swiper-wrapper').html("<p class='text-center text-danger p-5'>Não foi possível carregar os produtos.</p>");
        }
    });
    
    updateCartCounter();
});