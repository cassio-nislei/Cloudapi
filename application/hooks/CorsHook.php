<?php
/**
 * CORS Hook
 * Aplica CORS automaticamente em todas as requisições (após preflight)
 * 
 * IMPORTANTE: O preflight é tratado em index.php ANTES do CodeIgniter
 * Este hook apenas adiciona headers nas requisições normais
 * 
 * Ativar em application/config/hooks.php:
 * $hook['pre_system'] = array(
 *     'class'    => 'CorsHook',
 *     'function' => 'execute',
 *     'filename' => 'CorsHook.php',
 *     'filepath' => 'hooks'
 * );
 */

class CorsHook
{
    public function execute()
    {
        // Lista de origens permitidas
        $allowed_origins = array(
            'https://admcloud.papion.com.br',
            'http://104.234.173.105:7010',
            'http://localhost:8080',
            'http://127.0.0.1:8080',
            'http://127.0.0.1:8888',
        );

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        // Se tem origin header e está na lista de permitidas
        if (!empty($origin) && in_array($origin, $allowed_origins)) {
            // Adicionar headers CORS
            header("Access-Control-Allow-Origin: {$origin}");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Expose-Headers: Content-Length, X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset, X-Request-ID");
            header("Access-Control-Max-Age: 3600");
        }
    }
}
?>
