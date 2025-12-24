<?php
/**
 * Teste do endpoint /pessoas (antigo) com CNPJs específicos
 * Testa se a API base está respondendo
 */

$baseURL = 'https://admcloud.papion.com.br/v1/';
$token = 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg';

echo "╔" . str_repeat("=", 78) . "╗\n";
echo "║" . str_pad("TESTE DO ENDPOINT /pessoas (SEM /api)", 78) . "║\n";
echo "║" . str_pad("Verificando se servidor está respondendo", 78) . "║\n";
echo "╚" . str_repeat("=", 78) . "╝\n";

// Teste 1: Tentar /pessoas
echo "\n✓ Teste 1: GET /pessoas (endpoint antigo)\n";
echo str_repeat("-", 80) . "\n";

$url = $baseURL . 'pessoas?cnpj=92702067000196';
echo "URL: $url\n\n";

try {
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'header' => "Content-Type: application/json\r\n"
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        $json = json_decode($response, true);
        if ($json) {
            echo "✅ Servidor respondeu!\n";
            echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "⚠️  Resposta recebida mas não é JSON:\n";
            echo $response . "\n";
        }
    } else {
        echo "❌ Erro ao conectar\n";
    }
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "\n";
}

// Teste 2: Listar endpoints disponíveis
echo "\n\n✓ Teste 2: GET / (raiz da API)\n";
echo str_repeat("-", 80) . "\n";

$url = $baseURL;
echo "URL: $url\n\n";

try {
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'header' => "Content-Type: application/json\r\n"
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        $json = json_decode($response, true);
        if ($json) {
            echo "✅ Servidor respondeu!\n";
            echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "⚠️  Resposta recebida:\n";
            echo substr($response, 0, 500) . "\n";
        }
    } else {
        echo "❌ Erro ao conectar\n";
    }
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "\n";
}

// Teste 3: Verificar /passport (endpoint público)
echo "\n\n✓ Teste 3: GET /passport (endpoint público)\n";
echo str_repeat("-", 80) . "\n";

$url = $baseURL . 'passport?cgc=92702067&hostname=TEST&guid=00000000-0000-0000-0000-000000000000';
echo "URL: $url\n\n";

try {
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'header' => "Content-Type: application/json\r\n"
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        $json = json_decode($response, true);
        if ($json) {
            echo "✅ Servidor respondeu!\n";
            echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "⚠️  Resposta recebida:\n";
            echo substr($response, 0, 500) . "\n";
        }
    } else {
        echo "❌ Erro ao conectar\n";
    }
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "\n";
}

echo "\n\n";
echo "╔" . str_repeat("=", 78) . "╗\n";
echo "║" . str_pad("CONCLUSÃO", 78) . "║\n";
echo "╚" . str_repeat("=", 78) . "╝\n";
echo "\n";
?>
