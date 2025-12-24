<?php
/**
 * Script de teste para diagnosticar problema de dados
 */

// Carregar configuração do CodeIgniter
require_once 'application/config/database.php';
require_once 'application/config/config.php';

echo "=== Diagnóstico de Conexão e Dados ===\n\n";

// 1. Testar conexão
echo "1. Testando conexão com banco de dados...\n";
try {
    $connection = new mysqli(
        $db['default']['hostname'],
        $db['default']['username'],
        $db['default']['password'],
        $db['default']['database']
    );
    
    if ($connection->connect_error) {
        die("ERRO: " . $connection->connect_error);
    }
    
    echo "✓ Conexão estabelecida com sucesso!\n";
    echo "  Host: " . $db['default']['hostname'] . "\n";
    echo "  Database: " . $db['default']['database'] . "\n\n";
    
} catch (Exception $e) {
    die("ERRO na conexão: " . $e->getMessage());
}

// 2. Verificar tabelas
echo "2. Verificando tabelas...\n";
$result = $connection->query("SHOW TABLES");
if (!$result) {
    die("ERRO: " . $connection->error);
}

$tables = array();
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

if (empty($tables)) {
    echo "⚠ NENHUMA TABELA ENCONTRADA!\n";
} else {
    echo "✓ Tabelas encontradas: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
}
echo "\n";

// 3. Verificar dados da tabela PESSOAS
echo "3. Verificando tabela PESSOAS...\n";
if (in_array('PESSOAS', $tables)) {
    $result = $connection->query("SELECT COUNT(*) as total FROM PESSOAS");
    if (!$result) {
        echo "ERRO: " . $connection->error . "\n";
    } else {
        $row = $result->fetch_assoc();
        $total = $row['total'];
        echo "✓ Total de registros: $total\n\n";
        
        if ($total > 0) {
            echo "4. Listando registros da tabela PESSOAS:\n";
            $result = $connection->query("SELECT ID_PESSOA, NOME, CGC, EMAIL, ATIVO FROM PESSOAS LIMIT 10");
            if (!$result) {
                echo "ERRO: " . $connection->error . "\n";
            } else {
                while ($row = $result->fetch_assoc()) {
                    echo "  ID: " . $row['ID_PESSOA'] . " | Nome: " . $row['NOME'] . " | CGC: " . $row['CGC'] . " | Ativo: " . $row['ATIVO'] . "\n";
                }
            }
        } else {
            echo "⚠ Tabela PESSOAS vazia!\n";
        }
    }
} else {
    echo "⚠ Tabela PESSOAS não existe!\n";
}
echo "\n";

// 4. Testar query via CodeIgniter
echo "5. Testando query via CodeIgniter:\n";
require_once 'index.php';

$CI =& get_instance();
$CI->load->model('Pessoas_model');

$dados = $CI->Pessoas_model->getAll();
if ($dados === FALSE) {
    echo "⚠ getAll() retornou FALSE\n";
} else if (empty($dados)) {
    echo "⚠ getAll() retornou array vazio\n";
} else {
    echo "✓ getAll() retornou " . count($dados) . " registros\n";
    foreach ($dados as $d) {
        echo "  - " . $d->NOME . " (" . $d->CGC . ")\n";
    }
}

$connection->close();
echo "\n=== Diagnóstico Completo ===\n";
?>
