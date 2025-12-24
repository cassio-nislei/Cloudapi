<?php
/**
 * Rate Limiting Hook
 * Aplica rate limiting em todos os requests
 * 
 * Ativar em: application/config/hooks.php
 */

class RateLimitHook
{
    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Executar rate limiting (deve ser chamado no hook pre_system)
     */
    public function execute()
    {
        // Carregar library
        $this->CI->load->library('rate_limiter', array(
            'enabled'          => TRUE,
            'max_requests'     => 1000,        // 1000 requisições
            'time_window'      => 3600,        // por hora
            'storage'          => 'database',  // usar banco de dados
            'identify_by'      => 'ip',        // por IP
            'headers_enabled'  => TRUE,        // incluir headers
            'log_violations'   => TRUE,        // logar violações
            'whitelist_ips'    => array(
                '127.0.0.1',
                '::1',
                // Adicionar IPs internos aqui
            ),
            'whitelist_paths'  => array(
                '/^(health|status|ping)$/',    // Health check endpoints
                '/^api\/v1\/passport$/',       // Passport não limita
                '/^Account\/login$/'           // Login não limita
            )
        ));

        // Verificar rate limit
        if (!$this->CI->rate_limiter->check_limit()) {
            // Excedido limite
            $remaining = $this->CI->rate_limiter->get_remaining();
            $reset_time = $this->CI->rate_limiter->get_reset_time();

            if ($this->CI->input->is_ajax_request()) {
                header('HTTP/1.1 429 Too Many Requests');
                header('Content-Type: application/json');
                header('Retry-After: ' . $reset_time);
                echo json_encode(array(
                    'status' => FALSE,
                    'msg' => 'Too many requests. Try again in ' . $reset_time . ' seconds.',
                    'reset_time' => $reset_time
                ));
            } else {
                header('HTTP/1.1 429 Too Many Requests');
                header('Retry-After: ' . $reset_time);
                echo "429 Too Many Requests\n";
                echo "Retry after: $reset_time seconds";
            }

            exit;
        }
    }

    /**
     * Hook chamado após resposta (pós-controlador)
     */
    public function post_response()
    {
        // Pode ser usado para logging ou limpeza
        $remaining = $this->CI->rate_limiter->get_remaining();
        
        if ($remaining < 100) {
            log_message('info', 'Rate limit approaching for ' . 
                        $_SERVER['REMOTE_ADDR'] . ': ' . $remaining . ' remaining');
        }
    }
}

// Função hook para ser chamada automaticamente
function apply_rate_limiting()
{
    $hook = new RateLimitHook();
    $hook->execute();
}

?>
