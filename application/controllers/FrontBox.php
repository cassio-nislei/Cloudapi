<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FrontBox extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        $this->load->model('Pessoas_model','pessoas');        
    }
    
    function acessaImpostos() {
        try {
            $cgc = somenteNumeros($this->input->get('cgc'));
            
            $pessoa = $this->pessoas->getByCgc($cgc);
            if (!$pessoa) {
                throw new Exception('Registro não encontrado.'.$cgc);
            }
            
            echo $pessoa->ACESSA_IMPOSTOS === 'S' ? '#SIM' : '#NAO';
            
        } catch (Exception $ex) {
            echo "#NAO";
        }
    }    
    
    function getInfo() {
        try {
            $cgc = somenteNumeros( $this->input->get('q') );
            
            if (empty($cgc)) {
                throw new Exception('Faltam dados.');
            }
            
            //$cgc = base64_decode($cgc, true);
            //if (empty($cgc)) {
            //    throw new Exception('Faltam dados.'.$cgc);
            //}
            
            if (!valida_cgc($cgc)) {
                throw new Exception('Informação incorreta.');
            }
            
            $pessoa = $this->pessoas->getByCgc($cgc);
            if (!$pessoa) {
                throw new Exception('Registro não encontrado.'.$cgc);
            }
            
            $pessoa->CGC      = formata_cgc($pessoa->CGC);
            $pessoa->TELEFONE = formata_celular($pessoa->TELEFONE);
            
            // Garantir que todos os campos existem e têm valores padrão
            $pessoa->NOME        = isset($pessoa->NOME) && !empty($pessoa->NOME) ? $pessoa->NOME : '';
            $pessoa->FANTASIA    = isset($pessoa->FANTASIA) && !empty($pessoa->FANTASIA) ? $pessoa->FANTASIA : '';
            $pessoa->ENDERECO    = isset($pessoa->ENDERECO) && !empty($pessoa->ENDERECO) ? $pessoa->ENDERECO : '';
            $pessoa->COMPLEMENTO = isset($pessoa->COMPLEMENTO) && !empty($pessoa->COMPLEMENTO) ? $pessoa->COMPLEMENTO : '';
            $pessoa->IE          = isset($pessoa->IE) && !empty($pessoa->IE) ? $pessoa->IE : '';
            $pessoa->NUMERO      = isset($pessoa->NUMERO) && !empty($pessoa->NUMERO) ? $pessoa->NUMERO : '';
            $pessoa->BAIRRO      = isset($pessoa->BAIRRO) && !empty($pessoa->BAIRRO) ? $pessoa->BAIRRO : '';
            $pessoa->CIDADE      = isset($pessoa->CIDADE) && !empty($pessoa->CIDADE) ? $pessoa->CIDADE : '';
            $pessoa->ESTADO      = isset($pessoa->ESTADO) && !empty($pessoa->ESTADO) ? $pessoa->ESTADO : '';
            $pessoa->CNAE        = isset($pessoa->CNAE) && !empty($pessoa->CNAE) ? $pessoa->CNAE : '';
            $pessoa->IM          = isset($pessoa->IM) && !empty($pessoa->IM) ? $pessoa->IM : '';
            $pessoa->TIPO        = isset($pessoa->TIPO) && !empty($pessoa->TIPO) ? $pessoa->TIPO : '';
            $pessoa->EMAIL       = isset($pessoa->EMAIL) && !empty($pessoa->EMAIL) ? $pessoa->EMAIL : '';
            
            $resp = "{status}OK{/status}" .
                    "{nome}$pessoa->NOME{/nome}" .
                    "{fantasia}$pessoa->FANTASIA{/fantasia}" . 
                    "{endereco}$pessoa->ENDERECO{/endereco}" .
                    "{complemento}$pessoa->COMPLEMENTO{/complemento}" .
                    "{cgc}$pessoa->CGC{/cgc}" .
                    "{ie}$pessoa->IE{/ie}" .
                    "{telefone}$pessoa->TELEFONE{/telefone}" .
                    "{numero}$pessoa->NUMERO{/numero}" .
                    "{bairro}$pessoa->BAIRRO{/bairro}" .
                    "{cidade}$pessoa->CIDADE{/cidade}" .
                    "{estado}$pessoa->ESTADO{/estado}" .
                    "{cnae}$pessoa->CNAE{/cnae}" .
                    "{im}$pessoa->IM{/im}" .
                    "{tipo}$pessoa->TIPO{/tipo}" .                    
                    "{email}$pessoa->EMAIL{/email}";
            
            echo $resp;
            
        } catch (Exception $ex) {
            echo '{status}ERRO{/status}{mensagem}'.$ex->getMessage().'{/mensagem}';
        }
    }
    
}
