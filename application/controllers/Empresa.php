<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
use Restserver\Libraries\REST_Controller;

class Empresa extends REST_Controller {
    
    function __construct() {        
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
        
        $this->load->model('sys/Emitentes_model','emitentes');
    }
    
    public function index_get() {
        $status = FALSE;
        $msg = NULL;
        $data = [];
        try {
            pode_ler('empresa');
            
            $id = (int)$this->session->userdata('emit.id');
            
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $emitente = $this->emitentes->get($id);
            
            if (!$emitente) {
                throw new Exception("Nenhum registro encontrado.");
            }
            
            if (!empty($emitente->logo)) {
                $emitente->logo = base_url("uploads/$emitente->logo");
            } else {
                $emitente->logo = base_url("images/sem_imagem.jpg");
            }
            
            if (!empty($emitente->banner)) {
                $emitente->banner = base_url("uploads/$emitente->banner");
            } else {
                $emitente->banner = base_url("images/sem_imagem.jpg");
            }
            
            $status = TRUE;
            $msg    = 'Registro encontrado.'; 
            $data   = $emitente;
            
        } catch (Exception $ex) {
            $status = FALSE;
            $msg = $ex->getMessage();
        }
        
         $this->response(['status' => $status,
                          'msg'    => $msg, 
                          'data'   => $data,
                         ], REST_Controller::HTTP_OK);
    }

    public function index_post() {
        try {
            pode_gravar('empresa');
            
            $registro = $this->post('registro');
            if (!$registro) {
                throw new Exception('Registro não especificado.');
            }
            
            $action = $this->post('action');
            if ($action === 'update') {
                $this->update($registro);
                return;
            } else if ($action === 'changepass') {
                $this->changePass();
                return;
            } else {
                throw new Exception('Ação desconhecida.');
            }
            
        } catch (Exception $ex) {
            $this->response(['status' => 'ERRO', 
                    'msg' => $ex->getMessage()
                   ], REST_Controller::HTTP_OK);
        }
    }
    
    private function update($registro) {                                  
        $data = [];
        $img_logo = '';
        $img_banner = '';
        try {  
            pode_gravar('empresa');
            
            //ID PEGAR SEMPRE DA SESSION :P
            $id = (int)$this->session->userdata('emit.id');
            
            if (!$id) {
                throw new Exception('Registro não especificado.');
            }
            
            $nome          = isset($registro['nome']) ? $registro['nome'] : '';
            $cgc           = isset($registro['cgc']) ? SomenteNumeros( $registro['cgc']) : '';
            $fantasia      = isset($registro['fantasia']) ? $registro['fantasia'] : '';
            $descricao     = isset($registro['descricao']) ? $registro['descricao'] : '';           
            $endereco      = isset($registro['endereco']) ? $registro['endereco'] : '';
            $numero        = isset($registro['numero']) ? $registro['numero'] : '';
            $complemento   = isset($registro['complemento']) ? $registro['complemento'] : '';           
            $bairro        = isset($registro['bairro']) ? $registro['bairro'] : '';
            $cidade        = isset($registro['cidade']) ? $registro['cidade'] : '';
            $estado        = isset($registro['estado']) ? $registro['estado'] : '';            
            $email         = isset($registro['email']) ? $registro['email'] : '';
            $telefone      = isset($registro['telefone']) ? $registro['telefone'] : '';
            $celular       = isset($registro['celular']) ? $registro['celular'] : '';            
            $cep           = isset($registro['cep']) ? somenteNumeros( $registro['cep'] ) : '';            
            $logo          = isset($registro['logo']) ? $registro['logo'] : '';
            $banner        = isset($registro['banner']) ? $registro['banner'] : '';
            $alias         = isset($registro['alias']) ? $registro['alias'] : '';
            $facebook      = isset($registro['facebook']) ? $registro['facebook'] : '';
            $instagram     = isset($registro['instagram']) ? $registro['instagram'] : '';
            $dias_fechar_diario = isset($registro['dias_fechar_diario']) ? (int)$registro['dias_fechar_diario'] : 2;
                                    
            //if (!empty($tempo_entrega_minimo) && empty($tempo_entrega_maximo)) {
            //    throw new Exception('Especifique o tempo de entrega máximo ou deixe embos em branco.');
            //}
            
            //if (empty($tempo_entrega_minimo) && !empty($tempo_entrega_maximo)) {
            //    throw new Exception('Especifique o tempo de entrega mínimo ou deixe embos em branco.');
            //}  
            
            //if (empty($tempo_entrega_minimo) || empty($tempo_entrega_maximo)) {
            //    throw new Exception('Especifique o tempo de entrega.');
            //}
            
            if (!empty($email)) {
                if (!validar_email($email)) {
                    throw new Exception('Especifique um e-mail válido.');
                }
            }                    
                        
            if ($alias) {
                $alias = is5_strtolower($alias);
                $alias = remover_acentos($alias);
                $alias = somenteLetrasNumeros($alias);
            }
            
            $data = [     
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
                'alias'         => addslashes($alias),                 
                'facebook'      => addslashes($facebook),
                'instagram'     => addslashes($instagram), 
                'dias_fechar_diario' => $dias_fechar_diario,
            ];   
                                    
            if ($img_logo !== '') {
                $data['logo'] = $img_logo;
            }
            
            if ($img_banner !== '') {
                $data['banner'] = $img_banner;
            }
            
            //se foi excluido, zera
            if (strpos($logo, 'sem_imagem')) {
                $data['logo'] = '';
            }            
            if (strpos($banner, 'sem_imagem')) {
                $data['banner'] = '';
            }     
            
            $erro = $this->emitentes->validar($data, $id);
            if ($erro !== '') {
                throw new Exception($erro);
            }
            
            if ($logo && isB64($logo)) {
                $data['logo'] = $this->validarImagem($logo, 'logo_', 0, 0);
            }
            
            if ($banner && isB64($banner)) {
                $data['banner'] = $this->validarImagem($banner, 'banner_', 0, 0);
            }
            
            $resp = $this->emitentes->gravar($data, $id);
            if ($resp) {                
                $this->response(['status' => 'OK', 
                                 'msg' => 'Registro atualizado.'
                                ], REST_Controller::HTTP_OK);                
            } else {
                throw new Exception('Erro ao atualizar dados. Tente novamente.');
            }            
            
        } catch (Exception $ex) {
            $this->response(['status' => 'ERRO', 
                             'msg' => $ex->getMessage()
                            ], REST_Controller::HTTP_OK);
        }
    }       
        
    private function validarImagem($imagem, $prefix = 'img_', $w = 500, $h = 500) {
        $img_name = '';
        $b64_img = extractB64($imagem);
                
        if (empty($b64_img)) {
            throw new Exception('Impossível recuperar imagem do Produto.');
        }

        //gera nome destino
        $img_name = uniqid($prefix).'.jpg';
        //$img_path = '/home/nextingresso/api/www'.$this->session->userdata('emitente_home').'/'.$img_name;
        $img_path = "/home/embaixadadafe/console/www/uploads/$img_name";

        //salva arquivos
        file_put_contents($img_path, base64_decode($b64_img));

        //verificar se realmente eh uma imagem
        $r = getimagesize($img_path);
        if (!$r) {
            unlink($img_path);
            throw new Exception('Especifique uma imagem válida para o Produto.');
        }

        //verificar tamanho 
        if (filesize($img_path) > (5 * 1024 * 1024)) {
            unlink($img_path);
            throw new Exception("A imagem ($prefix) não pode ser maior que 5MB.");
        }

        //verificar resolucao
        if ($w > 0 && $h > 0) {
            if ( ($r[0] < $w) || ($r[1] < $h) ) {
                //$r[0] = width
                //$r[1] = height
                unlink($img_path);
                throw new Exception("A imagem deve ter uma resolução maior ou igual a $w x $h.\n\rResolução da imagem enviada: $r[0]x$r[1].");
            }
        }

        //verificar tipo
        $mime = getMimeType($img_path);
        if (!in_array($mime, ['image/png','image/jpeg','image/jpg'])) {
            unlink($img_path);
            throw new Exception('A imagem deve estar no formato JPG, JPEG ou PNG.');
        }
        
        return $img_name;
    }


    
    
    
}


