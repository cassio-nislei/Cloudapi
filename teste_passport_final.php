<?php
/**
 * teste_passport_final.php
 * Testa o endpoint /api/passport com GET (como a classe Delphi faz)
 */

echo "=== TESTE FINAL DO PASSPORT (Via GET como Delphi) ===\n\n";

$cgc = '92702067000181';
$hostname = 'FINAL-HOST-' . time();
$guid = 'FINAL-GUID-' . time();

$url = 'http://104.234.173.105:7010/api/passport';

echo "Parâmetros:\n";
echo "  CGC: $cgc\n";
echo "  Hostname: $hostname\n";
echo "  GUID: $guid\n\n";

echo "URL chamada:\n";
echo "  " . $url . "?cgc=" . $cgc . "&hostname=" . $hostname . "&guid=" . $guid . "\n\n";

// Tentar acessar via simples file_get_contents
echo "Tentando acessar via file_get_contents...\n";

$fullUrl = $url . "?cgc=" . $cgc . "&hostname=" . $hostname . "&guid=" . $guid;

// Configurar context para ignorar SSL e seguir redirects
$options = [
    'http' => [
        'method' => 'GET',
        'timeout' => 30,
        'ignore_errors' => true,
    ]
];

$context = stream_context_create($options);

try {
    $response = @file_get_contents($fullUrl, false, $context);
    
    if ($response === false) {
        echo "✗ Não foi possível acessar a URL\n";
        echo "  Possível causa: Servidor não está respondendo\n";
    } else {
        echo "✓ Resposta recebida:\n";
        echo "  " . $response . "\n\n";
        
        // Decodificar JSON
        $data = json_decode($response, true);
        if ($data) {
            echo "Dados decodificados:\n";
            echo "  Status: " . ($data['Status'] ? 'TRUE' : 'FALSE') . "\n";
            echo "  Mensagem: " . $data['Mensagem'] . "\n\n";
            
            if ($data['Status']) {
                echo "✓ PASSPORT VALIDADO COM SUCESSO!\n\n";
                
                // Agora verificar se foi gravado no banco
                echo "Verificando se licença foi gravada no banco...\n";
                
                $dsn = "mysql:host=104.234.173.105;dbname=admCloud;charset=utf8";
                $pdo = new PDO($dsn, 'root', 'Ncm@647534', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                
                // Buscar o registro
                $stmt = $pdo->prepare("SELECT * FROM PESSOA_LICENCAS WHERE GUID = ? LIMIT 1");
                $stmt->execute([$guid]);
                $licenca = $stmt->fetch();
                
                if ($licenca) {
                    echo "✓ LICENÇA FOI GRAVADA!\n";
                    echo "  ID: " . $licenca['ID'] . "\n";
                    echo "  ID_PESSOA: " . $licenca['ID_PESSOA'] . "\n";
                    echo "  HOSTNAME: " . $licenca['HOSTNAME'] . "\n";
                    echo "  GUID: " . $licenca['GUID'] . "\n";
                    echo "  CREATED_AT: " . $licenca['CREATED_AT'] . "\n";
                    echo "  LAST_LOGIN: " . $licenca['LAST_LOGIN'] . "\n";
                } else {
                    echo "✗ LICENÇA NÃO FOI GRAVADA\n";
                }
            }
        }
    }
} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
?>
