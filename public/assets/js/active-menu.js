// Script para marcar o item ativo no menu sidebar
function marcarMenuAtivo() {
    const currentPath = window.location.pathname;
    
    // Remove a classe ativa de todos os links
    const allLinks = document.querySelectorAll('aside nav a');
    
    allLinks.forEach(link => {
        link.classList.remove('active-page');
    });
    
    // Adiciona a classe ativa no link correspondente à página atual
    let activeLink = null;
    
    if (currentPath === '/' || currentPath === '/dashboard') {
        activeLink = document.querySelector('aside nav a[href="/dashboard"]');
    } else if (currentPath === '/cliente') {
        activeLink = document.querySelector('aside nav a[href="/cliente"]');
    } else if (currentPath === '/produto') {
        activeLink = document.querySelector('aside nav a[href="/produto"]');
    } else if (currentPath === '/pedido') {
        activeLink = document.querySelector('aside nav a[href="/pedido"]');
    } else if (currentPath === '/promocao') {
        activeLink = document.querySelector('aside nav a[href="/promocao"]');
    }
    
    if (activeLink) {
        activeLink.classList.add('active-page');
    }
}

// Executar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', marcarMenuAtivo);
} else {
    marcarMenuAtivo();
}
