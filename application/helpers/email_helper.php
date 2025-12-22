<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function sendmail($destino, $assunto, $mensagem) {
    
    $CI =& get_instance();
       
    //Load email library
    $CI->load->library('email');
    $CI->email->from('fbx@papion.com.br', 'FrontBox');
    $CI->email->to($destino);
    $CI->email->subject($assunto);
    $CI->email->message($mensagem);    
    
    //Send mail
    return $CI->email->send();
    //return $CI->email->print_debugger();
    
    //sendmail2($destino, $assunto, $mensagem);
}

function sendmail_ci($destino, $assunto, $mensagem) {    
    $CI =& get_instance();
       
    //Load email library
    $CI->load->library('email');
    $CI->email->from('fbx@papion.com.br', 'FrontBox');
    $CI->email->to($destino);
    $CI->email->subject($assunto);
    $CI->email->message($mensagem);    
        
    return $CI->email->send();
    //return $CI->email->print_debugger();    
}

