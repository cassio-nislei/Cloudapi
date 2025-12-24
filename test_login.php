<?php
/**
 * Script de teste de login
 */

echo "=== Teste de Login ===\n\n";

// Carregar configuração
require_once 'application/config/database.php';

$db_config = $db['default'];

echo "Configuração do Banco:\n";
echo "  Host: " . $db_config['hostname'] . "\n";
echo "  Database: " . $db_config['database'] . "\n";
echo "  Usuario: " . $db_config['username'] . "\n\n";

// Tentar conectar
try {
    $mysqli = new mysqli(
        $db_config['hostname'],
        $db_config['username'],
        $db_config['password'],
        $db_config['database']
    );
    
    if ($mysqli->connect_error) {
        die("✗ Erro na conexão: " . $mysqli->connect_error);
    }
    
    echo "✓ Conexão estabelecida\n\n";
    
    // Buscar um usuário de teste
    $sql = "SELECT * FROM ADM_USUARIOS WHERE EMAIL = 'admin@admcloud.com.br' LIMIT 1";
    $result = $mysqli->query($sql);
    
    if (!$result) {
        echo "✗ Erro na query: " . $mysqli->error . "\n";
    } else {
        echo "Query executada: OK\n\n";
        
        if ($result->num_rows === 0) {
            echo "⚠ Usuário de teste não encontrado\n";
            echo "  Email: admin@admcloud.com.br\n\n";
            
            // Listar todos os usuários
            echo "Usuários existentes:\n";
            $result2 = $mysqli->query("SELECT ID, EMAIL FROM ADM_USUARIOS");
            if ($result2) {
                while ($row = $result2->fetch_assoc()) {
                    echo "  - " . $row['EMAIL'] . "\n";
                }
            }
        } else {
            $user = $result->fetch_assoc();
            echo "✓ Usuário encontrado\n";
            echo "  ID: " . $user['ID'] . "\n";
            echo "  Email: " . $user['EMAIL'] . "\n";
            echo "  Ativo: " . (isset($user['ATIVO']) ? $user['ATIVO'] : 'N/A') . "\n";
            
            // Verificar senha
            $password = 'Ncm@647534';
            $hash = sha1($password);
            echo "\n  Senha de teste: $password\n";
            echo "  SHA1 esperado: 89465a5d9d9daab4d138c3a985994531c0f1f15b\n";
            echo "  SHA1 gerado: $hash\n";
            echo "  SHA1 no BD: " . $user['SENHA'] . "\n";
            echo "  Match: " . ($hash === $user['SENHA'] ? '✓ SIM' : '✗ NÃO') . "\n";
        }
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "✗ Exceção: " . $e->getMessage();
}

?>
