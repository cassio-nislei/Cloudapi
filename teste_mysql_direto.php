<?php
/**
 * Teste direto de inserção em MySQL
 */

echo "=== TESTE DE INSERÇÃO DIRETA EM MYSQL ===\n\n";

// Parâmetros de conexão
$host = '104.234.173.105';
$user = 'root';
$pass = 'Ncm@647534';
$database = 'admCloud';

try {
    // Conexão com MySQL via PDO (como CodeIgniter faz)
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "✓ Conexão com MySQL estabelecida\n";
    echo "  Host: $host\n";
    echo "  Database: $database\n\n";
    
    // 1. Verificar se tabela existe
    echo "1. Verificando estrutura da tabela PESSOA_LICENCAS...\n";
    $columns = $pdo->query("DESCRIBE PESSOA_LICENCAS")->fetchAll();
    
    if (empty($columns)) {
        echo "✗ Tabela não encontrada!\n";
        exit;
    }
    
    echo "✓ Tabela encontrada com " . count($columns) . " colunas:\n";
    foreach ($columns as $col) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")" . ($col['Null'] === 'NO' ? ' NOT NULL' : '') . "\n";
    }
    
    // 2. Contar registros antes
    echo "\n2. Contando registros antes da inserção...\n";
    $countBefore = $pdo->query("SELECT COUNT(*) as cnt FROM PESSOA_LICENCAS")->fetch();
    echo "  Total antes: " . $countBefore['cnt'] . "\n";
    
    // 3. Tentar inserir
    echo "\n3. Inserindo novo registro...\n";
    
    $id_pessoa = 1; // Ajustar conforme necessário
    $guid = 'TEST-' . time();
    $hostname = 'TEST-HOST-' . time();
    $now = date('Y-m-d H:i:s');
    
    $sql = "INSERT INTO PESSOA_LICENCAS (ID_PESSOA, GUID, HOSTNAME, CREATED_AT, LAST_LOGIN) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$id_pessoa, $guid, $hostname, $now, $now]);
    
    echo "  INSERT executado: " . ($result ? 'SIM' : 'NÃO') . "\n";
    echo "  Rows affected: " . $stmt->rowCount() . "\n";
    echo "  Last Insert ID: " . $pdo->lastInsertId() . "\n";
    
    // 4. Contar registros depois (sem commit explícito)
    echo "\n4. Verificando dados após INSERT (sem commit)...\n";
    $countAfter = $pdo->query("SELECT COUNT(*) as cnt FROM PESSOA_LICENCAS")->fetch();
    echo "  Total depois: " . $countAfter['cnt'] . "\n";
    
    if ($countAfter['cnt'] > $countBefore['cnt']) {
        echo "  ✓ Dados foram inseridos (visível imediatamente)\n";
        
        // Tentar buscar o registro que acabou de inserir
        $stmt = $pdo->prepare("SELECT * FROM PESSOA_LICENCAS WHERE GUID = ? LIMIT 1");
        $stmt->execute([$guid]);
        $inserted = $stmt->fetch();
        
        if ($inserted) {
            echo "  ✓ Registro localizado:\n";
            echo "    " . json_encode($inserted) . "\n";
        }
    } else {
        echo "  ✗ PROBLEMA: Dados não aparecem após INSERT!\n";
        echo "  Isso pode indicar:\n";
        echo "    - AutoCommit desligado em nível de conexão PDO\n";
        echo "    - Permissão insuficiente para INSERT\n";
        echo "    - Trigger rejeitando inserção silenciosamente\n";
    }
    
    // 5. Teste explícito de commit
    echo "\n5. Testando COMMIT explícito...\n";
    
    $guid2 = 'TEST-COMMIT-' . time();
    $sql = "INSERT INTO PESSOA_LICENCAS (ID_PESSOA, GUID, HOSTNAME, CREATED_AT, LAST_LOGIN) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_pessoa, $guid2, $hostname, $now, $now]);
    
    echo "  INSERT executado\n";
    echo "  Executando COMMIT...\n";
    $pdo->commit();
    echo "  ✓ COMMIT executado\n";
    
    // Verificar
    $stmt = $pdo->prepare("SELECT * FROM PESSOA_LICENCAS WHERE GUID = ? LIMIT 1");
    $stmt->execute([$guid2]);
    $inserted2 = $stmt->fetch();
    
    if ($inserted2) {
        echo "  ✓ Após COMMIT, registro está no banco\n";
    } else {
        echo "  ✗ Após COMMIT, registro ainda não está visível\n";
    }
    
} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
    echo "Verificar:\n";
    echo "1. Credenciais (host, user, pass)\n";
    echo "2. Se MySQL está rodando em 104.234.173.105\n";
    echo "3. Se database 'admCloud' existe\n";
}
?>
