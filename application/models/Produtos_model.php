<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'models/Pessoa.php';

class Produtos_model extends CI_Model {
    
    private $tabela = 'PRODUTOS';
    private $view = 'PRODUTOS';
    
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
        $all = $this->db->get($this->view);
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
                            ->order_by('DESCRICAO')
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
    
    public function check_referencia($referencia, $id = 0) {                
        return $this->db->where(['REFERENCIA' => somenteNumeros($referencia), 'ID !=' => (int)$id])
                        ->count_all_results($this->tabela) > 0;
    }
    
    public function getByCodigo($codigo) {
        try {
            $resp = $this->db->where(['CODIGO' => somenteNumeros($codigo)])
                             ->get($this->tabela);
            
            if ($resp->num_rows()) {
                return $resp->result_object()[0];
            }
            
        } catch (Exception $ex) {
            //
        }
        return NULL;
    }
    
    public function getByReferencia($referencia, $array = FALSE) {
        try {
            $resp = $this->db->where(['REFERENCIA' => somenteNumeros($referencia)])
                             ->get($this->tabela);
            
            if ($resp->num_rows()) {
                return $array ? $resp->result_array()[0] : $resp->result_object()[0];
            }
            
        } catch (Exception $ex) {
            //
        }
        return NULL;
    }
    
        
}



