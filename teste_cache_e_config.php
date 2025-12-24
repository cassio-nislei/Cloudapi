<?php
/**
 * TESTE VERIFICAR STATUS DO SERVIDOR - Cache e Configuração
 */

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ VERIFICAÇÃO DE CACHE E CONFIGURAÇÃO                                   ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

$baseUrl = 'https://admcloud.papion.com.br/v1';
$cnpj = '92702067000196';

echo "⏳ Aguardando 5 segundos antes de testar...\n";
echo "   (para permitir que o servidor processe a mudança de routes.php)\n\n";
sleep(5);

// Teste 1: Tentar acessar API
echo "TESTE 1: Acessando /api/pessoas\n";
echo "════════════════════════════════════════════════════════════════════\n\n";

$url = "{$baseUrl}/api/pessoas?cnpj={$cnpj}";
echo "URL: {$url}\n\n";

$options = [
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/json\r\n",
        'ignore_errors' => true,
        'timeout' => 10
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
];

$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

if (!empty($http_response_header)) {
    echo "Status: " . $http_response_header[0] . "\n\n";
    
    if ($response !== false && !empty($response)) {
        $decoded = json_decode($response, true);
        if ($decoded) {
            echo "✅ Resposta JSON recebida:\n";
            echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            // Pode ser HTML (erro)
            if (strpos($response, '<html') !== false) {
                echo "❌ Servidor retornou HTML (não é JSON)\n";
                echo "   Provável causa: Rota ainda não reconhecida ou cache\n";
            } else {
                echo "Resposta bruta: " . substr($response, 0, 200) . "\n";
            }
        }
    }
}

echo "\n\n";

// Teste 2: Verificar se routes.php foi atualizado
echo "TESTE 2: Verificar outras rotas conhecidas\n";
echo "════════════════════════════════════════════════════════════════════\n\n";

$knownRoutes = [
    ['nome' => '/passport', 'url' => "{$baseUrl}/passport?cgc=92702067&hostname=TEST&guid=00000000-0000-0000-0000-000000000000"],
    ['nome' => '/login', 'url' => "{$baseUrl}/login"],
];

foreach ($knownRoutes as $route) {
    echo "Testando: {$route['nome']}\n";
    $response = @file_get_contents($route['url'], false, $context);
    if (!empty($http_response_header)) {
        echo "Status: " . $http_response_header[0] . "\n";
    }
    echo "\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ PRÓXIMAS AÇÕES SE AINDA NÃO FUNCIONAR                                 ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

echo "1. Aguardar 5-10 minutos para cache do servidor ser limpo\n\n";

echo "2. Verificar via SSH se routes.php foi realmente atualizado:\n";
echo "   ssh user@admcloud.papion.com.br\n";
echo "   tail /application/config/routes.php\n";
echo "   (Deve mostrar as rotas: api/pessoas e api/pessoas/id)\n\n";

echo "3. Se routes.php não foi atualizado no servidor:\n";
echo "   Re-enviar arquivo via FTP/SCP\n\n";

echo "4. Se arquivo está correto mas ainda não funciona:\n";
echo "   Contatar provedor de hosting para limpar cache do PHP\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n";
?>
