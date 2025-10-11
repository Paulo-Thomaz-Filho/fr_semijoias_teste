<?php
// Verifica se a variável de sessão 'user_logged_in' não existe ou não é verdadeira.
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login');
    exit;
}