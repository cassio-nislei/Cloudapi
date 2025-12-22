<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API Logging Helper
 * Registra acessos, erros e mantém histórico de requisições
 */

/**
 * Registra acesso a um endpoint da API
 * @param string $endpoint Endpoint acessado
 * @param string $method Método HTTP (GET, POST, etc)
 * @param int $status_code Código HTTP da resposta
 * @param float $response_time Tempo de resposta em segundos
 * @param array $data Dados adicionais
 * @return bool Resultado da operação
 */
function log_api_access($endpoint, $method, $status_code, $response_time = 0, $data = array()) {
    $ci =& get_instance();
    
    try {
        $log_data = array(
            'data_acesso' => date('Y-m-d H:i:s'),
            'ip_cliente' => $ci->input->ip_address(),
            'user_agent' => substr($ci->input->user_agent(), 0, 255),
            'endpoint' => $endpoint,
            'metodo' => $method,
            'parametros' => json_encode($ci->input->get()),
            'body_request' => isset($data['body']) ? substr(json_encode($data['body']), 0, 500) : '',
            'status_code' => $status_code,
            'tempo_resposta' => round($response_time, 4),
            'usuario' => $ci->session->userdata('username') ? $ci->session->userdata('username') : 'anonimo',
            'cgc_cliente' => isset($data['cgc']) ? $data['cgc'] : null,
        );
        
        // Salvar no banco de dados se a tabela existir
        if ($ci->db->table_exists('api_acessos')) {
            $ci->db->insert('api_acessos', $log_data);
        }
        
        // Registrar em arquivo também
        log_message('info', json_encode($log_data));
        
        return true;
        
    } catch (Exception $ex) {
        log_message('error', 'Erro ao registrar acesso à API: ' . $ex->getMessage());
        return false;
    }
}

/**
 * Registra erro na API
 * @param string $endpoint Endpoint onde ocorreu o erro
 * @param string $method Método HTTP
 * @param string $error_message Mensagem de erro
 * @param string $stack_trace Stack trace do erro
 * @param array $data Dados adicionais
 * @return bool Resultado da operação
 */
function log_api_error($endpoint, $method, $error_message, $stack_trace = '', $data = array()) {
    $ci =& get_instance();
    
    try {
        $error_data = array(
            'data_erro' => date('Y-m-d H:i:s'),
            'ip_cliente' => $ci->input->ip_address(),
            'endpoint' => $endpoint,
            'metodo' => $method,
            'mensagem_erro' => substr($error_message, 0, 500),
            'stack_trace' => substr($stack_trace, 0, 2000),
            'usuario' => $ci->session->userdata('username') ? $ci->session->userdata('username') : 'anonimo',
            'cgc_cliente' => isset($data['cgc']) ? $data['cgc'] : null,
        );
        
        // Salvar em tabela de erros se existir
        if ($ci->db->table_exists('api_erros')) {
            $ci->db->insert('api_erros', $error_data);
        }
        
        // Registrar em arquivo
        log_message('error', json_encode($error_data));
        
        return true;
        
    } catch (Exception $ex) {
        log_message('error', 'Erro ao registrar erro na API: ' . $ex->getMessage());
        return false;
    }
}

/**
 * Retorna estatísticas de acessos à API
 * @param string $data_inicio Data inicial (YYYY-MM-DD)
 * @param string $data_fim Data final (YYYY-MM-DD)
 * @param string $endpoint Endpoint específico (opcional)
 * @return array Estatísticas
 */
function get_api_stats($data_inicio = null, $data_fim = null, $endpoint = null) {
    $ci =& get_instance();
    
    if ($data_inicio === null) {
        $data_inicio = date('Y-m-d', strtotime('-30 days'));
    }
    if ($data_fim === null) {
        $data_fim = date('Y-m-d');
    }
    
    if (!$ci->db->table_exists('api_acessos')) {
        return array();
    }
    
    $query = $ci->db->select('
        COUNT(*) as total_requisicoes,
        COUNT(DISTINCT ip_cliente) as clientes_unicos,
        AVG(tempo_resposta) as tempo_medio,
        MAX(tempo_resposta) as tempo_maximo,
        MIN(tempo_resposta) as tempo_minimo,
        endpoint,
        metodo
    ')
    ->from('api_acessos')
    ->where('data_acesso >=', $data_inicio . ' 00:00:00')
    ->where('data_acesso <=', $data_fim . ' 23:59:59');
    
    if ($endpoint !== null) {
        $query->where('endpoint', $endpoint);
    }
    
    $query->group_by('endpoint, metodo');
    $result = $query->get();
    
    return $result->result_array();
}

/**
 * Retorna total de requisições por hora (últimas 24 horas)
 * @return array Dados de requisições por hora
 */
function get_api_requests_by_hour() {
    $ci =& get_instance();
    
    if (!$ci->db->table_exists('api_acessos')) {
        return array();
    }
    
    $query = $ci->db->select("
        DATE_FORMAT(data_acesso, '%Y-%m-%d %H:00:00') as hora,
        COUNT(*) as total_requisicoes,
        SUM(CASE WHEN status_code = 200 THEN 1 ELSE 0 END) as sucesso,
        SUM(CASE WHEN status_code != 200 THEN 1 ELSE 0 END) as erro
    ")
    ->from('api_acessos')
    ->where('data_acesso >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
    ->group_by("DATE_FORMAT(data_acesso, '%Y-%m-%d %H:00:00')")
    ->order_by('hora', 'ASC')
    ->get();
    
    return $query->result_array();
}

/**
 * Retorna IPs com mais requisições
 * @param int $limit Número de IPs a retornar
 * @param int $horas Últimas N horas
 * @return array Lista de IPs e acessos
 */
function get_top_ips($limit = 10, $horas = 24) {
    $ci =& get_instance();
    
    if (!$ci->db->table_exists('api_acessos')) {
        return array();
    }
    
    $query = $ci->db->select('
        ip_cliente,
        COUNT(*) as total_requisicoes,
        COUNT(DISTINCT endpoint) as endpoints_acessados,
        AVG(tempo_resposta) as tempo_medio
    ')
    ->from('api_acessos')
    ->where('data_acesso >=', date('Y-m-d H:i:s', strtotime("-$horas hours")))
    ->group_by('ip_cliente')
    ->order_by('total_requisicoes', 'DESC')
    ->limit($limit)
    ->get();
    
    return $query->result_array();
}

/**
 * Retorna estatísticas de erros
 * @param int $dias Últimos N dias
 * @return array Estatísticas de erros
 */
function get_api_error_stats($dias = 7) {
    $ci =& get_instance();
    
    if (!$ci->db->table_exists('api_erros')) {
        return array();
    }
    
    $query = $ci->db->select('
        endpoint,
        metodo,
        COUNT(*) as total_erros,
        COUNT(DISTINCT ip_cliente) as clientes_afetados
    ')
    ->from('api_erros')
    ->where('data_erro >=', date('Y-m-d', strtotime("-$dias days")))
    ->group_by('endpoint, metodo')
    ->order_by('total_erros', 'DESC')
    ->get();
    
    return $query->result_array();
}

/**
 * Limpa logs antigos (mantém apenas últimos N dias)
 * @param int $dias Dias a manter
 * @return bool Resultado da operação
 */
function cleanup_old_logs($dias = 90) {
    $ci =& get_instance();
    
    try {
        $data_limite = date('Y-m-d H:i:s', strtotime("-$dias days"));
        
        if ($ci->db->table_exists('api_acessos')) {
            $ci->db->delete('api_acessos', "data_acesso < '$data_limite'");
        }
        
        if ($ci->db->table_exists('api_erros')) {
            $ci->db->delete('api_erros', "data_erro < '$data_limite'");
        }
        
        log_message('info', "Limpeza de logs antigos executada. Mantendo últimos $dias dias.");
        return true;
        
    } catch (Exception $ex) {
        log_message('error', 'Erro ao limpar logs antigos: ' . $ex->getMessage());
        return false;
    }
}
