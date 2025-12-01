# Instruções para Deploy no cPanel

## 📋 Passo a Passo

### 1. Upload dos Arquivos
- Faça upload de todos os arquivos do projeto para o diretório `public_html` ou subdiretório do cPanel
- Certifique-se de que o arquivo `.env` está na raiz do projeto (não dentro de `public`)

### 2. Instalar Dependências do Composer

Acesse o Terminal do cPanel e execute:

```bash
cd /home/seu-usuario/public_html/seu-projeto
php composer.phar install
```

**Nota**: Se você ainda não tem o `composer.phar`, baixe com:
```bash
curl -sS https://getcomposer.org/installer | php
```

### 3. Verificar Instalação

Acesse no navegador:
```
https://frsemijoias.ifhost.gru.br/test_env.php
```

Este arquivo vai mostrar:
- ✅ Se o PHP está na versão correta
- ✅ Se o autoload do Composer foi carregado
- ✅ Se as classes do Mercado Pago estão disponíveis  
- ✅ Se o arquivo .env está sendo lido
- ✅ Se todas as variáveis de ambiente estão configuradas
- ✅ Um botão para testar a criação de preferência

### 4. Configurar Permissões (se necessário)

```bash
chmod 644 .env
chmod 755 public
chmod 644 public/*.php
```

### 5. Testar o Pagamento

Acesse:
```
https://frsemijoias.ifhost.gru.br/pagamento_exemplo.html
```

## 🔧 Troubleshooting

### Erro: "Autoload não encontrado"
**Solução**: Execute `php composer.phar install` no terminal do cPanel

### Erro: "MERCADO_PAGO_ACCESS_TOKEN não configurado"
**Solução**: Verifique se o arquivo `.env` existe na raiz do projeto e contém a linha:
```
MERCADO_PAGO_ACCESS_TOKEN=seu-token-aqui
```

### Erro 500 sem mensagem
**Solução**: 
1. Acesse `test_env.php` para ver detalhes
2. Verifique os logs de erro do cPanel em "Metrics" > "Errors"

### Composer não instala as dependências
**Solução**: Verifique a versão do PHP no cPanel. O projeto requer PHP >= 8.2

Para mudar a versão do PHP no cPanel:
1. Vá em "Select PHP Version"
2. Escolha PHP 8.2 ou superior
3. Execute `php composer.phar install` novamente

## 📁 Estrutura de Arquivos Importante

```
/home/usuario/public_html/projeto/
├── .env                    ← Deve estar aqui!
├── composer.json
├── composer.phar
├── composer.lock
├── vendor/
│   └── autoload.php       ← Criado pelo Composer
├── public/
│   ├── index.php
│   ├── test_env.php       ← Use este para diagnosticar
│   ├── payment_preference.php
│   └── pagamento_exemplo.html
└── app/
    └── ...
```

## 🚀 Workflow de Desenvolvimento

1. **Desenvolva localmente** com Docker
2. **Commit e push** para o repositório
3. **No cPanel**: 
   - Vá para o Git Version Control
   - Faça "Pull" das alterações
   - Se modificou `composer.json`, execute: `php composer.phar update`
4. **Teste** acessando `test_env.php` primeiro

## ⚠️ Arquivos que NÃO devem ser editados no cPanel
- Tudo em `vendor/` (gerenciado pelo Composer)
- `composer.lock` (gerenciado pelo Composer)

## ✅ Checklist de Deploy

- [ ] Arquivo `.env` na raiz com todas as variáveis
- [ ] `composer.phar` existe no projeto
- [ ] Executou `php composer.phar install`
- [ ] Pasta `vendor/` foi criada
- [ ] Arquivo `vendor/autoload.php` existe
- [ ] `test_env.php` mostra tudo verde (✓)
- [ ] `pagamento_exemplo.html` funciona sem erros
