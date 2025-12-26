<?php
/**
 * Teste do Endpoint FrontBox - Validar retorno de CEP
 * Verifica se o CEP agora está sendo retornado corretamente
 */

$baseURL = 'http://localhost';
// Para produção, use: $baseURL = 'https://admcloud.papion.com.br';

// Dados de teste - use um CNPJ válido que exista no seu banco
$cgc_teste = '92702067000196';  // Altere para um CNPJ válido
$endpoint = '/api/frontbox/getInfo?q=' . $cgc_teste;
$url = $baseURL . $endpoint;

echo "=" . str_repeat("=", 100) . "\n";
echo "TESTE: Validação de CEP no Endpoint FrontBox\n";
echo "=" . str_repeat("=", 100) . "\n\n";

echo "📍 URL: $url\n";
echo "📊 Método: GET\n\n";

// Fazer requisição
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "🔄 Resposta HTTP: $httpcode\n";
echo "=====================================\n\n";

if ($httpcode == 200 || $httpcode == 0) {
    echo "✅ Resposta Bruta:\n";
    echo "$response\n\n";
    
    // Parse da resposta (formato custom XML)
    echo "📋 Campos Extraídos:\n";
    echo str_repeat("-", 100) . "\n";
    
    // Usar regex para extrair todos os campos
    $campos = [];
    $pattern = '/{([^}]+)}([^{]*){\/\1}/';
    
    if (preg_match_all($pattern, $response, $matches)) {
        for ($i = 0; $i < count($matches[1]); $i++) {
            $campo = $matches[1][$i];
            $valor = $matches[2][$i];
            $campos[$campo] = $valor;
            
            // Destaca o CEP
            $destaque = ($campo === 'cep') ? ' ✅ CEP PRESENTE!' : '';
            echo sprintf("  %-20s: %s%s\n", $campo, $valor, $destaque);
        }
    }
    
    echo str_repeat("-", 100) . "\n\n";
    
    // Validar se CEP está presente
    if (isset($campos['cep'])) {
        echo "✅ SUCESSO: CEP está sendo retornado!\n";
        echo "   Valor: " . $campos['cep'] . "\n";
    } else {
        echo "❌ ERRO: CEP NÃO está sendo retornado!\n";
    }
    
} else {
    echo "❌ Erro na requisição\n";
    echo "HTTP Code: $httpcode\n";
    echo "Error: $error\n";
    echo "Response: $response\n";
}

echo "\n";
?>
