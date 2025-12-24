<?php
/**
 * Rate Limiting Configuration
 * Configurações de limite de requisições
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['rate_limiting'] = array(

    // ========================================
    // Configurações Gerais
    // ========================================
    
    'enabled'           => TRUE,           // Ativar/desativar rate limiting
    'max_requests'      => 1000,           // Máximo de requisições permitidas
    'time_window'       => 3600,           // Janela de tempo em segundos (1 hora)
    'storage'           => 'database',     // 'database' ou 'file'
    'storage_path'      => 'application/logs/rate_limit/', // Caminho para armazenamento em arquivo
    'identify_by'       => 'ip',           // 'ip' ou 'user_id' - como identificar cliente
    'headers_enabled'   => TRUE,           // Incluir X-RateLimit-* headers
    'log_violations'    => TRUE,           // Logar quando limite é excedido

    // ========================================
    // Limites por Tipo de Requisição
    // ========================================
    
    'per_endpoint'      => array(
        // Path => max_requests per time_window
        'api/v1/passport'           => 500,    // Passport é crítico, limite maior
        'api/v1/registro'           => 100,    // Registro é protegido
        'Account/login'             => 20,     // Login tem limite menor por segurança
        'Account/logout'            => 50,
        'Account/perfil'            => 200,
        'usuarios'                  => 150,
        'pessoas'                   => 200,
        'ncm'                       => 300,
    ),

    // ========================================
    // Limites por Tipo de Usuário
    // ========================================
    
    'per_role'          => array(
        'admin'                     => 5000,   // Admins tem limite mais alto
        'user'                      => 1000,   // Usuários normais
        'guest'                     => 100,    // Visitantes têm limite muito baixo
    ),

    // ========================================
    // Whitelist - IPs e Caminhos Permitidos
    // ========================================
    
    'whitelist_ips'     => array(
        '127.0.0.1',                          // Localhost
        '::1',                                // IPv6 localhost
        // Adicionar IPs internos/confiáveis:
        // '10.0.0.0/8',                      // Rede privada
        // '192.168.0.0/16',                  // Rede privada
        // Adicionar IPs de servidores internos
    ),

    'whitelist_paths'   => array(
        // Paths que não são limitados
        'health',
        'status',
        'ping',
        'robots.txt',
        'sitemap.xml',
        // Health check endpoints
        '#^api/v1/health#',
        '#^api/v1/status#',
        // Paths administrativos podem ser excluídos
        // '#^admin/#',
    ),

    'whitelist_headers' => array(
        // Headers que indicam requisição confiável
        'X-API-Key',
        'X-Admin-Token',
    ),

    // ========================================
    // Ações ao Exceder Limite
    // ========================================
    
    'on_limit_exceeded' => array(
        'action'                    => 'block',      // 'block' ou 'log'
        'response_code'             => 429,          // HTTP 429 Too Many Requests
        'response_type'             => 'json',       // 'json' ou 'text'
        'include_retry_after'       => TRUE,         // Incluir header Retry-After
        'custom_message'            => 'Too many requests. Please try again later.',
    ),

    // ========================================
    // Limpeza de Dados Antigos
    // ========================================
    
    'cleanup'           => array(
        'enabled'                   => TRUE,         // Ativar limpeza automática
        'interval'                  => 86400,        // Executar a cada 24 horas
        'keep_days'                 => 7,            // Manter registros dos últimos 7 dias
        'last_run'                  => NULL,         // Será preenchido automaticamente
    ),

    // ========================================
    // Logging e Monitoramento
    // ========================================
    
    'logging'           => array(
        'log_to_file'               => TRUE,         // Logar em arquivo
        'log_to_database'           => TRUE,         // Logar em banco de dados
        'log_level'                 => 'warning',    // 'debug', 'info', 'warning', 'error'
        'alert_threshold'           => 900,          // Alertar quando atinge % do limite
        'alert_recipients'          => array(),      // Emails para alertas
    ),

    // ========================================
    // Configurações Avançadas
    // ========================================
    
    'advanced'          => array(
        'use_redis'                 => FALSE,        // Usar Redis para cache distribuído
        'redis_host'                => 'localhost',
        'redis_port'                => 6379,
        'distributed'               => FALSE,        // Rate limiting distribuído entre servidores
        'sync_interval'             => 60,           // Sincronizar a cada 60 segundos
    ),

    // ========================================
    // Tratamento Especial de Bots
    // ========================================
    
    'bot_detection'     => array(
        'enabled'                   => TRUE,
        'detect_bots'               => TRUE,
        'bot_limit'                 => 50,           // Bots têm limite menor
        'block_suspicious_agents'   => FALSE,        // Bloquear user-agents suspeitos
        'suspicious_patterns'       => array(
            'scanner',
            'crawler',
            'bot',
            'spider',
        ),
    ),

    // ========================================
    // Regras de Rate Limit Dinâmico
    // ========================================
    
    'dynamic_limits'    => array(
        'enabled'                   => TRUE,
        // Aumentar limite durante horários de baixo tráfego
        'peak_hours'                => array(
            'start'                 => 9,       // 9 AM
            'end'                   => 18,      // 6 PM
        ),
        'peak_multiplier'           => 1.5,     // 1.5x limites normais durante pico
        'off_peak_multiplier'       => 2.0,     // 2x limites normais fora do pico
    ),
);

/**
 * Função helper para obter configuração de rate limit
 */
if (!function_exists('get_rate_limit_config')) {
    function get_rate_limit_config($key = NULL)
    {
        $CI =& get_instance();
        $CI->config->load('rate_limiting', FALSE, TRUE);
        $config = $CI->config->item('rate_limiting', 'rate_limiting');
        
        if ($key === NULL) {
            return $config;
        }
        
        return isset($config[$key]) ? $config[$key] : NULL;
    }
}

?>
