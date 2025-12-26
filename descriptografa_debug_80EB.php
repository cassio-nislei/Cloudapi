<?php
/**
 * Descriptografa "80EB" com KEY=2024
 * Com LOG detalhado de cada passo
 */

function decrypt_debug($encrypted_hex, $key = 2024) {
    echo "\n=== DESCRIPTOGRAFIA DEBUG ===\n";
    echo "Valor criptografado: " . $encrypted_hex . "\n";
    echo "KEY inicial: " . $key . "\n";
    echo "C1: 32810\n";
    echo "C2: 52010\n\n";
    
    $C1 = 32810;
    $C2 = 52010;
    $result = '';
    $i = 0;
    
    // Processa os pares de hex
    while ($i < strlen($encrypted_hex)) {
        $hex_pair = substr($encrypted_hex, $i, 2);
        $decimal = hexdec($hex_pair);
        
        echo "Passo " . ($i/2 + 1) . ":\n";
        echo "  Hex: " . $hex_pair . " => Decimal: " . $decimal . "\n";
        echo "  KEY antes: " . $key . "\n";
        echo "  KEY >> 8 (Key / 256): " . ($key >> 8) . "\n";
        echo "  XOR: " . $decimal . " XOR " . ($key >> 8) . " = " . ($decimal ^ ($key >> 8)) . "\n";
        
        $decrypted_byte = $decimal ^ ($key >> 8);
        $decrypted_char = chr($decrypted_byte);
        
        echo "  Char: '" . $decrypted_char . "' (ASCII " . $decrypted_byte . ")\n";
        
        $result .= $decrypted_char;
        
        // Atualiza KEY: (x + Key) * C1 + C2
        // Nota: em Delphi, isso é (byte(x) + Key) onde a soma é feita antes da multiplicação
        $key_calc = ($decimal + $key) * $C1 + $C2;
        $key = $key_calc & 0xFFFF; // Máscara de 16 bits (Word)
        
        echo "  KEY novo: (" . $decimal . " + " . ($key_calc / $C1 / $C2) . ") * " . $C1 . " + " . $C2 . " = " . $key_calc . " & 0xFFFF = " . $key . "\n\n";
        
        $i += 2;
    }
    
    echo "=== RESULTADO ===\n";
    echo "String descriptografada: '" . $result . "'\n";
    echo "Bytes: " . implode(',', array_map('ord', str_split($result))) . "\n";
    
    // Tenta converter para número
    $num = intval($result);
    echo "Como número (intval): " . $num . "\n";
    
    return $result;
}

// Teste
$resultado = decrypt_debug('80EB', 2024);

// Tenta também sem a máscara de 16 bits para comparar
echo "\n\n=== TESTE SEM MÁSCARA 16 BITS ===\n";

function decrypt_no_mask($encrypted_hex, $key = 2024) {
    echo "Valor criptografado: " . $encrypted_hex . "\n";
    echo "KEY inicial: " . $key . "\n\n";
    
    $C1 = 32810;
    $C2 = 52010;
    $result = '';
    $i = 0;
    
    while ($i < strlen($encrypted_hex)) {
        $hex_pair = substr($encrypted_hex, $i, 2);
        $decimal = hexdec($hex_pair);
        
        $decrypted_byte = $decimal ^ ($key >> 8);
        $decrypted_char = chr($decrypted_byte & 0xFF); // Máscara de 8 bits para char
        
        echo "Passo " . ($i/2 + 1) . ": Hex=" . $hex_pair . " -> Char='" . $decrypted_char . "' (ASCII " . $decrypted_byte . ")\n";
        
        $result .= $decrypted_char;
        
        // SEM máscara de 16 bits
        $key = ($decimal + $key) * $C1 + $C2;
        
        $i += 2;
    }
    
    echo "\nResultado: '" . $result . "'\n";
    echo "Como número: " . intval($result) . "\n";
    
    return $result;
}

$resultado_no_mask = decrypt_no_mask('80EB', 2024);

// Tenta com valores hex diretos
echo "\n\n=== TESTE INTERPRETANDO 80EB COMO NÚMERO HEX ===\n";
echo "80EB como hex puro: " . hexdec('80EB') . "\n";
echo "80EB dividido por 256: " . (hexdec('80EB') / 256) . "\n";
?>
