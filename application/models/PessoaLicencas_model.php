<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PessoaLicencas_model extends CI_Model {
    
    private $tabela = 'PESSOA_LICENCAS';
    private $view = 'PESSOA_LICENCAS';
    
    function __construct() {
        parent::__construct();
    }
    
    public function get($id, $modelo = null) {
        $this->db->where('ID', $id);        
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
            $ret = $this->db->update($this->tabela, $dados, [ 'ID' => $id ]);
            if ($ret) {                
                return $id;                
            }
            return FALSE;            
        }
        //insert
        else 
        {   
            $ret = $this->db->insert($this->tabela, $dados);
            if ($ret) {
                $ret_id = $this->db->insert_id();
                return $ret_id;
            }
            return FALSE;
        }
    }
    
    public function pesquisar($pesquisa) {                
        $retorno = $this->db->where($pesquisa)
                            ->order_by('nome')
                            ->get($this->view);
        
        if ($retorno->num_rows()) {
            return $retorno->result_object();
        }else {
            return FALSE;
        }
    } 
    
    public function excluir($id = null){
        if ($id) {    
            return $this->db->where('ID', $id)
                            ->delete($this->tabela);
        }
    }
    
    public function check_nome($nome, $id) {                
        return $this->db->where(['lower(NOME)' => addslashes(is5_strtolower(trim($nome))), 'ID_PESSOA !=' => (int)$id])
                        ->count_all_results($this->tabela) > 0;
    }
    
    public function countLicencas($id_pessoa) {
        return $this->db->where(['ID_PESSOA' => (int)$id_pessoa, 'STATUS' => 'A'])
                        ->count_all_results($this->tabela);
    }
    
    public function countGUID($id_pessoa, $guid) {
        return $this->db->where(['ID_PESSOA' => (int)$id_pessoa, 'GUID' => addslashes($guid)])
                        ->count_all_results($this->tabela);
    }
    
    public function getByGUID($id_pessoa, $guid) {
        try {
            //status [A]tivado, [D]esativado
            $resp = $this->db->where(['ID_PESSOA' => (int)$id_pessoa, 'GUID' => addslashes($guid), 'STATUS' => 'A'])
                             ->get($this->tabela);
            
            if ($resp->num_rows()) {
                return $resp->result_object()[0];
            }
            
        } catch (Exception $ex) {
            //
        }
        return NULL;
    }
    
    public function getByIdPessoa($id_pessoa) {
        try {            
            $resp = $this->db->where(['ID_PESSOA' => (int)$id_pessoa])
                             ->get($this->tabela);
            
            if ($resp->num_rows()) {
                return $resp->result_object();
            }
            
        } catch (Exception $ex) {
            //
        }
        return NULL;
    }
    
        
}



