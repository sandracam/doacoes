-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS sistema_doacoes;
USE sistema_doacoes;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    endereco TEXT,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_email CHECK (email LIKE '%@%.%')
);

-- Tabela de categorias de livros
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT
);

-- Tabela de autores
CREATE TABLE autores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    biografia TEXT,
    data_nascimento DATE
);

-- Tabela de livros
CREATE TABLE livros (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(200) NOT NULL,
    id_categoria INT,
    ano_publicacao INT,
    editora VARCHAR(100),
    estado_conservacao ENUM('Novo', 'Ótimo', 'Bom', 'Regular') NOT NULL,
    descricao TEXT,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);

-- Tabela de relação entre livros e autores (muitos para muitos)
CREATE TABLE livros_autores (
    id_livro INT,
    id_autor INT,
    PRIMARY KEY (id_livro, id_autor),
    FOREIGN KEY (id_livro) REFERENCES livros(id),
    FOREIGN KEY (id_autor) REFERENCES autores(id)
);

-- Tabela de doações
CREATE TABLE doacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_livro INT,
    id_doador INT,
    id_receptor INT,
    data_doacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_retirada DATETIME,
    status ENUM('Disponível', 'Reservado', 'Doado') DEFAULT 'Disponível',
    observacoes TEXT,
    FOREIGN KEY (id_livro) REFERENCES livros(id),
    FOREIGN KEY (id_doador) REFERENCES usuarios(id),
    FOREIGN KEY (id_receptor) REFERENCES usuarios(id)
);

-- Inserindo algumas categorias básicas
INSERT INTO categorias (nome, descricao) VALUES
('Romance', 'Livros de ficção com foco em histórias românticas'),
('Ficção Científica', 'Livros que exploram conceitos científicos e tecnológicos'),
('História', 'Livros sobre eventos históricos e civilizações'),
('Autoajuda', 'Livros focados em desenvolvimento pessoal'),
('Literatura Brasileira', 'Obras de autores brasileiros'),
('Infantil', 'Livros para crianças'),
('Técnico', 'Livros técnicos e acadêmicos'),
('Biografia', 'Histórias de vida de pessoas reais'); 