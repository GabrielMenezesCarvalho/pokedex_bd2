<?php
include 'conexao.php';

// --- Lógica de Upload de Imagem ---
function uploadImagem(&$params) {
    $upload_dir = 'images/profilePhoto/';
    $imagem_path = $params['imagem_existente']; // Começa com a imagem que já existe

    if (isset($_FILES['imagem_padrao']) && $_FILES['imagem_padrao']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['imagem_padrao']['tmp_name'];
        $file_name = $_FILES['imagem_padrao']['name'];
        $file_size = $_FILES['imagem_padrao']['size'];
        $file_type = $_FILES['imagem_padrao']['type'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validação simples (ex: apenas PNG)
        if ($file_extension === 'png') {
            // Gera um nome de arquivo único para evitar conflitos
            $new_file_name = uniqid('', true) . '.' . $file_extension;
            $dest_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                // Se o upload for bem-sucedido, deleta a imagem antiga (se existir)
                if (!empty($params['imagem_existente']) && file_exists($params['imagem_existente'])) {
                    unlink($params['imagem_existente']);
                }
                // Define o novo caminho da imagem
                $imagem_path = '/' . $dest_path;
            }
        }
    }
    // Atualiza o parâmetro com o caminho final da imagem (nova ou a existente)
    $params['imagem_padrao'] = $imagem_path;
}


// --- Lógica de Exclusão ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->begin_transaction();
    try {
        // Busca o caminho da imagem antes de deletar o registro
        $stmt_img = $conn->prepare("SELECT imagem_padrao FROM pokemons WHERE id = ?");
        $stmt_img->bind_param("i", $id);
        $stmt_img->execute();
        $result_img = $stmt_img->get_result();
        if ($row = $result_img->fetch_assoc()) {
            if (!empty($row['imagem_padrao']) && file_exists(ltrim($row['imagem_padrao'], '/'))) {
                unlink(ltrim($row['imagem_padrao'], '/'));
            }
        }
        $stmt_img->close();

        // Deleta fraquezas e o Pokémon
        $stmt_fraq = $conn->prepare("DELETE FROM fraquezas WHERE pokemon_id = ?");
        $stmt_fraq->bind_param("i", $id);
        $stmt_fraq->execute();
        $stmt_fraq->close();

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
        'imagem_existente' => $_POST['imagem_existente'], // Passa a imagem atual
        'hp' => $_POST['hp'], 'ataque' => $_POST['ataque'],
        'defesa' => $_POST['defesa'], 'ataque_especial' => $_POST['ataque_especial'],
        'defesa_especial' => $_POST['defesa_especial'], 'velocidade' => $_POST['velocidade']
    ];

    // Processa o upload da imagem
    uploadImagem($params);

    // Remove a imagem existente do array de parâmetros para não tentar inserir no DB
    unset($params['imagem_existente']);

    // Adiciona o caminho final da imagem ao array de parâmetros
    $db_params = $params;
    
    $tipos_bind = "ssiiisiiiiii"; // Ajustado para os 12 campos

    $conn->begin_transaction();

    try {
        if (empty($id)) {
            // INSERT
            $sql = "INSERT INTO pokemons (nome, descricao, tipo_principal_id, tipo_secundario_id, nivel, habilidade, hp, ataque, defesa, ataque_especial, defesa_especial, velocidade, imagem_padrao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            // Reordena os parâmetros para o INSERT
            $insert_params = [
                $db_params['nome'], $db_params['descricao'], $db_params['tipo_principal_id'], $db_params['tipo_secundario_id'],
                $db_params['nivel'], $db_params['habilidade'], $db_params['hp'], $db_params['ataque'], $db_params['defesa'],
                $db_params['ataque_especial'], $db_params['defesa_especial'], $db_params['velocidade'], $db_params['imagem_padrao']
            ];
            $tipos_bind .= "s";
            $stmt->bind_param($tipos_bind, ...$insert_params);
        } else {
            // UPDATE
            $sql = "UPDATE pokemons SET nome = ?, descricao = ?, tipo_principal_id = ?, tipo_secundario_id = ?, nivel = ?, habilidade = ?, hp = ?, ataque = ?, defesa = ?, ataque_especial = ?, defesa_especial = ?, velocidade = ?, imagem_padrao = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $update_params = [
                $db_params['nome'], $db_params['descricao'], $db_params['tipo_principal_id'], $db_params['tipo_secundario_id'],
                $db_params['nivel'], $db_params['habilidade'], $db_params['hp'], $db_params['ataque'], $db_params['defesa'],
                $db_params['ataque_especial'], $db_params['defesa_especial'], $db_params['velocidade'], $db_params['imagem_padrao'],
                $id
            ];
            $tipos_bind .= "si";
            $stmt->bind_param($tipos_bind, ...$update_params);

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