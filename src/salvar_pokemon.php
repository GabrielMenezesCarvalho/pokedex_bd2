<?php
include 'conexao.php';

// --- Lógica de Exclusão ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->begin_transaction();
    try {
        // Deleta primeiro as fraquezas associadas
        $stmt_fraq = $conn->prepare("DELETE FROM fraquezas WHERE pokemon_id = ?");
        $stmt_fraq->bind_param("i", $id);
        $stmt_fraq->execute();
        $stmt_fraq->close();

        // Deleta o Pokémon
        $stmt_poke = $conn->prepare("DELETE FROM pokemons WHERE id = ?");
        $stmt_poke->bind_param("i", $id);
        $stmt_poke->execute();
        $stmt_poke->close();

        $conn->commit();
        header("Location: index.php?status=excluido");
    } catch (Exception $e) {
        $conn->rollback();
        die("Erro ao excluir Pokémon: " . $e->getMessage());
    }
    exit;
}

// --- Lógica de Inserção/Atualização ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_secundario_id = !empty($_POST['tipo_secundario_id']) ? $_POST['tipo_secundario_id'] : NULL;
    $id = $_POST['id'];

    $params = [
        'nome' => $_POST['nome'], 'descricao' => $_POST['descricao'],
        'tipo_principal_id' => $_POST['tipo_principal_id'], 'tipo_secundario_id' => $tipo_secundario_id,
        'nivel' => $_POST['nivel'], 'habilidade' => $_POST['habilidade'],
        'imagem_padrao' => $_POST['imagem_padrao'], 'hp' => $_POST['hp'], 'ataque' => $_POST['ataque'],
        'defesa' => $_POST['defesa'], 'ataque_especial' => $_POST['ataque_especial'],
        'defesa_especial' => $_POST['defesa_especial'], 'velocidade' => $_POST['velocidade']
    ];
    $tipos_bind = "ssiiisssssiiiii";

    $conn->begin_transaction();

    try {
        if (empty($id)) {
            // INSERT
            $sql = "INSERT INTO pokemons (nome, descricao, tipo_principal_id, tipo_secundario_id, nivel, habilidade, imagem_padrao, hp, ataque, defesa, ataque_especial, defesa_especial, velocidade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($tipos_bind, ...array_values($params));
        } else {
            // UPDATE
            $sql = "UPDATE pokemons SET nome = ?, descricao = ?, tipo_principal_id = ?, tipo_secundario_id = ?, nivel = ?, habilidade = ?, imagem_padrao = ?, hp = ?, ataque = ?, defesa = ?, ataque_especial = ?, defesa_especial = ?, velocidade = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $params['id'] = $id;
            $tipos_bind .= "i";
            $stmt->bind_param($tipos_bind, ...array_values($params));

            // Limpa fraquezas antigas para atualizar
            $stmt_delete_fraq = $conn->prepare("DELETE FROM fraquezas WHERE pokemon_id = ?");
            $stmt_delete_fraq->bind_param("i", $id);
            $stmt_delete_fraq->execute();
            $stmt_delete_fraq->close();
        }

        $stmt->execute();
        
        if (empty($id)) {
            $id = $conn->insert_id;
        }
        $stmt->close();

        // Salva as novas fraquezas
        if (!empty($_POST['fraquezas'])) {
            $sql_fraq = "INSERT INTO fraquezas (pokemon_id, tipo_id) VALUES (?, ?)";
            $stmt_fraq = $conn->prepare($sql_fraq);
            foreach ($_POST['fraquezas'] as $tipo_id) {
                $stmt_fraq->bind_param("ii", $id, $tipo_id);
                $stmt_fraq->execute();
            }
            $stmt_fraq->close();
        }

        $conn->commit();
        header("Location: index.php?status=sucesso");

    } catch (Exception $e) {
        $conn->rollback();
        die("Erro ao salvar Pokémon: " . $e->getMessage());
    }

} else {
    header("Location: index.php");
}

$conn->close();
?>
