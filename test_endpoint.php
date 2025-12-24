#!/usr/bin/env php
<?php
/**
 * Simular a chamada ao endpoint /Pessoas/getAll
 */

// Simular uma requisição HTTP para o CodeIgniter
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/Pessoas/getAll';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '8080';
$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
$_SERVER['DOCUMENT_ROOT'] = __DIR__;

// Simular sessão de usuário logado
$_SESSION['logado'] = TRUE;
$_SESSION['id_usuario'] = 1;
$_SESSION['email'] = 'test@test.com';

// Iniciar a sessão
session_start();

// Definir como AJAX para passar no teste
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

// Carregar o CodeIgniter
require_once __DIR__ . '/index.php';

?>
