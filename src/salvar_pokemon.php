<?php
include 'conexao.php'; // O logger já é incluído aqui

// --- Lógica de Upload de Imagem ---
function uploadImagem(&$params) {
    $upload_dir = 'images/profilePhoto/';
    $imagem_path = $params['imagem_existente']; 

    if (isset($_FILES['imagem_padrao']) && $_FILES['imagem_padrao']['error'] === UPLOAD_ERR_OK) {
        log_message("Nova imagem '{$_FILES['imagem_padrao']['name']}' enviada. Tentando processar...");
        $file_tmp_path = $_FILES['imagem_padrao']['tmp_name'];
        $file_extension = strtolower(pathinfo($_FILES['imagem_padrao']['name'], PATHINFO_EXTENSION));

        if ($file_extension === 'png') {
            $new_file_name = uniqid('', true) . '.' . $file_extension;
            $dest_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                log_message("Upload da imagem '{$new_file_name}' bem-sucedido para '{$dest_path}'.");
                if (!empty($params['imagem_existente']) && file_exists(ltrim($params['imagem_existente'], '/'))) {
                    unlink(ltrim($params['imagem_existente'], '/'));
                    log_message("Imagem antiga '{$params['imagem_existente']}' deletada.");
                }
                $imagem_path = '/' . $dest_path;
            } else {
                log_message("ERRO: Falha ao mover a imagem enviada para '{$dest_path}'.");
            }
        } else {
            log_message("ERRO: O arquivo enviado não é um PNG. Extensão: {$file_extension}.");
        }
    }
    $params['imagem_padrao'] = $imagem_path;
}

// --- Lógica de Exclusão ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    log_message("Iniciando processo de exclusão para o Pokémon ID: {$id}.");
    $conn->begin_transaction();
    try {
        // Lógica para deletar imagem...
        $stmt_img = $conn->prepare("SELECT imagem_padrao FROM pokemons WHERE id = ?");
        $stmt_img->bind_param("i", $id);
        $stmt_img->execute();
        $result_img = $stmt_img->get_result();
        if ($row = $result_img->fetch_assoc()) {
            if (!empty($row['imagem_padrao']) && file_exists(ltrim($row['imagem_padrao'], '/'))) {
                unlink(ltrim($row['imagem_padrao'], '/'));
                log_message("Imagem '{$row['imagem_padrao']}' associada ao Pokémon ID {$id} foi deletada.");
            }
        }
        $stmt_img->close();

        // Lógica para deletar o Pokémon
        $stmt_poke = $conn->prepare("DELETE FROM pokemons WHERE id = ?");
        $stmt_poke->bind_param("i", $id);
        $stmt_poke->execute();
        log_message("Executando DELETE na tabela 'pokemons' para o ID: {$id}. Sucesso: " . ($stmt_poke->affected_rows > 0 ? 'Sim' : 'Não'));
        $stmt_poke->close();

        $conn->commit();
        log_message("COMMIT da transação de exclusão para o Pokémon ID: {$id}.");
        header("Location: index.php?status=excluido");
    } catch (Exception $e) {
        $conn->rollback();
        log_message("ROLLBACK da transação de exclusão. ERRO: " . $e->getMessage());
        die("Erro ao excluir Pokémon: " . $e->getMessage());
    }
    exit;
}

// --- Lógica de Inserção/Atualização ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = empty($id) ? 'criação' : 'atualização';
    log_message("Iniciando processo de {$action} de Pokémon.");

    $tipo_secundario_id = !empty($_POST['tipo_secundario_id']) ? $_POST['tipo_secundario_id'] : NULL;
    
    $params = [
        'nome' => $_POST['nome'], 'descricao' => $_POST['descricao'],
        'tipo_principal_id' => $_POST['tipo_principal_id'], 'tipo_secundario_id' => $tipo_secundario_id,
        'habilidade' => $_POST['habilidade'],
        'imagem_existente' => $_POST['imagem_existente'],
        'hp' => $_POST['hp'], 'ataque' => $_POST['ataque'], 'defesa' => $_POST['defesa'],
        'ataque_especial' => $_POST['ataque_especial'], 'defesa_especial' => $_POST['defesa_especial'],
        'velocidade' => $_POST['velocidade']
    ];
    log_message("Dados recebidos do formulário: " . json_encode(array_filter($params, fn($key) => $key !== 'imagem_existente', ARRAY_FILTER_USE_KEY)));

    uploadImagem($params);
    unset($params['imagem_existente']);
    $db_params = $params;

    $conn->begin_transaction();
    log_message("Iniciando transação do banco de dados.");

    try {
        if (empty($id)) {
            // INSERT
            $sql = "INSERT INTO pokemons (nome, descricao, tipo_principal_id, tipo_secundario_id, habilidade, hp, ataque, defesa, ataque_especial, defesa_especial, velocidade, imagem_padrao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            log_message("Preparando SQL: " . $sql);
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiisiiiiiss", ...array_values($db_params));
        } else {
            // UPDATE
            $sql = "UPDATE pokemons SET nome = ?, descricao = ?, tipo_principal_id = ?, tipo_secundario_id = ?, habilidade = ?, hp = ?, ataque = ?, defesa = ?, ataque_especial = ?, defesa_especial = ?, velocidade = ?, imagem_padrao = ? WHERE id = ?";
            log_message("Preparando SQL: " . $sql);
            $stmt = $conn->prepare($sql);
            $update_params = array_values($db_params);
            $update_params[] = $id;
            $stmt->bind_param("ssiisiiiiissi", ...$update_params);
        }

        $stmt->execute();
        log_message("SQL executado com sucesso.");
        
        if (empty($id)) {
            $id = $conn->insert_id;
        }

        if (!empty($_POST['fraquezas'])) {
            $stmt_delete_fraq = $conn->prepare("DELETE FROM fraquezas WHERE pokemon_id = ?");
            $stmt_delete_fraq->bind_param("i", $id);
            $stmt_delete_fraq->execute();
            $stmt_delete_fraq->close();

            $sql_fraq = "INSERT INTO fraquezas (pokemon_id, tipo_id) VALUES (?, ?)";
            $stmt_fraq = $conn->prepare($sql_fraq);
            foreach ($_POST['fraquezas'] as $tipo_id) {
                $stmt_fraq->bind_param("ii", $id, $tipo_id);
                $stmt_fraq->execute();
            }
            $stmt_fraq->close();
        }

        $conn->commit();
        log_message("COMMIT da transação de {$action} bem-sucedido.");
        header("Location: index.php?status=sucesso");

    } catch (Exception $e) {
        $conn->rollback();
        log_message("!!! ERRO DE BANCO DE DADOS DETECTADO !!!");
        log_message("Mensagem: " . $e->getMessage());
        log_message("ROLLBACK da transação executado.");

        if (str_contains($e->getMessage(), 'chk_tipos_diferentes')) {
            $redirect_url = "form_pokemon.php?error=tipos_iguais";
            if (!empty($id)) {
                $redirect_url .= "&id=" . $id;
            }
            header("Location: " . $redirect_url);
            exit;
        } else {
            die("Erro ao salvar Pokémon: " . $e->getMessage());
        }
    }
} else {
    header("Location: index.php");
}

$conn->close();
?>