<?php
/**
 * Teste direto no banco de dados para verificar se PESSOA_LICENCAS está recebendo dados
 */

// Simular ambiente CodeIgniter
define('BASEPATH', dirname(__FILE__) . '/');
define('APPPATH', dirname(__FILE__) . '/application/');
define('FCPATH', dirname(__FILE__) . '/');

// Carregar configuração
require_once APPPATH . 'config/database.php';

// Conectar diretamente ao banco
echo "=== TESTE DIRETO DE INSERÇÃO EM PESSOA_LICENCAS ===\n\n";

try {
    // Tentar conexão via PDO para Firebird (se for Firebird)
    // Ou ajustar para a configuração do banco em config/database.php
    
    // Para Firebird
    $dsn = 'firebird:dbname=localhost:/opt/firebird/data/ADMCLOUD.FDB';
    $user = 'SYSDBA';
    $pass = 'masterkey';
    
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Conexão com banco de dados OK\n\n";
    
    // 1. Verifica se tabela existe e mostra estrutura
    $tables = $pdo->query("SELECT RDB\$RELATION_NAME FROM RDB\$RELATIONS WHERE RDB\$SYSTEM_FLAG = 0")->fetchAll();
    echo "Tabelas no banco:\n";
    foreach ($tables as $t) {
        $name = trim($t[0]);
        if (strpos($name, 'PESSOA') !== false) {
            echo "  - " . $name . "\n";
        }
    }
    echo "\n";
    
    // 2. Verifica registros existentes em PESSOA_LICENCAS
    $count = $pdo->query("SELECT COUNT(*) as total FROM PESSOA_LICENCAS")->fetch();
    echo "Total de registros em PESSOA_LICENCAS: " . $count['TOTAL'] . "\n\n";
    
    // 3. Tenta inserir um teste
    echo "Tentando inserir teste...\n";
    $stmt = $pdo->prepare("INSERT INTO PESSOA_LICENCAS (ID_PESSOA, GUID, HOSTNAME, CREATED_AT, LAST_LOGIN) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        1,  // ID_PESSOA
        'TEST-GUID-' . time(),
        'TEST-HOSTNAME',
        new DateTime(),
        new DateTime()
    ]);
    
    echo "✓ INSERT executado\n";
    echo "Rows affected: " . $stmt->rowCount() . "\n";
    echo "Last insert ID: " . $pdo->lastInsertId() . "\n\n";
    
    // 4. Verifica se foi realmente inserido
    $count2 = $pdo->query("SELECT COUNT(*) as total FROM PESSOA_LICENCAS")->fetch();
    echo "Total após insert: " . $count2['TOTAL'] . "\n";
    
    if ($count2['TOTAL'] > $count['TOTAL']) {
        echo "✓ Dado foi realmente inserido no banco!\n";
    } else {
        echo "✗ PROBLEMA: Inserção foi executada, mas dado não aparece no banco!\n";
        echo "  Isso indica: AutoCommit desligado, tabela com trigger de rejeição, ou permissão insuficiente\n";
    }
    
} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
    echo "Verificar:\n";
    echo "1. Credenciais do banco (user/senha)\n";
    echo "2. Caminho do banco de dados\n";
    echo "3. Se o servidor Firebird está rodando\n";
}
?>
