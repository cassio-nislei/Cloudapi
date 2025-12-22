<?php defined('BASEPATH') OR exit('No direct script access allowed');

define('END_POINT','https://api.nextingresso.com.br000/v1/');

function api_getauth() { 
    $CI =& get_instance();
    
    $username = $CI->session->userdata('_email');
    $token_auth = $CI->session->userdata('_token_auth');
    
    return base64_encode($username.':'.$token_auth);
}

function api_get($url) {	
    try {
        $url = END_POINT.$url; 
        //$auth = base64_encode(USERNAME.':'.PASSWORD);

        $header = [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.api_getauth()
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
        
        
        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response);

        return [( (json_last_error() === 0) && $json ), $json, $response ];
        
    } catch (Exception $ex) {
        //cho "Erro: ".$ex->getMessage();
        return [NULL, NULL, NULL];
    }
}

function api_post($url, $data) {
    try {
        $url = END_POINT.$url; 
        //$auth = base64_encode(USERNAME.':'.PASSWORD);

        $header = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Basic '.api_getauth()
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response);

        return [( (json_last_error() === 0) && $json ), $json, $response ];
        
    } catch (Exception $ex) {
        gravarLog('Erro zoop_post_api: '.$ex->getMessage());
        return [NULL, NULL, NULL];
    }
}

function api_post_return($url, $data) {
    //retorna de forma mais simples, jah validando por aki 
    try {
        list($ok, $json) = api_post($url, $data);
            
        if (!$ok) {
            throw new Exception('Erro ao processar requisição. Tente novamente.');
        }

        if (isset($json->status)) {
            if ($json->status === 'OK') {                
                return [TRUE, $json->msg, $json];                                
            } else {
                throw new Exception($json->msg);
            }
        } else {
            throw new Exception('Erro ao retornar Status. Tente novamente.');
        }
        
    } catch (Exception $ex) {
        return [FALSE, $ex->getMessage(), NULL];
    }
}

function api_get_return($url, $data) {
    //retorna de forma mais simples, jah validando por aki 
    try {
        list($ok, $json) = api_get($url, $data);
            
        if (!$ok) {
            throw new Exception('Erro ao solicitar dados. Tente novamente.');
        }

        if (isset($json->status)) {
            if ($json->status === 'OK') {                
                return [TRUE, $json->msg];                                
            } else {
                throw new Exception($json->msg);
            }
        } else {
            throw new Exception('Erro ao retornar Status. Tente novamente.');
        }
        
    } catch (Exception $ex) {
        return [FALSE, $ex->getMessage()];
    }
}

function api_delete($url) {	
    try {
        $url = END_POINT.$url;         

        $header = [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.api_getauth()
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response);

        return [( (json_last_error() === 0) && $json ), $json ];
        
    } catch (Exception $ex) {
        //cho "Erro: ".$ex->getMessage();
        return [NULL, NULL];
    }
}

