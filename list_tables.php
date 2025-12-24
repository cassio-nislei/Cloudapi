<?php
$host = '104.234.173.105';
$user = 'root';
$pass = 'Ncm@647534';
$db = 'admCloud';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    echo "Erro: " . mysqli_connect_error();
    exit;
}

echo "Tabelas na base 'admCloud':\n\n";
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    echo "- " . $row[0] . "\n";
}

mysqli_close($conn);
?>
