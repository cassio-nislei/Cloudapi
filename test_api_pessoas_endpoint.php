<?php
/**
 * Teste do novo endpoint /api/pessoas?cnpj=XXX
 * Validação se o correção do btnRegistrarEmpresa funciona
 */

$baseURL = 'http://localhost:8080/';

// Função auxiliar para fazer requisição
function testarEndpoint($url, $metodo = 'GET', $headers = [], $body = null) {
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "TESTE: $metodo $url\n";
    echo str_repeat("=", 70) . "\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
    
    // Headers padrão
    $headers = array_merge([
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: Test-Script/1.0'
    ], $headers);
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($body) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Code: $http_code\n";
    
    if ($error) {
        echo "Erro cURL: $error\n";
        return null;
    }
    
    echo "\nResposta:\n";
    $json = json_decode($response, true);
    if ($json) {
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo $response . "\n";
    }
    
    return $json;
}

echo "\n";
echo "╔" . str_repeat("=", 68) . "╗\n";
echo "║" . str_pad("TESTE DO ENDPOINT /api/pessoas", 68) . "║\n";
echo "║" . str_pad("Validação da Correção do btnRegistrarEmpresa", 68) . "║\n";
echo "╚" . str_repeat("=", 68) . "╝\n";

// CNPJs de teste
$cnpjs = [
    '92702067',           // Limpo (deve existir)
    '92.702.067/0001-96', // Formatado (deve existir)
    '26578378',           // Limpo (pode existir)
    '26.578.378/0001-60', // Formatado (pode existir)
    '11111111111111',     // Inválido (não deve existir)
];

$token = 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg';

echo "\n1. TESTE SEM AUTENTICAÇÃO (Bearer Token)\n";

foreach (array_slice($cnpjs, 0, 2) as $cnpj) {
    $url = $baseURL . 'api/pessoas?cnpj=' . urlencode($cnpj);
    testarEndpoint($url);
}

echo "\n\n2. TESTE COM AUTENTICAÇÃO (Bearer Token)\n";

foreach ($cnpjs as $cnpj) {
    $url = $baseURL . 'api/pessoas?cnpj=' . urlencode($cnpj);
    $headers = [
        'Authorization: Bearer ' . $token
    ];
    testarEndpoint($url, 'GET', $headers);
}

echo "\n\n3. TESTE USANDO PARÂMETRO cgc (alternativa)\n";

$url = $baseURL . 'api/pessoas?cgc=' . urlencode('92702067');
$headers = [
    'Authorization: Bearer ' . $token
];
testarEndpoint($url, 'GET', $headers);

echo "\n\n";
echo "╔" . str_repeat("=", 68) . "╗\n";
echo "║" . str_pad("TESTES CONCLUÍDOS", 68) . "║\n";
echo "╚" . str_repeat("=", 68) . "╝\n";
echo "\n";

?>
