<?php require_once 'verifica_login.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR Semijoias - Inicio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/logo.svg" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="d-flex">
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="assets/images/logo.svg" alt="Logo FR Semijoias" class="logo-small">
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="admin">
                        <i class="bi bi-grid-fill"></i>
                        <span>Inicio</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cliente">
                        <i class="bi bi-people-fill"></i>
                        <span>Clientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="produto">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>Produtos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pedido">
                        <i class="bi bi-receipt"></i>
                        <span>Pedidos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="promocao">
                        <i class="bi bi-cash-stack"></i>
                        <span>Promoção</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <a class="nav-link" href="#">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sair</span>
                </a>
            </div>
        </nav>

        <main class="main-content">
            <header class="d-flex justify-content-between align-items-center py-3">
                <div>
                    <h1 class="h2">Bem-vinda, Fernanda!</h1>
                    <p class="text-muted mb-0">O que você gostaria de fazer hoje?</p>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" class="ms-4 text-dark">
                        <i class="bi bi-person-circle fs-3"></i>
                    </a>
                </div>
            </header>

            <section class="stats-cards mt-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-circle bg-light-primary">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0 text-muted">Total de Ganhos</p>
                                    <h5 class="mb-0" id="totalGanhos">R$ 0,00</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-circle bg-light-success">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0 text-muted">Cadastrados</p>
                                    <h5 class="mb-0" id="totalCadastrados">0</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-circle bg-light-warning">
                                    <i class="bi bi-tag-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0 text-muted">Vendidos no Mês</p>
                                    <h5 class="mb-0" id="totalVendidos">0 Vendas</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="orders-table mt-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Próximos Pedidos</h5>
                        <div class="table-responsive">
                            <table class="table table-hover mt-3" id="tabelaPedidos">
                                <thead>
                                    <tr>
                                        <th>Produto <i class="bi bi-arrow-down-up"></i></th>
                                        <th>Cliente <i class="bi bi-arrow-down-up"></i></th>
                                        <th>Endereço <i class="bi bi-arrow-down-up"></i></th>
                                        <th>Preço <i class="bi bi-arrow-down-up"></i></th>
                                        <th>Data <i class="bi bi-arrow-down-up"></i></th>
                                        <th>Status <i class="bi bi-arrow-down-up"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script src="public/assets/js/script_dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>