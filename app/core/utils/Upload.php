<?php

namespace app\core\utils;

use app\core\utils\UploadedCtrl;
use core\utils\Base64Files;


// Diretório para salvar os arquivos enviados
$uploadPathDir = __DIR__."/../../../public/assets/images"; // Diretório para salvar os arquivos enviados
$uploader = new UploadedCtrl(); // Instancia a classe UploadedCtrl

// Verifica se o método de requisição é POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Verifica se o método de requisição é POST
    if (!isset($_FILES['file'])) {  // Verifica se o arquivo foi enviado
        http_response_code(400);
        // ...
        return;
    }

    // Processa o arquivo enviado
    $file = $_FILES['file'];                  // Obtém o arquivo enviado
    $accessCtrl = $_POST['accessCtrl'] ?? 0;  // Default para público se não definido
    $shareMails = $_POST['shareMails'] ?? ""; // Default para nenhum email se não definido

    $tempPath = $file['tmp_name'];            // Caminho temporário do arquivo enviado
    $fileName = $file['name'];                // Nome original do arquivo

    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);              // Extensão do arquivo
    $fileBase64Name = "up" . date('YmdHisv') . ".{$fileExtension}.base64"; // Nome do arquivo em base64 com timestamp único

    $base64Files = new Base64Files(); // Instancia a classe Base64Files
    $fileContent = $base64Files->fileToBase64($tempPath); // Converte o arquivo para base64
    $base64Files->base64ToFile($fileContent, $uploadPathDir . $fileBase64Name); // Salva o arquivo em base64 no diretório de upload

    // Registrar o arquivo
    $owner = $_SESSION["idUsuario"]; // Obtém o ID do usuário logado
    $filePath = $uploadPathDir . $fileBase64Name; // Caminho completo do arquivo salvo
    $uploader->registerUploadedfile($owner, $filePath, $accessCtrl, $shareMails); // Registra o arquivo no banco de dados

    // Retornar o nome do arquivo para o cliente
    // ...
}

?>
