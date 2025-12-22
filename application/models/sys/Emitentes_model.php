<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emitentes_model extends CI_Model {
    
    private $tabela = 'sys_emitentes';
    private $view = 'sys_emitentes';
    
    function __construct() {
        parent::__construct();
    }
    
    public function get($id, $modelo = null) {
        $this->db->where('id', $id);
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
        
    public function getAll($usuario_id = null) {
        if ($usuario_id) {
            $this->db->where(['usuario_id' => $usuario_id]);
        }
        //$this->db->where(['emitente_id' => (int)$this->session->userdata('emitente_id')]);
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
            $ret = $this->db->update($this->tabela, $dados, [ 'id' => $id ]);
            if ($ret) {                
                return $id;                
            }
            return FALSE;            
        }
        //insert
        else 
        {
            //$dados['emitente_id'] = (int)$this->session->userdata('emitente_id');
            //$dados['home'] = '/home/'.uniqid( somenteNumeros($dados['cgc']).'_' );            
            $dados['home'] = '/home/'.uniqid( somenteLetrasNumeros($dados['alias']).'_' );            
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
            $this->db->where(['emitente_id' => $this->session->userdata('emitente_id')]);
            return $this->db->where('id', $id)
                            ->delete($this->tabela);
        }
    }
    
    public function pesquisar($pesquisa, $usuario_id = null) {
        //$this->db->where($pesquisa);
        //$retorno = $this->db->get('vw_posts');
        if ($usuario_id) {
            $pesquisa[] = ['usuario_id' => $usuario_id];
        }
        //$this->db->where(['emitente_id' => $this->session->userdata('emitente_id')]);
        $retorno = $this->db->where($pesquisa)
                            ->order_by('id')
                            ->get($this->view);
        
        if ($retorno->num_rows()) {
            return $retorno->result_object();
        }else {
            return FALSE;
        }
    } 
    
    public function validar($emitente, $id) {
        $msg = '';
        try {
            if (!is_seted($emitente, 'nome')) {
                $msg = 'Nome não especificado. ';                
            }
            
            if (!is_seted($emitente,'cgc')) {
                $msg .= 'CPF/CNPJ não especificado. ';
            }
            
            if (!is_seted($emitente,'alias')) {
                $msg .= 'URL não especificada. ';
            }
            
            if ($this->cgc_cadastrado($emitente['cgc'], $id)) {
                throw new Exception('O CPF/CNPJ informado já está cadastrado.');
            }
                        
            if (!is_seted($emitente,'email')) {
                $msg .= 'E-mail não especificado. ';
            }            
            
            if ($this->email_cadastrado($emitente['email'], $id)) {
                throw new Exception('O e-mail informado já está cadastrado.');
            }
            
            if ($this->alias_cadastrado($emitente['alias'], $id)) {
                throw new Exception('O endereço da URL já está cadastrado.');
            }
            
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
        }        
        return $msg;
    }
    
    public function cgc_cadastrado($cgc, $id) {
        $cgc = somenteNumeros($cgc);
        
        return $this->db->where(['cgc' => $cgc, 'id !=' => $id])
                        ->count_all_results($this->tabela) > 0;
    }
    
    public function email_cadastrado($email, $id) {
        $email = is5_strtolower($email);
        
        return $this->db->where(['lower(email)' => $email, 'id !=' => $id])
                        ->count_all_results($this->tabela) > 0;
        
        
    }
    
    public function alias_cadastrado($alias, $id) {
        $alias = is5_strtolower($alias);
        
        return $this->db->where(['lower(alias)' => $alias, 'id !=' => $id])
                        ->count_all_results($this->tabela) > 0;
    }
    
    public function getCGC($id) {
        $resp = $this->db->select('cgc')
                         ->where(['id' => (int)$id])
                         ->get('sys_emitentes');
        
        if ($resp->num_rows()) {
            $obj = $resp->result_object()[0];
            return somenteNumeros( $obj->cgc );
        }
        return NULL;
    }
    
    
}
