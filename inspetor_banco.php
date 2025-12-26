<?php
/**
 * Inspeciona a configuração do banco de dados
 */

// Carregar configuração do CodeIgniter
$configFile = dirname(__FILE__) . '/application/config/database.php';

if (!file_exists($configFile)) {
    echo "Arquivo de configuração não encontrado: $configFile\n";
    exit;
}

require_once $configFile;

echo "=== CONFIGURAÇÃO DO BANCO DE DADOS ===\n\n";

if (isset($db)) {
    echo "Banco de dados definido:\n";
    foreach ($db as $group => $config) {
        if ($group === 'default' || $group === 'tests') {
            echo "\nGrupo: $group\n";
            echo "  DBDriver: " . $config['dbdriver'] . "\n";
            echo "  Hostname: " . $config['hostname'] . "\n";
            echo "  Username: " . $config['username'] . "\n";
            echo "  Database: " . $config['database'] . "\n";
            echo "  DBPrefix: " . $config['dbprefix'] . "\n";
            echo "  Auto Connect: " . ($config['autoinit'] ? 'Sim' : 'Não') . "\n";
        }
    }
}

echo "\n\n=== VERIFICANDO ESTRUTURA ESPERADA ===\n\n";

echo "Tabelas esperadas:\n";
echo "  - PESSOA\n";
echo "  - PESSOA_LICENCAS\n";
echo "\nCampos esperados em PESSOA_LICENCAS:\n";
echo "  - ID (PRIMARY KEY)\n";
echo "  - ID_PESSOA (FOREIGN KEY)\n";
echo "  - GUID (UNIQUE?)\n";
echo "  - HOSTNAME\n";
echo "  - CREATED_AT\n";
echo "  - LAST_LOGIN\n";
echo "  - STATUS (opcional)\n";

echo "\n\n=== SUGESTÕES DE DEBUG ===\n\n";

echo "Para investigar o problema, execute no servidor:\n\n";

echo "1. Verificar se tabela existe:\n";
echo "   SELECT * FROM RDB\$RELATIONS WHERE RDB\$RELATION_NAME = 'PESSOA_LICENCAS'\n\n";

echo "2. Listar campos da tabela:\n";
echo "   SELECT RDB\$FIELD_NAME FROM RDB\$RELATION_FIELDS WHERE RDB\$RELATION_NAME = 'PESSOA_LICENCAS'\n\n";

echo "3. Contar registros:\n";
echo "   SELECT COUNT(*) FROM PESSOA_LICENCAS\n\n";

echo "4. Ver últimos registros:\n";
echo "   SELECT * FROM PESSOA_LICENCAS ORDER BY ID DESC ROWS 10\n\n";

echo "5. Verificar espaço em disco:\n";
echo "   Confirmar que há espaço disponível no servidor\n\n";

echo "6. Verificar logs do PHP:\n";
echo "   error_log() escreve em:\n";
if (function_exists('ini_get')) {
    $errorLog = ini_get('error_log');
    echo "   " . ($errorLog ? $errorLog : "Padrão do sistema") . "\n";
}
?>
