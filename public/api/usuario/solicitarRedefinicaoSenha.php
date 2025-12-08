<?php
// Endpoint público para solicitar redefinição de senha
// Caminho: public/api/usuario/solicitarRedefinicaoSenha.php

// Garante autoload das classes
require_once __DIR__ . '/../../../vendor/autoload.php';
// Redireciona requisições para o controlador real
require_once __DIR__ . '/../../../app/controllers/Usuario/solicitarRedefinicaoSenha.php';
