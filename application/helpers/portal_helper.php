<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function portal_gerar_alias($nome) {
    $i = 1;
    $valido = false;
    try {
        $CI =& get_instance();
        /* gera o slug */
        $CI->load->library('Slug','slug');
        $slug = $CI->slug->gen($nome);

        /* valido o slug */
        $tmp = $slug;
        while ($valido == false) {
            $valido = portal_validar_slug($tmp);
            if ($valido) {
                $slug = $tmp;
                break;
            }else {                    
                $tmp = $slug.'-'.$i;
                $i++;
            }       
        }            
        return $slug;

    } catch (Exception $ex) {
        return null;
    }
}

function portal_validar_slug($slug) {
    $CI =& get_instance();
    
    /* verifica se jah existe outro com mesmo nome */
    $cont = $CI->db->where(['alias' => $slug])                     
                    ->count_all_results('sys_emitentes');

    return ($cont == 0);
}

function portal_getusuarios() {
    $CI =& get_instance();
    try {
        $resp = $CI->db->where(['emitente_id' => (int)$CI->session->userdata('user.emitente_id')])
                       ->order_by('nome')
                       ->get('vw_usuarios');

        if ($resp->num_rows()) {
            return $resp->result_object();
        }   
        
    } catch (Exception $ex) {
        //
    }
    return NULL;
}

