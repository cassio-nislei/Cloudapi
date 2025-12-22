<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
    }
    
    function home() {          
        $data['index'] = 0;
        $data['content'] = $this->load->view('home/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);             
    }
    
    function empresa() { 
        $data['index'] = 1;
        $data['content'] = $this->load->view('empresa/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    
    function usuarios() {         
        $data['index'] = 2;
        $data['content'] = $this->load->view('usuarios/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    
    function perfil() {
        $data['index'] = 3;
        $data['content'] = $this->load->view('perfil/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    
    //SYSOP
    function modulos() {
        $data['index'] = 3;
        $data['content'] = $this->load->view('modulos/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    
    function grupos() {
        $data['index'] = 3;
        $data['content'] = $this->load->view('grupos/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    //SYSOP FIM
    
    function pessoas() {
        $data['index'] = 3;
        $data['content'] = $this->load->view('pessoas/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    
    /// EMBAIXADA 
    function filiais() {
        $data['index'] = 3;
        $data['content'] = $this->load->view('embaixada/filiais/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    
    function relatorios() {        
        $data['index'] = 3;
        $data['content'] = $this->load->view('relatorios/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    
    function aprovacoes() {
        $data['index'] = 3;
        $data['content'] = $this->load->view('aprovacoes/index',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    
    function filial() {
        $filial_id = (int)$this->uri->segment(2);
        $data['index'] = 3;
        $data['content'] = $this->load->view('embaixada/filiais/filial',['filial_id' => $filial_id],TRUE);
        $this->load->view('templates/dashboard', $data);                
    }
    
    
        
       
    
    
    
}
