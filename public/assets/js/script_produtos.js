// Script completo para gerenciamento de produtos
document.addEventListener("DOMContentLoaded", function () {
  // Carregar informações do usuário logado (igual pedidos)
  const carregarUsuarioLogado = () => {
    const nomeCompleto = sessionStorage.getItem("usuario_nome") || "Usuário";
    const primeiroNome = nomeCompleto.split(" ")[0];
    const elementoNomeCompleto = document.getElementById(
      "usuario-nome-completo"
    );
    if (elementoNomeCompleto) {
      elementoNomeCompleto.textContent = nomeCompleto;
    }
    const elementoPrimeiroNome = document.getElementById(
      "usuario-primeiro-nome"
    );
    if (elementoPrimeiroNome) {
      elementoPrimeiroNome.textContent = primeiroNome;
    }
  };
  carregarUsuarioLogado();
  // Logout global para produtos
  var btnsLogout = document.querySelectorAll(".btn-logout-dashboard");
  btnsLogout.forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      fetch("/api/usuario/logout").then(function () {
        localStorage.clear();
        sessionStorage.clear();
        window.location.href = "/login";
      });
    });
  });
  // ...existing code...
  // Atualiza promoções/produtos ao receber evento global
  window.addEventListener("promocaoAtualizada", async function () {
    await carregarPromocoes();
    await carregarProdutos();
  });
  // Referências aos elementos do DOM
  const formProduto = document.getElementById("form-produto");
  const inputId = document.getElementById("produto_id");
  const inputNome = document.getElementById("nome_produto");
  const inputDescricao = document.getElementById("descricao_produto");
  const inputCategoria = document.getElementById("categoria_produto");
  const inputMarca = document.getElementById("marca_produto");
  const inputPreco = document.getElementById("preco_produto");
  const selectPromocao = document.getElementById("promocao_produto");
  const inputEstoque = document.getElementById("estoque");
  const selectDisponivel = document.getElementById("disponivel");
  const inputImagem = document.getElementById("imagem_produto");
  const tabelaProdutos = document.querySelector("#tabelaProdutos tbody");
  const btnCadastrarProduto = document.getElementById("btnCadastrarProduto");
  const btnAtualizarProduto = document.getElementById("btnAtualizarProduto");
  const btnExcluirProduto = document.getElementById("btnExcluirProduto");

  // Atualiza preview da imagem ao selecionar arquivo
  inputImagem.addEventListener("change", function (e) {
    const previewImg = document.getElementById("preview_imagem_produto");
    const file = e.target.files[0];
    // Não bloqueia o comportamento padrão do input
    if (file) {
      const reader = new FileReader();
      reader.onload = function (ev) {
        previewImg.src = ev.target.result;
        previewImg.style.display = "block";
      };
      reader.readAsDataURL(file);
    } else {
      previewImg.src = "/public/assets/images/placeholder-image.svg";
      previewImg.style.display = "block";
    }
  });

  // Mapeamento de promoções por id
  let mapaPromocoes = {};
  let produtoSelecionado = null;
  let precoOriginalProduto = 0; // Armazena o preço original do produto

  // Função para exibir mensagens
  function exibirMensagemProduto(mensagem, tipo = "erro") {
    const msgDiv = document.getElementById("produtoMsg");
    if (!msgDiv) return;

    msgDiv.textContent = mensagem;
    msgDiv.style.display = "block";
    msgDiv.className =
      tipo === "sucesso"
        ? "alert alert-success text-center mt-3"
        : "alert alert-danger text-center mt-3";

    setTimeout(() => {
      msgDiv.style.display = "none";
    }, 4000);
  }

  // Carregar promoções no select e no mapa
  const carregarPromocoes = async () => {
    try {
      const response = await fetch("/promocoes");
      const promocoes = await response.json();
      // Salva array global para uso no cálculo de preço
      window.promocoesArray = promocoes;
      selectPromocao.innerHTML =
        '<option value="" disabled selected>Selecione uma promoção</option>';
      selectPromocao.innerHTML += '<option value="sem">Sem promoção</option>';
      mapaPromocoes = {};
      promocoes.forEach((promocao) => {
        // Só adiciona se ativa e dentro do período (backend já filtra, mas reforça no frontend)
        const hoje = new Date();
        const inicio = new Date(promocao.dataInicio);
        const fim = new Date(promocao.dataFim);
        if (promocao.status == 1 && inicio <= hoje && fim >= hoje) {
          const option = document.createElement("option");
          option.value = promocao.idPromocao;
          let desconto =
            promocao.desconto !== undefined && promocao.desconto !== null
              ? parseInt(promocao.desconto)
              : "";
          let tipo = promocao.tipo_desconto === "valor" ? "R$" : "%";
          let descontoFormatado =
            tipo === "R$" ? `R$ ${desconto}` : `${desconto}%`;
          option.textContent = `${promocao.nome} - ${descontoFormatado}`;
          selectPromocao.appendChild(option);
          mapaPromocoes[
            promocao.idPromocao
          ] = `${promocao.nome} - ${descontoFormatado}`;
        }
      });
    } catch (error) {
    }
  };

  // Função para formatar preço em Real
  const formatarPreco = (preco) => {
    return parseFloat(preco).toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    });
  };

  // Formatar input de preço enquanto digita (padrão brasileiro com vírgula automática)
  inputPreco.addEventListener("input", function (e) {
    let valor = e.target.value;

    // Remove tudo que não é número
    valor = valor.replace(/\D/g, "");

    // Se não tiver nada, limpa o campo
    if (!valor) {
      e.target.value = "";
      precoOriginalProduto = 0;
      return;
    }

    // Converte para número e divide por 100 para ter 2 casas decimais
    let numero = parseInt(valor) / 100;

    // Formata com 2 casas decimais e vírgula
    e.target.value = numero.toFixed(2).replace(".", ",");

    // Se não tiver produto selecionado (modo cadastro), salva como preço original
    if (!produtoSelecionado) {
      precoOriginalProduto = numero;
    }
  });

  // Função para converter valor formatado (com vírgula) para número
  const precoParaNumero = (valorFormatado) => {
    if (!valorFormatado) return 0;
    return parseFloat(valorFormatado.replace(",", "."));
  };

  // Carregar opções de disponibilidade
  const carregarDisponibilidade = () => {
    selectDisponivel.innerHTML =
      '<option value="" disabled selected>Selecione uma disponibilidade</option>';
    selectDisponivel.innerHTML += '<option value="1">Sim</option>';
    selectDisponivel.innerHTML += '<option value="0">Não</option>';
  };

  // Carregar produtos na tabela
  const carregarProdutos = async () => {
    const cardsContainer = document.getElementById("cardsProdutos");

    tabelaProdutos.innerHTML =
      '<tr><td colspan="10" class="text-center py-4 text-muted">Carregando produtos...</td></tr>';
    if (cardsContainer) {
      cardsContainer.innerHTML =
        '<div class="text-center py-4 text-muted">Carregando produtos...</div>';
    }

    if (Object.keys(mapaPromocoes).length === 0) {
      await carregarPromocoes();
    }

    try {
      const response = await fetch("/produtos"); // Endpoint de listagem
      const produtos = await response.json();

      if (!Array.isArray(produtos) || produtos.length === 0) {
        tabelaProdutos.innerHTML =
          '<tr><td colspan="10" class="text-center py-4 text-muted">Nenhum produto cadastrado</td></tr>';
        if (cardsContainer) {
          cardsContainer.innerHTML =
            '<div class="text-center py-4 text-muted">Nenhum produto cadastrado</div>';
        }
        return;
      }

      // Renderizar tabela
      tabelaProdutos.innerHTML = produtos
        .map((p) => {
          let precoOriginal = parseFloat(p.preco);
          let precoFinal = precoOriginal;
          let promocao = "N/A";
          let promocaoId = p.idPromocao;
          let promocaoObj = null;
          if (promocaoId) {
            promocaoObj = window.promocoesArray?.find(
              (pr) => pr.idPromocao == promocaoId
            );
          }

          if (promocaoObj) {
            let descontoFormatado = "";
            if (promocaoObj.tipo_desconto === "valor") {
              descontoFormatado = "R$ " + parseInt(promocaoObj.desconto);
            } else {
              descontoFormatado = parseInt(promocaoObj.desconto) + "%";
            }
            promocao = promocaoObj.nome + " - " + descontoFormatado;
            if (promocaoObj.tipo_desconto === "percentual") {
              precoFinal = precoOriginal * (1 - promocaoObj.desconto / 100);
            } else if (promocaoObj.tipo_desconto === "valor") {
              precoFinal = precoOriginal - promocaoObj.desconto;
            }
            if (precoFinal < 0) precoFinal = 0;
          }
          const precoFormatado = formatarPreco(precoFinal);
          const disponivelBadge =
            p.disponivel == 1
              ? '<span class="status-badge status-green">• Sim</span>'
              : '<span class="status-badge status-danger">• Não</span>';
          return (
            '<tr class="border-bottom border-light">' +
            '<td class="py-4 text-dark">' +
            p.idProduto +
            "</td>" +
            '<td class="py-4 text-dark">' +
            (p.nome || "N/A") +
            "</td>" +
            '<td class="py-4 text-dark">' +
            precoFormatado +
            "</td>" +
            '<td class="py-4 text-dark">' +
            (p.marca || "N/A") +
            "</td>" +
            '<td class="py-4 text-dark">' +
            (p.categoria || "N/A") +
            "</td>" +
            '<td class="py-4 text-dark">' +
            (p.estoque || 0) +
            "</td>" +
            '<td class="py-4">' +
            disponivelBadge +
            "</td>" +
            '<td class="py-4 text-dark">' +
            promocao +
            "</td>" +
            '<td class="py-4"><button class="btn btn-sm btn-success px-3 py-2 fw-medium rounded-4 btn-selecionar-produto" data-id="' +
            p.idProduto +
            '">Selecionar</button></td>' +
            "</tr>"
          );
        })
        .join("");

      // Renderizar cards para mobile
      if (cardsContainer) {
        cardsContainer.innerHTML = produtos
          .map((p) => {
            let precoOriginal = parseFloat(p.preco);
            let precoFinal = precoOriginal;
            let promocao = "N/A";
            let promocaoId = p.idPromocao;
            let promocaoObj = null;

            if (promocaoId) {
              promocaoObj = window.promocoesArray?.find(
                (pr) => pr.idPromocao == promocaoId
              );
            }

            if (promocaoObj) {
              let descontoFormatado = "";
              if (promocaoObj.tipo_desconto === "valor") {
                descontoFormatado = "R$ " + parseInt(promocaoObj.desconto);
              } else {
                descontoFormatado = parseInt(promocaoObj.desconto) + "%";
              }
              promocao = promocaoObj.nome + " - " + descontoFormatado;
              if (promocaoObj.tipo_desconto === "percentual") {
                precoFinal = precoOriginal * (1 - promocaoObj.desconto / 100);
              } else if (promocaoObj.tipo_desconto === "valor") {
                precoFinal = precoOriginal - promocaoObj.desconto;
              }
              if (precoFinal < 0) precoFinal = 0;
            }

            const precoFormatado = formatarPreco(precoFinal);
            const disponivelBadge =
              p.disponivel == 1
                ? '<span class="status-badge status-green">• Sim</span>'
                : '<span class="status-badge status-danger">• Não</span>';

            return `
                        <div class="card border-0 bg-white mb-3 shadow-sm rounded-4">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-medium mb-0 text-dark">${
                                      p.nome || "N/A"
                                    }</h6>
                                    ${disponivelBadge}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>ID:</strong> ${p.idProduto}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Preço:</strong> ${precoFormatado}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Marca:</strong> ${p.marca || "N/A"}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Categoria:</strong> ${
                                      p.categoria || "N/A"
                                    }
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Estoque:</strong> ${p.estoque || 0}
                                </div>
                                <div class="small text-muted mb-2">
                                    <strong>Promoção:</strong> ${promocao}
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    <button class="btn btn-sm btn-success px-3 py-2 fw-medium rounded-4 w-100 btn-selecionar-produto-mobile" data-id="${
                                      p.idProduto
                                    }">
                                        Selecionar Produto
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
          })
          .join("");
      }

      // Adicionar eventos aos botões de seleção (tabela)
      document
        .querySelectorAll(".btn-selecionar-produto")
        .forEach((btnSelecionarProduto) => {
          btnSelecionarProduto.addEventListener("click", function () {
            tabelaProdutos
              .querySelectorAll("tr")
              .forEach((row) => row.classList.remove("table-active"));
            this.closest("tr").classList.add("table-active");
            selecionarProduto(this.dataset.id);
          });
        });

      // Adicionar eventos aos botões de seleção (cards mobile)
      document
        .querySelectorAll(".btn-selecionar-produto-mobile")
        .forEach((btn) => {
          btn.addEventListener("click", function () {
            selecionarProduto(this.dataset.id);
          });
        });
    } catch (error) {
      tabelaProdutos.innerHTML =
        '<tr><td colspan="9" class="text-center py-4 text-danger">Erro ao carregar os produtos</td></tr>';
      if (cardsContainer) {
        cardsContainer.innerHTML =
          '<div class="text-center py-4 text-danger">Erro ao carregar os produtos</div>';
      }
    }
  };

  // Selecionar produto para edição
  const selecionarProduto = async (id) => {
    try {
      const response = await fetch(`/produtos/buscar?idProduto=${id}`);
      const produto = await response.json();

      inputId.value = produto.idProduto ?? "";
      inputNome.value = produto.nome ?? "";
      inputDescricao.value = produto.descricao ?? "";
      inputCategoria.value = produto.categoria ?? "";
      inputMarca.value = produto.marca ?? "";

      let precoAtual = parseFloat(produto.preco);
      let promocaoId = produto.idPromocao ?? "";

      // O preço no banco SEMPRE é o original (sem desconto)
      // A correção anterior garantiu isso
      precoOriginalProduto = precoAtual;

      // Verifica se tem promoção VÁLIDA para calcular desconto na exibição
      let precoComDesconto = precoAtual;
      const promocoesValidas = Object.keys(mapaPromocoes);
      const temPromocaoValida =
        promocaoId && promocoesValidas.includes(promocaoId.toString());

      if (temPromocaoValida) {
        const promocaoObj = window.promocoesArray?.find(
          (pr) => pr.idPromocao == promocaoId
        );

        if (promocaoObj) {
          const desconto = parseFloat(promocaoObj.desconto);

          // Calcula o preço com desconto para exibição
          if (promocaoObj.tipo_desconto === "percentual") {
            precoComDesconto = precoAtual * (1 - desconto / 100);
          } else if (promocaoObj.tipo_desconto === "valor") {
            precoComDesconto = precoAtual - desconto;
          }

          if (precoComDesconto < 0) precoComDesconto = 0;
        }
      }

      // Seleciona a promoção correta e mostra o preço
      if (temPromocaoValida) {
        selectPromocao.value = promocaoId;
        inputPreco.value = precoComDesconto.toFixed(2).replace(".", ",");
      } else {
        selectPromocao.value = "sem";
        inputPreco.value = precoAtual.toFixed(2).replace(".", ",");
      }
      inputEstoque.value = produto.estoque ?? 0;
      selectDisponivel.value = produto.disponivel ?? 1;
      produtoSelecionado = produto;
      btnCadastrarProduto.disabled = true;
      btnAtualizarProduto.disabled = false;
      btnExcluirProduto.disabled = false;
      // Preview da imagem cadastrada
      const previewImg = document.getElementById("preview_imagem_produto");
      if (produto.caminhoImagem) {
        previewImg.src = "assets/images/" + produto.caminhoImagem;
        previewImg.style.display = "block";
      } else {
        previewImg.src = "/public/assets/images/placeholder-image.svg";
        previewImg.style.display = "block";
      }
      formProduto.scrollIntoView({ behavior: "smooth", block: "start" });
    } catch (error) {
      exibirMensagemProduto("Erro ao carregar dados do produto");
    }
  };

  // Cadastrar produto
  const cadastrarProduto = async (formData) => {
    try {
      const response = await fetch("/produtos/salvar", {
        method: "POST",
        body: formData, // Envia o FormData
      });
      const result = await response.json();
      const msgDiv = document.getElementById("produtoMsg");
      if (response.ok) {
        limparFormulario();
        if (msgDiv) {
          msgDiv.textContent = "Produto cadastrado com sucesso!";
          msgDiv.className = "text-success text-center mt-3";
          msgDiv.style.display = "block";
          setTimeout(() => {
            msgDiv.style.display = "none";
            msgDiv.textContent = "";
          }, 1500);
        }
        carregarProdutos();
        if (typeof window.carregarGraficoPizza === "function")
          window.carregarGraficoPizza();
        if (typeof window.atualizarDashboard === "function")
          window.atualizarDashboard();
      } else {
        if (msgDiv) {
          msgDiv.textContent = result.erro || "Erro ao cadastrar produto";
          msgDiv.className = "text-danger text-center mt-3";
          msgDiv.style.display = "block";
        } else {
        }
      }
    } catch (error) {
      const msgDiv = document.getElementById("produtoMsg");
      if (msgDiv) {
        msgDiv.textContent = "Erro ao cadastrar produto";
        msgDiv.className = "text-danger text-center mt-3";
        msgDiv.style.display = "block";
      }
    }
  };

  // Atualizar produto
  const atualizarProduto = async (formData) => {
    try {
      const response = await fetch("/produtos/atualizar", {
        method: "POST",
        body: formData, // Envia o FormData
      });
      const result = await response.json();
      const msgDiv = document.getElementById("produtoMsg");
      if (response.ok) {
        limparFormulario();
        if (msgDiv) {
          msgDiv.textContent = "Produto atualizado com sucesso!";
          msgDiv.className = "text-success text-center mt-3";
          msgDiv.style.display = "block";
          setTimeout(() => {
            msgDiv.style.display = "none";
            msgDiv.textContent = "";
          }, 1500);
        }
        carregarProdutos();
        if (typeof window.carregarGraficoPizza === "function")
          window.carregarGraficoPizza();
        if (typeof window.atualizarDashboard === "function")
          window.atualizarDashboard();
      } else {
        if (msgDiv) {
          msgDiv.textContent = result.erro || "Erro ao atualizar produto";
          msgDiv.className = "text-danger text-center mt-3";
          msgDiv.style.display = "block";
        } else {
          alert(result.erro || "Erro ao atualizar produto");
        }
      }
    } catch (error) {
      const msgDiv = document.getElementById("produtoMsg");
      if (msgDiv) {
        msgDiv.textContent = "Erro ao atualizar produto";
        msgDiv.className = "text-danger text-center mt-3";
        msgDiv.style.display = "block";
      }
      console.error("Erro ao atualizar produto:", error);
    }
  };

  // Deletar produto
  const deletarProduto = async (id) => {
    if (!confirm("Tem certeza que deseja excluir este produto?")) return;
    try {
      const response = await fetch("/produtos/deletar", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ idProduto: id }),
      });
      const result = await response.json();
      const msgDiv = document.getElementById("produtoMsg");
      if (response.ok) {
        // Limpa apenas os campos do formulário, mas mantém a mensagem visível
        limparFormulario();
        if (msgDiv) {
          msgDiv.textContent = "Produto excluído com sucesso!";
          msgDiv.className = "text-success text-center mt-3";
          msgDiv.style.display = "block";
          setTimeout(() => {
            msgDiv.style.display = "none";
            msgDiv.textContent = "";
            msgDiv.className = "text-center mt-3";
          }, 1500);
        }
        carregarProdutos();
        if (typeof window.carregarGraficoPizza === "function")
          window.carregarGraficoPizza();
        if (typeof window.atualizarDashboard === "function")
          window.atualizarDashboard();
      } else {
        exibirMensagemProduto(
          result.erro || "Erro ao excluir produto",
          "danger"
        );
      }
    } catch (error) {
      const msgDiv = document.getElementById("produtoMsg");
      if (msgDiv) {
        msgDiv.textContent = "Erro ao excluir produto";
        msgDiv.className = "text-danger text-center mt-3";
        msgDiv.style.display = "block";
      }
    }
  };

  // Limpar formulário
  const limparFormulario = () => {
    formProduto.reset();
    inputId.value = "Auto";
    selectDisponivel.value = "";
    produtoSelecionado = null;
    precoOriginalProduto = 0; // Limpa o preço original salvo
    btnCadastrarProduto.disabled = false;
    btnAtualizarProduto.disabled = true;
    btnExcluirProduto.disabled = true;
    tabelaProdutos &&
      tabelaProdutos
        .querySelectorAll("tr")
        .forEach((row) => row.classList.remove("table-active"));

    // Resetar preview da imagem para o SVG padrão
    const previewImg = document.getElementById("preview_imagem_produto");
    if (previewImg) {
      previewImg.src = "/public/assets/images/placeholder-image.svg";
      previewImg.style.display = "block";
    }
    // Limpar input file
    if (inputImagem) {
      inputImagem.value = "";
    }
  };

  // Event listener para o formulário
  // Listener do Formulário (CORRIGIDO para FormData e snake_case)
  formProduto.addEventListener("submit", function (e) {
    e.preventDefault();

    // Validações de campos obrigatórios
    if (!inputNome.value.trim()) {
      exibirMensagemProduto("Por favor, preencha o nome do produto.");
      inputNome.focus();
      return;
    }

    if (!inputPreco.value.trim()) {
      exibirMensagemProduto("Por favor, preencha o preço do produto.");
      inputPreco.focus();
      return;
    }

    if (!inputMarca.value.trim()) {
      exibirMensagemProduto("Por favor, preencha a marca do produto.");
      inputMarca.focus();
      return;
    }

    if (!inputCategoria.value.trim()) {
      exibirMensagemProduto("Por favor, preencha a categoria do produto.");
      inputCategoria.focus();
      return;
    }

    if (!inputEstoque.value && inputEstoque.value !== "0") {
      exibirMensagemProduto("Por favor, preencha a quantidade em estoque.");
      inputEstoque.focus();
      return;
    }

    let idPromocaoValue = selectPromocao.value;
    if (idPromocaoValue === "sem") idPromocaoValue = "";

    const formData = new FormData();

    // Adiciona os campos de texto
    formData.append("nome", inputNome.value);
    formData.append("descricao", inputDescricao.value);
    // SEMPRE envia o preço ORIGINAL (sem desconto)
    formData.append(
      "preco",
      precoOriginalProduto || precoParaNumero(inputPreco.value)
    );
    formData.append("marca", inputMarca.value);
    formData.append("categoria", inputCategoria.value);
    formData.append("id_promocao", idPromocaoValue);
    formData.append("estoque", inputEstoque.value || 0);
    formData.append("disponivel", selectDisponivel.value);

    // Adiciona o arquivo de imagem
    const file = inputImagem.files[0];
    if (file) {
      formData.append("caminho_imagem", file); // CORRIGIDO
    }

    if (produtoSelecionado) {
      formData.append("id_produto", inputId.value); // CORRIGIDO: backend espera id_produto
      atualizarProduto(formData);
    } else {
      cadastrarProduto(formData);
    }
  });

  // Os botões Cadastrar, Atualizar, Excluir têm listeners separados no seu HTML

  btnCadastrarProduto.addEventListener("click", function (e) {
    // Se o botão for type="submit", ele dispara o evento 'submit' do formulário
    // A lógica principal já está no listener 'submit' do formProduto
  });

  btnAtualizarProduto.addEventListener("click", function () {
    if (!produtoSelecionado) {
      alert("Selecione um produto primeiro");
      return;
    }
    formProduto.dispatchEvent(
      new Event("submit", { cancelable: true, bubbles: true })
    );
  });

  btnExcluirProduto.addEventListener("click", function () {
    if (!produtoSelecionado) {
      alert("Selecione um produto primeiro");
      return;
    }
    deletarProduto(inputId.value);
  });

  formProduto.addEventListener("reset", function () {
    limparFormulario();
  });

  // Listener para recalcular preço quando muda a promoção
  selectPromocao.addEventListener("change", function () {
    const promocaoId = this.value;

    // Se não tiver preço original salvo, não faz nada
    if (!precoOriginalProduto || precoOriginalProduto <= 0) {
      return;
    }

    let precoFinal = precoOriginalProduto;

    // Se selecionou "sem promoção", volta ao preço original
    if (promocaoId === "sem" || !promocaoId) {
      inputPreco.value = precoOriginalProduto.toFixed(2).replace(".", ",");
      return;
    }

    // Busca a promoção selecionada no array global
    const promocaoObj = window.promocoesArray?.find(
      (pr) => pr.idPromocao == promocaoId
    );

    if (promocaoObj) {
      // Garante que o desconto é um número
      const desconto = parseFloat(promocaoObj.desconto);

      // Calcula o preço com desconto
      if (promocaoObj.tipo_desconto === "percentual") {
        precoFinal = precoOriginalProduto * (1 - desconto / 100);
      } else if (promocaoObj.tipo_desconto === "valor") {
        precoFinal = precoOriginalProduto - desconto;
      }

      if (precoFinal < 0) precoFinal = 0;
    }

    // Atualiza o campo de preço
    inputPreco.value = precoFinal.toFixed(2).replace(".", ",");
  });

  // --- INICIALIZAÇÃO ---
  btnCadastrarProduto.disabled = false;
  btnAtualizarProduto.disabled = true;
  btnExcluirProduto.disabled = true;

  carregarPromocoes();
  carregarDisponibilidade();
  carregarProdutos();
});
