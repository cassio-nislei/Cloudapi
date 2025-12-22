<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Especiais extends CI_Controller {
    
    private $CONTROLE = 'relatorios';
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
        
        $this->load->model('edf/Especiais_model', 'modelo'); 
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
        $total = 0.00;
        $total_fp = NULL;
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
                $total_fp = edf_totais_fp_especiais($diario_id);
                
                $total = 0.00;                
                foreach($data as $d) {
                    $total += $d->total;                    
                }                                
                
            } else {
                throw new Exception('Nenhum registro encontrado.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data, 'total' => $total, 'total_fp' => $total_fp]);
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
            
            $id           = is_seted($registro, 'id') ? (int)$registro['id'] : 0;   
            //$nome         = isset($registro['nome']) ? $registro['nome'] : '';
            $total        = isset($registro['total']) ? $registro['total'] : 0.00;
            $forma_pag_id = isset($registro['forma_pag_id']) ? (int)$registro['forma_pag_id'] : 0;
            $diario_id    = isset($registro['diario_id']) ? (int)$registro['diario_id'] : 0;
            
            //if (empty($nome)) {
            //    throw new Exception('Especifique o nome.');
            //}
            
            if ($total) {
                $total = valorToDb($total);                
            } else {
                throw new Exception('Especifique o valor da contribuição.');
            }
            
            if (!$forma_pag_id) {
                throw new Exception('Especifique a forma de pagamento.');
            }
            
            if (!$diario_id) {
                throw new Exception('Relatório não especificado.');
            }
            
            //se jah existe com essa FP, incrementa
            if (!$id) {
                $oferta = $this->modelo->getByFp($forma_pag_id, $diario_id);
                if ($oferta) {
                    $total += $oferta->total;
                    $id = $oferta->id;
                }
            }
            
            $diario = $this->diarios->get($diario_id);
            if (!$diario) {
                throw new Exception('Relatório não encontrado.');
            }
            
            $dados = [
                'diario_id'    => $diario_id,
                //'nome'       => addslashes($nome),
                'total'        => addslashes($total),
                'forma_pag_id' => $forma_pag_id,                
            ];
            
            if (!$id) {
                $dados['filial_id'] = $diario->filial_id;
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



