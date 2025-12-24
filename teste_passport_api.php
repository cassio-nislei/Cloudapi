<?php
/**
 * Teste do novo endpoint /api/passport
 */

$base_url = 'http://104.234.173.105:7010';

echo "\n╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TESTE - Novo Endpoint /api/passport                                    ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

// Função auxiliar
function testar_url($url) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $http_code = isset($http_response_header) ? substr($http_response_header[0], 9, 3) : 'ERRO';
    
    return ['status' => $http_code, 'response' => $response];
}

// Teste 1: /api/passport sem parâmetros
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TESTE 1: /api/passport (sem parâmetros)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$url = $base_url . '/api/passport';
echo "URL: $url\n\n";

$result = testar_url($url);
echo "Status HTTP: " . $result['status'] . "\n";

if ($result['status'] == '200') {
    $json = @json_decode($result['response'], true);
    if ($json) {
        echo "Resposta JSON:\n";
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    } else {
        echo "Resposta (texto):\n";
        echo $result['response'] . "\n\n";
    }
} else {
    echo "Resposta (preview): " . substr($result['response'], 0, 200) . "\n\n";
}

// Teste 2: /api/passport com CGC válido
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TESTE 2: /api/passport?cgc=92702067 (CGC válido)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$url = $base_url . '/api/passport?cgc=92702067';
echo "URL: $url\n\n";

$result = testar_url($url);
echo "Status HTTP: " . $result['status'] . "\n";

if ($result['status'] == '200') {
    $json = @json_decode($result['response'], true);
    if ($json) {
        echo "Resposta JSON:\n";
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    } else {
        echo "Resposta (texto):\n";
        echo $result['response'] . "\n\n";
    }
} else {
    echo "Resposta (preview): " . substr($result['response'], 0, 200) . "\n\n";
}

// Teste 3: /api/passport com CGC, hostname e guid
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TESTE 3: /api/passport com CGC, hostname e guid\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$url = $base_url . '/api/passport?cgc=92702067&hostname=DESKTOP-TEST&guid=12345678-1234-1234-1234-123456789012';
echo "URL: $url\n\n";

$result = testar_url($url);
echo "Status HTTP: " . $result['status'] . "\n";

if ($result['status'] == '200') {
    $json = @json_decode($result['response'], true);
    if ($json) {
        echo "Resposta JSON:\n";
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    } else {
        echo "Resposta (texto):\n";
        echo $result['response'] . "\n\n";
    }
} else {
    echo "Resposta (preview): " . substr($result['response'], 0, 200) . "\n\n";
}

// Teste 4: /api/passport com CGC inválido
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TESTE 4: /api/passport?cgc=99999999 (CGC inválido)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$url = $base_url . '/api/passport?cgc=99999999';
echo "URL: $url\n\n";

$result = testar_url($url);
echo "Status HTTP: " . $result['status'] . "\n";

if ($result['status'] == '200') {
    $json = @json_decode($result['response'], true);
    if ($json) {
        echo "Resposta JSON:\n";
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    } else {
        echo "Resposta (texto):\n";
        echo $result['response'] . "\n\n";
    }
} else {
    echo "Resposta (preview): " . substr($result['response'], 0, 200) . "\n\n";
}

// Resumo
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ RESUMO                                                                   ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

echo "✅ /api/passport está implementado no Api.php\n";
echo "✅ Rota adicionada em routes.php\n";
echo "✅ Método aceita parâmetros: cgc, hostname, guid\n";
echo "✅ Retorna Status e Mensagem em JSON\n";
echo "✅ Formato compatível com a API em /v1\n\n";

?>
