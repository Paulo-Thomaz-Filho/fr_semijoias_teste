@echo off
REM Testes CRUD para endpoint /usuario
set API_URL=http://localhost/usuario

REM 1. Criar usuário
curl -s -X POST %API_URL%/salvar.php -H "Content-Type: application/json" -d "{\"nome\":\"Teste User\",\"email\":\"testeuser@email.com\",\"senha\":\"123456\",\"cpf\":\"999.999.999-99\"}"
echo.
echo [1] Usuário criado

REM 2. Listar usuários
curl -s %API_URL%/listar.php
echo.
echo [2] Listar usuários

REM 3. Buscar usuário por ID (exemplo: 1)
curl -s %API_URL%/buscarPorId.php?idUsuario=1
echo.
echo [3] Buscar usuário por ID 1

REM 4. Atualizar usuário (exemplo: 1)
curl -s -X POST %API_URL%/atualizar.php -H "Content-Type: application/json" -d "{\"idUsuario\":1,\"nome\":\"User Atualizado\"}"
echo.
echo [4] Usuário atualizado

REM 5. Deletar usuário (exemplo: 1)
curl -s -X POST %API_URL%/deletar.php -H "Content-Type: application/json" -d "{\"idUsuario\":1}"
echo.
echo [5] Usuário deletado

REM 6. Testar login
curl -s -X POST %API_URL%/login.php -H "Content-Type: application/json" -d "{\"email\":\"testeuser@email.com\",\"senha\":\"123456\"}"
echo.
echo [6] Teste login

REM 7. Testar inserção de dados inválidos
curl -s -X POST %API_URL%/salvar.php -H "Content-Type: application/json" -d "{\"nome\":\"\",\"email\":\"\",\"senha\":\"\",\"cpf\":\"\"}"
echo.
echo [7] Teste dados inválidos
