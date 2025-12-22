<?php

function fiscal_get_impostos($imposto, $categoria) {
    $CI =& get_instance();
    $data = [];
    try {
        $resp = $CI->db->order_by('ID')
                       ->select('CODIGO, DESCRICAO')
                       ->where(['TABELA' => 'ESTOQUE', 'CATEGORIA' => $categoria, 'ITEM' => $imposto])
                       ->get('TABELA_AUXILIAR');


        if ($resp->num_rows()) {
            $data = $resp->result_object();
        }             
    } catch (Exception $ex) {
        //
    }
    return $data;
}

function papion_extrair_produtos($xmlString) {
    // Carrega o XML
    $dom = new DOMDocument();
    $dom->loadXML($xmlString);
    
    // Cria um novo XPath
    $xpath = new DOMXPath($dom);
    
    // Define o namespace para buscar as tags
    $xpath->registerNamespace('nfe', 'http://www.portalfiscal.inf.br/nfe');
    
    // Seleciona todas as tags <prod> dentro de <det>
    $produtosNodes = $xpath->query('//nfe:det/nfe:prod');
    
    $produtos = [];
    
    foreach ($produtosNodes as $prodNode) {
        $produto = [];
        
        // Itera por cada elemento filho dentro de <prod>
        foreach ($prodNode->childNodes as $childNode) {
            if ($childNode->nodeType === XML_ELEMENT_NODE) {
                $produto[$childNode->nodeName] = $childNode->nodeValue;
            }
        }
        
        $produtos[] = $produto;
    }
    
    return $produtos;
}

function papion_extrair_produtos_impostos($xmlString) {
    $produtos = [];
    $nfeXml = new SimpleXMLElement($xmlString);
    $nfeXml->registerXPathNamespace('ns', 'http://www.portalfiscal.inf.br/nfe');

    $itens = $nfeXml->xpath('//ns:det');
    foreach ($itens as $item) {
        $produto = [];
        $produto['cProd']    = (string)$item->prod->cProd;
        $produto['cEAN']     = (string)$item->prod->cEAN;
        $produto['xProd']    = (string)$item->prod->xProd;
        $produto['NCM']      = (string)$item->prod->NCM;
        $produto['CEST']     = (string)$item->prod->CEST;
        $produto['CFOP']     = (string)$item->prod->CFOP;
        $produto['uCom']     = (string)$item->prod->uCom;
        $produto['qCom']     = (float)$item->prod->qCom;
        $produto['vUnCom']   = (float)$item->prod->vUnCom;
        $produto['vProd']    = (float) $item->prod->vProd;
        $produto['cEANTrib'] = (string)$item->prod->cEANTrib;
        $produto['uTrib']    = (string)$item->prod->uTrib;
        $produto['qTrib']    = (float)$item->prod->qTrib;
        $produto['vUnTrib']  = (float)$item->prod->vUnTrib;
        $produto['indTot']   = (string)$item->prod->indTot;

        $impostos = [];
        if (isset($item->imposto->ICMS)) {
            $icms = $item->imposto->ICMS->children();
            foreach ($icms as $tipoICMS) {
                $impostos['ICMS'] = [
                    'origem' => (string)$tipoICMS->orig,
                    'CST'    => (string)$tipoICMS->CST,
                    'vBC'    => (float)$tipoICMS->vBC,
                    'pICMS'  => (float)$tipoICMS->pICMS,
                    'vICMS'  => (float)$tipoICMS->vICMS,
                    'pRedBC' => (float)$tipoICMS->pRedBC ?? 0.00,
                    'pFCP'   => (float)$tipoICMS->pFCP ?? 0.00,
                ];
                break; // Pega o primeiro ICMS encontrado
            }
        }
        if (isset($item->imposto->IPI)) {
            $ipi = $item->imposto->IPI->children();
            $impostos['IPI'] = [
                'CST'  => (string)$ipi->IPITrib->CST,
                'vBC'  => (float)$ipi->IPITrib->vBC,
                'pIPI' => (float)$ipi->IPITrib->pIPI,
                'vIPI' => (float)$ipi->IPITrib->vIPI,
            ];
        }
        if (isset($item->imposto->PIS)) {
            $pis = $item->imposto->PIS->children();
            foreach ($pis as $tipoPIS) {
                $impostos['PIS'] = [
                    'CST'  => (string)$tipoPIS->CST,
                    'vBC'  => (float)$tipoPIS->vBC,
                    'pPIS' => (float)$tipoPIS->pPIS,
                    'vPIS' => (float)$tipoPIS->vPIS,
                ];
                break; // Pega o primeiro PIS encontrado
            }
        }
        if (isset($item->imposto->COFINS)) {
            $cofins = $item->imposto->COFINS->children();
            foreach ($cofins as $tipoCOFINS) {
                $impostos['COFINS'] = [
                    'CST'     => (string)$tipoCOFINS->CST,
                    'vBC'     => (float)$tipoCOFINS->vBC,
                    'pCOFINS' => (float)$tipoCOFINS->pCOFINS,
                    'vCOFINS' => (float)$tipoCOFINS->vCOFINS,
                ];
                break; // Pega o primeiro COFINS encontrado
            }
        }

        $produto['impostos'] = $impostos;
        $produtos[] = $produto;
    }
        
    return $produtos;
}

function papion_importar_produtos_xml($xmlString) {
    $status = FALSE;
    $msg = NULL;    
    $cont_ncm = 0;
    $cont_prod = 0;
    try {
        $CI =& get_instance();
        
        $CI->load->model('NcmDetalhe_model', 'ncms');
        $CI->load->model('Produtos_model', 'produtos');
        
        $produtos = papion_extrair_produtos_impostos($xmlString);
        
        if (count($produtos) === 0) {
            throw new Exception('Nenhum produto encontrado no XML informado.');
        }
        
        foreach($produtos as $p) {  
            //VERIFICA SE EXISTE NCM
            $existe = $CI->ncms->check_ncm($p['NCM'] ?? '', 0);
            
            //SE NAO EXISTE, CADASTRA NCM
            if (!$existe) { 
                //$msg .= 'NCM '.$p['NCM'].' NAO EXISTE! ';
                $dados = [
                    //BASICO
                    'NCM'    => $p['NCM'] ?? '',
                    'CEST'   => $p['CFOP'] ?? '',
                    'CFOP'   => $p['CEST'] ?? '',
                    'CBENEF' => $p['cBenef'] ?? '',
                    
                    //ICMS
                    'CST'     => $p['impostos']['ICMS']['CST'] ?? '',                    
                    'CSOSN'   => $p['impostos']['ICMS']['CSOSN'] ?? '',
                    'ORIGEM'  => $p['impostos']['ICMS']['origem'] ?? '',
                    'RED_BC'  => $p['impostos']['ICMS']['pRedBC'] ?? '',
                    'IMP_FCP' => $p['impostos']['ICMS']['pFCP'] ?? '',
                    
                    //IPI
                    'IPI_CST'        => $p['impostos']['IPI']['CST'] ?? '',  
                    'IPI_PERCENTUAL' => $p['impostos']['IPI']['pIPI'] ?? '',  
                    
                    //PIS
                    'PISCOFINS_CST_ENTRADA'      => $p['impostos']['PIS']['CST'] ?? '',
                    'PISCOFINS_PERC_PIS_ENTRADA' => $p['impostos']['PIS']['pPIS'] ?? '',
                    
                    //COFINS
                    'PISCOFINS_PERC_COFINS_ENTRADA' => $p['impostos']['COFINS']['pCOFINS'] ?? '',
                ];
                $CI->ncms->gravar($dados);
                $cont_ncm++;
            } else {
                //$msg .= 'NCM '.$p['NCM'].' EXISTE! ';
            }
            
            //GRAVOU OU EXISTE NCM?
            if ($CI->ncms->check_ncm($p['NCM'] ?? '', 0)) {
                //VERIFICA SE EXISTE O PRODUTO
                $existe = $CI->produtos->check_referencia($p['cEAN'] ?? '', 0); 
            
                //SE NAO EXISTE, CADASTRA PRODUTO
                if (!$existe) {
                    //cadastra
                    $dados = [
                        'CODIGO'     => $p['cProd']   ?? '',
                        'REFERENCIA' => $p['cEAN']    ?? '',
                        'DESCRICAO'  => $p['xProd']   ?? '',
                        'MEDIDA'     => $p['uCom']    ?? '',
                        'NCM'        => $p['NCM']     ?? '',
                        'CEST'       => $p['CFOP']    ?? '',
                        'CFOP'       => $p['CEST']    ?? '',
                        'CBENEF'     => $p['cBenef']  ?? '',
                    ];
                    $CI->produtos->gravar($dados);
                    $cont_prod++;                    
                }
            }            
        }
        
        $status = ($cont_ncm > 0) || ($cont_prod > 0);
        $msg    .= "Registros importados: NCM ($cont_ncm), Produtos ($cont_prod).";
        
    } catch (Exception $ex) {
        $status = FALSE;
        $msg = $ex->getMessage();
    }
    return [$status, $msg];
}
