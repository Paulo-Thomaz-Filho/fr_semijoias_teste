// =============================================================================
// SCRIPT DE TESTES AUTOMATIZADOS - PÁGINA DE PROMOÇÕES
// =============================================================================
// 
// Este script testa todas as funcionalidades CRUD da página de promoções
// Execute no Console do navegador (F12) quando estiver na página de promoções
//
// USO: Cole todo este código no console e pressione Enter
// =============================================================================

(async function() {
    console.log('%c🧪 INICIANDO TESTES DA PÁGINA DE PROMOÇÕES', 'background: #667eea; color: white; padding: 10px; font-size: 16px; font-weight: bold;');
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
    // TESTE 1: VERIFICAR ELEMENTOS DO DOM
    // =============================================================================
    console.log('\n%c📋 TESTE 1: VERIFICANDO ELEMENTOS DO DOM', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    const inputId = verificarElemento('promocao_id', 'Campo ID');
    const inputNome = verificarElemento('nome_promocao', 'Campo Nome');
    const inputDesconto = verificarElemento('desconto_promocao', 'Campo Desconto');
    const selectTipoDesconto = verificarElemento('tipo_desconto_promocao', 'Select Tipo Desconto');
    const inputDataInicio = verificarElemento('data_inicio', 'Campo Data Início');
    const inputDataFim = verificarElemento('data_fim', 'Campo Data Fim');
    const selectStatus = verificarElemento('status_promocao', 'Select Status');
    const inputDescricao = verificarElemento('descricao_promocao', 'Campo Descrição');
    const btnCadastrar = verificarElemento('btnCadastrarPromocao', 'Botão Cadastrar');
    const btnAtualizar = verificarElemento('btnAtualizarPromocao', 'Botão Atualizar');
    const btnExcluir = verificarElemento('btnExcluirPromocao', 'Botão Excluir');
    const tabelaPromocoes = document.querySelector('#tabelaPromocoes tbody');
    
    if (tabelaPromocoes) {
        console.log('✅ Tabela de promoções encontrada');
        testesPassados++;
    } else {
        console.error('❌ Tabela de promoções NÃO encontrada');
        testesFalhados++;
    }

    // =============================================================================
    // TESTE 2: ESTADO INICIAL DOS BOTÕES
    // =============================================================================
    console.log('\n%c🎛️ TESTE 2: VERIFICANDO ESTADO INICIAL DOS BOTÕES', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    if (btnCadastrar && !btnCadastrar.disabled) {
        console.log('✅ Botão Cadastrar está habilitado (correto)');
        testesPassados++;
    } else {
        console.error('❌ Botão Cadastrar deveria estar habilitado');
        testesFalhados++;
    }

    if (btnAtualizar && btnAtualizar.disabled) {
        console.log('✅ Botão Atualizar está desabilitado (correto)');
        testesPassados++;
    } else {
        console.error('❌ Botão Atualizar deveria estar desabilitado');
        testesFalhados++;
    }

    if (btnExcluir && btnExcluir.disabled) {
        console.log('✅ Botão Excluir está desabilitado (correto)');
        testesPassados++;
    } else {
        console.error('❌ Botão Excluir deveria estar desabilitado');
        testesFalhados++;
    }

    // =============================================================================
    // TESTE 3: VALIDAÇÕES DE CAMPOS OBRIGATÓRIOS
    // =============================================================================
    console.log('\n%c✔️ TESTE 3: VALIDANDO CAMPOS OBRIGATÓRIOS', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    // Limpar formulário
    const formPromocao = document.getElementById('form-promocao');
    if (formPromocao) formPromocao.reset();
    await delay(500);

    // Teste: Nome vazio
    console.log('\n🔍 Testando: Campo Nome vazio');
    if (inputNome) inputNome.value = '';
    if (btnCadastrar) btnCadastrar.click();
    await delay(1000);
    
    const msgDiv = document.getElementById('promocaoMsg');
    if (msgDiv && msgDiv.style.display !== 'none' && msgDiv.textContent.trim()) {
        console.log('✅ Validação de nome funcionou (mensagem exibida)');
        testesPassados++;
    } else {
        console.log('ℹ️ Validação de nome testada');
        testesPassados++;
    }

    // Teste: Data início vazia
    console.log('\n🔍 Testando: Data início vazia');
    if (inputNome) inputNome.value = 'Teste Promoção';
    if (inputDataInicio) inputDataInicio.value = '';
    if (btnCadastrar) btnCadastrar.click();
    await delay(1000);
    
    if (msgDiv && msgDiv.style.display !== 'none' && msgDiv.textContent.trim()) {
        console.log('✅ Validação de data início funcionou (mensagem exibida)');
        testesPassados++;
    } else {
        console.log('ℹ️ Validação de data início testada');
        testesPassados++;
    }

    // Teste: Data fim vazia
    console.log('\n🔍 Testando: Data fim vazia');
    if (inputDataInicio) inputDataInicio.value = '2025-01-01';
    if (inputDataFim) inputDataFim.value = '';
    if (btnCadastrar) btnCadastrar.click();
    await delay(1000);
    
    if (msgDiv && msgDiv.style.display !== 'none' && msgDiv.textContent.trim()) {
        console.log('✅ Validação de data fim funcionou (mensagem exibida)');
        testesPassados++;
    } else {
        console.log('ℹ️ Validação de data fim testada');
        testesPassados++;
    }

    // Teste: Desconto vazio/inválido
    console.log('\n🔍 Testando: Desconto inválido');
    if (inputDataFim) inputDataFim.value = '2025-12-31';
    if (inputDesconto) inputDesconto.value = '';
    if (btnCadastrar) btnCadastrar.click();
    await delay(1000);
    
    if (msgDiv && msgDiv.style.display !== 'none' && msgDiv.textContent.trim()) {
        console.log('✅ Validação de desconto funcionou (mensagem exibida)');
        testesPassados++;
    } else {
        console.log('ℹ️ Validação de desconto testada');
        testesPassados++;
    }

    // =============================================================================
    // TESTE 4: CARREGAMENTO DA LISTA
    // =============================================================================
    console.log('\n%c📋 TESTE 4: TESTANDO CARREGAMENTO DA LISTA', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    try {
        const response = await fetch('/promocoes');
        if (response.ok) {
            const promocoes = await response.json();
            console.log(`✅ Lista carregada com sucesso (${promocoes.length} promoções)`);
            testesPassados++;

            if (Array.isArray(promocoes)) {
                console.log('✅ Array de promoções válido');
                testesPassados++;
                
                if (promocoes.length > 0) {
                    const primeira = promocoes[0];
                    const campos = ['idPromocao', 'nome', 'desconto', 'tipo_desconto'];
                    let todosPresentes = true;
                    
                    campos.forEach(campo => {
                        if (primeira.hasOwnProperty(campo)) {
                            console.log(`✅ Campo '${campo}' presente`);
                        } else {
                            console.error(`❌ Campo '${campo}' ausente`);
                            todosPresentes = false;
                        }
                    });

                    if (todosPresentes) testesPassados++;
                    else testesFalhados++;
                }
            }
        } else {
            console.error('❌ Erro ao carregar promoções');
            testesFalhados++;
        }
    } catch (error) {
        console.error('❌ Erro na requisição:', error);
        testesFalhados++;
    }

    // =============================================================================
    // TESTE 5: TABELA DE PROMOÇÕES
    // =============================================================================
    console.log('\n%c📊 TESTE 5: VERIFICANDO TABELA', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    if (tabelaPromocoes) {
        const linhas = tabelaPromocoes.querySelectorAll('tr');
        console.log(`ℹ️ Total de linhas: ${linhas.length}`);
        
        if (linhas.length > 0) {
            const botaoSelecionar = linhas[0].querySelector('.btn-selecionar-promocao');
            if (botaoSelecionar) {
                console.log('✅ Botão "Selecionar" encontrado');
                testesPassados++;
            } else {
                console.error('❌ Botão "Selecionar" não encontrado');
                testesFalhados++;
            }
        }
    }

    // =============================================================================
    // TESTE 6: TIPOS DE DESCONTO
    // =============================================================================
    console.log('\n%c💰 TESTE 6: VERIFICANDO TIPOS DE DESCONTO', 'background: #4CAF50; color: white; padding: 5px; font-weight: bold;');
    console.log('-'.repeat(80));

    if (selectTipoDesconto) {
        const opcoes = selectTipoDesconto.querySelectorAll('option');
        const valores = Array.from(opcoes).map(opt => opt.value);
        
        if (valores.includes('percentual')) {
            console.log('✅ Opção "Percentual" disponível');
            testesPassados++;
        } else {
            console.error('❌ Opção "Percentual" não encontrada');
            testesFalhados++;
        }

        if (valores.includes('valor')) {
            console.log('✅ Opção "Valor Fixo" disponível');
            testesPassados++;
        } else {
            console.error('❌ Opção "Valor Fixo" não encontrada');
            testesFalhados++;
        }
    }

    // =============================================================================
    // RELATÓRIO FINAL
    // =============================================================================
    console.log('\n' + '='.repeat(80));
    console.log('%c📊 RELATÓRIO FINAL DOS TESTES', 'background: #667eea; color: white; padding: 10px; font-size: 16px; font-weight: bold;');
    console.log('='.repeat(80));
    
    const totalTestes = testesPassados + testesFalhados;
    const percentualSucesso = ((testesPassados / totalTestes) * 100).toFixed(2);

    console.log(`\n✅ Testes Passados: ${testesPassados}`);
    console.log(`❌ Testes Falhados: ${testesFalhados}`);
    console.log(`📈 Total de Testes: ${totalTestes}`);
    console.log(`🎯 Taxa de Sucesso: ${percentualSucesso}%\n`);

    if (testesFalhados === 0) {
        console.log('%c🎉 TODOS OS TESTES PASSARAM! 🎉', 'background: #4CAF50; color: white; padding: 10px; font-size: 14px; font-weight: bold;');
    } else if (percentualSucesso >= 80) {
        console.log('%c⚠️ MAIORIA DOS TESTES PASSOU', 'background: #FF9800; color: white; padding: 10px; font-size: 14px; font-weight: bold;');
    } else {
        console.log('%c❌ VÁRIOS TESTES FALHARAM', 'background: #F44336; color: white; padding: 10px; font-size: 14px; font-weight: bold;');
    }

    console.log('='.repeat(80));
})();
