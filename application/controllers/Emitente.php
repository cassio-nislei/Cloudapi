<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emitente extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
    }
    
    function configHtiPay() {
        $status = FALSE;
        $mensagem = '';
        try {
            $id    = (int)$this->session->userdata('emit.id');
            $email = $this->input->post('email');
            $token = $this->input->post('token');
                        
            if (!$id) {
                throw new Exception('Emitente não especificado.');
            }
            
            if (empty($email)) {
                throw new Exception('Especifique o E-mail.');
            }
            
            if (!validar_email($email)) {
                throw new Exception('Especifique um e-mail válido.');
            }
            
            if (empty($token)) {
                throw new Exception('Especifique o Token de acesso.');
            }
            
            if (strlen($token) < 32) {
                throw new Exception('Especifique um token válido.');
            }
            
            $email = is5_strtolower($email);
            $token = is5_strtolower($token);
            
            $dados = [
                'aceita_hticard' => 'S',
                'user_htipay'    => addslashes($email),
                'token_htipay'   => addslashes($token),
            ];
            
            $resp = $this->db->update('sys_emitentes', $dados, ['id' => $id]);
            if ($resp) {
                $status = TRUE;
                
                $this->session->set_userdata('emit.aceita_hticard','S');
                $this->session->set_userdata('emit.user_htipay',$email);
                $this->session->set_userdata('emit.token_htipay',$token);
                
                $mensagem = 'Acesso ao HTI Pay configurado!';
            } else {
                throw new Exception('Erro ao atualizar dados. Tente novamente.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $mensagem = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $mensagem]);
    }
    
}