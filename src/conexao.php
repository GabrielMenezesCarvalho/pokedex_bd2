<?php
// Lê as variáveis de ambiente fornecidas pelo docker-compose
$host = getenv('MYSQL_HOST');
$user = getenv('MYSQL_USER');
$pass = getenv('MYSQL_PASSWORD');
$db = getenv('MYSQL_DATABASE');

// Cria a conexão
$conn = new mysqli($host, $user, $pass, $db);

// Define o charset para UTF-8 para evitar problemas com acentuação
$conn->set_charset("utf8mb4");

// Verifica se há erros na conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// (Opcional) Linha útil para debug, comente ou remova em produção
// echo "Conexão bem-sucedida ao banco $db!"; 
?>