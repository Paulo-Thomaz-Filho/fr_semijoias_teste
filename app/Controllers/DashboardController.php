<?php
// Salve como app/controllers/DashboardController.php
namespace App\Controllers;

use App\Models\PedidoDAO;
use App\Models\UsuarioDAO;
use PDO;

class DashboardController
{
    private PDO $pdo;

    public function __construct(PDO $connection)
    {
        $this->pdo = $connection;
    }

    /**
     * Agrega e retorna todas as estatísticas para o painel de administração.
     */
    public function getStats(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $pedidoDAO = new PedidoDAO($this->pdo);
            $usuarioDAO = new UsuarioDAO($this->pdo);

            $stats = [
                'totalGanhos' => $pedidoDAO->getTotalGanhos(),
                'vendasNoMes' => $pedidoDAO->getVendasNoMes(),
                'totalUsuarios' => $usuarioDAO->countTotalUsuarios(),
                'itemMaisVendido' => $pedidoDAO->getItemMaisVendido(),
            ];

            echo json_encode($stats);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao buscar estatísticas do dashboard.', 'details' => $e->getMessage()]);
        }
    }
}
