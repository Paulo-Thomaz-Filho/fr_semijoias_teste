# ✨ FR Semijoias

> Sistema completo de e-commerce para venda de semijoias com integração ao Mercado Pago

[![PHP Version](https://img.shields.io/badge/PHP-8.2.29-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.8-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Sobre o Projeto

FR Semijoias é uma plataforma de e-commerce desenvolvida para facilitar a venda online de semijoias, oferecendo uma experiência completa tanto para clientes quanto para administradores. O sistema conta com área administrativa robusta, integração com gateway de pagamento Mercado Pago, sistema de autenticação com ativação por email e dashboard com métricas em tempo real.

### 🎯 Principais Funcionalidades

#### Para Administradores
- 📊 **Dashboard Analítico**: Métricas de vendas, estoque e produtos mais vendidos
- 👥 **Gestão de Clientes**: CRUD completo de usuários
- 📦 **Gestão de Produtos**: Cadastro com imagens e controle de estoque
- 📋 **Gestão de Pedidos**: Controle de status e histórico
- 🎫 **Gestão de Promoções**: Sistema de descontos
- 🔔 **Webhooks**: Atualização automática de pedidos via notificações do Mercado Pago

#### Para Clientes
- 🛍️ **Catálogo de Produtos**: Navegação intuitiva com busca e filtros
- 🛒 **Carrinho de Compras**: Gestão completa de itens
- 💳 **Pagamento Integrado**: Checkout via Mercado Pago (Pix, Cartão, Boleto)
- 👤 **Área do Cliente**: Gerenciamento de dados pessoais e endereço
- 📧 **Sistema de Autenticação**: Registro com verificação por email

## 🚀 Tecnologias Utilizadas

### Backend
- **PHP**: Linguagem principal
- **MySQL**: Banco de dados relacional
- **Composer**: Gerenciador de dependências
- **PHPMailer**: Envio de emails SMTP

### Frontend
- **HTML / CSS**: Estrutura e estilização
- **Bootstrap**: Framework CSS responsivo
- **JavaScript**: Interatividade e validações
- **Chart.js**: Gráficos no dashboard

### Infraestrutura
- **Docker**: Containerização da aplicação
- **Apache**: Servidor web
- **phpMyAdmin**: Administração do banco de dados

### Integrações
- **Mercado Pago SDK**: Gateway de pagamento
- **SMTP**: Serviço de email transacional

## 📁 Estrutura do Projeto

```
fr_semijoias_teste/
├── app/
│   ├── controllers/          # Controllers organizados por funcionalidade
│   │   ├── Dashboard/        # Estatísticas, estoque, mais vendidos
│   │   ├── Usuario/          # Autenticação e gestão de usuários
│   │   ├── Produto/          # Gestão de produtos
│   │   ├── Pedido/           # Gestão de pedidos
│   │   ├── Promocao/         # Gestão de promoções
│   │   ├── Pagamento/        # Webhook Mercado Pago
│   │   └── Status/           # Status de pedidos
│   ├── models/               # Modelos e DAOs
│   ├── core/
│   │   ├── database/         # Conexão e queries
│   │   └── utils/            # Classes auxiliares (Mail, Upload, Router, etc)
│   └── etc/
│       ├── config.php        # Configurações gerais
│       └── routes.json       # Mapeamento de rotas
├── public/
│   ├── views/                # Páginas HTML
│   ├── assets/
│   │   ├── css/              # Estilos customizados
│   │   ├── js/               # Scripts JavaScript
│   │   └── images/           # Imagens
│   └── index.php             # Ponto de entrada da aplicação
├── vendor/                   # Dependências Composer
├── docker-compose.yml        # Configuração Docker
├── Dockerfile                # Imagem PHP-Apache customizada
└── setup_database.sql        # Script de criação e população do banco
```

## 🛠️ Instalação e Configuração

### Pré-requisitos

- Docker Desktop instalado
- Git instalado
- Composer instalado (opcional, já incluído no container)

### Passo a Passo

1. **Clone o repositório**
```bash
git clone https://github.com/Paulo-Thomaz-Filho/fr_semijoias.git
cd fr_semijoias
```

2. **Configure as variáveis de ambiente**

Crie um arquivo `.env` na raiz do projeto:

```env
# Banco de Dados
DB_HOST=mysql
DB_NAME=fr_semijoias
DB_USER=root
DB_PASS=root

# Email (SMTP)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
MAIL_FROM_EMAIL=seu-email@gmail.com
MAIL_FROM_NAME=FR Semijoias

# Mercado Pago
MERCADO_PAGO_ACCESS_TOKEN=seu-access-token
MERCADO_PAGO_WEBHOOK_SECRET=seu-webhook-secret
```

3. **Inicie os containers Docker**
```bash
docker compose up -d
```

4. **Configure o banco de dados**

Acesse: `http://localhost:8080` (phpMyAdmin)
- Usuário: `root`
- Senha: `root`

Execute o script `setup_database.sql` para criar as tabelas.

5. **Acesse a aplicação**

- **Frontend**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

### 🔑 Credenciais Padrão

Após configurar o banco, crie um usuário administrador manualmente ou através do endpoint `/usuario/salvar-admin`.

## 📧 Sistema de Email

O projeto utiliza PHPMailer para envio de emails transacionais:

### Templates Disponíveis
- **Ativação de Conta**: Email com token de 6 caracteres
- **Boas-vindas**: Email de boas-vindas após registro
- **Conta Ativada**: Confirmação de ativação
- **Recuperação de Senha**: Link para redefinir senha (futuro)
- **Pedido Realizado**: Confirmação de compra

### Fluxo de Ativação
1. Usuário se cadastra → Recebe email de ativação
2. Clica no link ou digita código → Conta é ativada
3. Recebe email de confirmação → Pode fazer login

## 💳 Integração Mercado Pago

### Configuração do Webhook

1. Acesse o [Painel do Mercado Pago](https://www.mercadopago.com.br/developers/panel)
2. Vá em **Webhooks**
3. Configure a URL: `https://seu-dominio.com/pagamento/notificacao`
4. Copie o **Secret** e adicione ao `.env` como `MERCADO_PAGO_WEBHOOK_SECRET`

### Fluxo de Pagamento
1. Cliente finaliza compra → Sistema cria Preference
2. Cliente é redirecionado ao Mercado Pago
3. Após pagamento → Webhook notifica o sistema
4. Sistema atualiza status do pedido automaticamente

### Status de Pedidos
- **Aprovado**: Pagamento confirmado
- **Pendente**: Aguardando pagamento
- **Cancelado**: Pagamento cancelado/recusado
- **Enviado**: Pedido enviado ao cliente

## 🎨 Interface

### Área Pública
- Design responsivo (mobile-first)
- Telas modernas e minimalista com Bootstrap
- Animações suaves e interações intuitivas
- Login com slider de transição (login/cadastro)

### Dashboard Administrativo
- Gráficos interativos (Chart.js)
- Tabelas com busca e ordenação
- Páginas para CRUD (Create, Read, Update, Delete)
- Sidebar fixa com navegação

## 🔒 Segurança

- ✅ Senhas hasheadas com `password_hash()`
- ✅ Validação HMAC SHA256 nos webhooks
- ✅ Proteção contra SQL Injection (PDO com prepared statements)
- ✅ Sanitização de inputs
- ✅ Sessões seguras para autenticação
- ✅ Controle de acesso por níveis (Admin/Cliente)
- ✅ Tokens de ativação com expiração

## 📊 Database Schema

```sql
-- Tabelas
usuarios (id, nome, email, senha, telefone, cpf, endereco, data_nascimento, id_nivel, status, token_ativacao)
produtos (id_produto, nome, descricao, preco, marca, categoria, id_promocao, caminho_imagem, estoque, disponivel)
pedidos (id_pedido, produto_nome, preco, endereco, data_pedido, quantidade, id_status, descricao, id_produto, id_cliente)
promocoes (id_promocao, nome, desconto, tipo_desconto, data_inicio, data_fim, status, descricao)
status (id_status, nome)
nivel_acesso (id_nivel, tipo)
```

## 👥 Equipe de Desenvolvimento

- **Eduardo Nogueira Simoes**
- **Henrico da Silva Santos**
- **Jhonny Sancho Chagas**
- **João Marcos da Cruz**
- **Paulo Thomaz Filho**

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.