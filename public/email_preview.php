<?php
// Preview de todos os templates de email
$rootPath = dirname(__DIR__);
require_once $rootPath . '/app/core/utils/EmailTemplate.php';

// Dados de exemplo
$nomeUsuario = 'Maria Silva';
$email = 'maria.silva@email.com';
$token = 'ABC123XYZ789';
$numeroPedido = '12345';
$linkLogin = 'http://frsemijoias.ifhost.gru.br/public/views/login.html';
$linkAtivacao = 'http://frsemijoias.ifhost.gru.br/public/views/ativar.html?token=' . $token;
$linkRecuperacao = 'http://frsemijoias.ifhost.gru.br/public/views/recuperar_senha.html?token=' . $token;
$linkPedido = 'http://frsemijoias.ifhost.gru.br/public/views/pedido.html?id=' . $numeroPedido;

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview de Emails - FR Semijoias</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 50px;
        }
        .header h1 {
            font-size: 48px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .header p {
            font-size: 20px;
            opacity: 0.9;
        }
        .email-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .email-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .email-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-welcome { background: #d1ecf1; color: #0c5460; }
        .badge-activation { background: #fff3cd; color: #856404; }
        .badge-confirmation { background: #d4edda; color: #155724; }
        .badge-recovery { background: #f8d7da; color: #721c24; }
        .badge-order { background: #e7d4f5; color: #6f42c1; }
        .email-description {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #667eea;
        }
        .email-description h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .email-description p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        .email-preview {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            background: #f4f4f4;
        }
        .email-preview iframe {
            width: 100%;
            min-height: 600px;
            border: none;
            display: block;
        }
        .back-link {
            text-align: center;
            margin-top: 40px;
        }
        .back-link a {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: all 0.3s;
        }
        .back-link a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Preview de Emails</h1>
            <p>Sistema FR Semijoias - Visualização de Templates</p>
        </div>

        <!-- 1. Email de Boas-vindas -->
        <div class="email-section">
            <div class="email-title">
                🎉 Email de Boas-vindas
                <span class="email-badge badge-welcome">Cadastro Admin</span>
            </div>
            <div class="email-description">
                <h3>📌 Quando é enviado:</h3>
                <p>Após o administrador cadastrar um novo usuário pela página de clientes.</p>
                <h3>✨ Características:</h3>
                <p>• Conta já está ativa (sem necessidade de ativação)</p>
                <p>• Informa dados de acesso (email)</p>
                <p>• Link direto para fazer login</p>
            </div>
            <div class="email-preview">
                <iframe srcdoc="<?php echo htmlspecialchars(\app\core\utils\EmailTemplate::emailBoasVindas($nomeUsuario, $email, $linkLogin)); ?>"></iframe>
            </div>
        </div>

        <!-- 2. Email de Ativação -->
        <div class="email-section">
            <div class="email-title">
                🔓 Email de Ativação de Conta
                <span class="email-badge badge-activation">Cadastro Cliente</span>
            </div>
            <div class="email-description">
                <h3>📌 Quando é enviado:</h3>
                <p>Após o cliente se cadastrar pelo site (página de login/cadastro).</p>
                <h3>✨ Características:</h3>
                <p>• Contém token de ativação único</p>
                <p>• Link para ativação automática</p>
                <p>• Código alternativo em destaque</p>
                <p>• Aviso de validade por tempo limitado</p>
            </div>
            <div class="email-preview">
                <iframe srcdoc="<?php echo htmlspecialchars(\app\core\utils\EmailTemplate::emailAtivacaoConta($nomeUsuario, $linkAtivacao, $token)); ?>"></iframe>
            </div>
        </div>

        <!-- 3. Email de Conta Ativada -->
        <div class="email-section">
            <div class="email-title">
                ✅ Email de Confirmação de Ativação
                <span class="email-badge badge-confirmation">Confirmação</span>
            </div>
            <div class="email-description">
                <h3>📌 Quando é enviado:</h3>
                <p>Após o cliente ativar sua conta com sucesso.</p>
                <h3>✨ Características:</h3>
                <p>• Confirma que a conta foi ativada</p>
                <p>• Link direto para fazer login</p>
                <p>• Mensagem curta e objetiva</p>
            </div>
            <div class="email-preview">
                <iframe srcdoc="<?php echo htmlspecialchars(\app\core\utils\EmailTemplate::emailContaAtivada($nomeUsuario, $linkLogin)); ?>"></iframe>
            </div>
        </div>

        <!-- 4. Email de Recuperação de Senha -->
        <div class="email-section">
            <div class="email-title">
                🔑 Email de Recuperação de Senha
                <span class="email-badge badge-recovery">Segurança</span>
            </div>
            <div class="email-description">
                <h3>📌 Quando é enviado:</h3>
                <p>Quando o usuário solicita recuperação/redefinição de senha.</p>
                <h3>✨ Características:</h3>
                <p>• Link seguro para redefinir senha</p>
                <p>• Token alternativo em destaque</p>
                <p>• Aviso de segurança importante</p>
                <p>• Instruções se não foi o usuário que solicitou</p>
            </div>
            <div class="email-preview">
                <iframe srcdoc="<?php echo htmlspecialchars(\app\core\utils\EmailTemplate::emailRecuperacaoSenha($nomeUsuario, $linkRecuperacao, $token)); ?>"></iframe>
            </div>
        </div>

        <!-- 5. Email de Pedido Realizado -->
        <div class="email-section">
            <div class="email-title">
                📦 Email de Pedido Confirmado
                <span class="email-badge badge-order">E-commerce</span>
            </div>
            <div class="email-description">
                <h3>📌 Quando é enviado:</h3>
                <p>Após o cliente realizar um pedido com sucesso no sistema.</p>
                <h3>✨ Características:</h3>
                <p>• Número do pedido em destaque</p>
                <p>• Link para ver detalhes do pedido</p>
                <p>• Mensagem de confirmação clara</p>
                <p>• Info sobre acompanhamento do status</p>
            </div>
            <div class="email-preview">
                <iframe srcdoc="<?php echo htmlspecialchars(\app\core\utils\EmailTemplate::emailPedidoRealizado($nomeUsuario, $numeroPedido, $linkPedido)); ?>"></iframe>
            </div>
        </div>

        <div class="back-link">
            <a href="/dashboard">← Voltar ao Dashboard</a>
        </div>
    </div>
</body>
</html>
