SET NAMES 'utf8mb4';
SET CHARACTER SET 'utf8mb4';

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
        pokedex_index INT UNIQUE NOT NULL,
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
INSERT INTO pokemons (id, nome, pokedex_index, descricao, tipo_principal_id, tipo_secundario_id, habilidade, imagem_padrao, hp, ataque, defesa, ataque_especial, defesa_especial, velocidade) VALUES
(1, 'Bulbasaur', 1, 'Há uma semente de planta em suas costas desde o dia em que este Pokémon nasce. A semente cresce lentamente.', 10, 14, 'Overgrow', '/images/profilePhoto/1.png', 45, 49, 49, 65, 65, 45),
(2, 'Ivysaur', 2, 'Quando o bulbo em suas costas cresce, ele parece perder a capacidade de ficar em pé sobre as patas traseiras.', 10, 14, 'Overgrow', '/images/profilePhoto/2.png', 60, 62, 63, 80, 80, 60),
(3, 'Venusaur', 3, 'Sua planta floresce quando está absorvendo energia solar. Ele se mantém em movimento para procurar a luz do sol.', 10, 14, 'Overgrow', '/images/profilePhoto/3.png', 80, 82, 83, 100, 100, 80),
(4, 'Charmander', 4, 'Ele tem preferência por coisas quentes. Quando chove, dizem que vapor sai da ponta de sua cauda.', 7, NULL, 'Blaze', '/images/profilePhoto/4.png', 39, 52, 43, 60, 50, 65),
(5, 'Charmeleon', 5, 'Tem uma natureza bárbara. Em batalha, ele chicoteia sua cauda de fogo e ataca com garras afiadas.', 7, NULL, 'Blaze', '/images/profilePhoto/5.png', 58, 64, 58, 80, 65, 80),
(6, 'Charizard', 6, 'Cospe fogo quente o suficiente para derreter pedras. Pode causar incêndios florestais ao soprar chamas.', 7, 8, 'Blaze', '/images/profilePhoto/6.png', 78, 84, 78, 109, 85, 100),
(7, 'Squirtle', 7, 'Quando retrai seu longo pescoço para dentro de sua concha, esguicha água com força vigorosa.', 18, NULL, 'Torrent', '/images/profilePhoto/7.png', 44, 48, 65, 50, 64, 43),
(8, 'Wartortle', 8, 'É reconhecido como um símbolo de longevidade. Se sua concha tiver algas, aquele Wartortle é muito velho.', 18, NULL, 'Torrent', '/images/profilePhoto/8.png', 59, 63, 80, 65, 80, 58),
(9, 'Blastoise', 9, 'Esmaga seu inimigo sob seu corpo pesado para fazê-lo desmaiar. Em apuros, ele se retira para dentro de sua concha.', 18, NULL, 'Torrent', '/images/profilePhoto/9.png', 79, 83, 100, 85, 105, 78),
(10, 'Caterpie', 10, 'Para se proteger, ele libera um odor horrível da antena em sua cabeça para afastar os inimigos.', 1, NULL, 'Shield Dust', '/images/profilePhoto/10.png', 45, 30, 35, 20, 20, 45),
(11, 'Metapod', 11, 'Uma casca dura como aço protege seu corpo macio. Ele suporta silenciosamente as dificuldades enquanto aguarda a evolução.', 1, NULL, 'Shed Skin', '/images/profilePhoto/11.png', 50, 20, 55, 25, 25, 30),
(12, 'Butterfree', 12, 'Em batalha, bate as asas em alta velocidade para liberar uma poeira altamente tóxica no ar.', 1, 8, 'Compound Eyes', '/images/profilePhoto/12.png', 60, 45, 50, 90, 80, 70),
(13, 'Weedle', 13, 'Cuidado com o ferrão afiado em sua cabeça. Ele se esconde na grama e em arbustos onde come folhas.', 1, 14, 'Shield Dust', '/images/profilePhoto/13.png', 40, 35, 30, 20, 20, 50),
(14, 'Kakuna', 14, 'Capaz de se mover apenas um pouco. Quando em perigo, pode expor seu ferrão e envenenar seu inimigo.', 1, 14, 'Shed Skin', '/images/profilePhoto/14.png', 45, 25, 50, 25, 25, 35),
(15, 'Beedrill', 15, 'Possui três ferrões venenosos em suas patas dianteiras e em sua cauda. Eles são usados para picar o inimigo repetidamente.', 1, 14, 'Swarm', '/images/profilePhoto/15.png', 65, 90, 40, 45, 80, 75),
(16, 'Pidgey', 16, 'Muito dócil. Se atacado, muitas vezes chuta areia para se proteger em vez de lutar.', 13, 8, 'Keen Eye', '/images/profilePhoto/16.png', 40, 45, 40, 35, 35, 56),
(17, 'Pidgeotto', 17, 'Este Pokémon é cheio de vitalidade. Ele voa constantemente por seu grande território em busca de presas.', 13, 8, 'Keen Eye', '/images/profilePhoto/17.png', 63, 60, 55, 50, 50, 71),
(18, 'Pidgeot', 18, 'Este Pokémon voa na velocidade de Mach 2, procurando presas. Suas grandes garras são temidas como armas perversas.', 13, 8, 'Keen Eye', '/images/profilePhoto/18.png', 83, 80, 75, 70, 70, 101),
(19, 'Rattata', 19, 'Rói qualquer coisa com suas presas. Se você vir um, pode ter certeza de que mais 40 vivem na área.', 13, NULL, 'Run Away', '/images/profilePhoto/19.png', 30, 56, 35, 25, 35, 72),
(20, 'Raticate', 20, 'Suas patas traseiras são palmadas. Elas agem como nadadeiras, para que possa nadar em rios e caçar presas.', 13, NULL, 'Guts', '/images/profilePhoto/20.png', 55, 81, 60, 50, 70, 97),
(21, 'Spearow', 21, 'Inapto para voar alto. No entanto, pode voar muito rápido para proteger seu território.', 13, 8, 'Keen Eye', '/images/profilePhoto/21.png', 40, 60, 30, 31, 31, 70),
(22, 'Fearow', 22, 'Um Pokémon que remonta aos tempos pré-históricos. Aparentemente, ele voava pelos céus antigos.', 13, 8, 'Keen Eye', '/images/profilePhoto/22.png', 65, 90, 65, 61, 61, 100),
(23, 'Ekans', 23, 'Quanto mais velho fica, mais comprido se torna. À noite, enrola seu longo corpo em galhos de árvores para descansar.', 14, NULL, 'Intimidate', '/images/profilePhoto/23.png', 35, 60, 44, 40, 54, 55),
(24, 'Arbok', 24, 'Os padrões assustadores em sua barriga foram estudados. Seis variações foram confirmadas.', 14, NULL, 'Intimidate', '/images/profilePhoto/24.png', 60, 95, 69, 65, 79, 80),
(25, 'Pikachu', 25, 'Ele mantém sua cauda levantada para monitorar seus arredores. Se você puxar sua cauda, ele tentará te morder.', 4, NULL, 'Static', '/images/profilePhoto/25.png', 35, 55, 40, 50, 50, 90);