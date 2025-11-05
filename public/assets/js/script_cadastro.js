$(document).ready(function(){
    
    $('#formCadastro').on('submit', function(event){
        event.preventDefault();
        
        const nome = $('#cadastroNome').val();
        const email = $('#cadastroEmail').val();
        const senha = $('#cadastroSenha').val();


        if (!nome || !email || !senha) {
            alert('Por favor, preencha todos os campos de cadastro');
            return;
        }

        // Validação de senha forte
        const senhaForteRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]).{8,}$/;
        if (!senhaForteRegex.test(senha)) {
            alert('A senha deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma minúscula e um caractere especial.');
            return;
        }

        const cadastroData = {
            nome: nome,
            email: email,
            senha: senha
        }

        console.log('Enviando dados do cadastro via JSON');

        $.ajax({
            url: 'usuario/salvar',
            type: 'POST',
            contentType: 'application/json', // INFORMA que estamos enviando JSON
            data: JSON.stringify(cadastroData), // CONVERTE o objeto para uma string JSON

            success: function(response) {
                console.log('Resposta do servidor:', response);
                // O salvar.php retorna {sucesso: "mensagem", id: ...} ou {erro: "mensagem"}
                if (response.sucesso) { 
                    alert('Cadastro realizado com sucesso!');
                    window.location.href = '/login'; 
                } else {
                    alert('Erro ao cadastrar: ' + (response.erro || 'Verifique os dados e tente novamente.'));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                const errorResponse = jqXHR.responseJSON;
                if (errorResponse && errorResponse.erro) {
                    alert('Falha no cadastro: ' + errorResponse.erro);
                } else {
                    alert('Ocorreu um erro de comunicação. Tente novamente.');
                }
            }
        });
    });
});
