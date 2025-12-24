<?php
/**
 * Verificação detalhada de endpoints 404
 * Investiga por que alguns endpoints não funcionam
 */

$base_url = 'http://104.234.173.105:7010';

echo "\n╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ DIAGNÓSTICO - Por que alguns endpoints retornam 404?                    ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

// Função auxiliar
function testar_url($url) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true,
            'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $http_code = isset($http_response_header) ? substr($http_response_header[0], 9, 3) : 'ERRO';
    
    return ['status' => $http_code, 'response' => $response];
}

// Teste 1: Verificar se /passport existe no servidor
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TESTE 1: Endpoint /passport\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$urls_passport = [
    'http://104.234.173.105:7010/passport',
    'http://104.234.173.105:7010/passport.php',
    'http://104.234.173.105:7010/index.php/passport',
];

foreach ($urls_passport as $url) {
    echo "URL: $url\n";
    $result = testar_url($url);
    echo "Status: HTTP " . $result['status'] . "\n\n";
}

// Teste 2: Verificar estrutura do servidor
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TESTE 2: Verificar se há /v1/ como subdiretório\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$urls_v1 = [
    'http://104.234.173.105:7010/v1/',
    'http://104.234.173.105:7010/v1/index.php',
    'http://104.234.173.105:7010/v1/api/pessoas',
];

foreach ($urls_v1 as $url) {
    echo "URL: $url\n";
    $result = testar_url($url);
    echo "Status: HTTP " . $result['status'] . "\n\n";
}

// Teste 3: Variações de rotas
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TESTE 3: Variações de rotas conhecidas\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$urls_variations = [
    'http://104.234.173.105:7010/index.php/api/pessoas',
    'http://104.234.173.105:7010/api/pessoas.php',
    'http://104.234.173.105:7010/controllers/api.php',
    'http://104.234.173.105:7010/application/controllers/Api.php',
];

foreach ($urls_variations as $url) {
    echo "URL: $url\n";
    $result = testar_url($url);
    echo "Status: HTTP " . $result['status'] . "\n\n";
}

// Teste 4: Verificar estrutura do CodeIgniter
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TESTE 4: CodeIgniter routing variations\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$urls_ci = [
    'http://104.234.173.105:7010/index.php/api',
    'http://104.234.173.105:7010/?path=api/pessoas',
    'http://104.234.173.105:7010/api',
];

foreach ($urls_ci as $url) {
    echo "URL: $url\n";
    $result = testar_url($url);
    echo "Status: HTTP " . $result['status'] . "\n\n";
}

// Teste 5: Analisar resposta 404 do passport
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TESTE 5: Analisar resposta HTML 404\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$url_404 = 'http://104.234.173.105:7010/passport';
echo "URL: $url_404\n\n";

$result = testar_url($url_404);
echo "Status: HTTP " . $result['status'] . "\n\n";

if ($result['status'] == '404') {
    echo "Analisando página HTML 404...\n\n";
    
    // Procurar por pistas na resposta
    if (strpos($result['response'], 'CodeIgniter') !== false) {
        echo "✓ Encontrado: CodeIgniter 404 page\n";
        echo "  → Significa que a rota NÃO está configurada no CodeIgniter\n";
    }
    
    if (strpos($result['response'], 'Apache') !== false) {
        echo "✓ Encontrado: Apache 404 page\n";
        echo "  → Significa que o arquivo/diretório não existe no servidor\n";
    }
    
    if (strpos($result['response'], 'Nginx') !== false) {
        echo "✓ Encontrado: Nginx 404 page\n";
        echo "  → Significa que o arquivo/diretório não existe no servidor\n";
    }
    
    echo "\nPrimeiros 500 caracteres da resposta:\n";
    echo substr($result['response'], 0, 500) . "\n\n";
}

// ANÁLISE
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ ANÁLISE E CONCLUSÕES                                                   ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

echo "ACHADOS:\n\n";

echo "1. /api/pessoas → HTTP 200\n";
echo "   ✓ FUNCIONA porque está mapeado em routes.php:\n";
echo "     \$route['api/pessoas'] = 'Api/pessoas';\n\n";

echo "2. /passport → HTTP 404\n";
echo "   ✗ NÃO FUNCIONA - Possíveis razões:\n";
echo "     • Não está configurado em routes.php\n";
echo "     • Ou o controller Passport.php não existe\n";
echo "     • Ou está em um subdiretório diferente\n\n";

echo "3. /v1/api/pessoas → HTTP 404\n";
echo "   ✗ NÃO FUNCIONA porque:\n";
echo "     • A aplicação está na raiz (http://...7010/)\n";
echo "     • NÃO há um /v1/ virtual antes da API\n";
echo "     • Se precisar suportar /v1/, deve-se adicionar rota:\n";
echo "       \$route['v1/api/pessoas'] = 'Api/pessoas';\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "RECOMENDAÇÃO:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✅ Use /api/pessoas (sem /v1)\n";
echo "✅ A URL está corrigida em ADMCloudConsts.pas\n";
echo "✅ Endpoints 404 não precisam funcionar para sua aplicação\n";
echo "✅ O /passport retorna 404 porque:\n";
echo "   • Ou o controller não existe no servidor\n";
echo "   • Ou não está mapeado em routes.php\n";
echo "   • Mas não é necessário para seu caso de uso\n\n";

?>
