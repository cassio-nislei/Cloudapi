# CORS (Cross-Origin Resource Sharing) - Guia de Implementação

## Visão Geral

CORS permite que aplicações em diferentes domínios façam requisições HTTP para a API ADMCloud com segurança. A implementação inclui:

- ✅ Validação de origens
- ✅ Suporte a preflight requests
- ✅ Configuração por ambiente (dev/prod)
- ✅ Headers de segurança
- ✅ Logging de violações
- ✅ Suporte a credenciais

## Como Funciona CORS

### Requisição Simples (GET, HEAD, POST básico)

```
Cliente (exemplo.com)
    ↓
[Browser adiciona header Origin]
    ↓
Servidor (api.admcloud.com)
    ↓
[Valida Origin]
    ↓
[Retorna headers Access-Control-*]
    ↓
[Browser libera resposta]
```

### Requisição com Preflight (PUT, DELETE, custom headers)

```
Cliente (exemplo.com)
    ↓
[Browser envia OPTIONS request]
    ↓
Servidor
    ↓
[Valida método e headers]
    ↓
[Retorna métodos e headers permitidos]
    ↓
[Se OK, browser envia requisição real]
    ↓
[Servidor processa e retorna resultado]
```

## Instalação

### 1. Copiar Arquivos

```bash
cp application/libraries/Cors.php <projeto>/application/libraries/
cp application/hooks/CorsHook.php <projeto>/application/hooks/
cp application/config/cors.php <projeto>/application/config/
```

### 2. Ativar Hook

Editar `application/config/hooks.php`:

```php
$hook['pre_system'] = array(
    'class'    => 'CorsHook',
    'function' => 'execute',
    'filename' => 'CorsHook.php',
    'filepath' => 'hooks'
);
```

## Configuração por Ambiente

Editar `application/config/cors.php`:

### Produção

```php
'allowed_origins' => array(
    'https://admcloud.papion.com.br',
    'https://app.admcloud.papion.com.br',
    'https://api.admcloud.papion.com.br',
),
```

### Desenvolvimento

```php
'allowed_origins' => array(
    'http://localhost:3000',
    'http://localhost:8080',
    'http://127.0.0.1:3000',
    'https://localhost:3000',
),
```

### Com Wildcards

```php
'allowed_origins' => array(
    'https://*.admcloud.papion.com.br',  // Qualquer subdomínio
    'https://api.example.*',              // Qualquer TLD
),
```

## Configuração Detalhada

### Origens Permitidas

```php
'allowed_origins' => array(
    'https://example.com',           // Origem exata
    'https://*.example.com',         // Qualquer subdomínio
    'http://localhost:*',            // Qualquer porta
    'app://mobile',                  // App nativo
    'capacitor://localhost',         // Ionic Capacitor
),
```

### Métodos HTTP

```php
'allowed_methods' => array(
    'GET',                   // Lectura
    'POST',                  // Crear
    'PUT',                   // Actualizar
    'DELETE',                // Eliminar
    'PATCH',                 // Actualizar parcial
    'OPTIONS',               // Preflight
    'HEAD'                   // Solo headers
),
```

### Headers Permitidos

```php
'allowed_headers' => array(
    'Content-Type',          // Tipo de conteúdo
    'Authorization',         // Token de autenticação
    'X-Request-ID',         // ID único da requisição
    'X-API-Key',            // Chave de API
    'X-CSRF-Token',         // Token CSRF
),
```

### Headers Expostos

```php
'exposed_headers' => array(
    'X-RateLimit-Limit',     // Limite de requisições
    'X-RateLimit-Remaining', // Requisições restantes
    'X-RateLimit-Reset',     // Quando reseta o limite
    'X-Total-Count',         // Contagem total (paginação)
    'X-Request-ID',          // ID da requisição
),
```

## Uso em Aplicação Frontend

### JavaScript/Fetch

```javascript
// Requisição simples
fetch("https://api.admcloud.com/api/v1/usuarios", {
  method: "GET",
  headers: {
    Authorization: "Bearer seu_token_aqui",
  },
}).then((response) => response.json());

// Com credenciais
fetch("https://api.admcloud.com/api/v1/usuarios", {
  method: "GET",
  credentials: "include", // Incluir cookies
  headers: {
    Authorization: "Bearer seu_token_aqui",
  },
});

// POST com headers customizados
fetch("https://api.admcloud.com/api/v1/usuarios", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    Authorization: "Bearer seu_token_aqui",
    "X-Request-ID": generateRequestId(),
  },
  body: JSON.stringify({
    nome: "João Silva",
    email: "joao@example.com",
  }),
});
```

### jQuery/AJAX

```javascript
$.ajax({
  url: "https://api.admcloud.com/api/v1/usuarios",
  type: "GET",
  headers: {
    Authorization: "Bearer seu_token_aqui",
  },
  xhrFields: {
    withCredentials: true, // Incluir credenciais
  },
  success: function (data) {
    console.log(data);
  },
});
```

### Axios

```javascript
const instance = axios.create({
  baseURL: "https://api.admcloud.com/api/v1",
  headers: {
    Authorization: "Bearer seu_token_aqui",
  },
  withCredentials: true, // Incluir cookies
});

// Usar em requisições
instance.get("/usuarios").then((response) => {
  console.log(response.data);
});
```

## Troubleshooting

### Erro: "Access-Control-Allow-Origin header missing"

**Causa**: Origem não está na whitelist

**Solução 1**: Adicionar origem em `cors.php`

```php
'allowed_origins' => array(
    'https://seu-dominio.com',  // Adicionar aqui
),
```

**Solução 2**: Verificar se Origin está sendo enviado

```javascript
// No browser console
fetch("https://api.admcloud.com/api/v1/usuarios", {
  method: "GET",
  headers: {
    Authorization: "Bearer token",
  },
  // Origin é adicionado automaticamente pelo browser
});
```

### Erro: "Access-Control-Allow-Methods missing"

**Causa**: Método HTTP não está permitido

**Solução**: Adicionar método em `cors.php`

```php
'allowed_methods' => array(
    'GET',
    'POST',
    'PUT',      // Adicionar se faltando
    'DELETE',   // Adicionar se faltando
),
```

### Erro: "Access-Control-Allow-Headers missing"

**Causa**: Header customizado não está permitido

**Solução**: Adicionar header em `cors.php`

```php
'allowed_headers' => array(
    'Content-Type',
    'Authorization',
    'X-Custom-Header',  // Adicionar header customizado
),
```

### Preflight request fica pendurado

**Causa**: Servidor retorna erro no preflight

**Solução**: Verificar logs do servidor

```bash
tail -f application/logs/log-*.php | grep CORS
```

## Testando CORS

### Com curl (simula preflight)

```bash
# Preflight request
curl -X OPTIONS \
  -H "Origin: https://example.com" \
  -H "Access-Control-Request-Method: POST" \
  -H "Access-Control-Request-Headers: Content-Type" \
  https://api.admcloud.com/api/v1/usuarios -v

# Resultado esperado
> Access-Control-Allow-Origin: https://example.com
> Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
> Access-Control-Allow-Headers: Content-Type, Authorization
```

### Com postman

1. Abrir Postman
2. Criar requisição POST para `https://api.admcloud.com/api/v1/usuarios`
3. Ir em Headers
4. Adicionar `Origin: https://example.com`
5. Enviar
6. Verificar response headers por `Access-Control-*`

### Com desenvolvimento local

```html
<!DOCTYPE html>
<html>
  <head>
    <title>CORS Test</title>
  </head>
  <body>
    <h1>CORS Test</h1>
    <button onclick="testCors()">Test CORS</button>
    <div id="result"></div>

    <script>
      function testCors() {
        fetch("https://api.admcloud.com/api/v1/usuarios", {
          method: "GET",
          headers: {
            Authorization: "Bearer seu_token_aqui",
          },
        })
          .then((response) => {
            console.log("Response headers:", response.headers);
            return response.json();
          })
          .then((data) => {
            document.getElementById("result").innerHTML =
              "<pre>" + JSON.stringify(data, null, 2) + "</pre>";
          })
          .catch((error) => {
            document.getElementById("result").innerHTML =
              '<pre style="color:red">' + error.message + "</pre>";
          });
      }
    </script>
  </body>
</html>
```

## Casos de Uso Comuns

### 1. App em localhost acessando API em produção

```php
// cors.php
'allowed_origins' => array(
    'http://localhost:3000',       // Dev local
    'https://app.production.com',  // Produção
),
```

### 2. Múltiplos subdomínios

```php
'allowed_origins' => array(
    'https://admin.example.com',
    'https://app.example.com',
    'https://mobile.example.com',
    // Ou usar wildcard
    'https://*.example.com',
),
```

### 3. App nativa (React Native, Flutter)

```php
'allowed_origins' => array(
    'app://localhost',          // React Native
    'capacitor://localhost',    // Ionic Capacitor
    'file:///',                 // File protocol (cuidado!)
),
```

### 4. Desenvolvimento com Docker

```php
'allowed_origins' => array(
    'http://localhost:3000',
    'http://127.0.0.1:3000',
    'http://host.docker.internal:3000',  // Docker for Mac/Windows
    'http://frontend:3000',               // Docker network
),
```

## Segurança

### ✅ Boas Práticas

1. **Whitelist específica**: Apenas adicionar origens confiáveis
2. **HTTPS em produção**: Sempre usar HTTPS em produção
3. **Validação de credentials**: Validar usuário mesmo com CORS
4. **Rate limiting**: Aplicar rate limiting junto com CORS
5. **Logging**: Logar requisições CORS suspeitas

### ❌ Evitar

1. `allow_any_origin: true` em produção
2. Permitir `file://` protocol
3. Confiar apenas em CORS (validar no servidor)
4. Headers de autenticação muito permissivos

## Monitoramento

### Logs de Violação CORS

```bash
# Ver violações CORS
grep "CORS" application/logs/log-*.php

# Ver origens rejeitadas
grep "Invalid origin" application/logs/log-*.php
```

### Dashboard de CORS

```sql
-- Origens que tentaram acessar
SELECT DISTINCT origin, COUNT(*) as attempts
FROM cors_attempts
WHERE status = 'rejected'
GROUP BY origin
ORDER BY attempts DESC;
```

## Deployment em Produção

### Pré-deployment Checklist

- [ ] Configurar `allowed_origins` com domínios de produção
- [ ] Desabilitar `allow_any_origin`
- [ ] Settar `strict_mode: true`
- [ ] Configurar `max_age` para cache long-lived
- [ ] Testar com ferramentas externas (não-localhost)
- [ ] Revisar logs para origens suspeitas
- [ ] Documentar origens permitidas

### Exemplo Production

```php
// cors.php - PRODUCTION
'allowed_origins' => array(
    'https://admcloud.papion.com.br',
    'https://app.admcloud.papion.com.br',
),

'allowed_methods' => array('GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'),

'allow_credentials' => TRUE,

'strict_mode' => TRUE,

'max_age' => 86400,  // 24 horas

'log_cors_errors' => TRUE,
```

## Performance

CORS tem impacto mínimo:

- Validação de origin: < 1ms
- Preflight request: ~100-300ms (cached depois)
- Headers: < 1ms

Para otimizar:

1. Aumentar `max_age` em produção (cache preflight)
2. Usar DNS caching
3. Considerar CDN para assets estáticos

## Próximos Passos

- [ ] Implementar rate limiting por origin
- [ ] Dashboard de monitoramento CORS
- [ ] Alertas para origens suspeitas
- [ ] Integração com WAF (Web Application Firewall)

---

**Versão**: 1.0  
**Mantido por**: ADMCloud Team  
**Última Atualização**: 2024
