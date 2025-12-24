#!/usr/bin/env php
<?php
/**
 * Teste Final: Simula exatamente o que o endpoint /Pessoas/getAll deveria retornar
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

// Conexão direta ao banco
$host = '104.234.173.105';
$user = 'root';
$pass = 'Ncm@647534';
$db = 'admCloud';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    $response = [
        'status' => FALSE,
        'msg' => 'Erro na conexão: ' . mysqli_connect_error(),
        'data' => []
    ];
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode($response);
    exit;
}

// Simular getAll() exato do controller
$status = FALSE;
$msg = NULL;
$data = [];

try {
    $registros = mysqli_query($conn, "SELECT * FROM PESSOAS ORDER BY NOME");
    
    if (!$registros) {
        throw new Exception("Erro na query: " . mysqli_error($conn));
    }
    
    $num_rows = mysqli_num_rows($registros);
    
    if ($num_rows > 0) {
        $status = TRUE;
        $msg = "Registros encontrados: $num_rows";
        
        // Processar registros e adicionar campo 'status'
        while ($row = mysqli_fetch_object($registros)) {
            $row->status = $row->ATIVO === 'S' ? 'Ativo' : 'Desativado';
            $data[] = $row;
        }
    } else {
        throw new Exception("Nenhum registro encontrado.");
    }
    
} catch (Exception $ex) {
    $status = FALSE;
    $msg = $ex->getMessage();
    $data = [];
}

mysqli_close($conn);

// Retornar exatamente como o controlador retorna
header('Content-Type: application/json');
http_response_code($status ? 200 : 400);

$response = [
    'status' => $status,
    'msg' => $msg,
    'data' => $data
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
