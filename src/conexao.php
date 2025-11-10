<?php
// Inclui o logger
require_once 'logger.php';

// Lê as variáveis de ambiente fornecidas pelo docker-compose
$host = getenv('MYSQL_HOST');
$user = getenv('MYSQL_USER');
$pass = getenv('MYSQL_PASSWORD');
$db = getenv('MYSQL_DATABASE');

// Tenta a conexão
try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Define o charset para UTF-8 para evitar problemas com acentuação
    $conn->set_charset("utf8mb4");
    $conn->query("SET NAMES 'utf8mb4'");

    //log_message("Conexão com o banco de dados '$db' estabelecida com sucesso.");
} catch (Exception $e) {
    log_message("FALHA na conexão com o banco de dados: " . $e->getMessage());
    die("Falha na conexão com o banco de dados: " . $e->getMessage());
}
?>