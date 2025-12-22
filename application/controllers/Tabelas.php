<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tabelas extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        //se nao estiver logado, direciona para login
        if ($this->session->userdata('logado') !== TRUE) {           
            redirect(base_url('Account/login'));
        }
    }
    
    function ncm() {          
        $data['index'] = 0;
        $data['content'] = $this->load->view('tabelas/ncm',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);             
    }
    
    function cfop() {          
        $data['index'] = 0;
        $data['content'] = $this->load->view('tabelas/cfop',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);             
    }
    
    function cest() {          
        $data['index'] = 0;
        $data['content'] = $this->load->view('tabelas/cest',NULL,TRUE);
        $this->load->view('templates/dashboard', $data);             
    }
    
}
