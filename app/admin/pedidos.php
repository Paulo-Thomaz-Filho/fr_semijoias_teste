<?php require_once __DIR__.'/verifica_login.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR Semijoias - Pedidos</title>

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
                <li class="nav-item"><a class="nav-link" href="admin"><i class="bi bi-grid-fill"></i><span>Início</span></a></li>
                <li class="nav-item"><a class="nav-link" href="cliente"><i class="bi bi-people-fill"></i><span>Clientes</span></a></li>
                <li class="nav-item"><a class="nav-link" href="produto"><i class="bi bi-box-seam-fill"></i><span>Produtos</span></a></li>
                <li class="nav-item"><a class="nav-link active" href="pedido"><i class="bi bi-receipt"></i><span>Pedidos</span></a></li>
                <li class="nav-item"><a class="nav-link" href="promocao"><i class="bi bi-cash-stack"></i><span>Promoção</span></a></li>
            </ul>
            <div class="sidebar-footer"><a class="nav-link" href="#"><i class="bi bi-box-arrow-right"></i><span>Sair</span></a></div>
        </nav>

        <main class="main-content">
            <section class="mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Gerenciar Pedido</h5>
                        <form id="formPedido">
                            <div class="row">
                                <div class="col-md-2"><label for="pedidoId" class="form-label">ID Pedido</label><input type="text" class="form-control" id="pedidoId" placeholder="Auto" readonly></div>
                                <div class="col-md-5"><label for="pedidoUsuario" class="form-label">Cliente</label><select class="form-select" id="pedidoUsuario" name="usuario_id" required><option selected disabled value="">Selecione...</option></select></div>
                                <div class="col-md-5"><label for="pedidoEndereco" class="form-label">Endereço</label><select class="form-select" id="pedidoEndereco" name="endereco_id" required><option selected disabled value="">Selecione...</option></select></div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4"><label for="pedidoData" class="form-label">Data</label><input type="date" class="form-control" id="pedidoData" name="data_pedido" ></div>
                                <div class="col-md-4"><label for="pedidoStatus" class="form-label">Status</label><select class="form-select" id="pedidoStatus" name="status"><option value="Pendente">Pendente</option><option value="Processando">Processando</option><option value="Enviado">Enviado</option><option value="Entregue">Entregue</option><option value="Cancelado">Cancelado</option></select></div>
                            </div>

                            <hr class="my-4">

                            <h6 class="card-subtitle mb-3 text-muted">Itens do Pedido</h6>
                            <div class="row align-items-end p-2 mb-3 bg-light rounded">
                                <div class="col-md-6"><label for="itemProduto" class="form-label">Produto</label><select class="form-select" id="itemProduto"><option selected disabled value="">Selecione um produto...</option></select></div>
                                <div class="col-md-2"><label for="itemQuantidade" class="form-label">Quantidade</label><input type="number" class="form-control" id="itemQuantidade" value="1" min="1"></div>
                                <div class="col-md-2"><label for="itemValorUnitario" class="form-label">Valor Unit.</label><input type="text" class="form-control" id="itemValorUnitario" placeholder="R$ 0,00" readonly></div>
                                <div class="col-md-2 d-grid"><button type="button" class="btn btn-success" id="btnAdicionarItem">Adicionar Item</button></div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm" id="tabelaItensPedido">
                                    <thead>
                                        <tr><th>Produto</th><th>Qtd.</th><th>Valor Unit.</th><th>Subtotal</th><th>Ação</th></tr>
                                    </thead>
                                    <tbody>
                                        </tbody>
                                </table>
                            </div>

                            <div class="row mt-4 align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-secondary me-2" id="btnLimpar">Limpar</button>
                                        <button type="button" class="btn btn-danger me-2 d-none" id="btnExcluir">Cancelar Pedido</button>
                                        <button type="submit" class="btn btn-info text-white me-2 d-none" id="btnAtualizar">Atualizar Pedido</button>
                                        <button type="submit" class="btn btn-primary" id="btnSalvar">Salvar Novo Pedido</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="pedidoValorTotal" class="form-label">Valor Total do Pedido</label>
                                    <input type="text" class="form-control form-control-lg" id="pedidoValorTotal" name="valor_total" placeholder="R$ 0,00" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

           <section>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Histórico de Pedidos</h5>
                        <div class="table-responsive">
                            <table class="table table-hover mt-3" id="tabelaPedidos">
                                <thead>
                                    <tr>
                                        <th>ID Pedido</th>
                                        <th>Cliente</th>
                                        <th>Valor Total</th>
                                        <th>Data</th>
                                        <th>Status</th>
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
    <script src="public/assets/js/script_pedidos.js"></script>
</body>
</html>