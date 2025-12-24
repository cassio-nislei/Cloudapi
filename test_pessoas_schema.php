<?php
$host = '104.234.173.105';
$user = 'root';
$pass = 'Ncm@647534';
$db = 'admCloud';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Erro: " . mysqli_connect_error());

echo "Estrutura da tabela PESSOAS:\n";
$result = mysqli_query($conn, "DESCRIBE PESSOAS");
while ($row = mysqli_fetch_assoc($result)) {
    echo "- {$row['Field']}: {$row['Type']}\n";
}

mysqli_close($conn);
?>
