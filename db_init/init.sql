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
        habilidade VARCHAR(100),
        imagem_padrao VARCHAR(255),
        hp INT,
        ataque INT,
        defesa INT,
        ataque_especial INT,
        defesa_especial INT,
        velocidade INT,
        FOREIGN KEY (tipo_principal_id) REFERENCES tipos (id),
        FOREIGN KEY (tipo_secundario_id) REFERENCES tipos (id),
        CONSTRAINT chk_tipos_diferentes CHECK (tipo_principal_id <> tipo_secundario_id OR tipo_secundario_id IS NULL)
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
    tipos (id, nome, icone)
VALUES
    (1, 'Bug', '/images/icons/Bug.png'),
    (2, 'Dark', '/images/icons/Dark.png'),
    (3, 'Dragon', '/images/icons/Dragon.png'),
    (4, 'Electric', '/images/icons/Electric.png'),
    (5, 'Fairy', '/images/icons/Fairy.png'),
    (6, 'Fighting', '/images/icons/Fighting.png'),
    (7, 'Fire', '/images/icons/Fire.png'),
    (8, 'Flying', '/images/icons/Flying.png'),
    (9, 'Ghost', '/images/icons/Ghost.png'),
    (10, 'Grass', '/images/icons/Grass.png'),
    (11, 'Ground', '/images/icons/Ground.png'),
    (12, 'Ice', '/images/icons/Ice.png'),
    (13, 'Normal', '/images/icons/Normal.png'),
    (14, 'Poison', '/images/icons/Poison.png'),
    (15, 'Psychic', '/images/icons/Psychic.png'),
    (16, 'Rock', '/images/icons/Rock.png'),
    (17, 'Steel', '/images/icons/Steel.png'),
    (18, 'Water', '/images/icons/Water.png');

-- Popular com os 25 primeiros Pokémon
INSERT INTO pokemons (id, nome, descricao, tipo_principal_id, tipo_secundario_id, habilidade, imagem_padrao, hp, ataque, defesa, ataque_especial, defesa_especial, velocidade) VALUES
(1, 'Bulbasaur', 'There is a plant seed on its back right from the day this Pokémon is born. The seed slowly grows larger.', 10, 14, 'Overgrow', '/images/profilePhoto/1.png', 45, 49, 49, 65, 65, 45),
(2, 'Ivysaur', 'When the bulb on its back grows large, it appears to lose the ability to stand on its hind legs.', 10, 14, 'Overgrow', '/images/profilePhoto/2.png', 60, 62, 63, 80, 80, 60),
(3, 'Venusaur', 'Its plant blooms when it is absorbing solar energy. It stays on the move to seek sunlight.', 10, 14, 'Overgrow', '/images/profilePhoto/3.png', 80, 82, 83, 100, 100, 80),
(4, 'Charmander', 'It has a preference for hot things. When it rains, steam is said to spout from the tip of its tail.', 7, NULL, 'Blaze', '/images/profilePhoto/4.png', 39, 52, 43, 60, 50, 65),
(5, 'Charmeleon', 'It has a barbaric nature. In battle, it whips its fiery tail around and slashes away with sharp claws.', 7, NULL, 'Blaze', '/images/profilePhoto/5.png', 58, 64, 58, 80, 65, 80),
(6, 'Charizard', 'It spits fire that is hot enough to melt boulders. It may cause forest fires by blowing flames.', 7, 8, 'Blaze', '/images/profilePhoto/6.png', 78, 84, 78, 109, 85, 100),
(7, 'Squirtle', 'When it retracts its long neck into its shell, it squirts out water with vigorous force.', 18, NULL, 'Torrent', '/images/profilePhoto/7.png', 44, 48, 65, 50, 64, 43),
(8, 'Wartortle', 'It is recognized as a symbol of longevity. If its shell has algae on it, that Wartortle is very old.', 18, NULL, 'Torrent', '/images/profilePhoto/8.png', 59, 63, 80, 65, 80, 58),
(9, 'Blastoise', 'It crushes its foe under its heavy body to cause fainting. In a pinch, it will withdraw inside its shell.', 18, NULL, 'Torrent', '/images/profilePhoto/9.png', 79, 83, 100, 85, 105, 78),
(10, 'Caterpie', 'For protection, it releases a horrible stench from the antenna on its head to drive away enemies.', 1, NULL, 'Shield Dust', '/images/profilePhoto/10.png', 45, 30, 35, 20, 20, 45),
(11, 'Metapod', 'A steel-hard shell protects its tender body. It quietly endures hardships while awaiting evolution.', 1, NULL, 'Shed Skin', '/images/profilePhoto/11.png', 50, 20, 55, 25, 25, 30),
(12, 'Butterfree', 'In battle, it flaps its wings at high speed to release highly toxic dust into the air.', 1, 8, 'Compound Eyes', '/images/profilePhoto/12.png', 60, 45, 50, 90, 80, 70),
(13, 'Weedle', 'Beware of the sharp stinger on its head. It hides in grass and bushes where it eats leaves.', 1, 14, 'Shield Dust', '/images/profilePhoto/13.png', 40, 35, 30, 20, 20, 50),
(14, 'Kakuna', 'Able to move only slightly. When endangered, it may stick out its stinger and poison its enemy.', 1, 14, 'Shed Skin', '/images/profilePhoto/14.png', 45, 25, 50, 25, 25, 35),
(15, 'Beedrill', 'It has three poisonous stingers on its forelegs and its tail. They are used to jab its enemy repeatedly.', 1, 14, 'Swarm', '/images/profilePhoto/15.png', 65, 90, 40, 45, 80, 75),
(16, 'Pidgey', 'Very docile. If attacked, it will often kick up sand to protect itself rather than fight back.', 13, 8, 'Keen Eye', '/images/profilePhoto/16.png', 40, 45, 40, 35, 35, 56),
(17, 'Pidgeotto', 'This Pokémon is full of vitality. It constantly flies around its large territory in search of prey.', 13, 8, 'Keen Eye', '/images/profilePhoto/17.png', 63, 60, 55, 50, 50, 71),
(18, 'Pidgeot', 'This Pokémon flies at Mach 2 speed, seeking prey. Its large talons are feared as wicked weapons.', 13, 8, 'Keen Eye', '/images/profilePhoto/18.png', 83, 80, 75, 70, 70, 101),
(19, 'Rattata', 'Will chew on anything with its fangs. If you see one, you can be certain that 40 more live in the area.', 13, NULL, 'Run Away', '/images/profilePhoto/19.png', 30, 56, 35, 25, 35, 72),
(20, 'Raticate', 'Its hind feet are webbed. They act as flippers, so it can swim in rivers and hunt for prey.', 13, NULL, 'Guts', '/images/profilePhoto/20.png', 55, 81, 60, 50, 70, 97),
(21, 'Spearow', 'Inept at flying high. However, it can fly around very fast to protect its territory.', 13, 8, 'Keen Eye', '/images/profilePhoto/21.png', 40, 60, 30, 31, 31, 70),
(22, 'Fearow', 'A Pokémon that dates back to prehistoric times. It was apparently flying about in the ancient skies.', 13, 8, 'Keen Eye', '/images/profilePhoto/22.png', 65, 90, 65, 61, 61, 100),
(23, 'Ekans', 'The older it gets, the longer it grows. At night, it wraps its long body around tree branches to rest.', 14, NULL, 'Intimidate', '/images/profilePhoto/23.png', 35, 60, 44, 40, 54, 55),
(24, 'Arbok', 'The frightening patterns on its belly have been studied. Six variations have been confirmed.', 14, NULL, 'Intimidate', '/images/profilePhoto/24.png', 60, 95, 69, 65, 79, 80),
(25, 'Pikachu', 'It keeps its tail raised to monitor its surroundings. If you yank its tail, it will try to bite you.', 4, NULL, 'Static', '/images/profilePhoto/25.png', 35, 55, 40, 50, 50, 90);