<?php require_once 'verifica_login.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR Semijoias - Clientes</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/logo.svg" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
                <li class="nav-item"><a class="nav-link" href="admin"><i class="bi bi-grid-fill"></i><span>Inicio</span></a></li>
                <li class="nav-item"><a class="nav-link active" href="cliente"><i class="bi bi-people-fill"></i><span>Clientes</span></a></li>
                <li class="nav-item"><a class="nav-link" href="produto"><i class="bi bi-box-seam-fill"></i><span>Produtos</span></a></li>
                <li class="nav-item"><a class="nav-link" href="pedido"><i class="bi bi-receipt"></i><span>Pedidos</span></a></li>
                <li class="nav-item"><a class="nav-link" href="promocao"><i class="bi bi-cash-stack"></i><span>Promoção</span></a></li>
            </ul>
            <div class="sidebar-footer"><a class="nav-link" href="#"><i class="bi bi-box-arrow-right"></i><span>Sair</span></a></div>
        </nav>

        <main class="main-content">
            <section class="mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Gerenciar Cliente</h5>
                        <form id="formCliente">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="clienteId" class="form-label">ID</label>
                                    <input type="text" class="form-control" id="clienteId" placeholder="Auto" readonly>
                                </div>
                                <div class="col-md-5">
                                    <label for="clienteNome" class="form-label">Nome Completo</label>
                                    <input type="text" class="form-control" id="clienteNome" name="clienteNome" required>
                                </div>
                                <div class="col-md-5">
                                    <label for="clienteEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="clienteEmail" name="clienteEmail" required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-start mt-4">
                                <button type="button" class="btn btn-secondary me-2" id="btnLimpar">Limpar</button>
                                <button type="button" class="btn btn-danger me-2 d-none" id="btnExcluir">Excluir</button>
                                <button type="button" class="btn btn-info text-white me-2 d-none" id="btnAtualizar">Atualizar</button>
                                <button type="submit" class="btn btn-secondary" id="btnSalvar" disabled>Salvar Novo Cliente</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <section>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Usuários Cadastrados</h5>
                        <div class="table-responsive">
                            <table class="table table-hover mt-3" id="tabelaUsuarios">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th class="text-center">Ação</th>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/assets/js/script_clientes.js"></script>
</body>
</html>