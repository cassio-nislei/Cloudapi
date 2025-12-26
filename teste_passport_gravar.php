<?php

/**
 * TESTE PASSPORT - Validar se está gravando na tabela PESSOA_LICENCAS
 */

// Configuração
$API_URL = 'http://104.234.173.105:7010';
$USERNAME = 'api_frontbox';
$PASSWORD = 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg';

// Dados para teste
$CGC = '19788379000174';  // CNPJ da empresa RO CARNES
$HOSTNAME = 'DESKTOP-TEST';
$GUID = '550e8400-e29b-41d4-a716-446655440000';

// Montar URL do Passport
$url = $API_URL . '/api/passport?cgc=' . $CGC . '&hostname=' . $HOSTNAME . '&guid=' . $GUID;

echo "═══════════════════════════════════════════════════════════\n";
echo "TESTE PASSPORT - Validando se está gravando na base\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "URL: " . $url . "\n";
echo "CGC: " . $CGC . "\n";
echo "HOSTNAME: " . $HOSTNAME . "\n";
echo "GUID: " . $GUID . "\n\n";

// Fazer requisição GET com autenticação Basic
$auth = base64_encode($USERNAME . ':' . $PASSWORD);

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: Basic " . $auth . "\r\n" .
                    "Content-Type: application/json\r\n",
        'timeout' => 30
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "✗ ERRO na requisição HTTP\n";
    echo "Verifique se o servidor está acessível em: " . $API_URL . "\n";
    if (!empty($http_response_header)) {
        echo "Headers: " . json_encode($http_response_header) . "\n";
    }
} else {
    echo "✓ Requisição realizada com sucesso\n\n";
    
    echo "═══════════════════════════════════════════════════════════\n";
    echo "RESPOSTA BRUTA DA API:\n";
    echo "═══════════════════════════════════════════════════════════\n";
    echo $response . "\n\n";

    // Tentar fazer parse JSON
    echo "═══════════════════════════════════════════════════════════\n";
    echo "RESPOSTA PARSEADA:\n";
    echo "═══════════════════════════════════════════════════════════\n";

    $json = json_decode($response, true);

    if ($json) {
        echo "✓ JSON válido\n";
        echo "Status: " . (isset($json['Status']) ? ($json['Status'] ? 'TRUE' : 'FALSE') : (isset($json['status']) ? ($json['status'] ? 'TRUE' : 'FALSE') : 'N/A')) . "\n";
        echo "Mensagem: " . (isset($json['Mensagem']) ? $json['Mensagem'] : (isset($json['msg']) ? $json['msg'] : 'N/A')) . "\n";
        
        if (isset($json['Dados'])) {
            echo "\nDADOS DO CLIENTE RETORNADOS:\n";
            echo json_encode($json['Dados'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        }
    } else {
        echo "✗ JSON inválido ou não é JSON\n";
        echo "Erro JSON: " . json_last_error_msg() . "\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════\n";
echo "PRÓXIMO PASSO: Verificar manualmente na base se apareceu em PESSOA_LICENCAS:\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "SELECT * FROM PESSOA_LICENCAS WHERE GUID = '" . $GUID . "';\n";
echo "\n";

?>

