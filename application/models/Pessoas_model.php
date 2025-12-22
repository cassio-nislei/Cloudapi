<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'models/Pessoa.php';

class Pessoas_model extends CI_Model {
    
    private $tabela = 'PESSOAS';
    private $view = 'PESSOAS';
    
    function __construct() {
        parent::__construct();
    }
    
    public function get($id, $modelo = null) {
        $this->db->where('ID_PESSOA', $id);        
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
        $all = $this->db->order_by('nome')->get($this->view);
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
            $ret = $this->db->update($this->tabela, $dados, [ 'ID_PESSOA' => $id ]);
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
            return $this->db->where('ID_PESSOA', $id)
                            ->delete($this->tabela);
        }
    }
    
    public function check_nome($nome, $id) {                
        return $this->db->where(['lower(NOME)' => addslashes(is5_strtolower(trim($nome))), 'ID_PESSOA !=' => (int)$id])
                        ->count_all_results($this->tabela) > 0;
    }
    
    public function check_cgc($cgc, $id) {                
        return $this->db->where(['CGC' => somenteNumeros($cgc), 'ID_PESSOA !=' => (int)$id])
                        ->count_all_results($this->tabela) > 0;
    }
    
    
    public function getByCgc($cgc) {
        try {
            $resp = $this->db->where(['CGC' => somenteNumeros($cgc), "coalesce(CGC,'') !=" => ''])
                             ->get($this->tabela);
            
            if ($resp->num_rows()) {
                return $resp->result_object()[0];
            }
            
        } catch (Exception $ex) {
            //
        }
        return NULL;
    }
    
    function setVersaoFbxPdv($id_pessoa, $fbx, $pdv) {
        try {
            $dados = NULL;
            
            if (!embranco($fbx)) {
                $dados['VERSAO_FBX'] = addslashes($fbx);
            }
            
            if (!embranco($pdv)) {
                $dados['VERSAO_PDV'] = addslashes($pdv);
            }
            
            if ($dados) {
                $this->gravar($dados, (int)$id_pessoa);
            }
            
        } catch (Exception $ex) {
            //
        }
    }
    
    function setUltimoAcesso($id_pessoa) {
        try {
            $this->gravar(['ULTIMO_ACESSO' => getDateTimeCurrent()], (int)$id_pessoa);
        } catch (Exception $ex) {

        }
    }
    
    public function arrayToPessoa($arr) {
        //dados que vem da API
        $p = new Pessoa();
        try {
            $p->bairro      = isset($arr['bairro']) ? $arr['bairro'] : '';
            $p->cep         = isset($arr['cep']) ? $arr['cep'] : '';
            $p->cgc         = isset($arr['cgc']) ? $arr['cgc'] : '';
            $p->complemento = isset($arr['complemento']) ? $arr['complemento'] : '';
            $p->contato     = isset($arr['contato']) ? $arr['contato'] : '';
            $p->email       = isset($arr['email']) ? $arr['email'] : '';
            $p->fantasia    = isset($arr['fantasia']) ? $arr['fantasia'] : '';
            $p->endereco    = isset($arr['logradouro']) ? $arr['logradouro'] : '';
            $p->cidade      = isset($arr['municipio']) ? $arr['municipio'] : '';
            $p->numero      = isset($arr['numero']) ? $arr['numero'] : '';
            $p->nome        = isset($arr['razao']) ? $arr['razao'] : '';
            $p->telefone    = isset($arr['telefone']) ? $arr['telefone'] : '';
            $p->estado      = isset($arr['uf']) ? $arr['uf'] : '';
            $p->celular     = isset($arr['whatsapp']) ? $arr['whatsapp'] : ''; 
            $p->chave_a     = isset($arr['chave_a']) ? $arr['chave_a'] : ''; 
            $p->chave_b     = isset($arr['chave_b']) ? $arr['chave_b'] : ''; 
            $p->ativo       = isset($arr['ativo']) ? $arr['ativo'] : ''; 
                    
        } catch (Exception $ex) {
            //
        }
        return $p;
    }
    
    public function pessoaToArray($p) {
        //dados q vao para o banco
        $a = [];
        try {
            $a['id_emitente']  = 6;
            $a['bairro']       = substr($p->bairro, 0, 35);
            $a['cep']          = substr(somenteNumeros($p->cep), 0, 10);
            $a['cgc']          = substr(somenteNumeros($p->cgc), 0, 20);
            $a['complemento']  = substr($p->complemento, 0, 35);
            $a['nome_contato'] = substr($p->contato, 0, 35);
            $a['email']        = substr($p->email, 0, 50);
            $a['fantasia']     = substr($p->fantasia, 0, 50);
            $a['endereco']     = substr($p->endereco, 0, 50);
            $a['cidade']       = substr($p->cidade, 0, 35);
            $a['numero']       = substr($p->numero, 0, 10);
            $a['nome']         = substr($p->nome, 0, 50);
            $a['telefone']     = substr(somenteNumeros($p->telefone), 0, 15);
            $a['estado']       = substr($p->estado, 0, 2);
            $a['celular']      = substr(somenteNumeros($p->celular), 0, 15); 
            $a['contato_cel']  = substr(somenteNumeros($p->celular), 0, 15);
            $a['ativo']        = substr($p->ativo, 0, 1);
            
            $a['data_cadastro'] = $p->data_cadastro;
            $a['licencas']      = $p->licencas;
            $a['cont_licencas'] = $p->cont_licencas;
            $a['periodo']       = $p->periodo;
            $a['expira_em']     = $p->expira_em;  
            
            $a['auto_install'] = $p->auto_install;
            $a['data_install'] = $p->data_install;
            
        } catch (Exception $ex) {
            //
        }
        return $a;
    }
    
    public function validar($pessoa) {
        $msg = '';
        try {
            //verificar campos obrigatorios
            if ($pessoa->cgc == '') {
                throw new Exception('O campo CNPJ/CPF é obrigatório.');
            }
            
            if (!valida_cgc($pessoa->cgc)) {
                throw new Exception('Informe um CNPJ/CPF válido.');
            }
            
            //if ($pessoa->chave_a == '') {
            //    throw new Exception('Chave A não especificada.');
            //}
            
            if ($pessoa->contato == '' || $pessoa->celular == '') {
                throw new Exception('Informe os dados para contato (nome, WhatsApp).');
            }
            
            //verificar se o CNPJ jah esta cadastrado
            $r = $this->db->where(['CGC' => $pessoa->cgc])->count_all_results($this->tabela);
            if ($r) {
                throw new Exception('O CNPJ/CPF já está cadastrado no sistema.');
            }
            
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
        }
        return $msg;        
    }
    
    
        
}



