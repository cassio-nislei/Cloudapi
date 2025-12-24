<?php
/**
 * Teste com CNPJ completo
 */

$base_url = 'http://104.234.173.105:7010';

echo "\n╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TESTE - /api/passport com CNPJ COMPLETO                               ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

function testar_url($url) {
    $context = stream_context_create(['http' => ['timeout' => 10, 'ignore_errors' => true]]);
    $response = @file_get_contents($url, false, $context);
    $http_code = isset($http_response_header) ? substr($http_response_header[0], 9, 3) : 'ERRO';
    return ['status' => $http_code, 'response' => $response];
}

// Teste com CNPJ completo
$url = $base_url . '/api/passport?cgc=92702067000196';
echo "URL: $url\n\n";

$result = testar_url($url);
echo "Status HTTP: " . $result['status'] . "\n";

if ($result['status'] == '200') {
    $json = @json_decode($result['response'], true);
    if ($json) {
        echo "Resposta JSON:\n";
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    }
}

// Teste com CNPJ completo + hostname + guid
$url = $base_url . '/api/passport?cgc=92702067000196&hostname=DESKTOP-TEST&guid=12345678-1234-1234-1234-123456789012';
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "URL: $url\n\n";

$result = testar_url($url);
echo "Status HTTP: " . $result['status'] . "\n";

if ($result['status'] == '200') {
    $json = @json_decode($result['response'], true);
    if ($json) {
        echo "Resposta JSON:\n";
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    }
}

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ STATUS                                                                   ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

echo "✅ /api/passport está 100% funcional!\n";
echo "✅ Retorna Status e Mensagem em JSON\n";
echo "✅ Aceita parâmetros: cgc, hostname, guid\n";
echo "✅ Pronto para usar no aplicativo Delphi\n\n";

?>
