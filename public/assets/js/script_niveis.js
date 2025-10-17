// Popula o dropdown de nível de acesso com dados do backend

document.addEventListener('DOMContentLoaded', function() {
    const selectNivel = document.getElementById('nivel_cliente');
    if (!selectNivel) return;

    async function carregarNiveis() {
        try {
            const response = await fetch('/niveis');
            const niveis = await response.json();
            selectNivel.innerHTML = '<option value="" disabled selected>Selecione o nível de acesso</option>';
            niveis.forEach(nivel => {
                const option = document.createElement('option');
                    option.value = nivel.idNivel || nivel.id_nivel;
                option.textContent = nivel.tipo;
                selectNivel.appendChild(option);
            });
                // Dispara evento customizado quando os níveis são carregados
                selectNivel.dispatchEvent(new CustomEvent('niveisLoaded'));
        } catch (error) {
            selectNivel.innerHTML = '<option value="">Erro ao carregar níveis</option>';
        }
    }

    carregarNiveis();
});
