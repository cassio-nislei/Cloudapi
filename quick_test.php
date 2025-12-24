<?php
/**
 * Script rápido para testar query diretamente
 */

require_once 'application/config/database.php';

$cfg = $db['default'];

echo "Conectando ao banco...\n";
echo "Host: {$cfg['hostname']}\n";
echo "DB: {$cfg['database']}\n\n";

try {
    $conn = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database']);
    
    if ($conn->connect_error) {
        die("Erro: {$conn->connect_error}\n");
    }
    
    echo "✓ Conectado\n\n";
    
    // Testar se a tabela existe
    $result = $conn->query("SHOW TABLES LIKE 'PESSOAS'");
    if ($result->num_rows === 0) {
        echo "✗ Tabela PESSOAS não existe!\n";
        echo "  Executar: docker-compose up -d\n";
        echo "  Ou verifique se os dados foram inseridos\n";
        $conn->close();
        exit;
    }
    
    echo "✓ Tabela PESSOAS encontrada\n\n";
    
    // Contar registros
    $result = $conn->query("SELECT COUNT(*) as total FROM PESSOAS");
    $row = $result->fetch_assoc();
    $total = $row['total'];
    
    echo "Total de registros: $total\n\n";
    
    if ($total === 0) {
        echo "⚠ Tabela vazia! Inserir dados para testar.\n";
        echo "  Ver docker/init.sql para dados de exemplo\n";
    } else {
        echo "Primeiros 5 registros:\n";
        echo "═" . str_repeat("═", 98) . "═\n";
        echo "│ " . str_pad("ID", 5) . " │ " . str_pad("NOME", 40) . " │ " . str_pad("CGC", 20) . " │ " . str_pad("ATIVO", 10) . " │\n";
        echo "╞" . str_repeat("═", 98) . "╡\n";
        
        $result = $conn->query("SELECT ID_PESSOA, NOME, CGC, ATIVO FROM PESSOAS ORDER BY ID_PESSOA LIMIT 5");
        while ($row = $result->fetch_assoc()) {
            echo "│ " . str_pad($row['ID_PESSOA'], 5) . " │ ";
            echo str_pad(substr($row['NOME'], 0, 40), 40) . " │ ";
            echo str_pad($row['CGC'], 20) . " │ ";
            echo str_pad($row['ATIVO'], 10) . " │\n";
        }
        echo "└" . str_repeat("─", 98) . "┘\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Erro: {$e->getMessage()}\n";
}

?>
