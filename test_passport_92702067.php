<?php
/**
 * Test Passport API
 * CNPJ: 92.702.067/0001-96
 */

$cnpj = '92.702.067/0001-96';
$cnpj_clean = preg_replace('/[^0-9]/', '', $cnpj);

echo "=== TESTE PASSPORT API ===\n";
echo "CNPJ Original: $cnpj\n";
echo "CNPJ Limpo: $cnpj_clean\n\n";

// Database connection
$servername = "104.234.173.105";
$username = "root";
$password = "Ncm@647534";
$dbname = "admCloud";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Test 1: Check if CNPJ exists in PESSOAS table (coluna CGC)
echo "1. Verificando CGC na tabela PESSOAS...\n";
$sql = "SELECT ID_PESSOA, NOME, FANTASIA, CGC, ATIVO FROM PESSOAS WHERE CGC = '$cnpj_clean' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "   ✓ Encontrado!\n";
    echo "   ID: {$row['ID_PESSOA']}\n";
    echo "   Nome: {$row['NOME']}\n";
    echo "   Fantasia: {$row['FANTASIA']}\n";
    echo "   CGC: {$row['CGC']}\n";
    echo "   Status: " . ($row['ATIVO'] == 'S' ? '✓ Ativo' : '✗ Inativo') . "\n\n";
    
    $id_pessoa = $row['ID_PESSOA'];
    $pessoa_found = true;
    $pessoa = $row;
} else {
    echo "   ✗ CGC não encontrado na tabela PESSOAS\n\n";
    $pessoa_found = false;
}

if ($pessoa_found) {
    // Test 2: Check PESSOA_LICENCAS table (acesso/dispositivos)
    echo "2. Verificando acessos registrados (PESSOA_LICENCAS)...\n";
    
    $sql = "SELECT ID, HOSTNAME, GUID, STATUS, CREATED_AT, LAST_LOGIN FROM PESSOA_LICENCAS 
            WHERE ID_PESSOA = $id_pessoa LIMIT 5";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "   ✓ Encontrados " . $result->num_rows . " dispositivos/acessos:\n";
        while ($row = $result->fetch_assoc()) {
            echo "   - Hostname: {$row['HOSTNAME']}, GUID: {$row['GUID']}, Status: {$row['STATUS']}\n";
            echo "     Criado: {$row['CREATED_AT']}, Último Acesso: {$row['LAST_LOGIN']}\n";
        }
    } else {
        echo "   ⚠ Nenhum dispositivo/acesso registrado\n";
    }
    echo "\n";
}

// Test 3: Show API endpoint URL
echo "3. Endpoint da API Passport:\n";
$api_base = "http://104.234.173.105:7010";
$api_endpoint = "/Passport/consulta";
$api_url = $api_base . $api_endpoint . "?cgc=" . $cnpj_clean;
echo "   URL: $api_url\n\n";

// Test 4: Statistics
echo "4. Estatísticas gerais:\n";
$sql = "SELECT COUNT(*) as total FROM PESSOAS";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
echo "   Total de registros em PESSOAS: " . $row['total'] . "\n";

$sql = "SELECT COUNT(*) as total FROM PESSOA_LICENCAS";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
echo "   Total de registros em PESSOA_LICENCAS (acessos): " . $row['total'] . "\n\n";

// Test 5: Sample data
echo "5. Amostra de dados (primeiros 5 registros com CGC):\n";
$sql = "SELECT CGC, NOME, FANTASIA, ATIVO FROM PESSOAS WHERE CGC IS NOT NULL AND CGC != '' LIMIT 5";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $status = ($row['ATIVO'] == 'S') ? '✓ Ativo' : '✗ Inativo';
    echo "   - CGC: {$row['CGC']}, Nome: {$row['NOME']}, Fantasia: {$row['FANTASIA']} ($status)\n";
}

echo "\n=== Resumo da Consulta Passport ===\n";
if ($pessoa_found) {
    echo "JSON esperado da API:\n";
    $response = array(
        'status' => true,
        'msg' => 'Empresa encontrada',
        'data' => array(
            'id_pessoa' => $pessoa['ID_PESSOA'],
            'nome' => $pessoa['NOME'],
            'fantasia' => $pessoa['FANTASIA'] ?: 'N/A',
            'cgc' => $cnpj_clean,
            'ativo' => $pessoa['ATIVO']
        )
    );
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo "CNPJ não encontrado no banco de dados.\n";
}

$conn->close();

echo "\n\n=== FIM DO TESTE ===\n";
?>
