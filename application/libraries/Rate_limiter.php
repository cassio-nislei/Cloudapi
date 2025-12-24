<?php
/**
 * Rate Limiter Library for CodeIgniter
 * Implementação de rate limiting por IP
 * 
 * Uso:
 * $this->load->library('rate_limiter');
 * if (!$this->rate_limiter->check_limit($identifier)) {
 *     http_response_code(429);
 *     exit('Too Many Requests');
 * }
 */

class Rate_limiter
{
    private $CI;
    private $config = array();
    private $default_config = array(
        'enabled'           => TRUE,
        'max_requests'      => 1000,        // Máximo de requisições
        'time_window'       => 3600,        // Janela de tempo em segundos (1 hora)
        'storage'           => 'database',  // 'database' ou 'file'
        'storage_path'      => 'application/logs/rate_limit/',
        'whitelist_ips'     => array(),     // IPs que não são limitados
        'whitelist_paths'   => array(),     // Caminhos que não são limitados
        'identify_by'       => 'ip',        // 'ip' ou 'user_id'
        'headers_enabled'   => TRUE,        // Incluir rate limit headers na resposta
        'log_violations'    => TRUE         // Logar violações
    );

    public function __construct($config = array())
    {
        $this->CI =& get_instance();
        
        // Mesclar configuração padrão com fornecida
        $this->config = array_merge($this->default_config, $config);
        
        // Se usar armazenamento em arquivo, criar diretório
        if ($this->config['storage'] === 'file' && !is_dir($this->config['storage_path'])) {
            @mkdir($this->config['storage_path'], 0755, TRUE);
        }
        
        log_message('debug', 'Rate_limiter Library Initialized');
    }

    /**
     * Verificar se a requisição está dentro do limite
     * 
     * @param string $identifier Identificador (IP, user_id, etc)
     * @return bool TRUE se dentro do limite, FALSE se excedido
     */
    public function check_limit($identifier = NULL)
    {
        if (!$this->config['enabled']) {
            return TRUE;
        }

        // Obter identificador se não fornecido
        if ($identifier === NULL) {
            $identifier = $this->get_identifier();
        }

        // Verificar se está na whitelist
        if ($this->is_whitelisted($identifier)) {
            return TRUE;
        }

        // Obter dados do armazenamento
        $data = $this->get_data($identifier);
        
        // Resetar se passou o time window
        if (time() - $data['first_request'] > $this->config['time_window']) {
            $data = $this->reset_data();
        }

        // Incrementar contador
        $data['request_count']++;
        $data['last_request'] = time();

        // Salvar dados
        $this->save_data($identifier, $data);

        // Verificar limite
        if ($data['request_count'] > $this->config['max_requests']) {
            $this->log_violation($identifier, $data['request_count']);
            return FALSE;
        }

        // Adicionar headers
        if ($this->config['headers_enabled']) {
            $this->add_headers($identifier, $data);
        }

        return TRUE;
    }

    /**
     * Obter número de requisições pendentes
     * 
     * @param string $identifier
     * @return int Número de requisições restantes
     */
    public function get_remaining($identifier = NULL)
    {
        if ($identifier === NULL) {
            $identifier = $this->get_identifier();
        }

        $data = $this->get_data($identifier);
        $remaining = $this->config['max_requests'] - $data['request_count'];
        
        return max(0, $remaining);
    }

    /**
     * Obter tempo até reset (em segundos)
     * 
     * @param string $identifier
     * @return int Segundos até reset
     */
    public function get_reset_time($identifier = NULL)
    {
        if ($identifier === NULL) {
            $identifier = $this->get_identifier();
        }

        $data = $this->get_data($identifier);
        $elapsed = time() - $data['first_request'];
        $remaining = $this->config['time_window'] - $elapsed;
        
        return max(0, $remaining);
    }

    /**
     * Resetar limite para um identificador
     * 
     * @param string $identifier
     * @return void
     */
    public function reset($identifier = NULL)
    {
        if ($identifier === NULL) {
            $identifier = $this->get_identifier();
        }

        if ($this->config['storage'] === 'database') {
            $this->CI->db->delete('rate_limits', array('identifier' => $identifier));
        } else {
            @unlink($this->get_file_path($identifier));
        }
        
        log_message('info', "Rate limit reset for: {$identifier}");
    }

    /**
     * Obter identificador (IP ou user_id)
     * 
     * @return string
     */
    private function get_identifier()
    {
        if ($this->config['identify_by'] === 'user_id') {
            // Se autenticado, usar user_id
            if ($user_id = $this->CI->session->userdata('user_id')) {
                return 'user_' . $user_id;
            }
        }

        // Caso contrário, usar IP
        return $this->get_client_ip();
    }

    /**
     * Obter IP do cliente (trata proxies)
     * 
     * @return string
     */
    private function get_client_ip()
    {
        // Verificar proxies
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Verificar se identificador está na whitelist
     * 
     * @param string $identifier
     * @return bool
     */
    private function is_whitelisted($identifier)
    {
        // Verificar IP whitelist
        if (in_array($identifier, $this->config['whitelist_ips'])) {
            return TRUE;
        }

        // Verificar caminho whitelist
        $current_path = $this->CI->uri->uri_string();
        foreach ($this->config['whitelist_paths'] as $pattern) {
            if (preg_match($pattern, $current_path)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Obter dados do armazenamento
     * 
     * @param string $identifier
     * @return array
     */
    private function get_data($identifier)
    {
        if ($this->config['storage'] === 'database') {
            return $this->get_data_database($identifier);
        } else {
            return $this->get_data_file($identifier);
        }
    }

    /**
     * Obter dados do banco de dados
     * 
     * @param string $identifier
     * @return array
     */
    private function get_data_database($identifier)
    {
        // Garantir que tabela existe
        if (!$this->CI->db->table_exists('rate_limits')) {
            $this->create_rate_limits_table();
        }

        $query = $this->CI->db->get_where('rate_limits', array('identifier' => $identifier), 1);

        if ($query->num_rows() === 0) {
            return $this->reset_data();
        }

        $row = $query->row_array();
        return array(
            'request_count' => (int)$row['request_count'],
            'first_request' => (int)$row['first_request'],
            'last_request'  => (int)$row['last_request']
        );
    }

    /**
     * Obter dados do arquivo
     * 
     * @param string $identifier
     * @return array
     */
    private function get_data_file($identifier)
    {
        $file_path = $this->get_file_path($identifier);

        if (!file_exists($file_path)) {
            return $this->reset_data();
        }

        $data = json_decode(file_get_contents($file_path), TRUE);
        return is_array($data) ? $data : $this->reset_data();
    }

    /**
     * Salvar dados
     * 
     * @param string $identifier
     * @param array $data
     * @return void
     */
    private function save_data($identifier, $data)
    {
        if ($this->config['storage'] === 'database') {
            $this->save_data_database($identifier, $data);
        } else {
            $this->save_data_file($identifier, $data);
        }
    }

    /**
     * Salvar dados no banco de dados
     * 
     * @param string $identifier
     * @param array $data
     * @return void
     */
    private function save_data_database($identifier, $data)
    {
        $exists = $this->CI->db->get_where('rate_limits', array('identifier' => $identifier), 1)->num_rows();

        $save_data = array(
            'identifier'     => $identifier,
            'request_count'  => $data['request_count'],
            'first_request'  => $data['first_request'],
            'last_request'   => $data['last_request'],
            'updated_at'     => date('Y-m-d H:i:s')
        );

        if ($exists > 0) {
            $this->CI->db->where('identifier', $identifier);
            $this->CI->db->update('rate_limits', $save_data);
        } else {
            $save_data['created_at'] = date('Y-m-d H:i:s');
            $this->CI->db->insert('rate_limits', $save_data);
        }
    }

    /**
     * Salvar dados em arquivo
     * 
     * @param string $identifier
     * @param array $data
     * @return void
     */
    private function save_data_file($identifier, $data)
    {
        $file_path = $this->get_file_path($identifier);
        file_put_contents($file_path, json_encode($data));
    }

    /**
     * Obter caminho do arquivo
     * 
     * @param string $identifier
     * @return string
     */
    private function get_file_path($identifier)
    {
        $safe_identifier = md5($identifier);
        return $this->config['storage_path'] . $safe_identifier . '.json';
    }

    /**
     * Resetar dados para novo ciclo
     * 
     * @return array
     */
    private function reset_data()
    {
        return array(
            'request_count' => 0,
            'first_request' => time(),
            'last_request'  => time()
        );
    }

    /**
     * Adicionar headers de rate limit
     * 
     * @param string $identifier
     * @param array $data
     * @return void
     */
    private function add_headers($identifier, $data)
    {
        $remaining = max(0, $this->config['max_requests'] - $data['request_count']);
        $reset_time = $data['first_request'] + $this->config['time_window'];

        header('X-RateLimit-Limit: ' . $this->config['max_requests']);
        header('X-RateLimit-Remaining: ' . $remaining);
        header('X-RateLimit-Reset: ' . $reset_time);
    }

    /**
     * Logar violação de rate limit
     * 
     * @param string $identifier
     * @param int $request_count
     * @return void
     */
    private function log_violation($identifier, $request_count)
    {
        if (!$this->config['log_violations']) {
            return;
        }

        $message = "Rate limit exceeded for {$identifier}: {$request_count} requests";
        log_message('warn', $message);

        // Também logar em database se disponível
        if ($this->config['storage'] === 'database' && $this->CI->db->table_exists('api_logs')) {
            $this->CI->db->insert('api_logs', array(
                'type'      => 'RATE_LIMIT',
                'identifier'=> $identifier,
                'count'     => $request_count,
                'path'      => $this->CI->uri->uri_string(),
                'created_at'=> date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Criar tabela rate_limits
     * 
     * @return void
     */
    private function create_rate_limits_table()
    {
        $query = "CREATE TABLE IF NOT EXISTS `rate_limits` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `identifier` VARCHAR(255) NOT NULL UNIQUE,
            `request_count` INT NOT NULL DEFAULT 0,
            `first_request` INT NOT NULL,
            `last_request` INT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY `idx_identifier` (`identifier`),
            KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->CI->db->query($query);
        log_message('info', 'Created rate_limits table');
    }

    /**
     * Limpar registros antigos (pode ser executado via cron)
     * 
     * @param int $days Remover registros mais antigos que X dias
     * @return int Número de registros removidos
     */
    public function cleanup($days = 7)
    {
        if ($this->config['storage'] === 'database') {
            $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            
            if ($this->CI->db->table_exists('rate_limits')) {
                $this->CI->db->where('updated_at <', $cutoff_date);
                $this->CI->db->delete('rate_limits');
                
                return $this->CI->db->affected_rows();
            }
        } else {
            // Limpar arquivos antigos
            $cutoff_time = time() - ($days * 86400);
            $removed = 0;

            $files = glob($this->config['storage_path'] . '*.json');
            foreach ($files as $file) {
                if (filemtime($file) < $cutoff_time) {
                    unlink($file);
                    $removed++;
                }
            }

            return $removed;
        }

        return 0;
    }

    /**
     * Obter estatísticas de rate limiting
     * 
     * @return array
     */
    public function get_stats()
    {
        if ($this->config['storage'] === 'database') {
            if (!$this->CI->db->table_exists('rate_limits')) {
                return array();
            }

            $total = $this->CI->db->count_all('rate_limits');
            $query = $this->CI->db->query("SELECT AVG(request_count) as avg_requests, 
                                                MAX(request_count) as max_requests 
                                           FROM rate_limits");
            $result = $query->row_array();

            return array(
                'total_tracked'     => $total,
                'average_requests'  => (int)$result['avg_requests'],
                'max_requests'      => (int)$result['max_requests']
            );
        }

        return array();
    }
}
?>
