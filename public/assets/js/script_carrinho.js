$(document).ready(function() {     
    function updateCartCounter() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        let totalItems = 0;
        cart.forEach(item => {
            totalItems += item.quantity; 
        });
        $('#cart-counter').text(totalItems);
    }


    // Formata um número para o padrão de moeda brasil (R$)
    function formatarMoeda(valor) {
        const numero = parseFloat(valor);
        if (isNaN(numero)) {
            return "R$ 0,00"; 
        }
        return numero.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    //Cria o HTML para um único item do carrinho.
    function criarItemHtml(item) {
        return `
            <div class="card mb-3 cart-item" data-price="${item.preco}" data-id="${item.id}"> 
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-2 text-center">
                            <img src="assets/images/${item.imagem}" class="img-fluid rounded" alt="${item.nome}" style="max-height: 100px;">
                        </div>
                        <div class="col-12 col-md-6">
                            <h6 class="mb-1">${item.nome}</h6>
                            <div class="input-group" style="width: 130px;">
                                <button class="btn btn-outline-secondary btn-decrease" type="button">−</button>
                                <input type="number" class="form-control text-center quantity" value="${item.quantity}" min="1">
                                <button class="btn btn-outline-secondary btn-increase" type="button">+</button>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 text-md-end">
                            <p class="mb-1">Total: <strong class="item-total">${formatarMoeda(item.preco * item.quantity)}</strong></p>
                            <button class="btn btn-link text-danger p-0 remove-item" type="button">
                                Remover <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    //FUNÇÕES DO CARRINHO

    // Uma função central para salvar mudanças de quantidade no localStorage
    function updateCartInLocalStorage(itemId, newQuantity) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        // Encontra o item no array do carrinho
        let itemToUpdate = cart.find(item => item.id == itemId);

        if (itemToUpdate) {
            itemToUpdate.quantity = newQuantity; // Atualiza a quantidade
            localStorage.setItem('cart', JSON.stringify(cart)); // Salva o array inteiro de volta
        }
        // Atualiza o contador da navbar também
        updateCartCounter();
    }

    // atualiza o subtaotal de um item
    function atualizarTotalItem(itemElement) {
        const $item = $(itemElement);
        const precoBase = parseFloat($item.data('price'));
        const quantidade = parseInt($item.find('.quantity').val());
        const totalItem = precoBase * quantidade;

        $item.find('.item-total').text(formatarMoeda(totalItem));
        atualizarResumoCarrinho(); 
    }

    // Atualiza o total geral
    function atualizarResumoCarrinho() {
        let subtotal = 0;
        let frete = 0;
        
        //obter desconto se houver
        let desconto = parseFloat($('#desconto').data('discount')) || 0;

        $('.cart-item').each(function() {
            const $item = $(this);
            const precoBase = parseFloat($item.data('price'));
            const quantidade = parseInt($item.find('.quantity').val());
            subtotal += precoBase * quantidade;
        });

        //aplicar desconto se houver
        subtotal -= desconto;   

        const valorFixoDoFrete = 9.25; 
        if (subtotal > 0) {
            frete = valorFixoDoFrete;
        }
        
        const total = subtotal + frete;

        $('#subtotal').text(formatarMoeda(subtotal));
        $('#frete').text(formatarMoeda(frete)); 
        $('#total').text(formatarMoeda(total));

        // Atualiza o contador da navbar
        updateCartCounter();
    }

    // Carrega o carrinho do localStorage
    function carregarCarrinho() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const $container = $('#cart-items');
        
        $container.empty(); 

        if (cart.length === 0) {
            $container.html('<div class="alert alert-info">Seu carrinho está vazio.</div>');
        } else {
            cart.forEach(item => {
                const itemHtml = criarItemHtml(item);
                $container.append(itemHtml);
            });
        }
        
        atualizarResumoCarrinho();
    }

    //EVENTOS

    // 1. AUMENTAR QUANTIDADE (Botão +)
    $(document).on('click', '.btn-increase', function() {
        const $item = $(this).closest('.cart-item');
        const $quantityInput = $item.find('.quantity');
        let quantidade = parseInt($quantityInput.val());
        quantidade++;
        $quantityInput.val(quantidade);
        
        atualizarTotalItem($item);

        // Salva no localStorage
        const itemId = $item.data('id');
        updateCartInLocalStorage(itemId, quantidade);
    });

    // 2. DIMINUIR QUANTIDADE (Botão -)
    $(document).on('click', '.btn-decrease', function() {
        const $item = $(this).closest('.cart-item');
        const $quantityInput = $item.find('.quantity');
        let quantidade = parseInt($quantityInput.val());
        
        if (quantidade > 1) { 
            quantidade--;
            $quantityInput.val(quantidade);
            atualizarTotalItem($item);

            // Salva no localStorage
            const itemId = $item.data('id');
            updateCartInLocalStorage(itemId, quantidade);
        }
    });

    // Remover item
    $(document).on('click', '.remove-item', function() {
        const $item = $(this).closest('.cart-item');
        const itemId = $item.data('id');

        Swal.fire({
            title: "Tem certeza?",
            text: "Você não poderá reverter isso!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sim, remover!",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                // Remove do localStorage
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                cart = cart.filter(item => item.id != itemId); 
                localStorage.setItem('cart', JSON.stringify(cart)); 
                
                // Remove do HTML
                $item.remove();
                
                // Recalcula tudo e atualiza o contador
                atualizarResumoCarrinho();

                Swal.fire({
                    title: "Removido!",
                    text: "O item foi removido do seu carrinho.",
                    icon: "success"
                });
            }
        });
    });

    // validando a digitação
    $(document).on('blur', '.quantity', function() {
        const $input = $(this);
        const $item = $input.closest('.cart-item');
        const itemId = $item.data('id');
        let qty = 1;

        try {
            qty = parseInt($input.val());
            if (isNaN(qty) || qty < 1) {
                qty = 1; // aqui reseta para 1 se for inválido
            }
        } catch (e) {
            qty = 1; // aqui reseta para 1 em caso de erro
        }

        // atualiza o <input> para o valor "limpo"
        $input.val(qty); 

        // atualiza o total
        atualizarTotalItem($item); 
        
        // salva a nova quantidade no localStorage
        updateCartInLocalStorage(itemId, qty);
    });

    carregarCarrinho();

});