document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (sidebarToggle) {
        // Evento para abrir a sidebar
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.add('is-open');
            sidebarOverlay.classList.add('is-active');
        });
    }

    if (sidebarOverlay) {
        // Evento para fechar a sidebar ao clicar no overlay
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('is-open');
            sidebarOverlay.classList.remove('is-active');
        });
    }
});