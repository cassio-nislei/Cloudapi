<?php 
/*
    EXEMPLO:
 
    $pay = new HtiPayClient(
        $this->session->userdata('emit.user_htipay'),
        $this->session->userdata('emit.token_htipay')
    );  
  
    //enviar NFCe
    $pay->enviarNFCe($nNF, $pedido->codigo, $json);
 
    //post generico
    $pay->post('payments', $json);   

    $status    = $pay->status;
    $mensagem  = $pay->msg;    
    $data      = $pay->data;
 */

class HtiPayClient {
    private $END_POINT = 'https://pay.hticard.com.br/api/';
    private $USER      = NULL;
    private $TOKEN     = NULL;
    
    public $status         = FALSE;
    public $msg            = NULL;
    public $response       = NULL;
    //public $transaction_id = NULL;  
    //public $pagamento_id   = NULL;
    //public $url_boleto     = NULL;
    //public $chave_pix      = NULL;
    //public $vencimento_pix = NULL;
    //public $codigo         = NULL; //da compra
    public $data           = [];
    
    function __construct($user, $token) {
        if ($user) {
            $this->USER = $user;
        }
        
        if ($token) {
            $this->TOKEN = $token;
        }
    }
    
    private function clearData() {
        $this->status         = FALSE;
        $this->msg            = NULL;
        $this->response       = NULL;
        //$this->transaction_id = NULL;
        //$this->pagamento_id   = NULL;
        //$this->url_boleto     = NULL;
        //$this->chave_pix      = NULL;
        //$this->vencimento_pix = NULL;
        //$this->codigo         = NULL;
        $this->data           = [];
    }
    
    private function getauth() { 
        return base64_encode($this->USER.':'.$this->TOKEN);
    }
    
    private function get($url, $auth = TRUE) {        
        try {
            $url = $this->END_POINT.$url; 

            $header[] = 'Content-Type: application/json; charset=utf-8';

            if ($auth) {
                $header[] = 'Authorization: Basic '.$this->getauth();
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);

            $response = curl_exec($ch);
            curl_close($ch);

            $json = json_decode($response);

            return [( (json_last_error() === 0) && $json ), $json, $response];

        } catch (Exception $ex) {
            //cho "Erro: ".$ex->getMessage();
            return [NULL, NULL, NULL];
        }
    }
    
    private function post($url, $data, $auth = TRUE) {        
        try {
            $url = $this->END_POINT.$url; 

            $header = [
                'Accept: application/json',
                'Content-Type: application/json'
            ];

            if ($auth) {
                $header[] = 'Authorization: Basic '.$this->getauth();
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            curl_close($ch);

            $json = json_decode($response);

            return [( (json_last_error() === 0) && $json ), $json, $response ];

        } catch (Exception $ex) {
            gravarLog('Erro zoop_post_api: '.$ex->getMessage());
            return [NULL, NULL, NULL];
        }
    }
    
    private function isAuth() {
        if (empty($this->USER)) {
            throw new Exception('Usuário não especificado.');
        }
        if (empty($this->TOKEN)) {
            throw new Exception('Token não especificado.');
        }
    }
    
    //BOLETO
    function payBoleto($valor, $pessoa, $historico = '') {
        $this->clearData();
        try {
            $this->isAuth();        
            
            $data = [
                'token'     => $this->TOKEN,
                'type'      => 'B',
                'valor'     => somenteNumeros(valorToBr($valor)),
                'historico' => $historico,
                'pessoa' => [
                    'nome'        => $pessoa->nome,
                    'cgc'         => $pessoa->cgc,
                    'endereco'    => $pessoa->endereco,
                    'numero'      => $pessoa->numero,
                    'complemento' => $pessoa->complemento,
                    'cidade'      => $pessoa->cidade,                    
                    'estado'      => $pessoa->estado,
                    'bairro'      => $pessoa->bairro,                    
                    'cep'         => $pessoa->cep,   
                    'celular'     => '559999999',
                    'email'       => 'teste@gmail.com'
                ]
            ];
            
            //var_dump($data);
            
            list ($ok, $json, $this->response) = $this->post('payments', $data);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API: '.$this->response);
            }
            
            $this->status = $json->status;
            $this->msg    = $json->msg;
            $this->data   = $json->data;            

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    //CARTAO
    function payCard($valor, $cartao, $pessoa = NULL, $historico = '') {
        $this->clearData();
        try {
            $this->isAuth();        
                        
            $data = [
                'token'     => $this->TOKEN,
                'type'      => 'C',
                'valor'     => somenteNumeros(valorToBr($valor)),
                'historico' => $historico,
                'cartao' => [
                    'numero'     => $cartao->numero,
                    'titular'    => $cartao->titular,
                    'vencimento' => $cartao->vencimento,
                    'codigo'     => $cartao->codigo
                ]
            ];
            
            if ($pessoa) {
                $data['pessoa'] = [
                    'nome'        => $pessoa->nome,
                    'cgc'         => $pessoa->cgc,
                    'endereco'    => $pessoa->endereco,
                    'numero'      => $pessoa->numero,
                    'complemento' => $pessoa->complemento,
                    'cidade'      => $pessoa->cidade,
                    'bairro'      => $pessoa->bairro,
                    'estado'      => $pessoa->estado,
                    'cep'         => $pessoa->cep,
                    'celular'     => $pessoa->celular,
                    'email'       => $pessoa->email
                ];
            }
            
            list ($ok, $json, $this->response) = $this->post('payments', $data);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API: '.$this->response);
            }
            
            $this->status = $json->status;
            $this->msg    = $json->msg;
            $this->data   = $json->data;

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    //PIX
    function payPix($valor, $pessoa, $historico = '') {
        $this->clearData();
        try {
            $this->isAuth();        
                        
            $data = [
                'token'     => $this->TOKEN,
                'type'      => 'P',
                'valor'     => somenteNumeros(valorToBr($valor)),
                'historico' => $historico,
                'pessoa' => [
                    'nome'        => $pessoa->nome,
                    'cgc'         => $pessoa->cgc,
                    'endereco'    => $pessoa->endereco,
                    'numero'      => $pessoa->numero,
                    'complemento' => $pessoa->complemento,
                    'cidade'      => $pessoa->cidade,
                    'bairro'      => $pessoa->bairro,
                    'estado'      => $pessoa->estado,
                    'cep'         => $pessoa->cep,
                    'celular'     => $pessoa->celular,
                    'email'       => $pessoa->email,
                ]
            ];
            
            list ($ok, $json, $this->response) = $this->post('payments', $data);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API: '.$this->response);
            }

            $this->status = $json->status;
            $this->msg    = $json->msg;
            $this->data   = $json->data;

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    function getTransacoes($data_inicial, $data_final, $status) {
        $this->clearData();
        try {
            $this->isAuth();
            
            $url = "transacoes?data_inicial=$data_inicial&data_final=$data_final&status=$status";
            
            list ($ok, $json, $this->response) = $this->get($url);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API de pagamento: '.$this->response);
            }

            if ($json->status) {
                $this->status = TRUE;
                $this->msg    = $json->msg;                
                $this->data   = isset($json->data) ? $json->data : [];
            } else {
                throw new Exception(isset($json->msg) ? $json->msg : 'Erro indefinido.');
            }

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    function getTransferencias($data_inicial, $data_final, $status, $historico) {
        $this->clearData();
        try {
            $this->isAuth();    
            
            if (!empty($historico)) {
                $historico = urlencode($historico);
            }
                        
            $url = "transferencias?data_inicial=$data_inicial&data_final=$data_final&status=$status&historico=$historico";
            
            list ($ok, $json, $this->response) = $this->get($url);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API de pagamento: '.$this->response);
            }

            if ($json->status) {
                $this->status = TRUE;
                $this->msg    = $json->msg;                
                $this->data   = isset($json->data) ? $json->data : [];
            } else {
                throw new Exception(isset($json->msg) ? $json->msg : 'Erro indefinido.');
            }

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    function getTransferenciaDetalhe($id) {
        $this->clearData();
        try {
            $this->isAuth();                    
            
            $url = "transferencias?id=$id&action=get_detalhe";
            
            list ($ok, $json, $this->response) = $this->get($url);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API de pagamento: '.$this->response);
            }

            if ($json->status) {
                $this->status = TRUE;
                $this->msg    = $json->msg;                
                $this->data   = isset($json->data) ? $json->data : [];
            } else {
                throw new Exception(isset($json->msg) ? $json->msg : 'Erro indefinido.');
            }

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    function estornarTransacao($transaction_id, $amount) {
        $this->clearData();
        try {
            $this->isAuth();                    
            
            $data = [
                'action'         => 'estornar',
                'transaction_id' => $transaction_id,
                'amount'         => somenteNumeros($amount)
            ];
            
            list ($ok, $json, $this->response) = $this->post('Transacoes', $data);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API de pagamento: '.$this->response);
            }

            if ($json->status) {
                $this->status = TRUE;
                $this->msg    = $json->msg;               
                
            } else {
                throw new Exception(isset($json->msg) ? $json->msg : 'Erro indefinido.');
            }

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    function getPIX() {
        $this->clearData();
        try {
            $this->isAuth();                    
            
            $url = "perfil?action=pix";
            
            list ($ok, $json, $this->response) = $this->get($url);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API de pagamento: '.$this->response);
            }

            if ($json->status) {
                $this->status = TRUE;
                $this->msg    = $json->msg;                
                $this->data   = isset($json->data) ? $json->data : [];
            } else {
                throw new Exception(isset($json->msg) ? $json->msg : 'Erro indefinido.');
            }

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    function setPIX($tipo, $dest, $banco, $chave) {
        $this->clearData();
        try {
            $this->isAuth();                    
            
            $data = [
                'action'     => 'pix',
                'pix_tipo'   => $tipo,
                'pix_dest'   => $dest,
                'pix_banco'  => $banco,
                'pix_chave'  => $chave
            ];
            
            list ($ok, $json, $this->response) = $this->post('perfil', $data);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API de pagamento: '.$this->response);
            }

            if ($json->status) {
                $this->status = TRUE;
                $this->msg    = $json->msg;                
                $this->data   = isset($json->data) ? $json->data : [];
            } else {
                throw new Exception(isset($json->msg) ? $json->msg : 'Erro indefinido.');
            }

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    function getNotas($data_inicial, $data_final, $filtro = 'T', $tipodoc = 'T') {
        $this->clearData();
        try {
            $this->isAuth();        
                        
            $url = "NFCe?action=notas&data_inicial=$data_inicial&data_final=$data_final&filtro=$filtro&tipodoc=$tipodoc";
            
            list ($ok, $json, $this->response) = $this->get($url);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API: '.$this->response);
            }

            if ($json->status) {
                $this->status = TRUE;
                $this->msg    = $json->msg;                
                $this->data   = isset($json->data) ? $json->data : [];
            } else {
                throw new Exception(isset($json->msg) ? $json->msg : 'Erro indefinido.');
            }

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    private function getDOC($tipo, $chave) {
        $this->clearData();
        try {
            $this->isAuth();        
                        
            $url = "NFCe?action=$tipo&chave=$chave";
            
            list ($ok, $json, $this->response) = $this->get($url);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API: '.$this->response);
            }

            $this->status = $json->status;
            $this->msg = $json->msg;

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    function getZIP($inicio, $fim) {
        $this->clearData();
        try {
            $this->isAuth(); 
            
            if (!is5_data_valida_br($inicio)) {
                throw new Exception('Especifique uma data inicial válida (dd/MM/yyyy)');
            }
            
            if (!is5_data_valida_br($fim)) {
                throw new Exception('Especifique uma data final válida (dd/MM/yyyy)');
            }
            
            $inicio = str_replace('/', '-', $inicio);
            $fim    = str_replace('/', '-', $fim);
                        
            $url = "NFCe?action=zip&inicio=$inicio&fim=$fim";
            
            list ($ok, $json, $this->response) = $this->get($url);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API: '.$this->response);
            }

            $this->status = $json->status;
            $this->msg = $json->msg;

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    function getXml($chave) {
        $this->getDOC('xml', $chave);
    }
    
    function getDanfe($chave) {
        $this->getDOC('danfe', $chave);
    }
    
    function enviarNFCe($nNF, $documento, $json) {
        /* COMO NAO EXISTE CUPOM NESSE SISTEMA, DESATIVEI ESSA FUNCAO AKI
         * PRA NAO FICAR DANDO BO (NAO ENCONTRAR HELPER GETMENU, ETC)
        $i = 0;
        $nfce = [];
        $pedido = NULL;
        //$total = 0.00;
        try {
            $nfce['documento'] = $documento; //PARA VINCULAR A NOTA COM O PEDIDO/CONTRATO/ETC
            $nfce['emissao'] = getDateTimeCurrent();
            
            $nfce['ide'] = [
                'nNF' => (int)$nNF
            ];
            
            //dados do cliente
            if (isset($json->cliente)) {
                $nfce['dest'] = [
                    'CNPJCPF' => $json->cliente->cgc,
                    'xNome'   => $json->cliente->nome,
                    'enderDest' => [                        
                        'xLgr'    => isset($json->cliente->endereco) ? $json->cliente->endereco : '',
                        'nro'     => isset($json->cliente->numero) ? $json->cliente->numero : '',
                        'xCpl'    => isset($json->cliente->complemento) ? $json->cliente->complemento : '',
                        'xBairro' => isset($json->cliente->bairro) ? $json->cliente->bairro : '',
                        'cMun'    => isset($json->cliente->cod_mun) ? (int)$json->cliente->cod_mun : 4311502,
                        'xMun'    => isset($json->cliente->cidade) ? $json->cliente->cidade : '',
                        'UF'      => isset($json->cliente->estado) ? $json->cliente->estado : '',
                        'CEP'     => isset($json->cliente->cep) ? (int)$json->cliente->cep : 0,
                        'cPais'   => 1058,
                        'xPais'   => 'BRASIL',
                        'fone'    => isset($json->cliente->celular) ? $json->cliente->celular : ''
                    ],
                    'indIEDest' => 9,
                    'email' => isset($json->cliente->email) ? $json->cliente->email : ''                    
                ];                
            }
            
            //produtos
            foreach($json->sacola->produtos as $p) {  
                if (!$p->quantidade) {
                    $p->quantidade = 1;
                }
                $unitario = floatval($p->total/$p->quantidade);
                
                $imp = getmenu_get_imp_produto($p->id); 
                //no DB:     cest (str), cfop (str), csosn (str), ncm (str), origem (str), tipo (str)
                //no server: cest (str), cfop (str), csosn (int), ncm (str), origem (str), tipo (int)
                        
                $nfce['det'][] = [
                    'nItem'    => ++$i,                    
                    'cProd'    => isset($p->codigo) ? zeros(trim($p->codigo),5) : '00000',
                    'eEAN'     => isset($p->codigo) ? $p->codigo : 'SEM GETIN',
                    'xProd'    => isset($p->nome)   ? $p->nome   : '', //$p->nome.' '.$p->descricao
                    'NCM'      => isset($imp->ncm)  ? $imp->ncm  : '64051010',
                    'CFOP'     => isset($imp->cfop) ? $imp->cfop : '5102',
                    'uCom'     => isset($p->media)  ? $p->medida : 'UND',
                    'qCom'     => floatval($p->quantidade),
                    'vUnCom'   => $unitario,
                    'qTrib'    => floatval($p->quantidade),
                    'vUnTrib'  => $unitario,
                    'vProd'    => isset($p->total)  ? floatval($p->total) : 0.00,
                    'cEANTrib' => isset($p->codigo) ? $p->codigo : 'SEM GETIN',
                    'uTrib'    => isset($p->media)  ? $p->media  : 'UND',
                    'intTot'   => 1,
                    'imposto' => [
                        'ICMS' => [
                            'orig'  => isset($imp->origem) ? (int)$imp->origem : 0,
                            'CSOSN' => isset($imp->csosn)  ? (int)$imp->csosn  : 102,
                            'CST'   => '00',
                            'modBC' => 0,
                            'vBC'   => 0,
                            'pICMS' => 0,
                            'vICMS' => 0                            
                        ],
                        'PIS' => [
                            'CST'  => '99',
                            'vBC'  => 0,
                            'pPIS' => 0,
                            'vPIS' => 0
                        ],
                        'COFINS' => [
                            'CST'  => '99',
                            'vBC'  => 0,
                            'pCOFINS' => 0,
                            'vCOFINS' => 0
                        ]
                    ]                   
                ];
                //$total += ($p->novo_preco * $p->quantidade);
            }
                        
            //configura variaveis pra evitar erros :P
            $total_produtos = isset($json->total_produtos) ? floatval($json->total_produtos) : 0;
            $desconto       = isset($json->desconto)       ? floatval($json->desconto)       : 0;
            $tele_entrega   = isset($json->tele_entrega)   ? floatval($json->tele_entrega)   : 0;
            $total_pedido   = isset($json->total_pedido)   ? floatval($json->total_pedido)   : 0;
            $total_pago     = isset($json->total_pago)     ? floatval($json->total_pago)     : 0;
            $troco          = isset($json->troco)          ? floatval($json->troco)          : 0;
            
            //total
            $nfce['total'] = [
                'vBC'        => 0,
                'vICMS'      => 0,
                'vICMSDeson' => 0,
                'FCP'        => 0,
                'vBCST'      => 0,
                'vST'        => 0,
                'vFCPSTRet'  => 0,
                'vProd'      => $total_produtos,
                'vFrete'     => 0,
                'vSeg'       => 0,
                'vDesc'      => $desconto,
                'vII'        => 0,
                'vIPI'       => 0,
                'vIPIDevolv' => 0,
                'vPIS'       => 0,
                'vCOFINS'    => 0,
                'vOutro'     => $tele_entrega,
                'vNF'        => $total_pedido, //floatval($json->total_produtos), floatval($json->total_pedido)
                'vTroco'     => $troco
            ];
            
            $tPag = '01'; //dinheiro
            $tBand = '';
            
            if (isset($json->pagamento)) {
                $tipo = (int)$json->pagamento->tipo_id;
                switch($tipo) {
                    case 1: //credito
                        $tPag = '03'; 
                        $tBand = getmenu_get_tband((int)$json->pagamento->metodo_id);
                        break;
                    case 2: //debito
                        $tPag = '04'; 
                        $tBand = getmenu_get_tband((int)$json->pagamento->metodo_id);
                        break;
                    case 5: //outros
                        if ($json->pagamento->metodo === "Dinheiro") {
                            $tPag = '01';
                        }
                        else if ($json->pagamento->metodo === "Pix") {
                            $tPag = '17';
                        }
                        else if ($json->pagamento->metodo === "Anotar") {
                            $tPag = '05'; //credito loja
                        }
                        break;                        
                }
            }
            
            $nfce['pag'][] = [
                'indPag' => 0,
                'tPag'   => $tPag,
                'tBand'  => $tBand,
                'vPag'   => ($total_pago > 0) ? $total_pago : $total_pedido //$total                 
            ];
            
            //gravarLog('Impostos: '.json_encode($imp));
            //gravarLog('nfce: '.json_encode($nfce));
            
            list ($ok, $jreturn, $response) = $this->post('NFCe', ['nfce' => $nfce]);
                                    
            if (!$ok) {
                throw new Exception('Erro ao acessar API: '.$response);
            }

            if ($jreturn->status) {
                $this->status = TRUE;
                $this->msg    = $jreturn->msg;               
                $this->data = [
                    'cStat'     => $jreturn->cStat,
                    'xMotivo'   => $jreturn->xMotivo,
                    'protocolo' => $jreturn->Protocolo,
                    'PDF'       => $jreturn->PDF,
                    'chave'     => $jreturn->Chave,
                ];
                
            } else {
                $this->data = [
                    'cStat'   => isset($jreturn->cStat) ? $jreturn->cStat : 0,
                    'xMotivo' => isset($jreturn->xMotivo) ? $jreturn->xMotivo : 'Motivo desconhecido.'
                ];
                throw new Exception(isset($jreturn->msg) ? $jreturn->msg : 'Erro indefinido.');
            }
            
            
        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }*/
    }
    
    function getStatus() {
        $this->clearData();
        try {
            $this->isAuth();        
                        
            $url = "NFCe?action=status";
            
            list ($ok, $json, $this->response) = $this->get($url);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API: '.$this->response);
            }

            if (isset($json->cStat)) {
                $this->status = $json->cStat === 107;
                $this->msg    = isset($json->xMotivo) ? $json->xMotivo : $this->response;
                $this->data   = $json;
                
            } else {
                $this->status = FALSE;
                $this->msg    = $json->msg; 
                $this->data   = $json;
            }

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    function cancelarNFCe($chave, $just) {
        $this->clearData();
        try {
            $this->isAuth();        
            
            $cancelar = [
                'chave' => $chave,
                'just'  => $just
            ];
                        
            list ($ok, $json, $response) = $this->post('NFCe', ['cancelar' => $cancelar]);
                                    
            if (!$ok) {
                throw new Exception('Erro ao acessar API: '.$response);
            }

            $this->status = TRUE;
            $this->msg    = $json->msg;            

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    function getTaxas() {
        $this->clearData();
        try {
            $this->isAuth();                    
            
            $url = "Taxas";
            
            list ($ok, $json, $this->response) = $this->get($url);
            
            if (!$ok) {
                throw new Exception('Erro ao acessar API de pagamento: '.$this->response);
            }

            if ($json->status) {
                $this->status = TRUE;
                $this->msg    = $json->msg;                
                $this->data   = isset($json->data) ? $json->data : [];
            } else {
                throw new Exception(isset($json->msg) ? $json->msg : 'Erro indefinido.');
            }

        } catch (Exception $ex) {
            $this->status = FALSE;
            $this->msg = $ex->getMessage();
        }
    }
    
    
}









