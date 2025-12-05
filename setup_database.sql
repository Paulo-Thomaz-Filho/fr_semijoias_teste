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
    caminho_imagem VARCHAR(255),
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
    status VARCHAR(20) NOT NULL DEFAULT 'pendente',
    token_ativacao VARCHAR(255) DEFAULT NULL,
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
-- ==========================================

-- PROMOÇÕES
INSERT INTO promocoes (id_promocao, nome, desconto, tipo_desconto, data_inicio, data_fim, status, descricao) VALUES
(1, 'Promoção Percentual', 15.00, 'percentual', '2025-10-17', '2025-12-18', 1, '15% de desconto por tempo limitado.'),
(2, 'Promoção Valor Fixo', 25.00, 'valor', '2025-10-17', '2025-12-18', 1, 'R$25 de desconto em produtos selecionados.'),
(3, 'Promoção Expirada', 10.00, 'percentual', '2025-08-01', '2025-08-15', 0, 'Promoção já expirada, não pode ser usada.');

-- PRODUTOS
INSERT INTO produtos (id_produto, nome, descricao, preco, marca, categoria, id_promocao, caminho_imagem, estoque, disponivel) VALUES
(1, 'Colar Canutilho Duo', 'Minimalismo e versatilidade definem este acessório. Conhecido como "ponto de luz" ou "colar invisível", ele é confeccionado em fio de nylon transparente de alta resistência, dando a impressão de que os detalhes metálicos flutuam sobre a pele.', 53.30, 'FR Semijoias', 'Colares', 1, 'produto_6931109abd56720251204013954.jpeg.base64', 10, 1),
(2, 'Brinco Coração Olho de Gato', 'Um toque de romantismo e sofisticação para o seu dia a dia. Este brinco em formato de coração destaca-se pela beleza da pedra Olho de Gato, conhecida por seu tom perolado e brilho suave', 78.00, 'FR Semijoias', 'Brincos', NULL, 'produto_693111d50166b20251204014509.jpeg.base64', 10, 1),
(3, 'Conjunto Jade Coração Rosa', 'Feminilidade e cor se unem neste conjunto vibrante de pedras rosas com pingente de coração. O colar de contas traz presença ao look, enquanto os brincos delicados equilibram a produção com charme. Uma escolha romântica e cheia de vida, perfeita para realçar o visual com um toque de alegria e elegância.', 159.00, 'FR Semijoias', 'Conjuntos', NULL, 'produto_693114116b79b20251204015441.jpeg.base64', 10, 1),
(4, 'Brinco Rubi Pérola', 'O clássico requinte da pérola pendente com o requinte do brilho de uma argolinha cravejada. Os cristais em tom rubi trazem um ponto de cor vibrante que atualiza e rejuvenesce a joia. Um acessório com movimento e sofisticação, capaz de elevar instantaneamente desde o jeans até vestidos de festa.', 55.90, 'FR Semijoias', 'Brincos', NULL, 'produto_69311594c2caa20251204020108.jpeg.base64', 10, 1),
(5, 'Colar Duplo Jade Coração', 'Uma composição apaixonante que une a delicadeza da corrente dourada com a vibração intensa dos cristais vermelhos. O pingente vertical "LOVE" e o coração cravejado criam uma narrativa romântica e cheia de significado. Prático e estiloso, este mix já vem pronto para usar, garantindo um visual de camadas perfeito sem esforço.', 161.20, 'FR Semijoias', 'Colares', NULL, 'produto_6931163ab206d20251204020354.jpeg.base64', 10, 1),
(6, 'Escapulário Deus é Fiel', 'Expresse sua fé com elegância e significado através deste escapulário dourado com acabamento impecável. As medalhas retangulares trazem a frase "Deus é Fiel" e a Estrela de Davi, unindo proteção e simbolismo sagrado. Uma peça atemporal, perfeita para ser usada diariamente como um amuleto de devoção ou para presentear com carinho.', 68.90, 'FR Semijoias', 'Colares', NULL, 'produto_6931174111ed720251204020817.jpeg.base64', 10, 1),
(7, 'Conjunto Laços Dourados', 'A delicadeza em forma de joia: este conjunto de laços dourados exala feminilidade e acompanha a tendência romântica atual. O design vazado e fluido traz leveza à peça, garantindo um brilho sofisticado e discreto para o dia a dia. Uma escolha encantadora e atemporal, perfeita para presentear ou adicionar um toque de charme a qualquer produção.', 64.35, 'FR Semijoias', 'Conjuntos', NULL, 'produto_693118aff0cd920251204021423.jpeg.base64', 10, 1),
(8, 'Anel', 'Ousadia e estilo se encontram neste anel de dedinho bicolor, uma tendência absoluta entre as fashionistas. A fusão dos tons dourado e prateado garante versatilidade máxima, facilitando o mix com acessórios de qualquer cor. Com design moderno e acabamento impecável, é a peça perfeita para modernizar o visual e fugir do óbvio.', 69.50, 'FR Semijoias', 'Anéis', NULL, 'produto_69311a068d1de20251204022006.jpeg.base64', 10, 1),
(9, 'Brinco Triângulo Duo', 'Aposte na versatilidade máxima com estes brincos triangulares bicolores. O design moderno em forma de leque une o dourado e o prateado, criando uma peça marcante e arquitetônica que combina facilmente com qualquer outro acessório do seu porta-joias.', 53.90, 'FR Semijoias', 'Brincos', NULL, 'produto_69311df31d4d020251204023651.jpeg.base64', 10, 1),
(10, 'Colar de Esferas Diamantadas Prata', 'Eleve seu estilo com este colar de esferas prateadas texturizadas. O acabamento martelado reflete a luz de forma única, criando um visual contemporâneo, robusto e cheio de personalidade que não passa despercebido.', 175.00, 'FR Semijoias', 'Colares', NULL, 'produto_69311e5b93c9520251204023835.jpeg.base64', 10, 1),
(11, 'Anel Quadrado Dourado', 'Adicione um toque moderno e ousado ao seu visual com este anel geométrico prateado. Seu design quadrado e acabamento polido garantem sofisticação instantânea, tornando-o a peça statement perfeita para elevar qualquer look básico.', 90.90, 'FR Semijoias', 'Anéis', NULL, 'produto_69311ec28c78f20251204024018.jpeg.base64', 10, 1),
(12, 'Conjunto Coraçõa Resina Italiana vermelha', 'Adicione cor e paixão ao seu dia com este conjunto vibrante de coração com esmalte vermelho e acabamento dourado. O contraste perfeito para iluminar o rosto e as mãos com um toque divertido, romântico e elegante.', 84.45, 'FR Semijoias', 'Conjuntos', NULL, 'produto_69311f44e24fd20251204024228.jpeg.base64', 10, 1),
(13, 'Colar Coração Zircônia Vazado', 'Brilhe com delicadeza usando este colar de coração vazado em tom rose gold, cravejado com pedras cintilantes. Uma peça atemporal que une o romantismo do design à sofisticação do brilho, perfeita para ocasiões especiais ou para iluminar o dia a dia.', 95.90, 'FR Semijoias', 'Colares', NULL, 'produto_6931200a92d7120251204024546.jpeg.base64', 10, 1),
(14, 'Conjunto Coração Abaloado prata', 'Expresse seu estilo com este conjunto apaixonante de anel e brincos de coração prateados. O design escultural "puffy" e o acabamento polido trazem uma abordagem moderna e volumosa ao romantismo clássico.', 63.70, 'FR Semijoias', 'Conjuntos', NULL, 'produto_6931209bb943220251204024811.jpeg.base64', 10, 1),
(15, 'Tornozeleira Elos Achatados Dourada', 'Destaque seus passos com esta tornozeleira clássica de elos dourados. O design de corrente atemporal e o brilho intenso trazem um toque de glamour e feminilidade, sendo o acessório indispensável para os dias de sol e pernas de fora.', 58.50, 'FR Semijoias', 'Tornozeleiras', NULL, 'produto_693120fa2eb1d20251204024946.jpeg.base64', 10, 1),
(16, 'Bracelete Fino dourado', 'Adote a tendência mais quente do momento com este bracelete de braço minimalista prateado. Seu design fino, aberto e delicado oferece um toque sutil de modernidade e sensualidade, perfeito para complementar looks sem mangas.', 51.35, 'FR Semijoias', 'Braceletes', NULL, 'produto_693121bb5f92020251204025259.jpeg.base64', 10, 1),
(17, 'Brinco Argola Dourada', 'Imponha presença com estas argolas "chunky" douradas de alto brilho. O design volumoso, largo e o acabamento polido transformam um clássico em um verdadeiro statement de moda, garantindo poder e sofisticação instantânea a qualquer produção.', 72.90, 'FR Semijoias', 'Brincos', 1, 'produto_69312499be4cf20251204030513.jpeg.base64', 10, 1);

-- NÍVEIS DE ACESSO
INSERT INTO nivel_acesso (id_nivel, tipo) VALUES
(1, 'Administrador'),
(2, 'Cliente');

-- Senhas com password_hash (BCRYPT - seguro):
-- admin123 -> $2y$10$j7HX35qfCBuB8Z47ZFUrcesQpocsrm9awqDZ0Gj/bcMEcDSz3S10u
-- 123456   -> $2y$10$3DsJ05LgzAy.SJjeddxY2eOh4VfQZhv7lAd6RTXOi5ISLaXNrxWDW

INSERT INTO usuarios (id_usuario, nome, email, senha, cpf, telefone, endereco, data_nascimento, id_nivel, status) VALUES
(1, 'Fernanda Cristina', 'admin@frsemijoias.com', '$2y$10$j7HX35qfCBuB8Z47ZFUrcesQpocsrm9awqDZ0Gj/bcMEcDSz3S10u', '111.111.111-11', '(11) 99999-9999', 'Rua das Flores, 100 - São Paulo, SP', '1990-01-01', 1, 'ativo'),
(2, 'Maria Silva', 'maria.silva@email.com', '$2y$10$3DsJ05LgzAy.SJjeddxY2eOh4VfQZhv7lAd6RTXOi5ISLaXNrxWDW', '222.222.222-22', '(11) 98888-8888', 'Avenida Brasil, 200 - São Paulo, SP', '1985-05-15', 2, 'ativo'),
(3, 'João Santos', 'joao.santos@email.com', '$2y$10$3DsJ05LgzAy.SJjeddxY2eOh4VfQZhv7lAd6RTXOi5ISLaXNrxWDW', '333.333.333-33', '(11) 97777-7777', 'Rua do Sol, 300 - São Paulo, SP', '1992-08-20', 2, 'ativo'),
(4, 'Ana Costa', 'ana.costa@email.com', '$2y$10$3DsJ05LgzAy.SJjeddxY2eOh4VfQZhv7lAd6RTXOi5ISLaXNrxWDW', '444.444.444-44', '(11) 96666-6666', 'Praça Central, 400 - São Paulo, SP', '1988-12-10', 2, 'ativo'),
(5, 'Pedro Oliveira', 'pedro.oliveira@email.com', '$2y$10$3DsJ05LgzAy.SJjeddxY2eOh4VfQZhv7lAd6RTXOi5ISLaXNrxWDW', '555.555.555-55', '(11) 95555-5555', 'Alameda Santos, 500 - São Paulo, SP', '1995-03-25', 2, 'ativo'),
(6, 'Carla Mendes', 'carla.mendes@email.com', '$2y$10$3DsJ05LgzAy.SJjeddxY2eOh4VfQZhv7lAd6RTXOi5ISLaXNrxWDW', '666.666.666-66', '(11) 94444-4444', 'Rua Verde, 600 - São Paulo, SP', '1990-07-30', 2, 'ativo');

-- STATUS
INSERT INTO status (nome) VALUES
('Cancelado'),
('Pendente'),
('Aprovado'),
('Enviado');

-- PEDIDOS
INSERT INTO pedidos (id_pedido, produto_nome, preco, endereco, data_pedido, quantidade, id_status, descricao, id_produto, id_cliente) VALUES
(1, 'Brinco Argola Média', 239.80, 'Rua das Flores, 123 - São Paulo, SP', '2025-10-14', 2, 2, 'Dois colares ponto de luz', 5, 2),
(2, 'Conjunto Delicado', 799.90, 'Avenida Brasil, 456 - São Paulo, SP', '2025-10-14', 1, 2, 'Conjunto presente', 7, 3),
(3, 'Pulseira Elos', 399.90, 'Rua das Acácias, 789 - São Paulo, SP', '2025-10-13', 1, 4, 'Pedido especial', 17, 4),
(4, 'Anel Meia Aliança', 159.80, 'Rua do Ouro, 321 - São Paulo, SP', '2025-10-13', 1, 4, 'Dois anéis diferentes', 2, 5),
(5, 'Pulseira Riviera', 299.90, 'Rua das Pedras, 654 - São Paulo, SP', '2025-10-12', 1, 3, 'Pulseira Riviera', 15, 6),
(6, 'Ear Cuff Estrelas', 349.90, 'Rua das Flores, 123 - São Paulo, SP', '2025-10-11', 1, 3, 'Colar premium', 8, 2),
(7, 'Brinco Argola Pequena', 259.70, 'Avenida Brasil, 456 - São Paulo, SP', '2025-10-10', 3, 3, '3 brincos argola', 9, 3),
(8, 'Bracelete Liso', 449.90, 'Rua das Acácias, 789 - São Paulo, SP', '2025-10-09', 1, 1, 'Conjunto dourado', 16, 4),
(9, 'Brinco Gota Luxo', 329.85, 'Rua do Ouro, 321 - São Paulo, SP', '2025-10-08', 2, 4, 'Brincos para presente', 10, 5),
(10, 'Gargantilha Choker', 189.90, 'Rua das Pedras, 654 - São Paulo, SP', '2025-10-08', 1, 3, 'Brinco Pandora', 12, 6),
(11, 'Conjunto Noiva', 599.90, 'Rua das Flores, 123 - São Paulo, SP', '2025-10-05', 1, 4, 'Conjunto de noiva', 3, 2),
(12, 'Colar Gravatinha', 319.80, 'Avenida Brasil, 456 - São Paulo, SP', '2025-10-03', 1, 3, 'Pulseira berloque', 14, 3),
(13, 'Anel Solitário', 449.75, 'Rua das Acácias, 789 - São Paulo, SP', '2025-10-02', 5, 3, '5 anéis solitário', 1, 4),
(14, 'Brinco Argola Média', 299.70, 'Rua do Ouro, 321 - São Paulo, SP', '2025-09-28', 3, 3, '3 colares ponto de luz', 5, 5),
(15, 'Colar Ponto de Luz', 219.80, 'Rua das Pedras, 654 - São Paulo, SP', '2025-09-25', 2, 3, '2 ear cuff', 11, 6);