<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';
require_once APPPATH . 'libraries/Format.php';
use Restserver\Libraries\REST_Controller;

require_once APPPATH . 'models/Pessoa.php';

class Registro extends REST_Controller {

    function __construct() {        
        parent::__construct();
        
        auth_token();
        
        $this->load->model('Pessoas_model', 'pessoas');        
    }       
    
    public function index_get() {
        $data = [];
        try {
            $this->response(['status' => 'OK', 
                             'msg'    => 'GET', 
                             'data'   => $data,
                            ], REST_Controller::HTTP_OK);
            
        } catch (Exception $ex) {
            $this->response(['status' => 'ERRO', 
                             'msg' => $ex->getMessage()
                            ], REST_Controller::HTTP_OK);
        }
    }
    
    public function index_post() {
        $data = [];
        try {
            $registro = $this->post('registro');
            
            if (!$registro) {
                throw new Exception('Dados não especificados.');
            }
                        
            $pessoa = $this->pessoas->arrayToPessoa($registro);
            
            $error = $this->pessoas->validar($pessoa);
            if ($error !== '') {
                throw new Exception($error);
            }
            
            $pessoa->auto_install = 'S';            
            $pessoa->data_install = getDateTimeCurrent();     
            
            $pessoa->licencas      = 1;
            $pessoa->cont_licencas = 1;
            $pessoa->periodo       = 30;
            $pessoa->expira_em     = dateToDb( dateIncDay(getDateCurrentBr(), 30) ); 
            
            /*
                S - ATIVO
                N - DESATIVADO
                B - BLOQUEADO
            */
            
            $pessoa->ativo = 'S'; //pra liberar chave b
            
            $data = $this->pessoas->pessoaToArray($pessoa);
            
            $respId = $this->pessoas->gravar($data);
            if (!$respId) {
                throw new Exception('Erro ao salvar dados. Tente novamente.');
            }
            
            //gera chave B (retornando para ser cadastrado no FBX) e ativa o cliente
            //list($st, $key) = $this->getChaveB($pessoa->cgc, $pessoa->chave_a);
            //if (!$st) {
            //    $this->pessoas->excluir($respId);
            //    throw new Exception($key);                
            //}
            
            //$pessoa->chave_b = $key;
            
            $this->enviarEmailAdmin($pessoa);
            
            $this->response(['status' => 'OK', 
                             'msg'    => $pessoa->chave_b, 
                             'data'   => $pessoa,
                            ], REST_Controller::HTTP_OK);
            
        } catch (Exception $ex) {
            $this->response(['status' => 'ERRO', 
                             'msg' => $ex->getMessage()
                            ], REST_Controller::HTTP_OK);
        }
    }

    /*
    private function getChaveB($cgc, $chaveA) {
        $status = FALSE;
        $msg = '';
        $url = '';
        try {
            $cgc = somenteNumeros($cgc);
            $token = sha1($cgc.'t0k3n');
            
            $url  = "http://www.fbx.net.br/FrontBox/RegistrarJson?cgc=$cgc&ChaveA=$chaveA&token=$token";
            $t    = file_get_contents($url);
            $json = json_decode($t);
            
            if (!$json) { 
                throw new Exception('Impossível gerar Chave. Tente novamente.');
            }
            
            if (!$json->status) {
                throw new Exception($json->msg);                
            }
            
            $status = TRUE;
            $msg  = $json->msg;

        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage().': '.$url;
        }
        return [$status, $msg];
    }
    */
    
    private function enviarEmailAdmin($pessoa) {
        try {
            $msg = "<b>NOVA INSTALAÇÃO FRONTBOX</b><br>"
                 . "<br>Razão Social: <b>$pessoa->nome</b>"
                 . "<br>Fantasia: <b>$pessoa->fantasia</b>"
                 . "<br>CNPJ/CPF: <b>" . formata_cgc($pessoa->cgc)."</b>"
                 . "<br>Contato: <b>$pessoa->contato</b>"   
                 . "<br>Telefone: <b>" . formata_celular($pessoa->telefone)."</b>"
                 . "<br>Celular: <b>" . formata_celular($pessoa->celular)."</b>"
                 . "<br>Endereço: <b>$pessoa->endereco, $pessoa->numero $pessoa->complemento</b>, Bairro: <b>$pessoa->bairro</b>"
                 . "<br>Cidade: <b>$pessoa->cidade/$pessoa->estado</b>, CEP: <b>$pessoa->cep</b>"
                 . "<br>E-mail: <b>$pessoa->email</b>"
                 . "<hr>Data Instalação: <b>" . dateToBr($pessoa->data_install) . "</b>"
                 . "<br>Licenças: <b>$pessoa->cont_licencas/$pessoa->licencas ($pessoa->periodo dias)</b>"
                 . "<br>Expira em: <b>". dateToBr($pessoa->expira_em) . "</b>";
                 //. "<br>Chave A: <b>$pessoa->chave_a</b>"
                 //. "<br>Chave B: <b>$pessoa->chave_b</b>";
                     
            //sendmail(['ivan@is5.com.br','ubirajarard@gmail.com'], 'Nova instalação FBX', $msg); 
            sendmail('papion@papion.com.br', 'Nova instalação FBX', $msg); 
            sendmail('ivan@is5.com.br', 'Nova instalação FBX', $msg);             
         
        } catch (Exception $ex) {
            //
        }
    }
    
    
    
}



