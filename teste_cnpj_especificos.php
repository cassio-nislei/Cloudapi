<?php
/**
 * Teste do endpoint /api/pessoas com CNPJs específicos
 * CNPJs a testar: 92702067000196 e 19788379000174
 * Usando file_get_contents em vez de cURL
 */

$baseURL = 'https://admcloud.papion.com.br/v1/';
$token = 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg';

// CNPJs a testar
$cnpjs_teste = [
    '92702067000196',      // Sem formatação
    '92.702.067/0001-96',  // Com formatação
    '19788379000174',      // Sem formatação
    '19.788.379/0001-74',  // Com formatação
];

function testarCNPJ($cnpj, $baseURL, $token) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "TESTANDO CNPJ: $cnpj\n";
    echo str_repeat("=", 80) . "\n";
    
    // Teste 1: Sem autenticação
    echo "\n✓ Teste 1: SEM Autenticação\n";
    echo str_repeat("-", 80) . "\n";
    
    $url = $baseURL . 'api/pessoas?cnpj=' . urlencode($cnpj);
    echo "URL: $url\n\n";
    
    try {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'header' => "Content-Type: application/json\r\nAccept: application/json\r\n"
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $json = json_decode($response, true);
            if ($json) {
                echo "✅ Resposta JSON recebida:\n";
                echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
                
                if ($json['status'] === true) {
                    echo "✅ STATUS: ENCONTRADO\n";
                    if (isset($json['data']['ID_PESSOA'])) {
                        echo "   ID_PESSOA: " . $json['data']['ID_PESSOA'] . "\n";
                        echo "   NOME: " . $json['data']['NOME'] . "\n";
                        echo "   CGC: " . $json['data']['CGC'] . "\n";
                        echo "   ATIVO: " . (isset($json['data']['ATIVO']) ? $json['data']['ATIVO'] : 'N/A') . "\n";
                    }
                } else {
                    echo "❌ STATUS: NÃO ENCONTRADO\n";
                    echo "   Mensagem: " . $json['msg'] . "\n";
                }
            } else {
                echo "❌ Response não é JSON válido:\n";
                echo $response . "\n";
            }
        } else {
            echo "❌ Erro ao conectar: " . error_get_last()['message'] . "\n";
        }
    } catch (Exception $e) {
        echo "❌ Exceção: " . $e->getMessage() . "\n";
    }
    
    // Teste 2: Com autenticação
    echo "\n✓ Teste 2: COM Autenticação (Bearer Token)\n";
    echo str_repeat("-", 80) . "\n";
    
    $url = $baseURL . 'api/pessoas?cnpj=' . urlencode($cnpj);
    echo "URL: $url\n";
    echo "Header: Authorization: Bearer {token}\n\n";
    
    try {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'header' => "Content-Type: application/json\r\nAccept: application/json\r\nAuthorization: Bearer " . $token . "\r\n"
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $json = json_decode($response, true);
            if ($json) {
                echo "✅ Resposta JSON recebida:\n";
                echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
                
                if ($json['status'] === true) {
                    echo "✅ STATUS: ENCONTRADO\n";
                    if (isset($json['data']['ID_PESSOA'])) {
                        echo "   ID_PESSOA: " . $json['data']['ID_PESSOA'] . "\n";
                        echo "   NOME: " . $json['data']['NOME'] . "\n";
                        echo "   CGC: " . $json['data']['CGC'] . "\n";
                        echo "   ATIVO: " . (isset($json['data']['ATIVO']) ? $json['data']['ATIVO'] : 'N/A') . "\n";
                    }
                } else {
                    echo "❌ STATUS: NÃO ENCONTRADO\n";
                    echo "   Mensagem: " . $json['msg'] . "\n";
                }
            } else {
                echo "❌ Response não é JSON válido:\n";
                echo $response . "\n";
            }
        } else {
            echo "❌ Erro ao conectar: " . error_get_last()['message'] . "\n";
        }
    } catch (Exception $e) {
        echo "❌ Exceção: " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "╔" . str_repeat("=", 78) . "╗\n";
echo "║" . str_pad("TESTE DOS CNPJs 92702067000196 e 19788379000174", 78) . "║\n";
echo "║" . str_pad("Endpoint: /api/pessoas", 78) . "║\n";
echo "╚" . str_repeat("=", 78) . "╝\n";

foreach ($cnpjs_teste as $cnpj) {
    testarCNPJ($cnpj, $baseURL, $token);
}

echo "\n\n";
echo "╔" . str_repeat("=", 78) . "╗\n";
echo "║" . str_pad("RESULTADO DOS TESTES", 78) . "║\n";
echo "╚" . str_repeat("=", 78) . "╝\n";

echo "\n⚠️  NOTA IMPORTANTE:\n";
echo "━" . str_repeat("━", 76) . "━\n";
echo "\nHTTP 404 significa que o controlador Api.php ainda NÃO foi\n";
echo "implantado no servidor em produção.\n";
echo "\n✅ PRÓXIMO PASSO:\n";
echo "   Copiar o arquivo Api.php para a pasta:\n";
echo "   /application/controllers/\n";
echo "   no servidor em produção (https://admcloud.papion.com.br/v1/)\n";
echo "\n";
echo "📋 PROCEDIMENTO:\n";
echo "   1. SSH ou FTP para o servidor\n";
echo "   2. Localizar: /application/controllers/\n";
echo "   3. Copiar: Api.php (do arquivo criado)\n";
echo "   4. Testar novamente este script\n";
echo "\n";
echo "━" . str_repeat("━", 76) . "━\n";
echo "\n";
?>
