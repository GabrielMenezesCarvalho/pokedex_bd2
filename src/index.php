<?php
include 'conexao.php';

$sql = "SELECT p.*, t.nome AS tipo_nome, t.icone AS tipo_icone
        FROM pokemons p
        LEFT JOIN tipos t ON p.tipo_principal_id = t.id
        ORDER BY p.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex BD2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            transition: transform .2s;
            height: 100%;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card-img-top {
            background-color: #e9ecef;
            height: 200px;
            object-fit: contain;
            padding: 1rem;
        }
        .navbar-brand {
            font-weight: bold;
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
                <a href="form_pokemon.php" class="btn btn-light">
                    <i class="fas fa-plus"></i> Novo Pokémon
                </a>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Lista de Pokémon</h1>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">

            <?php if ($result->num_rows > 0): ?>
                <?php while($pokemon = $result->fetch_assoc()): ?>
                
                <div class="col">
                    <div class="card shadow-sm">
                        <img src="<?= htmlspecialchars($pokemon['imagem_padrao'] ?: 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/0.png') ?>" 
                             class="card-img-top" 
                             alt="Imagem de <?= htmlspecialchars($pokemon['nome']) ?>">
                        
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($pokemon['nome']) ?></h5>
                            <p class="card-text">
                                <span class="badge bg-secondary d-inline-flex align-items-center">
                                    <img src="<?= htmlspecialchars($pokemon['tipo_icone'] ?? '') ?>" alt="<?= htmlspecialchars($pokemon['tipo_nome']) ?>" class="me-1" style="width: 16px; height: 16px;">
                                    <?= htmlspecialchars($pokemon['tipo_nome'] ?? 'N/A') ?>
                                </span>
                                <span class="ms-2">Nível: <?= htmlspecialchars($pokemon['nivel'] ?? '??') ?></span>
                            </p>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-end gap-2">
                            <a href="form_pokemon.php?id=<?= $pokemon['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="salvar_pokemon.php?action=delete&id=<?= $pokemon['id'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir <?= htmlspecialchars($pokemon['nome']) ?>?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning" role="alert">
                        Nenhum Pokémon cadastrado ainda. <a href="form_pokemon.php" class="alert-link">Clique aqui para adicionar um!</a>
                    </div>
                </div>
            <?php endif; ?>
            
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
