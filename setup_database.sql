-- ==========================================
-- FR SEMIJOIAS - DATABASE SETUP SCRIPT
-- ==========================================
-- Este script cria todas as tabelas do sistema
-- ATENÇÃO: Executar apenas uma vez na inicialização
-- ATUALIZADO: Usando snake_case (padrão SQL)

CREATE DATABASE IF NOT EXISTS fr_semijoias CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fr_semijoias;

-- ==========================================
-- TABELA: marcas
-- ==========================================
CREATE TABLE IF NOT EXISTS marcas (
    id_marca INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABELA: categorias
-- ==========================================
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABELA: promocoes
-- ==========================================
CREATE TABLE IF NOT EXISTS promocoes (
    id_promocao INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    desconto DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    data_inicio DATE,
    data_fim DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABELA: produtos
-- ==========================================
CREATE TABLE IF NOT EXISTS produtos (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    id_marca INT,
    id_categoria INT,
    id_promocao INT NULL,
    imagem VARCHAR(255),
    unidade_estoque INT DEFAULT 0,
    disponivel TINYINT(1) DEFAULT 1,
    FOREIGN KEY (id_marca) REFERENCES marcas(id_marca) ON DELETE SET NULL,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL,
    FOREIGN KEY (id_promocao) REFERENCES promocoes(id_promocao) ON DELETE SET NULL,
    INDEX idx_marca (id_marca),
    INDEX idx_categoria (id_categoria),
    INDEX idx_promocao (id_promocao),
    INDEX idx_disponivel (disponivel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABELA: usuarios
-- ==========================================
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    cpf VARCHAR(14) UNIQUE,
    endereco VARCHAR(500),
    data_nascimento DATE,
    id_nivel INT DEFAULT 2,
    INDEX idx_email (email),
    INDEX idx_cpf (cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABELA: pedidos
-- ==========================================
CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    produto_nome VARCHAR(200),
    cliente_nome VARCHAR(200) NOT NULL,
    preco DECIMAL(10,2),
    endereco VARCHAR(255),
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    quantidade INT DEFAULT 1,
    status VARCHAR(50) DEFAULT 'Pendente',
    descricao TEXT,
    INDEX idx_cliente (cliente_nome),
    INDEX idx_data (data_pedido),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- VERIFICAÇÃO FINAL
-- ==========================================
SELECT 
    'Banco de dados criado com sucesso!' AS Status,
    (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'fr_semijoias') AS Total_Tabelas;
