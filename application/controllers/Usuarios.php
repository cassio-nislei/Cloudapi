<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {
    
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
        
        //if (!pode_acessar('usuarios')) {
        //    die('Sem permissão');
        //}
        
        $this->load->model('adm/Usuarios_model','usuarios');
        $this->load->model('adm/Permissoes_model','permissoes');
    }
    
    function getAll() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {   
            pode_ler('usuarios');
            
            //nao mostrar sysop
            if (!$this->session->userdata('user.sysop')) {
                $this->db->where(['id !=' => 1]);
            }
            
            $resp = $this->db->where(['emitente_id' => (int)$this->session->userdata('user.emitente_id')])
                             ->order_by('grupo, nome')
                             ->get('vw_usuarios');
            
            if ($resp->num_rows()) {
                $status = TRUE;
                $msg    = 'Registro encontrado';
                $data   = $resp->result_object();
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
            pode_gravar('usuarios');
            
            $usuario = NULL;            
            $registro = $this->input->post('registro');            
            if (!$registro) {
                throw new Exception('Registro não especificado');
            }
            
            $id        = is_seted($registro, 'id') ? (int)$registro['id'] : NULL;
            $nome      = is_seted($registro, 'nome') ? $registro['nome'] : '';
            $email     = is_seted($registro, 'email') ? is5_strtolower(trim($registro['email'])) : '';
            $grupo_id  = is_seted($registro, 'grupo_id') ? (int)$registro['grupo_id'] : 0;
            $st        = is_seted($registro, 'status') ? $registro['status'] : 'S';
            $senha     = is_seted($registro, 'senha') ? $registro['senha'] : '';
            $filial_id = is_seted($registro, 'filial_id') ? (int)$registro['filial_id'] : NULL; 
            
            if (empty($nome) || empty($email) || !$grupo_id) {
                throw new Exception('Especifique Nome, E-mail e Grupo.');
            }
            
            if (!$id) {
                if (empty($senha)) {
                    throw new Exception('Especifique a senha.');
                }
            }
            
            if ($this->usuarios->check_email($email, $id)) {
                throw new Exception('O e-mail especificado já está cadastrado.');
            }
            
            $data = [
                'nome'      => addslashes($nome),                
                'email'     => addslashes($email),
                'grupo_id'  => (int)$grupo_id,
                'ativo'     => addslashes($st),
                'filial_id' => $filial_id,
            ];
            
            if (!empty($senha)) {
                if (strlen($senha) < 6) {
                    throw new Exception('A senha deve ter mais ao menos 6 caracteres.');
                }
                $data['senha'] = sha1(trim($senha));
            }
            
            //retorna grupo_id original do user (se jah cadastrado)
            $old_grupo_id = 0;
            if ($id) {
                $usuario = $this->usuarios->get($id);
                if ($usuario) {
                    $old_grupo_id = $usuario->grupo_id;
                }
            }
            
            $id = $this->usuarios->gravar($data, $id);
            if (!$id) {
                throw new Exception('Erro ao salvar dados. Tente novamente.');
            }
            
            //se eh usuario novo, ou o grupo foi alterado
            //carrego permissoes do grupo
            if ($grupo_id !== $old_grupo_id) {
                $this->permissoes->setPermissoesUsuario($id, $grupo_id); 
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
            pode_excluir('usuarios');
            
            $id = (int)$this->uri->segment(3);
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $resp = $this->usuarios->excluir($id);
            
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
    
    
    function getById() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            pode_ler('usuarios');
            
            $id = (int)$this->input->get('id');
            
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $resp = $this->db->where(['id' => $id, 'emitente_id' => (int)$this->session->userdata('user.emitente_id')])
                             ->get('adm_usuarios');
            
            if ($resp->num_rows()) {
                $status = TRUE;
                $msg    = 'Registro encontrado';
                $data   = $resp->result_object()[0];
            } else {
                throw new Exception('Nenhum registro encontrado.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    /*
    public function requestPass($email) {
        $status = FALSE;
        $mensagem = '';
        $token_request = NULL;
        try {
            $email = addslashes( is5_strtoupper($email) );
            
            $resp = $this->db->select('id, email')
                             ->where(['email' => $email, 'ativo' => 'S'])
                             ->get('adm_usuarios');
            
            if (!$resp) {
                throw new Exception('Dados não retornados. Tente novamente.');
            }
            
            if ($resp->num_rows()) {
                //retorna dados do usuario
                $user = $resp->result_object()[0];
                
                //gera um token de request de nova senha
                $token_request = sha1( $user->id.'@'.getDateTimeCurrent().'69i57j0l7.1503j0j7' );
                
                //atualiza no registro do usuario
                $status = $this->db->update('adm_usuarios',
                                            ['token_request' => $token_request], 
                                            ['id' => $user->id]);
                if ($status) {                    
                    $mensagem = 'Requisição realizada com sucesso!';
                } else {
                    throw new Exception('Erro ao requisitar atualização. Tente novamente.');
                }
                
            } else {
                throw new Exception('E-mail não cadastrado ou usuário bloqueado.');
            }
        } catch (Exception $ex) {
            $status = FALSE;
            $mensagem = $ex->getMessage();
        }
        return [$status, $mensagem, $token_request];
    }
    
    function newPass($senha, $token_request) {
        $status = FALSE;
        $mensagem = '';
        
        try {            
            $senha = addslashes($senha);
            $token_request = addslashes($token_request);
            
            if (strlen($senha) < 5) {
                throw new Exception('A senha deve ter no mínimo 5 caracteres.');
            }
             
            $resp = $this->db->where(['token_request' => $token_request])
                             ->get('adm_usuarios');
            
            if (!$resp->num_rows()) {
                throw new Exception('Usuário não localizado.');
            }

            $user = $resp->result_object()[0];
            $status = $this->db->update('adm_usuarios',
                                        ['senha' => sha1($senha), 'token_request' => '', 'email_confirmado' => 'S'],
                                        ['token_request' => $token_request]);
            if (!$status) {
                throw new Exception('Erro ao atualizar senha. Tente novamente.');
            }
            
            if ($status) {
                $mensagem = 'Senha alterada com sucesso!';
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $mensagem = $ex->getMessage();
        }
        return [$status, $mensagem];
    }
    
    function changePass($id, $token_auth, $senha) {
        $status = FALSE;
        $mensagem = '';        
        try {                 
            $senha = addslashes($senha);
            $token_request = addslashes($token_request);
            
            if (strlen($senha) < 5) {
                throw new Exception('A senha deve ter no mínimo 5 caracteres.');
            }
             
            $count = $this->db->where(['id' => $id, 'token_request' => $token_request, 'ativo' => 'S'])
                              ->count_all_results('adm_usuarios');
            
            if (!$count) {
                throw new Exception('Usuário não localizado.');
            }
            
            $status = $this->db->update('adm_usuarios',
                                        ['senha' => sha1($senha)],
                                        ['id' => $id]);
            if (!$status) {
                throw new Exception('Erro ao atualizar senha. Tente novamente.');
            }
            
            if ($status) {
                $mensagem = 'Senha alterada com sucesso!';
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $mensagem = $ex->getMessage();
        }
        return [$status, $mensagem];
    }*/
    
    function getPermissoes() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {            
            $usuario_id = (int)$this->uri->segment(3);
            
            if (!$usuario_id) {
                throw new Exception('Usuário não especificado.');
            }
            
            $data = $this->permissoes->pesquisar(['usuario_id' => $usuario_id]);
            
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
