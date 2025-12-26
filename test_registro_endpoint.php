<?php
/**
 * Teste do endpoint de registro
 * Testa o POST /v1/api/registro com dados de exemplo
 */

// Configurações
$baseUrl = 'http://104.234.173.105:7010';
$username = 'api_frontbox';
$password = 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg';

// Testar diferentes possibilidades de URL
$urls = [
    'http://104.234.173.105:7010/api/registro',
    'http://104.234.173.105:7010/api/v1/registro',
    'http://104.234.173.105:7010/v1/api/registro',
    'http://104.234.173.105:7010/registro',
    'http://104.234.173.105:7010/v1/registro',
];

// Dados de exemplo para registro
$registroData = [
    'nome' => 'EMPRESA TESTE LTDA',
    'fantasia' => 'EMPRESA TESTE',
    'cgc' => '12345678000190',
    'contato' => 'CONTATO TESTE',
    'email' => 'teste@exemplo.com',
    'telefone' => '1133334444',
    'celular' => '11999998888',
    'endereco' => 'Rua Teste',
    'numero' => '123',
    'bairro' => 'Centro',
    'cidade' => 'São Paulo',
    'estado' => 'SP',
    'cep' => '01310100',
];

// JSON com wrapper 'registro'
$jsonPayload = json_encode(['registro' => $registroData], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

echo "=== TESTE DE ENDPOINT DE REGISTRO ===\n\n";
echo "Credenciais:\n";
echo "  Username: $username\n";
echo "  Password: (senha configurada)\n\n";

echo "Dados a registrar:\n";
echo "  Nome: " . $registroData['nome'] . "\n";
echo "  CNPJ: " . $registroData['cgc'] . "\n";
echo "  Email: " . $registroData['email'] . "\n\n";

echo "Testando URLs:\n\n";

foreach ($urls as $url) {
    echo "─────────────────────────────────────────────\n";
    echo "URL: $url\n";
    echo "─────────────────────────────────────────────\n";
    
    // Criar contexto HTTP com Basic Auth
    $auth = base64_encode($username . ':' . $password);
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'Authorization: Basic ' . $auth,
                'Content-Length: ' . strlen($jsonPayload),
            ],
            'content' => $jsonPayload,
            'timeout' => 30,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ];
    
    $context = stream_context_create($options);
    
    try {
        $response = @file_get_contents($url, false, $context);
        
        // Pegar informações de resposta
        $headers = $http_response_header ?? [];
        $httpCodeLine = $headers[0] ?? 'Unknown';
        
        echo "Status: $httpCodeLine\n";
        
        if ($response !== false) {
            echo "Response Length: " . strlen($response) . " bytes\n";
            if (strlen($response) > 0) {
                if (strlen($response) > 500) {
                    echo "Response (primeiros 500 chars):\n";
                    echo substr($response, 0, 500) . "...\n";
                } else {
                    echo "Response:\n";
                    echo $response . "\n";
                }
            } else {
                echo "Response: (vazio)\n";
            }
        } else {
            echo "Response: (erro ao obter)\n";
        }
    } catch (Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "\n=== FIM DO TESTE ===\n";
?>
