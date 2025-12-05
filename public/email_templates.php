
<?php
require_once __DIR__ . '/../app/core/utils/EmailTemplate.php';
use app\core\utils\EmailTemplate;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Templates de Email</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 32px; }
        h1 { text-align: center; margin-bottom: 32px; color: #333; }
        .template-block { margin-bottom: 48px; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden; }
        .template-title { background: #6c757d; color: #fff; padding: 18px 24px; font-size: 20px; font-weight: 600; }
        .template-preview { padding: 24px; background: #fafafa; }
        .template-preview-inner { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 0; overflow-x: auto; }
        @media (max-width: 600px) { .container { padding: 8px; } .template-preview { padding: 8px; } }
    </style>
</head>
<body>
    <div class="container">
        <h1>Templates de Email Disponíveis</h1>
        <div class="template-block">
            <div class="template-title">Ativação de Conta</div>
            <div class="template-preview">
                <div class="template-preview-inner">
                    <?php echo EmailTemplate::emailAtivacaoConta('Usuário Exemplo', 'https://frsemijoias.ifhost.gru.br/ativar?token=123', '123456'); ?>
                </div>
            </div>
        </div>
        <div class="template-block">
            <div class="template-title">Conta Ativada</div>
            <div class="template-preview">
                <div class="template-preview-inner">
                    <?php echo EmailTemplate::emailContaAtivada('Usuário Exemplo', 'https://frsemijoias.ifhost.gru.br/login'); ?>
                </div>
            </div>
        </div>
        <div class="template-block">
            <div class="template-title">Recuperação de Senha</div>
            <div class="template-preview">
                <div class="template-preview-inner">
                    <?php echo EmailTemplate::emailRecuperacaoSenha('Usuário Exemplo', 'https://frsemijoias.ifhost.gru.br/recuperar?token=123', '654321'); ?>
                </div>
            </div>
        </div>
        <div class="template-block">
            <div class="template-title">Boas-Vindas (Cadastro pelo Admin)</div>
            <div class="template-preview">
                <div class="template-preview-inner">
                    <?php echo EmailTemplate::emailBoasVindas('Usuário Exemplo', 'usuario@exemplo.com', 'https://frsemijoias.ifhost.gru.br/login'); ?>
                </div>
            </div>
        </div>
        <div class="template-block">
            <div class="template-title">Pedido Realizado</div>
            <div class="template-preview">
                <div class="template-preview-inner">
                    <?php echo EmailTemplate::emailPedidoRealizado('Usuário Exemplo', '12345', 'https://frsemijoias.ifhost.gru.br/pedido.html?id=12345'); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
