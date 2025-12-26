<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';
require_once APPPATH . 'libraries/Format.php';
use Restserver\Libraries\REST_Controller;

class Passport extends REST_Controller {  
    
    function __construct() {        
        parent::__construct();    
        
        $this->load->model('Pessoas_model', 'pessoas');
        $this->load->model('PessoaLicencas_model', 'licencas');
    }       

    public function index_get() {
        $status = FALSE;
        $msg = NULL;
        $data = NULL;
        try {
            $cgc      = somenteNumeros($this->get('cgc'));
            $hostname = $this->get('hostname');
            $guid     = $this->get('guid');
            
            //versoes
            $fbx = $this->get('fbx');
            $pdv = $this->get('pdv');
            
            if (empty($cgc) || empty($hostname) || empty($guid)) {
                throw new Exception('Faltam dados.');
            }
            
            //1. retorna pessoa
            $pessoa = $this->pessoas->getByCgc($cgc);
            if (!$pessoa) {
                throw new Exception('Registro não encontrado.');
            }
            
            //set versao do sistema ao tentar logar
            $this->pessoas->setVersaoFbxPdv($pessoa->ID_PESSOA, $fbx, $pdv);
            
            //2. cadastro ativo?
            if ($pessoa->ATIVO !== 'S') {
                throw new Exception('Sua licença expirou ou a conta está desativada. ' . $pessoa->OBS);
            }
            
            //2.5. verifica se tem data de expiracao. Se tiver, valida
            $expira_em = $pessoa->EXPIRA_EM;
            if (!empty($expira_em)) {
                if (getDateCurrent() >= $expira_em) {
                    throw new Exception('Sua licença expirou em '.dateToBr($expira_em).'. Entre em contato com o revendedor.');
                }
            }
            
            //3. retorna licenca (se status = A, ativa)
            $licenca = $this->licencas->getByGUID($pessoa->ID_PESSOA, $guid);
            
            //4. nao encontrada? Verifica limite de licencas
            if (!$licenca) {                
                $qtd_licencas   = (int)$pessoa->LICENCAS;
                $qtd_utilizadas = (int)$this->licencas->countLicencas($pessoa->ID_PESSOA);

                //5. limite excedido? bloqueia
                if ($qtd_utilizadas >= $qtd_licencas) {
                    throw new Exception("Limite de licenças atingido: $qtd_licencas.");
                }

                //6. ainda tem limite? entao cadastra a nova licenca
                $dados_licenca = [
                    'ID_PESSOA'  => $pessoa->ID_PESSOA,
                    'HOSTNAME'   => addslashes($hostname),
                    'GUID'       => addslashes($guid),
                    'CREATED_AT' => getDateTimeCurrent(),
                    'LAST_LOGIN' => getDateTimeCurrent()
                ];
                
                // Debug log
                error_log('DEBUG: Tentando gravar licença com dados: ' . json_encode($dados_licenca));
                
                $resp = $this->licencas->gravar($dados_licenca);
                
                // Debug log
                error_log('DEBUG: Resposta do gravar: ' . var_export($resp, true));
                
                if (!$resp) {
                    error_log('DEBUG: ERRO ao gravar licença');
                    throw new Exception('Erro ao registrar licença.');
                }
                
                error_log('DEBUG: Licença gravada com sucesso. ID: ' . $resp);
            } else {
                //encontrou? 
                
                //verifica se esta vinculado ao mesmo hostname
                if (trim($licenca->HOSTNAME) !== trim($hostname)) {
                    throw new Exception('A Licença não está associada a este dispositivo.');
                }
                
                //Atualiza o last_login                    
                $this->licencas->gravar(['LAST_LOGIN' => getDateTimeCurrent()], $licenca->ID);
            }
            
            //set data/hoora ultimo login
            $this->pessoas->setUltimoAcesso($pessoa->ID_PESSOA);
            
            $status = TRUE;
            $msg    = 'Passport OK!';
            
            // Preparar dados do cliente para retorno
            $data = [
                'id_pessoa'      => $pessoa->ID_PESSOA,
                'nome'           => $pessoa->NOME,
                'fantasia'       => $pessoa->FANTASIA,
                'cgc'            => $pessoa->CGC,
                'email'          => $pessoa->EMAIL,
                'telefone'       => $pessoa->TELEFONE,
                'celular'        => $pessoa->CELULAR,
                'contato'        => $pessoa->NOME_CONTATO,
                'endereco'       => $pessoa->ENDERECO,
                'numero'         => $pessoa->NUMERO,
                'complemento'    => $pessoa->COMPLEMENTO,
                'bairro'         => $pessoa->BAIRRO,
                'cidade'         => $pessoa->CIDADE,
                'estado'         => $pessoa->ESTADO,
                'cep'            => $pessoa->CEP,
                'ativo'          => $pessoa->ATIVO,
                'licencas'       => $pessoa->LICENCAS,
                'cont_licencas'  => $pessoa->CONT_LICENCAS,
                'periodo'        => $pessoa->PERIODO,
                'expira_em'      => $pessoa->EXPIRA_EM,
                'data_cadastro'  => $pessoa->DATA_CADASTRO,
                'ultimo_acesso'  => $pessoa->ULTIMO_ACESSO,
                'versao_fbx'     => $pessoa->VERSAO_FBX,
                'versao_pdv'     => $pessoa->VERSAO_PDV,
                'obs'            => $pessoa->OBS
            ];
                        
        } catch (Exception $ex) {
            $status = FALSE;
            $msg    = $ex->getMessage();
        }
        
        $response = [
            'Status'   => $status, 
            'Mensagem' => $msg,            
        ];
        
        // Adicionar dados do cliente na resposta se sucesso
        if ($status && $data) {
            $response['Dados'] = $data;
        }
        
        $this->response($response, REST_Controller::HTTP_OK);
    }
    
    
    
}

