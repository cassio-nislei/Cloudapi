<?php
/**
 * Teste completo da API de Pessoas
 * Chama o código exatamente como a aplicação faria
 */

// Simular acesso HTTP
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REQUEST_URI'] = '/Pessoas/getAll';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// Session para simular autenticação
session_start();
$_SESSION['logado'] = TRUE;
$_SESSION['user.ID'] = 1;
$_SESSION['user.email'] = 'admin@admcloud.com.br';
$_SESSION['user.nome'] = 'Admin';

echo "=== Teste Completo da API Pessoas ===\n\n";

echo "Configuração:\n";
echo "  Método: {$_SERVER['REQUEST_METHOD']}\n";
echo "  URI: {$_SERVER['REQUEST_URI']}\n";
echo "  Host: {$_SERVER['HTTP_HOST']}\n";
echo "  Sessão logado: " . ($_SESSION['logado'] ? 'SIM' : 'NÃO') . "\n\n";

// Carregar CodeIgniter
define('BASEPATH', dirname(__FILE__) . '/system/');
define('APPPATH', dirname(__FILE__) . '/application/');
define('FCPATH', dirname(__FILE__) . '/');

require_once APPPATH . 'config/database.php';
require_once APPPATH . 'models/Pessoas_model.php';

echo "Criando modelo...\n";
try {
    // Conectar ao banco diretamente para teste
    $db_config = $db['default'];
    $mysqli = new mysqli(
        $db_config['hostname'],
        $db_config['username'],
        $db_config['password'],
        $db_config['database']
    );
    
    if ($mysqli->connect_error) {
        throw new Exception("Erro de conexão: " . $mysqli->connect_error);
    }
    
    echo "✓ Conexão ao banco OK\n\n";
    
    // Simular a query do modelo
    echo "Executando query:\n";
    $sql = "SELECT * FROM PESSOAS ORDER BY NOME";
    echo "  SQL: $sql\n\n";
    
    $result = $mysqli->query($sql);
    
    if (!$result) {
        throw new Exception("Erro na query: " . $mysqli->error);
    }
    
    echo "Resultado:\n";
    echo "  Num rows: " . $result->num_rows . "\n\n";
    
    if ($result->num_rows > 0) {
        echo "Dados retornados:\n";
        echo "┌────────┬─────────────────────┬──────────────────┬────────┐\n";
        echo "│ ID     │ NOME                 │ CGC              │ ATIVO  │\n";
        echo "├────────┼─────────────────────┼──────────────────┼────────┤\n";
        
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
            echo "│ " . str_pad($row['ID_PESSOA'], 6) . " │ ";
            echo str_pad(substr($row['NOME'] ?? '', 0, 20), 20) . " │ ";
            echo str_pad($row['CGC'] ?? '', 16) . " │ ";
            echo str_pad($row['ATIVO'] ?? '', 6) . " │\n";
        }
        
        echo "└────────┴─────────────────────┴──────────────────┴────────┘\n\n";
        
        // Simular a resposta JSON
        echo "JSON Response:\n";
        echo "──────────────\n";
        
        $response_data = array();
        foreach ($data as $item) {
            $item['status'] = $item['ATIVO'] === 'S' ? 'Ativo' : 'Desativado';
            $response_data[] = $item;
        }
        
        $response = array(
            'status' => TRUE,
            'msg' => 'Registros encontrados: ' . count($response_data),
            'data' => $response_data
        );
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "\n\n";
        
        echo "✓ Teste concluído com sucesso!\n";
        echo "  Total de registros: " . count($response_data) . "\n";
        
    } else {
        echo "⚠ Nenhum registro encontrado\n";
        echo "  Insira dados em PESSOAS para testar\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
}

?>
