// pagamento_mercadopago.js - Integração do carrinho com Mercado Pago

$(document).ready(function () {
  // Evento de clique no botão "FINALIZAR COMPRA"
  $("#btn-finalizar-compra").on("click", function () {
    // Pega o carrinho do localStorage
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    if (cart.length === 0) {
      Swal.fire({
        title: "Carrinho vazio!",
        text: "Adicione produtos ao carrinho antes de finalizar a compra.",
        icon: "warning",
        confirmButtonText: "OK",
      });
      return;
    }

    // Pegar dados do usuário logado
    const usuarioLogado = JSON.parse(sessionStorage.getItem("usuario")) || {};
    const idCliente = usuarioLogado.idUsuario;

    if (!idCliente) {
      Swal.fire({
        title: "Erro!",
        text: "Você precisa estar logado para finalizar a compra.",
        icon: "error",
        confirmButtonText: "OK",
        confirmButtonColor: "#dc3545",
      }).then(() => {
        window.location.href = "/login";
      });
      return;
    }

    // Mostra loading
    Swal.fire({
      title: "Processando...",
      text: "Criando pedido",
      icon: "info",
      showConfirmButton: false,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Calcular preço total e criar descrição dos produtos
    const precoTotal = cart.reduce(
      (total, item) => total + parseFloat(item.preco) * item.quantity,
      0
    );
    const produtosNomes = cart.map((item) => item.nome).join(", ");
    const quantidadeTotal = cart.reduce(
      (total, item) => total + item.quantity,
      0
    );

    // PASSO 1: Criar pedido no banco de dados
    const formData = new FormData();
    formData.append("produto_nome", produtosNomes);
    formData.append("id_cliente", idCliente);
    formData.append("preco", precoTotal.toFixed(2));
    formData.append(
      "endereco",
      usuarioLogado.endereco || "Endereço não informado"
    );
    formData.append("quantidade", quantidadeTotal);
    formData.append(
      "data_pedido",
      new Date().toISOString().slice(0, 19).replace("T", " ")
    );
    formData.append("descricao", `Pedido do carrinho: ${produtosNomes}`);
    formData.append("status", "Pendente");

    $.ajax({
      url: "/pedidos/salvar",
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (pedidoResponse) {
        let idPedido = null;
        if (pedidoResponse.ids && Array.isArray(pedidoResponse.ids) && pedidoResponse.ids.length > 0) {
          idPedido = pedidoResponse.ids[0];
        } else if (pedidoResponse.id) {
          idPedido = pedidoResponse.id;
        }

        if (idPedido) {
          // PASSO 2: Criar preferência de pagamento com o ID do pedido
          const items = cart.map((item) => ({
            title: item.nome,
            quantity: item.quantity,
            unit_price: parseFloat(item.preco),
          }));

          $.ajax({
            url: "/payment_preference.php",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({
              items: items,
              id_pedido: idPedido, // ID do pedido como external_reference
            }),
            success: function (response) {
              if (response.id && response.init_point) {
                // Fecha o loading
                Swal.close();

                // Redireciona para o checkout do Mercado Pago
                window.location.href = response.init_point;
              } else {
                Swal.fire({
                  title: "Erro!",
                  text:
                    "Não foi possível criar a preferência de pagamento: " +
                    (response.error || "Erro desconhecido"),
                  icon: "error",
                  confirmButtonText: "OK",
                });
              }
            },
            error: function (xhr) {
              let errorMessage = "Erro ao processar pagamento";
              try {
                const errorData = JSON.parse(xhr.responseText);
                errorMessage = errorData.error || errorMessage;
              } catch (e) {
                errorMessage = xhr.responseText || errorMessage;
              }

              Swal.fire({
                title: "Erro!",
                text: errorMessage,
                icon: "error",
                confirmButtonText: "OK",
              });
            },
          });
        } else {
          Swal.fire({
            title: "Erro!",
            text:
              "Não foi possível criar o pedido: " +
              (pedidoResponse.erro || "Erro desconhecido"),
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      },
      error: function (xhr) {
        let errorMessage = "Erro ao criar pedido";
        try {
          const errorData = JSON.parse(xhr.responseText);
          errorMessage = errorData.erro || errorMessage;
        } catch (e) {
          errorMessage = xhr.responseText || errorMessage;
        }

        Swal.fire({
          title: "Erro!",
          text: errorMessage,
          icon: "error",
          confirmButtonText: "OK",
        });
      },
    });
  });
});
