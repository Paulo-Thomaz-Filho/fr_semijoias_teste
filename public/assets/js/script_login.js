$(document).ready(function() {

    $('#signUp').on('click', function() {
        $('#container').addClass('right-panel-active');
    });

    $('#signIn').on('click', function() {
        $('#container').removeClass('right-panel-active');
    });

    $('#loginForm').on('submit', function(event){
        event.preventDefault();

        const email = $('#loginEmail').val();
        const senha = $('#loginPassword').val();

        if (!email || !senha) {
            alert('Por favor, digite seu e-mail e senha.');
            return;
        }

        const loginData = {
            email: email,
            senha: senha
        };

        $.ajax({
            url: '/api/usuario/login',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(loginData),

            success: function(response) {
                console.log('Resposta do servidor:', response);

                if (response.sucesso) {
                    // Salvar o nome do usuário na sessionStorage
                    if (response.usuario_nome) {
                        sessionStorage.setItem('usuario_nome', response.usuario_nome);
                    }
                    alert('Login bem-sucedido!');
                    window.location.href = '/inicio'; 
                } else {
                    alert('Falha no login: ' + (response.erro || 'Credenciais inválidas.'));
                }
            },

            error: function(jqXHR) {
                const errorResponse = jqXHR.responseJSON;
                if (errorResponse && errorResponse.erro) {
                    alert('Falha no login: ' + errorResponse.erro);
                } else {
                    alert('Ocorreu um erro de comunicação. Tente novamente.');
                }
            }
        });
    });
});