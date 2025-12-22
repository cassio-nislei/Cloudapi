<?php defined('BASEPATH') OR exit('No direct script access allowed');

function edf_diario_istemp($id) {
    $CI =& get_instance();
    return $CI->db->where(['id' => (int)$id, 'status' => 'T'])
                  ->count_all_results('edf_diarios') > 0;
}

function edf_get_filial_by_diario($diario_id) {
    $CI =& get_instance();
    try {
        pode_ler('filiais');
        
        $CI->load->model('edf/Diarios_model','diarios');
        $CI->load->model('edf/Filiais_model','filiais');
        
        $diario = $CI->diarios->get((int)$diario_id);
        if ($diario) {
            $filial = $CI->filiais->get((int)$diario->filial_id);
            if ($filial) {
                return $filial;
            }
        }
        
    } catch (Exception $ex) {
        //
    }
    return NULL;
}

function edf_lista_filiais() {
    $CI =& get_instance();
    try {
        pode_ler('filiais');
        
        $resp = $CI->db->where(['emitente_id' => (int)$CI->session->userdata('user.emitente_id')])
                       ->select('id, nome')
                       ->order_by('nome')
                       ->get('edf_filiais');
        
        if ($resp->num_rows()) {
            return $resp->result_object();
        }
        
    } catch (Exception $ex) {
        //
    }
    return [];
}

function edf_lista_formas_pag($por_ordem = FALSE) {
    $CI =& get_instance();
    try {
        pode_ler('relatorios');
        
        $order_by = $por_ordem ? 'ordem' : 'nome';
        
        $resp = $CI->db->select('id, nome, sigla')
                       ->order_by($order_by)
                       ->get('sys_pag_formas');
        
        if ($resp->num_rows()) {
            return $resp->result_object();
        }
        
    } catch (Exception $ex) {
        //
    }
    return [];
}

function edf_totais_fp_dizimos($diario_id) {
    return edf_totais_fp_contrib($diario_id, 'dizimos');
}

function edf_totais_fp_ofertas($diario_id) {
    return edf_totais_fp_contrib($diario_id, 'ofertas');
}

function edf_totais_fp_especiais($diario_id) {
    return edf_totais_fp_contrib($diario_id, 'especiais');
}

function edf_totais_fp_missoes($diario_id) {
    return edf_totais_fp_contrib($diario_id, 'missoes');
}

function edf_lista_siglas_fp() {
    return ['DINH','DEPTO','PMAQ','PBCO','CCRED','CDEB'];
}

function edf_fp_descricao($sigla) {
    switch ($sigla) {
        case 'DINH': 
            return 'Dinheiro';            
        case 'DEPTO':
            return 'Depósito';            
        case 'PMAQ':
            return 'Pix Máq.';
        case 'PBCO':
            return 'Pix Banco';
        case 'CCRED':
            return 'Cartão Créd.';
        case 'CDEB':
            return 'Cartão Déb.';            
    }
    return $sigla;
}

function edf_totais_fp_contrib($diario_id, $tabela) {
    /*
     * edf_diario_dizimos
     * edf_diario_ofertas
     * edf_diario_especiais
     * edf_diario_missoes
     */
    
    $CI =& get_instance();
    $field = 'total';
    $total = 0.00;
    try {
        pode_ler('relatorios');
        
        $emitente_id = (int)$CI->session->userdata('user.emitente_id');
        
        //portal
        $filial_id = (int)$CI->session->userdata('user.filial_id');
        
        if ($tabela === 'dizimos') {
            $field = 'valor';
        }
        
        $return = [
            'DINH'  => 0.00,
            'DEPTO' => 0.00,
            'PMAQ'  => 0.00,
            'PBCO'  => 0.00,
            'CCRED' => 0.00,
            'CDEB'  => 0.00,
            'TOTAL' => 0.00,
        ];
        
        $sql = "select                    
                  spf.sigla as nome,
                  sum(edd.$field) as total  
                from
                  edf_diario_$tabela edd 
                  inner join sys_pag_formas spf on edd.forma_pag_id = spf.id 
                where
                  edd.diario_id = $diario_id
                  and edd.emitente_id = $emitente_id";
        
        if ($filial_id) {
            $sql .= " and edd.filial_id = $filial_id ";
        }
        
        $sql .=  " and edd.filial_id = $filial_id
                group by
                  edd.emitente_id, spf.nome";
        
        $resp = $CI->db->query($sql);
        
        if ($resp->num_rows()) {
            $registros = $resp->result_object();
            foreach($registros as $r) {
                switch ($r->nome) {
                    case 'DINH':
                        $return['DINH'] = $r->total;
                        break;
                    case 'DEPTO':
                        $return['DEPTO'] = $r->total;
                        break;
                    case 'PMAQ':
                        $return['PMAQ'] = $r->total;
                        break;
                    case 'PBCO':
                        $return['PBCO'] = $r->total;
                        break;
                    case 'CCRED':
                        $return['CCRED'] = $r->total;
                        break;
                    case 'CDEB':
                        $return['CDEB'] = $r->total;                        
                }                
                $total += $r->total;            
            }
            
        }
        $return['TOTAL'] = $total;
        
    } catch (Exception $ex) {
        //
    }
    return $return;
}

function edf_get_totais_diario($diario_id) {
    $CI =& get_instance();
    $data = NULL;
    try {
        $CI->load->model('edf/Diarios_model','diarios');
        $diario = $CI->diarios->get($diario_id, NULL, TRUE);
        
        $dizimos_fp   = edf_totais_fp_dizimos($diario_id);
        $ofertas_fp   = edf_totais_fp_ofertas($diario_id);
        $especiais_fp = edf_totais_fp_especiais($diario_id);
        $missoes_fp   = edf_totais_fp_missoes($diario_id);

        $data['pagamentos'] = [
            ['nome' => 'Dinheiro',     'valor' => $dizimos_fp['DINH']  + $ofertas_fp['DINH']  + $especiais_fp['DINH']  + $missoes_fp['DINH']  ],
            ['nome' => 'Depósito',     'valor' => $dizimos_fp['DEPTO'] + $ofertas_fp['DEPTO'] + $especiais_fp['DEPTO'] + $missoes_fp['DEPTO'] ],
            ['nome' => 'Pix Máq.',     'valor' => $dizimos_fp['PMAQ']  + $ofertas_fp['PMAQ']  + $especiais_fp['PMAQ']  + $missoes_fp['PMAQ']  ],
            ['nome' => 'Pix Banco',    'valor' => $dizimos_fp['PBCO']  + $ofertas_fp['PBCO']  + $especiais_fp['PBCO']  + $missoes_fp['PBCO']  ],
            ['nome' => 'Cartão Créd.', 'valor' => $dizimos_fp['CCRED'] + $ofertas_fp['CCRED'] + $especiais_fp['CCRED'] + $missoes_fp['CCRED'] ],
            ['nome' => 'Cartão Deb.',  'valor' => $dizimos_fp['CDEB']  + $ofertas_fp['CDEB']  + $especiais_fp['CDEB']  + $missoes_fp['CDEB']  ],
        ];

        $data['categorias'] = [
            ['nome' => 'Dízimos',         'valor' => $diario ? $diario->total_dizimos   : 0.00],
            ['nome' => 'Ofertas',         'valor' => $diario ? $diario->total_gerais    : 0.00],
            ['nome' => 'Ofertas Espec.',  'valor' => $diario ? $diario->total_especiais : 0.00],
            ['nome' => 'Ofertas Missões', 'valor' => $diario ? $diario->total_missoes   : 0.00],
            ['nome' => 'Total Final',     'valor' => $diario ? (float)$diario->total_dizimos + (float)$diario->total_gerais + (float)$diario->total_especiais + (float)$diario->total_missoes : 0.00],
        ];

    } catch (Exception $ex) {
        throw new Exception($ex->getMessage());
    }
    return $data;
}

function edf_totais_fp_geral($tabela, $filial_id = NULL, $data_inicial = NULL, $data_final = NULL) {
    /*
     * edf_diario_dizimos
     * edf_diario_ofertas
     * edf_diario_especiais
     * edf_diario_missoes
     */
    
    $CI =& get_instance();
    $field = 'total';
    $total = 0.00;
    try {
        pode_ler('relatorios');
        
        $emitente_id = (int)$CI->session->userdata('user.emitente_id');
        
        if ($tabela === 'dizimos') {
            $field = 'valor';
        }
        
        $return = [
            'DINH'  => 0.00,
            'DEPTO' => 0.00,
            'PMAQ'  => 0.00,
            'PBCO'  => 0.00,
            'CCRED' => 0.00,
            'CDEB'  => 0.00,
            'TOTAL' => 0.00,
        ];
        
        $sql = "select                    
                  spf.sigla as nome,
                  sum(edd.$field) as total  
                from
                  edf_diario_$tabela edd 
                  inner join edf_diarios d on edd.diario_id = d.id
                  inner join sys_pag_formas spf on edd.forma_pag_id = spf.id 
                where
                  edd.emitente_id = $emitente_id";
        
        if ($filial_id) {
            $sql .= ' and d.filial_id = '.(int)$filial_id;
        }
        
        if (!empty($data_inicial) && !empty($data_final)) {
            $sql .= " and d.data between '$data_inicial' and '$data_final' ";
        }
        
        $sql .= " group by
                    edd.emitente_id, spf.nome";
        
        $resp = $CI->db->query($sql);
        
        if ($resp->num_rows()) {
            $registros = $resp->result_object();
            foreach($registros as $r) {
                switch ($r->nome) {
                    case 'DINH':
                        $return['DINH'] = $r->total;
                        break;
                    case 'DEPTO':
                        $return['DEPTO'] = $r->total;
                        break;
                    case 'PMAQ':
                        $return['PMAQ'] = $r->total;
                        break;
                    case 'PBCO':
                        $return['PBCO'] = $r->total;
                        break;
                    case 'CCRED':
                        $return['CCRED'] = $r->total;
                        break;
                    case 'CDEB':
                        $return['CDEB'] = $r->total;                        
                }                
                $total += $r->total;            
            }
            
        }
        $return['TOTAL'] = $total;
        
    } catch (Exception $ex) {
        //
    }
    return $return;
}

//// PORTAL
function edf_tem_alteracoes($diario_id) {
    $CI =& get_instance();
    
    return $CI->db->where(['diario_id' => (int)$diario_id, 'status' => 'P'])
                  ->count_all_results('edf_diario_alteracoes') > 0;
}

function edf_get_nome_fp($id) {
    $CI =& get_instance();
    try {
        $resp = $CI->db->select('nome')
                       ->where(['id' => (int)$id])
                       ->get('sys_pag_formas');
        
        if ($resp->num_rows()) {
            return $resp->result_object()[0]->nome;
        }        
    } catch (Exception $ex) {
        //
    }    
    return 'Desconhecida';    
}

function edf_diario_limite_dias_edicao() {
    $CI =& get_instance();
    try {
        $emitente_id = (int)$CI->session->userdata('user.emitente_id');

        $resp = $CI->db->select('dias_fechar_diario')
                       ->where(['id' => $emitente_id])
                       ->get('sys_emitentes');

        if ($resp->num_rows()) {
            $obj = $resp->result_object()[0];
            return (int)$obj->dias_fechar_diario;
        }
        
    } catch (Exception $ex) {
        //
    }    
    return 2;    
}

function edf_diario_isconfimado($id) {
    $CI =& get_instance();
    return $CI->db->where(['id' => (int)$id, 'status' => 'C'])
                  ->count_all_results('edf_diarios') > 0;
}