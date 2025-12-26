<?php
/**
 * Verifica se a pessoa existe no banco
 */

echo "=== VERIFICAÇÃO DA PESSOA NO BANCO ===\n\n";

$cgc = '92702067000181';

$dsn = "mysql:host=104.234.173.105;dbname=admCloud;charset=utf8";
$pdo = new PDO($dsn, 'root', 'Ncm@647534', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// 1. Buscar por CGC exato
echo "1. Buscando por CGC exato: $cgc\n";
$stmt = $pdo->prepare("SELECT * FROM PESSOA WHERE CGC = ?");
$stmt->execute([$cgc]);
$resultado = $stmt->fetch();

if ($resultado) {
    echo "✓ ENCONTRADA!\n";
    echo "  ID_PESSOA: " . $resultado['ID_PESSOA'] . "\n";
    echo "  NOME: " . $resultado['NOME'] . "\n";
    echo "  CGC: " . $resultado['CGC'] . "\n";
} else {
    echo "✗ Não encontrada com CGC exato\n\n";
    
    // 2. Listar algumas pessoas para comparação
    echo "2. Listando primeiras 5 pessoas no banco:\n";
    $stmt = $pdo->query("SELECT ID_PESSOA, NOME, CGC FROM PESSOA LIMIT 5");
    $pessoas = $stmt->fetchAll();
    
    foreach ($pessoas as $p) {
        echo "  - ID: " . $p['ID_PESSOA'] . " | Nome: " . $p['NOME'] . " | CGC: " . $p['CGC'] . "\n";
    }
    
    // 3. Verificar se há alguma pessoa com CGC parecido
    echo "\n3. Buscando CGC que contenha: " . substr($cgc, 0, 8) . "\n";
    $stmt = $pdo->prepare("SELECT * FROM PESSOA WHERE CGC LIKE ? LIMIT 5");
    $stmt->execute(['%' . substr($cgc, 0, 8) . '%']);
    $resultados = $stmt->fetchAll();
    
    if (!empty($resultados)) {
        echo "Encontradas " . count($resultados) . " pessoa(s):\n";
        foreach ($resultados as $r) {
            echo "  - ID: " . $r['ID_PESSOA'] . " | Nome: " . $r['NOME'] . " | CGC: " . $r['CGC'] . "\n";
        }
    } else {
        echo "Nenhuma pessoa encontrada com esse padrão\n";
    }
}

// 4. Verificar total de pessoas
echo "\n4. Total de pessoas no banco:\n";
$stmt = $pdo->query("SELECT COUNT(*) as total FROM PESSOA");
$total = $stmt->fetch();
echo "   " . $total['total'] . " registros\n";

// 5. Verificar se há alguma pessoa com licenças
echo "\n5. Pessoas com licenças registradas:\n";
$stmt = $pdo->query("
    SELECT p.ID_PESSOA, p.NOME, COUNT(pl.ID) as qtd_licencas
    FROM PESSOA p
    LEFT JOIN PESSOA_LICENCAS pl ON p.ID_PESSOA = pl.ID_PESSOA
    GROUP BY p.ID_PESSOA
    HAVING qtd_licencas > 0
    LIMIT 5
");
$pessoas_com_licencas = $stmt->fetchAll();

if (!empty($pessoas_com_licencas)) {
    foreach ($pessoas_com_licencas as $p) {
        echo "   ID: " . $p['ID_PESSOA'] . " | Nome: " . $p['NOME'] . " | Licenças: " . $p['qtd_licencas'] . "\n";
    }
} else {
    echo "   Nenhuma pessoa com licenças\n";
}
?>
