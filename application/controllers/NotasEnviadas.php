<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NotasEnviadas extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
        
        $this->load->model('NotasEnviadas_model', 'modelo');
    }
    
    function index() {          
        $data['index'] = 0;
        $data['content'] = $this->load->view('notasEnviadas/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);             
    }
    
    function get() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            //pode_ler($this->CONTROLE);
            
            $id = (int)$this->input->get('id');           
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
            //$data = $this->modelo->getAll();
            $data_inicial = $this->input->get('data_inicial');
            $data_final   = $this->input->get('data_final');
            $importado    = $this->input->get('importado');
            
            if (!in_array($importado,['S','N'])) {
                $importado = 'N';
            }
            
            $pesquisa = [
                'date(DATA_HORA) >=' => dateToDb($data_inicial),
                'date(DATA_HORA) <=' => dateToDb($data_final),
                'IMPORTADO' => $importado,
            ];
            
            $data = $this->modelo->pesquisar($pesquisa);
            
            if ($data) {                
                $status = TRUE;
                $msg    = 'Registros encontrados: '.count($data);
                
                foreach($data as $d) {
                    $d->CGC          = formata_cgc($d->CGC);
                    $d->DATA_HORA    = dateToBr($d->DATA_HORA);   
                    $d->DH_IMPORTADO = dateToBr($d->DH_IMPORTADO);   
                    $d->IMPORTADO    = $d->IMPORTADO === 'S' ? 'Sim' : 'Não';
                }
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }    
    
    function importar() {
        $status = FALSE;
        $msg = NULL;
        try {  
            $id = (int)$this->input->get('id');
            
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $notas = $this->modelo->get($id);
            if (!$notas) {
                throw new Exception('Registro não encontrado.');
            }
            
            list($status, $msg) = papion_importar_produtos_xml($notas->XML);
            
            if ($status) {
                $this->modelo->gravar([
                    'IMPORTADO'    => 'S',
                    'DH_IMPORTADO' => getDateTimeCurrent()
                ], $id);
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }    
    
    function getProdutos() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            $id = (int)$this->input->get('id');           
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $registro = $this->modelo->get($id);
            if (!$registro) {
                throw new Exception('Registro não encontrado.');
            }
            
            $data = papion_extrair_produtos_impostos($registro->XML);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registros encontrado: '.count($data);                
                
            } else {
                throw new Exception('Nenhum registro encontrado.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    function excluir() {
        $status = FALSE;
        $msg = '';        
        try {
            $id = (int)$this->uri->segment(3);
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $ncm = $this->modelo->get($id);
            if (!$ncm) {
                throw new Exception('Registro não encontrado.');
            }
            
            $count = $this->db->where(['NCM' => $ncm->NCM])
                              ->count_all_results('PRODUTOS');
            
            if ($count) {
                throw new Exception("Impossível excluir NCM. Existem $count produtos vinculados!");
            }
            
            $resp = $this->modelo->excluir($id);
             
            if ($resp) {
                $status = TRUE;
                $msg = 'Registro excluído com sucesso!';                
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

