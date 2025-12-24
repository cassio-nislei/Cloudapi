<?php
/**
 * API Logging Library for CodeIgniter
 * Implementação de logging detalhado para auditoria de APIs
 * 
 * Uso:
 * $this->load->library('api_logger');
 * $this->api_logger->log_request($method, $endpoint, $status, $response_time);
 */

class Api_logger
{
    private $CI;
    private $config = array();
    private $default_config = array(
        'enabled'               => TRUE,
        'log_to_database'       => TRUE,
        'log_to_file'          => TRUE,
        'log_path'             => 'application/logs/api/',
        'database_table'       => 'api_logs',
        'capture_request_body' => TRUE,
        'capture_response_body'=> FALSE, // Cuidado: Pode ter dados sensíveis
        'exclude_paths'        => array(),
        'exclude_methods'      => array('OPTIONS'),
        'log_level'            => 'info', // 'debug', 'info', 'warning', 'error'
        'retention_days'       => 30,
    );

    public function __construct($config = array())
    {
        $this->CI =& get_instance();
        
        // Mesclar configuração
        $this->config = array_merge($this->default_config, $config);
        
        // Criar diretório de logs
        if ($this->config['log_to_file'] && !is_dir($this->config['log_path'])) {
            @mkdir($this->config['log_path'], 0755, TRUE);
        }
        
        log_message('debug', 'Api_logger Library Initialized');
    }

    /**
     * Registrar requisição de API
     * 
     * @param string $method    HTTP method (GET, POST, etc)
     * @param string $endpoint  Endpoint da requisição
     * @param int $status_code  HTTP status code da resposta
     * @param float $duration   Tempo de execução em segundos
     * @param mixed $user_id    ID do usuário ou NULL
     * @param array $extra_data Dados adicionais para logar
     * @return int ID do log no banco de dados (se database log ativo)
     */
    public function log_request($method, $endpoint, $status_code, $duration = 0, $user_id = NULL, $extra_data = array())
    {
        if (!$this->config['enabled']) {
            return NULL;
        }

        // Verificar se está em exclude_paths
        if ($this->is_excluded_path($endpoint)) {
            return NULL;
        }

        // Preparar dados do log
        $log_data = array(
            'timestamp'     => date('Y-m-d H:i:s'),
            'method'        => strtoupper($method),
            'endpoint'      => $endpoint,
            'status_code'   => $status_code,
            'duration_ms'   => round($duration * 1000, 2),
            'ip_address'    => $this->get_client_ip(),
            'user_agent'    => substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255),
            'user_id'       => $user_id,
            'request_id'    => $this->get_request_id(),
            'referer'       => $_SERVER['HTTP_REFERER'] ?? NULL,
            'extra_data'    => $extra_data,
        );

        // Log de requisição detalhado
        if ($this->config['log_to_database']) {
            $log_id = $this->log_to_database($log_data);
        }

        // Log em arquivo
        if ($this->config['log_to_file']) {
            $this->log_to_file($log_data);
        }

        // Log CodeIgniter padrão
        if ($this->should_log($status_code)) {
            $level = $this->get_log_level($status_code);
            log_message($level, $this->format_log_message($log_data));
        }

        return isset($log_id) ? $log_id : NULL;
    }

    /**
     * Registrar erro de API
     * 
     * @param string $error_message Mensagem de erro
     * @param string $endpoint      Endpoint onde erro ocorreu
     * @param int $error_code       Código de erro
     * @param mixed $user_id        ID do usuário
     * @param array $context        Contexto addicional
     */
    public function log_error($error_message, $endpoint, $error_code = 500, $user_id = NULL, $context = array())
    {
        $log_data = array(
            'timestamp'     => date('Y-m-d H:i:s'),
            'type'          => 'ERROR',
            'message'       => $error_message,
            'endpoint'      => $endpoint,
            'error_code'    => $error_code,
            'ip_address'    => $this->get_client_ip(),
            'user_id'       => $user_id,
            'request_id'    => $this->get_request_id(),
            'stack_trace'   => $this->get_stack_trace(),
            'context'       => $context,
        );

        if ($this->config['log_to_database']) {
            $this->log_to_database($log_data);
        }

        if ($this->config['log_to_file']) {
            $this->log_to_file($log_data);
        }

        log_message('error', $error_message);
    }

    /**
     * Registrar atividade de segurança
     * 
     * @param string $activity      Tipo de atividade (LOGIN, LOGOUT, PERMISSION_DENIED, etc)
     * @param mixed $user_id        ID do usuário
     * @param string $description   Descrição da atividade
     * @param array $details        Detalhes adicionais
     */
    public function log_security_activity($activity, $user_id, $description = '', $details = array())
    {
        $log_data = array(
            'timestamp'     => date('Y-m-d H:i:s'),
            'type'          => 'SECURITY',
            'activity'      => $activity,
            'user_id'       => $user_id,
            'description'   => $description,
            'ip_address'    => $this->get_client_ip(),
            'user_agent'    => substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255),
            'details'       => $details,
        );

        if ($this->config['log_to_database']) {
            $this->log_to_database($log_data);
        }

        if ($this->config['log_to_file']) {
            $this->log_to_file($log_data);
        }

        log_message('warning', "Security Activity: {$activity} - User: {$user_id}");
    }

    /**
     * Registrar mudanças de dados (auditoria)
     * 
     * @param string $table         Tabela modificada
     * @param string $operation     CREATE, UPDATE, DELETE
     * @param int $record_id        ID do registro modificado
     * @param array $old_values     Valores antigos
     * @param array $new_values     Valores novos
     * @param mixed $user_id        ID do usuário
     */
    public function log_data_change($table, $operation, $record_id, $old_values, $new_values, $user_id = NULL)
    {
        $log_data = array(
            'timestamp'     => date('Y-m-d H:i:s'),
            'type'          => 'AUDIT',
            'table'         => $table,
            'operation'     => strtoupper($operation),
            'record_id'     => $record_id,
            'old_values'    => $old_values,
            'new_values'    => $new_values,
            'user_id'       => $user_id,
            'ip_address'    => $this->get_client_ip(),
        );

        if ($this->config['log_to_database']) {
            $this->log_to_database($log_data);
        }

        if ($this->config['log_to_file']) {
            $this->log_to_file($log_data);
        }
    }

    /**
     * Obter logs recentes
     * 
     * @param int $limit Número de registros
     * @param int $offset Offset
     * @return array
     */
    public function get_recent_logs($limit = 50, $offset = 0)
    {
        if (!$this->config['log_to_database']) {
            return array();
        }

        if (!$this->CI->db->table_exists($this->config['database_table'])) {
            return array();
        }

        return $this->CI->db
            ->select('*')
            ->from($this->config['database_table'])
            ->order_by('id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->result_array();
    }

    /**
     * Obter logs filtrados
     * 
     * @param array $filters Array com filtros (method, endpoint, user_id, status_code, etc)
     * @param int $limit
     * @return array
     */
    public function get_logs_by_filter($filters = array(), $limit = 50)
    {
        if (!$this->config['log_to_database']) {
            return array();
        }

        if (!$this->CI->db->table_exists($this->config['database_table'])) {
            return array();
        }

        $query = $this->CI->db;

        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $query->where_in($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query
            ->order_by('id', 'DESC')
            ->limit($limit)
            ->get($this->config['database_table'])
            ->result_array();
    }

    /**
     * Obter estatísticas de logs
     * 
     * @param string $endpoint Filtrar por endpoint (opcional)
     * @return array
     */
    public function get_stats($endpoint = NULL)
    {
        if (!$this->config['log_to_database']) {
            return array();
        }

        if (!$this->CI->db->table_exists($this->config['database_table'])) {
            return array();
        }

        $query = $this->CI->db;

        if ($endpoint) {
            $query->where('endpoint', $endpoint);
        }

        // Total de requisições
        $total = $query->count_all_results($this->config['database_table']);

        // Estatísticas por método
        $by_method = $this->CI->db->query("
            SELECT method, COUNT(*) as count 
            FROM {$this->config['database_table']}
            " . ($endpoint ? "WHERE endpoint = '{$endpoint}'" : "") . "
            GROUP BY method
        ")->result_array();

        // Estatísticas por código de status
        $by_status = $this->CI->db->query("
            SELECT status_code, COUNT(*) as count 
            FROM {$this->config['database_table']}
            " . ($endpoint ? "WHERE endpoint = '{$endpoint}'" : "") . "
            GROUP BY status_code
        ")->result_array();

        // Tempo médio de resposta
        $avg_duration = $this->CI->db->query("
            SELECT AVG(duration_ms) as avg_duration 
            FROM {$this->config['database_table']}
            " . ($endpoint ? "WHERE endpoint = '{$endpoint}'" : "")
        )->row_array();

        return array(
            'total_requests'    => $total,
            'by_method'         => $by_method,
            'by_status'         => $by_status,
            'avg_response_time' => round($avg_duration['avg_duration'] ?? 0, 2),
        );
    }

    /**
     * Limpar logs antigos
     * 
     * @param int $days Remover logs mais antigos que X dias
     * @return int Número de registros removidos
     */
    public function cleanup($days = NULL)
    {
        if ($days === NULL) {
            $days = $this->config['retention_days'];
        }

        if ($this->config['log_to_database']) {
            if ($this->CI->db->table_exists($this->config['database_table'])) {
                $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
                
                $this->CI->db->where('timestamp <', $cutoff_date);
                $this->CI->db->delete($this->config['database_table']);
                
                $affected = $this->CI->db->affected_rows();
                log_message('info', "Cleaned up {$affected} old API logs from database");
            }
        }

        if ($this->config['log_to_file']) {
            $cutoff_time = time() - ($days * 86400);
            $removed = 0;

            $files = glob($this->config['log_path'] . '*.log');
            foreach ($files as $file) {
                if (filemtime($file) < $cutoff_time) {
                    unlink($file);
                    $removed++;
                }
            }

            if ($removed > 0) {
                log_message('info', "Cleaned up {$removed} old API log files");
            }
        }
    }

    // ========== MÉTODOS PRIVADOS ==========

    /**
     * Logar em banco de dados
     */
    private function log_to_database($log_data)
    {
        if (!$this->CI->db->table_exists($this->config['database_table'])) {
            $this->create_logs_table();
        }

        $data = array(
            'timestamp'     => $log_data['timestamp'],
            'type'          => $log_data['type'] ?? 'REQUEST',
            'method'        => $log_data['method'] ?? NULL,
            'endpoint'      => $log_data['endpoint'] ?? NULL,
            'status_code'   => $log_data['status_code'] ?? NULL,
            'duration_ms'   => $log_data['duration_ms'] ?? NULL,
            'ip_address'    => $log_data['ip_address'],
            'user_agent'    => $log_data['user_agent'] ?? NULL,
            'user_id'       => $log_data['user_id'],
            'request_id'    => $log_data['request_id'] ?? NULL,
            'message'       => $log_data['message'] ?? NULL,
            'data'          => json_encode($log_data),
        );

        $this->CI->db->insert($this->config['database_table'], $data);
        return $this->CI->db->insert_id();
    }

    /**
     * Logar em arquivo
     */
    private function log_to_file($log_data)
    {
        $log_file = $this->config['log_path'] . date('Y-m-d') . '.log';
        $message = $this->format_log_message($log_data);
        
        file_put_contents($log_file, $message . "\n", FILE_APPEND);
    }

    /**
     * Formatar mensagem de log
     */
    private function format_log_message($log_data)
    {
        $timestamp = $log_data['timestamp'];
        $type = $log_data['type'] ?? 'API';
        
        if (isset($log_data['method']) && isset($log_data['endpoint'])) {
            // Formato para requisição
            $method = $log_data['method'];
            $endpoint = $log_data['endpoint'];
            $status = $log_data['status_code'];
            $duration = $log_data['duration_ms'] ?? 0;
            $ip = $log_data['ip_address'];
            $user = $log_data['user_id'] ? "[User: {$log_data['user_id']}]" : '';
            
            return "[{$timestamp}] {$method} {$endpoint} -> {$status} ({$duration}ms) {$ip} {$user}";
        } else {
            // Formato para segurança/erro
            return "[{$timestamp}] [{$type}] " . json_encode($log_data);
        }
    }

    /**
     * Verificar se deve logar
     */
    private function should_log($status_code)
    {
        switch ($this->config['log_level']) {
            case 'debug':
                return TRUE;
            case 'info':
                return $status_code >= 200 && $status_code < 500;
            case 'warning':
                return $status_code >= 400;
            case 'error':
                return $status_code >= 500;
            default:
                return TRUE;
        }
    }

    /**
     * Obter nível de log apropriado
     */
    private function get_log_level($status_code)
    {
        if ($status_code >= 500) {
            return 'error';
        } elseif ($status_code >= 400) {
            return 'warning';
        } else {
            return 'info';
        }
    }

    /**
     * Verificar se está em excluded paths
     */
    private function is_excluded_path($endpoint)
    {
        foreach ($this->config['exclude_paths'] as $pattern) {
            if (preg_match($pattern, $endpoint)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Obter IP do cliente
     */
    private function get_client_ip()
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Obter ID único para requisição
     */
    private function get_request_id()
    {
        // Verificar se já existe X-Request-ID
        if (!empty($_SERVER['HTTP_X_REQUEST_ID'])) {
            return $_SERVER['HTTP_X_REQUEST_ID'];
        }

        // Gerar novo ID baseado em microtime
        return uniqid(base_convert(time(), 10, 36) . '-', TRUE);
    }

    /**
     * Obter stack trace (para erros)
     */
    private function get_stack_trace()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $formatted = array();

        foreach ($trace as $item) {
            $formatted[] = array(
                'file'     => $item['file'] ?? 'unknown',
                'line'     => $item['line'] ?? 0,
                'function' => $item['function'] ?? 'unknown',
                'class'    => $item['class'] ?? NULL,
            );
        }

        return json_encode($formatted);
    }

    /**
     * Criar tabela de logs
     */
    private function create_logs_table()
    {
        $query = "CREATE TABLE IF NOT EXISTS `{$this->config['database_table']}` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `timestamp` DATETIME NOT NULL,
            `type` VARCHAR(50) DEFAULT 'REQUEST',
            `method` VARCHAR(10),
            `endpoint` VARCHAR(255),
            `status_code` INT,
            `duration_ms` DECIMAL(10,2),
            `ip_address` VARCHAR(45),
            `user_agent` VARCHAR(255),
            `user_id` INT,
            `request_id` VARCHAR(100),
            `message` TEXT,
            `data` LONGTEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            KEY `idx_timestamp` (`timestamp`),
            KEY `idx_endpoint` (`endpoint`),
            KEY `idx_user_id` (`user_id`),
            KEY `idx_type` (`type`),
            KEY `idx_request_id` (`request_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->CI->db->query($query);
        log_message('info', 'Created api_logs table');
    }
}
?>
