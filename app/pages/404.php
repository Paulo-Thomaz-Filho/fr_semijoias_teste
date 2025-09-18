<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro 404 - Página Não Encontrada</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* CSS customizado */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa; /* Cor de fundo suave */
        }

        .container-404 {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
        }

        .error-code {
            font-size: 10rem; /* Tamanho grande para o "404" */
            font-weight: 700;
            color: #6c757d; /* Cinza do Bootstrap */
            text-shadow: 4px 4px 0px #dee2e6; /* Sombra sutil para dar profundidade */
            line-height: 1;
        }

        .error-message {
            font-size: 1.75rem;
            font-weight: 400;
            color: #343a40;
            margin-top: 0;
        }

        .error-description {
            font-size: 1rem;
            color: #6c757d;
        }

        .btn-home {
            transition: all 0.3s ease; /* Efeito de transição suave */
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        .btn-home:hover {
            transform: translateY(-5px); /* Efeito de "levantar" o botão */
            box-shadow: 0 10px 20px rgba(0,0,0,0.1); /* Sombra ao passar o mouse */
        }
    </style>
</head>
<body>

    <div class="container-404">
        <div class="col-md-8">
            <div class="error-code">404</div>
            <p class="error-message">Oops! Página não encontrada.</p>
            <p class="error-description mb-4">
                A página que você está procurando não existe, foi removida ou está temporariamente indisponível.
            </p>
            <a href="/FR_Semijoias_teste" class="btn btn-primary btn-lg btn-home">
                Voltar para a Página Inicial
            </a>
        </div>
    </div>

</body>
</html>