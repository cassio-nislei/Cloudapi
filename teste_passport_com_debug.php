<?php
/**
 * teste_passport_com_debug.php
 * Teste do Passport endpoint com captura de logs de debug
 */

$cgc = '92702067000181'; // CNPJ teste
$hostname = 'TEST-HOST-' . time();
$guid = 'TEST-GUID-' . time();

$url = 'http://104.234.173.105:7010/api/passport';
$auth = 'api_frontbox:api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg';

echo "=== TESTE PASSPORT COM DEBUG ===\n";
echo "URL: $url\n";
echo "CNPJ: $cgc\n";
echo "Hostname: $hostname\n";
echo "GUID: $guid\n";
echo "\n";

// Fazer requisição
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . '?cgc=' . $cgc . '&hostname=' . $hostname . '&guid=' . $guid);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $auth);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);

// Capturar logs verbosos
$verbose = fopen('php://temp', 'r+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

rewind($verbose);
$verboseLog = stream_get_contents($verbose);

echo "HTTP Code: $httpcode\n";
echo "Response: $response\n\n";

echo "=== LOGS CURL ===\n";
echo $verboseLog . "\n";

curl_close($ch);

// Agora verificar se foi inserido
echo "\n=== VERIFICAÇÃO NO BANCO ===\n";

// Tentar ler logs da aplicação
$logPath = 'application/logs/';
if (is_dir($logPath)) {
    $files = scandir($logPath);
    rsort($files); // Mais recentes primeiro
    
    if (!empty($files) && $files[0] !== '.' && $files[0] !== '..') {
        $latestLog = $logPath . $files[0];
        echo "Lendo arquivo de log mais recente: " . basename($latestLog) . "\n\n";
        
        $content = file_get_contents($latestLog);
        
        // Procurar por linhas de DEBUG
        $lines = explode("\n", $content);
        echo "Últimas linhas com DEBUG:\n";
        foreach (array_reverse($lines) as $line) {
            if (strpos($line, 'DEBUG') !== false) {
                echo $line . "\n";
            }
        }
    }
} else {
    echo "Pasta de logs não encontrada em: $logPath\n";
}

echo "\nAguarde alguns segundos e execute novamente para ver logs atualizados.\n";
?>
