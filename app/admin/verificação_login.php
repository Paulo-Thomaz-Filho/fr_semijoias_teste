<?php
// verifica_login.php

// Inicia a sessão. Sempre no topo do arquivo.
session_start();

// Verifica se a variável de sessão 'user_logged_in' não existe ou não é verdadeira.
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: login.php');
    exit; // Garante que o script pare de ser executado após o redirecionamento
}