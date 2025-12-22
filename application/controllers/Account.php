<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//fgestor.is5.com.br
define('SITE_KEY','6LfQe10pAAAAAHrsT3cJYu3aN7cngnEkSk12ydu4');
define('SECRET_KEY','6LfQe10pAAAAAOfIuY2hr7MWEWmgcTtwhGfOoEjU');

/*
 * https://www.google.com/recaptcha/admin/site/693992400/setup
 * 
 * Chave site    : 6LfQe10pAAAAAHrsT3cJYu3aN7cngnEkSk12ydu4
 * Chave secreta : 6LfQe10pAAAAAOfIuY2hr7MWEWmgcTtwhGfOoEjU
 *  * 
 * isvargas.oficial@gmail.com
 * 
 * fonte: https://pt.stackoverflow.com/questions/59952/como-implementar-recaptcha-do-google-no-meu-site
 */

class Account extends CI_Controller {
    
    function __construct() {
        parent::__construct();  

        $this->load->model('sys/Emitentes_model','emitentes');
    }
    
    function login() {
        $status = '';
        $mensagem = '';
        try {
            if ($this->input->post('login') === 'ENTRAR') { 
                $email    = trim($this->input->post('email'));
                $senha    = $this->input->post('senha');
                $captcha  = $this->input->post('g-recaptcha-response'); //get por js: grecaptcha.getResponse();

                if (empty($email) || empty($senha)) {
                    throw new Exception('Especifique E-mail e Senha.');
                }
                
                /*                
                $resp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . SECRET_KEY . "&response=$captcha&remoteip=" . getIP());
            
                $obj = json_decode($resp);
                if (!$obj) {
                    throw new Exception('Erro ao converter resposta.');
                }

                if (!$obj->success) {
                    //throw new Exception(json_encode($obj));
                    throw new Exception('Captcha incorreto. Atualize a página e tente novamente.');
                }
                */
                
                $pesquisa = [                    
                    'email'  => is5_strtolower( trim($email) ),
                    'senha'  => sha1(trim($senha)),
                    'ativo'  => 'S'
                ];
                
                $resp = $this->db->where($pesquisa)
                                 ->get('vw_usuarios');

                //var_dump($resp);
                
                if ($resp->num_rows()) {
                    $user = $resp->result_object()[0];                    
                    if ($user) {                      
                        $user->emitente = $this->emitentes->get($user->emitente_id);
                        
                        if (isset($user->emitente) && $user->emitente) {                               
                            $e = $user->emitente;
                            if ($e->ativo !== 'S') {
                                $f = ($e->fantasia) ? $e->fantasia : $e->nome;
                                $f = is5_strtoupper($f);
                                $m = "O acesso de $f está temporariamente suspenso!";
                                if ($e->mensagem) {
                                    $m .= "<br>$e->mensagem";
                                }
                                throw new Exception($m);
                            }
                        } else {
                            throw new Exception('Emitente não encontrado.');
                        }
                        /// FIM VERIFICACAO EMITENTE ///////////////////////////
                                                                 
                        $session = [
                            'logado'           => TRUE,
                            'user.ID'          => (int)$user->id,
                            'user.nome'        => $user->nome,
                            'user.email'       => $user->email,
                            'user.telefone'    => $user->telefone,
                            'user.token_auth'  => $user->token_auth,
                            'user.emitente_id' => $user->emitente_id,
                            'user.grupo_id'    => $user->grupo_id,
                            'user.grupo'       => $user->grupo,
                            'user.modulos'     => $user->permissoes, //get_permissoes($user->grupo_id, $user->emitente_id)
                            'user.auth'        => base64_encode($user->email.':'.$user->token_auth),
                            'user.sysop'       => $user->sysop === 'S',
                        ];
                        
                        //dados do emitente                               
                        $emitente = $user->emitente; //$this->emitentes->get($user->emitente_id);
                        if ($emitente) {
                            $sessemit = [
                                'emit.id'         => $emitente->id,
                                'emit.nome'       => $emitente->nome,
                                'emit.fantasia'   => $emitente->fantasia,
                                'emit.cgc'        => formata_cgc($emitente->cgc),
                                'emit.telefone'   => formata_celular($emitente->telefone),
                                'emit.celular'    => formata_celular($emitente->celular),
                                'emit.endereco'   => $emitente->endereco . ' ' . $emitente->numero,
                                'emit.cidade'     => $emitente->cidade .'/'. $emitente->estado,
                                'emit.bairro'     => $emitente->bairro .' CEP: '.$emitente->cep,
                                'emit.logo'       => empty($emitente->logo) ? '' : base_url("uploads/$emitente->logo"),          
                                //'emit.home_local' => str_replace('//', '/', get_rootdir().$user->emitente_home ),                                
                                'emit.home'       => 'https://api.nextingresso.com.br'.$user->emitente_home.'/',
                                'emit.token'      => $emitente->token,                                                                 
                                'emit.user_htipay'  => $emitente->user_htipay,
                                'emit.token_htipay' => $emitente->token_htipay,                                
                            ];
                            $this->session->set_userdata($sessemit);
                        }
                        
                        $this->session->set_userdata($session);
                        
                        redirect(base_url('index')); //index
                    } else {
                        throw new Exception('Nenhum registro encontrado.');
                    }                    
                } else { 
                    throw new Exception('Usuário não cadastrado ou bloqueado.');
                }
            }
            
        } catch (Exception $ex) {
            $status = 'ERRO';
            $mensagem = $ex->getMessage();
        }        
        $data['status'] = $status;
        $data['mensagem'] = $mensagem;
        $this->load->view('account/login', $data);        
    }
    
    function recover() {        
        $status = '';
        $mensagem = '';
        try {
            if ($this->input->post('confirmar') === 'CONFIRMAR') { 
                $email = trim(is5_strtoupper($this->input->post('email')));
                
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
                        $param = base64_encode( $email.':'.$token_request );
                        $link = base_url('Account/newpass?t='.$param);                        
                        sendmail($email, 'Alterar Senha', "Caro usuário,<br><br>Para gerar uma nova senha, acesse o link a seguir:<br><br>$link<br><br>Att,<br>Equipe");
                        
                        $mensagem = 'Requisição realizada com sucesso!';
                    } else {
                        throw new Exception('Erro ao requisitar atualização. Tente novamente.');
                    }

                } else {
                    throw new Exception('E-mail não cadastrado ou usuário bloqueado.');
                }
            }
            
        } catch (Exception $ex) {
            $status = 'ERRO';
            $mensagem = $ex->getMessage();
        }        
        $data['status'] = $status;
        $data['mensagem'] = $mensagem;
        $this->load->view('account/recover', $data);        
    }

    function newpass() {        
        $status = '';
        $mensagem = '';
        $token_request = '';
        try {
            if ($this->input->post('confirmar') === 'CONFIRMAR') { 
                $senha = $this->input->post('senha');
                $senha_confirmacao = $this->input->post('senha_confirmacao');
                $token_request = $this->input->post('token_request');

                if (empty($senha) || empty($senha_confirmacao)) {
                    throw new Exception('Especifique a senha e confirmação.');
                }
                
                if ($senha !== $senha_confirmacao) {
                    throw new Exception('A senha não confere com sua confirmação.');
                }
                
                if (empty($token_request)) {
                    throw new Exception('Requisição não especificada.');
                }
                
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
                
            } else {
                //GET QDO ENTRA NA PAGINA (EXTRAIR TOKEN E JOGAR EM UM HIDDEN)
                $tparam = $this->input->get('t');
                if (empty($tparam)) {
                    throw new Exception('Requisição não encontrada.');
                }

                $param = base64_decode($tparam);
                if (!$param) {
                    throw new Exception('Requisição desconhecida.');
                }

                $arr = explode(':', $param);
                if ((!$arr) || (!isset($arr[1]))) {
                    throw new Exception('Padrão desconhecido.');
                }

                $token_request = $arr[1];
            }
            
        } catch (Exception $ex) {
            $status = 'ERRO';
            $mensagem = $ex->getMessage();
        }        
        $data['status'] = $status;
        $data['mensagem'] = $mensagem;
        $data['token_request'] = $token_request;
        $this->load->view('account/newpass', $data);        
    }
    
    /*
    function register() {
        $status = '';
        $mensagem = '';
        try {
            $nome   = $this->input->post('nome');
            $email  = trim($this->input->post('email'));
            $senha  = $this->input->post('senha');
            $senha2 = $this->input->post('senha_confirmacao');
            $fantasia = $this->input->post('fantasia');
            
            if ($this->input->post('confirmar') === 'CADASTRAR') { 
                //throw new Exception('Em desenvolvimento :)'); 
                
                if (empty($nome) || empty($email) || empty($senha) || empty($senha2)) {
                    throw new Exception('Especifique nome, e-mail e senha.');
                }
                               
                $dados = [  
                    'action'            => 'register',
                    'nome'              => $nome,                    
                    'email'             => $email,
                    'senha'             => $senha,
                    'senha_confirmacao' => $senha2,
                    'fantasia'          => $fantasia
                ];
                
                list($ret, $json, $response) = api_post('acc/Account', $dados);
                
                if ($ret) {
                    if ($json->status === 'OK') {
                        $status = 'OK';
                        $mensagem = $json->msg;
                    } else {
                        throw new Exception($json->msg.' - '.$response);
                    }                    
                } else {                    
                    throw new Exception('Ops, erro ao registrar usuário. Tente novamente. '.$response);
                }
                
            }
            
        } catch (Exception $ex) {
            $status = 'ERRO';
            $mensagem = $ex->getMessage();
        }        
        $data['status'] = $status;
        $data['mensagem'] = $mensagem;
        $this->load->view('account/register', $data);        
    }
    
    function confirmar() {
        $status = '';
        $mensagem = '';
        $emitente_id = 0;
        $alias = '';
        try {
            $token = $this->input->get('p');
            
            if (empty($token)) { 
                throw new Exception('Token de confirmação não especificado.');
            }
            
            $dados = [
                'action' => 'confirmar',
                'token'  => $token
            ];
            
            list($ret, $json, $response) = api_post('acc/Account', $dados);
            
            if ($ret) {
                if ($json->status === 'OK') {
                    $url = "https://portal.nextingresso.com.br/Account/login";
                    echo "<center>Cadastro atualizado com sucesso!<br>Direcionando para login...</br>";
                    echo "<script>setTimeout(function(){ window.location.href='$url'; }, 3000);</script>";
                } else {
                    throw new Exception($json->msg.' - '.$response);
                }                    
            } else {
                throw new Exception('Erro ao ativar conta. Tente novamente. '.$response);
            }
            
        } catch (Exception $ex) {
            $status = 'ERRO';
            $mensagem = $ex->getMessage();
        }        
        echo "<center>$mensagem</center>";        
    }
    */
    
    public function logout() {
        $this->session->sess_destroy();
        redirect(base_url('login'));
    } 
    
    
   
}

