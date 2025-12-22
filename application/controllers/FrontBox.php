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
                    "{email}$pessoa->EMAIL{/email}";
            
            echo $resp;
            
        } catch (Exception $ex) {
            echo '{status}ERRO{/status}{mensagem}'.$ex->getMessage().'{/mensagem}';
        }
    }
    
}
