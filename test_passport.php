<?php
/**
 * Test Passport API - Versão Corrigida
 * CNPJ: 26.578.378/0001-60
 */

$cnpj = '26.578.378/0001-60';
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
    echo "   Status: {$row['ATIVO']}\n\n";
    
    $id_pessoa = $row['ID_PESSOA'];
    $pessoa_found = true;
} else {
    echo "   ✗ CGC não encontrado na tabela PESSOAS\n\n";
    
    // Try to find similar CNPJs
    echo "2. Procurando CNPJs similares...\n";
    $sql = "SELECT CGC, NOME FROM PESSOAS WHERE CGC LIKE '26%' LIMIT 10";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "   Encontrados " . $result->num_rows . " registros começando com '26':\n";
        while ($row = $result->fetch_assoc()) {
            echo "   - CGC: {$row['CGC']}, Nome: {$row['NOME']}\n";
        }
    } else {
        echo "   Nenhum CGC começando com '26' encontrado.\n";
    }
    
    $pessoa_found = false;
}

if ($pessoa_found) {
    // Test 2: Check LICENCAS table
    echo "3. Verificando LICENCAS para este CGC...\n";
    $sql = "SELECT ID_LICENCA, PRODUTO, VERSAO, DATA_INICIO, DATA_FIM, STATUS FROM LICENCAS 
            WHERE ID_PESSOA = $id_pessoa LIMIT 5";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "   ✓ Encontradas " . $result->num_rows . " licenças:\n";
        while ($row = $result->fetch_assoc()) {
            echo "   - Produto: {$row['PRODUTO']}, Versão: {$row['VERSAO']}, Status: {$row['STATUS']}\n";
        }
    } else {
        echo "   ✗ Nenhuma licença encontrada\n";
    }
    echo "\n";
}

// Test 3: Show API endpoint URL
echo "4. Endpoint da API Passport:\n";
$api_base = "http://104.234.173.105:7010";
$api_endpoint = "/Passport/consulta";
$api_url = $api_base . $api_endpoint . "?cgc=" . $cnpj_clean;
echo "   URL: $api_url\n\n";

// Test 4: Statistics
echo "5. Estatísticas gerais:\n";
$sql = "SELECT COUNT(*) as total FROM PESSOAS";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
echo "   Total de registros em PESSOAS: " . $row['total'] . "\n";

$sql = "SELECT COUNT(*) as total FROM LICENCAS";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
echo "   Total de registros em LICENCAS: " . $row['total'] . "\n\n";

// Test 5: Sample data
echo "6. Amostra de dados (primeiros 5 registros com CGC):\n";
$sql = "SELECT CGC, NOME, FANTASIA, ATIVO FROM PESSOAS WHERE CGC IS NOT NULL AND CGC != '' LIMIT 5";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $status = ($row['ATIVO'] == 'S') ? '✓ Ativo' : '✗ Inativo';
    echo "   - CGC: {$row['CGC']}, Nome: {$row['NOME']}, Fantasia: {$row['FANTASIA']} ($status)\n";
}

$conn->close();

echo "\n=== FIM DO TESTE ===\n";
?>
