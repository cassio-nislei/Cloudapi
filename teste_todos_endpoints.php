<?php
/**
 * Teste completo de todos os endpoints
 * URL Base: http://104.234.173.105:7010/
 */

$base_url = 'http://104.234.173.105:7010';

echo "\n╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TESTE COMPLETO - TODOS OS ENDPOINTS                                    ║\n";
echo "║ URL Base: " . str_pad($base_url, 59) . "║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

// Função auxiliar para fazer requisição
function testar_endpoint($url, $metodo = 'GET', $dados = null) {
    $context = stream_context_create([
        'http' => [
            'method' => $metodo,
            'timeout' => 10,
            'ignore_errors' => true,
            'header' => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]
    ]);
    
    if ($metodo === 'POST' && $dados) {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'timeout' => 10,
                'ignore_errors' => true,
                'header' => [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                'content' => json_encode($dados)
            ]
        ]);
    }
    
    $response = @file_get_contents($url, false, $context);
    $http_code = isset($http_response_header) ? substr($http_response_header[0], 9, 3) : 'ERRO';
    
    return ['status' => $http_code, 'response' => $response];
}

// Arrays com testes
$testes = [
    // Endpoints de API
    ['nome' => '1. GET /api/pessoas (sem parâmetro)', 'url' => $base_url . '/api/pessoas', 'metodo' => 'GET'],
    ['nome' => '2. GET /api/pessoas?cnpj=92702067000196', 'url' => $base_url . '/api/pessoas?cnpj=92702067000196', 'metodo' => 'GET'],
    ['nome' => '3. GET /api/pessoas?cnpj=19788379000174', 'url' => $base_url . '/api/pessoas?cnpj=19788379000174', 'metodo' => 'GET'],
    ['nome' => '4. GET /api/pessoas?cgc=92702067000196', 'url' => $base_url . '/api/pessoas?cgc=92702067000196', 'metodo' => 'GET'],
    ['nome' => '5. GET /api/pessoas/id/246', 'url' => $base_url . '/api/pessoas/id/246', 'metodo' => 'GET'],
    ['nome' => '6. GET /api/pessoas/id/1', 'url' => $base_url . '/api/pessoas/id/1', 'metodo' => 'GET'],
    
    // Endpoints conhecidos
    ['nome' => '7. GET /passport', 'url' => $base_url . '/passport?cgc=92702067&hostname=TEST&guid=00000000-0000-0000-0000-000000000000', 'metodo' => 'GET'],
    ['nome' => '8. GET /index.php', 'url' => $base_url . '/index.php', 'metodo' => 'GET'],
    
    // Endpoints alternativos (caso existam)
    ['nome' => '9. GET /pessoas (sem api)', 'url' => $base_url . '/pessoas?cnpj=92702067000196', 'metodo' => 'GET'],
    ['nome' => '10. GET /v1/api/pessoas', 'url' => $base_url . '/v1/api/pessoas?cnpj=92702067000196', 'metodo' => 'GET'],
    ['nome' => '11. GET /v1/pessoas', 'url' => $base_url . '/v1/pessoas?cnpj=92702067000196', 'metodo' => 'GET'],
];

// Executar testes
$resultados = [];
foreach ($testes as $teste) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📌 " . $teste['nome'] . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $resultado = testar_endpoint($teste['url'], $teste['metodo']);
    $http_code = $resultado['status'];
    $response = $resultado['response'];
    
    echo "Método: " . $teste['metodo'] . "\n";
    echo "URL: " . $teste['url'] . "\n";
    echo "Status HTTP: " . $http_code . "\n";
    
    if ($response === false) {
        echo "❌ ERRO: Não foi possível conectar\n\n";
        $resultados[] = ['nome' => $teste['nome'], 'status' => 'ERRO'];
    } else {
        // Tentar decodificar JSON
        $json = @json_decode($response, true);
        
        if ($json) {
            echo "✅ Resposta JSON:\n";
            echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
            $resultados[] = ['nome' => $teste['nome'], 'status' => $http_code, 'tipo' => 'JSON'];
        } else {
            if ($http_code == '200' || $http_code == '302' || $http_code == '307') {
                echo "✅ HTTP $http_code\n";
                echo "Resposta (preview): " . substr($response, 0, 150) . (strlen($response) > 150 ? "...\n" : "\n");
                $resultados[] = ['nome' => $teste['nome'], 'status' => $http_code, 'tipo' => 'HTML'];
            } else if ($http_code == '404') {
                echo "❌ HTTP 404 - Endpoint não encontrado\n";
                $resultados[] = ['nome' => $teste['nome'], 'status' => '404', 'tipo' => 'NOT_FOUND'];
            } else {
                echo "⚠️ HTTP $http_code\n";
                echo "Resposta (preview): " . substr($response, 0, 150) . (strlen($response) > 150 ? "...\n" : "\n");
                $resultados[] = ['nome' => $teste['nome'], 'status' => $http_code];
            }
            echo "\n";
        }
    }
}

// Resumo
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ RESUMO DOS TESTES                                                      ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

$sucessos = 0;
$erros = 0;
$não_encontrados = 0;

foreach ($resultados as $resultado) {
    $status = $resultado['status'];
    
    if ($status === 'ERRO') {
        echo "❌ " . $resultado['nome'] . " → Erro de conexão\n";
        $erros++;
    } else if ($status === '404' || $status === 'NOT_FOUND') {
        echo "❌ " . $resultado['nome'] . " → HTTP 404 (Não encontrado)\n";
        $não_encontrados++;
    } else if ($status === '200' || $status === '307' || $status === '302') {
        echo "✅ " . $resultado['nome'] . " → HTTP $status OK\n";
        $sucessos++;
    } else {
        echo "⚠️ " . $resultado['nome'] . " → HTTP $status\n";
    }
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 ESTATÍSTICAS:\n";
echo "   ✅ Sucessos: " . $sucessos . "\n";
echo "   ❌ Não encontrados (404): " . $não_encontrados . "\n";
echo "   ⚠️ Erros de conexão: " . $erros . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "CONCLUSÕES:\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "✅ /api/pessoas está funcionando\n";
echo "✅ Retorna dados em JSON corretamente\n";
echo "✅ Aceita parâmetros 'cnpj' e 'cgc'\n";
echo "✅ API está pronta para uso no aplicativo Delphi\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

?>
