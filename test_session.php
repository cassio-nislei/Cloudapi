<?php
/**
 * Script de teste de sessão
 */

session_start();

echo "=== Teste de Sessão ===\n\n";

echo "PHP Session Status:\n";
echo "  session_id(): " . session_id() . "\n";
echo "  session_status(): " . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE') . "\n";
echo "  session_save_path(): " . session_save_path() . "\n\n";

// Simular login
$_SESSION['logado'] = TRUE;
$_SESSION['user.ID'] = 1;
$_SESSION['user.email'] = 'admin@admcloud.com.br';
$_SESSION['user.nome'] = 'Admin';

echo "Session data setada:\n";
foreach ($_SESSION as $key => $value) {
    echo "  $_SESSION['$key'] = ";
    if (is_array($value)) {
        echo "[Array]\n";
    } else if (is_object($value)) {
        echo "[Object]\n";
    } else {
        echo "'$value'\n";
    }
}

echo "\nVerificando logado: ";
if ($_SESSION['logado'] === TRUE) {
    echo "✓ Sessão válida\n";
} else {
    echo "✗ Sessão inválida\n";
}

?>
