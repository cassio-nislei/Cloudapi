<?php
/**
 * Teste direto no IP/Porta do servidor
 * URL: http://104.234.173.105:7010/
 */

$base_url = 'http://104.234.173.105:7010';

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TESTE DIRETO - IP:PORTA                                                ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

// Teste 1: Root
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📌 Teste 1: Root da aplicação\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$url = $base_url . '/';
echo "URL: $url\n\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

$response = @file_get_contents($url, false, $context);
$http_code = isset($http_response_header) ? substr($http_response_header[0], 9, 3) : 'ERRO';

if ($response === false) {
    echo "❌ ERRO: Não foi possível conectar\n";
} else {
    echo "✅ HTTP/1.1 $http_code\n";
    echo "   Resposta (preview): " . substr($response, 0, 200) . "...\n\n";
}

// Teste 2: /api/pessoas
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📌 Teste 2: /api/pessoas com CNPJ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$url = $base_url . '/api/pessoas?cnpj=92702067000196';
echo "URL: $url\n\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

$response = @file_get_contents($url, false, $context);
$http_code = isset($http_response_header) ? substr($http_response_header[0], 9, 3) : 'ERRO';

if ($response === false) {
    echo "❌ ERRO: Não foi possível conectar\n";
} else {
    echo "✅ HTTP/1.1 $http_code\n";
    if ($http_code == '200') {
        echo "   Resposta JSON:\n";
        $json = json_decode($response, true);
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    } else {
        echo "   Resposta (preview): " . substr($response, 0, 300) . "...\n\n";
    }
}

// Teste 3: /passport
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📌 Teste 3: /passport (teste se servidor está online)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$url = $base_url . '/passport?cgc=92702067&hostname=TEST&guid=00000000-0000-0000-0000-000000000000';
echo "URL: $url\n\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

$response = @file_get_contents($url, false, $context);
$http_code = isset($http_response_header) ? substr($http_response_header[0], 9, 3) : 'ERRO';

if ($response === false) {
    echo "❌ ERRO: Não foi possível conectar\n";
} else {
    echo "✅ HTTP/1.1 $http_code\n";
    if ($http_code == '200') {
        echo "   Resposta JSON:\n";
        $json = json_decode($response, true);
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    } else {
        echo "   Resposta (preview): " . substr($response, 0, 300) . "...\n\n";
    }
}

// Teste 4: /v1/api/pessoas (caso a base_url não inclua v1)
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📌 Teste 4: /v1/api/pessoas (com prefixo v1)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$url = $base_url . '/v1/api/pessoas?cnpj=92702067000196';
echo "URL: $url\n\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

$response = @file_get_contents($url, false, $context);
$http_code = isset($http_response_header) ? substr($http_response_header[0], 9, 3) : 'ERRO';

if ($response === false) {
    echo "❌ ERRO: Não foi possível conectar\n";
} else {
    echo "✅ HTTP/1.1 $http_code\n";
    if ($http_code == '200') {
        echo "   Resposta JSON:\n";
        $json = json_decode($response, true);
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    } else {
        echo "   Resposta (preview): " . substr($response, 0, 300) . "...\n\n";
    }
}

// Resumo
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ RESUMO                                                                   ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

echo "✅ Se /api/pessoas retorna HTTP 200:\n";
echo "   → Api.php está funcionando!\n";
echo "   → Próximo passo: Compilar Delphi\n\n";

echo "❌ Se /api/pessoas retorna HTTP 404:\n";
echo "   → Api.php NÃO está sendo encontrado\n";
echo "   → Verificar estrutura de pastas no servidor\n\n";

echo "✅ Se /passport retorna HTTP 200:\n";
echo "   → Servidor está online\n";
echo "   → Problema é específico da rota /api/pessoas\n\n";

echo "❌ Se todos os testes retornam erro:\n";
echo "   → Servidor pode estar offline\n";
echo "   → Ou IP/Porta incorretos\n\n";

?>
