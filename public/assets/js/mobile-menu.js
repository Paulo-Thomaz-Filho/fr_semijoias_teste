// =============================================================================
// MENU MOBILE - CONTROLE DE SIDEBAR RESPONSIVA
// =============================================================================

(function() {
    'use strict';

    // Criar elementos do menu mobile
    function criarMenuMobile() {
        // Botão hamburger
        const btnToggle = document.createElement('button');
        btnToggle.className = 'mobile-menu-toggle';
        btnToggle.setAttribute('aria-label', 'Abrir menu');
        btnToggle.innerHTML = `
            <svg width="24" height="24" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="none">
                <path fill="currentColor" fill-rule="evenodd" d="M19 4a1 1 0 01-1 1H2a1 1 0 010-2h16a1 1 0 011 1zm0 6a1 1 0 01-1 1H2a1 1 0 110-2h16a1 1 0 011 1zm-1 7a1 1 0 100-2H2a1 1 0 100 2h16z"/>
            </svg>
        `;

        // Overlay
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';

        // Adicionar ao body
        document.body.appendChild(btnToggle);
        document.body.appendChild(overlay);

        return { btnToggle, overlay };
    }

    // Inicializar quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        const aside = document.querySelector('aside');
        
        if (!aside) return;

        const { btnToggle, overlay } = criarMenuMobile();

        // Abrir menu
        function abrirMenu() {
            aside.classList.add('show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
            btnToggle.setAttribute('aria-label', 'Fechar menu');
        }

        // Fechar menu
        function fecharMenu() {
            aside.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            btnToggle.setAttribute('aria-label', 'Abrir menu');
        }

        // Toggle menu
        btnToggle.addEventListener('click', function() {
            if (aside.classList.contains('show')) {
                fecharMenu();
            } else {
                abrirMenu();
            }
        });

        // Fechar ao clicar no overlay
        overlay.addEventListener('click', fecharMenu);

        // Fechar ao clicar em um link do menu
        const menuLinks = aside.querySelectorAll('nav a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Pequeno delay para permitir a navegação
                setTimeout(fecharMenu, 150);
            });
        });

        // Fechar ao pressionar ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && aside.classList.contains('show')) {
                fecharMenu();
            }
        });

        // Fechar menu ao redimensionar para desktop
        let timeoutResize;
        window.addEventListener('resize', function() {
            clearTimeout(timeoutResize);
            timeoutResize = setTimeout(function() {
                if (window.innerWidth > 992 && aside.classList.contains('show')) {
                    fecharMenu();
                }
            }, 250);
        });
    });
})();
