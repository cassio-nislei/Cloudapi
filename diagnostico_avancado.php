<?php
/**
 * DIAGNÓSTICO AVANÇADO - Verificar status da API em Produção
 */

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ DIAGNÓSTICO AVANÇADO - Teste de Endpoints                             ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

$baseUrl = 'https://admcloud.papion.com.br/v1';
$token = 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg';
$cnpj = '92702067000196';

// Lista de endpoints para testar
$endpoints = [
    [
        'nome' => 'Endpoint NOVO: /api/pessoas (onde Api.php deve estar)',
        'url' => "{$baseUrl}/api/pessoas?cnpj={$cnpj}",
        'descricao' => 'Deveria ser: HTTP 200 OK (se Api.php foi deployado)'
    ],
    [
        'nome' => 'Endpoint ALTERNATIVO: /pessoas/cnpj (se existe em Pessoas.php)',
        'url' => "{$baseUrl}/pessoas?cnpj={$cnpj}",
        'descricao' => 'Alternativa caso Api.php não funcione'
    ],
    [
        'nome' => 'Endpoint CONHECIDO: /passport (teste se servidor está online)',
        'url' => "{$baseUrl}/passport?cgc=92702067&hostname=TEST&guid=00000000-0000-0000-0000-000000000000",
        'descricao' => 'Deve funcionar se servidor está online'
    ],
    [
        'nome' => 'Root da API: / (raiz do servidor)',
        'url' => "{$baseUrl}/",
        'descricao' => 'Deve retornar alguma resposta'
    ]
];

foreach ($endpoints as $endpoint) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📌 " . $endpoint['nome'] . "\n";
    echo "   " . $endpoint['descricao'] . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "URL: " . $endpoint['url'] . "\n\n";
    
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json\r\n"
            ],
            'ignore_errors' => true,
            'timeout' => 10
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($endpoint['url'], false, $context);
    
    if ($response === false) {
        echo "❌ Erro ao conectar\n";
        if (!empty($http_response_header)) {
            echo "   Status: " . $http_response_header[0] . "\n";
        }
    } else {
        if (!empty($http_response_header)) {
            echo "✅ " . $http_response_header[0] . "\n";
        }
        
        if (!empty($response)) {
            $decoded = json_decode($response, true);
            if ($decoded) {
                echo "   Resposta JSON:\n";
                echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            } else {
                $preview = substr($response, 0, 200);
                echo "   Resposta (preview): " . $preview . "...\n";
            }
        }
    }
    
    echo "\n";
}

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ DIAGNÓSTICO ANÁLISE                                                    ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

echo "📋 INTERPRETAÇÃO DOS RESULTADOS:\n\n";

echo "✅ Se /api/pessoas retorna HTTP 200:\n";
echo "   → Api.php foi deployado com sucesso!\n";
echo "   → Próximo passo: Compilar Delphi e testar\n\n";

echo "❌ Se /api/pessoas retorna HTTP 404:\n";
echo "   → Api.php NÃO está em /application/controllers/\n";
echo "   → Possíveis causas:\n";
echo "      1. Arquivo não foi enviado\n";
echo "      2. Arquivo está em pasta errada\n";
echo "      3. Arquivo foi deletado após upload\n";
echo "   → Solução: Re-enviar Api.php\n\n";

echo "✅ Se /passport retorna HTTP 200:\n";
echo "   → Servidor está online e respondendo\n";
echo "   → Problema é específico do /api/pessoas\n\n";

echo "❌ Se /passport retorna erro:\n";
echo "   → Servidor pode estar offline\n";
echo "   → Ou problema de conectividade\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n";
?>
