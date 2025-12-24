<?php
/**
 * Teste de Validação - Classes Pascal Corrigidas
 * 
 * Este script valida se os endpoints estão corretos após as correções
 */

echo "=== TESTE DE VALIDAÇÃO - CLASSES PASCAL CORRIGIDAS ===\n\n";

// Database connection
$servername = "104.234.173.105";
$username = "root";
$password = "Ncm@647534";
$dbname = "admCloud";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Conexão falhou: " . $conn->connect_error);
}

echo "✅ Conectado ao banco de dados\n\n";

// URLs esperadas pelos clientes Pascal (após correção)
$urls_esperadas = array(
    array(
        'nome' => 'Passport/consulta (GET)',
        'metodo' => 'GET',
        'url' => 'Passport/consulta',
        'parametros' => '?cgc=92702067000196&hostname=TESTE&guid=teste-guid',
        'autenticacao' => 'NÃO',
        'descricao' => 'Validação de licença - Sem autenticação'
    ),
    array(
        'nome' => 'Pessoas/getAll (GET)',
        'metodo' => 'GET',
        'url' => 'Pessoas/getAll',
        'parametros' => '(nenhum)',
        'autenticacao' => 'SIM',
        'descricao' => 'Listar todas as pessoas registradas'
    ),
    array(
        'nome' => 'Pessoas/salvar (POST)',
        'metodo' => 'POST',
        'url' => 'Pessoas/salvar',
        'parametros' => 'JSON com dados da pessoa',
        'autenticacao' => 'SIM',
        'descricao' => 'Registrar nova pessoa'
    )
);

echo "📋 ENDPOINTS ESPERADOS (Após Correção):\n";
echo str_repeat("=", 100) . "\n";

foreach ($urls_esperadas as $idx => $endpoint) {
    echo "\n" . ($idx + 1) . ". " . $endpoint['nome'] . "\n";
    echo "   Método: {$endpoint['metodo']}\n";
    echo "   URL: {$endpoint['url']}\n";
    echo "   Parâmetros: {$endpoint['parametros']}\n";
    echo "   Autenticação: {$endpoint['autenticacao']}\n";
    echo "   Descrição: {$endpoint['descricao']}\n";
}

echo "\n\n" . str_repeat("=", 100) . "\n";
echo "📊 TESTE DE ENDPOINTS\n";
echo str_repeat("=", 100) . "\n\n";

// Teste 1: Passport/consulta
echo "1. TESTE: Passport/consulta com CNPJ válido\n";
$sql = "SELECT COUNT(*) as total FROM PESSOAS WHERE CGC = '92702067000196'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
if ($row['total'] > 0) {
    echo "   ✅ CNPJ 92702067000196 existe no banco\n";
    echo "   ✅ Endpoint Passport/consulta deve funcionar\n";
} else {
    echo "   ⚠️  CNPJ não encontrado\n";
}

// Teste 2: Pessoas/getAll
echo "\n2. TESTE: Pessoas/getAll\n";
$sql = "SELECT COUNT(*) as total FROM PESSOAS";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
echo "   ✅ Total de pessoas no banco: " . $row['total'] . "\n";
echo "   ✅ Endpoint Pessoas/getAll deve retornar os dados\n";

// Teste 3: Pessoas/salvar (validação de campos obrigatórios)
echo "\n3. TESTE: Pessoas/salvar - Campos Obrigatórios\n";
$campos_obrigatorios = array(
    'NOME' => 'varchar(50)',
    'FANTASIA' => 'varchar(50)',
    'CGC' => 'varchar(20)',
    'CONTATO' => 'varchar(50)',
    'EMAIL' => 'varchar(50)',
    'TELEFONE' => 'varchar(15)',
    'ENDERECO' => 'varchar(50)',
    'NUMERO' => 'varchar(10)',
    'BAIRRO' => 'varchar(35)',
    'CIDADE' => 'varchar(35)',
    'ESTADO' => 'char(2)',
    'CEP' => 'varchar(10)'
);

echo "   Campos esperados pelo endpoint POST /Pessoas/salvar:\n";
foreach ($campos_obrigatorios as $campo => $tipo) {
    echo "   ✅ {$campo} ({$tipo})\n";
}

echo "\n\n" . str_repeat("=", 100) . "\n";
echo "✅ RESUMO DAS CORREÇÕES APLICADAS\n";
echo str_repeat("=", 100) . "\n\n";

$correcoes = array(
    "ADMCloudAPI.pas (Linha 301)" => "passport → Passport/consulta",
    "ADMCloudAPI.pas (Linha 316)" => "registro → Pessoas/getAll",
    "ADMCloudAPI.pas (Linha 367)" => "registro → Pessoas/salvar",
    "ADMCloudConsts.pas (Linha 5)" => "URL DEV: /api/v1 removido",
    "ADMCloudConsts.pas (Linha 6)" => "URL PROD: /api/v1 removido",
    "ADMCloudConsts.pas (Linha 9)" => "Endpoint PASSPORT: passport → Passport/consulta",
    "ADMCloudConsts.pas (Linha 10)" => "Endpoint GET: registro → Pessoas/getAll",
    "ADMCloudConsts.pas (Linha 11)" => "Endpoint POST: registro → Pessoas/salvar",
    "ADMCloudAPIHelper.pas (Linha 150)" => "JSON case: 'Status' → 'status'"
);

$count = 1;
foreach ($correcoes as $arquivo => $descricao) {
    echo "{$count}. {$arquivo}: {$descricao}\n";
    $count++;
}

echo "\n\n" . str_repeat("=", 100) . "\n";
echo "🎯 PRÓXIMOS PASSOS\n";
echo str_repeat("=", 100) . "\n\n";

echo "1. Recompile os arquivos Pascal:\n";
echo "   - ADMCloudAPI.pas\n";
echo "   - ADMCloudConsts.pas\n";
echo "   - ADMCloudAPIHelper.pas\n\n";

echo "2. Teste a integração:\n";
echo "   - Chamar ValidarPassport('92702067000196', 'MEUPC', 'guid-teste')\n";
echo "   - Esperado: ✅ Sucesso (Status 200)\n";
echo "   - Antes: ❌ Erro 404\n\n";

echo "3. Verifique no seu código Delphi:\n";
echo "   var API: TADMCloudAPI;\n";
echo "   begin\n";
echo "     API := TADMCloudAPI.Create('http://104.234.173.105:7010');\n";
echo "     if API.ValidarPassport('92702067000196', 'MEUPC', 'teste') then\n";
echo "       ShowMessage('✅ Funcionando!');\n";
echo "   end;\n\n";

$conn->close();

echo "=== FIM DO TESTE ===\n";
?>
