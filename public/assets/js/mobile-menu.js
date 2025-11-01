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
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 12H21M3 6H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
