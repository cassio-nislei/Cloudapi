<?php
/**
 * Teste do Endpoint de Registro com os novos campos: tipo, CNAE, IM
 * Verifica se os dados estão sendo salvos corretamente
 */

$baseURL = 'http://localhost';
// Para produção, use: $baseURL = 'https://admcloud.papion.com.br';

echo "=" . str_repeat("=", 100) . "\n";
echo "TESTE: Registro de Empresa com TIPO, CNAE e IM\n";
echo "=" . str_repeat("=", 100) . "\n\n";

// Dados de teste
$registro = [
    'nome'         => 'EMPRESA TESTE TIPOS LTDA',
    'fantasia'     => 'EMPRESA TESTE TIPOS',
    'cgc'          => '12345678901234',
    'contato'      => 'João Silva',
    'email'        => 'joao@empresa.test',
    'telefone'     => '1133334444',
    'endereco'     => 'Rua das Flores',
    'numero'       => '100',
    'bairro'       => 'Centro',
    'cidade'       => 'São Paulo',
    'estado'       => 'SP',
    'cep'          => '01310100',
    'tipo'         => 'Jurídica',      // NOVO
    'cnae'         => '6202-3/00',     // NOVO
    'im'           => '123456789',     // NOVO
];

$endpoint = '/v1/api/registro';
$url = $baseURL . $endpoint;

echo "📍 URL: $url\n";
echo "📊 Método: POST\n\n";

echo "📋 Dados enviados:\n";
echo str_repeat("-", 100) . "\n";
foreach ($registro as $key => $value) {
    $destaque = in_array($key, ['tipo', 'cnae', 'im']) ? ' ⭐ NOVO' : '';
    echo sprintf("  %-20s: %s%s\n", $key, $value, $destaque);
}
echo str_repeat("-", 100) . "\n\n";

// Montar JSON
$json_data = json_encode(['registro' => $registro]);

echo "📦 JSON enviado:\n";
echo $json_data . "\n\n";

// Fazer requisição com autenticação
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, 'api_frontbox:api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($json_data),
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "🔄 Resposta HTTP: $httpcode\n";
echo "=====================================\n\n";

if ($httpcode == 200 || $httpcode == 201) {
    echo "✅ Sucesso!\n\n";
    echo "📄 Resposta:\n";
    echo $response . "\n\n";
    
    $json_response = json_decode($response, true);
    if (isset($json_response['status'])) {
        echo "Status: " . $json_response['status'] . "\n";
    }
    if (isset($json_response['msg'])) {
        echo "Mensagem: " . $json_response['msg'] . "\n";
    }
    if (isset($json_response['data'])) {
        echo "\n📊 Dados salvos:\n";
        $data = $json_response['data'];
        foreach ($data as $key => $value) {
            if (in_array($key, ['tipo', 'cnae', 'im'])) {
                echo sprintf("  %-20s: %s ⭐\n", $key, $value);
            } else {
                echo sprintf("  %-20s: %s\n", $key, $value);
            }
        }
    }
} else {
    echo "❌ Erro na requisição\n";
    echo "HTTP Code: $httpcode\n";
    echo "Error: $error\n";
    echo "Response: $response\n";
}

echo "\n";
?>
