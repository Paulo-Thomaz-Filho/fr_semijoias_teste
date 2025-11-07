-- ==========================================
-- FR SEMIJOIAS
-- ==========================================
CREATE DATABASE IF NOT EXISTS fr_semijoias CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fr_semijoias;
SET NAMES utf8mb4;

-- ==========================================
-- TABELA: promocoes
-- ==========================================
CREATE TABLE IF NOT EXISTS promocoes (
    id_promocao INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    desconto DECIMAL(10,2) NOT NULL,
    tipo_desconto ENUM('percentual','valor') NOT NULL DEFAULT 'percentual',
    data_inicio DATE,
    data_fim DATE,
    status BOOLEAN NOT NULL,
    descricao TEXT
) ENGINE=InnoDB;

-- ==========================================
-- TABELA: produtos
-- ==========================================
CREATE TABLE IF NOT EXISTS produtos (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    marca VARCHAR(100),
    categoria VARCHAR(100),
    id_promocao INT NULL,
    caminho_imagem VARCHAR(255), -- ATUALIZADO (era 'imagem')
    estoque INT NOT NULL,
    disponivel BOOLEAN NOT NULL,
    FOREIGN KEY (id_promocao) REFERENCES promocoes(id_promocao) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- TABELA: nivel_acesso
-- ==========================================
CREATE TABLE IF NOT EXISTS nivel_acesso (
    id_nivel INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

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
    id_nivel INT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendente', -- ADICIONADO
    token_ativacao VARCHAR(255) DEFAULT NULL,       -- ADICIONADO
    FOREIGN KEY (id_nivel) REFERENCES nivel_acesso(id_nivel) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- TABELA: status
-- ==========================================
CREATE TABLE IF NOT EXISTS status (
    id_status INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ==========================================
-- TABELA: pedidos
-- ==========================================
CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    produto_nome VARCHAR(200),
    preco DECIMAL(10,2),
    endereco VARCHAR(255),
    data_pedido DATE,
    quantidade INT NOT NULL,
    id_status INT NOT NULL,
    descricao TEXT,
    id_produto INT,
    id_cliente INT NOT NULL,
    FOREIGN KEY (id_produto) REFERENCES produtos(id_produto) ON DELETE SET NULL,
    FOREIGN KEY (id_status) REFERENCES status(id_status) ON DELETE RESTRICT,
    FOREIGN KEY (id_cliente) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ==========================================
-- POPULAÇÃO DAS TABELAS
-- (Mantidos os dados originais do setup_database.sql por serem mais completos)
-- ==========================================

-- Datas
-- Hoje: 2025-10-18
-- Ontem: 2025-10-17
-- Daqui 2 meses: 2025-12-18
INSERT INTO promocoes (id_promocao, nome, desconto, tipo_desconto, data_inicio, data_fim, status, descricao) VALUES
(1, 'Promoção Percentual', 15.00, 'percentual', '2025-10-17', '2025-12-18', 1, '15% de desconto por tempo limitado.'),
(2, 'Promoção Valor Fixo', 25.00, 'valor', '2025-10-17', '2025-12-18', 1, 'R$25 de desconto em produtos selecionados.'),
(3, 'Promoção Expirada', 10.00, 'percentual', '2025-08-01', '2025-08-15', 0, 'Promoção já expirada, não pode ser usada.');

-- PRODUTOS
INSERT INTO produtos (id_produto, nome, descricao, estoque, preco, marca, categoria, id_promocao, disponivel) VALUES
(1, 'Anel Solitário', 'Anel com zircônia central', 50, 89.90, 'Rommanel', 'Anéis', 1, 1),
(2, 'Anel Meia Aliança', 'Anel com zircônias laterais', 30, 129.90, 'Vivara', 'Anéis', 2, 1),
(3, 'Anel Trio', 'Set com 3 anéis empilháveis', 25, 149.90, 'Life by Vivara', 'Anéis', NULL, 1),
(4, 'Anel Life', 'Anel delicado folheado', 40, 69.90, 'Life by Vivara', 'Anéis', NULL, 1),
(5, 'Brinco Argola Média', 'Argola lisa 3cm', 60, 119.90, 'Pandora', 'Brincos', NULL, 1),
(6, 'Brinco Ponto de Luz', 'Brinco com zircônia 6mm', 80, 79.90, 'Rommanel', 'Brincos', NULL, 1),
(7, 'Brinco Argola Grande', 'Argola grossa 5cm', 35, 179.90, 'Vivara', 'Brincos', NULL, 1),
(8, 'Ear Cuff Estrelas', 'Brinco sem furo', 45, 89.90, 'Life by Vivara', 'Brincos', NULL, 1),
(9, 'Brinco Argola Pequena', 'Argola fina 1.5cm', 100, 79.90, 'Rommanel', 'Brincos', NULL, 1),
(10, 'Brinco Gota Luxo', 'Brinco de gota com pedras', 20, 139.90, 'Carla Amorim', 'Brincos', NULL, 1),
(11, 'Colar Ponto de Luz', 'Colar delicado com zircônia', 55, 119.90, 'Rommanel', 'Colares', NULL, 1),
(12, 'Gargantilha Choker', 'Gargantilha ajustável', 40, 99.90, 'Life by Vivara', 'Colares', NULL, 1),
(13, 'Colar Corrente Grossa', 'Corrente statement', 25, 189.90, 'Vivara', 'Colares', NULL, 1),
(14, 'Colar Gravatinha', 'Gravatinha com zircônia', 50, 129.90, 'Pandora', 'Colares', NULL, 1),
(15, 'Pulseira Riviera', 'Pulseira com zircônias sequenciais', 30, 299.90, 'Vivara', 'Pulseiras', NULL, 1),
(16, 'Pulseira Berloque', 'Pulseira com berloques Pandora', 25, 249.90, 'Pandora', 'Pulseiras', NULL, 1),
(17, 'Pulseira Elos', 'Pulseira de elos cartier', 35, 399.90, 'Carla Amorim', 'Pulseiras', NULL, 1),
(18, 'Bracelete Liso', 'Bracelete aberto ajustável', 50, 149.90, 'Life by Vivara', 'Pulseiras', NULL, 1),
(19, 'Conjunto Delicado', 'Colar + brincos + anel', 15, 799.90, 'Vivara', 'Conjuntos', NULL, 1),
(20, 'Conjunto Noiva', 'Set completo para casamento', 10, 599.90, 'Carla Amorim', 'Conjuntos', NULL, 1);

-- NÍVEIS DE ACESSO
INSERT INTO nivel_acesso (id_nivel, tipo) VALUES
(1, 'Administrador'),
(2, 'Cliente');

INSERT INTO usuarios (id_usuario, nome, email, senha, cpf, telefone, endereco, data_nascimento, id_nivel) VALUES
(1, 'Fernanda Cristina', 'admin@frsemijoias.com', MD5('admin123'), '111.111.111-11', '(11) 99999-9999', 'Rua das Flores, 100 - São Paulo, SP', '1990-01-01', 1),
(2, 'Maria Silva', 'maria.silva@email.com', MD5('123456'), '222.222.222-22', '(11) 98888-8888', 'Avenida Brasil, 200 - São Paulo, SP', '1985-05-15', 2),
(3, 'João Santos', 'joao.santos@email.com', MD5('123456'), '333.333.333-33', '(11) 97777-7777', 'Rua do Sol, 300 - São Paulo, SP', '1992-08-20', 2),
(4, 'Ana Costa', 'ana.costa@email.com', MD5('123456'), '444.444.444-44', '(11) 96666-6666', 'Praça Central, 400 - São Paulo, SP', '1988-12-10', 2),
(5, 'Pedro Oliveira', 'pedro.oliveira@email.com', MD5('123456'), '555.555.555-55', '(11) 95555-5555', 'Alameda Santos, 500 - São Paulo, SP', '1995-03-25', 2),
(6, 'Carla Mendes', 'carla.mendes@email.com', MD5('123456'), '666.666.666-66', '(11) 94444-4444', 'Rua Verde, 600 - São Paulo, SP', '1990-07-30', 2);

-- STATUS
INSERT INTO status (nome) VALUES
('Cancelado'),
('Pendente'),
('Concluído'),
('Enviado');

-- PEDIDOS
INSERT INTO pedidos (id_pedido, produto_nome, preco, endereco, data_pedido, quantidade, id_status, descricao, id_produto, id_cliente) VALUES
(1, 'Brinco Argola Média', 239.80, 'Rua das Flores, 123 - São Paulo, SP', '2025-10-14', 2, 2, 'Dois colares ponto de luz', 5, 2),
(2, 'Conjunto Delicado', 799.90, 'Avenida Brasil, 456 - São Paulo, SP', '2025-10-14', 1, 2, 'Conjunto presente', 19, 3),
(3, 'Pulseira Elos', 399.90, 'Rua das Acácias, 789 - São Paulo, SP', '2025-10-13', 1, 4, 'Pedido especial', 17, 4),
(4, 'Anel Meia Aliança', 159.80, 'Rua do Ouro, 321 - São Paulo, SP', '2025-10-13', 1, 4, 'Dois anéis diferentes', 2, 5),
(5, 'Pulseira Riviera', 299.90, 'Rua das Pedras, 654 - São Paulo, SP', '2025-10-12', 1, 3, 'Pulseira Riviera', 15, 6),
(6, 'Ear Cuff Estrelas', 349.90, 'Rua das Flores, 123 - São Paulo, SP', '2025-10-11', 1, 3, 'Colar premium', 8, 2),
(7, 'Brinco Argola Pequena', 259.70, 'Avenida Brasil, 456 - São Paulo, SP', '2025-10-10', 3, 3, '3 brincos argola', 9, 3),
(8, 'Bracelete Liso', 449.90, 'Rua das Acácias, 789 - São Paulo, SP', '2025-10-09', 1, 1, 'Conjunto dourado', 18, 4),
(9, 'Brinco Gota Luxo', 329.85, 'Rua do Ouro, 321 - São Paulo, SP', '2025-10-08', 2, 4, 'Brincos para presente', 10, 5),
(10, 'Gargantilha Choker', 189.90, 'Rua das Pedras, 654 - São Paulo, SP', '2025-10-08', 1, 3, 'Brinco Pandora', 12, 6),
(11, 'Conjunto Noiva', 599.90, 'Rua das Flores, 123 - São Paulo, SP', '2025-10-05', 1, 4, 'Conjunto de noiva', 20, 2),
(12, 'Colar Gravatinha', 319.80, 'Avenida Brasil, 456 - São Paulo, SP', '2025-10-03', 1, 3, 'Pulseira berloque', 14, 3),
(13, 'Anel Solitário', 449.75, 'Rua das Acácias, 789 - São Paulo, SP', '2025-10-02', 5, 3, '5 anéis solitário', 1, 4),
(14, 'Brinco Argola Média', 299.70, 'Rua do Ouro, 321 - São Paulo, SP', '2025-09-28', 3, 3, '3 colares ponto de luz', 5, 5),
(15, 'Colar Ponto de Luz', 219.80, 'Rua das Pedras, 654 - São Paulo, SP', '2025-09-25', 2, 3, '2 ear cuff', 11, 6);