<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apresentacoes extends CI_Controller {
    
    private $CONTROLE = 'relatorios';
    
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
        
        $this->load->model('edf/Apresentacoes_model', 'modelo'); 
        $this->load->model('edf/Diarios_model', 'diarios');
    }
    
    function get() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            pode_ler($this->CONTROLE);
            
            $id = (int)$this->uri->segment( $this->uri->total_segments() );            
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $data = $this->modelo->get($id);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registro encontrado';     
                
                $data->data = dateToBr($data->data);
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
            pode_ler($this->CONTROLE);
            
            $diario_id = (int)$this->input->get('diario_id');
            if ($diario_id) {
                $data = $this->modelo->pesquisar(['diario_id' => $diario_id]);
            } else {            
                $data = $this->modelo->getAll();
            }
            
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
        $data = NULL;
        try {         
            pode_ler($this->CONTROLE);
            
            $registro = $this->input->post('registro');            
            if (!$registro) {
                throw new Exception('Registro não especificado.');
            }
            
            $id        = is_seted($registro, 'id') ? (int)$registro['id'] : 0;   
            $nome      = isset($registro['nome']) ? $registro['nome'] : '';
            $nome_pai  = isset($registro['nome_pai']) ? $registro['nome_pai'] : '';
            $nome_mae  = isset($registro['nome_mae']) ? $registro['nome_mae'] : '';
            $diario_id = isset($registro['diario_id']) ? (int)$registro['diario_id'] : 0;
            
            if (empty($nome) || empty($nome_mae)) {
                throw new Exception('Especifique o nome da criança e o nome da mãe.');
            }
            
            if (!$diario_id) {
                throw new Exception('Relatório não especificado.');
            }
            
            $diario = $this->diarios->get($diario_id);
            if (!$diario) {
                throw new Exception('Relatório não encontrado.');
            }
            
            $dados = [
                'diario_id' => $diario_id,
                'nome'      => addslashes($nome),
                'nome_pai'  => addslashes($nome_pai),
                'nome_mae'  => addslashes($nome_mae),                
            ];
            
            if (!$id) {
                $dados['filial_id'] = (int)$diario->filial_id;
            }
            
            $resp = $this->modelo->gravar($dados, $id);            
            if (!$resp) {
                throw new Exception('Erro ao salvar dados. Tente novamente.');
            }
            
            $status = TRUE;
            $msg = 'Registro salvo com sucesso!';
            $data['id'] = $resp;
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    function excluir() {
        $status = FALSE;
        $msg = NULL;        
        try {   
            pode_excluir($this->CONTROLE);
            
            $id = (int)$this->input->post('id');
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


