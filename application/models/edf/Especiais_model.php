<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Especiais_model extends CI_Model {
    
    private $tabela = 'edf_diario_especiais';
    private $view = 'vw_edf_diario_especiais';
    
    function __construct() {
        parent::__construct();
    }
    
    public function get($id, $modelo = null) {
        $this->db->where('id', $id);
        $this->db->where(['emitente_id' => (int)$this->session->userdata('emit.id')]);
        
        $g = $this->db->get($this->tabela);
        
        if ($g->num_rows()) 
        {
            if ($modelo === "array") 
            {
                //retorno como array para facilitar o transporte para a view ao editar :)
                return $g->result_array()[0];
            }
            else 
            {
                return $g->result_object()[0];
            }            
        }
        else 
        {
            return FALSE;
        }
    }
        
    public function getAll() {       
        $this->db->where(['emitente_id' => (int)$this->session->userdata('emit.id')]);
        
        $all = $this->db->order_by('id')->get($this->view);
        if ($all->num_rows()) {
            return $all->result_object();        
        }else {
            return FALSE;
        }
    }
    
    public function gravar($dados, $id = null) {
        //update
        if ($id) 
        {   
            $ret = $this->db->update($this->tabela, $dados, [ 'id' => $id ]);
            if ($ret) {                
                return $id;                
            }
            return FALSE;            
        }
        //insert
        else 
        {              
            $dados['emitente_id'] = (int)$this->session->userdata('emit.id');            
            
            $ret = $this->db->insert($this->tabela, $dados);
            if ($ret) {
                $ret_id = $this->db->insert_id();
                return $ret_id;
            }
            return FALSE;
        }
    }
    
    public function pesquisar($pesquisa) {        
        $this->db->where(['emitente_id' => $this->session->userdata('emit.id')]);  
        
        $retorno = $this->db->where($pesquisa)
                            ->order_by('id')
                            ->get($this->view);
        
        if ($retorno->num_rows()) {
            return $retorno->result_object();
        }else {
            return FALSE;
        }
    } 
    
    public function excluir($id = null){
        if ($id) {            
            if (!$this->podeExcluir($id)) {
                throw new Exception('Impossível excluir registro. Existem permissões vinculadas.');
            }
            
            $this->db->where(['emitente_id' => $this->session->userdata('emit.id')]); 
            
            return $this->db->where('id', $id)
                            ->delete($this->tabela);
        }
    }
    
    
    private function podeExcluir($id) {
        //somente permite excluir se nao tem permissao vinculado
        //return $this->db->where(['emitente_id' => $this->session->userdata('emit.id'), 'filial_id' => (int)$id])
        //                ->count_all_results('adm_usuarios') === 0;
        return TRUE;
    }
    

    function getByFp($forma_pag_id, $diario_id) {
        try {
            $this->db->where(['emitente_id' => $this->session->userdata('emit.id')]); 
            
            $resp = $this->db->where(['diario_id' => (int)$diario_id, 'forma_pag_id' => (int)$forma_pag_id])
                             ->get('edf_diario_especiais');
            
            if ($resp->num_rows()) {
                return $resp->result_object()[0];
            }
            
        } catch (Exception $ex) {
            //
        }
        return NULL;
    }
    
}


