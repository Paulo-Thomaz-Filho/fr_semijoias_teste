$(document).ready(function () {

    // --- Funcionalidade para mostrar/ocultar a senha (sem alterações) ---
    $('.toggle-password').on('click', function() {
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
        const passwordInput = $(this).prev('input');
        const currentType = passwordInput.attr('type');
        if (currentType === 'password') {
            passwordInput.attr('type', 'text');
        } else {
            passwordInput.attr('type', 'password');
        }
    });
  
    // --- Lógica de envio do formulário via AJAX ---
    $('.login-form').on('submit', function (event) {
        event.preventDefault();
  
        const email = $('#email').val();
        const password = $('#password').val();
        const mensagemErro = $('#mensagemErro');
        
        // Garante que a mensagem de erro esteja oculta ao tentar fazer login novamente
        mensagemErro.addClass('d-none');
    
        $.ajax({
            url: 'app/php/login.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                email: email,
                senha: password,
            }),
        })
        .done(function (response) {
            if (response.success) {
                // Se o login for bem-sucedido, redireciona para a página de administração
                window.location.href = 'inicio'; 
            } else {
                // Se falhar, exibe a mensagem de erro retornada pelo PHP
                mensagemErro.text(response.error || 'Credenciais inválidas.').removeClass('d-none');
            }
        })
        .fail(function () {
            // Em caso de falha na comunicação (ex: servidor offline)
            mensagemErro.text('Erro de comunicação com o servidor. Tente novamente.').removeClass('d-none');
        });
    });
});