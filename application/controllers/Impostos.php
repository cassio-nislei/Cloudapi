<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Impostos extends CI_Controller {
    
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
    }
    
    private function getTabela($tabela) {
        $data = [];
        try {
            $resp = $this->db->order_by('id')
                             ->get($tabela);
            
            if ($resp->num_rows()) {
                $data = $resp->result_object();
            }             
        } catch (Exception $ex) {
            //
        }
        return $data;
    }
    
    function cest() {        
        //echo json_encode( $this->getTabela('cest') ); //NAO RETORNO ECHO PQ IGONARARIA O CACHE (?)
        //$this->output->cache(self::TEMPO_CACHE);
        //RETORNO EM VIEW PARA CONSIDERAR O CACHE
        $this->load->view('Tabela', ['data' => $this->getTabela('CEST')]);
    }
    
    function cfop() {
        //echo json_encode( $this->getTabela('cfop') );
        //$this->output->cache(self::TEMPO_CACHE);
        $this->load->view('Tabela', ['data' => $this->getTabela('CFOP')]);
    }
    
    function cfop_externo() {
        $this->cfop();
    }
    
    function csosn() {        
        //echo json_encode( $this->getTabela('csosn') );
        //$this->output->cache(self::TEMPO_CACHE);
        $this->load->view('Tabela', ['data' => $this->getTabela('csosn')]);
    }
    
    function medida() {        
        //echo json_encode( $this->getTabela('medida') );
        //$this->output->cache(self::TEMPO_CACHE);
        $this->load->view('Tabela', ['data' => $this->getTabela('medida')]);
    }
    
    function ncm() {        
        //echo json_encode( $this->getTabela('ncm') );
        //$this->output->cache(self::TEMPO_CACHE);
        
        $data = [];
        
        $resp = $this->db->select('NCM as CODIGO, DESCRICAO')
                         ->where(['NCM !=' => NULL])
                         ->order_by('NCM')
                         ->get('NCM_DETALHE');
        
        if ($resp->num_rows()) {
            $data = $resp->result_object();
        }
        
        $this->load->view('Tabela', ['data' => $data]);
    }
    
    function origem() {        
        //echo json_encode( $this->getTabela('origem') );
        //$this->output->cache(self::TEMPO_CACHE);
        $this->load->view('Tabela', ['data' => $this->getTabela('origem')]);
    }
    
    function pagamento() {        
        //echo json_encode( $this->getTabela('pagamento_tipos') );
        //$this->output->cache(self::TEMPO_CACHE);
        $this->load->view('Tabela', ['data' => $this->getTabela('pagamento_tipos')]);
    }
    
    function tipo() {                
        //echo json_encode( $this->getTabela('produto_tipos') );
        //$this->output->cache(self::TEMPO_CACHE);
        $this->load->view('Tabela', ['data' => $this->getTabela('produto_tipos')]);
    }
    
    function clear() {
        $this->output->delete_cache(); 
        echo "OK";
    }
    
}

