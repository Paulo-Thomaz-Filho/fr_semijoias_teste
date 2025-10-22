@echo off
REM Testes CRUD para endpoint /pedidos
set API_URL=http://localhost/pedidos

REM 1. Criar pedido
curl -s -X POST %API_URL%/salvar.php -H "Content-Type: application/json" -d "{\"produto_nome\":\"Produto Teste\",\"cliente_nome\":\"Cliente Teste\",\"preco\":99.99,\"quantidade\":2,\"id_status\":2}"
echo.
echo [1] Pedido criado

REM 2. Listar pedidos
curl -s %API_URL%/listar.php
echo.
echo [2] Listar pedidos

REM 3. Buscar pedido por ID (exemplo: 1)
curl -s %API_URL%/buscarPorId.php?idPedido=1
echo.
echo [3] Buscar pedido por ID 1

REM 4. Atualizar pedido (exemplo: 1)
curl -s -X POST %API_URL%/atualizar.php -H "Content-Type: application/json" -d "{\"idPedido\":1,\"produto_nome\":\"Produto Atualizado\"}"
echo.
echo [4] Pedido atualizado

REM 5. Deletar pedido (exemplo: 1)
curl -s -X POST %API_URL%/deletar.php -H "Content-Type: application/json" -d "{\"idPedido\":1}"
echo.
echo [5] Pedido deletado

REM 6. Testar inserção de dados inválidos
curl -s -X POST %API_URL%/salvar.php -H "Content-Type: application/json" -d "{\"produto_nome\":\"\",\"cliente_nome\":\"\",\"preco\":\"\",\"quantidade\":\"\"}"
echo.
echo [6] Teste dados inválidos
