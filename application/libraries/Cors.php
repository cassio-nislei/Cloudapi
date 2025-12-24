<?php
/**
 * CORS (Cross-Origin Resource Sharing) Library for CodeIgniter
 * Implementa CORS com segurança
 * 
 * Uso:
 * $this->load->library('cors');
 * $this->cors->handle();
 */

class Cors
{
    private $CI;
    private $config = array();
    private $default_config = array(
        'enabled'               => TRUE,
        'allowed_origins'       => array(),      // Array de origens permitidas
        'allow_credentials'     => TRUE,         // Permitir credenciais
        'allowed_methods'       => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'),
        'allowed_headers'       => array(
            'Content-Type',
            'Authorization',
            'X-Requested-With',
            'X-Request-ID',
            'Accept',
            'Origin',
            'Cache-Control',
            'X-CSRF-Token'
        ),
        'exposed_headers'       => array(
            'Content-Length',
            'X-RateLimit-Limit',
            'X-RateLimit-Remaining',
            'X-RateLimit-Reset',
            'X-Request-ID'
        ),
        'max_age'              => 3600,          // Tempo de cache do preflight
        'send_headers'         => TRUE,
        'log_cors_errors'      => TRUE,
        'allow_any_origin'     => FALSE,         // Perigoso: apenas para desenvolvimento
        'strict_mode'          => TRUE,          // Validação rigorosa
    );

    public function __construct($config = array())
    {
        $this->CI =& get_instance();
        $this->config = array_merge($this->default_config, $config);
        
        log_message('debug', 'CORS Library Initialized');
    }

    /**
     * Processar requisição CORS
     */
    public function handle()
    {
        if (!$this->config['enabled']) {
            return;
        }

        // Verificar se é requisição com Origin header
        if (!isset($_SERVER['HTTP_ORIGIN'])) {
            return;
        }

        $origin = $_SERVER['HTTP_ORIGIN'];

        // Validar origem
        if (!$this->validate_origin($origin)) {
            if ($this->config['log_cors_errors']) {
                log_message('warning', "CORS: Invalid origin rejected - {$origin}");
            }
            // Não retornar headers CORS se origem inválida
            return;
        }

        // Se preflight request (OPTIONS)
        if ($this->CI->input->method() === 'options') {
            $this->handle_preflight($origin);
            exit;
        }

        // Se requisição normal
        $this->set_cors_headers($origin);
    }

    /**
     * Definir headers CORS
     */
    private function set_cors_headers($origin)
    {
        header("Access-Control-Allow-Origin: {$origin}");
        
        if ($this->config['allow_credentials']) {
            header("Access-Control-Allow-Credentials: true");
        }

        if (!empty($this->config['exposed_headers'])) {
            header("Access-Control-Expose-Headers: " . implode(', ', $this->config['exposed_headers']));
        }

        header("Access-Control-Max-Age: {$this->config['max_age']}");
    }

    /**
     * Processar preflight request
     */
    private function handle_preflight($origin)
    {
        $requested_method = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ?? 'GET';
        $requested_headers = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? '';

        // Validar método
        if (!in_array(strtoupper($requested_method), $this->config['allowed_methods'])) {
            if ($this->config['log_cors_errors']) {
                log_message('warning', "CORS: Method not allowed - {$requested_method}");
            }
            http_response_code(403);
            exit;
        }

        // Validar headers
        if (!empty($requested_headers)) {
            $requested_headers_arr = array_map('trim', explode(',', $requested_headers));
            
            if ($this->config['strict_mode']) {
                foreach ($requested_headers_arr as $header) {
                    if (!in_array($header, $this->config['allowed_headers'])) {
                        if ($this->config['log_cors_errors']) {
                            log_message('warning', "CORS: Header not allowed - {$header}");
                        }
                        http_response_code(403);
                        exit;
                    }
                }
            }
        }

        // Retornar headers de sucesso
        http_response_code(200);
        header("Access-Control-Allow-Origin: {$origin}");
        header("Access-Control-Allow-Methods: " . implode(', ', $this->config['allowed_methods']));
        header("Access-Control-Allow-Headers: " . implode(', ', $this->config['allowed_headers']));
        
        if ($this->config['allow_credentials']) {
            header("Access-Control-Allow-Credentials: true");
        }
        
        header("Access-Control-Max-Age: {$this->config['max_age']}");
        header("Content-Length: 0");
        header("Content-Type: text/plain");
    }

    /**
     * Validar origem
     */
    private function validate_origin($origin)
    {
        // Permitir qualquer origem (apenas desenvolvimento)
        if ($this->config['allow_any_origin']) {
            return TRUE;
        }

        // Validar contra lista de origens permitidas
        foreach ($this->config['allowed_origins'] as $allowed) {
            // Suporte para wildcards
            if ($this->match_origin($origin, $allowed)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Comparar origem (com suporte a wildcards)
     */
    private function match_origin($origin, $pattern)
    {
        // Exato match
        if ($origin === $pattern) {
            return TRUE;
        }

        // Wildcard match
        if (strpos($pattern, '*') !== FALSE) {
            $pattern = str_replace('*', '.*', preg_quote($pattern, '/'));
            return preg_match('/^' . $pattern . '$/', $origin) === 1;
        }

        return FALSE;
    }

    /**
     * Adicionar origem permitida
     */
    public function add_allowed_origin($origin)
    {
        if (!in_array($origin, $this->config['allowed_origins'])) {
            $this->config['allowed_origins'][] = $origin;
        }
    }

    /**
     * Adicionar múltiplas origens
     */
    public function add_allowed_origins($origins)
    {
        foreach ((array)$origins as $origin) {
            $this->add_allowed_origin($origin);
        }
    }

    /**
     * Adicionar método permitido
     */
    public function add_allowed_method($method)
    {
        $method = strtoupper($method);
        if (!in_array($method, $this->config['allowed_methods'])) {
            $this->config['allowed_methods'][] = $method;
        }
    }

    /**
     * Adicionar header permitido
     */
    public function add_allowed_header($header)
    {
        if (!in_array($header, $this->config['allowed_headers'])) {
            $this->config['allowed_headers'][] = $header;
        }
    }

    /**
     * Adicionar header exposto
     */
    public function add_exposed_header($header)
    {
        if (!in_array($header, $this->config['exposed_headers'])) {
            $this->config['exposed_headers'][] = $header;
        }
    }

    /**
     * Obter configuração
     */
    public function get_config()
    {
        return $this->config;
    }

    /**
     * Definir configuração
     */
    public function set_config($key, $value)
    {
        $this->config[$key] = $value;
    }
}
?>
