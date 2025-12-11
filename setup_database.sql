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
-- TABELA: redefinicao_senha
-- ==========================================
CREATE TABLE IF NOT EXISTS redefinicao_senha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expira DATETIME NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
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
(1, 'Colar Canutilho Duo', 'Minimalista e versátil, o Colar Canutilho Duo é perfeito para quem busca leveza e modernidade no visual. Seu design delicado garante brilho e sofisticação, tornando-o ideal para compor diferentes combinações e estilos, do casual ao elegante. Use sozinho para um toque discreto ou combine com outros colares para um look cheio de personalidade.', 53.30, 'FR Semijoias', 'Colares', 1, 'produto_6931109abd56720251204013954.jpeg.base64', 10, 1),
(2, 'Brinco Coração Olho de Gato', 'O Brinco Coração Olho de Gato destaca-se pelo brilho perolado e formato delicado. Ideal para quem busca romantismo e leveza, ele complementa produções sofisticadas ou casuais com charme sutil. Aposte para iluminar o rosto e criar composições modernas e femininas.', 78.00, 'FR Semijoias', 'Brincos', NULL, 'produto_693111d50166b20251204014509.jpeg.base64', 10, 1),
(3, 'Conjunto Jade Coração Rosa (Brinco + Colar 40 + 7 cm)', 'O Conjunto Jade Coração Rosa é uma celebração da delicadeza e do brilho feminino. O colar de contas rosas, com pingente de coração, adiciona cor e destaque ao colo, enquanto os brincos complementam com suavidade e elegância. Ideal para quem deseja transmitir romantismo e alegria, esta composição valoriza looks casuais ou sofisticados, tornando-se o ponto alto de qualquer produção.', 159.00, 'FR Semijoias', 'Conjuntos', NULL, 'produto_693114116b79b20251204015441.jpeg.base64', 10, 1),
(4, 'Brinco Rubi Pérola', 'O Brinco Rubi Pérola une o charme clássico da pérola ao toque vibrante dos cristais rubi. Seu design pendente traz movimento e elegância, iluminando o rosto com sofisticação. Perfeito para quem deseja destacar produções básicas ou sofisticadas com um acessório atemporal e cheio de personalidade.', 55.90, 'FR Semijoias', 'Brincos', NULL, 'produto_69311594c2caa20251204020108.jpeg.base64', 10, 1),
(5, 'Colar Duplo (Jade/Coração)', 'O Colar Duplo Jade/Coração é a escolha ideal para quem ama composições modernas e cheias de significado. A corrente dourada delicada contrasta com cristais vermelhos intensos, enquanto o pingente "LOVE" e o coração cravejado criam um visual romântico e sofisticado. Pronto para usar, ele garante camadas perfeitas e um toque especial ao look.', 161.20, 'FR Semijoias', 'Colares', NULL, 'produto_6931163ab206d20251204020354.jpeg.base64', 10, 1),
(6, 'Escapulário Deus é Fiel', 'O Escapulário Deus é Fiel traduz fé e proteção em um design elegante e atemporal. As medalhas douradas, com frase e símbolo sagrado, trazem significado e estilo para o dia a dia. Ideal para quem busca um amuleto de devoção com acabamento impecável, seja para uso próprio ou para presentear com carinho.', 68.90, 'FR Semijoias', 'Colares', NULL, 'produto_6931174111ed720251204020817.jpeg.base64', 10, 1),
(7, 'Conjunto Laços Dourados (Brinco + Colar)', 'O Conjunto Laços Dourados é pura delicadeza e charme. O design vazado dos laços dourados traz leveza e brilho sutil, acompanhando a tendência romântica com elegância. Ideal para presentear ou compor looks do dia a dia, é uma escolha encantadora e atemporal para quem valoriza feminilidade.', 64.35, 'FR Semijoias', 'Conjuntos', NULL, 'produto_693118aff0cd920251204021423.jpeg.base64', 10, 1),
(8, 'Anel Dedinho Duo Dourado', 'O Anel Dedinho Duo Dourado é sinônimo de ousadia e modernidade. O contraste entre dourado e prateado permite combinações versáteis, enquanto o design contemporâneo destaca o visual com personalidade. Perfeito para quem busca inovar e adicionar um toque fashionista ao look.', 69.50, 'FR Semijoias', 'Anéis', NULL, 'produto_69311a068d1de20251204022006.jpeg.base64', 10, 1),
(9, 'Brinco Triângulo Duo', 'O Brinco Triângulo Duo traz um design geométrico marcante, misturando dourado e prateado para máxima versatilidade. Ideal para compor looks modernos e arquitetônicos, é a peça perfeita para quem gosta de acessórios diferenciados e cheios de estilo.', 53.90, 'FR Semijoias', 'Brincos', NULL, 'produto_69311df31d4d020251204023651.jpeg.base64', 10, 1),
(10, 'Colar Esferas Diamantada Prata', 'O Colar Esferas Diamantada Prata destaca-se pelo acabamento texturizado e brilho intenso. As esferas prateadas refletem a luz de maneira única, criando um visual moderno e sofisticado. Uma peça robusta e cheia de personalidade para quem quer se destacar.', 175.00, 'FR Semijoias', 'Colares', NULL, 'produto_69311e5b93c9520251204023835.jpeg.base64', 10, 1),
(11, 'Anel Quadrado Dourado', 'O Anel Quadrado Dourado é a escolha certa para quem busca um acessório statement. O design geométrico e acabamento polido trazem sofisticação e ousadia, elevando qualquer produção com um toque moderno e elegante.', 90.90, 'FR Semijoias', 'Anéis', NULL, 'produto_69311ec28c78f20251204024018.jpeg.base64', 10, 1),
(12, 'Conjunto Coração Resina Italiana Vermelho', 'O Conjunto Coração Resina Italiana Vermelho é puro destaque e paixão. O esmalte vermelho vibrante contrasta com o dourado, iluminando o rosto e as mãos com elegância. Ideal para quem deseja um toque divertido e romântico no visual.', 84.45, 'FR Semijoias', 'Conjuntos', NULL, 'produto_69311f44e24fd20251204024228.jpeg.base64', 10, 1),
(13, 'Colar Coração Zircônia Vazado', 'O Colar Coração Zircônia Vazado une o romantismo do coração ao brilho sofisticado das pedras. O tom rose gold e o design delicado tornam a peça perfeita para iluminar o dia a dia ou ocasiões especiais com elegância.', 95.90, 'FR Semijoias', 'Colares', NULL, 'produto_6931200a92d7120251204024546.jpeg.base64', 10, 1),
(14, 'Conjunto Coração Abaulado Prata (Brinco + Anel)', 'O Conjunto Coração Abaulado Prata traz uma abordagem moderna ao clássico coração. O design volumoso e acabamento polido garantem destaque e sofisticação, ideal para quem busca acessórios esculturais e cheios de personalidade.', 63.70, 'FR Semijoias', 'Conjuntos', NULL, 'produto_6931209bb943220251204024811.jpeg.base64', 10, 1),
(15, 'Tornozeleira Elos Achatados Dourada', 'A Tornozeleira Elos Achatados Dourada é o acessório perfeito para dias de sol e looks despojados. O brilho intenso e o design clássico de corrente trazem glamour e feminilidade, destacando os passos com elegância.', 58.50, 'FR Semijoias', 'Tornozeleiras', NULL, 'produto_693120fa2eb1d20251204024946.jpeg.base64', 10, 1),
(16, 'Bracelete Fino Dourado', 'O Bracelete Fino Dourado é minimalista e elegante, perfeito para complementar produções sem mangas. O design aberto e delicado adiciona modernidade e sensualidade ao visual, seguindo as tendências atuais com sofisticação.', 51.35, 'FR Semijoias', 'Braceletes', NULL, 'produto_693121bb5f92020251204025259.jpeg.base64', 10, 1),
(17, 'Brinco Argola Dourado', 'O Brinco Argola Dourado é um clássico reinventado. O design volumoso e acabamento polido garantem presença e sofisticação, tornando-o a escolha ideal para quem deseja um acessório marcante e elegante em qualquer ocasião.', 72.90, 'FR Semijoias', 'Brincos', 1, 'produto_69312499be4cf20251204030513.jpeg.base64', 10, 1),
(18, 'Conjunto Sextavado Turquesa (Brinco + Colar)', 'Conquiste um visual sofisticado e moderno com o Conjunto Sextavado Turquesa. O design geométrico das pedras em tom turquesa transmite elegância e personalidade, enquanto o acabamento dourado realça ainda mais o brilho das peças. Ideal para quem busca um toque de cor e exclusividade, este conjunto é perfeito para compor looks versáteis, do casual ao elegante, destacando-se em qualquer ocasião', 128.70, 'FR Semijoias', 'Conjuntos', NULL, 'produto_6939025d4267320251210051717.jpeg.base64', 10, 1),
(19, 'Piercing Dourado Liso', 'Minimalista e versátil, o Piercing Dourado Liso é perfeito para quem busca um toque de modernidade sem abrir mão da elegância. Seu acabamento polido garante brilho e sofisticação, tornando-o ideal para compor diferentes combinações e estilos, do casual ao sofisticado. Use sozinho para um visual discreto ou combine com outros piercings para um look cheio de personalidade.', 15.60, 'FR Semijoias', 'Piercings', NULL, 'produto_6939036cea3d320251210052148.jpeg.base64', 10, 1),
(20, 'Pulseira Cartier Dourada', 'Clássica e atemporal, a Pulseira Cartier Dourada é o acessório perfeito para quem valoriza elegância e sofisticação. Seu design icônico com elos entrelaçados proporciona brilho e destaque ao pulso, tornando-se uma peça coringa para compor desde looks casuais até produções mais refinadas. Use sozinha para um visual minimalista ou combine com outras pulseiras para um mix moderno e cheio de estilo.', 81.90, 'FR Semijoias', 'Pulseiras', NULL, 'produto_69390499cdcf220251210052649.jpeg.base64', 10, 1),
(21, 'Brinco Argola Earhook', 'O Brinco Argola Earhook traz um design contemporâneo e elegante, perfeito para quem busca modernidade com um toque de sofisticação. Seu formato inovador se encaixa de forma confortável na orelha, proporcionando um visual marcante e cheio de estilo. Ideal para compor produções versáteis, do dia a dia a ocasiões especiais, garantindo destaque e personalidade ao seu look.', 68.90, 'FR Semijoias', 'Brincos', NULL, 'produto_693904f0822f120251210052816.jpeg.base64', 10, 1),
(22, 'Choker Olhos Gregos', 'O Choker Olhos Gregos une proteção e estilo em uma peça delicada e cheia de significado. Com detalhes de olhos gregos ao longo da corrente, este colar traz um toque moderno e místico ao visual, além de ser perfeito para compor mix de colares ou ser usado sozinho como destaque. Ideal para quem busca um acessório versátil, elegante e com energia positiva para o dia a dia.', 37.00, 'FR Semijoias', 'Colares', NULL, 'produto_69390583ae48b20251210053043.jpeg.base64', 10, 1),
(23, 'Brinco Flor Dourado', 'Delicado e romântico, o Brinco Flor Dourado traz o charme das flores em um design minimalista e elegante. Seu acabamento dourado garante brilho sutil e sofisticação, tornando-o perfeito para compor looks do dia a dia ou dar um toque especial em ocasiões especiais. Uma peça versátil que combina com todos os estilos e realça a feminilidade com leveza.', 43.50, 'FR Semijoias', 'Brincos', NULL, 'produto_6939065acbcf920251210053418.jpeg.base64', 10, 1),
(24, 'Brinco Gota Fina Dourado', 'O Brinco Gota Fina Dourado traz elegância e modernidade em um design minimalista e sofisticado. Seu formato alongado valoriza o rosto e adiciona um toque de brilho sutil ao visual, sendo perfeito para compor produções tanto do dia a dia quanto para ocasiões especiais. Uma peça versátil que combina com diferentes estilos e destaca a delicadeza com muito charme.', 28.50, 'FR Semijoias', 'Brincos', NULL, 'produto_693906af7a5a920251210053543.jpeg.base64', 10, 1),
(25, 'Brinco Maxi Oval Polido Prata', 'O Brinco Maxi Oval Polido Prata é a escolha perfeita para quem busca impacto e sofisticação em um só acessório. Com design oval robusto e acabamento polido de alto brilho, este brinco valoriza qualquer produção, do casual ao elegante. Ideal para quem gosta de peças marcantes e modernas, ele garante destaque imediato ao visual e combina facilmente com diferentes estilos e ocasiões.', 70.00, 'FR Semijoias', 'Brincos', NULL, 'produto_693907020dd1720251210053706.jpeg.base64', 10, 1),
(26, 'Colar Aro Fino Prata', 'O Colar Aro Fino Prata é sinônimo de elegância minimalista. Com design delicado e acabamento polido, ele valoriza o colo com um toque moderno e sofisticado. Perfeito para ser usado sozinho ou combinado com outros colares, é uma peça versátil que complementa desde produções básicas até looks mais elaborados, trazendo leveza e brilho na medida certa.', 64.00, 'FR Semijoias', 'Colares', NULL, 'produto_6939077c46aad20251210053908.jpeg.base64', 10, 1),
(27, 'Brinco Dupla Gota Prata', 'O Brinco Dupla Gota Prata traz delicadeza e modernidade em um design minimalista com duas gotas sobrepostas. Seu acabamento prateado garante brilho sutil e versatilidade, tornando-o perfeito para compor looks do dia a dia ou dar um toque especial em ocasiões mais elegantes. Uma peça leve, confortável e cheia de charme para realçar sua beleza com discrição e estilo.', 25.00, 'FR Semijoias', 'Brincos', NULL, 'produto_693907d5ccef520251210054037.jpeg.base64', 10, 1),
(28, 'Conjunto Flores Prateadas (Brinco + Colar)', 'O Conjunto Flores Prateadas é perfeito para quem busca elegância e destaque em uma única composição. Com design floral marcante e acabamento prateado de alto brilho, o conjunto valoriza o colo e o rosto, trazendo sofisticação e feminilidade ao visual. Ideal para ocasiões especiais ou para transformar produções básicas em looks cheios de personalidade e charme.', 111.00, 'FR Semijoias', 'Conjuntos', NULL, 'produto_693908289d79c20251210054200.jpeg.base64', 10, 1),
(29, 'Pulseira Berloques Flores Prata', 'A Pulseira Berloques Flores Prata é delicada e cheia de charme, perfeita para quem gosta de acessórios com detalhes especiais. Os berloques em formato de flores trazem movimento e um toque romântico ao pulso, enquanto o acabamento prateado garante brilho e versatilidade. Ideal para compor looks do dia a dia ou dar um toque feminino em ocasiões especiais.', 62.20, 'FR Semijoias', 'Pulseiras', NULL, 'produto_69390893dc4d220251210054347.jpeg.base64', 10, 1),
(30, 'Brinco Gota Resina Nude White', 'O Brinco Gota Resina Nude White combina delicadeza e modernidade em um design atemporal. Com formato de gota e acabamento em resina, ele traz leveza e sofisticação ao visual, sendo perfeito para compor looks elegantes ou dar um toque especial ao dia a dia. Versátil e confortável, é uma peça que valoriza todos os estilos com discrição e charme.', 36.00, 'FR Semijoias', 'Brincos', NULL, 'produto_693909a06f4e220251210054816.jpeg.base64', 10, 1),
(31, 'Colar Pérolas/Esferas Prata (45 + 5 cm)', 'O Colar Pérolas/Esferas Prata é uma peça sofisticada que une o clássico ao moderno. Suas esferas prateadas de acabamento polido proporcionam brilho e elegância, tornando o colar perfeito para valorizar produções do dia a dia ou ocasiões especiais. Versátil e marcante, ele destaca o colo com personalidade e combina facilmente com outros acessórios para um visual ainda mais estiloso.', 204.70, 'FR Semijoias', 'Colares', NULL, 'produto_69390a24daa7f20251210055028.jpeg.base64', 10, 1),
(32, 'Brinco Oval Botão Cherry Red', 'O Brinco Oval Botão Cherry Red é a escolha ideal para quem busca um toque de cor e elegância no visual. Com design clássico de botão e acabamento em tom vermelho cereja, ele destaca o rosto com sofisticação e modernidade. Versátil, pode ser usado tanto em produções do dia a dia quanto em ocasiões especiais, trazendo charme e personalidade ao seu look.', 40.50, 'FR Semijoias', 'Brincos', NULL, 'produto_69390ace5a32e20251210055318.jpeg.base64', 10, 1),
(33, 'Conjunto Elo Português Dourado/Branco (Brinco + Colar 45 cm)', 'O Conjunto Elo Português Dourado/Branco é sinônimo de sofisticação e elegância. Com elos dourados robustos e detalhes em branco, o conjunto traz um visual moderno e atemporal, perfeito para valorizar qualquer produção. As pérolas pendentes adicionam um toque clássico e refinado, tornando a peça ideal para ocasiões especiais ou para transformar looks básicos em composições cheias de estilo e personalidade.', 244.50, 'FR Semijoias', 'Conjuntos', NULL, 'produto_69390b193171320251210055433.jpeg.base64', 10, 1),
(34, 'Pulseira Elo Português Dourada (19 cm)', 'A Pulseira Elo Português Dourada é um clássico indispensável para quem valoriza elegância e versatilidade. Com elos dourados robustos e acabamento polido, ela traz brilho e sofisticação ao pulso, sendo perfeita para usar sozinha ou em combinações com outras pulseiras. Ideal para compor desde looks casuais até produções mais refinadas, garantindo sempre um toque de estilo e personalidade.', 95.20, 'FR Semijoias', 'Pulseiras', NULL, 'produto_69390b612dd1b20251210055545.jpeg.base64', 10, 1),
(35, 'Conjunto Árvore da Vida Dourado (Colar + Brinco)', 'O Conjunto Árvore da Vida Dourado simboliza força, crescimento e renovação, trazendo significado e beleza para o seu visual. Com acabamento dourado e design detalhado, é perfeito para quem busca um acessório elegante e cheio de significado. Ideal para presentear ou compor looks especiais, este conjunto valoriza o colo e o rosto com delicadeza e sofisticação.', 115.70, 'FR Semijoias', 'Conjuntos', NULL, 'produto_69390b9f8de1420251210055647.jpeg.base64', 10, 1),
(36, 'Choker Cartier (32 + 10 cm)', 'Conquiste um visual sofisticado e moderno com o Conjunto Sextavado Turquesa. O design geométrico das pedras em tom turquesa transmite elegância e personalidade, enquanto o acabamento dourado realça ainda mais o brilho das peças. Ideal para quem busca um toque de cor e exclusividade, este conjunto é perfeito para compor looks versáteis, do casual ao elegante, destacando-se em qualquer ocasião', 71.50, 'FR Semijoias', 'Colares', NULL, 'produto_6939025d4267320251210051717.jpeg.base64', 10, 1),
(37, 'Brinco Quadrado Branco Resina', 'Minimalista e versátil, o Piercing Dourado Liso é perfeito para quem busca um toque de modernidade sem abrir mão da elegância. Seu acabamento polido garante brilho e sofisticação, tornando-o ideal para compor diferentes combinações e estilos, do casual ao sofisticado. Use sozinho para um visual discreto ou combine com outros piercings para um look cheio de personalidade.', 27.30, 'FR Semijoias', 'Brincos', NULL, 'produto_6939036cea3d320251210052148.jpeg.base64', 10, 1),
(38, 'Pulseira Ágata Rosa', 'Clássica e atemporal, a Pulseira Cartier Dourada é o acessório perfeito para quem valoriza elegância e sofisticação. Seu design icônico com elos entrelaçados proporciona brilho e destaque ao pulso, tornando-se uma peça coringa para compor desde looks casuais até produções mais refinadas. Use sozinha para um visual minimalista ou combine com outras pulseiras para um mix moderno e cheio de estilo.', 63.70, 'FR Semijoias', 'Pulseiras', NULL, 'produto_69390499cdcf220251210052649.jpeg.base64', 10, 1),
(39, 'Colar Elos Maciço Prata', 'O Choker Olhos Gregos une proteção e estilo em uma peça delicada e cheia de significado. Com detalhes de olhos gregos ao longo da corrente, este colar traz um toque moderno e místico ao visual, além de ser perfeito para compor mix de colares ou ser usado sozinho como destaque. Ideal para quem busca um acessório versátil, elegante e com energia positiva para o dia a dia.', 50.00, 'FR Semijoias', 'Colares', NULL, 'produto_69390583ae48b20251210053043.jpeg.base64', 10, 1),
(40, 'Brinco Flor White', 'Delicado e romântico, o Brinco Flor Dourado traz o charme das flores em um design minimalista e elegante. Seu acabamento dourado garante brilho sutil e sofisticação, tornando-o perfeito para compor looks do dia a dia ou dar um toque especial em ocasiões especiais. Uma peça versátil que combina com todos os estilos e realça a feminilidade com leveza.', 31.20, 'FR Semijoias', 'Brincos', NULL, 'produto_6939065acbcf920251210053418.jpeg.base64', 10, 1),
(41, 'Colar Pérolas White/Gold (40 cm)', 'O Brinco Gota Fina Dourado traz elegância e modernidade em um design minimalista e sofisticado. Seu formato alongado valoriza o rosto e adiciona um toque de brilho sutil ao visual, sendo perfeito para compor produções tanto do dia a dia quanto para ocasiões especiais. Uma peça versátil que combina com diferentes estilos e destaca a delicadeza com muito charme.', 121.50, 'FR Semijoias', 'Colares', NULL, 'produto_693906af7a5a920251210053543.jpeg.base64', 10, 1),
(42, 'Anel 3 Aros Prata', 'O Brinco Maxi Oval Polido Prata é a escolha perfeita para quem busca impacto e sofisticação em um só acessório. Com design oval robusto e acabamento polido de alto brilho, este brinco valoriza qualquer produção, do casual ao elegante. Ideal para quem gosta de peças marcantes e modernas, ele garante destaque imediato ao visual e combina facilmente com diferentes estilos e ocasiões.', 84.00, 'FR Semijoias', 'Anéis', NULL, 'produto_693907020dd1720251210053706.jpeg.base64', 10, 1),
(43, 'Colar Elo Português Prata', 'O Colar Aro Fino Prata é sinônimo de elegância minimalista. Com design delicado e acabamento polido, ele valoriza o colo com um toque moderno e sofisticado. Perfeito para ser usado sozinho ou combinado com outros colares, é uma peça versátil que complementa desde produções básicas até looks mais elaborados, trazendo leveza e brilho na medida certa.', 77.00, 'FR Semijoias', 'Colares', NULL, 'produto_6939077c46aad20251210053908.jpeg.base64', 10, 1),
(44, 'Brinco Gota Resina Cherry Red', 'O Brinco Gota Resina Nude White combina delicadeza e modernidade em um design atemporal. Com formato de gota e acabamento em resina, ele traz leveza e sofisticação ao visual, sendo perfeito para compor looks elegantes ou dar um toque especial ao dia a dia. Versátil e confortável, é uma peça que valoriza todos os estilos com discrição e charme.', 36.00, 'FR Semijoias', 'Brincos', NULL, 'produto_693909a06f4e220251210054816.jpeg.base64', 10, 1),
(45, 'Brinco Botão Cinza', 'O Colar Pérolas/Esferas Prata é uma peça sofisticada que une o clássico ao moderno. Suas esferas prateadas de acabamento polido proporcionam brilho e elegância, tornando o colar perfeito para valorizar produções do dia a dia ou ocasiões especiais. Versátil e marcante, ele destaca o colo com personalidade e combina facilmente com outros acessórios para um visual ainda mais estiloso.', 67.40, 'FR Semijoias', 'Brincos', NULL, 'produto_69390a24daa7f20251210055028.jpeg.base64', 10, 1),
(46, 'Colar Aro Fixo Dourado', 'O Brinco Oval Botão Cherry Red é a escolha ideal para quem busca um toque de cor e elegância no visual. Com design clássico de botão e acabamento em tom vermelho cereja, ele destaca o rosto com sofisticação e modernidade. Versátil, pode ser usado tanto em produções do dia a dia quanto em ocasiões especiais, trazendo charme e personalidade ao seu look.', 140.20, 'FR Semijoias', 'Colares', NULL, 'produto_69390ace5a32e20251210055318.jpeg.base64', 10, 1),
(47, 'Pulseira Elos Alongados Dourada (19 cm)', 'A Pulseira Elo Português Dourada é um clássico indispensável para quem valoriza elegância e versatilidade. Com elos dourados robustos e acabamento polido, ela traz brilho e sofisticação ao pulso, sendo perfeita para usar sozinha ou em combinações com outras pulseiras. Ideal para compor desde looks casuais até produções mais refinadas, garantindo sempre um toque de estilo e personalidade.', 119.90, 'FR Semijoias', 'Pulseiras', NULL, 'produto_69390b612dd1b20251210055545.jpeg.base64', 10, 1),
(48, 'Bracelete Aro Turquesa', 'Conquiste um visual sofisticado e moderno com o Conjunto Sextavado Turquesa. O design geométrico das pedras em tom turquesa transmite elegância e personalidade, enquanto o acabamento dourado realça ainda mais o brilho das peças. Ideal para quem busca um toque de cor e exclusividade, este conjunto é perfeito para compor looks versáteis, do casual ao elegante, destacando-se em qualquer ocasião', 101.40, 'FR Semijoias', 'Braceletes', NULL, 'produto_6939025d4267320251210051717.jpeg.base64', 10, 1),
(49, 'Conjunto Turquesa Jade Pérola Rosa (Brinco + Pulseira)', 'Clássica e atemporal, a Pulseira Cartier Dourada é o acessório perfeito para quem valoriza elegância e sofisticação. Seu design icônico com elos entrelaçados proporciona brilho e destaque ao pulso, tornando-se uma peça coringa para compor desde looks casuais até produções mais refinadas. Use sozinha para um visual minimalista ou combine com outras pulseiras para um mix moderno e cheio de estilo.', 109.20, 'FR Semijoias', 'Conjuntos', NULL, 'produto_69390499cdcf220251210052649.jpeg.base64', 10, 1),
(50, 'Colar Elos Prata 3x1 (60 cm)', 'O Choker Olhos Gregos une proteção e estilo em uma peça delicada e cheia de significado. Com detalhes de olhos gregos ao longo da corrente, este colar traz um toque moderno e místico ao visual, além de ser perfeito para compor mix de colares ou ser usado sozinho como destaque. Ideal para quem busca um acessório versátil, elegante e com energia positiva para o dia a dia.', 90.90, 'FR Semijoias', 'Colares', NULL, 'produto_69390583ae48b20251210053043.jpeg.base64', 10, 1);

-- NÍVEIS DE ACESSO
INSERT INTO nivel_acesso (id_nivel, tipo) VALUES
(1, 'Administrador'),
(2, 'Cliente');

-- USUÁRIOS
-- Regina!180913# -> $2y$10$8UzfczGzBXlW.3TtDaX/beHAPC9zChH8.CQPgukIk0xOI5DSIURAu
-- 123456 -> $2y$10$3DsJ05LgzAy.SJjeddxY2eOh4VfQZhv7lAd6RTXOi5ISLaXNrxWDW
INSERT INTO usuarios (id_usuario, nome, email, senha, cpf, telefone, endereco, data_nascimento, id_nivel, status) VALUES
(1, 'Fernanda Cristina', 'nogueira.fer1979@gmail.com', '$2y$10$8UzfczGzBXlW.3TtDaX/beHAPC9zChH8.CQPgukIk0xOI5DSIURAu', '27416824885', '11930651979', 'Avenida da Paz, 478, Apto. 55-B - Guarulhos, SP', '1979-05-18', 1, 'ativo'),
(2, 'Maria Silva', 'maria.silva@email.com', '$2y$10$3DsJ05LgzAy.SJjeddxY2eOh4VfQZhv7lAd6RTXOi5ISLaXNrxWDW', '22222222222', '11988888888', 'Avenida Brasil, 200 - São Paulo, SP', '1985-05-15', 2, 'ativo'),
(3, 'João Santos', 'joao.santos@email.com', '$2y$10$3DsJ05LgzAy.SJjeddxY2eOh4VfQZhv7lAd6RTXOi5ISLaXNrxWDW', '33333333333', '11977777777', 'Rua do Sol, 300 - São Paulo, SP', '1992-08-20', 2, 'ativo');

-- STATUS
INSERT INTO status (nome) VALUES
('Cancelado'),
('Pendente'),
('Aprovado'),
('Enviado'),
('Entregue');

-- PEDIDOS
INSERT INTO pedidos (id_pedido, produto_nome, preco, endereco, data_pedido, quantidade, id_status, descricao, id_produto, id_cliente) VALUES
(1, 'Colar Canutilho Duo', 53.30, 'Rua das Flores, 123 - São Paulo, SP', '2025-12-09', 1, 3, 'Colar bonito', 1, 2),
(2, 'Brinco Coração Olho de Gato', 78.00, 'Avenida Brasil, 200 - São Paulo, SP', '2025-12-10', 2, 2, 'Brinco delicado', 2, 2),
(3, 'Conjunto Jade Coração Rosa', 159.00, 'Rua do Sol, 300 - São Paulo, SP', '2025-12-11', 1, 1, 'Conjunto feminino', 3, 3),
(4, 'Brinco Rubi Pérola', 55.90, 'Avenida da Paz, 478 - Guarulhos, SP', '2025-12-12', 1, 3, 'Brinco clássico', 4, 1),
(5, 'Colar Duplo Jade/Coração', 161.20, 'Rua das Flores, 123 - São Paulo, SP', '2025-12-13', 1, 2, 'Colar moderno', 5, 2),
(6, 'Escapulário Deus é Fiel', 68.90, 'Avenida Brasil, 200 - São Paulo, SP', '2025-12-14', 1, 1, 'Escapulário religioso', 6, 2),
(7, 'Conjunto Laços Dourados', 64.35, 'Rua do Sol, 300 - São Paulo, SP', '2025-12-15', 1, 3, 'Conjunto delicado', 7, 3),
(8, 'Anel Dedinho Duo Dourado', 69.50, 'Avenida da Paz, 478 - Guarulhos, SP', '2025-12-16', 1, 2, 'Anel moderno', 8, 1),
(9, 'Brinco Triângulo Duo', 53.90, 'Rua das Flores, 123 - São Paulo, SP', '2025-12-17', 1, 1, 'Brinco geométrico', 9, 2),
(10, 'Colar Esferas Diamantada Prata', 175.00, 'Avenida Brasil, 200 - São Paulo, SP', '2025-12-18', 1, 3, 'Colar sofisticado', 10, 2);