<?php
/**
 * Teste do endpoint /pessoas?cnpj=XXXXX
 * Valida se o endpoint retorna corretamente as pessoas por CNPJ
 */

// Configuração
$api_base = 'http://localhost:8080/';
$api_endpoint = 'pessoas';

// CNPJs de teste (alguns que existem no banco)
$cnpjs_teste = [
    '92702067',           // Sem formatação
    '92.702.067/0001-96', // Com formatação
    '26578378',           // Outro CNPJ
    '26.578.378/0001-60', // Com formatação
    '12345678901234',     // Inválido para teste
];

echo "=== TESTE DO ENDPOINT /pessoas?cnpj=XXX ===\n\n";

foreach ($cnpjs_teste as $cnpj) {
    echo "Testando CNPJ: $cnpj\n";
    echo str_repeat("-", 60) . "\n";
    
    // Fazer requisição GET
    $url = $api_base . $api_endpoint . '?cnpj=' . urlencode($cnpj);
    
    echo "URL: $url\n\n";
    
    // Usar cURL para fazer requisição
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Status: $http_code\n";
    echo "Resposta:\n";
    
    // Tentar fazer parse JSON
    $json = json_decode($response, true);
    if ($json) {
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo $response . "\n";
    }
    
    echo "\n\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
?>
