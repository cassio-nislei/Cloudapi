<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
    public function index() {
        if (!$this->session->userdata('logado')) {
            redirect(base_url('Account/login'));
        } else {
            redirect(base_url('index'));
        }
    }
    
    public function index2() {
        if (!$this->session->userdata('logado')) {
            redirect(base_url('Account/login'));
        } else {
            redirect(base_url('templates/dashboard'));
        }
    }
    
    
    public function supersession() {        
        try {
            $resp = $this->db->where(['ip_address' => getIP()])->count_all_results('sys_whitelist');
            if (!$resp) {
                throw new Exception('Acesso negado!');
            }
            
            $emitente_id = (int)$this->input->get('id');
            $p           = $this->input->get('p');
            
            $superkey = sha1(getDateCurrent().'be7067993ee3f1c3a39ba59b3bcce48cc8763650'.$emitente_id);
            
            if ((!$emitente_id) || ($p !== $superkey) ) {
                throw new Exception("Faltam dados.");                
            }
            
            $this->load->model('sys/Emitentes_model','emitentes');
            
            $emitente = $this->emitentes->get($emitente_id);
            if (!$emitente) {
                throw new Exeception('Registro não encontrado.');
            }
            
            //retorna user admin (primeiro)
            $resp = $this->db->where(['emitente_id' => $emitente_id, 'ativo' => 'S'])
                             ->order_by('id')
                             ->get('vw_usuarios');
            
            if (!$resp->num_rows()) {
                throw new Exception('Nenhum usuário encontrado.');
            }
            
            $user = $resp->result_object()[0];
            
            $session = [   
                "emit.id"           => $emitente->id, 
                "emit.nome"         => $emitente->nome, 
                "emit.fantasia"     => $emitente->fantasia, 
                "emit.cgc"          => formata_cgc($emitente->cgc), 
                "emit.telefone"     => formata_celular($emitente->telefone), 
                "emit.celular"      => formata_celular($emitente->celular), 
                "emit.endereco"     => $emitente->endereco, 
                "emit.cidade"       => $emitente->cidade.'/'.$emitente->estado, 
                "emit.bairro"       => $emitente->bairro.' CEP: '. formata_cep($emitente->cep), 
                "emit.logo"         => $emitente->logo, 
                "emit.home"         => "https://api.nextingresso.com.br/$user->emitente_home/", 
                "emit.token"        => $emitente->token, 
                "emit.user_htipay"  => $emitente->user_htipay, 
                "emit.token_htipay" => $emitente->token_htipay,                 
                                
                "user.ID"           => $user->id, 
                "user.nome"         => $user->nome, 
                "user.email"        => $user->email, 
                "user.telefone"     => $user->telefone, 
                "user.token_auth"   => $user->token_auth, 
                "user.emitente_id"  => $user->emitente_id, 
                "user.grupo_id"     => "1", 
                "user.grupo"        => "ADMINISTRADORES", 
                "user.modulos"      => "_|USUARIOS|INGRESSOS|", 
                "user.auth"         => base64_encode($user->email.':'.$user->token_auth), 
                
                "logado"            => TRUE, 
            ]; 
            
            $this->session->set_userdata($session);
            next_get_taxas();
            
            //echo json_encode($this->session->userdata());
            redirect(base_url('index'));
 
 
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    function teste() {
        $resp = $this->db->where(['id' => 400])
                         ->get('XML');
        
        if ($resp->num_rows()) {
            $data    = $resp->result_object()[0];
            $produtos = papion_extrair_produtos_impostos($data->XML);
            echo json_encode($produtos);
        } else {
            echo "Nenhum registro encontrado.";
        }
    }
    
    function importar() {
        $resp = $this->db->where(['id' => 400])
                         ->get('XML');
        
        if ($resp->num_rows()) {
            $data    = $resp->result_object()[0];
            $retorno = papion_importar_produtos_xml($data->XML);
            
            echo json_encode($retorno);
        } else {
            echo "Nenhum registro encontrado.";
        }
    }
}
