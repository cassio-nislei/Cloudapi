<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Atendimentos extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
        
        $this->load->model('Atendimentos_model', 'modelo');        
    }
    
    function get() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            $id = (int)$this->uri->segment(3);            
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $data = $this->modelo->get($id);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registro encontrado';                
            } else {
                throw new Exception('Nenhum registro encontrado.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    function getAll() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {       
            $id_pessoa = (int)$this->input->get('id_pessoa');
            
            if (!$id_pessoa) {
                throw new Exception('Pessoa não especificada.');
            }
            
            $data = $this->modelo->pesquisar(['PESSOA_ID' => $id_pessoa]);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registros encontrados: '. is5_count($data);
                
                foreach($data as $d) {
                    $d->CREATED_AT = dateToBr($d->CREATED_AT);
                    $d->UPDATED_AT = dateToBr($d->UPDATED_AT);
                }
                
            } else {
                throw new Exception('Nenhum registro encontrado.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    } 
    
    function salvar() {
        $status = FALSE;
        $msg = NULL;
        try {           
            $id         = (int)$this->input->post('id');
            $pessoa_id  = (int)$this->input->post('pessoa_id');
            $texto      = $this->input->post('texto');
            
            $data = [
                'TEXTO'     => addslashes($texto),
                'PESSOA_ID' => $pessoa_id,
            ];
                        
            $data[$id > 0 ? 'UPDATED_BY' : 'CREATED_BY'] = (int)$this->session->userdata('user.ID');
            $data[$id > 0 ? 'UPDATED_AT' : 'CREATED_AT'] = getDateTimeCurrent();                
            
            $resp = $this->modelo->gravar($data, $id);
            if (!$resp) {
                throw new Exception('Erro ao salvar dados. Tente novamente.');
            }
            
            $status = TRUE;
            $msg = 'Registro salvo com sucesso!';
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }
    
    function excluir() {
        $status = FALSE;
        $msg = NULL;        
        try {            
            $id = (int)$this->uri->segment(3);
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $resp = $this->modelo->excluir($id);
            
            if ($resp) {
                $status = TRUE;
                $msg    = 'Registro excluído com sucesso!';                
            } else {
                throw new Exception('Erro ao excluir registro.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }
    
    function getPermissoes() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {            
            $grupo_id = (int)$this->uri->segment(3);
            
            if (!$grupo_id) {
                throw new Exception('Grupo não especificado.');
            }
            
            $data = $this->permissoes->pesquisar(['grupo_id' => $grupo_id]);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registros encontrados: '. is5_count($data);
                $data   = $data;
            } else {
                throw new Exception('Nenhum registro encontrado.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    
    
    
    
    
}


