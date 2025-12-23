<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produtos extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {
            // Se for requisição AJAX, retorna JSON
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => FALSE, 'msg' => 'Não autenticado', 'data' => []]);
                exit;
            }
            redirect(base_url('Account/login'));
        }
        
        $this->load->model('Produtos_model', 'modelo');
        $this->load->model('NcmDetalhe_model', 'ncm');
    }
    
    function index() {          
        $data['index'] = 0;
        $data['content'] = $this->load->view('produtos/index',NULL,TRUE);
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
                
                $data->DATA_HORA_AUDITADO = dateToBr($data->DATA_HORA_AUDITADO);
                
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
            $referencia = $this->input->get('referencia');
            $filtro    = $this->input->get('filtro');
            
            if (empty($ncm) && empty($descricao) && empty($referencia) && empty($filtro)) {
                throw new Exception('Informe algum campo para a pesquisa.');
            }
            
            if (!empty($ncm)) {
                $pesquisa['NCM like'] = $ncm.'%';
            }
            
            if (!empty($descricao)) {
                $pesquisa['descricao like'] = '%'.$descricao.'%';
            }
            
            if (!empty($referencia)) {
                $pesquisa['referencia like'] = '%'.$referencia.'%';
            }
            
            if (in_array($filtro, ['S','N'])) {                
                //auditado nos ultimos 10 dias
                if ($filtro === 'S') {                    
                    $pesquisa['auditado'] = 'S';
                    $pesquisa['DATEDIFF(CURRENT_DATE, DATA_HORA_AUDITADO) <='] = 10;
                }                
                else {
                    $pesquisa['auditado'] = 'N';
                }
            }
            
            $data = $this->modelo->pesquisar($pesquisa);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registros encontrados: '. is5_count($data);
                
                foreach($data as $d) {
                    $d->DATA_HORA_AUDITADO = dateToBr($d->DATA_HORA_AUDITADO);
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
            $NCM           = trim($this->input->post('NCM'));            
            $DESCRICAO     = trim($this->input->post('DESCRICAO'));            
            $MEDIDA        = trim($this->input->post('MEDIDA'));
            $REFERENCIA    = somenteNumeros($this->input->post('REFERENCIA'));
                          
            if (empty($NCM)) {
                throw new Exception('O campo NCM/Código é obrigatório.');
            }
            
            if (empty($DESCRICAO)) {
                throw new Exception('O campo Nome é obrigatório.');
            }
            
            if ($this->modelo->check_referencia($REFERENCIA, $ID)) {
                throw new Exception("A Referência $NCM já está cadastrada no sistema.");
            }
                                    
            $data = [
                //'ID'       => addslashes($ID),
                'NCM'        => addslashes($NCM),
                'DESCRICAO'  => addslashes(is5_strtoupper($DESCRICAO)),
                'MEDIDA'     => addslashes(is5_strtoupper($MEDIDA)),
                'REFERENCIA' => addslashes($REFERENCIA),                
            ];
            
            $ret = $this->modelo->gravar($data, $ID);
            if ($ret) {
                $status = TRUE;
                $msg = 'Registro gravado com sucesso!';
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
        $msg = NULL;        
        try {   
            $id = (int)$this->uri->segment(3);
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $resp = $this->modelo->excluir($id);
            
            if ($resp) {
                $status = TRUE;
                $msg    = 'Registro excluído com sucesso!';                
            } else {
                throw new Exception('Erro ao excluir registro.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }
    
    function getNCM() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            //pode_ler($this->CONTROLE);
            
            $ncm = $this->input->get('ncm');           
            if (!$ncm) {
                throw new Exception('Registro não especificado.');
            }
            
            $data = $this->ncm->getByCodigo($ncm);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registro encontrado';  
                
                $this->valoresToBr($data);
                
            } else {
                throw new Exception("NCM $ncm não encontrado.");
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
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
    }
    
}