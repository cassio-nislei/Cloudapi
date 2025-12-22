<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pessoas extends CI_Controller {
    
    private $CONTROLE = 'pessoas';
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
        
        $this->load->model('Pessoas_model', 'modelo');
        $this->load->model('PessoaLicencas_model', 'licencas');
    }
    
    function get() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            //pode_ler($this->CONTROLE);
            
            $id = (int)$this->uri->segment(3);            
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $data = $this->modelo->get($id);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registro encontrado';   
                
                $data->CGC                = formata_cgc($data->CGC);
                $data->CEP                = formata_cep($data->CEP);
                
                $data->CELULAR            = formata_celular($data->CELULAR);
                $data->TELEFONE           = formata_celular($data->TELEFONE);
                $data->FAX                = formata_celular($data->FAX);
                
                $data->DATA_CADASTRO      = dateToBr($data->DATA_CADASTRO);
                $data->ULTIMA_ATUALIZACAO = dateToBr($data->ULTIMA_ATUALIZACAO);
                $data->EXPIRA_EM          = dateToBr($data->EXPIRA_EM);
                
                $data->DATA_INSTALL       = dateToBr($data->DATA_INSTALL);
                $data->MENSALIDADE        = valorToBr($data->MENSALIDADE);
                
                $data->LISTA_LICENCAS = $this->licencas->getByIdPessoa($id);
                if ($data->LISTA_LICENCAS) {
                    foreach($data->LISTA_LICENCAS as $licenca) {
                        $licenca->LAST_LOGIN = dateToBr($licenca->LAST_LOGIN);
                    }
                }
                
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
            //pode_ler($this->CONTROLE);
            
            $data = $this->modelo->getAll();
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registros encontrados: '. is5_count($data);
                
                foreach($data as $d) {
                    //para poder filtrar
                    $d->status = $d->ATIVO === 'S' ? 'Ativo' : 'Desativado';
                }
                
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
            //pode_gravar($this->CONTROLE);
            
            $registro = $this->input->post('registro');            
            if (!$registro) {
                throw new Exception('Registro não especificado');
            }
            
            $ID_PESSOA    = isset($registro['ID_PESSOA']) ? (int)$registro['ID_PESSOA'] : 0;
            //$ID_EMITENTE  = isset($registro['ID_EMITENTE']) ? (int)$registro['ID_EMITENTE'] : 0;
            
            //DADOS BASICOS
            $NOME         = isset($registro['NOME']) ? strtoupper($registro['NOME']) : '';
            $FANTASIA     = isset($registro['FANTASIA']) ? strtoupper($registro['FANTASIA']) : '';
            $CGC          = isset($registro['CGC']) ? somenteNumeros($registro['CGC']) : '';
            $IE           = isset($registro['IE']) ? somenteNumeros($registro['IE']) : '';
            $EMAIL        = isset($registro['EMAIL']) ? strtolower($registro['EMAIL']) : '';
            $TELEFONE     = isset($registro['TELEFONE']) ? somenteNumeros($registro['TELEFONE']) : '';
            $CELULAR      = isset($registro['CELULAR']) ? somenteNumeros($registro['CELULAR']) : '';
            $FAX          = isset($registro['FAX']) ? somenteNumeros($registro['FAX']) : '';
            $NOME_CONTATO = isset($registro['NOME_CONTATO']) ? strtoupper($registro['NOME_CONTATO']) : '';            
            $ENDERECO     = isset($registro['ENDERECO']) ? strtoupper($registro['ENDERECO']) : '';
            $NUMERO       = isset($registro['NUMERO']) ? strtoupper($registro['NUMERO']) : '';
            $COMPLEMENTO  = isset($registro['COMPLEMENTO']) ? strtoupper($registro['COMPLEMENTO']) : '';
            $CIDADE       = isset($registro['CIDADE']) ? strtoupper($registro['CIDADE']) : '';
            $ESTADO       = isset($registro['ESTADO']) ? strtoupper($registro['ESTADO']) : '';
            $CEP          = isset($registro['CEP']) ? somenteNumeros($registro['CEP']) : '';
            $BAIRRO       = isset($registro['BAIRRO']) ? strtoupper($registro['BAIRRO']) : '';
            
            //COBRANCA 
            $DIA_ACERTO         = isset($registro['DIA_ACERTO']) ? (int)$registro['DIA_ACERTO'] : 0;
            $MENSALIDADE        = isset($registro['MENSALIDADE']) ? (float)$registro['MENSALIDADE'] : 0;
            $DATA_CADASTRO      = isset($registro['DATA_CADASTRO']) ? dateToDb($registro['DATA_CADASTRO']) : NULL;
            $ULTIMA_ATUALIZACAO = isset($registro['ULTIMA_ATUALIZACAO']) ? dateToDb($registro['ULTIMA_ATUALIZACAO']) : NULL;
            
            //LICENCAS
            $LICENCAS        = isset($registro['LICENCAS']) ? (int)$registro['LICENCAS'] : 0; 
            $CONT_LICENCAS   = isset($registro['CONT_LICENCAS']) ? (int)$registro['CONT_LICENCAS'] : 0; 
            $PERIODO         = isset($registro['PERIODO']) ? (int)$registro['PERIODO'] : 0; 
            $EXPIRA_EM       = isset($registro['EXPIRA_EM']) ? dateToDb($registro['EXPIRA_EM']) : NULL;
            $ACESSA_IMPOSTOS = isset($registro['ACESSA_IMPOSTOS']) ? $registro['ACESSA_IMPOSTOS'] : 'N';
            
            //ATUALIZACOES
            $ATIVO         = isset($registro['ATIVO']) ? $registro['ATIVO'] : 'N';
            $AUTO_INSTALL  = isset($registro['AUTO_INSTALL']) ? $registro['AUTO_INSTALL'] : 'N';
            $DATA_INSTALL  = isset($registro['DATA_INSTALL']) ? dateToDb($registro['DATA_INSTALL']) : NULL;
            $OBS           = isset($registro['OBS']) ? $registro['OBS'] : '';
            
            //SPC
            $ACESSO_SPC       = isset($registro['ACESSO_SPC']) ? $registro['ACESSO_SPC'] : 'N';
            $SPC_CONSULTA     = isset($registro['SPC_CONSULTA']) ? (int)$registro['SPC_CONSULTA'] : 0;
            $SPC_NEGATIVACAO  = isset($registro['SPC_NEGATIVACAO']) ? (int)$registro['SPC_NEGATIVACAO'] : 0;
            $SPC_REABILITACAO = isset($registro['OBS']) ? (int)$registro['SPC_REABILITACAO'] : 0;
            
            //$DATA_NASCIMENTO     = isset($registro['DATA_NASCIMENTO'])     ? $registro['DATA_NASCIMENTO'] : NULL;
            //$CONTATO_TEL         = isset($registro['CONTATO_TEL'])         ? somenteNumeros($registro['CONTATO_TEL']) : '';
            //$CONTATO_CEL         = isset($registro['CONTATO_CEL'])         ? somenteNumeros($registro['CONTATO_CEL']) : '';
            //$NOME_PAI            = isset($registro['NOME_PAI'])            ? strtoupper($registro['NOME_PAI']) : '';
            //$NOME_CONJUGE        = isset($registro['NOME_CONJUGE'])        ? strtoupper($registro['NOME_CONJUGE']) : '';
            //$NOME_MAE            = isset($registro['NOME_MAE'])            ? strtoupper($registro['NOME_MAE']) : '';
            //$EMISSOR             = isset($registro['EMISSOR'])             ? strtoupper($registro['EMISSOR']) : '';
            //$SEXO                = isset($registro['SEXO'])                ? strtoupper($registro['SEXO']) : '';
               
            if (empty($NOME)) {
                throw new Exception('Especifique o campo Nome!');
            }
            
            if (empty($CGC)) {
                throw new Exception('Especifique o campo CPF/CNPJ!');
            }
            
            if (!valida_cgc($CGC)) {
                throw new Exception('Informe um CPF/CNPJ válido.');
            }
            
            if ($this->modelo->check_cgc($CGC, $ID_PESSOA)) {
                throw new Exception('O CPF/CNPJ informado já está cadastrado no sistema.');
            }

            $dados = [
                'NOME'               => addslashes($NOME),
                'FANTASIA'           => addslashes($FANTASIA),
                'CGC'                => addslashes($CGC),
                'IE'                 => addslashes($IE),
                'EMAIL'              => addslashes($EMAIL),
                'TELEFONE'           => addslashes($TELEFONE),
                'CELULAR'            => addslashes($CELULAR),
                'FAX'                => addslashes($FAX),
                'NOME_CONTATO'       => addslashes($NOME_CONTATO),
                'ENDERECO'           => addslashes($ENDERECO),
                'NUMERO'             => addslashes($NUMERO),
                'COMPLEMENTO'        => addslashes($COMPLEMENTO),
                'CIDADE'             => addslashes($CIDADE),
                'ESTADO'             => addslashes($ESTADO),
                'CEP'                => addslashes($CEP),
                'BAIRRO'             => addslashes($BAIRRO),
                'DIA_ACERTO'         => addslashes($DIA_ACERTO),
                'MENSALIDADE'        => addslashes($MENSALIDADE),
                'DATA_CADASTRO'      => addslashes($DATA_CADASTRO),
                'ULTIMA_ATUALIZACAO' => addslashes($ULTIMA_ATUALIZACAO),
                'LICENCAS'           => addslashes($LICENCAS),
                'CONT_LICENCAS'      => addslashes($CONT_LICENCAS),
                'PERIODO'            => addslashes($PERIODO),
                'EXPIRA_EM'          => addslashes($EXPIRA_EM),
                'ACESSA_IMPOSTOS'    => addslashes($ACESSA_IMPOSTOS),
                'ATIVO'              => addslashes($ATIVO),
                'AUTO_INSTALL'       => addslashes($AUTO_INSTALL),
                'DATA_INSTALL'       => addslashes($DATA_INSTALL),
                'OBS'                => addslashes($OBS),
                'ACESSO_SPC'         => addslashes($ACESSO_SPC),
                'SPC_CONSULTA'       => addslashes($SPC_CONSULTA),
                'SPC_NEGATIVACAO'    => addslashes($SPC_NEGATIVACAO),
                'SPC_REABILITACAO'   => addslashes($SPC_REABILITACAO),
            ];
             
            $resp = $this->modelo->gravar($dados, $ID_PESSOA);
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
            //pode_excluir($this->CONTROLE);
            
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
    
    function excluirLicenca() {
        $status = FALSE;
        $msg = NULL;        
        try {   
            $id = (int)$this->input->post('id');
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $resp = $this->licencas->excluir($id);
            
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
    
    function changeStatus() {
        $status = FALSE;
        $msg = NULL;        
        try {   
            //pode_excluir($this->CONTROLE);
            
            $id = (int)$this->input->post('id');
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $licenca = $this->licencas->get($id);
            
            if (!$licenca) {
                throw new Exception('Licença não encontrada.');
            }
            
            $ST = $licenca->STATUS === 'A' ? 'D' : 'A';
            
            $resp = $this->licencas->gravar(['STATUS' => $ST], $id);
            
            if ($resp) {
                $status = TRUE;
                $msg    = 'Registro alterado com sucesso!';
            } else {
                throw new Exception('Erro ao alterar registro.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }
    
    function getLicencas() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {  
            $id_pessoa = $this->input->get('id_pessoa');
            
            $data = $this->licencas->getByIdPessoa($id_pessoa);
            
            if ($data) {
                $status = TRUE;
                $msg = 'Registros encontrado: '.is5_count($data);
                
                foreach($data as $d) {
                    $d->LAST_LOGIN = dateToBr($d->LAST_LOGIN);
                }                
                
            } else {
                throw new Exception('Nenhum registro encontrado.');
            }
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    
    
}


