-- Seleciona o banco de dados que será criado pelo Docker
USE pokedex_db;

-- Tabela de Tipos
CREATE TABLE
    IF NOT EXISTS tipos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(50) NOT NULL,
        icone VARCHAR(255)
    ) DEFAULT CHARSET = utf8mb4;

-- Tabela de Pokémons
CREATE TABLE
    IF NOT EXISTS pokemons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT,
        tipo_principal_id INT,
        tipo_secundario_id INT,
        nivel INT,
        habilidade VARCHAR(100),
        imagem_padrao VARCHAR(255),
        hp INT,
        ataque INT,
        defesa INT,
        ataque_especial INT,
        defesa_especial INT,
        velocidade INT,
        FOREIGN KEY (tipo_principal_id) REFERENCES tipos (id),
        FOREIGN KEY (tipo_secundario_id) REFERENCES tipos (id)
    ) DEFAULT CHARSET = utf8mb4;

-- Tabela de Fraquezas (Relação N:N entre pokemons e tipos)
CREATE TABLE
    IF NOT EXISTS fraquezas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pokemon_id INT,
        tipo_id INT,
        FOREIGN KEY (pokemon_id) REFERENCES pokemons (id) ON DELETE CASCADE,
        FOREIGN KEY (tipo_id) REFERENCES tipos (id) ON DELETE CASCADE
    ) DEFAULT CHARSET = utf8mb4;

-- Inserir os tipos correspondentes aos ícones na pasta /images/icons/
INSERT INTO
    tipos (nome, icone)
VALUES
    ('Bug', '/images/icons/Bug.png'),
    ('Dark', '/images/icons/Dark.png'),
    ('Dragon', '/images/icons/Dragon.png'),
    ('Electric', '/images/icons/Electric.png'),
    ('Fairy', '/images/icons/Fairy.png'),
    ('Fighting', '/images/icons/Fighting.png'),
    ('Fire', '/images/icons/Fire.png'),
    ('Flying', '/images/icons/Flying.png'),
    ('Ghost', '/images/icons/Ghost.png'),
    ('Grass', '/images/icons/Grass.png'),
    ('Ground', '/images/icons/Ground.png'),
    ('Ice', '/images/icons/Ice.png'),
    ('Normal', '/images/icons/Normal.png'),
    ('Poison', '/images/icons/Poison.png'),
    ('Psychic', '/images/icons/Psychic.png'),
    ('Rock', '/images/icons/Rock.png'),
    ('Steel', '/images/icons/Steel.png'),
    ('Water', '/images/icons/Water.png');