<?php
/**
 * CORS Configuration
 * Configurações de Cross-Origin Resource Sharing
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// ========================================
// Determinar ambiente
// ========================================

$is_production = getenv('ENV') === 'production' || 
                 ($_SERVER['HTTP_HOST'] ?? '') === 'admcloud.papion.com.br' ||
                 ($_SERVER['HTTP_HOST'] ?? '') === 'api.admcloud.papion.com.br';

$is_local = $_SERVER['HTTP_HOST'] === 'localhost' || 
            $_SERVER['HTTP_HOST'] === '127.0.0.1' ||
            strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0;

$is_development = getenv('ENV') === 'development';

// ========================================
// Configuração CORS
// ========================================

$config['cors'] = array(

    // ========================================
    // Controle Geral
    // ========================================
    
    'enabled'               => TRUE,
    'allow_credentials'     => TRUE,            // Permitir cookies em requisições cross-origin
    'send_headers'          => TRUE,
    'log_cors_errors'       => TRUE,
    'strict_mode'          => TRUE,             // Validar rigorosamente


    // ========================================
    // Origens Permitidas (por ambiente)
    // ========================================
    
    'allowed_origins'       => $is_production ? array(
        // Produção - apenas domínios específicos
        'https://admcloud.papion.com.br',
        'https://app.admcloud.papion.com.br',
        'https://api.admcloud.papion.com.br',
        'http://104.234.173.105:7010',      // IP de teste/staging
        
        // Se tiver aplicação desktop/mobile específica
        // 'app://localhost',
        // 'capacitor://localhost',
        
    ) : ($is_development ? array(
        // Desenvolvimento - mais permissivo
        'http://localhost:3000',
        'http://localhost:8080',
        'http://localhost:4200',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:8080',
        'http://127.0.0.1:4200',
        'http://127.0.0.1:7010',            // IP local de teste
        'http://104.234.173.105:7010',      // IP remoto de teste
        'https://localhost:3000',
        'https://localhost:8080',
        
        // Frontend em desenvolvimento
        'http://localhost',
        'http://127.0.0.1',
        
        // Docker
        'http://localhost:80',
        'http://127.0.0.1:80',
        
    ) : array(
        // Teste - incluir também ambientes de teste
        'http://localhost',
        'http://127.0.0.1',
        'http://localhost:8000',
        'http://104.234.173.105:7010',
    )),

    'allow_any_origin'      => FALSE,           // PERIGOSO: Apenas para debug
    

    // ========================================
    // Métodos HTTP Permitidos
    // ========================================
    
    'allowed_methods'       => array(
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'PATCH',
        'OPTIONS',
        'HEAD'
    ),


    // ========================================
    // Headers Permitidos
    // ========================================
    
    'allowed_headers'       => array(
        // Headers obrigatórios
        'Content-Type',
        'Authorization',
        'Accept',
        'Origin',
        'User-Agent',
        'DNT',
        'Cache-Control',
        'X-Requested-With',
        
        // Headers customizados da API
        'X-Request-ID',
        'X-API-Key',
        'X-CSRF-Token',
        'X-Custom-Auth',
        'X-Device-ID',
        'X-App-Version',
        
        // Headers de segurança
        'X-Security-Token',
        'X-Platform',
        
        // Para file uploads
        'X-File-Name',
        'X-File-Size',
        'X-File-Type',
    ),


    // ========================================
    // Headers Expostos na Resposta
    // ========================================
    
    'exposed_headers'       => array(
        // Headers de resposta visíveis ao cliente
        'Content-Length',
        'Content-Type',
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset',
        'X-Request-ID',
        'X-API-Version',
        'Retry-After',
        'Location',                        // Para redirects
        'X-Total-Count',                   // Para paginação
        'Link',                            // Para links de HATEOAS
    ),


    // ========================================
    // Cache de Preflight
    // ========================================
    
    'max_age'               => $is_production ? 86400 : 3600,  // 24h produção, 1h development
    

    // ========================================
    // Logging e Monitoramento
    // ========================================
    
    'log_cors_violations'   => TRUE,
    'log_preflight'         => !$is_production, // Logar preflight apenas em dev
);

// ========================================
// Função Helper
// ========================================

if (!function_exists('get_cors_config')) {
    function get_cors_config($key = NULL)
    {
        $CI =& get_instance();
        $CI->config->load('cors', FALSE, TRUE);
        $config = $CI->config->item('cors', 'cors');
        
        if ($key === NULL) {
            return $config;
        }
        
        return isset($config[$key]) ? $config[$key] : NULL;
    }
}

if (!function_exists('is_cors_origin_allowed')) {
    function is_cors_origin_allowed($origin = NULL)
    {
        if ($origin === NULL) {
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        }
        
        $config = get_cors_config();
        
        if ($config['allow_any_origin']) {
            return TRUE;
        }
        
        $allowed = $config['allowed_origins'];
        
        foreach ($allowed as $pattern) {
            if ($origin === $pattern) {
                return TRUE;
            }
            if (strpos($pattern, '*') !== FALSE) {
                $regex = str_replace('*', '.*', preg_quote($pattern, '/'));
                if (preg_match('/^' . $regex . '$/', $origin)) {
                    return TRUE;
                }
            }
        }
        
        return FALSE;
    }
}

?>
