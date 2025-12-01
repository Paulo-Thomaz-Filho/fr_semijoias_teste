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

    // Prepara os items para enviar ao Mercado Pago
    const items = cart.map((item) => ({
      title: item.nome,
      quantity: item.quantity,
      unit_price: parseFloat(item.preco),
    }));

    // Mostra loading
    Swal.fire({
      title: "Processando...",
      text: "Criando preferência de pagamento",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Envia para o backend criar a preferência
    $.ajax({
      url: "/payment_preference.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({
        items: items,
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
      error: function (xhr, status, error) {
        console.error("Erro:", error);
        console.error("Response:", xhr.responseText);

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
  });
});
