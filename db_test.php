<?php
// Teste simples de conexão - sem CodeIgniter

echo "=== TESTE DE BANCO DE DADOS ===\n\n";

// Dados hardcoded da config
$host = '104.234.173.105';
$user = 'root';
$pass = 'Ncm@647534';
$db = 'admCloud';

echo "Conectando a: $host / $db...\n";

$conn = @mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    echo "ERRO: " . mysqli_connect_error() . "\n";
    exit(1);
}

echo "✓ Conectado\n\n";

// Testar PESSOAS
echo "Contando PESSOAS:\n";
$result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM PESSOAS");
$row = mysqli_fetch_assoc($result);
echo "Total: " . $row['cnt'] . " registros\n\n";

// Mostrar alguns
echo "Amostra de dados:\n";
$result = mysqli_query($conn, "SELECT ID_PESSOA, NOME, CGC, ATIVO FROM PESSOAS LIMIT 3");
while ($row = mysqli_fetch_assoc($result)) {
    echo "- ID: {$row['ID_PESSOA']}, Nome: {$row['NOME']}, CGC: {$row['CGC']}, Ativo: {$row['ATIVO']}\n";
}

echo "\nUsuários ADM:\n";
$result = mysqli_query($conn, "SELECT EMAIL, SYSOP FROM ADM_USUARIOS");
while ($row = mysqli_fetch_assoc($result)) {
    echo "- {$row['EMAIL']} (Admin: {$row['SYSOP']})\n";
}

mysqli_close($conn);
echo "\n✓ Concluído\n";
?>
