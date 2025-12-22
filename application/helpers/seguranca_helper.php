<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function auth_token() {    
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        //filtra inputs
        $id    = $_SERVER['PHP_AUTH_USER'];
        $token = $_SERVER['PHP_AUTH_PW'];

        if ( ($id === 'api_frontbox') && ($token === 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg') ) {
            return TRUE;
        }
    }
    
    //se nao especificado, ou sem permissao, bloqueia e pede autenticacao
    //header('WWW-Authenticate: Basic'); //realm="Indentifique-se"
    //header('HTTP/1.0 401 Unauthorized');            
    //die('Acesso negado.');
    die ( json_encode(['status' => 'NO_AUTH', 'msg' => 'Acesso negado. Sem autorizacao!']) );    
}

function getHashId() {
    $CI =& get_instance();
    $salt = '6fcd8db26a1d64bd69740c442ef752da7a5d8e70';
    return sha1( $salt . $CI->session->userdata('_ID') . ':' . $CI->session->userdata('_emitente_id') . ':' . $CI->session->userdata('_emitente_cgc') );
}

