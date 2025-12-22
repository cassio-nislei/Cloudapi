<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diario_alteracoes_model extends CI_Model {
    
    private $tabela = 'edf_diario_alteracoes';
    private $view = 'vw_edf_diario_alteracoes';
    
    function __construct() {
        parent::__construct();
    }
    
    public function get($id, $modelo = null) {
        $this->db->where('id', $id);
        $this->db->where(['emitente_id' => (int)$this->session->userdata('emit.id'), 
                          'filial_id'   => $this->session->userdata('user.filial_id')]);
        
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
        $this->db->where(['emitente_id' => (int)$this->session->userdata('emit.id'),
                          'filial_id'   => $this->session->userdata('user.filial_id')]);
        
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
            $dados['dh_edit'] = getDateTimeCurrent();
            $dados['user_edit_id'] = (int)$this->session->userdata('emit.id');
            
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
            $dados['filial_id']   = (int)$this->session->userdata('user.filial_id');
            
            $dados['dh_insert'] = getDateTimeCurrent();
            $dados['user_insert_id'] = (int)$this->session->userdata('emit.id');
            
            $ret = $this->db->insert($this->tabela, $dados);
            if ($ret) {
                $ret_id = $this->db->insert_id();
                return $ret_id;
            }
            return FALSE;
        }
    }
    
    public function pesquisar($pesquisa) {        
        $this->db->where(['emitente_id' => $this->session->userdata('emit.id'),
                          'filial_id'   => $this->session->userdata('user.filial_id')]);  
        
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
            
            $this->db->where(['emitente_id' => $this->session->userdata('emit.id'),
                              'filial_id'   => $this->session->userdata('user.filial_id')]); 
            
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
    
    private function get_descricao($tabela) {
        switch($tabela) {
            case 'edf_diario_dizimos'  : return 'dízimo';
            case 'edf_diario_ofertas'  : return 'oferta';
            case 'edf_diario_especiais': return 'oferta especial';
            case 'edf_diario_missoes'  : return 'oferta missões';            
        }
    }
    
    public function log($id, $data, $tabela = 'edf_diario_dizimos') {
        try {            
            $diario_id = (int)$data['diario_id'];
            $descricao = $this->get_descricao($tabela);
            $field     = ($tabela === 'edf_diario_dizimos') ? 'valor' : 'total';
            
            if (!$diario_id) {
                throw new Exception('Diário não especificado.');
            }            
            
            $this->load->model('edf/Diarios_model','diarios');            
            
            $diario = $this->diarios->get($diario_id);
            if (!$diario) {
                throw new Exception('Registro nao encontrado.');
            }            
            
            if ($diario->status !== 'C') {
                throw new Exception('Somente logar alteracoes em diarios confirmados.');
            }            
            
            $resp = $this->db->where(['id' => (int)$id])
                             ->get($tabela);            
            
            if ($resp->num_rows()) {                
                //jah existe, entao verifica se foi alterado
                $registro = $resp->result_object()[0];
                $historico = '';
                
                if ($tabela === 'edf_diario_dizimos') {
                    if ($registro->nome !== $data['nome']) {
                        $historico .= "Nome dizimista $registro->nome alterado para ".$data['nome'].'. ';
                    }
                    
                    if ((float)$registro->valor !== (float)$data[$field]) {
                        $historico .= "Valor R$ $registro->valor alterado para R$ ".$data[$field].'. ';
                    }
                } 
                else {
                    if ((float)$registro->total !== (float)$data[$field]) {
                        $historico .= "Valor R$ $registro->total alterado para R$ ".$data[$field].'. ';
                    } 
                }
                
                if ((int)$registro->forma_pag_id !== (int)$data['forma_pag_id']) {
                    $de   = edf_get_nome_fp($registro->forma_pag_id);
                    $para = edf_get_nome_fp($data['forma_pag_id']);
                    
                    if ($tabela === 'edf_diario_dizimos') {
                        $historico .= "Alterada forma de pagamento de $de para $para do dizimista ".is5_strtoupper($data['nome']).'. ';
                    } else {
                        $historico .= "Alterada forma de pagamento de $de para $para do valor R$ ".$data[$field].'. ';
                    }
                }
                
                if ($historico !== '') {
                    $dados = [    
                        'diario_id'      => (int)$diario_id,
                        'emitente_id'    => (int)$this->session->userdata('emit.id'),
                        'filial_id'      => (int)$this->session->userdata('user.filial_id'),
                        'historico'      => addslashes($historico),                        
                        'status'         => 'P',
                        'tabela'         => substr($tabela, 11),
                    ];
                    
                    $resp = $this->alteracoes->gravar($dados);
                    if (!$resp) {
                        throw new Excepion('Erro ao registrar alteração.');
                    }
                    
                    return $resp;
                }
                
            } else {                
                //nao existe, entao loga insert                
                $dados = [    
                    'diario_id'      => (int)$diario_id,
                    'emitente_id'    => (int)$this->session->userdata('emit.id'),
                    'filial_id'      => (int)$this->session->userdata('user.filial_id'), 
                    'status'         => 'P',
                    'tabela'         => substr($tabela, 11),
                ];

                if ($tabela === 'edf_diario_dizimos') {
                    $dados['historico'] = 'Novo dízimo cadastrado: '.$data['nome'].' - R$ '.$data['valor'];
                } else {
                    $dados['historico'] = 'Nova '.$this->get_descricao($tabela).' cadastrada no valor de R$ '.$data['total'];
                }
                
                $resp = $this->alteracoes->gravar($dados);
                if (!$resp) {
                    throw new Exception('Erro ao registrar inserção.');
                }

                return $resp;
            }                
            
        } catch (Exception $ex) {
            gravarLog($ex->getMessage());           
        }
        return 0;
    }
    
        
}


