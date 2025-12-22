<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CORS Helper
 * Gerencia headers CORS para permitir requisições cross-origin
 */

/**
 * Define headers CORS
 * @param array $allowed_origins Origens permitidas
 * @param array $allowed_methods Métodos HTTP permitidos
 * @param array $allowed_headers Headers permitidos
 * @param bool $allow_credentials Se permite credenciais
 * @return bool
 */
function set_cors_headers($allowed_origins = array(), $allowed_methods = array(), $allowed_headers = array(), $allow_credentials = true) {
    
    // Origens permitidas padrão
    if (empty($allowed_origins)) {
        $allowed_origins = array(
            'https://admcloud.papion.com.br',
            'https://www.fbx.net.br',
            'http://localhost:3000',  // Desenvolvimento
            'http://localhost:8080',  // Desenvolvimento
            'http://localhost'        // Desenvolvimento
        );
    }
    
    // Métodos HTTP permitidos padrão
    if (empty($allowed_methods)) {
        $allowed_methods = array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH');
    }
    
    // Headers permitidos padrão
    if (empty($allowed_headers)) {
        $allowed_headers = array(
            'Content-Type',
            'Authorization',
            'X-Requested-With',
            'Accept',
            'Origin'
        );
    }
    
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    
    // Verificar se origem está na whitelist
    if (!empty($origin) && in_array($origin, $allowed_origins)) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Methods: " . implode(', ', $allowed_methods));
        header("Access-Control-Allow-Headers: " . implode(', ', $allowed_headers));
        header("Access-Control-Max-Age: 86400"); // 24 horas
        
        if ($allow_credentials) {
            header("Access-Control-Allow-Credentials: true");
        }
        
        return true;
    }
    
    // Se origin não está na whitelist, ainda permitir mas sem credenciais
    if (!empty($origin)) {
        header("Access-Control-Allow-Origin: " . htmlspecialchars($origin));
        header("Access-Control-Allow-Methods: " . implode(', ', $allowed_methods));
        header("Access-Control-Allow-Headers: " . implode(', ', $allowed_headers));
    }
    
    // Responder a OPTIONS pre-flight request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header("HTTP/1.1 200 OK");
        exit(0);
    }
    
    return false;
}

/**
 * Retorna configuração CORS padrão
 * @return array Configuração CORS
 */
function get_cors_config() {
    return array(
        'allowed_origins' => array(
            'https://admcloud.papion.com.br',
            'https://www.fbx.net.br',
            'http://localhost:3000',
            'http://localhost:8080',
            'http://localhost'
        ),
        'allowed_methods' => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'),
        'allowed_headers' => array(
            'Content-Type',
            'Authorization',
            'X-Requested-With',
            'Accept',
            'Origin'
        ),
        'max_age' => 86400,
        'allow_credentials' => true
    );
}

/**
 * Adiciona origem CORS permitida
 * @param string $origin Origem a adicionar
 * @return bool Sucesso da operação
 */
function add_cors_origin($origin) {
    // Validar formato da origem
    if (!filter_var($origin, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    $ci =& get_instance();
    $ci->load->database();
    
    // Salvar em banco se tiver tabela de configuração
    if ($ci->db->table_exists('cors_origins')) {
        $data = array(
            'origin' => $origin,
            'data_criacao' => date('Y-m-d H:i:s')
        );
        
        $ci->db->insert('cors_origins', $data);
        return true;
    }
    
    return false;
}

/**
 * Remove origem CORS permitida
 * @param string $origin Origem a remover
 * @return bool Sucesso da operação
 */
function remove_cors_origin($origin) {
    $ci =& get_instance();
    $ci->load->database();
    
    if ($ci->db->table_exists('cors_origins')) {
        $ci->db->delete('cors_origins', array('origin' => $origin));
        return true;
    }
    
    return false;
}

/**
 * Retorna todas as origens CORS permitidas do banco
 * @return array Lista de origens
 */
function get_cors_origins() {
    $ci =& get_instance();
    $ci->load->database();
    
    $origins = array();
    
    if ($ci->db->table_exists('cors_origins')) {
        $query = $ci->db->get('cors_origins');
        foreach ($query->result() as $row) {
            $origins[] = $row->origin;
        }
    }
    
    return $origins;
}

/**
 * Verifica se uma origem está permitida
 * @param string $origin Origem a verificar
 * @return bool
 */
function is_cors_origin_allowed($origin) {
    $allowed_origins = get_cors_config()['allowed_origins'];
    return in_array($origin, $allowed_origins);
}
