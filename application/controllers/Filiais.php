<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Filiais extends CI_Controller {
    
    private $CONTROLE = 'filiais';
    
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
        
        $this->load->model('edf/Filiais_model', 'modelo');
    }
    
    function get() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            pode_ler($this->CONTROLE);
            
            $id = (int)$this->uri->segment(3);            
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $data = $this->modelo->get($id);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registro encontrado';                
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
        try {  
            pode_ler($this->CONTROLE);
            
            $data = $this->modelo->getAll();
            
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
    
    function salvar() {
        $status = FALSE;
        $msg = NULL;
        $data = NULL;
        try {         
            pode_gravar($this->CONTROLE);
            
            $registro = $this->input->post('registro');            
            if (!$registro) {
                throw new Exception('Registro não especificado');
            }
            
            $id            = is_seted($registro, 'id')       ?  (int)$registro['id'] : 0;            
            $nome          = isset($registro['nome'])        ?  $registro['nome'] : '';
            $cgc           = isset($registro['cgc'])         ?  SomenteNumeros( $registro['cgc']) : '';
            $fantasia      = isset($registro['fantasia'])    ?  $registro['fantasia'] : '';
            $descricao     = isset($registro['descricao'])   ?  $registro['descricao'] : '';           
            $endereco      = isset($registro['endereco'])    ?  $registro['endereco'] : '';
            $numero        = isset($registro['numero'])      ?  $registro['numero'] : '';
            $complemento   = isset($registro['complemento']) ?  $registro['complemento'] : '';           
            $bairro        = isset($registro['bairro'])      ?  $registro['bairro'] : '';
            $cidade        = isset($registro['cidade'])      ?  $registro['cidade'] : '';
            $estado        = isset($registro['estado'])      ?  $registro['estado'] : '';            
            $email         = isset($registro['email'])       ?  $registro['email'] : '';
            $telefone      = isset($registro['telefone'])    ?  $registro['telefone'] : '';
            $celular       = isset($registro['celular'])     ?  $registro['celular'] : '';            
            $cep           = isset($registro['cep'])         ?  somenteNumeros( $registro['cep'] ) : '';             
            
            if (empty($nome)) {
                throw new Exception('Especifique o nome da Filial!');
            }
            
            //if (!$this->modelo->check_nome($nome, $id)) {
            //    throw new Exception("O nome especificado já está cadastrado.");
            //}
                        
            $dados = [     
                'nome'          => addslashes($nome),
                'cgc'           => addslashes($cgc),
                'fantasia'      => addslashes($fantasia),
                'descricao'     => html_escape(addslashes($descricao)),                
                'endereco'      => addslashes($endereco),
                'numero'        => addslashes($numero),
                'complemento'   => addslashes($complemento),
                'bairro'        => addslashes($bairro),
                'cidade'        => addslashes($cidade),
                'estado'        => addslashes(is5_strtoupper($estado)),                
                'email'         => addslashes(is5_strtolower($email)),
                'telefone'      => addslashes(somenteNumeros($telefone)),
                'celular'       => addslashes(somenteNumeros($celular)),                
                'cep'           => addslashes($cep),                   
            ];   
                
            
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
            
            $id = (int)$this->uri->segment(3);
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


