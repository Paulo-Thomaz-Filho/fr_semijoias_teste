<?php
namespace App\Controllers;

class AuthController
{
    public function checkAuthStatus(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json; charset=utf-8');

        // Verifica se o usuário está logado e se é do tipo 'admin'
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true && $_SESSION['user_tipo'] === 'admin') {
            http_response_code(200); // OK
            echo json_encode([
                'isAuthenticated' => true,
                'isAdmin' => true,
                'userName' => $_SESSION['user_nome'] ?? 'Admin'
            ]);
        } else {
            http_response_code(401); // Unauthorized
            echo json_encode([
                'isAuthenticated' => false,
                'isAdmin' => false,
                'error' => 'Acesso não autorizado.'
            ]);
        }
    }
}