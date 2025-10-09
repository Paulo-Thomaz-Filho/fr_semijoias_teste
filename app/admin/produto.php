<?php require_once 'verifica_login.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR Semijoias - Produtos</title>

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
                <li class="nav-item"><a class="nav-link active" href="produto"><i class="bi bi-box-seam-fill"></i><span>Produtos</span></a></li>
                <li class="nav-item"><a class="nav-link" href="pedido"><i class="bi bi-receipt"></i><span>Pedidos</span></a></li>
                <li class="nav-item"><a class="nav-link" href="promocao"><i class="bi bi-cash-stack"></i><span>Promoção</span></a></li>
            </ul>
            <div class="sidebar-footer"><a class="nav-link" href="#"><i class="bi bi-box-arrow-right"></i><span>Sair</span></a></div>
        </nav>

        <main class="main-content">
            <section class="mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Gerenciar Produto</h5>
                        <form id="formProduto">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="produtoId" class="form-label">ID</label>
                                    <input type="text" class="form-control" id="produtoId" placeholder="Auto" readonly>
                                </div>
                                <div class="col-md-10">
                                    <label for="produtoNome" class="form-label">Nome do Produto</label>
                                    <input type="text" class="form-control" id="produtoNome" name="nome" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <label for="produtoDescricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="produtoDescricao" name="descricao" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="produtoCategoria" class="form-label">Categoria</label>
                                    <input type="text" class="form-control" id="produtoCategoria" name="categoria" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="produtoMarca" class="form-label">Marca</label>
                                    <input type="text" class="form-control" id="produtoMarca" name="marca" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="produtoValor" class="form-label">Valor (R$)</label>
                                    <input type="number" class="form-control" id="produtoValor" name="valor" step="0.01" required>
                                </div>
                            </div>
                             <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="produtoPromocao" class="form-label">Promoção Associada</label>
                                    <select class="form-select" id="produtoPromocao" name="idPromocao">
                                        <option selected disabled value="">Selecione uma promoção...</option>
                                        </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-secondary me-2" id="btnLimpar">Limpar</button>
                                <button type="button" class="btn btn-danger me-2 d-none" id="btnExcluir">Excluir</button>
                                <button type="submit" class="btn btn-info text-white me-2 d-none" id="btnAtualizar">Atualizar</button>
                                <button type="submit" class="btn btn-primary" id="btnSalvar">Salvar Novo Produto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <section>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Produtos Cadastrados</h5>
                        <div class="table-responsive">
                            <table class="table table-hover mt-3" id="tabelaProdutos">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Categoria</th>
                                        <th>Marca</th>
                                        <th>Valor</th>
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
    <script src="public/assets/js/script_produto.js"></script>
</body>
</html>