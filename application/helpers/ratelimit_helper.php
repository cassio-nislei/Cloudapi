<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rate Limiting Helper
 * Controla o número de requisições por IP em um período de tempo
 */

/**
 * Verifica se cliente excedeu limite de requisições
 * @param int $limit Número máximo de requisições permitidas
 * @param int $window Janela de tempo em segundos
 * @return array Status da verificação
 */
function check_rate_limit($limit = 100, $window = 60) {
    $ci =& get_instance();
    $ip = $ci->input->ip_address();
    $key = "rate_limit_" . md5($ip);
    
    // Obter contador atual do cache
    $current = $ci->cache->get($key);
    
    if ($current === FALSE) {
        $current = 0;
    }
    
    $current++;
    
    // Salvar contador atualizado
    $ci->cache->save($key, $current, $window);
    
    // Adicionar headers informativos
    header("X-RateLimit-Limit: $limit");
    header("X-RateLimit-Remaining: " . max(0, $limit - $current));
    header("X-RateLimit-Reset: " . (time() + $window));
    
    // Se excedeu limite
    if ($current > $limit) {
        return array(
            'status' => 'ERRO',
            'msg' => 'Limite de requisições excedido. Tente novamente em ' . $window . ' segundos.',
            'retryAfter' => $window,
            'limit' => $limit,
            'current' => $current
        );
    }
    
    return array(
        'status' => 'OK',
        'remaining' => $limit - $current,
        'limit' => $limit
    );
}

/**
 * Retorna informações de rate limit para o cliente atual
 * @return array Informações de uso
 */
function get_client_rate_limit_info() {
    $ci =& get_instance();
    $ip = $ci->input->ip_address();
    $key = "rate_limit_" . md5($ip);
    
    $current = $ci->cache->get($key);
    
    return array(
        'ip' => $ip,
        'requisicoes_usadas' => $current ? $current : 0,
        'limite_maximo' => 100,
        'percentual_uso' => $current ? round(($current / 100) * 100, 2) : 0
    );
}

/**
 * Limpa contador de rate limit para um IP específico
 * @param string $ip IP a limpar (opcional, usa IP atual se não fornecido)
 * @return bool Resultado da operação
 */
function clear_rate_limit($ip = null) {
    $ci =& get_instance();
    
    if ($ip === null) {
        $ip = $ci->input->ip_address();
    }
    
    $key = "rate_limit_" . md5($ip);
    return $ci->cache->delete($key);
}

/**
 * Retorna estatísticas de rate limit
 * @return array Estatísticas
 */
function get_rate_limit_stats() {
    $ci =& get_instance();
    
    // Retorna informações úteis
    return array(
        'metodo' => 'cache-based',
        'backend' => $ci->cache->get_config('adapter'),
        'tempo_verificacao' => 'real-time'
    );
}
