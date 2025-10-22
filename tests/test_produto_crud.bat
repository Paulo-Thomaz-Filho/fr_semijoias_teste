@echo off
REM Testes CRUD para endpoint /produtos
set API_URL=http://localhost/produtos

REM 1. Criar produto
curl -s -X POST %API_URL%/salvar.php -H "Content-Type: application/json" -d "{\"nome\":\"Produto Teste\",\"descricao\":\"Descrição Teste\",\"preco\":99.99,\"estoque\":10,\"disponivel\":1}"
echo.
echo [1] Produto criado

REM 2. Listar produtos
curl -s %API_URL%/listar.php
echo.
echo [2] Listar produtos

REM 3. Buscar produto por ID (exemplo: 1)
curl -s %API_URL%/buscarPorId.php?idProduto=1
echo.
echo [3] Buscar produto por ID 1

REM 4. Atualizar produto (exemplo: 1)
curl -s -X POST %API_URL%/atualizar.php -H "Content-Type: application/json" -d "{\"idProduto\":1,\"nome\":\"Produto Atualizado\"}"
echo.
echo [4] Produto atualizado

REM 5. Deletar produto (exemplo: 1)
curl -s -X POST %API_URL%/deletar.php -H "Content-Type: application/json" -d "{\"idProduto\":1}"
echo.
echo [5] Produto deletado

REM 6. Testar inserção de dados inválidos
curl -s -X POST %API_URL%/salvar.php -H "Content-Type: application/json" -d "{\"nome\":\"\",\"preco\":\"\",\"estoque\":\"\"}"
echo.
echo [6] Teste dados inválidos
