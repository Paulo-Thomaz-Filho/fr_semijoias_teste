$(document).ready(function(){
    
    $('#formCadastro').on('submit', function(event){
        event.preventDefault();
        
        const nome = $('#cadastroNome').val();
        const email = $('#cadastroEmail').val();
        const senha = $('#cadastroSenha').val();
        const telefone = $('#cadastroTelefone').val();
        const cpf = $('#cadastroCpf').val();
        const endereco = $('#cadastroEndereco').val();

        if (!nome || !email || !senha || !telefone || !cpf || !endereco) {
            alert('Por favor, preencha todos os campos de cadastro');
            return;
        }

        // Validação básica de senha
        if (senha.length < 6) {
            alert('A senha deve ter no mínimo 6 caracteres');
            return;
        }

        const cadastroData = {
            nome: nome,
            email: email,
            senha: senha,
            telefone: telefone,
            cpf: cpf,
            endereco: endereco
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
                    alert('Cadastro realizado com sucesso! Você já pode fazer login.');
                    // Limpar formulário
                    $('#formCadastro')[0].reset();
                    // Voltar para o login
                    $('#signIn').click();
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
