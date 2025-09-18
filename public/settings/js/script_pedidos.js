$(document).ready(function() {

    // --- SELETORES E VARIÁVEIS ---
    const corpoTabela = $('#tabela-pedidos-corpo');
    const formPedidos = $('#form-pedidos'); // Dando um ID mais genérico ao formulário
    // IDs dos botões que vamos controlar
    const btnCadastrar = $('#btn-cadastrar-pedido');
    const btnAtualizar = $('#btn-atualizar-pedido');
    const btnCancelarEdicao = $('#btn-cancelar-operacao');
    const tituloFormulario = $('#titulo-gerenciar-pedido');

    // --- FUNÇÕES PRINCIPAIS ---

    /**
     * Busca os pedidos na API e atualiza a tabela na tela.
     */
    function carregarPedidos() {
        const usuarioIdParaTeste = 1; 

        $.ajax({
            url: `api/pedidos/listarPorUsuario?id_usuario=${usuarioIdParaTeste}`,
            type: 'GET',
            dataType: 'json',
            success: function(pedidos) {
                construirTabela(pedidos || []);
            },
            error: function(xhr) {
                console.error('Erro ao carregar os pedidos:', xhr.responseText);
                corpoTabela.html('<tr><td colspan="8" class="text-center text-danger">Ocorreu um erro ao carregar os dados.</td></tr>');
            }
        });
    }

    /**
     * Constrói as linhas da tabela com base nos dados recebidos.
     */
    function construirTabela(pedidos) {
        corpoTabela.empty();

        if (pedidos.length === 0) {
            corpoTabela.html('<tr><td colspan="8" class="text-center">Nenhum pedido encontrado.</td></tr>');
            return;
        }

        pedidos.forEach(pedido => {
            const linha = `
                <tr class="border-bottom border-light">
                    <td class="py-4 text-dark">${pedido.id_pedido}</td>
                    <td class="py-4 text-dark">Produto a ser definido</td>
                    <td class="py-4 text-dark">Cliente a ser definido</td>
                    <td class="py-4 text-dark">Qtd. a ser definida</td>
                    <td class="py-4 text-dark">R$ ${parseFloat(pedido.valor_total).toFixed(2).replace('.', ',')}</td>
                    <td class="py-4 text-dark">${new Date(pedido.data_pedido).toLocaleDateString('pt-BR')}</td>
                    <td class="py-4 text-dark">${pedido.status}</td>
                    <td class="py-3">
                        <button class="btn btn-sm btn-primary" onclick="selecionarPedido(${pedido.id_pedido})">
                            Editar
                        </button>
                    </td>
                </tr>
            `;
            corpoTabela.append(linha);
        });
    }
    
    /**
     * Preenche o formulário com os dados de um pedido específico.
     * Esta função é chamada pela função global 'selecionarPedido'.
     */
    function preencherFormularioPedido(pedido) {
        // Mapeia os dados do objeto 'pedido' para os IDs dos campos do formulário
        $('#id_cadastro').val(pedido.id_pedido);
        
        // NOTA: Para preencher o nome do cliente e os produtos, sua API `api/pedido/buscar`
        // precisará retornar mais dados (usando JOINs no SQL do PedidoDAO).
        // Por enquanto, vamos preencher com os IDs que temos.
        $('#cliente_cadastro').val(pedido.id_usuario); 
        $('#valor_cadastro').val(parseFloat(pedido.valor_total).toFixed(2));
        
        // Formata a data para o formato que o input[type=date] aceita (YYYY-MM-DD)
        if (pedido.data_pedido) {
            const dataFormatada = new Date(pedido.data_pedido).toISOString().split('T')[0];
            $('#data_cadastro').val(dataFormatada);
        }
        
        // Lógica para selecionar o status correto no <select> (se você tiver um)
        // $('#status_cadastro').val(pedido.status);

        // Altera a interface para o "modo de edição"
        alternarModoFormulario(true);

        // Rola a página para o topo para que o usuário veja o formulário preenchido
        $('html, body').animate({ scrollTop: formPedidos.offset().top - 20 }, 'slow');
    }

    /**
     * Altera a interface do formulário entre modo de Cadastro e Edição.
     */
    function alternarModoFormulario(modoEdicao) {
        if (modoEdicao) {
            tituloFormulario.text('Editar Pedido');
            btnCadastrar.hide();
            btnAtualizar.show();
            btnCancelarEdicao.show();
        } else {
            tituloFormulario.text('Gerenciar Pedido');
            formPedidos[0].reset(); // Limpa todos os campos do formulário
            btnCadastrar.show();
            btnAtualizar.hide();
            btnCancelarEdicao.hide();
        }
    }

    // --- EVENT HANDLERS (Ações do Usuário) ---

    // Evento para CADASTRAR ou ATUALIZAR um pedido
    formPedidos.on('submit', function(event) {
        event.preventDefault();
        
        const pedidoId = $('#id_cadastro').val();
        const isEditing = pedidoId && pedidoId > 0;
        
        const url = isEditing ? 'api/pedido/atualizar' : 'api/pedido/criar'; // Você precisará criar a rota "atualizar"

        const dadosFormulario = {
            ...(isEditing && { id_pedido: parseInt(pedidoId) }),
            id_usuario: parseInt($('#cliente_cadastro').val()), // Supondo que o campo contenha o ID
            id_endereco_entrega: 1, // Fixo para teste
            valor_total: parseFloat($('#valor_cadastro').val()),
            status: 'processando', // Obter de um campo de status se houver
        };

        $.ajax({
            url: url,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(dadosFormulario),
            success: function(response) {
                if (response.success) {
                    alert(`Pedido ${isEditing ? 'atualizado' : 'cadastrado'} com sucesso!`);
                    alternarModoFormulario(false); // Volta ao modo de cadastro e limpa o form
                    carregarPedidos(); // Atualiza a tabela
                } else {
                    alert('Erro: ' + (response.error || 'Não foi possível salvar o pedido.'));
                }
            },
            error: function(xhr) {
                console.error('Erro na chamada AJAX:', xhr.responseText);
                alert('Ocorreu um erro de comunicação ao salvar o pedido.');
            }
        });
    });

    // Evento para o botão "Cancelar Edição"
    btnCancelarEdicao.on('click', function() {
        alternarModoFormulario(false);
    });

    // --- INICIALIZAÇÃO ---
    alternarModoFormulario(false); // Garante que o formulário comece no estado de cadastro
    carregarPedidos(); // Carrega os pedidos pela primeira vez
});


/**
 * Função global para ser chamada pelo botão "Editar" na tabela.
 * Esta função precisa ser global para ser acessível pelo 'onclick' do HTML.
 */
function selecionarPedido(pedidoId) {
    // Para chamar a função que está dentro do $(document).ready(),
    // podemos disparar um evento customizado.
    $(document).trigger('selecionarPedido', [pedidoId]);
}

// Escuta o evento customizado e chama a função interna
$(document).on('selecionarPedido', function(event, pedidoId) {
    if (!pedidoId) return;

    $.ajax({
        url: `api/pedido/buscar?id=${pedidoId}`,
        type: 'GET',
        dataType: 'json',
        success: function(pedido) {
            if (pedido) {
                // Dispara um novo evento para preencher o formulário com os dados recebidos
                $(document).trigger('preencherFormulario', [pedido]);
            } else {
                alert('Pedido não encontrado.');
            }
        },
        error: function(xhr) {
            console.error("Erro ao buscar pedido:", xhr.responseText);
            alert('Ocorreu um erro ao buscar os detalhes do pedido.');
        }
    });
});

// Escuta o evento de preenchimento
$(document).on('preencherFormulario', function(event, pedido) {
    // A lógica de preenchimento agora está aqui para ser acessada globalmente
    $('#id_cadastro').val(pedido.id_pedido);
    $('#cliente_cadastro').val(pedido.id_usuario);
    $('#valor_cadastro').val(parseFloat(pedido.valor_total).toFixed(2));
    if (pedido.data_pedido) {
        const dataFormatada = new Date(pedido.data_pedido).toISOString().split('T')[0];
        $('#data_cadastro').val(dataFormatada);
    }

    // Altera para o modo de edição
    $('#titulo-gerenciar-pedido').text('Editar Pedido');
    $('#btn-cadastrar-pedido').hide();
    $('#btn-atualizar-pedido').show();
    $('#btn-cancelar-operacao').show();

    // Rola a página para o formulário
    $('html, body').animate({ scrollTop: $('#form-pedidos').offset().top - 20 }, 'slow');
});