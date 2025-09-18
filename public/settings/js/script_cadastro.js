$(document).ready(function(){
    
    $('#formCadastro').on('submit', function(event){
        event.preventDefault();
        
        const nome = $('#cadastroNome').val();
        const email = $('#cadastroEmail').val();
        const senha = $('#cadastroSenha').val();

        if (!nome || !email || !senha) {
            alert('Porfavor, preencha todos os campos de cadastro');
            return;
        }

        const cadastroData = {
            nome: nome,
            email: email,
            senha: senha
        }

        const jsonData = JSON.stringify(cadastroData);

        console.log('Enviando dados do cadastro via JSON') 

        $.ajax({
            url: 'api/usuario/registrar',
            type: 'POST',
            data: cadastroData,

            success: function(response) {
                console.log('Resposta do servidor:', response);
                if (response.success) {
                    alert('Cadastro realizado com sucesso!');
                    window.location.href = 'login'; 
                } else {
                    alert('Erro ao cadastrar: ' + (response.error || 'Verifique os dados e tente novamente.'));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Erro na requisição de cadastro:', textStatus, errorThrown);
                alert('Ocorreu um erro de comunicação. Tente mais tarde.');
            }
        });
    });
});
