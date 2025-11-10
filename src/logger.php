<?php
function log_message($message) {
    $log_dir = __DIR__ . '/logs';
    if (!is_dir($log_dir)) {
        // Tenta criar o diretório se ele não existir (como um fallback)
        // @ para suprimir erros caso o diretório não possa ser criado
        @mkdir($log_dir, 0777, true);
    }

    $log_file = $log_dir . '/db_interactions.log';
    $timestamp = date('Y-m-d H:i:s');
    
    // Constrói a entrada de log
    $log_entry = "[$timestamp] " . $message . "\n";
    
    // Adiciona a entrada ao arquivo de log, suprimindo erros de permissão
    @file_put_contents($log_file, $log_entry, FILE_APPEND);
}
?>