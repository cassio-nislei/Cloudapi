<?php
try {
    $pdo = new PDO('mysql:host=104.234.173.105;dbname=admCloud', 'root', 'Adm@2024');
    
    echo "=== ESTRUTURA DA TABELA PESSOA ===\n\n";
    
    $stmt = $pdo->query('DESC PESSOA');
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($cols as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
    echo "\n\n=== DADOS DE UMA PESSOA COM LICENÇA ===\n\n";
    
    // Pega a primeira pessoa com LICENCAS > 0
    $stmt = $pdo->query("SELECT * FROM PESSOA WHERE LICENCAS > 0 LIMIT 1");
    $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($pessoa) {
        echo "ID_PESSOA: " . $pessoa['ID_PESSOA'] . "\n";
        echo "NOME: " . $pessoa['NOME'] . "\n";
        echo "LICENCAS: " . $pessoa['LICENCAS'] . "\n";
        echo "CONT_LICENCAS: " . $pessoa['CONT_LICENCAS'] . "\n";
        
        // Procura por campos que contenham valores hexadecimais
        echo "\n\nCAMPOS COM VALORES SUSPEITOS (poderiam ser encriptados):\n";
        foreach ($pessoa as $key => $val) {
            if ($val && strlen($val) < 50 && !is_numeric($val) && !preg_match('/[a-z0-9\-\.@]/i', $val)) {
                echo "  " . $key . ": " . $val . "\n";
            }
            // Verifica se parece hex
            if (preg_match('/^[0-9A-Fa-f]+$/', $val) && strlen($val) > 2) {
                echo "  [HEX] " . $key . ": " . $val . " => " . hexdec($val) . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage();
}
?>
