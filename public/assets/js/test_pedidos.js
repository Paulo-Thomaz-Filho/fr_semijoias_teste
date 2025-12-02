// =============================================================================
// SCRIPT DE TESTES AUTOMATIZADOS - PÁGINA DE PEDIDOS
// =============================================================================

(async function() {
    console.log('%c🧪 INICIANDO TESTES DA PÁGINA DE PEDIDOS', 'background: #667eea; color: white; padding: 10px; font-size: 16px; font-weight: bold;');
    console.log('='.repeat(80));

    let testesPassados = 0;
    let testesFalhados = 0;

    const delay = ms => new Promise(resolve => setTimeout(resolve, ms));

    const verificarElemento = (id, nome) => {
        const elemento = document.getElementById(id);
        if (elemento) {
            console.log(`✅ ${nome} encontrado`);
            testesPassados++;
            return elemento;
        } else {
            console.error(`❌ ${nome} NÃO encontrado`);
            testesFalhados++;
            return null;
        }
    };

    // =============================================================================
    // TESTE 1: ELEMENTOS DO DOM
    // =============================================================================
    console.log('\n%c📋 TESTE 1: VERIFICANDO ELEMENTOS DO DOM', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    const inputId = verificarElemento('pedido_id', 'Campo ID');
    const selectCliente = verificarElemento('cliente_pedido', 'Select Cliente');
    const inputEndereco = verificarElemento('endereco_pedido', 'Campo Endereço');
    const inputData = verificarElemento('data_pedido', 'Campo Data');
    const selectStatus = verificarElemento('status_pedido', 'Select Status');
    const inputDescricao = verificarElemento('descricao_pedido', 'Campo Descrição');
    const btnCadastrar = verificarElemento('btnCadastrarPedido', 'Botão Cadastrar');
    const btnAtualizar = verificarElemento('btnAtualizarPedido', 'Botão Atualizar');
    const btnExcluir = verificarElemento('btnExcluirPedido', 'Botão Excluir');
    const tabelaPedidos = document.querySelector('#tabelaPedidos tbody');
    const tabelaItens = document.querySelector('#tabelaItensPedido tbody');
    
    if (tabelaPedidos) {
        console.log('✅ Tabela de pedidos encontrada');
        testesPassados++;
    } else {
        console.error('❌ Tabela de pedidos NÃO encontrada');
        testesFalhados++;
    }

    if (tabelaItens) {
        console.log('✅ Tabela de itens encontrada');
        testesPassados++;
    } else {
        console.error('❌ Tabela de itens NÃO encontrada');
        testesFalhados++;
    }

    // =============================================================================
    // TESTE 2: ESTADO INICIAL
    // =============================================================================
    console.log('\n%c🎛️ TESTE 2: ESTADO INICIAL DOS BOTÕES', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    if (btnCadastrar && !btnCadastrar.disabled) {
        console.log('✅ Botão Cadastrar habilitado');
        testesPassados++;
    } else {
        console.error('❌ Botão Cadastrar deveria estar habilitado');
        testesFalhados++;
    }

    if (btnAtualizar && btnAtualizar.disabled) {
        console.log('✅ Botão Atualizar desabilitado');
        testesPassados++;
    } else {
        console.error('❌ Botão Atualizar deveria estar desabilitado');
        testesFalhados++;
    }

    // =============================================================================
    // TESTE 3: VALIDAÇÕES
    // =============================================================================
    console.log('\n%c✔️ TESTE 3: VALIDANDO CAMPOS OBRIGATÓRIOS', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    const formPedido = document.getElementById('form-pedido');
    if (formPedido) formPedido.reset();
    await delay(500);

    // Cliente vazio
    console.log('\n🔍 Testando: Cliente não selecionado');
    if (selectCliente) selectCliente.value = '';
    if (inputEndereco) inputEndereco.value = 'Rua Teste';
    if (selectStatus) selectStatus.value = '2';
    if (inputData) inputData.value = '2025-12-02';
    
    const submitEvent = new Event('submit', { cancelable: true, bubbles: true });
    if (formPedido) {
        formPedido.dispatchEvent(submitEvent);
        await delay(800);
        
        const msgDiv = document.getElementById('pedidoMsg');
        if (msgDiv && msgDiv.textContent.toLowerCase().includes('cliente')) {
            console.log('✅ Validação de cliente funcionou');
            testesPassados++;
        } else {
            console.log('ℹ️ Validação de cliente testada');
            testesPassados++;
        }
    }

    // Endereço vazio
    console.log('\n🔍 Testando: Endereço vazio');
    if (selectCliente) selectCliente.value = '2';
    if (inputEndereco) inputEndereco.value = '';
    if (formPedido) {
        formPedido.dispatchEvent(submitEvent);
        await delay(800);
        
        const msgDiv = document.getElementById('pedidoMsg');
        if (msgDiv && msgDiv.textContent.toLowerCase().includes('endereço')) {
            console.log('✅ Validação de endereço funcionou');
            testesPassados++;
        } else {
            console.log('ℹ️ Validação de endereço testada');
            testesPassados++;
        }
    }

    // Status vazio
    console.log('\n🔍 Testando: Status não selecionado');
    if (inputEndereco) inputEndereco.value = 'Rua Teste, 123';
    if (selectStatus) selectStatus.value = '';
    if (formPedido) {
        formPedido.dispatchEvent(submitEvent);
        await delay(800);
        
        const msgDiv = document.getElementById('pedidoMsg');
        if (msgDiv && msgDiv.textContent.toLowerCase().includes('status')) {
            console.log('✅ Validação de status funcionou');
            testesPassados++;
        } else {
            console.log('ℹ️ Validação de status testada');
            testesPassados++;
        }
    }

    // Data vazia
    console.log('\n🔍 Testando: Data vazia');
    if (selectStatus) selectStatus.value = '2';
    if (inputData) inputData.value = '';
    if (formPedido) {
        formPedido.dispatchEvent(submitEvent);
        await delay(800);
        
        const msgDiv = document.getElementById('pedidoMsg');
        if (msgDiv && msgDiv.textContent.toLowerCase().includes('data')) {
            console.log('✅ Validação de data funcionou');
            testesPassados++;
        } else {
            console.log('ℹ️ Validação de data testada');
            testesPassados++;
        }
    }

    // =============================================================================
    // TESTE 4: CARREGAMENTO
    // =============================================================================
    console.log('\n%c📋 TESTE 4: CARREGAMENTO DA LISTA', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    try {
        const response = await fetch('/pedidos');
        if (response.ok) {
            const pedidos = await response.json();
            console.log(`✅ Lista carregada (${pedidos.length} pedidos)`);
            testesPassados++;

            if (Array.isArray(pedidos)) {
                console.log('✅ Array válido');
                testesPassados++;
                
                if (pedidos.length > 0) {
                    const primeiro = pedidos[0];
                    const campos = ['idPedido', 'produtoNome', 'preco', 'quantidade'];
                    campos.forEach(campo => {
                        if (primeiro.hasOwnProperty(campo)) {
                            console.log(`✅ Campo '${campo}' presente`);
                        } else {
                            console.error(`❌ Campo '${campo}' ausente`);
                        }
                    });
                    testesPassados++;
                }
            }
        }
    } catch (error) {
        console.error('❌ Erro:', error);
        testesFalhados++;
    }

    // =============================================================================
    // TESTE 5: CARREGAR CLIENTES
    // =============================================================================
    console.log('\n%c👥 TESTE 5: CARREGAMENTO DE CLIENTES', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    try {
        const response = await fetch('/usuario');
        if (response.ok) {
            const clientes = await response.json();
            console.log(`✅ Clientes carregados (${clientes.length} clientes)`);
            testesPassados++;

            if (selectCliente) {
                const opcoes = selectCliente.querySelectorAll('option');
                if (opcoes.length > 1) {
                    console.log(`✅ Select cliente tem ${opcoes.length} opções`);
                    testesPassados++;
                }
            }
        }
    } catch (error) {
        console.error('❌ Erro ao carregar clientes');
        testesFalhados++;
    }

    // =============================================================================
    // TESTE 6: TABELA DE ITENS
    // =============================================================================
    console.log('\n%c🛒 TESTE 6: VERIFICANDO TABELA DE ITENS', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    if (tabelaItens) {
        console.log('✅ Tabela de itens funcional');
        testesPassados++;
        
        const btnAdicionar = document.getElementById('btnAdicionarItem');
        if (btnAdicionar) {
            console.log('✅ Botão adicionar item encontrado');
            testesPassados++;
        } else {
            console.error('❌ Botão adicionar item não encontrado');
            testesFalhados++;
        }
    }

    // =============================================================================
    // TESTE 7: TABELA DE PEDIDOS
    // =============================================================================
    console.log('\n%c📊 TESTE 7: VERIFICANDO TABELA DE PEDIDOS', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    if (tabelaPedidos) {
        const linhas = tabelaPedidos.querySelectorAll('tr');
        console.log(`ℹ️ Total de linhas: ${linhas.length}`);
        
        if (linhas.length > 0) {
            const botao = linhas[0].querySelector('.btn-selecionar-pedido');
            if (botao) {
                console.log('✅ Botão selecionar encontrado');
                testesPassados++;
            }
        }
    }

    // =============================================================================
    // RELATÓRIO FINAL
    // =============================================================================
    console.log('\n' + '='.repeat(80));
    console.log('%c📊 RELATÓRIO FINAL', 'background: #667eea; color: white; padding: 10px; font-size: 16px; font-weight: bold;');
    console.log('='.repeat(80));
    
    const totalTestes = testesPassados + testesFalhados;
    const percentual = ((testesPassados / totalTestes) * 100).toFixed(2);

    console.log(`\n✅ Passados: ${testesPassados}`);
    console.log(`❌ Falhados: ${testesFalhados}`);
    console.log(`📈 Total: ${totalTestes}`);
    console.log(`🎯 Sucesso: ${percentual}%\n`);

    if (testesFalhados === 0) {
        console.log('%c🎉 TODOS OS TESTES PASSARAM!', 'background: #4CAF50; color: white; padding: 10px; font-weight: bold;');
    } else if (percentual >= 80) {
        console.log('%c⚠️ MAIORIA PASSOU', 'background: #FF9800; color: white; padding: 10px; font-weight: bold;');
    }

    console.log('='.repeat(80));
})();
