<?php
header('Content-Type: application/json; charset=utf-8');

$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/vendor/autoload.php';
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';
require_once $rootPath . '/app/core/utils/CodeGenerator.php';
require_once $rootPath . '/app/core/utils/Mail.php';
require_once $rootPath . '/app/core/utils/EmailTemplate.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$nome            = $_POST['nome']            ?? null;
$email           = $_POST['email']           ?? null;
$senha           = $_POST['senha']           ?? null;
$cpf             = $_POST['cpf']             ?? null;
$telefone        = $_POST['telefone']        ?? null;
$dataNascimento  = $_POST['data_nascimento'] ?? null;
$endereco        = $_POST['endereco']        ?? null;
$id_nivel        = $_POST['id_nivel']        ?? 2;    // 2 para novos clientes

if (!$nome || !$email || !$senha) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Nome, email e senha são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $usuarioDAO = new \app\models\UsuarioDAO();

    if ($usuarioDAO->getByEmail($email)) {
        http_response_code(409);
        echo json_encode(['erro' => 'E-mail já cadastrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($cpf && $usuarioDAO->getByCpf($cpf)) {
        http_response_code(409);
        echo json_encode(['erro' => 'CPF já cadastrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $generator = new app\core\utils\CodeGenerator();
    $token = $generator->run(6); // Gera um código aleatório de 64 caracteres

    $novoUsuario = new \app\models\Usuario();
    $novoUsuario->setNome($nome);
    $novoUsuario->setEmail($email);
    $novoUsuario->setSenha(password_hash($senha, PASSWORD_DEFAULT));
    $novoUsuario->setCpf($cpf);
    $novoUsuario->setTelefone($telefone);
    $novoUsuario->setDataNascimento($dataNascimento);
    $novoUsuario->setEndereco($endereco);
    $novoUsuario->setIdNivel($id_nivel);
    $novoUsuario->setStatus('pendente');
    $novoUsuario->setTokenAtivacao($token);
    $idInserido = $usuarioDAO->insert($novoUsuario);
    
    if ($idInserido) {
        // 1. Enviar e-mail de ativação de conta
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $linkDeAtivacao = $baseUrl . "/ativar?token=" . $token;
        $corpoEmailAtivacao = app\core\utils\EmailTemplate::emailAtivacaoConta($nome, $linkDeAtivacao, $token);
        try {
            $mailAtivacao = new app\core\utils\Mail($email, 'Confirmação de cadastro - FR Semijoias', $corpoEmailAtivacao);
            $mailAtivacao->send();
            http_response_code(201);
            echo json_encode([
                'sucesso' => 'Cadastro realizado com sucesso!',
                'mensagem' => 'Enviamos um email para ' . $email . ' com as instruções para ativar sua conta.'
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            http_response_code(201);
            echo json_encode([
                'sucesso' => 'Cadastro realizado!',
                'aviso' => 'Não foi possível enviar o email de ativação. Entre em contato com o suporte.',
                'erro_email' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao cadastrar o usuário.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}