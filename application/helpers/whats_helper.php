<?php defined('BASEPATH') OR exit('No direct script access allowed');

//define('END_POINT_ZAP','http://191.250.26.197:3281/whats/');
define('END_POINT_ZAP','http://10.25.0.30:3282/whats/'); 

//3281: Getmenu
//3282: Next

function whats_getauth() { 
    $username   = 'user'; //'NextIngresso';
    $token_auth = '123';  //'N3xt@5201';
    
    return base64_encode($username.':'.$token_auth);
}

function whats_get($url, $auth = TRUE) {	
    try {
        $url = END_POINT_ZAP.$url; 

        $header[] = 'Content-Type: application/json; charset=utf-8';
        
        if ($auth) {
            $header[] = 'Authorization: Basic '.whats_getauth();
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
                
        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response);

        return [( (json_last_error() === 0) && $json ), $json, $response];
        
    } catch (Exception $ex) {
        //cho "Erro: ".$ex->getMessage();
        return [NULL, NULL, NULL];
    }
}

function whats_post($url, $data, $auth = TRUE) {
    try {
        $url = END_POINT_ZAP.$url; 
        
        $header = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        if ($auth) {
            $header[] = 'Authorization: Basic '.whats_getauth();
        }

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

function whats_post_return($url, $data) {
    //retorna de forma mais simples, jah validando por aki 
    try {
        list($ok, $json) = whats_post($url, $data);
            
        if (!$ok) {
            throw new Exception('Erro ao processar requisição. Tente novamente.');
        }

        if (isset($json->Status)) {
            if ($json->Status === 'OK') {                
                return [TRUE, $json->Mensagem, $json];                                
            } else {
                throw new Exception($json->Mensagem);
            }
        } else {
            throw new Exception('Erro ao retornar Status. Tente novamente.');
        }
        
    } catch (Exception $ex) {
        return [FALSE, $ex->getMessage(), NULL];
    }
}

function whats_enviar($numero, $mensagem, $nome = '') {
    //retorna de forma mais simples, jah validando por aki 
    $status = FALSE;
    $response = '';
    try {
        // = '/home/suporte/scripts/wsbsend.py "'.somenteNumeros($numero).'" "'.$mensagem.'"  > /dev/null 2>&1 &';
        //shell_exec($cmd);
        //return [TRUE, 'OK', 'OK'];  
        
        $data = [
            'numero'   => somenteNumeros($numero),
            'nome'     => $nome,
            'mensagem' => $mensagem
        ]; 
        
        list($ok, $json, $response) = whats_post('enviar', $data);
            
        if (!$ok) {
            throw new Exception('Erro ao processar requisição: '.$response);
        }

        if (isset($json->Status)) {
            if ($json->Status === 'OK') {                
                return [TRUE, $json->Mensagem, $json];                                
            } else {
                throw new Exception($json->Mensagem);
            }
        } else {
            throw new Exception('Erro ao retornar Status. Tente novamente.');
        }
        
    } catch (Exception $ex) {
        return [FALSE, $ex->getMessage(), $response];
    }
}

function whats_get_return($url, $data) {
    //retorna de forma mais simples, jah validando por aki 
    try {
        list($ok, $json) = whats_get($url, $data);
            
        if (!$ok) {
            throw new Exception('Erro ao solicitar dados. Tente novamente.');
        }

        if (isset($json->Status)) {
            if ($json->Status === 'OK') {                
                return [TRUE, $json->msg];                                
            } else {
                throw new Exception($json->Mensagem);
            }
        } else {
            throw new Exception('Erro ao retornar Status. Tente novamente.');
        }
        
    } catch (Exception $ex) {
        return [FALSE, $ex->getMessage()];
    }
}

