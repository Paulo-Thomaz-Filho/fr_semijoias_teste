$(document).ready(function() {

    //resquisitar endereço do usuário e preencher os campos do formulário
    $.ajax({
        url: 'usuario/obter-endereco',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
        if (response.endereco) {
            // Formato esperado: "CEP - (12345-678) | CIDADE - Cidade, Estado - Rua, Numero, Bairro, Complemento"
            const endereco = response.endereco;
            
            // 1. Quebrar pelas partes principais (CEP e CIDADE/ENDEREÇO)
            const enderecoParts = endereco.split('|');
            
            if (enderecoParts.length < 2) {
                console.error('Formato de endereço inválido.');
                return;
            }

            // --- Extração do CEP (Parte 1) ---
            const cepPart = enderecoParts[0].trim();
            const cepMatch = cepPart.match(/\(([^)]+)\)/);
            const cep = cepMatch ? cepMatch[1] : '';

            // --- Extração dos Dados do Endereço (Parte 2) ---
            const cidadePart = enderecoParts[1].trim();
            const enderecoDet = cidadePart.replace('CIDADE - ', '').trim();
            
            // Quebrar a parte do endereço principal: "Cidade, Estado - Rua, Numero, Bairro, Complemento"
            const [cidadeEstadoPart, ruaNumBairroCompPart] = enderecoDet.split(' - ');

            // Quebrar Cidade e Estado: "Cidade, Estado"
            const cidadeEstadoArr = cidadeEstadoPart.split(',');
            const cidade = cidadeEstadoArr[0] ? cidadeEstadoArr[0].trim() : '';
            const estado = cidadeEstadoArr[1] ? cidadeEstadoArr[1].trim() : '';

            // Quebrar Rua, Número, Bairro, Complemento
            const ruaNumBairroCompArr = ruaNumBairroCompPart.split(',');
            
            // Correção dos índices:
            const rua         = ruaNumBairroCompArr[0] ? ruaNumBairroCompArr[0].trim() : '';
            const numero      = ruaNumBairroCompArr[1] ? ruaNumBairroCompArr[1].trim() : '';
            const bairro      = ruaNumBairroCompArr[2] ? ruaNumBairroCompArr[2].trim() : '';
            const complemento = ruaNumBairroCompArr[3] ? ruaNumBairroCompArr[3].trim() : ''; // Opcional

            // 2. Preencher os campos do formulário
            $('#inputCEP').val(cep);
            $('#inputCidade').val(cidade);
            $('#inputEstado').val(estado); 
            $('#inputRua').val(rua);
            $('#inputNumero').val(numero);
            $('#inputBairro').val(bairro);
            $('#inputComplemento').val(complemento);
        }
    },
    error: function(xhr, status, error) {
        console.error('Erro ao obter o endereço do usuário:', error);
    }
    });

    $('#btn-salvar-endereco').click(function(event) {
        event.preventDefault(); // Evita o envio padrão do formulário
        const enderecoData = {
            cep: $('#inputCEP').val(),
            rua: $('#inputRua').val(),
            numero: $('#inputNumero').val(),
            complemento: $('#inputComplemento').val(),
            bairro: $('#inputBairro').val(),
            cidade: $('#inputCidade').val(),
            estado: $('#inputEstado').val()
        };
        // formatar em uma string o endereço completo
        const enderecoCompleto = `CEP - (${enderecoData.cep}) | CIDADE - ${enderecoData.cidade}, ${enderecoData.estado} - ${enderecoData.rua}, ${enderecoData.numero}, ${enderecoData.bairro}, ${enderecoData.complemento}`;
        // envia a variavel enderecoCompleto para o backend via json
        $.ajax({
            url: 'usuario/atualizar-endereco',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ endereco: enderecoCompleto }),
            success: function(response) {
                alert('Endereço atualizado com sucesso!');
            },
            error: function(xhr, status, error) {
                alert('Erro ao atualizar o endereço. Tente novamente.');
            }
        });
    });
});