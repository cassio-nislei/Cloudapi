<?php
/**
 * teste_leitura_logs.php
 * Lê os logs da aplicação para verificar o que foi gravado
 */

$logPath = __DIR__ . '/application/logs/';

echo "=== LEITURA DE LOGS DE DEBUG ===\n";
echo "Procurando logs em: $logPath\n\n";

if (!is_dir($logPath)) {
    echo "✗ Pasta de logs não encontrada!\n";
    exit;
}

$files = scandir($logPath);
$logFiles = array_filter($files, function($f) {
    return strpos($f, '.php') === strlen($f) - 4;
});

if (empty($logFiles)) {
    echo "Nenhum arquivo de log encontrado.\n";
    exit;
}

// Ordenar por data mais recente
usort($logFiles, function($a, $b) {
    return filemtime($logPath . $b) - filemtime($logPath . $a);
});

echo "Arquivos de log encontrados:\n";
foreach (array_slice($logFiles, 0, 3) as $idx => $file) {
    $mtime = date('Y-m-d H:i:s', filemtime($logPath . $file));
    echo ($idx + 1) . ". $file (modificado: $mtime)\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "CONTEÚDO DO LOG MAIS RECENTE:\n";
echo str_repeat("=", 70) . "\n\n";

$latestLog = $logPath . $logFiles[0];
$content = file_get_contents($latestLog);

// Procurar por linhas de DEBUG (últimas)
$lines = explode("\n", $content);

echo "Procurando por linhas com 'DEBUG'...\n\n";
$debugLines = [];
foreach ($lines as $idx => $line) {
    if (stripos($line, 'DEBUG') !== false || stripos($line, 'PESSOA_LICENCAS') !== false) {
        $debugLines[$idx] = $line;
    }
}

if (empty($debugLines)) {
    echo "Nenhuma linha de DEBUG encontrada.\n";
    echo "\nÚltimas 30 linhas do log:\n";
    echo str_repeat("-", 70) . "\n";
    $lastLines = array_slice($lines, max(0, count($lines) - 30));
    foreach ($lastLines as $line) {
        if (trim($line)) {
            echo $line . "\n";
        }
    }
} else {
    echo "Encontradas " . count($debugLines) . " linhas de DEBUG:\n\n";
    foreach ($debugLines as $idx => $line) {
        echo $line . "\n";
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "Para ver mais linhas, modifique o script para ler outro arquivo de log.\n";
?>
