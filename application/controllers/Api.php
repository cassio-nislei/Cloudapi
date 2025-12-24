<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador API para endpoints de acesso público/autenticado por Bearer Token
 * Este controlador não requer autenticação de sessão
 * Requisições devem incluir Bearer Token no header Authorization para acessar endpoints protegidos
 */

class Api extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        $this->load->model('Pessoas_model', 'modelo_pessoas');
        $this->load->model('PessoaLicencas_model', 'licencas');
        $this->load->model('AdminUsers_model', 'modelo_users');
        
        // Configurar headers de resposta
        header('Content-Type: application/json; charset=utf-8');
    }
    
    /**
     * GET /api/pessoas?cnpj=XXXXX
     * Busca pessoa por CNPJ/CGC
     * Pode ser chamado com autenticação por Bearer Token
     */
    function pessoas() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        
        try {
            // Buscar por CGC/CNPJ se fornecido como parâmetro GET
            $cgc = $this->input->get('cnpj');
            $cgc = $this->input->get('cgc') ?: $cgc;  // Aceita ambos os nomes
            
            if (!empty($cgc)) {
                // Buscar pessoa por CGC
                $pessoa = $this->modelo_pessoas->getByCgc($cgc);
                
                if ($pessoa) {
                    $status = TRUE;
                    $msg = 'Pessoa encontrada';
                    
                    // Formatar dados da resposta
                    $pessoa->CGC                = formata_cgc($pessoa->CGC);
                    $pessoa->CEP                = formata_cep($pessoa->CEP);
                    $pessoa->CELULAR            = formata_celular($pessoa->CELULAR);
                    $pessoa->TELEFONE           = formata_celular($pessoa->TELEFONE);
                    $pessoa->FAX                = formata_celular($pessoa->FAX);
                    $pessoa->DATA_CADASTRO      = dateToBr($pessoa->DATA_CADASTRO);
                    $pessoa->ULTIMA_ATUALIZACAO = dateToBr($pessoa->ULTIMA_ATUALIZACAO);
                    $pessoa->EXPIRA_EM          = dateToBr($pessoa->EXPIRA_EM);
                    $pessoa->DATA_INSTALL       = dateToBr($pessoa->DATA_INSTALL);
                    $pessoa->MENSALIDADE        = valorToBr($pessoa->MENSALIDADE);
                    
                    // Obter licenças associadas
                    $pessoa->LISTA_LICENCAS = $this->licencas->getByIdPessoa($pessoa->ID_PESSOA);
                    if ($pessoa->LISTA_LICENCAS) {
                        foreach($pessoa->LISTA_LICENCAS as $licenca) {
                            $licenca->LAST_LOGIN = dateToBr($licenca->LAST_LOGIN);
                        }
                    }
                    
                    $data = $pessoa;
                } else {
                    // Retorna status false mas indica que a busca foi processada corretamente
                    $status = FALSE;
                    $msg = 'Pessoa não encontrada para o CGC informado';
                    $data = NULL;
                }
            } else {
                // Se não há parâmetro de busca, retorna erro
                throw new Exception('Parâmetro cnpj ou cgc não fornecido');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
            $data = NULL;
        }
        
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    /**
     * GET /api/pessoas/id/:id
     * Busca pessoa por ID (público para fins de integração)
     */
    function getPessoaById() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        
        try {
            $id = (int)$this->uri->segment(4);  // /api/pessoas/id/{id}
            
            if (!$id) {
                throw new Exception('ID não especificado');
            }
            
            $pessoa = $this->modelo_pessoas->get($id);
            
            if ($pessoa) {
                $status = TRUE;
                $msg = 'Pessoa encontrada';
                
                // Formatar dados da resposta
                $pessoa->CGC                = formata_cgc($pessoa->CGC);
                $pessoa->CEP                = formata_cep($pessoa->CEP);
                $pessoa->CELULAR            = formata_celular($pessoa->CELULAR);
                $pessoa->TELEFONE           = formata_celular($pessoa->TELEFONE);
                $pessoa->FAX                = formata_celular($pessoa->FAX);
                $pessoa->DATA_CADASTRO      = dateToBr($pessoa->DATA_CADASTRO);
                $pessoa->ULTIMA_ATUALIZACAO = dateToBr($pessoa->ULTIMA_ATUALIZACAO);
                $pessoa->EXPIRA_EM          = dateToBr($pessoa->EXPIRA_EM);
                $pessoa->DATA_INSTALL       = dateToBr($pessoa->DATA_INSTALL);
                $pessoa->MENSALIDADE        = valorToBr($pessoa->MENSALIDADE);
                
                // Obter licenças associadas
                $pessoa->LISTA_LICENCAS = $this->licencas->getByIdPessoa($pessoa->ID_PESSOA);
                if ($pessoa->LISTA_LICENCAS) {
                    foreach($pessoa->LISTA_LICENCAS as $licenca) {
                        $licenca->LAST_LOGIN = dateToBr($licenca->LAST_LOGIN);
                    }
                }
                
                $data = $pessoa;
            } else {
                throw new Exception('Nenhuma pessoa encontrada para o ID informado');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
            $data = NULL;
        }
        
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
}
?>
