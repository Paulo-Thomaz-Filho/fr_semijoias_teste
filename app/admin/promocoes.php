<?php require_once 'verifica_login.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR Semijoias - Promoções</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="public/assets/images/logo.svg" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="d-flex">
        <nav class="sidebar">
            <div class="sidebar-header"><img src="assets/images/logo.svg" alt="Logo" class="logo-small"></div>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="admin"><i class="bi bi-grid-fill"></i><span>Início</span></a></li>
                <li class="nav-item"><a class="nav-link" href="cliente"><i class="bi bi-people-fill"></i><span>Clientes</span></a></li>
                <li class="nav-item"><a class="nav-link" href="produto"><i class="bi bi-box-seam-fill"></i><span>Produtos</span></a></li>
                <li class="nav-item"><a class="nav-link" href="pedido"><i class="bi bi-receipt"></i><span>Pedidos</span></a></li>
                <li class="nav-item"><a class="nav-link active" href="promocao"><i class="bi bi-cash-stack"></i><span>Promoção</span></a></li>
            </ul>
            <div class="sidebar-footer"><a class="nav-link" href="#"><i class="bi bi-box-arrow-right"></i><span>Sair</span></a></div>
        </nav>

        <main class="main-content">
            <section class="mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Gerenciar Promoção</h5>
                        <form id="formPromocao">
                            <div class="row">
                                <div class="col-md-1"><label for="promocaoId" class="form-label">ID</label><input type="text" class="form-control" id="promocaoId" placeholder="Auto" readonly></div>
                                <div class="col-md-5"><label for="promocaoNome" class="form-label">Nome da Promoção</label><input type="text" class="form-control" id="promocaoNome" name="nome" required></div>
                                <div class="col-md-3"><label for="promocaoDataInicio" class="form-label">Data de Início</label><input type="date" class="form-control" id="promocaoDataInicio" name="data_inicio" required></div>
                                <div class="col-md-3"><label for="promocaoDataFim" class="form-label">Data de Fim</label><input type="date" class="form-control" id="promocaoDataFim" name="data_fim" required></div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label for="promocaoTipo" class="form-label">Tipo de Desconto</label>
                                    <select class="form-select" id="promocaoTipo" name="tipo" required>
                                        <option value="porcentual">Percentual (%)</option>
                                        <option value="decimal">Valor Fixo (R$)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="promocaoValor" class="form-label">Valor do Desconto</label>
                                    <input type="number" class="form-control" id="promocaoValor" name="valor_desconto" step="0.01" required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-secondary me-2" id="btnLimpar">Limpar</button>
                                <button type="button" class="btn btn-danger me-2 d-none" id="btnExcluir">Excluir</button>
                                <button type="submit" class="btn btn-info text-white me-2 d-none" id="btnAtualizar">Atualizar</button>
                                <button type="submit" class="btn btn-primary" id="btnSalvar">Salvar Nova Promoção</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <section>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Promoções Cadastradas</h5>
                        <div class="table-responsive">
                            <table class="table table-hover mt-3" id="tabelaPromocoes">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Tipo</th>
                                        <th>Valor</th>
                                        <th>Início</th>
                                        <th>Fim</th>
                                        <th class="text-center">Ação</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/assets/js/script_promocao.js"></script>
</body>
</html>