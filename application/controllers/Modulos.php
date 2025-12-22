<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modulos extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
        
        $this->load->model('adm/Modulos_model', 'modelo');
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
            
            $id        = is_seted($registro, 'id')        ? (int)$registro['id'] : NULL;            
            $descricao = is_seted($registro, 'descricao') ? $registro['descricao'] : '';
            $controle  = is_seted($registro, 'controle')  ? $registro['controle'] : '';            
            $acao      = is_seted($registro, 'acao')      ? $registro['acao'] : '';
            
            if (empty($controle) || empty($acao)) {
                throw new Exception('Especifique Controle e Ação.');
            }
            
            if ($this->modelo->check_controle($controle, $id)) {
                throw new Exception('O Controle especificado já está cadastrado.');
            }
            
            $data = [                   
                'controle'  => addslashes($controle),
                'acao'      => addslashes($acao),
                'descricao' => addslashes($descricao)
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
    
    
    
    
    
    
}


