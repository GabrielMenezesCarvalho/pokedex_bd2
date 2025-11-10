<?php
header('Content-Type: text/html; charset=utf-8');
include 'conexao.php';

$pokemon = null;
$fraquezas = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $pokemon_id = $_GET['id'];

    $sql = "SELECT p.*, 
                   tp.nome AS tipo_principal_nome, tp.icone AS tipo_principal_icone,
                   ts.nome AS tipo_secundario_nome, ts.icone AS tipo_secundario_icone
            FROM pokemons p
            LEFT JOIN tipos tp ON p.tipo_principal_id = tp.id
            LEFT JOIN tipos ts ON p.tipo_secundario_id = ts.id
            WHERE p.id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $pokemon_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $pokemon = $result->fetch_assoc();
        $stmt->close();
    }

    // Fetch weaknesses
    $sql_fraquezas = "SELECT t.nome AS fraqueza_nome, t.icone AS fraqueza_icone
                      FROM fraquezas f
                      JOIN tipos t ON f.tipo_id = t.id
                      WHERE f.pokemon_id = ?";
    if ($stmt_fraq = $conn->prepare($sql_fraquezas)) {
        $stmt_fraq->bind_param("i", $pokemon_id);
        $stmt_fraq->execute();
        $result_fraq = $stmt_fraq->get_result();
        while ($fraqueza = $result_fraq->fetch_assoc()) {
            $fraquezas[] = $fraqueza;
        }
        $stmt_fraq->close();
    }
}

if (!$pokemon) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pokemon['nome'], ENT_QUOTES, 'UTF-8') ?> - Detalhes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .pokemon-image {
            max-width: 100%;
            height: auto;
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 1rem;
        }
        .type-badge {
            display: inline-flex;
            align-items: center;
            margin-right: 5px;
        }
        .type-badge img {
            width: 20px;
            height: 20px;
            margin-right: 5px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-database"></i> Pokédex BD2
            </a>
            <div class="ms-auto">
                <a href="form_pokemon.php" class="btn btn-light me-2">
                    <i class="fas fa-plus"></i> Novo Pokémon
                </a>
                <a href="index.php" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title h3 mb-0"><?= htmlspecialchars($pokemon['nome'], ENT_QUOTES, 'UTF-8') ?> <small class="text-white-50">#<?= htmlspecialchars(sprintf('%03d', $pokemon['pokedex_index']), ENT_QUOTES, 'UTF-8') ?></small></h1>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="<?= htmlspecialchars($pokemon['imagem_padrao'] ?: 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/0.png', ENT_QUOTES, 'UTF-8') ?>" 
                             class="pokemon-image" 
                             alt="Imagem de <?= htmlspecialchars($pokemon['nome'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="col-md-8">
                        <p><strong>Habilidade:</strong> <?= htmlspecialchars($pokemon['habilidade'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Tipos:</strong> 
                            <?php if ($pokemon['tipo_principal_nome']): ?>
                                <span class="badge bg-secondary type-badge">
                                    <img src="<?= htmlspecialchars($pokemon['tipo_principal_icone'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($pokemon['tipo_principal_nome'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($pokemon['tipo_principal_nome'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($pokemon['tipo_secundario_nome']): ?>
                                <span class="badge bg-secondary type-badge">
                                    <img src="<?= htmlspecialchars($pokemon['tipo_secundario_icone'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($pokemon['tipo_secundario_nome'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($pokemon['tipo_secundario_nome'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php endif; ?>
                        </p>
                        <p><strong>Fraquezas:</strong> 
                            <?php if (!empty($fraquezas)): ?>
                                <?php foreach ($fraquezas as $fraqueza): ?>
                                    <span class="badge bg-danger type-badge">
                                        <img src="<?= htmlspecialchars($fraqueza['fraqueza_icone'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($fraqueza['fraqueza_nome'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($fraqueza['fraqueza_nome'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </p>
                        <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($pokemon['descricao'], ENT_QUOTES, 'UTF-8')) ?></p>
                        
                        <h4 class="mt-4">Estatísticas Base</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>HP:</strong> <?= htmlspecialchars($pokemon['hp'], ENT_QUOTES, 'UTF-8') ?></li>
                            <li class="list-group-item"><strong>Ataque:</strong> <?= htmlspecialchars($pokemon['ataque'], ENT_QUOTES, 'UTF-8') ?></li>
                            <li class="list-group-item"><strong>Defesa:</strong> <?= htmlspecialchars($pokemon['defesa'], ENT_QUOTES, 'UTF-8') ?></li>
                            <li class="list-group-item"><strong>Ataque Especial:</strong> <?= htmlspecialchars($pokemon['ataque_especial'], ENT_QUOTES, 'UTF-8') ?></li>
                            <li class="list-group-item"><strong>Defesa Especial:</strong> <?= htmlspecialchars($pokemon['defesa_especial'], ENT_QUOTES, 'UTF-8') ?></li>
                            <li class="list-group-item"><strong>Velocidade:</strong> <?= htmlspecialchars($pokemon['velocidade'], ENT_QUOTES, 'UTF-8') ?></li>
                        </ul>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="form_pokemon.php?id=<?= $pokemon['id'] ?>" class="btn btn-primary" title="Editar">
                                <i class="fas fa-edit"></i> Editar Pokémon
                            </a>
                            <a href="salvar_pokemon.php?action=delete&id=<?= $pokemon['id'] ?>" class="btn btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir <?= htmlspecialchars($pokemon['nome'], ENT_QUOTES, 'UTF-8') ?>?');">
                                <i class="fas fa-trash"></i> Excluir Pokémon
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center text-muted py-4 mt-5">
        <p>&copy; <?= date('Y') ?> Pokédex BD2. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>