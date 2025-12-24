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

echo "=== TESTE PESSOAS ===\n\n";

// 1. Contar registros
echo "1. Total de registros na tabela PESSOAS:\n";
$result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM PESSOAS");
$row = mysqli_fetch_assoc($result);
echo "   " . $row['cnt'] . " registros\n\n";

// 2. Mostrar campo ATIVO
echo "2. Distribuição do campo ATIVO:\n";
$result = mysqli_query($conn, "SELECT ATIVO, COUNT(*) as cnt FROM PESSOAS GROUP BY ATIVO");
while ($row = mysqli_fetch_assoc($result)) {
    $status = $row['ATIVO'] === 'S' ? 'Ativo' : 'Desativado';
    echo "   $status: " . $row['cnt'] . "\n";
}
echo "\n";

// 3. Amostra de dados
echo "3. Primeiros 5 registros PESSOAS:\n";
$result = mysqli_query($conn, "SELECT ID_PESSOA, NOME, CGC, ATIVO FROM PESSOAS LIMIT 5");
while ($row = mysqli_fetch_assoc($result)) {
    $status = $row['ATIVO'] === 'S' ? 'Ativo' : 'Desativado';
    echo "   ID: {$row['ID_PESSOA']}, Nome: {$row['NOME']}, ATIVO: {$row['ATIVO']} ($status)\n";
}
echo "\n";

// 4. Verificar usuário criado
echo "4. Usuários ADM:\n";
$result = mysqli_query($conn, "SELECT id, email, sysop FROM adm_usuarios LIMIT 5");
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "   {$row['email']} (Admin: {$row['sysop']})\n";
    }
} else {
    echo "   Nenhum usuário encontrado\n";
}

// 5. Simulando o que getAll() deveria fazer
echo "\n5. Simulando resposta JSON do getAll():\n";
$result = mysqli_query($conn, "SELECT ID_PESSOA as id, NOME as nome, CGC as cgc, ATIVO as ativo FROM PESSOAS LIMIT 3");
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['status'] = $row['ativo'] === 'S' ? 'Ativo' : 'Desativado';
    $data[] = $row;
}
echo json_encode(['status' => true, 'msg' => 'Registros encontrados: ' . count($data), 'data' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

mysqli_close($conn);
?>
