<?php
include 'conexao.php';

// --- Lógica de Edição (Carregar dados) ---
$pokemon = [
    'id' => '', 'nome' => '', 'descricao' => '', 'tipo_principal_id' => '',
    'tipo_secundario_id' => '', 'nivel' => 5, 'habilidade' => '', 'imagem_padrao' => '',
    'hp' => 10, 'ataque' => 10, 'defesa' => 10, 'ataque_especial' => 10, 'defesa_especial' => 10,
    'velocidade' => 10
];
$fraquezas_pokemon = [];
$titulo_pagina = "Novo Pokémon";
$is_edit = false;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $titulo_pagina = "Editar Pokémon";
    $is_edit = true;

    $stmt = $conn->prepare("SELECT * FROM pokemons WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $pokemon = $result->fetch_assoc();
    } else {
        die("Pokémon não encontrado.");
    }
    $stmt->close();

    $stmt_fraq = $conn->prepare("SELECT tipo_id FROM fraquezas WHERE pokemon_id = ?");
    $stmt_fraq->bind_param("i", $id);
    $stmt_fraq->execute();
    $result_fraq = $stmt_fraq->get_result();
    while ($row_fraq = $result_fraq->fetch_assoc()) {
        $fraquezas_pokemon[] = $row_fraq['tipo_id'];
    }
    $stmt_fraq->close();
}

$tipos = [];
$result_tipos = $conn->query("SELECT id, nome, icone FROM tipos ORDER BY nome");
while ($row = $result_tipos->fetch_assoc()) {
    $tipos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?> - Pokédex BD2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-database"></i> Pokédex BD2
            </a>
            <a href="index.php" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </nav>

    <main class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h1 class="h3 mb-0"><?= $titulo_pagina ?></h1>
            </div>
            <div class="card-body p-4">
                <form action="salvar_pokemon.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $pokemon['id'] ?>">
                    <input type="hidden" name="imagem_existente" value="<?= htmlspecialchars($pokemon['imagem_padrao']) ?>">


                    <div class="row g-4">
                        <!-- Coluna da Imagem -->
                        <div class="col-md-4">
                            <h5 class="mb-3">Imagem de Perfil</h5>
                            <?php if ($is_edit && !empty($pokemon['imagem_padrao'])): ?>
                                <img src="<?= htmlspecialchars($pokemon['imagem_padrao']) ?>" alt="Imagem de <?= htmlspecialchars($pokemon['nome']) ?>" class="img-fluid rounded mb-3">
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="imagem_padrao" class="form-label">Enviar Nova Imagem</label>
                                <input class="form-control" type="file" name="imagem_padrao" id="imagem_padrao">
                                <div class="form-text">Envie uma imagem quadrada no formato PNG para melhor resultado.</div>
                            </div>
                        </div>

                        <!-- Coluna das Informações -->
                        <div class="col-md-8">
                            <h5 class="mb-3">Informações Básicas</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label for="nome" class="form-label">Nome</label>
                                    <input type="text" class="form-control" name="nome" id="nome" value="<?= htmlspecialchars($pokemon['nome']) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="nivel" class="form-label">Nível</label>
                                    <input type="number" class="form-control" name="nivel" id="nivel" value="<?= $pokemon['nivel'] ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="tipo_principal_id" class="form-label">Tipo Principal</label>
                                    <select class="form-select" name="tipo_principal_id" id="tipo_principal_id" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($tipos as $tipo): ?>
                                            <option value="<?= $tipo['id'] ?>" <?= ($tipo['id'] == $pokemon['tipo_principal_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($tipo['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="tipo_secundario_id" class="form-label">Tipo Secundário</label>
                                    <select class="form-select" name="tipo_secundario_id" id="tipo_secundario_id">
                                        <option value="">Nenhum</option>
                                        <?php foreach ($tipos as $tipo): ?>
                                            <option value="<?= $tipo['id'] ?>" <?= ($tipo['id'] == $pokemon['tipo_secundario_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($tipo['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="habilidade" class="form-label">Habilidade Principal</label>
                                    <input type="text" class="form-control" name="habilidade" id="habilidade" value="<?= htmlspecialchars($pokemon['habilidade']) ?>">
                                </div>
                                <div class="col-12">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" name="descricao" id="descricao" rows="3"><?= htmlspecialchars($pokemon['descricao']) ?></textarea>
                                </div>
                            </div>

                            <h5 class="mb-3">Estatísticas</h5>
                            <div class="row g-3 mb-4">
                                <?php
                                $stats = ['hp', 'ataque', 'defesa', 'ataque_especial', 'defesa_especial', 'velocidade'];
                                foreach ($stats as $stat):
                                ?>
                                <div class="col-md-2 col-4">
                                    <label for="<?= $stat ?>" class="form-label text-capitalize"><?= str_replace('_', ' ', $stat) ?></label>
                                    <input type="number" class="form-control" name="<?= $stat ?>" id="<?= $stat ?>" value="<?= $pokemon[$stat] ?>">
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <h5 class="mb-3">Fraquezas</h5>
                            <div class="row g-2 mb-4">
                                <?php foreach ($tipos as $tipo): ?>
                                <div class="col-md-3 col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="fraquezas[]" value="<?= $tipo['id'] ?>" id="fraqueza_<?= $tipo['id'] ?>"
                                            <?= (in_array($tipo['id'], $fraquezas_pokemon)) ? 'checked' : '' ?>>
                                        <label class="form-check-label d-flex align-items-center" for="fraqueza_<?= $tipo['id'] ?>">
                                            <img src="<?= htmlspecialchars($tipo['icone']) ?>" alt="<?= htmlspecialchars($tipo['nome']) ?>" style="width: 16px; height: 16px;" class="me-1">
                                            <?= htmlspecialchars($tipo['nome']) ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Salvar Pokémon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="text-center text-muted py-4 mt-5">
        <p>&copy; <?= date('Y') ?> Pokédex BD2.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>