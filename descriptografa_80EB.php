<?php
/**
 * DESCOBRIR QUANTOS TERMINAIS "80EB" REPRESENTA
 * 
 * "80EB" é a criptografia de um número de terminais
 * Usar a função Decrypt do Delphi convertida para PHP
 */

echo "=== DESCRIPTOGRAFAR '80EB' PARA ENCONTRAR QUANTIDADE DE TERMINAIS ===\n\n";

/**
 * Função Decrypt convertida exatamente do Pascal
 */
function decrypt($encrypted_hex, $key) {
    $C1 = 32810;
    $C2 = 52010;
    $result = '';
    
    $i = 0;
    while ($i < strlen($encrypted_hex)) {
        // Pega 2 caracteres HEX
        $hex_pair = substr($encrypted_hex, $i, 2);
        $x = intval($hex_pair, 16);
        
        // XOR com (Key shr 8)
        $byte = $x ^ ($key >> 8);
        
        // Converte para caractere
        $result .= chr($byte & 0xFF);
        
        // Atualiza Key
        $key = (($x & 0xFF) + $key) * $C1 + $C2;
        $key = $key & 0xFFFF; // Mantém como Word (16-bit)
        
        $i += 2;
    }
    
    return $result;
}

// Testar com a chave padrão do projeto
$encrypted = "80EB";
$key = 2024;

echo "Entrada:\n";
echo "  Valor criptografado: '$encrypted'\n";
echo "  Chave: $key\n\n";

// Descriptografar
$decrypted = decrypt($encrypted, $key);

echo "Resultado:\n";
echo "  Descriptografado: '$decrypted'\n";

// Verificar se é um número válido
$is_numeric = is_numeric($decrypted);
echo "  É número? " . ($is_numeric ? "SIM ✓" : "NÃO ✗") . "\n";

if ($is_numeric) {
    $num_terminais = intval($decrypted);
    echo "  Número inteiro: $num_terminais\n";
    echo "\n  ✓ RESPOSTA: '80EB' representa $num_terminais TERMINAIS\n";
} else {
    echo "  Valor em ASCII:\n";
    for ($i = 0; $i < strlen($decrypted); $i++) {
        $ascii = ord($decrypted[$i]);
        echo "    Caractere $i: '" . $decrypted[$i] . "' (ASCII: $ascii)\n";
    }
    
    echo "\n  ✗ Não é um número válido com KEY=2024\n";
    echo "\n  Testando com outras chaves...\n\n";
    
    // Testar com outras chaves possíveis
    $test_keys = [1, 10, 100, 1000, 2023, 2024, 2025, 5000, 10000, 32810, 52010];
    
    foreach ($test_keys as $test_key) {
        $dec = decrypt($encrypted, $test_key);
        if (is_numeric($dec)) {
            echo "  ✓ KEY=$test_key: '$encrypted' = $dec TERMINAIS\n";
        }
    }
}

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";

// Cálculo manual passo a passo
echo "\nCÁLCULO MANUAL PASSO-A-PASSO:\n\n";

echo "ITERAÇÃO 1 (processa '80'):\n";
$x1 = 0x80;
$key1_shr_8 = 2024 >> 8;
$byte1 = $x1 ^ $key1_shr_8;
echo "  x = 0x80 = " . $x1 . "\n";
echo "  Key shr 8 = 2024 >> 8 = " . $key1_shr_8 . "\n";
echo "  " . $x1 . " XOR " . $key1_shr_8 . " = " . $byte1 . "\n";
echo "  chr(" . $byte1 . ") = '" . chr($byte1) . "' (ASCII: " . $byte1 . ")\n";
$new_key1 = (($x1 & 0xFF) + 2024) * 32810 + 52010;
$new_key1 = $new_key1 & 0xFFFF;
echo "  Novo Key = " . $new_key1 . "\n\n";

echo "ITERAÇÃO 2 (processa 'EB'):\n";
$x2 = 0xEB;
$key2_shr_8 = $new_key1 >> 8;
$byte2 = $x2 ^ $key2_shr_8;
echo "  x = 0xEB = " . $x2 . "\n";
echo "  Key shr 8 = " . $new_key1 . " >> 8 = " . $key2_shr_8 . "\n";
echo "  " . $x2 . " XOR " . $key2_shr_8 . " = " . $byte2 . "\n";
echo "  chr(" . $byte2 . ") = '" . chr($byte2) . "' (ASCII: " . $byte2 . ")\n\n";

$final_string = chr($byte1) . chr($byte2);
echo "RESULTADO FINAL:\n";
echo "  Descriptografado: '$final_string'\n";
echo "  Código ASCII: " . implode(', ', array_map('ord', str_split($final_string))) . "\n";
?>
