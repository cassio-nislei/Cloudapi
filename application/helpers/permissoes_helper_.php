<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function pode_acessar($controle) {
    $CI =& get_instance();
    try  {
        $emitente_id = (int)$CI->session->userdata('emit.id');
        $usuario_id  = (int)$CI->session->userdata('user.ID');
        
        return $CI->db->where(['emitente_id'     => $emitente_id,
                               'usuario_id'      => $usuario_id,
                               'lower(controle)' => addslashes(is5_strtolower($controle))])
                      ->count_all_results('vw_permissoes') > 0;
        
    } catch (Exception $ex) {
        //
    }
    return FALSE;
}

function _check_permissao($controle, $field) {
    $CI =& get_instance();
    try  {
        $emitente_id = (int)$CI->session->userdata('emit.id');
        $usuario_id  = (int)$CI->session->userdata('user.ID');
        
        return $CI->db->where(['emitente_id'     => $emitente_id,
                               'usuario_id'      => $usuario_id,
                               $field            => 'S',
                               'lower(controle)' => addslashes(is5_strtolower($controle))])
                      ->count_all_results('vw_permissoes') > 0;
        
    } catch (Exception $ex) {
        //
    }
    return FALSE;
}

function pode_ler($controle, $exception = TRUE) {
    $return = _check_permissao($controle, 'ler');
    if (!$return && $exception) {
        throw new Exception('Sem permissão acessar este recurso.');        
    }
    return $return;
}

function pode_gravar($controle, $exception = TRUE) {
    $return = _check_permissao($controle, 'gravar');
    if (!$return && $exception) {
        throw new Exception('Sem permissão acessar este recurso.');        
    }
    return $return;
}

function pode_excluir($controle, $exception = TRUE) {
    $return = _check_permissao($controle, 'excluir');
    if (!$return && $exception) {
        throw new Exception('Sem permissão acessar este recurso.');        
    }
    return $return;
}

