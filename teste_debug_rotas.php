<?php
/**
 * TESTE AVANÇADO DE ROTAS (Versão corrigida com file_get_contents)
 */

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TESTE AVANÇADO: Diagnóstico de Rotas da API                           ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

$baseUrl = 'https://admcloud.papion.com.br/v1';
$cnpj = '92702067000196';
$token = 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg';

// Diferentes variações de URL a testar
$testeVariacoes = [
    "GET /api/pessoas?cnpj={$cnpj}" => "{$baseUrl}/api/pessoas?cnpj={$cnpj}",
    "GET /api/pessoas/ (com barra)" => "{$baseUrl}/api/pessoas/?cnpj={$cnpj}",
    "GET /index.php/api/pessoas" => "{$baseUrl}/index.php/api/pessoas?cnpj={$cnpj}",
];

foreach ($testeVariacoes as $descricao => $url) {
    echo "✓ Teste: {$descricao}\n";
    echo "────────────────────────────────────────────────────────────────────\n";
    echo "URL: {$url}\n";
    
    try {
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
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            // Obter informações do erro
            if (!empty($http_response_header)) {
                $status = $http_response_header[0];
                echo "❌ Erro: {$status}\n";
            } else {
                echo "❌ Erro ao conectar ao servidor\n";
            }
        } else {
            // Sucesso
            if (!empty($http_response_header)) {
                $status = $http_response_header[0];
                echo "✅ {$status}\n";
            }
            
            if (!empty($response)) {
                $decoded = json_decode($response, true);
                if ($decoded) {
                    echo "Resposta JSON:\n";
                    echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
                } else {
                    echo "Resposta (raw):\n";
                    echo substr($response, 0, 300) . "...\n";
                }
            }
        }
    } catch (Exception $e) {
        echo "❌ Exceção: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ RESUMO DOS TESTES                                                      ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

echo "Baseado nos testes anteriores:\n";
echo "✅ /passport (endpoint público) RESPONDE\n";
echo "❌ /pessoas e / retornam erro de conexão\n";
echo "❌ /api/pessoas não foi testado com sucesso ainda\n\n";

echo "POSSÍVEIS CAUSAS:\n";
echo "1. Api.php NÃO foi deployado para o servidor de produção\n";
echo "2. Problema de routing no servidor (index.php obrigatório?)\n";
echo "3. Problema de firewall/WAF bloqueando certos endpoints\n\n";

echo "PRÓXIMOS PASSOS:\n";
echo "→ Verificar se Api.php existe em: /application/controllers/Api.php no servidor\n";
echo "→ Verificar config.php: qual é a 'base_url'?\n";
echo "→ Verificar .htaccess: está removendo index.php?\n";
?>
