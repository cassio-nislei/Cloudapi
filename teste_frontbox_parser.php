<?php
// ========================================
// TESTE DO PARSER XML DO FRONTBOX
// ========================================
date_default_timezone_set('America/Sao_Paulo');

$cnpj_teste = '01611275000124';
$cnpj_formatado = '01.611.275/0001-24';

echo "=== TESTE FRONTBOX XML PARSER - " . date('d/m/Y H:i:s') . " ===\n";
echo "CNPJ: $cnpj_formatado\n";
echo "=================================\n\n";

// Simular a resposta que viria da API
$resposta_raw = '{status}OK{/status}{nome}PAPION INFORMATICA LTDA{/nome}{fantasia}PAPION INFORMÁTICA{/fantasia}{endereco}RUA DR JOAO L. BULCAO{/endereco}{complemento}CASA{/complemento}{cgc}01.611.275/0001-24{/cgc}{ie}0730019624{/ie}{telefone}(55)3282-1450{/telefone}{numero}229{/numero}{bairro}CENTRO{/bairro}{cidade}LAVRAS DO SUL{/cidade}{estado}RS{/estado}{cnae}{/cnae}{im}{/im}{tipo}{/tipo}{email}papion@papion.com.br{/email}';

echo "=== RESPOSTA RAW ===\n";
echo $resposta_raw . "\n\n";

// Função para parsear o XML customizado
function parseXmlCustomizado($xml) {
    $dados = array();
    
    // Padrão: {tag}valor{/tag}
    $pattern = '/{(\w+)}(.*?){\/\1}/';
    
    if (preg_match_all($pattern, $xml, $matches)) {
        for ($i = 0; $i < count($matches[1]); $i++) {
            $tag = strtoupper($matches[1][$i]);
            $valor = $matches[2][$i];
            $dados[$tag] = $valor;
        }
    }
    
    return $dados;
}

// Parsear a resposta
$parsed = parseXmlCustomizado($resposta_raw);

echo "=== DADOS PARSEADOS ===\n";
foreach ($parsed as $campo => $valor) {
    $display_valor = !empty($valor) ? $valor : '[VAZIO]';
    echo "? $campo: $display_valor\n";
}

echo "\n=== VERIFICAÇÃO DETALHADA ===\n";

// Verificar cada campo importante
$campos_obrigatorios = [
    'STATUS' => 'Status da resposta',
    'NOME' => 'Nome da empresa',
    'FANTASIA' => 'Nome fantasia',
    'CGC' => 'CNPJ formatado',
    'ENDERECO' => 'Endereço',
    'NUMERO' => 'Número',
    'COMPLEMENTO' => 'Complemento',
    'BAIRRO' => 'Bairro',
    'CIDADE' => 'Cidade',
    'ESTADO' => 'Estado',
    'IE' => 'Inscrição Estadual',
    'EMAIL' => 'Email',
    'TELEFONE' => 'Telefone',
    'CNAE' => 'CNAE',
    'IM' => 'Inscrição Municipal',
    'TIPO' => 'Tipo de empresa'
];

$campos_preenchidos = 0;
$campos_vazios = 0;

echo "\n";
foreach ($campos_obrigatorios as $campo => $descricao) {
    $valor = isset($parsed[$campo]) ? $parsed[$campo] : '';
    $status = !empty($valor) ? '✓' : '✗';
    
    if (!empty($valor)) {
        $campos_preenchidos++;
        echo "$status [$campo] $descricao: $valor\n";
    } else {
        $campos_vazios++;
        echo "$status [$campo] $descricao: [VAZIO]\n";
    }
}

echo "\n=== RESUMO ===\n";
echo "Total de campos esperados: " . count($campos_obrigatorios) . "\n";
echo "Campos preenchidos: $campos_preenchidos\n";
echo "Campos vazios: $campos_vazios\n";
echo "Completude: " . round(($campos_preenchidos / count($campos_obrigatorios)) * 100, 2) . "%\n";

// Análise de campos vazios
echo "\n=== CAMPOS VAZIOS ENCONTRADOS ===\n";
$vazios_encontrados = false;
foreach ($campos_obrigatorios as $campo => $descricao) {
    $valor = isset($parsed[$campo]) ? $parsed[$campo] : '';
    if (empty($valor)) {
        echo "- $campo ($descricao)\n";
        $vazios_encontrados = true;
    }
}

if (!$vazios_encontrados) {
    echo "Nenhum campo vazio encontrado!\n";
}

echo "\n=== JSON ESTRUTURADO ===\n";
echo json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

?>
