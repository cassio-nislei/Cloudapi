<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissoes_model extends CI_Model {
    
    private $tabela = 'adm_permissoes';
    private $view = 'vw_permissoes';
    
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
            $this->db->where(['emitente_id' => $this->session->userdata('emit.id')]);
            return $this->db->where('id', $id)
                            ->delete($this->tabela);
        }
    }
    
    public function pesquisar($pesquisa, $modelo = NULL) {        
        $this->db->where(['emitente_id' => $this->session->userdata('emit.id')]);
        $retorno = $this->db->where($pesquisa)
                            ->order_by('controle, acao')
                            ->get($this->view);
        
        if ($retorno->num_rows()) {
            if ($modelo === 'array') {
                return $retorno->result_array();
            } else {
                return $retorno->result_object();
            }                        
        }else {
            return FALSE;
        }
    } 
    
    public function check_grupo_vinculado($modulo_id, $grupo_id) {        
        $this->db->where(['emitente_id' => (int)$this->session->userdata('emit.id')]);
        return $this->db->where(['modulo_id' => (int)$modulo_id,                                  
                                 'grupo_id' => (int)$grupo_id])
                        ->count_all_results($this->view) > 0;
    }
    
    public function check_usuario_vinculado($modulo_id, $usuario_id) {        
        $this->db->where(['emitente_id' => (int)$this->session->userdata('emit.id')]);
        return $this->db->where(['modulo_id'  => (int)$modulo_id, 
                                 'usuario_id' => (int)$usuario_id])
                        ->count_all_results($this->view) > 0;
    }
    
    
    public function setPermissoesUsuario($usuario_id, $grupo_id) {        
        //retorna permissoes do grupo
        $permissoes = $this->pesquisar(['grupo_id' => (int)$grupo_id]);
        if (!$permissoes) {
            throw new Exception('Permissões não encontradas.');
        }

        //exclui permissoes do usuario            
        $resp = $this->db->where(['usuario_id'  => (int)$usuario_id,
                                  'emitente_id' => (int)$this->session->userdata('emit.id')])
                         ->delete('adm_permissoes');

        if (!$resp) {
            throw new Exception('Erro ao excluir permissões.');
        }

        //cadastra permissoes do usuario igual ao do grupo.
        //para isso, anulo o grupo e seto o usuario
        foreach($permissoes as $p) {                
            $dados = [
                'grupo_id'   => 0,
                'usuario_id' => $usuario_id,
                'modulo_id'  => $p->modulo_id,                
                'ler'        => $p->ler,
                'gravar'     => $p->gravar,
                'excluir'    => $p->excluir,                    
            ];
            $this->gravar($dados);                        
        }          
              
    }
    
         
}


