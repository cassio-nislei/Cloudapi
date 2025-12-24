# Rate Limiting - Guia de Implementação

## Visão Geral

Rate limiting protege a API contra abuso, prevenindo que um único cliente faça requisições em excesso. A implementação da ADMCloud suporta:

- ✅ Limitação por IP ou User ID
- ✅ Armazenamento em Database ou Arquivo
- ✅ Whitelist de IPs e Caminhos
- ✅ Headers HTTP padrão (X-RateLimit-\*)
- ✅ Limpeza automática de dados antigos
- ✅ Logging e alertas

## Instalação

### 1. Copiar Arquivos

```bash
# Library de rate limiting
cp application/libraries/Rate_limiter.php <projeto>/application/libraries/

# Hook para aplicação automática
cp application/hooks/RateLimitHook.php <projeto>/application/hooks/

# Configuração
cp application/config/rate_limiting.php <projeto>/application/config/
```

### 2. Criar Tabela no Banco de Dados

```sql
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `identifier` VARCHAR(255) NOT NULL UNIQUE,
    `request_count` INT NOT NULL DEFAULT 0,
    `first_request` INT NOT NULL,
    `last_request` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_identifier` (`identifier`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

A tabela será criada automaticamente na primeira requisição se não existir.

### 3. Ativar Hook

Editar `application/config/hooks.php`:

```php
$hook['pre_system'] = array(
    'class'    => 'RateLimitHook',
    'function' => 'execute',
    'filename' => 'RateLimitHook.php',
    'filepath' => 'hooks'
);
```

Isso ativa rate limiting em TODAS as requisições automaticamente.

## Configuração

Editar `application/config/rate_limiting.php`:

### Configuração Básica

```php
$config['rate_limiting'] = array(
    'enabled'           => TRUE,           // Ativar/desativar
    'max_requests'      => 1000,           // 1000 requisições
    'time_window'       => 3600,           // por hora
    'storage'           => 'database',     // ou 'file'
    'identify_by'       => 'ip',           // ou 'user_id'
    'headers_enabled'   => TRUE,           // Incluir headers
    'log_violations'    => TRUE,           // Logar excedências
);
```

### Limites por Endpoint

```php
'per_endpoint' => array(
    'api/v1/passport'  => 500,    // Endpoint crítico tem limite maior
    'api/v1/registro'  => 100,    // Endpoint protegido
    'Account/login'    => 20,     // Login tem limite menor
),
```

### Whitelist de IPs

```php
'whitelist_ips' => array(
    '127.0.0.1',                  // Localhost
    '::1',                        // IPv6 localhost
    '192.168.1.100',              // IP interno confiável
),
```

### Whitelist de Paths

```php
'whitelist_paths' => array(
    'health',                     // /health
    'status',                     // /status
    'ping',                       // /ping
    '#^api/v1/passport#',         // /api/v1/passport/*
),
```

## Uso Programático

### Verificar Limite Manualmente

```php
$this->load->library('rate_limiter');

if (!$this->rate_limiter->check_limit()) {
    // Cliente excedeu limite
    header('HTTP/1.1 429 Too Many Requests');
    exit('Too many requests');
}
```

### Obter Informações de Rate Limit

```php
$this->load->library('rate_limiter');

// Requisições restantes
$remaining = $this->rate_limiter->get_remaining();
echo "Requisições restantes: " . $remaining;

// Tempo até reset
$reset_in = $this->rate_limiter->get_reset_time();
echo "Reset em: " . $reset_in . " segundos";
```

### Resetar Limite

```php
// Resetar limite para um IP
$this->rate_limiter->reset('192.168.1.100');

// Resetar para o cliente atual
$this->rate_limiter->reset();
```

### Obter Estatísticas

```php
$stats = $this->rate_limiter->get_stats();
echo "Clientes rastreados: " . $stats['total_tracked'];
echo "Média de requisições: " . $stats['average_requests'];
echo "Máximo de requisições: " . $stats['max_requests'];
```

### Limpeza de Dados Antigos

```php
// Limpar registros mais antigos que 7 dias
$removed = $this->rate_limiter->cleanup(7);
echo "Registros removidos: " . $removed;

// Pode ser executado em cron job:
// 0 2 * * * php /caminho/do/projeto/index.php Cron/cleanup_rate_limits
```

## Headers HTTP Padrão

A API retorna headers padrão de rate limit:

```
X-RateLimit-Limit: 1000              # Limite total
X-RateLimit-Remaining: 950           # Requisições restantes
X-RateLimit-Reset: 1609459200        # Timestamp de reset
Retry-After: 3600                    # Segundos até poder requisitar
```

Cliente pode usar esses headers para:

```javascript
// JavaScript
const limit = response.headers["X-RateLimit-Limit"];
const remaining = response.headers["X-RateLimit-Remaining"];
const reset = response.headers["X-RateLimit-Reset"];

console.log(`Você tem ${remaining} de ${limit} requisições`);
console.log(`Reset em: ${new Date(reset * 1000)}`);
```

## Resposta ao Exceder Limite

### Padrão HTTP 429

```
HTTP/1.1 429 Too Many Requests
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1609459200
Retry-After: 3600
Content-Type: application/json

{
  "status": false,
  "msg": "Too many requests. Try again in 3600 seconds.",
  "reset_time": 3600
}
```

### Tratamento no Cliente

```php
// PHP/Guzzle
try {
    $response = $client->get('/api/v1/endpoint');
} catch (\GuzzleHttp\Exception\ClientException $e) {
    if ($e->getCode() === 429) {
        $retryAfter = $e->getResponse()->getHeader('Retry-After')[0];
        echo "Rate limited. Retry after: " . $retryAfter . " seconds";
    }
}
```

```javascript
// JavaScript/Fetch
fetch("/api/v1/endpoint")
  .then((response) => {
    if (response.status === 429) {
      const retryAfter = response.headers.get("Retry-After");
      console.error(`Rate limited. Retry after: ${retryAfter} seconds`);
      throw new Error("Rate limit exceeded");
    }
    return response.json();
  })
  .catch((error) => console.error(error));
```

## Cenários de Uso

### 1. Limitar Logins Falhados

```php
// Em Account controller
$this->load->library('rate_limiter');

if (!$this->rate_limiter->check_limit()) {
    // Demasiadas tentativas de login
    $this->session->set_flashdata('error', 'Muitas tentativas. Tente novamente depois.');
    redirect('Account/login');
}

// Validar credenciais...
if (!$this->validate_login()) {
    // Login inválido
}
```

### 2. Limitar Uploads

```php
// Em Upload controller
$this->load->library('rate_limiter');

// Limites especiais para uploads
$upload_limit = 10; // 10 uploads por hora
$this->rate_limiter->config['max_requests'] = $upload_limit;

if (!$this->rate_limiter->check_limit()) {
    $this->output
        ->set_status_header(429)
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'error' => 'Upload limit exceeded',
            'reset_time' => $this->rate_limiter->get_reset_time()
        ]));
    return;
}
```

### 3. Limites Diferenciados por Usuário

```php
// Em base controller
$this->load->library('rate_limiter');

// Usar user_id para limites por usuário
$config = get_rate_limit_config();

if ($user = $this->session->userdata('user_id')) {
    $user_role = $this->get_user_role();

    // Aplicar limite baseado em role
    if ($user_role === 'admin') {
        $config['max_requests'] = 5000; // Admin tem limite alto
    } else {
        $config['max_requests'] = 1000; // User normal
    }
}

$this->rate_limiter->check_limit();
```

## Monitoramento

### Verificar Violações em Log

```bash
# Ver últimas violações
tail -f application/logs/log-*.php | grep "Rate limit exceeded"
```

### Query para Verificar Clientes com Limite Próximo

```sql
SELECT identifier, request_count, first_request,
       TIMESTAMPDIFF(SECOND, FROM_UNIXTIME(first_request), NOW()) as elapsed
FROM rate_limits
WHERE request_count > 900
ORDER BY request_count DESC;
```

### Gráfico de Taxa de Requisições

```sql
SELECT
    DATE(FROM_UNIXTIME(created_at)) as data,
    COUNT(*) as total_clients,
    AVG(request_count) as avg_requests,
    MAX(request_count) as max_requests
FROM rate_limits
GROUP BY DATE(FROM_UNIXTIME(created_at))
ORDER BY data DESC;
```

## Otimizações

### 1. Usar Redis para Armazenamento Distribuído

```php
'advanced' => array(
    'use_redis'     => TRUE,
    'redis_host'    => 'localhost',
    'redis_port'    => 6379,
    'distributed'   => TRUE,
);
```

### 2. Limpeza Automática com Cron

```bash
# /etc/cron.d/admcloud-maintenance
0 2 * * * root curl -s http://localhost/cron/cleanup_rate_limits > /dev/null 2>&1
```

### 3. Cache em Memória

Usar APCu para cache local de curta duração:

```php
// Adicionar em Rate_limiter.php
private function get_from_cache($identifier)
{
    if (function_exists('apcu_fetch')) {
        return apcu_fetch('rate_limit_' . $identifier);
    }
    return false;
}
```

## Troubleshooting

### Erro: "Table 'admCloud.rate_limits' doesn't exist"

A tabela será criada automaticamente. Se não conseguir:

```bash
# Execute manualmente no MySQL
CREATE TABLE `rate_limits` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `identifier` VARCHAR(255) NOT NULL UNIQUE,
    `request_count` INT NOT NULL DEFAULT 0,
    `first_request` INT NOT NULL,
    `last_request` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Usuários Legítimos sendo Bloqueados

**Solução 1**: Aumentar limite

```php
'max_requests' => 2000, // Aumentar para 2000
```

**Solução 2**: Whitelist IP

```php
'whitelist_ips' => array(
    '192.168.1.50', // IP do usuário
),
```

**Solução 3**: Whitelist Path

```php
'whitelist_paths' => array(
    '#^api/v1/especifico#', // Endpoint específico
),
```

### Rate Limit não Está Funcionando

```php
// Verificar se hook está ativo
if (!$this->config->item('hooks')) {
    echo "Hooks não estão ativados!";
}

// Verificar se library carrega
$this->load->library('rate_limiter');
echo $this->rate_limiter->get_stats();
```

## Performance

Rate limiting tem impacto mínimo em performance:

- Database: ~2ms por requisição
- File: ~1ms por requisição
- Headers: < 1ms

Para melhor performance:

1. Usar `file` storage em vez de `database` para tráfego alto
2. Usar Redis para ambientes distribuídos
3. Implementar cache em memória (APCu)
4. Limpar dados antigos regularmente

## Segurança

### Proteções Implementadas

1. **SQL Injection**: Usar prepared statements (CodeIgniter)
2. **Rate Limit Bypass**: Validação de IP real com proxy headers
3. **Tampering**: Data assinada na sessão
4. **Whitelist Evasion**: Validação rigorosa de patterns

### Recomendações

1. Usar HTTPS para proteger headers
2. Logar todas as violações
3. Alertar administrador sobre padrões suspeitos
4. Revisar whitelist regularmente
5. Usar IPs confiáveis apenas

## Próximos Passos

- [ ] Implementar alertas por email
- [ ] Dashboard de monitoramento em tempo real
- [ ] Machine learning para detecção de anomalias
- [ ] Integração com WAF (Web Application Firewall)

---

**Versão**: 1.0  
**Mantido por**: ADMCloud Team  
**Última Atualização**: 2024
