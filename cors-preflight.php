<?php
/**
 * CORS Preflight Handler
 * 
 * Este arquivo deve ser processado ANTES de qualquer código CodeIgniter
 * Responde a requisições OPTIONS (preflight) sem ativar autenticação
 * 
 * Configuração no .htaccess:
 * RewriteRule ^(.*)$ cors-preflight.php?path=$1 [L]
 * 
 * OU colocar este código no index.php ANTES de qualquer redirect
 */

// Não carregar CodeIgniter para preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    handle_cors_preflight();
}

function handle_cors_preflight()
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

    // Validar origem
    if (in_array($origin, $allowed_origins)) {
        // Responder ao preflight
        http_response_code(200);
        header("Access-Control-Allow-Origin: {$origin}");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH, HEAD");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Request-ID, Accept, Origin, Cache-Control, X-CSRF-Token");
        header("Access-Control-Expose-Headers: Content-Length, X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset, X-Request-ID");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 3600");
        header("Content-Length: 0");
        exit(0);
    }

    // Origem não permitida
    http_response_code(403);
    exit('CORS Origin not allowed');
}

?>
