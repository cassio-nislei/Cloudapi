# API Logging - Guia de Implementação

## Visão Geral

Sistema completo de logging para auditoria e monitoramento da API ADMCloud. Registra:

- ✅ Todas as requisições HTTP (método, endpoint, status, tempo)
- ✅ Atividades de segurança (logins, acessos negados)
- ✅ Mudanças de dados (CREATE, UPDATE, DELETE)
- ✅ Erros e exceções
- ✅ Rastreamento de performance

## Instalação

### 1. Copiar Arquivos

```bash
# Library de logging
cp application/libraries/Api_logger.php <projeto>/application/libraries/

# Hook automático
cp application/hooks/ApiLoggingHook.php <projeto>/application/hooks/
```

### 2. Criar Tabela no Banco de Dados

```sql
CREATE TABLE `api_logs` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

A tabela será criada automaticamente na primeira requisição se não existir.

### 3. Ativar Hook (Opcional)

Para logging automático de todas as requisições, adicione em `application/config/hooks.php`:

```php
$hook['post_controller'] = array(
    'class'    => 'ApiLoggingHook',
    'function' => 'log_api_call',
    'filename' => 'ApiLoggingHook.php',
    'filepath' => 'hooks'
);
```

## Uso Programático

### Logging Simples de Requisição

```php
$this->load->library('api_logger');

// Log automático de requisição
$duration = microtime(TRUE) - $start_time;
$this->api_logger->log_request(
    'GET',                           // Método HTTP
    'api/v1/usuarios',              // Endpoint
    200,                            // Status code
    $duration,                      // Tempo em segundos
    $this->session->userdata('user_id'), // User ID
    array('count' => 42)            // Dados extras
);
```

### Logging de Erro

```php
try {
    // Alguma operação
} catch (Exception $e) {
    $this->api_logger->log_error(
        $e->getMessage(),            // Mensagem de erro
        'api/v1/usuarios',          // Endpoint
        500,                        // Código de erro
        $this->session->userdata('user_id')
    );
}
```

### Logging de Atividade de Segurança

```php
// Login bem-sucedido
$this->api_logger->log_security_activity(
    'LOGIN_SUCCESS',                // Tipo de atividade
    $user_id,                       // ID do usuário
    'User logged in successfully',  // Descrição
    array('ip' => $this->input->ip_address()) // Detalhes
);

// Tentativa de acesso não autorizado
$this->api_logger->log_security_activity(
    'PERMISSION_DENIED',
    $user_id,
    'Tentativa de acessar recurso não autorizado',
    array(
        'endpoint' => 'api/v1/usuarios/5',
        'reason' => 'User is not owner'
    )
);
```

### Logging de Mudanças de Dados (Auditoria)

```php
// Registrar antes e depois de UPDATE
$old_data = $usuario; // Dados antes da modificação
$new_data = $this->input->post(); // Novos dados

$this->api_logger->log_data_change(
    'ADM_USUARIOS',                 // Tabela
    'UPDATE',                       // Operação
    $usuario_id,                    // ID do registro
    array('nome' => $old_data['nome'], 'email' => $old_data['email']),
    array('nome' => $new_data['nome'], 'email' => $new_data['email']),
    $this->session->userdata('user_id')
);
```

## Consultas de Logs

### Obter Logs Recentes

```php
$this->load->library('api_logger');

// Últimos 50 logs
$logs = $this->api_logger->get_recent_logs(50);

foreach ($logs as $log) {
    echo "{$log['timestamp']} {$log['method']} {$log['endpoint']} -> {$log['status_code']}\n";
}
```

### Filtrar Logs

```php
$filters = array(
    'method'     => 'POST',
    'status_code'=> 200,
    'user_id'    => 5,
    'endpoint'   => 'api/v1/usuarios'
);

$logs = $this->api_logger->get_logs_by_filter($filters, 100);
```

### Obter Estatísticas

```php
// Estatísticas globais
$stats = $this->api_logger->get_stats();
echo "Total de requisições: " . $stats['total_requests'];
echo "Tempo médio de resposta: " . $stats['avg_response_time'] . "ms";

// Estatísticas por endpoint
$stats = $this->api_logger->get_stats('api/v1/usuarios');
```

## Limpeza Automática

```php
// Limpar logs mais antigos que 30 dias
$this->api_logger->cleanup(30);

// Usar período padrão da configuração
$this->api_logger->cleanup();
```

Adicione em cron job para limpeza automática:

```bash
# /etc/cron.d/admcloud-logs
0 3 * * * www-data curl -s http://localhost/cron/cleanup_logs > /dev/null 2>&1
```

## Queries SQL Úteis

### Encontrar Requisições Lentas

```sql
SELECT timestamp, method, endpoint, duration_ms, user_id
FROM api_logs
WHERE duration_ms > 1000  -- Mais de 1 segundo
ORDER BY duration_ms DESC
LIMIT 20;
```

### Atividades de Usuário

```sql
SELECT * FROM api_logs
WHERE user_id = 5
ORDER BY timestamp DESC
LIMIT 50;
```

### Erros por Endpoint

```sql
SELECT endpoint, COUNT(*) as errors,
       GROUP_CONCAT(DISTINCT status_code) as codes
FROM api_logs
WHERE status_code >= 400
GROUP BY endpoint
ORDER BY errors DESC;
```

### Taxa de Erro Hourly

```sql
SELECT DATE_FORMAT(timestamp, '%Y-%m-%d %H:00') as hour,
       COUNT(*) as total,
       SUM(CASE WHEN status_code >= 400 THEN 1 ELSE 0 END) as errors,
       ROUND(100.0 * SUM(CASE WHEN status_code >= 400 THEN 1 ELSE 0 END) / COUNT(*), 2) as error_rate
FROM api_logs
GROUP BY DATE_FORMAT(timestamp, '%Y-%m-%d %H:00')
ORDER BY hour DESC
LIMIT 24;
```

### Endpoints Mais Usados

```sql
SELECT endpoint, COUNT(*) as count,
       ROUND(AVG(duration_ms), 2) as avg_duration,
       MAX(duration_ms) as max_duration
FROM api_logs
WHERE type = 'REQUEST'
GROUP BY endpoint
ORDER BY count DESC
LIMIT 20;
```

## Relatórios em Dashboard

### Exemplo de Dashboard em Controller

```php
public function dashboard()
{
    $this->load->library('api_logger');

    $data['total_requests'] = $this->db->count_all('api_logs');

    $data['requests_24h'] = $this->db
        ->where('timestamp >', date('Y-m-d H:i:s', strtotime('-24 hours')))
        ->count_all_results('api_logs');

    $data['errors_24h'] = $this->db
        ->where('timestamp >', date('Y-m-d H:i:s', strtotime('-24 hours')))
        ->where('status_code >=', 400)
        ->count_all_results('api_logs');

    $data['stats'] = $this->api_logger->get_stats();
    $data['recent_logs'] = $this->api_logger->get_recent_logs(10);

    $this->load->view('admin/dashboard', $data);
}
```

## Exportação de Logs

### Exportar para CSV

```php
public function export_logs()
{
    $this->load->library('api_logger');
    $logs = $this->api_logger->get_logs_by_filter(array(), 5000);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="api_logs_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');

    // Header
    fputcsv($output, array('Timestamp', 'Type', 'Method', 'Endpoint', 'Status', 'Duration', 'User ID', 'IP'));

    // Dados
    foreach ($logs as $log) {
        fputcsv($output, array(
            $log['timestamp'],
            $log['type'],
            $log['method'],
            $log['endpoint'],
            $log['status_code'],
            $log['duration_ms'],
            $log['user_id'],
            $log['ip_address']
        ));
    }

    fclose($output);
    exit;
}
```

### Exportar para JSON

```php
public function export_logs_json()
{
    $this->load->library('api_logger');
    $logs = $this->api_logger->get_logs_by_filter(array(), 5000);

    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="api_logs_' . date('Y-m-d') . '.json"');

    echo json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}
```

## Monitoramento em Tempo Real

### Tail de Logs em Arquivo

```bash
# Ver últimos 100 linhas e seguir
tail -f -n 100 application/logs/api/$(date +%Y-%m-%d).log

# Filtrar apenas erros
tail -f application/logs/api/$(date +%Y-%m-%d).log | grep '\[ERROR\]'

# Filtrar apenas endpoint específico
tail -f application/logs/api/$(date +%Y-%m-%d).log | grep 'api/v1/usuarios'
```

## Segurança e Privacidade

### Dados Sensíveis

⚠️ **AVISO**: Não registrar dados sensíveis como senhas, tokens, números de cartão.

```php
// ❌ NÃO FAZER
$this->api_logger->log_request('POST', 'Account/login', 200, 0.1, $user_id, array(
    'password' => $_POST['password']  // NUNCA registrar senha!
));

// ✅ FAZER
$this->api_logger->log_request('POST', 'Account/login', 200, 0.1, $user_id, array(
    'success' => true  // Apenas informações não-sensíveis
));
```

### Retenção de Dados

```php
// Configurar período de retenção
$config['retention_days'] = 30;

// Executar limpeza manualmente
$this->api_logger->cleanup();
```

### Acesso a Logs

Restringir acesso ao painel de logs:

```php
public function view_logs()
{
    // Apenas admins podem ver logs
    if (!$this->session->userdata('is_admin')) {
        show_error('Access denied', 403);
    }

    // ... mostrar logs ...
}
```

## Troubleshooting

### Erro: "Table 'admCloud.api_logs' doesn't exist"

A tabela será criada automaticamente. Se não conseguir:

```sql
CREATE TABLE `api_logs` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Logs não aparecem

1. Verificar se hook está ativado em `hooks.php`
2. Verificar permissões de escrita em `application/logs/api/`
3. Verificar se database está acessível
4. Ver erro em `application/logs/log-*.php`

### Banco de dados crescendo muito

1. Aumentar frequência de limpeza
2. Reduzir período de retenção
3. Considerar armazenamento separado (data warehouse)

```bash
# Limpar logs manualmente
mysql> DELETE FROM api_logs WHERE timestamp < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

## Performance

Impacto estimado em performance:

- Database logging: ~2-5ms por requisição
- File logging: ~1-2ms por requisição
- Ambos: ~3-7ms por requisição

Para otimizar:

1. Usar file logging em produção (mais rápido)
2. Limpar logs antigos regularmente
3. Criar índices apropriados (já feito por padrão)
4. Considerar queue para logging assíncrono

## Próximos Passos

- [ ] Dashboard visual de logs
- [ ] Alertas por email para anomalias
- [ ] Integração com ELK Stack (Elasticsearch)
- [ ] Machine learning para detecção de padrões
- [ ] Real-time alerting com WebSockets

---

**Versão**: 1.0  
**Mantido por**: ADMCloud Team  
**Última Atualização**: 2024
