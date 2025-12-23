<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diarios extends CI_Controller {
    
    private $CONTROLE = 'relatorios';
    
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
        
        $diario = $this->modelo->get($id);
        
        $data['content'] = $this->load->view('embaixada/diarios/diario',['diario_id' => $id, 'diario' => $diario],TRUE);
        $this->load->view('templates/dashboard', $data);
    }
    
    function editar() {    
        $id = $this->uri->segment( $this->uri->total_segments() );
        $diario = $this->modelo->get($id);
        $data['content'] = $this->load->view('embaixada/diarios/diario',['diario_id' => $id, 'diario' => $diario],TRUE);
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
            
            $data = $this->modelo->get($id);
            
            if ($data) {
                $status = TRUE;
                $msg    = 'Registro encontrado';     
                
                $data->data = dateToBr($data->data);
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
            
            if (is5_data_valida_br($data_inicial) && is5_data_valida_br($data_final)) {
                $data_inicial = dateToDb($data_inicial);
                $data_final   = dateToDb($data_final);
                
                $data = $this->modelo->pesquisar(['data >=' => $data_inicial, 'data <=' => $data_final]);
                
            } else {
                $data = $this->modelo->getAll();
            }
            
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
            
            $filial_id = (int)$this->session->userdata('user.filial_id');
            
            if (!$filial_id) {
                throw new Exception('Filial não especificada.');
            }
            
            $id              = is_seted($registro, 'id')          ?  (int)$registro['id'] : 0;            
            $pregador        = isset($registro['pregador'])       ?  $registro['pregador'] : '';
            $dia             = isset($registro['data'])           ?  $registro['data'] : '';
            $hora            = isset($registro['hora'])           ?  $registro['hora'] : '';
            $adultos         = isset($registro['adultos'])        ?  (int)$registro['adultos'] : 0; 
            $visitantes      = isset($registro['visitantes'])     ?  (int)$registro['visitantes'] : 0;           
            $conversoes      = isset($registro['conversoes'])     ?  (int)$registro['conversoes'] : 0;
            $criancas_ate12  = isset($registro['criancas_ate12']) ?  (int)$registro['criancas_ate12'] : 0;
            //$total_pessoas   = isset($registro['total_pessoas'])  ?  (int)$registro['total_pessoas'] : 0;           
            $total_pessoas   = ($adultos + $criancas_ate12);          
            
            //if ($id) {
            //    //depois de confirmado, tesoureiro nao pode mais editar
            //    if (isTesoureiro()) {
            //        if ($this->modelo->isConfirmado($id)) {
            //            throw new Exception('O relatório já foi confirmado e não pode ser editado.');
            //        }
            //    }
            //}
            
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
                'adultos'        => $adultos,
                'visitantes'     => $visitantes,
                'conversoes'     => $conversoes,
                'criancas_ate12' => $criancas_ate12,
                'total_pessoas'  => $total_pessoas,                
            ]; 
            
            if ($id) {
                if ($this->modelo->isTemp($id)) {
                    $dados['status']         = 'C'; //T - temp, C - confirmado
                    $dados['dh_insert']      = getDateTimeCurrent();
                    $dados['user_insert_id'] = get_user_id();
                } else {                
                    $dados['dh_edit'] = getDateTimeCurrent();
                    $dados['user_edit_id'] = get_user_id();
                }
            }
            
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
            
            $diario = $this->modelo->get($id);
            if (!$diario) {
                throw new Exception('Registro não encontrado.');
            }
            
            //tesoureiro somente pode apagar relatorios gerados no mesmo dia
            if (isTesoureiro()) {
                if ($diario->data !== getDateCurrent()) {
                    throw new Exception('Tesoureiros somente podem editar relatórios gerados no mesmo dia.');
                }
            }
            
            //se nao eh pastor, nao pode excluir se o prazo foi ultrapassado
            if (isPastor()) {
                $limite = edf_diario_limite_dias_edicao();        
                $data = new DateTime($diario->data);
                $hoje = new DateTime(getDateCurrent());

                $diff = $data->diff($hoje)->format("%a"); //ret valor absluto (sem sinal)
                
                if ($diff > $limite) {
                    throw new Exception("O relatório não pode ser excluído. Prazo de $limite dias ultrapassado.");
                }
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
            
            //if (edf_tem_alteracoes($id)) {
            //    throw new Exception('<center>O Relatório tem alterações não confirmadas e não pode ser impresso.</center>');
            //}
            
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
    
    function excluirAlteracao() {
        $status = FALSE;
        $msg = NULL;        
        try {   
            //pode_excluir($this->CONTROLE);
            
            $diario_id = (int)$this->input->post('diario_id');
            if (!$diario_id) {
                throw new Exception('Relatório não especificado.');
            }
            
            $alteracao_id = (int)$this->input->post('id');
            if (!$alteracao_id) {
                throw new Exception('Alteracao não especificada.');
            }
            
            //verifica se o relatorio esta vinculado ao emitente
            $count = $this->db->where(['id' => $diario_id, 'emitente_id' => (int)$this->session->userdata('emit.id')])
                              ->count_all_results('edf_diarios');
            
            if (!$count) {
                throw new Exception('Relatório não encontrado.');
            }
            
            $resp = $this->db->where(['id' => $alteracao_id, 'diario_id' => $diario_id])
                             ->delete('edf_diario_alteracoes');
            
            if (!$resp) {
                throw new Exception('Erro ao excluir alteração.');
            } 
            
            $status = TRUE;
            $msg = 'Registro excluído.';
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }
    
}


