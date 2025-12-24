<?php
/**
 * API Logging Hook
 * Registra automaticamente todas as requisições de API
 * 
 * Adicionar em application/config/hooks.php:
 * $hook['post_controller'] = array(
 *     'class'    => 'ApiLoggingHook',
 *     'function' => 'log_api_call',
 *     'filename' => 'ApiLoggingHook.php',
 *     'filepath' => 'hooks'
 * );
 */

class ApiLoggingHook
{
    private $CI;
    private $start_time = 0;

    public function __construct()
    {
        $this->CI =& get_instance();
        // Registrar tempo de início
        $this->start_time = microtime(TRUE);
    }

    /**
     * Logar chamada de API (executado ao final do controller)
     */
    public function log_api_call()
    {
        $this->CI->load->library('api_logger', array(
            'enabled'               => TRUE,
            'log_to_database'       => TRUE,
            'log_to_file'          => TRUE,
            'log_path'             => 'application/logs/api/',
            'database_table'       => 'api_logs',
            'capture_request_body' => TRUE,
            'capture_response_body'=> FALSE,
            'exclude_paths'        => array(
                '#^assets#',
                '#^health#',
                '#^ping#',
            ),
            'exclude_methods'      => array('OPTIONS'),
            'log_level'            => 'info',
            'retention_days'       => 30,
        ));

        // Calcular tempo de execução
        $duration = microtime(TRUE) - $this->start_time;

        // Obter informações da requisição
        $method = $this->CI->input->server('REQUEST_METHOD', 'GET');
        $endpoint = $this->CI->uri->uri_string();
        $status_code = http_response_code() ?: 200;
        $user_id = $this->CI->session->userdata('user_id');

        // Dados extras
        $extra_data = array(
            'query_params'  => $_GET,
            'request_type'  => $this->CI->input->is_ajax_request() ? 'AJAX' : 'HTTP',
        );

        // Log da requisição
        $this->CI->api_logger->log_request(
            $method,
            $endpoint,
            $status_code,
            $duration,
            $user_id,
            $extra_data
        );

        // Log de segurança se houver erros
        if ($status_code >= 400) {
            if ($status_code === 401 || $status_code === 403) {
                $this->CI->api_logger->log_security_activity(
                    'PERMISSION_DENIED',
                    $user_id,
                    "Access denied to {$endpoint}",
                    array('status' => $status_code, 'method' => $method)
                );
            }
        }
    }

    /**
     * Hook para pré-sistema (pode ser chamado antes de executar controller)
     */
    public function pre_system()
    {
        // Validar autenticação e logar tentativas
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $endpoint = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Verificar if é login
        if (strpos($endpoint, 'Account/login') !== FALSE && $method === 'POST') {
            $this->CI->load->library('api_logger');
            $this->CI->api_logger->log_security_activity(
                'LOGIN_ATTEMPT',
                NULL,
                'Login attempt from ' . $_SERVER['REMOTE_ADDR'],
                array('endpoint' => $endpoint)
            );
        }
    }
}

?>
