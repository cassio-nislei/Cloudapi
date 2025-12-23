<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissoes extends CI_Controller {
    
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
        
        $this->load->model('adm/Permissoes_model', 'modelo');
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
                throw new Exception('Registro não especificado');
            }
            
            $id         = is_seted($registro, 'id')          ? (int)$registro['id'] : NULL; 
            $modulo_id  = is_seted($registro, 'modulo_id')   ? (int)$registro['modulo_id'] : NULL; 
            $grupo_id   = is_seted($registro, 'grupo_id')    ? (int)$registro['grupo_id'] : NULL; 
            $usuario_id = is_seted($registro, 'usuario_id')  ? (int)$registro['usuario_id'] : NULL;             
            
            $ler     = is_seted($registro, 'ler')     ? $registro['ler']     : NULL;
            $gravar  = is_seted($registro, 'gravar')  ? $registro['gravar']  : NULL;            
            $excluir = is_seted($registro, 'excluir') ? $registro['excluir'] : NULL;
            
            if (!in_array($ler, ['S','N'])) {
                $ler = 'S';
            }
            
            if (!in_array($gravar, ['S','N'])) {
                $gravar = 'S';
            }
            
            if (!in_array($excluir, ['S','N'])) {
                $excluir = 'S';
            }
            
            if (!$modulo_id) {
                throw new Exception('Módulo não especificado.');                
            }
            
            if (!$grupo_id && !$usuario_id) {
                throw new Exception('Especifique Grupo ou Usuário.');
            }
            
            if ($grupo_id) {
                if ($this->modelo->check_grupo_vinculado($modulo_id, $grupo_id)) {
                    throw new Exception('O Controle/ação já está vinculado ao Grupo.');
                }
            }
            
            if ($usuario_id) {
                if ($this->modelo->check_usuario_vinculado($modulo_id, $usuario_id)) {
                    throw new Exception('O Controle/ação já está vinculado ao Usuário.');
                }
            }
            
            $data = [                   
                'modulo_id'  => addslashes($modulo_id),
                'grupo_id'   => addslashes($grupo_id),
                'usuario_id' => addslashes($usuario_id),
                'ler'        => addslashes($ler),
                'gravar'     => addslashes($gravar),
                'excluir'    => addslashes($excluir)
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
    
    function setUnset() {
        $status = FALSE;
        $msg = NULL;        
        try {            
            $id = (int)$this->input->post('id');
            $field = $this->input->post('field');
            
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            if (!in_array($field,['ler','gravar','excluir'])) {
                throw new Exception('Campo desconhecido');
            }
            
            $permissao = $this->modelo->get($id, 'array');
            
            if (!$permissao) {
                throw new Exception('Permissão não encontrada.');
            }
            
            if (!isset($permissao[$field])) {
                throw new Exception('Field não especificado.');
            }
            
            $permissao[$field] = ($permissao[$field] === 'S') ? 'N' : 'S';
            
            $resp = $this->modelo->gravar($permissao, $id);
            
            if ($resp) {
                $status = TRUE;
                $msg    = 'Registro gravado com sucesso!';                
            } else {
                throw new Exception('Erro ao salvar registro.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }
       
    
    function getModulosGrupo() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {          
            $grupo_id = (int)$this->input->get('grupo_id');
            if (!$grupo_id) {
                throw new Exception('Grupo não especificado.');
                
            }
            $data = $this->modelo->pesquisar(['grupo_id' => $grupo_id]);
            
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



