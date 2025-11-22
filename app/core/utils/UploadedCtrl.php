<?php

namespace app\core\utils; 

use core\database\DBQuery; 
use core\database\Where; 
use Exception;
use core\utils\Base64Files; 

class UploadedCtrl {
    private $dbQuery;

    public function __construct() { // Inicializa a conexão com o banco de dados
        $this->dbQuery = new DBQuery('ctrlFiles', 'owner, filePath, accessCtrl, shareMails', 'filePath');
    }

    // Registrar o arquivo enviado no banco de dados
    public function registerUploadedfile($owner, $filePath, $accessCtrl, $shareMails) { // Retorna o caminho do arquivo registrado
        $values = [$owner, $filePath, $accessCtrl, $shareMails]; // Insere os valores na tabela ctrlFiles
        $this->dbQuery->insert($values); // Retorna o caminho do arquivo registrado
        return $filePath; // Retorna o caminho do arquivo registrado
    }

    public function addShareFile($filePath, $shareMails) { // Adiciona emails para compartilhar o arquivo
        $file = $this->dbQuery->selectFiltered(new Where('filePath', '=', $filePath))->fetch();
        $file['shareMails'] .= ",{$shareMails}";
        $this->dbQuery->update($file);
    }

    public function delShareFile($filePath, $shareMails) {
        $file = $this->dbQuery->selectFiltered(new Where('filePath', '=', $filePath))->fetch();
        $file['shareMails'] = str_replace($shareMails, '', $file['shareMails']);
        $this->dbQuery->update($file);
    }

    public function getFile($filePath) {
        $file = $this->dbQuery->selectFiltered(new Where('filePath', '=', $filePath))->fetch();
        if ($file['owner'] == $_SESSION['idUsuario'] || in_array($_SESSION['email'], explode(',', $file['shareMails']))) {
            $base64Files = new Base64Files();
            return $base64Files->fileToBase64($filePath);
        } else {
            throw new Exception('Acesso negado ao arquivo.');
        }
    }
}

?>
