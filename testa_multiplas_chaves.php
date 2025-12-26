<?php
/**
 * Testa a descriptografia de "80EB" com MÚLTIPLAS CHAVES
 * Para descobrir qual chave resulta em um número válido
 */

function decrypt($encrypted_hex, $key) {
    $C1 = 32810;
    $C2 = 52010;
    $result = '';
    
    $i = 0;
    while ($i < strlen($encrypted_hex)) {
        $hex_pair = substr($encrypted_hex, $i, 2);
        $x = intval($hex_pair, 16);
        $byte = $x ^ ($key >> 8);
        $result .= chr($byte & 0xFF);
        $key = (($x & 0xFF) + $key) * $C1 + $C2;
        $key = $key & 0xFFFF;
        $i += 2;
    }
    
    return $result;
}

echo "=== TESTE COM MÚLTIPLAS CHAVES ===\n\n";
echo "Procurando qual chave resulta em um número válido para '80EB':\n\n";

// Testar chaves sistemáticas
$test_keys = [];

// Chaves relacionadas a constantes do projeto
$test_keys[] = 2024;   // Padrão visto
$test_keys[] = 2023;
$test_keys[] = 2025;

// Chaves que vimos no código de criptografia
$test_keys[] = 32810;  // C1
$test_keys[] = 52010;  // C2

// Chaves simples
for ($k = 1; $k <= 100; $k++) {
    $test_keys[] = $k;
}

for ($k = 100; $k <= 1000; $k += 10) {
    $test_keys[] = $k;
}

// Remover duplicatas
$test_keys = array_unique($test_keys);
sort($test_keys);

$found = false;

foreach ($test_keys as $key) {
    $dec = decrypt("80EB", $key);
    
    // Verificar se é número
    if (ctype_digit($dec)) {
        echo "✓ KEY=$key: '80EB' = '$dec' (NÚMERO VÁLIDO) → $dec TERMINAIS\n";
        $found = true;
    }
}

if (!$found) {
    echo "Nenhuma chave resultou em um número válido.\n\n";
    echo "Resultados de algumas chaves:\n\n";
    
    $sample_keys = [1, 10, 100, 1000, 2024, 2025, 5000, 10000, 32810, 52010];
    
    foreach ($sample_keys as $key) {
        $dec = decrypt("80EB", $key);
        
        $display = '';
        for ($i = 0; $i < strlen($dec) && $i < 3; $i++) {
            $ascii = ord($dec[$i]);
            $display .= "chr($ascii) ";
        }
        
        echo "KEY=$key: '$dec' → " . trim($display) . "\n";
    }
}
?>
