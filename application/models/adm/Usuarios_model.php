<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_model extends CI_Model {
    
    private $tabela = 'adm_usuarios';
    private $view = 'adm_usuarios';
    
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
        $all = $this->db->order_by('grupo, nome')->get($this->view);
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
    
    public function excluir($id = null) {
        if ($id) {            
            $this->db->where(['emitente_id' => $this->session->userdata('emit.id')]);
            $resp = $this->db->where('id', $id)
                             ->delete($this->tabela);
            
            if ($resp) {
                //se excluiu, apaga permissoes vinculadas
                $this->db->where(['emitente_id' => $this->session->userdata('emit.id'), 'usuario_id' => (int)$id])
                         ->delete('adm_permissoes');
            }
            
            return $resp;
        }
    }
    
    public function pesquisar($pesquisa) {        
        $this->db->where(['emitente_id' => $this->session->userdata('emit.id')]);
        $retorno = $this->db->where($pesquisa)
                            ->order_by('grupo, nome')
                            ->get($this->view);
        
        if ($retorno->num_rows()) {
            return $retorno->result_object();
        }else {
            return FALSE;
        }
    } 
    
    public function check_celular($telefone, $id) {
        $telefone = somenteLetrasNumeros($telefone);
        $this->db->where(['emitente_id' => (int)$this->session->userdata('emit.id')]);
        return $this->db->where(['telefone' => $telefone, 'id !=' => (int)$id])
                        ->count_all_results('adm_usuarios') > 0;
    }
    
    public function check_email($email, $id) {
        $email = is5_strtolower(trim($email));
        $this->db->where(['emitente_id' => (int)$this->session->userdata('emit.id')]);
        return $this->db->where(['email' => $email, 'id !=' => (int)$id])
                        ->count_all_results('adm_usuarios') > 0;
    }
    
    
        
}


