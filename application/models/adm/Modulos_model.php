<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modulos_model extends CI_Model {
    
    private $tabela = 'adm_modulos';
    private $view = 'adm_modulos';
    
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
        $all = $this->db->order_by('controle, acao')->get($this->view);
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
    
    public function excluir($id = null){
        if ($id) {            
            if (!$this->podeExcluir($id)) {
                throw new Exception('Impossível excluir Módulo. Existem permissões vinculadas.');
            }
            
            $this->db->where(['emitente_id' => $this->session->userdata('emit.id')]);
            return $this->db->where('id', $id)
                             ->delete($this->tabela);
        }
    }
    
    private function podeExcluir($id) {
        //somente permite excluir se nao tem permissao vinculado
        return $this->db->where(['emitente_id' => $this->session->userdata('emit.id'), 'modulo_id' => (int)$id])
                        ->count_all_results('adm_permissoes') === 0;
    }
    
    public function pesquisar($pesquisa) {        
        $this->db->where(['emitente_id' => $this->session->userdata('emit.id')]);
        $retorno = $this->db->where($pesquisa)
                            ->order_by('controle, acao')
                            ->get($this->view);
        
        if ($retorno->num_rows()) {
            return $retorno->result_object();
        }else {
            return FALSE;
        }
    } 
    
    public function check_controle($controle, $id) {        
        $this->db->where(['emitente_id' => (int)$this->session->userdata('emit.id')]);
        return $this->db->where(['controle' => addslashes($controle), 'id !=' => (int)$id])
                        ->count_all_results($this->tabela) > 0;
    }
    
    
        
}


