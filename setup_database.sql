-- ==========================================
-- FR SEMIJOIAS
-- ==========================================
CREATE DATABASE IF NOT EXISTS fr_semijoias CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fr_semijoias;
SET NAMES utf8mb4;

-- ==========================================
-- TABELA: marcas
-- ==========================================
CREATE TABLE IF NOT EXISTS marcas (
    id_marca INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT
) ENGINE=InnoDB;

-- ==========================================
-- TABELA: categorias
-- ==========================================
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT
) ENGINE=InnoDB;

-- ==========================================
-- TABELA: promocoes
-- ==========================================
CREATE TABLE IF NOT EXISTS promocoes (
    id_promocao INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    desconto DECIMAL(5,2) NOT NULL,
    data_inicio DATE,
    data_fim DATE,
    ativo BOOLEAN NOT NULL
) ENGINE=InnoDB;

-- ==========================================
-- TABELA: produtos
-- ==========================================
CREATE TABLE IF NOT EXISTS produtos (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    id_marca INT,
    id_categoria INT,
    id_promocao INT NULL,
    imagem VARCHAR(255),
    estoque INT NOT NULL,
    disponivel TINYINT(1) NOT NULL,
    FOREIGN KEY (id_marca) REFERENCES marcas(id_marca) ON DELETE SET NULL,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL,
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
    FOREIGN KEY (id_nivel) REFERENCES nivel_acesso(id_nivel) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- TABELA: pedidos
-- ==========================================
CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    produto_nome VARCHAR(200),
    cliente_nome VARCHAR(200) NOT NULL,
    preco DECIMAL(10,2),
    endereco VARCHAR(255),
    data_pedido DATE,
    quantidade INT NOT NULL,
    status VARCHAR(50),
    descricao TEXT,
    id_produto INT,
    FOREIGN KEY (id_produto) REFERENCES produtos(id_produto) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- POPULAÇÃO DAS TABELAS
-- ==========================================

-- MARCAS
INSERT INTO marcas (id_marca, nome, descricao) VALUES
(1, 'Vivara', 'Joias e semijoias de alta qualidade'),
(2, 'Pandora', 'Joias personalizáveis e berloques exclusivos'),
(3, 'Rommanel', 'Semijoias folheadas a ouro'),
(4, 'Life by Vivara', 'Semijoias modernas e acessíveis'),
(5, 'Carla Amorim', 'Design exclusivo e pedras brasileiras');

-- CATEGORIAS
INSERT INTO categorias (id_categoria, nome, descricao) VALUES
(1, 'Anéis', 'Anéis diversos em ouro, prata e pedras'),
(2, 'Brincos', 'Brincos de argola, ponto de luz, ear cuff'),
(3, 'Colares', 'Colares delicados e gargantilhas'),
(4, 'Pulseiras', 'Pulseiras finas e braceletes'),
(5, 'Conjuntos', 'Kits de joias combinadas');

-- PROMOÇÕES
INSERT INTO promocoes (id_promocao, nome, desconto, data_inicio, data_fim, ativo) VALUES
(1, 'Black Friday 2025', 30.00, '2025-11-20', '2025-11-30', true),
(2, 'Natal Especial', 20.00, '2025-12-01', '2025-12-25', true),
(3, 'Dia das Mães', 25.00, '2026-05-01', '2026-05-12', false);

-- PRODUTOS
INSERT INTO produtos (id_produto, nome, descricao, estoque, preco, id_categoria, id_marca, id_promocao, disponivel) VALUES
(1, 'Anel Solitário', 'Anel com zircônia central', 50, 89.90, 1, 3, NULL, 1),
(2, 'Anel Meia Aliança', 'Anel com zircônias laterais', 30, 129.90, 1, 1, 1, 1),
(3, 'Anel Trio', 'Set com 3 anéis empilháveis', 25, 149.90, 1, 4, NULL, 1),
(4, 'Anel Life', 'Anel delicado folheado', 40, 69.90, 1, 4, 2, 1),
(5, 'Brinco Argola Média', 'Argola lisa 3cm', 60, 119.90, 2, 2, NULL, 1),
(6, 'Brinco Ponto de Luz', 'Brinco com zircônia 6mm', 80, 79.90, 2, 3, 1, 1),
(7, 'Brinco Argola Grande', 'Argola grossa 5cm', 35, 179.90, 2, 1, NULL, 1),
(8, 'Ear Cuff Estrelas', 'Brinco sem furo', 45, 89.90, 2, 4, 2, 1),
(9, 'Brinco Argola Pequena', 'Argola fina 1.5cm', 100, 79.90, 2, 3, NULL, 1),
(10, 'Brinco Gota Luxo', 'Brinco de gota com pedras', 20, 139.90, 2, 5, NULL, 1),
(11, 'Colar Ponto de Luz', 'Colar delicado com zircônia', 55, 119.90, 3, 3, 1, 1),
(12, 'Gargantilha Choker', 'Gargantilha ajustável', 40, 99.90, 3, 4, NULL, 1),
(13, 'Colar Corrente Grossa', 'Corrente statement', 25, 189.90, 3, 1, NULL, 1),
(14, 'Colar Gravatinha', 'Gravatinha com zircônia', 50, 129.90, 3, 2, 2, 1),
(15, 'Pulseira Riviera', 'Pulseira com zircônias sequenciais', 30, 299.90, 4, 1, NULL, 1),
(16, 'Pulseira Berloque', 'Pulseira com berloques Pandora', 25, 249.90, 4, 2, 1, 1),
(17, 'Pulseira Elos', 'Pulseira de elos cartier', 35, 399.90, 4, 5, NULL, 1),
(18, 'Bracelete Liso', 'Bracelete aberto ajustável', 50, 149.90, 4, 4, 2, 1),
(19, 'Conjunto Delicado', 'Colar + brincos + anel', 15, 799.90, 5, 1, NULL, 1),
(20, 'Conjunto Noiva', 'Set completo para casamento', 10, 599.90, 5, 5, NULL, 1);

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

-- PEDIDOS
INSERT INTO pedidos (id_pedido, produto_nome, cliente_nome, preco, endereco, data_pedido, quantidade, status, descricao, id_produto) VALUES
(1, 'Brinco Argola Média', 'Maria Silva', 239.80, 'Rua das Flores, 123 - São Paulo, SP', '2025-10-14', 2, 'Pendente', 'Dois colares ponto de luz', 5),
(2, 'Conjunto Delicado', 'João Santos', 799.90, 'Avenida Brasil, 456 - São Paulo, SP', '2025-10-14', 1, 'Pendente', 'Conjunto presente', 19),
(3, 'Pulseira Elos', 'Ana Costa', 399.90, 'Rua das Acácias, 789 - São Paulo, SP', '2025-10-13', 1, 'Concluído', 'Pedido especial', 17),
(4, 'Anel Meia Aliança', 'Pedro Oliveira', 159.80, 'Rua do Ouro, 321 - São Paulo, SP', '2025-10-13', 1, 'Concluído', 'Dois anéis diferentes', 2),
(5, 'Pulseira Riviera', 'Carla Mendes', 299.90, 'Rua das Pedras, 654 - São Paulo, SP', '2025-10-12', 1, 'Concluído', 'Pulseira Riviera', 15),
(6, 'Ear Cuff Estrelas', 'Maria Silva', 349.90, 'Rua das Flores, 123 - São Paulo, SP', '2025-10-11', 1, 'Concluído', 'Colar premium', 8),
(7, 'Brinco Argola Pequena', 'João Santos', 259.70, 'Avenida Brasil, 456 - São Paulo, SP', '2025-10-10', 3, 'Concluído', '3 brincos argola', 9),
(8, 'Bracelete Liso', 'Ana Costa', 449.90, 'Rua das Acácias, 789 - São Paulo, SP', '2025-10-09', 1, 'Cancelado', 'Conjunto dourado', 18),
(9, 'Brinco Gota Luxo', 'Pedro Oliveira', 329.85, 'Rua do Ouro, 321 - São Paulo, SP', '2025-10-08', 2, 'Concluído', 'Brincos para presente', 10),
(10, 'Gargantilha Choker', 'Carla Mendes', 189.90, 'Rua das Pedras, 654 - São Paulo, SP', '2025-10-08', 1, 'Concluído', 'Brinco Pandora', 12),
(11, 'Conjunto Noiva', 'Maria Silva', 599.90, 'Rua das Flores, 123 - São Paulo, SP', '2025-10-05', 1, 'Concluído', 'Conjunto de noiva', 20),
(12, 'Colar Gravatinha', 'João Santos', 319.80, 'Avenida Brasil, 456 - São Paulo, SP', '2025-10-03', 1, 'Concluído', 'Pulseira berloque', 14),
(13, 'Anel Solitário', 'Ana Costa', 449.75, 'Rua das Acácias, 789 - São Paulo, SP', '2025-10-02', 5, 'Concluído', '5 anéis solitário', 1),
(14, 'Brinco Argola Média', 'Pedro Oliveira', 299.70, 'Rua do Ouro, 321 - São Paulo, SP', '2025-09-28', 3, 'Concluído', '3 colares ponto de luz', 5),
(15, 'Colar Ponto de Luz', 'Carla Mendes', 219.80, 'Rua das Pedras, 654 - São Paulo, SP', '2025-09-25', 2, 'Concluído', '2 ear cuff', 11);
