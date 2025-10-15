-- ========================================
-- SCRIPT DE POPULAÇÃO DO BANCO DE DADOS
-- Projeto: FR Semijoias
-- Descrição: Dados de teste para desenvolvimento
-- ATUALIZADO: Usando snake_case (padrão SQL)
-- ========================================

USE fr_semijoias;

-- Limpa dados existentes (ordem importante por causa das FKs)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE pedidos;
TRUNCATE TABLE usuarios;
TRUNCATE TABLE produtos;
TRUNCATE TABLE promocoes;
TRUNCATE TABLE categorias;
TRUNCATE TABLE marcas;
SET FOREIGN_KEY_CHECKS = 1;

-- ========================================
-- MARCAS
-- ========================================
INSERT INTO marcas (id_marca, nome, descricao) VALUES
(1, 'Vivara', 'Joias e semijoias de alta qualidade'),
(2, 'Pandora', 'Joias personalizáveis e berloques exclusivos'),
(3, 'Rommanel', 'Semijoias folheadas a ouro'),
(4, 'Life by Vivara', 'Semijoias modernas e acessíveis'),
(5, 'Carla Amorim', 'Design exclusivo e pedras brasileiras');

-- ========================================
-- CATEGORIAS
-- ========================================
INSERT INTO categorias (id_categoria, nome, descricao) VALUES
(1, 'Anéis', 'Anéis diversos em ouro, prata e pedras'),
(2, 'Brincos', 'Brincos de argola, ponto de luz, ear cuff'),
(3, 'Colares', 'Colares delicados e gargantilhas'),
(4, 'Pulseiras', 'Pulseiras finas e braceletes'),
(5, 'Conjuntos', 'Kits de joias combinadas');

-- ========================================
-- PROMOÇÕES
-- ========================================
INSERT INTO promocoes (id_promocao, nome, desconto, data_inicio, data_fim, ativa) VALUES
(1, 'Black Friday 2025', 30.00, '2025-11-20', '2025-11-30', true),
(2, 'Natal Especial', 20.00, '2025-12-01', '2025-12-25', true),
(3, 'Dia das Mães', 25.00, '2026-05-01', '2026-05-12', false);

-- ========================================
-- PRODUTOS
-- ========================================
INSERT INTO produtos (id_produto, nome, descricao, unidade_estoque, preco, id_categoria, id_marca, id_promocao, material, peso, garantia) VALUES
-- Anéis
(1, 'Anel Solitário', 'Anel com zircônia central', 50, 89.90, 1, 3, NULL, 'Ouro 18k', 2.50, '1 ano'),
(2, 'Anel Meia Aliança', 'Anel com zircônias laterais', 30, 129.90, 1, 1, 1, 'Ouro 18k', 3.00, '2 anos'),
(3, 'Anel Trio', 'Set com 3 anéis empilháveis', 25, 149.90, 1, 4, NULL, 'Prata 925', 4.20, '6 meses'),
(4, 'Anel Life', 'Anel delicado folheado', 40, 69.90, 1, 4, 2, 'Folheado a ouro', 1.80, '6 meses'),

-- Brincos
(5, 'Brinco Argola Média', 'Argola lisa 3cm', 60, 119.90, 2, 2, NULL, 'Prata 925', 5.00, '1 ano'),
(6, 'Brinco Ponto de Luz', 'Brinco com zircônia 6mm', 80, 79.90, 2, 3, 1, 'Ouro 18k', 2.00, '1 ano'),
(7, 'Brinco Argola Grande', 'Argola grossa 5cm', 35, 179.90, 2, 1, NULL, 'Ouro 18k', 8.50, '2 anos'),
(8, 'Ear Cuff Estrelas', 'Brinco sem furo', 45, 89.90, 2, 4, 2, 'Prata 925', 3.20, '6 meses'),
(9, 'Brinco Argola Pequena', 'Argola fina 1.5cm', 100, 79.90, 2, 3, NULL, 'Folheado a ouro', 1.50, '6 meses'),
(10, 'Brinco Gota Luxo', 'Brinco de gota com pedras', 20, 139.90, 2, 5, NULL, 'Ouro 18k', 4.80, '2 anos'),

-- Colares
(11, 'Colar Ponto de Luz', 'Colar delicado com zircônia', 55, 119.90, 3, 3, 1, 'Ouro 18k', 3.50, '1 ano'),
(12, 'Gargantilha Choker', 'Gargantilha ajustável', 40, 99.90, 3, 4, NULL, 'Prata 925', 4.00, '6 meses'),
(13, 'Colar Corrente Grossa', 'Corrente statement', 25, 189.90, 3, 1, NULL, 'Ouro 18k', 12.00, '2 anos'),
(14, 'Colar Gravatinha', 'Gravatinha com zircônia', 50, 129.90, 3, 2, 2, 'Prata 925', 5.50, '1 ano'),

-- Pulseiras
(15, 'Pulseira Riviera', 'Pulseira com zircônias sequenciais', 30, 299.90, 4, 1, NULL, 'Ouro 18k', 8.00, '2 anos'),
(16, 'Pulseira Berloque', 'Pulseira com berloques Pandora', 25, 249.90, 4, 2, 1, 'Prata 925', 12.50, '1 ano'),
(17, 'Pulseira Elos', 'Pulseira de elos cartier', 35, 399.90, 4, 5, NULL, 'Ouro 18k', 15.00, '2 anos'),
(18, 'Bracelete Liso', 'Bracelete aberto ajustável', 50, 149.90, 4, 4, 2, 'Folheado a ouro', 6.00, '6 meses'),

-- Conjuntos
(19, 'Conjunto Delicado', 'Colar + brincos + anel', 15, 799.90, 5, 1, NULL, 'Ouro 18k', 20.00, '2 anos'),
(20, 'Conjunto Noiva', 'Set completo para casamento', 10, 599.90, 5, 5, NULL, 'Ouro 18k', 18.50, '2 anos');

-- ========================================
-- USUÁRIOS
-- Senhas: Admin = 'admin123' | Outros = '123456' (MD5)
-- ========================================
INSERT INTO usuarios (id_usuario, nome, email, senha, cpf, telefone, data_nascimento, id_nivel) VALUES
(1, 'Fernanda Cristina', 'admin@frsemijoias.com', MD5('admin123'), '111.111.111-11', '(11) 99999-9999', '1990-01-01', 1),
(2, 'Maria Silva', 'maria.silva@email.com', MD5('123456'), '222.222.222-22', '(11) 98888-8888', '1985-05-15', 2),
(3, 'João Santos', 'joao.santos@email.com', MD5('123456'), '333.333.333-33', '(11) 97777-7777', '1992-08-20', 2),
(4, 'Ana Costa', 'ana.costa@email.com', MD5('123456'), '444.444.444-44', '(11) 96666-6666', '1988-12-10', 2),
(5, 'Pedro Oliveira', 'pedro.oliveira@email.com', MD5('123456'), '555.555.555-55', '(11) 95555-5555', '1995-03-25', 2),
(6, 'Carla Mendes', 'carla.mendes@email.com', MD5('123456'), '666.666.666-66', '(11) 94444-4444', '1990-07-30', 2);

-- ========================================
-- PEDIDOS
-- ========================================
INSERT INTO pedidos (id_pedido, cliente_nome, data_pedido, preco, status, observacoes, id_produto, quantidade) VALUES
-- Pedidos recentes (última semana)
(1, 'Maria Silva', '2025-10-14 10:30:00', 239.80, 'Pendente', 'Dois colares ponto de luz', 5, 2),
(2, 'João Santos', '2025-10-14 11:15:00', 799.90, 'Em Processamento', 'Conjunto presente', 19, 1),
(3, 'Ana Costa', '2025-10-13 14:20:00', 399.90, 'Concluído', 'Pedido especial', 17, 1),
(4, 'Pedro Oliveira', '2025-10-13 16:45:00', 159.80, 'Concluído', 'Dois anéis diferentes', 2, 1),
(5, 'Carla Mendes', '2025-10-12 09:10:00', 299.90, 'Concluído', 'Pulseira Riviera', 15, 1),

-- Pedidos da semana passada
(6, 'Maria Silva', '2025-10-11 13:30:00', 349.90, 'Concluído', 'Colar premium', 8, 1),
(7, 'João Santos', '2025-10-10 15:20:00', 259.70, 'Concluído', '3 brincos argola', 9, 3),
(8, 'Ana Costa', '2025-10-09 10:00:00', 449.90, 'Cancelado', 'Conjunto dourado', 18, 1),
(9, 'Pedro Oliveira', '2025-10-08 11:30:00', 329.85, 'Concluído', 'Brincos para presente', 10, 2),
(10, 'Carla Mendes', '2025-10-08 14:15:00', 189.90, 'Concluído', 'Brinco Pandora', 12, 1),

-- Pedidos mais antigos
(11, 'Maria Silva', '2025-10-05 16:00:00', 599.90, 'Concluído', 'Conjunto de noiva', 20, 1),
(12, 'João Santos', '2025-10-03 12:30:00', 319.80, 'Concluído', 'Pulseira berloque', 14, 1),
(13, 'Ana Costa', '2025-10-02 09:45:00', 449.75, 'Concluído', '5 anéis solitário', 1, 5),
(14, 'Pedro Oliveira', '2025-09-28 14:20:00', 299.70, 'Concluído', '3 colares ponto de luz', 5, 3),
(15, 'Carla Mendes', '2025-09-25 10:10:00', 219.80, 'Concluído', '2 ear cuff', 11, 2);

-- ========================================
-- ESTATÍSTICAS FINAIS
-- ========================================
SELECT 
    'Dados inseridos com sucesso!' AS Status,
    (SELECT COUNT(*) FROM marcas) AS Total_Marcas,
    (SELECT COUNT(*) FROM categorias) AS Total_Categorias,
    (SELECT COUNT(*) FROM promocoes) AS Total_Promocoes,
    (SELECT COUNT(*) FROM produtos) AS Total_Produtos,
    (SELECT COUNT(*) FROM usuarios) AS Total_Usuarios,
    (SELECT COUNT(*) FROM pedidos) AS Total_Pedidos,
    (SELECT SUM(preco) FROM pedidos) AS Receita_Total;
