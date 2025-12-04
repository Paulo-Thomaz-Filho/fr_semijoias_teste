$(document).ready(function() {
    
    function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');
        if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

        let soma = 0;
        for (let i = 0; i < 9; i++)
            soma += parseInt(cpf.charAt(i)) * (10 - i);
        let resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(9))) return false;

        soma = 0;
        for (let i = 0; i < 10; i++)
            soma += parseInt(cpf.charAt(i)) * (11 - i);
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(10))) return false;

        return true;
    }
    
    //resquisitar dados do usuário (nome, email, cpf, data de nacimento, telefone) e preencher os campos do formulário
    $.ajax({
        url: 'usuario/buscar-dados',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            
            const dados = response.dados
            //preencher os campos do formulário com os dados obtidos
            $('#nome').val(dados.nome);
            $('#email').val(dados.email);
            $('#cpf').val(dados.cpf);
            $('#nascimento').val(dados.nascimento);
            $('#telefone').val(dados.telefone);       
        },
        error: function(xhr, status, error) {
            alert('Erro ao carregar os dados do usuário: ' + error);
        }
    });


    // Evento de clique no botão de salvar alterações
    $('#salvar_alteracao').click(function() {
        var cpf = $('#cpf').val();
        var nome = $('#nome').val();
        var email = $('#email').val();
        var nascimento = $('#nascimento').val();
        var telefone = $('#telefone').val();

        var cpfLimpo = cpf.replace(/[^\d]+/g, '');

        if (!validarCPF(cpfLimpo)) {
            alert('Erro: O número de CPF informado é inválido. Por favor, verifique e tente novamente.');
            $('#cpf').focus(); 
            return;
        }

        // Enviar os dados via AJAX
        $.ajax({
            url: '/usuario/atualizar-dados',
            type: 'POST',
            data: {
                nome: nome,
                email: email,
                cpf: cpf,
                nascimento: nascimento,
                telefone: telefone
            },
            success: function(response) {
                alert('Dados atualizados com sucesso!');
            },
            error: function(xhr, status, error) {
                alert('Erro ao atualizar os dados: ' + error);
            }
        });
    });
});