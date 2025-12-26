<?php
/**
 * Teste do Endpoint FrontBox com os Novos Campos
 * Verifica se CIDADE, ESTADO, CNAE, IM e TIPO estão sendo retornados
 */

$baseURL = 'http://localhost';
// Para produção, use: $baseURL = 'https://admcloud.papion.com.br';

// Dados de teste - use um CNPJ válido que exista no seu banco
$cgc_teste = '92702067000196';  // Altere para um CNPJ válido
$endpoint = '/api/frontbox/getInfo?q=' . $cgc_teste;
$url = $baseURL . $endpoint;

echo "=" . str_repeat("=", 100) . "\n";
echo "TESTE: Endpoint FrontBox com Novos Campos\n";
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
    
    preg_match_all('/{([^}]+)}([^{]*){\//', $response, $matches);
    
    if (count($matches[1]) > 0) {
        $campos_esperados = [
            'status', 'nome', 'fantasia', 'endereco', 'complemento',
            'cgc', 'ie', 'telefone', 'numero', 'bairro',
            'cidade', 'estado', 'cnae', 'im', 'tipo', 'email'
        ];
        
        $campos_encontrados = [];
        
        for ($i = 0; $i < count($matches[1]); $i++) {
            $campo = $matches[1][$i];
            $valor = $matches[2][$i];
            $campos_encontrados[] = $campo;
            
            // Destacar novos campos
            $novo = in_array($campo, ['cidade', 'estado', 'cnae', 'im', 'tipo']) ? ' ✅ NOVO' : '';
            
            echo sprintf("%-20s : %s%s\n", $campo, $valor, $novo);
        }
        
        echo "\n" . str_repeat("-", 100) . "\n";
        echo "📊 Verificação de Campos Novos:\n";
        echo str_repeat("-", 100) . "\n";
        
        $novos_campos = ['cidade', 'estado', 'cnae', 'im', 'tipo'];
        
        foreach ($novos_campos as $campo) {
            if (in_array($campo, $campos_encontrados)) {
                echo "✅ $campo está sendo retornado\n";
            } else {
                echo "❌ $campo NÃO está sendo retornado\n";
            }
        }
        
        echo "\n" . str_repeat("-", 100) . "\n";
        echo "📈 Resumo:\n";
        echo str_repeat("-", 100) . "\n";
        echo "Total de campos encontrados: " . count($campos_encontrados) . "\n";
        echo "Total de campos novos: " . count(array_intersect($novos_campos, $campos_encontrados)) . " de 5\n";
        
    } else {
        echo "⚠️ Não foi possível fazer parse da resposta\n";
    }
    
} else {
    echo "❌ Erro na Requisição:\n";
    echo "HTTP Code: $httpcode\n";
    echo "Erro: $error\n";
    echo "Resposta: $response\n";
}

echo "\n" . str_repeat("=", 100) . "\n";
echo "🔧 Próximas Etapas:\n";
echo "1. Confirmar que os dados CIDADE, ESTADO, CNAE, IM e TIPO existem na tabela PESSOAS\n";
echo "2. Verificar se o FrontBox está preenchendo esses campos ao registrar/atualizar empresas\n";
echo "3. Se os campos não aparecerem na resposta, verificar se estão no objeto \$pessoa\n";
echo str_repeat("=", 100) . "\n";
?>
