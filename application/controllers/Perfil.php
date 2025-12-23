<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
use Restserver\Libraries\REST_Controller;

class Perfil extends REST_Controller {
    
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
    }
    
    public function index_get() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            pode_ler('perfil');
            
            $id = (int)$this->session->userdata('user.ID');
            
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $resp = $this->db->select('id, emitente_id, grupo_id, nome, email, cgc, telefone')
                             ->where(['id' => $id, 'emitente_id' => $this->session->userdata('emit.id')])
                             ->get('adm_usuarios');
            
            if (!$resp->num_rows()) {
                throw new Exception('Nenhum registro encontrado.');
            }
            
            $data   = $resp->result_object()[0];
            $status = TRUE;
            $msg    = 'Registro encontrado.';
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        $this->response(['status' => $status, 
                         'msg'    => $msg,
                         'data'   => $data,
                        ], REST_Controller::HTTP_OK);
    }

    public function index_post() {
        try {
            pode_gravar('perfil');
            
            $action = $this->post('action');
            if ($action === 'update') {
                $this->update();
                return;
            } else if ($action === 'changepass') {
                $this->changePass();
                return;
            } else {
                throw new Exception('Ação desconhecida.');
            }
            
        } catch (Exception $ex) {
            $this->response(['status' => 'ERRO', 
                    'msg' => $ex->getMessage()
                   ], REST_Controller::HTTP_OK);
        }
    }
    
    private function update() {  
        $status = FALSE;
        $msg = NULL;        
        try {       
            pode_gravar('perfil');
            
            $id = (int)$this->session->userdata('user.ID');
            if (!$id) {
                throw new Exception('Permissão negada.');
            }
            
            $registro = $this->post('registro');
            if (!$registro) {
                throw new Exception('Registro não definido.');
            }
            
            $nome     = isset($registro['nome'])     ? $registro['nome']     : '';
            $cgc      = isset($registro['cgc'])      ? $registro['cgc']      : '';
            $telefone = isset($registro['telefone']) ? $registro['telefone'] : '';
            
            $cgc      = somenteLetrasNumeros($cgc);
            $telefone = somenteLetrasNumeros($telefone);
            
            if (empty($nome)) {
                throw new Exception('Especifique o nome.');
            }
                        
            if ($cgc) {
                if (!valida_cgc($cgc)) {
                    throw new Exception('Especifique um CPF/CNPJ válido.');
                }
                
                $jahExiste = $this->db->where(['id !=' => $id, 'cgc' => $cgc])
                                      ->count_all_results('adm_usuarios') > 0;
                
                if ($jahExiste) {
                    throw new Exception('O CPF/CNPJ especificado já está cadastrado no sistema.');
                }
            }
            
            $dados = [
                'nome'     => $nome,
                'cgc'      => $cgc,
                'telefone' => $telefone
            ];
            
            $resp = $this->db->update('adm_usuarios',$dados,['id' => $id]);
            if (!$resp) {                
                throw new Exception('Erro ao atualizar dados. Tente novamente.');
            }
            
            $status = TRUE;
            $msg = 'Registro atualizado.';
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        $this->response([
                          'status' => $status, 
                          'msg'    => 'Registro atualizado com sucesso!',                           
                         ], REST_Controller::HTTP_OK);
    }
    
    private function changePass() {
        $status = FALSE;
        $msg = NULL;
        try {
            $id = (int)$this->session->userdata('user.ID');
            if (!$id) {
                throw new Exception('Permissão negada.');
            }
            
            $atual   = trim($this->post('atual'));
            $nova    = trim($this->post('nova'));
            $confirm = trim($this->post('confirmacao'));
            
            if (empty($atual) || empty($nova) || empty($confirm)) {
                throw new Exception('Especifique todos os dados.');
            }
            
            $atualOK = $this->db->where(['id' => $id, 'senha' => sha1($atual)])
                                ->count_all_results('adm_usuarios') > 0;
            
            if (!$atualOK) {
                throw new Exception('A senha atual está incorreta.');
            }
            
            if ($nova !== $confirm) {
                throw new Exception('A senha atual está diferente da sua confirmação.');
            }
            
            if (strlen($nova) < 6) {
                throw new Exception('A senha deve ter no mínimo 6 caracteres.');
            }
            
            $resp = $this->db->update('adm_usuarios',['senha' => sha1($nova)], ['id' => $id]);
            if (!$resp) {
                throw new Exception('Erro ao atualizar dados.');
            }
            
            $status = TRUE;
            $msg = 'Registro atualizado com sucesso!';
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        $this->response(['status' => $status, 
                         'msg'    => $msg
                       ], REST_Controller::HTTP_OK);
    } 

    
    
    
}


