<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function comunica_ingresso_cortesia($nome, $email, $token, $evento, $obs = NULL) {
    try {
        $CI =& get_instance();
    
        $link = "https://nextingresso.com.br/cortesia/$token";   

        $dados = [
                    'nome'   => $nome, 
                    'link'   => $link, 
                    'evento' => $evento,
                    'obs'    => $obs
                ];
        
        $data['content'] = $CI->load->view('comunica/cortesia', $dados, TRUE);    
        $msg = $CI->load->view('templates/email_template', $data, TRUE);

        return sendmail($email, 'Você recebeu um Ingresso Cortesia', $msg);
        
    } catch (Exception $ex) {
        //
    }
    return FALSE;
}

function comunica_ingresso_nomeado($nome, $email, $token, $evento) {
    try {
        $CI =& get_instance();
    
        $link = "https://nextingresso.com.br/ingresso/$token";   

        $data['content'] = $CI->load->view('comunica/ingresso_nomeado', ['nome' => $nome, 'link' => $link, 'evento' => $evento], TRUE);    
        $msg = $CI->load->view('templates/email_template', $data, TRUE);

        sendmail_ci($email, 'Você recebeu um ingresso', $msg);        
        
    } catch (Exception $ex) {
        //
    }
}

