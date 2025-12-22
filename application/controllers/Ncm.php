<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ncm extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
        
        $this->load->model('NcmDetalhe_model', 'modelo');
    }
    
    function index() {          
        $data['index'] = 0;
        $data['content'] = $this->load->view('ncm/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);             
    }
    
    function get() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            //pode_ler($this->CONTROLE);
            
            $id = (int)$this->input->get('id');           
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $data = $this->modelo->get($id);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registro encontrado';   
                
                $this->valoresToBr($data);
                
            } else {
                throw new Exception('Nenhum registro encontrado.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    function getAll() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {  
            $pesquisa = [];
            
            $ncm       = $this->input->get('ncm');
            $descricao = $this->input->get('descricao');
            $filtro    = $this->input->get('filtro');
              
            /*
            if (empty($ncm) && empty($descricao) && empty($referencia)) {
                throw new Exception('Informe NCM ou Descrição ou Referência.');
            }*/
            
            if (!empty($ncm)) {
                $pesquisa['NCM like'] = $ncm.'%';
            }
            
            if (!empty($descricao)) {
                $pesquisa['descricao like'] = '%'.$descricao.'%';
            }
            
            if (in_array($filtro, ['S','N'])) {
                $pesquisa['auditado'] = $filtro;
            }
            
            $data = $this->modelo->pesquisar($pesquisa);            
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registros encontrados: '. is5_count($data);
                
                foreach($data as $d) {
                    $d->NOME  = is5_strtoupper($d->NOME);
                    
                    if (strlen($d->DESCRICAO) > 50) {
                        $d->DESCRICAO = mb_substr($d->DESCRICAO, 0, 50);
                    }
                    
                    $this->valoresToBr($d);
                }
                
            } else {
                throw new Exception('Nenhum registro encontrado.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    function salvar() {
        $status = FALSE;
        $msg = '';        
        try {
            $ID            = (int)$this->input->post('ID');
            $NCM           = $this->input->post('NCM');            
            $DESCRICAO     = $this->input->post('DESCRICAO');            
            
            $IMP_ALIQ_ICM  = $this->input->post('IMP_ALIQ_ICM');
            $CFOP          = somenteNumeros($this->input->post('CFOP'));
            $ORIGEM        = somenteNumeros($this->input->post('ORIGEM'));
            $CST           = somenteNumeros($this->input->post('CST'));
            $CSOSN         = somenteNumeros($this->input->post('CSOSN'));
            $CEST          = somenteNumeros($this->input->post('CEST'));            
            $RED_BC        = $this->input->post('RED_BC') ;
            $CRED_SN       = $this->input->post('CRED_SN');
            $CBENEF        = $this->input->post('CBENEF');
            $TIPO_ITEM     = $this->input->post('TIPO_ITEM');
            
            //IPO
            $IPI_CST        = $this->input->post('IPI_CST');
            $IPI_PERCENTUAL = $this->input->post('IPI_PERCENTUAL');
            
            //PIS
            $PISCOFINS_CST_SAIDA           = $this->input->post('PISCOFINS_CST_SAIDA');
            $PISCOFINS_CST_ENTRADA         = $this->input->post('PISCOFINS_CST_ENTRADA') ;
            
            $PISCOFINS_PERC_PIS_SAIDA      = $this->input->post('PISCOFINS_PERC_PIS_SAIDA') ;
            $PISCOFINS_PERC_COFINS_SAIDA   = $this->input->post('PISCOFINS_PERC_COFINS_SAIDA');
            $PISCOFINS_PERC_PIS_ENTRADA    = $this->input->post('PISCOFINS_PERC_PIS_ENTRADA');
            $PISCOFINS_PERC_COFINS_ENTRADA = $this->input->post('PISCOFINS_PERC_COFINS_ENTRADA');
            
            $IMP_TRIBUTACAO_MONOFASICA     = $this->input->post('IMP_TRIBUTACAO_MONOFASICA') ;
            
            //OUTROS
            $IMP_CSOSN_EXTERNO = $this->input->post('IMP_CSOSN_EXTERNO');
            $IMP_CFOP_EXTERNO  = $this->input->post('IMP_CFOP_EXTERNO');
            $IMP_COD_ENQ_IPI   = $this->input->post('IMP_COD_ENQ_IPI');
            $IMP_FCP           = $this->input->post('IMP_FCP');
            $IMP_MVA_NORMAL    = $this->input->post('IMP_MVA_NORMAL');            
            $IMP_MVA           = $this->input->post('IMP_MVA');
            $IMP_CST_EXTERNO   = $this->input->post('IMP_CST_EXTERNO');
            $IMP_GLP           = $this->input->post('IMP_GLP');
            $IMP_GNN           = $this->input->post('IMP_GNN');
            $IMP_GNI           = $this->input->post('IMP_GNI');
            
            $AUDITADO = $this->input->post('AUDITADO');
            
            //$IMP_MOTIVO_DESON = $this->input->post('IMP_MOTIVO_DESON');            
            
            if (empty($NCM)) {
                throw new Exception('O campo NCM/Código é obrigatório.');
            }
            
            if (empty($DESCRICAO)) {
                throw new Exception('O campo Descrição é obrigatório.');
            }
            
            if ($this->modelo->check_ncm($NCM, $ID)) {
                throw new Exception(" O código $NCM já está cadastrado no sistema.");
            }
                                    
            $data = [
                //'ID'             => addslashes($ID),
                'NCM'              => addslashes($NCM),
                'DESCRICAO'        => addslashes( is5_strtoupper($DESCRICAO) ),

                'IMP_ALIQ_ICM'     => valorToDb($IMP_ALIQ_ICM),
                'CFOP'             => addslashes($CFOP),
                'ORIGEM'           => addslashes($ORIGEM),
                'CST'              => addslashes($CST),
                'CSOSN'            => addslashes($CSOSN),
                'CEST'             => addslashes($CEST),
                'RED_BC'           => valorToDb($RED_BC),
                'CRED_SN'          => valorToDb($CRED_SN),
                'CBENEF'           => addslashes($CBENEF),
                'TIPO_ITEM'        => addslashes($TIPO_ITEM),

                // IPI
                'IPI_CST'          => addslashes($IPI_CST),
                'IPI_PERCENTUAL'   => valorToDb($IPI_PERCENTUAL),

                // PIS
                'PISCOFINS_CST_SAIDA'           => addslashes($PISCOFINS_CST_SAIDA),
                'PISCOFINS_PERC_PIS_SAIDA'      => valorToDb($PISCOFINS_PERC_PIS_SAIDA),
                'PISCOFINS_PERC_COFINS_SAIDA'   => valorToDb($PISCOFINS_PERC_COFINS_SAIDA),

                'PISCOFINS_CST_ENTRADA'         => addslashes($PISCOFINS_CST_ENTRADA),
                'PISCOFINS_PERC_PIS_ENTRADA'    => valorToDb($PISCOFINS_PERC_PIS_ENTRADA),
                'PISCOFINS_PERC_COFINS_ENTRADA' => valorToDb($PISCOFINS_PERC_COFINS_ENTRADA),

                'IMP_TRIBUTACAO_MONOFASICA'     => addslashes($IMP_TRIBUTACAO_MONOFASICA),

                // Outros
                'IMP_CSOSN_EXTERNO'  => addslashes($IMP_CSOSN_EXTERNO),
                'IMP_CFOP_EXTERNO'   => addslashes($IMP_CFOP_EXTERNO),
                'IMP_COD_ENQ_IPI'    => valorToDb($IMP_COD_ENQ_IPI),
                'IMP_FCP'            => valorToDb($IMP_FCP),
                'IMP_MVA_NORMAL'     => valorToDb($IMP_MVA_NORMAL),
                'IMP_MVA'            => valorToDb($IMP_MVA),
                'IMP_CST_EXTERNO'    => valorToDb($IMP_CST_EXTERNO),
                'IMP_GLP'            => valorToDb($IMP_GLP),
                'IMP_GNN'            => valorToDb($IMP_GNN),
                'IMP_GNI'            => valorToDb($IMP_GNI),
                
                'AUDITADO'           => in_array($AUDITADO, ['S','N']) ? $AUDITADO : 'N',
                'DATA_AUDITORIA'     => $AUDITADO === 'S' ? getDateTimeCurrent() : NULL,
                
                //'IMP_MOTIVO_DESON'   => addslashes($IMP_MOTIVO_DESON),
            ];
            
            $ret = $this->modelo->gravar($data, $ID);
            if ($ret) {
                $status = TRUE;
                $msg = 'Registro gravado com sucesso!';
                
                //atualiza em produtos data e hora da auditoria
                
                
            } else {
                throw new Exception('Erro ao gravar registro. Tente novamente.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }
    
    function excluir() {
        $status = FALSE;
        $msg = '';        
        try {
            $id = (int)$this->uri->segment(3);
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $ncm = $this->modelo->get($id);
            if (!$ncm) {
                throw new Exception('Registro não encontrado.');
            }
            
            $count = $this->db->where(['NCM' => $ncm->NCM])
                              ->count_all_results('PRODUTOS');
            
            if ($count) {
                throw new Exception("Impossível excluir NCM. Existem $count produtos vinculados!");
            }
            
            $resp = $this->modelo->excluir($id);
             
            if ($resp) {
                $status = TRUE;
                $msg = 'Registro excluído com sucesso!';                
            } else {
                throw new Exception('Erro ao excluir registro.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }
    
    private function valoresToBr($d) {
        $d->IMP_ALIQ_ICM                  = valorToBr($d->IMP_ALIQ_ICM);
        $d->IPI_PERCENTUAL                = valorToBr($d->IPI_PERCENTUAL);
        $d->RED_BC                        = valorToBr($d->RED_BC);
        $d->CRED_SN                       = valorToBr($d->CRED_SN);
        $d->PISCOFINS_PERC_PIS_SAIDA      = valorToBr($d->PISCOFINS_PERC_PIS_SAIDA);
        $d->PISCOFINS_PERC_COFINS_SAIDA   = valorToBr($d->PISCOFINS_PERC_COFINS_SAIDA);
        $d->PISCOFINS_PERC_PIS_ENTRADA    = valorToBr($d->PISCOFINS_PERC_PIS_ENTRADA);
        $d->PISCOFINS_PERC_COFINS_ENTRADA = valorToBr($d->PISCOFINS_PERC_COFINS_ENTRADA);
        $d->IMP_COD_ENQ_IPI               = valorToBr($d->IMP_COD_ENQ_IPI);
        $d->IMP_FCP                       = valorToBr($d->IMP_FCP);
        $d->IMP_MVA_NORMAL                = valorToBr($d->IMP_MVA_NORMAL);
        $d->IMP_MVA                       = valorToBr($d->IMP_MVA);
        $d->IMP_CST_EXTERNO               = valorToBr($d->IMP_CST_EXTERNO);
        $d->IMP_GLP                       = valorToBr($d->IMP_GLP);
        $d->IMP_GNN                       = valorToBr($d->IMP_GNN);
        $d->IMP_GNI                       = valorToBr($d->IMP_GNI);
        
        $d->DATA_AUDITORIA = dateToBr($d->DATA_AUDITORIA);
    }
    
    function setAuditado() {
        $status = FALSE;
        $msg = '';        
        try {
            $id = (int)$this->input->post('id');
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $ncm = $this->modelo->get($id);
            if (!$ncm) {
                throw new Exception('Registro não encontrado.');
            }
            
            $data = [
                'AUDITADO'       => 'S',
                'DATA_AUDITORIA' => getDateTimeCurrent(),
            ];
            
            $resp = $this->modelo->gravar($data, $id);
             
            if ($resp) {
                $status = TRUE;
                $msg = 'Registro auditado com sucesso!'; 
                
                //sinaliza em produtos                               
                $this->db->update('PRODUTOS', 
                        [
                            'AUDITADO'           => 'S',
                            'DATA_HORA_AUDITADO' => getDateTimeCurrent(),
                        ], 
                        ['NCM' => $ncm->NCM]);
                
            } else {
                throw new Exception('Erro ao auditar registro.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }
}

