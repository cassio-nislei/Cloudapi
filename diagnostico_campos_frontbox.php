<?php
/**
 * Diagnóstico: Verificar se os Campos Existem na Tabela PESSOAS
 * Verifica a estrutura da tabela e se CNAE, IM e TIPO existem
 */

// Configuração de conexão
$host = '104.234.173.105';
$user = 'root';
$pass = 'Ncm@647534';
$db = 'admCloud';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("❌ Erro de conexão: " . mysqli_connect_error());
}

echo "=" . str_repeat("=", 100) . "\n";
echo "DIAGNÓSTICO: Estrutura da Tabela PESSOAS\n";
echo "=" . str_repeat("=", 100) . "\n\n";

// 1. Verificar se tabela existe
echo "1️⃣ Verificando existência da tabela PESSOAS...\n";
$result = mysqli_query($conn, "SHOW TABLES LIKE 'PESSOAS'");
if (mysqli_num_rows($result) > 0) {
    echo "✅ Tabela PESSOAS existe\n\n";
} else {
    echo "❌ Tabela PESSOAS NÃO existe\n";
    mysqli_close($conn);
    exit;
}

// 2. Listar todos os campos
echo "2️⃣ Listando todos os campos da tabela PESSOAS:\n";
echo str_repeat("-", 100) . "\n";
$result = mysqli_query($conn, "DESCRIBE PESSOAS");
$campos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $campos[] = $row['Field'];
    echo sprintf("%-30s | %-20s | Null: %-3s | Key: %-3s\n", 
        $row['Field'], $row['Type'], $row['Null'], $row['Key']);
}
echo "\n";

// 3. Verificar campos específicos para FrontBox
echo "3️⃣ Verificação dos Novos Campos para FrontBox:\n";
echo str_repeat("-", 100) . "\n";

$campos_novos = [
    'CIDADE' => 'NOVO (retornado na resposta)',
    'ESTADO' => 'NOVO (retornado na resposta)',
    'CNAE' => 'NOVO (retornado na resposta)',
    'IM' => 'NOVO (retornado na resposta)',
    'TIPO' => 'NOVO (retornado na resposta)'
];

$campos_faltando = [];

foreach ($campos_novos as $campo => $descricao) {
    if (in_array($campo, $campos)) {
        echo "✅ $campo\n";
    } else {
        echo "❌ $campo - NÃO ENCONTRADO ($descricao)\n";
        $campos_faltando[] = $campo;
    }
}

echo "\n";

// 4. Amostra de dados
echo "4️⃣ Amostra de Dados (primeira pessoa com CNPJ):\n";
echo str_repeat("-", 100) . "\n";

$result = mysqli_query($conn, 
    "SELECT NOME, FANTASIA, CGC, CIDADE, ESTADO, EMAIL, IE " .
    "FROM PESSOAS " .
    "WHERE CGC IS NOT NULL AND CGC != '' " .
    "LIMIT 1"
);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo "Nome: " . $row['NOME'] . "\n";
    echo "Fantasia: " . $row['FANTASIA'] . "\n";
    echo "CNPJ: " . $row['CGC'] . "\n";
    echo "Cidade: " . $row['CIDADE'] . "\n";
    echo "Estado: " . $row['ESTADO'] . "\n";
    echo "E-mail: " . $row['EMAIL'] . "\n";
    echo "IE: " . $row['IE'] . "\n";
    
    // Verificar campos novos
    if (!in_array('CNAE', $campos)) {
        echo "⚠️ Campo CNAE não existe\n";
    }
    if (!in_array('IM', $campos)) {
        echo "⚠️ Campo IM não existe\n";
    }
    if (!in_array('TIPO', $campos)) {
        echo "⚠️ Campo TIPO não existe\n";
    }
} else {
    echo "⚠️ Nenhuma pessoa encontrada com CNPJ preenchido\n";
}

echo "\n";

// 5. Recomendações
echo "5️⃣ Recomendações:\n";
echo str_repeat("-", 100) . "\n";

if (count($campos_faltando) > 0) {
    echo "⚠️ Os seguintes campos precisam ser criados:\n\n";
    
    $sql_alter = "ALTER TABLE PESSOAS ADD COLUMN IF NOT EXISTS ";
    
    foreach ($campos_faltando as $campo) {
        $type = match($campo) {
            'CNAE' => 'VARCHAR(10) DEFAULT \'\'',
            'IM' => 'VARCHAR(15) DEFAULT \'\'',
            'TIPO' => 'VARCHAR(2) DEFAULT \'\'',
            default => 'VARCHAR(255) DEFAULT \'\''
        };
        
        echo "-- Executar este comando SQL:\n";
        echo "ALTER TABLE PESSOAS ADD COLUMN IF NOT EXISTS $campo $type;\n\n";
    }
    
    echo "📝 SQL Consolidado para Criar todos os Campos Faltando:\n";
    echo str_repeat("-", 100) . "\n";
    
    foreach ($campos_faltando as $campo) {
        $type = match($campo) {
            'CNAE' => 'VARCHAR(10) DEFAULT \'\'',
            'IM' => 'VARCHAR(15) DEFAULT \'\'',
            'TIPO' => 'VARCHAR(2) DEFAULT \'\'',
            default => 'VARCHAR(255) DEFAULT \'\''
        };
        
        echo "ALTER TABLE PESSOAS ADD COLUMN IF NOT EXISTS $campo $type;\n";
    }
    
} else {
    echo "✅ Todos os campos necessários já existem na tabela!\n";
}

mysqli_close($conn);

echo "\n" . str_repeat("=", 100) . "\n";
?>
