@echo off
REM Testes CRUD para endpoint /promocoes
set API_URL=http://localhost/promocoes

REM 1. Criar promoção
curl -s -X POST %API_URL%/salvar.php -H "Content-Type: application/json" -d "{\"nome\":\"Promo Teste\",\"desconto\":10,\"tipo_desconto\":\"percentual\",\"data_inicio\":\"2025-10-18\",\"data_fim\":\"2025-12-18\",\"status\":1}"
echo.
echo [1] Promoção criada

REM 2. Listar promoções
curl -s %API_URL%/listar.php
echo.
echo [2] Listar promoções

REM 3. Buscar promoção por ID (exemplo: 1)
curl -s %API_URL%/buscarPorId.php?idPromocao=1
echo.
echo [3] Buscar promoção por ID 1

REM 4. Atualizar promoção (exemplo: 1)
curl -s -X POST %API_URL%/atualizar.php -H "Content-Type: application/json" -d "{\"idPromocao\":1,\"nome\":\"Promo Atualizada\"}"
echo.
echo [4] Promoção atualizada

REM 5. Deletar promoção (exemplo: 1)
curl -s -X POST %API_URL%/deletar.php -H "Content-Type: application/json" -d "{\"idPromocao\":1}"
echo.
echo [5] Promoção deletada

REM 6. Testar inserção de dados inválidos
curl -s -X POST %API_URL%/salvar.php -H "Content-Type: application/json" -d "{\"nome\":\"\",\"desconto\":\"\",\"tipo_desconto\":\"\"}"
echo.
echo [6] Teste dados inválidos
