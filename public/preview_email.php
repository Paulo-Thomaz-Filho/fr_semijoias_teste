<?php
/**
 * Arquivo para visualizar os templates de email
 * Acesse: http://localhost/preview_email.php
 */

require_once __DIR__ . '/../app/core/utils/EmailTemplate.php';

use app\core\utils\EmailTemplate;

// Dados de exemplo
$nomeUsuario = "João Silva";
$linkAtivacao = "http://localhost/ativar?token=A1B2C3";
$token = "A1B2C3";
$linkLogin = "http://localhost/login";

// Determinar qual template mostrar
$template = $_GET['template'] ?? 'ativacao';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview de Email - FR Semijoias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .controls {
            max-width: 800px;
            margin: 0 auto 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .controls h2 {
            margin-top: 0;
            color: #667eea;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
        }
        .btn:hover {
            background: #5568d3;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .info {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="controls">
        <h2>📧 Preview de Templates de Email</h2>
        <p>Selecione um template para visualizar:</p>
        <a href="?template=ativacao" class="btn <?= $template === 'ativacao' ? '' : 'btn-secondary' ?>">
            🔓 Ativação de Conta
        </a>
        <a href="?template=ativada" class="btn <?= $template === 'ativada' ? '' : 'btn-secondary' ?>">
            ✅ Conta Ativada
        </a>
        <a href="?template=recuperacao" class="btn <?= $template === 'recuperacao' ? '' : 'btn-secondary' ?>">
            🔑 Recuperação de Senha
        </a>
        <a href="?template=pedido" class="btn <?= $template === 'pedido' ? '' : 'btn-secondary' ?>">
            🛍️ Pedido Realizado
        </a>
    </div>

    <div class="preview-container">
        <div class="info">
            <strong>ℹ️ Informação:</strong> Este é apenas um preview. O email real será enviado via SMTP com estes templates.
        </div>

        <?php
        switch ($template) {
            case 'ativacao':
                echo "<h3>📧 Email de Ativação de Conta</h3>";
                echo EmailTemplate::emailAtivacaoConta($nomeUsuario, $linkAtivacao, $token);
                break;

            case 'ativada':
                echo "<h3>📧 Email de Conta Ativada</h3>";
                echo EmailTemplate::emailContaAtivada($nomeUsuario, $linkLogin);
                break;

            case 'recuperacao':
                echo "<h3>📧 Email de Recuperação de Senha</h3>";
                $linkRecuperacao = "http://localhost/recuperar-senha?token=X1Y2Z3";
                $tokenRecuperacao = "X1Y2Z3";
                echo EmailTemplate::emailRecuperacaoSenha($nomeUsuario, $linkRecuperacao, $tokenRecuperacao);
                break;

            case 'pedido':
                echo "<h3>📧 Email de Pedido Realizado</h3>";
                $numeroPedido = 12345;
                $linkPedido = "http://localhost/pedido/12345";
                echo EmailTemplate::emailPedidoRealizado($nomeUsuario, $numeroPedido, $linkPedido);
                break;

            default:
                echo "<p>Template não encontrado.</p>";
        }
        ?>
    </div>

    <div style="text-align: center; margin-top: 30px; color: #666;">
        <p>FR Semijoias © <?= date('Y') ?></p>
        <p><small>Arquivo: public/preview_email.php</small></p>
    </div>
</body>
</html>
