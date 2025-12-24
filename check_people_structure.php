<?php
// Check PESSOAS table structure
$servername = "104.234.173.105";
$username = "root";
$password = "Ncm@647534";
$dbname = "admCloud";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

echo "=== ESTRUTURA DA TABELA PESSOAS ===\n\n";

$sql = "DESCRIBE PESSOAS";
$result = $conn->query($sql);

$columns = array();
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n=== COLUNAS DISPONÍVEIS ===\n";
echo implode(", ", $columns) . "\n";

// Now search for CNPJ
echo "\n=== PROCURANDO CNPJ 26578378000160 ===\n";

$cnpj = '26578378000160';

// Try to find it in any text column
foreach ($columns as $col) {
    if (strpos(strtolower($col), 'cnpj') !== false || strpos(strtolower($col), 'cpf') !== false) {
        $sql = "SELECT * FROM PESSOAS WHERE $col = '$cnpj' LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "Encontrado em coluna: $col\n";
            $row = $result->fetch_assoc();
            print_r($row);
            break;
        }
    }
}

$conn->close();
?>
