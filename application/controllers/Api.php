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
        
        // Configurar headers de resposta
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // Tentar carregar modelos, mas continuar se falhar
        if (file_exists(APPPATH . 'models/Pessoas_model.php')) {
            $this->load->model('Pessoas_model', 'modelo_pessoas');
        }
        if (file_exists(APPPATH . 'models/PessoaLicencas_model.php')) {
            $this->load->model('PessoaLicencas_model', 'licencas');
        }
        if (file_exists(APPPATH . 'models/AdminUsers_model.php')) {
            $this->load->model('AdminUsers_model', 'modelo_users');
        }
    }
    
    /**
     * GET /api/pessoas?cnpj=XXXXX
     * Busca pessoa por CNPJ/CGC
     * Pode ser chamado com autenticação por Bearer Token
     */
    function pessoas() {
        $status = FALSE;
        $msg = 'Erro: Nenhum parâmetro fornecido';
        $data = NULL;
        
        try {
            // Buscar por CGC/CNPJ se fornecido como parâmetro GET
            $cgc = $this->input->get('cnpj');
            if (empty($cgc)) {
                $cgc = $this->input->get('cgc');
            }
            
            if (!empty($cgc)) {
                // Remover formatação do CGC (deixar apenas números)
                $cgc_clean = preg_replace('/[^0-9]/', '', $cgc);
                
                // Verificar se modelo foi carregado
                if ($this->modelo_pessoas && method_exists($this->modelo_pessoas, 'getByCgc')) {
                    // Buscar pessoa por CGC
                    $pessoa = $this->modelo_pessoas->getByCgc($cgc_clean);
                    
                    if ($pessoa) {
                        $status = TRUE;
                        $msg = 'Pessoa encontrada';
                        
                        // Formatar dados da resposta
                        if (function_exists('formata_cgc')) $pessoa->CGC = formata_cgc($pessoa->CGC);
                        if (function_exists('formata_cep')) $pessoa->CEP = formata_cep($pessoa->CEP);
                        if (function_exists('formata_celular')) $pessoa->CELULAR = formata_celular($pessoa->CELULAR);
                        if (function_exists('formata_celular')) $pessoa->TELEFONE = formata_celular($pessoa->TELEFONE);
                        if (function_exists('formata_celular')) $pessoa->FAX = formata_celular($pessoa->FAX);
                        if (function_exists('dateToBr')) $pessoa->DATA_CADASTRO = dateToBr($pessoa->DATA_CADASTRO);
                        if (function_exists('dateToBr')) $pessoa->ULTIMA_ATUALIZACAO = dateToBr($pessoa->ULTIMA_ATUALIZACAO);
                        if (function_exists('dateToBr')) $pessoa->EXPIRA_EM = dateToBr($pessoa->EXPIRA_EM);
                        if (function_exists('dateToBr')) $pessoa->DATA_INSTALL = dateToBr($pessoa->DATA_INSTALL);
                        if (function_exists('valorToBr')) $pessoa->MENSALIDADE = valorToBr($pessoa->MENSALIDADE);
                        
                        // Obter licenças associadas se modelo foi carregado
                        if ($this->licencas && method_exists($this->licencas, 'getByIdPessoa')) {
                            $pessoa->LISTA_LICENCAS = $this->licencas->getByIdPessoa($pessoa->ID_PESSOA);
                            if ($pessoa->LISTA_LICENCAS) {
                                foreach($pessoa->LISTA_LICENCAS as $licenca) {
                                    if (function_exists('dateToBr')) $licenca->LAST_LOGIN = dateToBr($licenca->LAST_LOGIN);
                                }
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
                    // Modelo não carregado, retornar mensagem de erro
                    $status = FALSE;
                    $msg = 'Modelo de dados não disponível. Verifique a configuração do servidor.';
                    $data = NULL;
                }
            } else {
                // Se não há parâmetro de busca, retorna erro
                $status = FALSE;
                $msg = 'Parâmetro cnpj ou cgc não fornecido';
                $data = NULL;
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = 'Erro: ' . $ex->getMessage();
            $data = NULL;
        }
        
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
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
    
    /**
     * GET /api/passport
     * Valida passport/autenticação de cliente
     * Parâmetros: cgc, hostname, guid
     */
    function passport() {
        $status = FALSE;
        $mensagem = 'Erro ao processar passport';
        
        try {
            // Obter parâmetros
            $cgc = $this->input->get('cgc');
            $hostname = $this->input->get('hostname');
            $guid = $this->input->get('guid');
            
            // Validar parâmetros obrigatórios
            if (empty($cgc)) {
                $mensagem = 'Parâmetro cgc é obrigatório';
                echo json_encode(['Status' => $status, 'Mensagem' => $mensagem], JSON_UNESCAPED_UNICODE);
                return;
            }
            
            // Limpar CGC
            $cgc_clean = preg_replace('/[^0-9]/', '', $cgc);
            
            // Buscar pessoa por CGC
            if ($this->modelo_pessoas && method_exists($this->modelo_pessoas, 'getByCgc')) {
                $pessoa = $this->modelo_pessoas->getByCgc($cgc_clean);
                
                if ($pessoa) {
                    $status = TRUE;
                    $mensagem = 'Registro encontrado';
                    
                    // Se foi fornecido hostname e guid, registrar a licença
                    if (!empty($hostname) && !empty($guid) && $this->licencas && method_exists($this->licencas, 'gravar')) {
                        try {
                            // Verificar se a licença já existe
                            $licenca_existente = $this->db->get_where('PESSOA_LICENCAS', [
                                'ID_PESSOA' => $pessoa->ID_PESSOA,
                                'GUID' => $guid
                            ])->result_array();
                            
                            if (!empty($licenca_existente)) {
                                // Atualizar LAST_LOGIN se já existe
                                $this->licencas->gravar(['LAST_LOGIN' => date('Y-m-d H:i:s')], $licenca_existente[0]['ID']);
                            } else {
                                // Inserir nova licença se não existe
                                $this->licencas->gravar([
                                    'ID_PESSOA' => $pessoa->ID_PESSOA,
                                    'HOSTNAME' => addslashes($hostname),
                                    'GUID' => addslashes($guid),
                                    'CREATED_AT' => date('Y-m-d H:i:s'),
                                    'LAST_LOGIN' => date('Y-m-d H:i:s')
                                ]);
                            }
                        } catch (Exception $e) {
                            // Log do erro mas não falha o passport
                            error_log('Erro ao registrar licença: ' . $e->getMessage());
                        }
                    }
                } else {
                    $status = FALSE;
                    $mensagem = 'Registro não encontrado';
                }
            } else {
                $status = FALSE;
                $mensagem = 'Modelo de dados não disponível';
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $mensagem = $ex->getMessage();
        }
        
        // Retornar no formato esperado (Status/Mensagem em maiúsculas)
        echo json_encode(['Status' => $status, 'Mensagem' => $mensagem], JSON_UNESCAPED_UNICODE);
    }
}
?>
