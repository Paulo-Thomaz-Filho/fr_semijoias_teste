// =============================================================================
// SCRIPT DE GERENCIAMENTO DE NÍVEIS DE ACESSO
// =============================================================================

// =============================================================================
// INICIALIZAÇÃO
// =============================================================================

document.addEventListener('DOMContentLoaded', function() {
    
    // -------------------------------------------------------------------------
    // ELEMENTOS DO DOM
    // -------------------------------------------------------------------------
    
    const selectNivel = document.getElementById('nivel_cliente');
    
    // Verificar se o elemento existe na página
    if (!selectNivel) return;
    
    // -------------------------------------------------------------------------
    // FUNÇÕES DE CARREGAMENTO DE DADOS
    // -------------------------------------------------------------------------
    
    /**
     * Carrega os níveis de acesso do backend e popula o select
     */
    const carregarNiveis = async () => {
        try {
            const response = await fetch('/niveis');
            const niveis = await response.json();
            
            // Limpar e adicionar opção padrão
            selectNivel.innerHTML = '<option value="" disabled selected>Selecione o nível de acesso</option>';
            
            // Adicionar cada nível como opção
            niveis.forEach(nivel => {
                const option = document.createElement('option');
                option.value = nivel.idNivel || nivel.id_nivel;
                option.textContent = nivel.tipo;
                selectNivel.appendChild(option);
            });
            
            // Disparar evento customizado quando os níveis são carregados
            selectNivel.dispatchEvent(new CustomEvent('niveisLoaded'));
            
        } catch (error) {
            console.error('Erro ao carregar níveis:', error);
            selectNivel.innerHTML = '<option value="">Erro ao carregar níveis</option>';
        }
    };
    
    // -------------------------------------------------------------------------
    // EXECUÇÃO INICIAL
    // -------------------------------------------------------------------------
    
    carregarNiveis();
    
});
