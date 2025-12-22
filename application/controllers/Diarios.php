<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diarios extends CI_Controller {
    
    private $CONTROLE = 'relatorios';
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
        
        $this->load->model('edf/Diarios_model', 'modelo');
        $this->load->model('edf/Diaconos_model', 'diaconos');
        
        $this->load->model('edf/Pastores_model', 'pastores');
        $this->load->model('edf/Apresentacoes_model', 'apresentacoes');
        $this->load->model('edf/Visitantes_model', 'visitantes');
        
        $this->load->model('edf/Dizimos_model', 'dizimos');
        $this->load->model('edf/Ofertas_model', 'ofertas');
        $this->load->model('edf/Especiais_model', 'especiais');
        $this->load->model('edf/Missoes_model', 'missoes');
    }
    
    function index() {           
        $data['index'] = 3;
        $data['content'] = $this->load->view('embaixada/diarios/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    
    function novo() {  
        //gera um pre relatorio (status = 'T')
        $data = [
            'filial_id'      => (int)$this->session->userdata('user.filial_id'),
            'data'           => getDateCurrent(),
            'hora'           => substr(getTimeCurrent(), 0, 2).':00',
            'user_insert_id' => (int)$this->session->userdata('user.ID'),
            'dh_insert'      => getDateTimeCurrent(),
        ];

        $id = $this->modelo->gravar($data);
        if (!$id) {
            die('Erro ao gerar relatório. Tente novamente.');
        }
        
        $data['content'] = $this->load->view('embaixada/diarios/diario',['diario_id' => $id],TRUE);
        $this->load->view('templates/dashboard', $data);
    }
    
    function editar() {    
        $id = $this->uri->segment( $this->uri->total_segments() );
        $data['content'] = $this->load->view('embaixada/diarios/diario',['diario_id' => $id],TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    
    function get() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            pode_ler($this->CONTROLE);
            
            $id = (int)$this->uri->segment( $this->uri->total_segments() );            
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $data = $this->modelo->get($id, NULL, TRUE);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registro encontrado';     
                
                $data->data = dateToBr($data->data);                
                $data->total_final = (float)$data->total_dizimos + (float)$data->total_gerais + (float)$data->total_especiais + (float)$data->total_missoes;
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
            
            $data_inicial = $this->input->get('data_inicial');
            $data_final = $this->input->get('data_final');
            $filial_id = (int)$this->input->get('filial_id');
            
            //aki, mostrar somente relatorio confirmados na filial
            $pesquisa['status'] = 'C';
            
            if (is5_data_valida_br($data_inicial) && is5_data_valida_br($data_final)) {
                $data_inicial = dateToDb($data_inicial);
                $data_final   = dateToDb($data_final);
                
                $pesquisa['data >='] = $data_inicial;
                $pesquisa['data <='] = $data_final;
            } 
            
            if ($filial_id) {
                $pesquisa['filial_id'] = $filial_id;
            }
            
            $data = $this->modelo->pesquisar($pesquisa);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registros encontrados: '. is5_count($data);
                $data   = $data;
                
                foreach($data as $d) {
                    $d->data = dateToBr($d->data);
                    $d->total_final = (float)$d->total_dizimos 
                                    + (float)$d->total_gerais 
                                    + (float)$d->total_especiais 
                                    + (float)$d->total_missoes;
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
            pode_gravar($this->CONTROLE);
            
            $registro = $this->input->post('registro');            
            if (!$registro) {
                throw new Exception('Registro não especificado.');
            }
            
            //$filial_id = (int)$this->session->userdata('user.filial_id');
            //if (!$filial_id) {
            //    throw new Exception('Filial não especificada.');
            //}
            
            $id = is_seted($registro, 'id') ? (int)$registro['id'] : 0;             
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $pregador        = isset($registro['pregador'])       ?  $registro['pregador'] : '';
            $dia             = isset($registro['data'])           ?  $registro['data'] : '';
            $hora            = isset($registro['hora'])           ?  $registro['hora'] : '';
            $visitantes      = isset($registro['visitantes'])      ?  (int)$registro['visitantes'] : 0;           
            $conversoes      = isset($registro['conversoes'])     ?  (int)$registro['conversoes'] : 0;
            $criancas_ate12  = isset($registro['criancas_ate12']) ?  (int)$registro['criancas_ate12'] : 0;
            $total_pessoas   = isset($registro['total_pessoas'])  ?  (int)$registro['total_pessoas'] : 0;           
            
            if (empty($dia) || empty($hora)) {
                throw new Exception('Especifique Data e Hora.');
            }
            
            if (!is5_data_valida_br($dia)) {
                throw new Exception('Especifique uma Data válida.');
            }
            
            if (!is5_hora_valida($hora)) {
                throw new Exception('Especique uma Hora válida.');
            }           
            
            if (empty($pregador)) {
                throw new Exception('Especifique o Pregador.');
            }
                        
            $dados = [     
                'pregador'       => addslashes($pregador),
                'data'           => dateToDb($dia),
                'hora'           => $hora,
                'visitantes'     => $visitantes,
                'conversoes'     => $conversoes,
                'criancas_ate12' => $criancas_ate12,
                'total_pessoas'  => $total_pessoas,                
            ]; 
            
            /*if ($id) {
                if ($this->modelo->isTemp($id)) {
                    $dados['status']         = 'C'; //T - temp, C - confirmado
                    $dados['dh_insert']      = getDateTimeCurrent();
                    $dados['user_insert_id'] = get_user_id();
                }
                
                $dados['dh_edit'] = getDateTimeCurrent();
                $dados['user_edit_id'] = get_user_id();
            }*/
            
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
            
            $id = (int)$this->uri->segment( $this->uri->total_segments() );
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
    
    function pdf() {
        try {
            pode_ler($this->CONTROLE);
            
            $id = (int)$this->uri->segment( $this->uri->total_segments() );
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $diario = $this->modelo->get($id, NULL, TRUE);
            if (!$diario) {
                throw new Exception('Relatório não encontrado.');
            }
            
            $dados = [
                'diario'        => $diario,
                //pessoas
                'pastores'      => $this->pastores->pesquisar(['diario_id' => $id]),
                'diaconos'      => $this->diaconos->pesquisar(['diario_id' => $id]),
                'apresentacoes' => $this->apresentacoes->pesquisar(['diario_id' => $id]),
                'visitantes'    => $this->visitantes->pesquisar(['diario_id' => $id]),
                //valores
                'dizimos'    => $this->dizimos->pesquisar(['diario_id' => $id]),
                'ofertas'    => $this->ofertas->pesquisar(['diario_id' => $id]),
                'especiais'  => $this->especiais->pesquisar(['diario_id' => $id]),
                'missoes'    => $this->missoes->pesquisar(['diario_id' => $id]),
            ];
            
            $conteudo = $this->load->view('embaixada/relatorios/diario', $dados, TRUE);
            //echo $conteudo;
            showPDF($conteudo);
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }
    
    function getTotais() {
        $status = FALSE;
        $msg = NULL;
        $data = NULL;
        try {
            pode_ler($this->CONTROLE);
            
            $diario_id = (int)$this->uri->segment( $this->uri->total_segments() );
            if (!$diario_id) {
                throw new Exception('Relatório não especificado.');
            }
            
            $data = edf_get_totais_diario($diario_id);
            if ($data) {            
                $status = TRUE;
                $msg = 'Registros retornados: '. is5_count($data);
            } else {
                throw new Exception('Nenhum registro encontrao.');
            }
       
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }
    
    
}


