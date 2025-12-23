<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grupos extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {
            // Se for requisição AJAX, retorna JSON
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => FALSE, 'msg' => 'Não autenticado', 'data' => []]);
                exit;
            }
            redirect(base_url('Account/login'));
        }
        
        $this->load->model('adm/Grupos_model', 'modelo');
        $this->load->model('adm/Permissoes_model', 'permissoes');
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
            $data = $this->modelo->getAll();
            
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
    
    function salvar() {
        $status = FALSE;
        $msg = NULL;
        try {            
            $registro = $this->input->post('registro');            
            if (!$registro) {
                throw new Exception('Dados não especificados.');
            }
            
            $id        = is_seted($registro, 'id')        ? (int)$registro['id'] : NULL;
            $nome      = is_seted($registro, 'nome')      ? $registro['nome'] : '';
            $descricao = is_seted($registro, 'descricao') ? $registro['descricao'] : '';              
            
            if (empty($nome)) {
                throw new Exception('Especifique o nome do Grupo.');
            }
            
            if ($this->modelo->check_nome($nome, $id)) {
                throw new Exception('O Nome especificado já está cadastrado.');
            }
            
            $data = [
                'nome'      => addslashes($nome),                
                'descricao' => addslashes($descricao),                
            ];
            
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


