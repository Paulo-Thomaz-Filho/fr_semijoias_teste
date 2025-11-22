<?php
// Em: app/core/utils/imagem.php

// 1. Define o caminho raiz (app/core/utils/../../../ -> fr_semijoias_teste)
$rootPath = dirname(dirname(dirname(__DIR__)));

// 2. Pega e sanitiza o nome do arquivo
$filename = $_GET['file'] ?? null;
if (!$filename || $filename !== basename($filename)) {
    http_response_code(400); // Bad Request
    exit('Nome de arquivo inválido.');
}

// 3. Monta o caminho completo e seguro para o arquivo na pasta privada
$fullPath = $rootPath . '/public/assets/images/' . $filename;

if (!file_exists($fullPath)) {
    http_response_code(404); // Not Found
    exit('Arquivo de imagem não encontrado.');
}

try {
    // 4. Lê o conteúdo Base64 do arquivo
    $base64_content = file_get_contents($fullPath);
    if ($base64_content === false) {
        throw new Exception('Não foi possível ler o arquivo.');
    }

    // 5. Decodifica o conteúdo de volta para uma imagem binária
    $image_binary = base64_decode($base64_content);
    if ($image_binary === false) {
        throw new Exception('Arquivo corrompido ou não é Base64.');
    }

    // 6. Determina o tipo de imagem (MIME type) pela extensão original
    $parts = explode('.', $filename);
    $extension = 'png'; // Padrão
    if (count($parts) > 2) {
        $extension = $parts[count($parts) - 2]; // Pega a penúltima parte
    }

    $mime_type = 'image/jpeg'; // Padrão
    if ($extension === 'png') $mime_type = 'image/png';
    if ($extension === 'gif') $mime_type = 'image/gif';
    if ($extension === 'webp') $mime_type = 'image/webp';
    
    // 7. Envia os cabeçalhos corretos e exibe a imagem
    header('Content-Type: ' . $mime_type);
    header('Content-Length: ' . strlen($image_binary));
    echo $image_binary;
    exit;

} catch (Exception $e) {
    http_response_code(500);
    exit('Erro ao processar a imagem: ' . $e->getMessage());
}
?>