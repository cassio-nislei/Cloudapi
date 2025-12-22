<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Relatorios extends CI_Controller {
    
    private $CONTROLE = 'relatorios';
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
        
        $resp = pode_ler($this->CONTROLE, FALSE);
        if (!$resp) {
            die('Sem permissão para acessar este recurso.');
        }
        
        $this->load->model('edf/Filiais_model','filiais');
        $this->load->model('edf/Diarios_model','diarios');
    } 
    
    function porArrecadacao() {
        $status = FALSE;
        $msg = '';
        $data = NULL;
        try {
            $emitente_id  = (int)$this->session->userdata('emit.id');
            $filial_id    = (int)$this->input->get('filial_id');
            $data_inicial = $this->input->get('data_inicial');
            $data_final   = $this->input->get('data_final');
            
            $this->validar_periodo($data_inicial, $data_final); //essa funcao jah converta data para data db
            
            if ($filial_id) {
                $filial = $this->filiais->get($filial_id);
                if (!$filial) {
                    throw new Exception('Filial não encontrada.');
                }
            }
            
            $pesquisa['emitente_id'] = $emitente_id;
            
            if ($filial_id) {
                $pesquisa['filial_id'] = $filial_id;
            }
            
            if ($data_inicial && $data_final) {
                $pesquisa['data >='] = $data_inicial;
                $pesquisa['data <='] = $data_final;
            }
            
            $resp = $this->db->where($pesquisa)
                             ->get('vw_diarios_totais');
            
            if (!$resp->num_rows()) {
                throw new Exception('Nenhum registro encontrado.');
            }
            
            $registros = $resp->result_object();
            
            //converter registros para estrutura de grafico
            $total_dizimos   = 0.00;
            $total_ofertas   = 0.00;
            $total_especiais = 0.00;
            $total_missoes   = 0.00;
            $total_geral     = 0.00;
            
            foreach($registros as $r) {
                $total_dizimos   += (float)$r->dizimos;
                $total_ofertas   += (float)$r->ofertas;
                $total_especiais += (float)$r->especiais;
                $total_missoes   += (float)$r->missoes;                
            }
            
            $total_geral = ($total_dizimos + $total_ofertas + $total_especiais + $total_missoes);
            
            if ($total_geral > 0) {
                $perc_dizimos   = valorToBr(($total_dizimos   * 100)/$total_geral);
                $perc_ofertas   = valorToBr(($total_ofertas   * 100)/$total_geral);
                $perc_especiais = valorToBr(($total_especiais * 100)/$total_geral);
                $perc_missoes   = valorToBr(($total_missoes   * 100)/$total_geral);
            } else {
                $perc_dizimos   = 0.00;
                $perc_ofertas   = 0.00;
                $perc_especiais = 0.00;
                $perc_missoes   = 0.00;
            }
            
            $data = [
                'tipos' => [
                    "Dízimos: R$ ". valorToBr($total_dizimos)." ($perc_dizimos%)", 
                    "Ofertas: R$ ". valorToBr($total_ofertas)." ($perc_ofertas%)", 
                    "Of. Especiais: R$ ". valorToBr($total_especiais)." ($perc_especiais%)", 
                    "Of. Missões: R$ ". valorToBr($total_missoes)." ($perc_missoes%)"],
                'totais' => [
                    number_format($total_dizimos,2,'.',''), 
                    number_format($total_ofertas,2,'.',''),  
                    number_format($total_especiais,2,'.',''),  
                    number_format($total_missoes,2,'.',''), 
                ],
                'total_geral' => valorToBr($total_geral)
            ];
            
            //teste
            $status = TRUE;
            $msg = 'Registros encontrados: '.is5_count($registros);
            //$data = $registros;            
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }        
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    function porFormasPagamento() {
        $status = FALSE;
        $msg = '';
        $data = NULL;
        try {
            $emitente_id  = (int)$this->session->userdata('emit.id');
            $filial_id    = (int)$this->input->get('filial_id');
            $data_inicial = $this->input->get('data_inicial');
            $data_final   = $this->input->get('data_final');
            
            $this->validar_periodo($data_inicial, $data_final); //essa funcao jah converta data para data db
            
            if ($filial_id) {
                $filial = $this->filiais->get($filial_id);
                if (!$filial) {
                    throw new Exception('Filial não encontrada.');
                }
            }
            
            $dizimos_fp   = edf_totais_fp_geral('dizimos', $filial_id, $data_inicial, $data_final); 
            $ofertas_fp   = edf_totais_fp_geral('ofertas', $filial_id, $data_inicial, $data_final);
            $especiais_fp = edf_totais_fp_geral('especiais', $filial_id, $data_inicial, $data_final);
            $missoes_fp   = edf_totais_fp_geral('missoes', $filial_id, $data_inicial, $data_final);
            
            $total_dinh  = $dizimos_fp['DINH']  + $ofertas_fp['DINH']  + $especiais_fp['DINH']  + $missoes_fp['DINH'];
            $total_depto = $dizimos_fp['DEPTO'] + $ofertas_fp['DEPTO'] + $especiais_fp['DEPTO'] + $missoes_fp['DEPTO'];
            $total_pmaq  = $dizimos_fp['PMAQ']  + $ofertas_fp['PMAQ']  + $especiais_fp['PMAQ']  + $missoes_fp['PMAQ'];
            $total_pbco  = $dizimos_fp['PBCO']  + $ofertas_fp['PBCO']  + $especiais_fp['PBCO']  + $missoes_fp['PBCO'];
            $total_ccred = $dizimos_fp['CCRED'] + $ofertas_fp['CCRED'] + $especiais_fp['CCRED'] + $missoes_fp['CCRED'];
            $total_cdeb  = $dizimos_fp['CDEB']  + $ofertas_fp['CDEB']  + $especiais_fp['CDEB']  + $missoes_fp['CDEB'];
            $total_geral = $dizimos_fp['TOTAL']  + $ofertas_fp['TOTAL']  + $especiais_fp['TOTAL']  + $missoes_fp['TOTAL'];
            
            if ($total_geral > 0) {
                $perc_dinh  = valorToBr(($total_dinh  * 100)/$total_geral);
                $perc_depto = valorToBr(($total_depto * 100)/$total_geral);
                $perc_pmaq  = valorToBr(($total_pmaq  * 100)/$total_geral);
                $perc_pbco  = valorToBr(($total_pbco  * 100)/$total_geral);
                $perc_ccred = valorToBr(($total_ccred * 100)/$total_geral);
                $perc_cdeb  = valorToBr(($total_cdeb  * 100)/$total_geral);
            } else {
                $perc_dinh  = 0.00;
                $perc_depto = 0.00;
                $perc_pmaq  = 0.00;
                $perc_pbco  = 0.00;
                $perc_ccred = 0.00;
                $perc_cdeb  = 0.00;
            }
            
            $data = [
                'tipos' => [
                    "Dinheiro: R$ ". valorToBr($total_dinh)." ($perc_dinh%)",
                    "Depósito: R$ ". valorToBr($total_depto)." ($perc_depto%)",
                    "Pix Máq: R$ ". valorToBr($total_pmaq)." ($perc_pmaq%)",
                    "Pix Banco: R$ ". valorToBr($total_pbco)." ($perc_pbco%)",
                    "Cartão Créd: R$ ". valorToBr($total_ccred)." ($perc_ccred%)",
                    "Cartão Deb: R$ ". valorToBr($total_cdeb)." ($perc_cdeb%)",
                ],
                'totais' => [
                   number_format($total_dinh,'2','.',''),
                   number_format($total_depto,'2','.',''),
                   number_format($total_pmaq,'2','.',''), 
                   number_format($total_pbco,'2','.',''),
                   number_format($total_ccred,'2','.',''),
                   number_format($total_cdeb,'2','.',''),
                ],
                'total' => valorToBr($total_geral)
            ];
                        
            $status = $total_geral > 0;
            $msg = 'Processamento concluído.';            
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }        
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    function pessoas() {
        $status = FALSE;
        $msg = NULL;
        $data = NULL;
        try {
            $emitente_id  = (int)$this->session->userdata('emit.id');
            $filial_id    = (int)$this->input->get('filial_id');
            $data_inicial = $this->input->get('data_inicial');
            $data_final   = $this->input->get('data_final');
            
            $this->validar_periodo($data_inicial, $data_final); //essa funcao jah converta data para data db
            
            if ($filial_id) {
                $filial = $this->filiais->get($filial_id);
                if (!$filial) {
                    throw new Exception('Filial não encontrada.');
                }
            }
            
            $pesquisa['emitente_id'] = $emitente_id;
            
            if ($filial_id) {
                $pesquisa['filial_id'] = $filial_id;
            }
            
            if ($data_inicial && $data_final) {
                $pesquisa['data >='] = $data_inicial;
                $pesquisa['data <='] = $data_final;
            }
            
            $registros = $this->diarios->pesquisar($pesquisa);
            if (!$registros) {
                throw new Exception('Nenhum registro encontrado.');
            }
            
            $total_adultos  = 0;
            $total_criancas = 0;
            $total_pessoas  = 0;
            $perc_adultos   = 0.00;
            $perc_criancas  = 0.00;
            
            foreach($registros as $r) {
                $total_adultos  += (int)$r->adultos;
                $total_criancas += (int)$r->criancas_ate12;
                $total_pessoas  += (int)$r->total_pessoas;
            }
            
            if ($total_pessoas) {
                $perc_adultos  = valorToBr(($total_adultos * 100)/$total_pessoas);
                $perc_criancas = valorToBr(($total_criancas * 100)/$total_pessoas);
            }
            
            $data = [
                'tipos' => [
                    "ADULTOS:  " . number_format($total_adultos, 0, '', '.') . " ($perc_adultos%)",
                    "CRIANÇAS: " . number_format($total_criancas, 0, '', '.') . " ($perc_criancas%)",                    
                ],
                'totais' => [
                    $total_adultos, $total_criancas
                ],
                'total_geral' => number_format($total_pessoas, 0, '', '.')
            ];
            
            $status = $total_pessoas > 0;
            $msg = 'Processamento concluído.';
            
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }        
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
        
    }
    
    private function validar_periodo(&$data_inicial, &$data_final) {
        if (!empty($data_inicial)) {
            $data_inicial = dateToDb($data_inicial);
            if (!is5_data_valida_db($data_inicial)) {
                throw new Exception('Especifique uma Data Inicial válida.');
            }
        }

        if (!empty($data_final)) {
            $data_final = dateToDb($data_final);
            if (!is5_data_valida_db($data_final)) {
                throw new Exception('Especifique uma Data Final válida.');
            }
        }

        if ( (!empty($data_inicial) && empty($data_final)) || (empty($data_inicial) && !empty($data_final)) ) {
            throw new Exception('Especifique Data Inicial e Final ou deixe ambas em branco.');
        }

        if ($data_inicial > $data_final) {
            throw new Exception('A Data Inicial deve ser menor ou igual a Data Final.');
        }
    }
    
    function pessoasPorFiliais() {
        $status = FALSE;
        $msg = NULL;
        $data = NULL;
        $registros = NULL;
        try {
            $emitente_id  = (int)$this->session->userdata('emit.id');
            $filial_id    = (int)$this->input->get('filial_id');
            $data_inicial = $this->input->get('data_inicial');
            $data_final   = $this->input->get('data_final');
            
            $this->validar_periodo($data_inicial, $data_final); //essa funcao jah converta data para data db
            
            if ($filial_id) {
                $filial = $this->filiais->get($filial_id);
                if (!$filial) {
                    throw new Exception('Filial não encontrada.');
                }
            }
            
            $where = " where ed.emitente_id = $emitente_id ";
            
            if ($filial_id) {
                $where .= " and ed.filial_id = $filial_id ";
            }
            
            if ($data_inicial && $data_final) {
                $where .= " and ed.data between '$data_inicial' and '$data_final' ";
            }
            
            $sql = "select 
                      ef.nome as filial,
                      sum(ed.total_pessoas) as total_pessoas
                    from
                      edf_diarios ed 
                      inner join edf_filiais ef on ed.filial_id = ef.id 
                      inner join sys_emitentes se on ef.emitente_id = se.id
                    $where
                    group by
                      ef.nome";
            
            $resp = $this->db->query($sql);
            if ($resp->num_rows()) {
                $registros = $resp->result_object();
            }
            
            if (!$registros) {
                throw new Exception('Nenhum registro encontrado.');
            }
                        
            $total_pessoas = 0;            
            foreach($registros as $r) {                
                $total_pessoas  += (int)$r->total_pessoas;
            }
            
            foreach($registros as $r) { 
                $percentual = 0;
                if ($total_pessoas) {
                    $percentual = valorToBr(($r->total_pessoas * 100)/$total_pessoas);
                }
                $data['tipos'][]     = is5_strtoupper("$r->filial: " . number_format($r->total_pessoas, 0, '', '.') . " ($percentual%)");
                $data['totais'][]    = $r->total_pessoas; 
                $data['total_geral'] = number_format($total_pessoas, 0, '', '.');
            }
            
            $status = $total_pessoas > 0;
            $msg = 'Processamento concluído.';
            
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }        
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
        
    }


}

