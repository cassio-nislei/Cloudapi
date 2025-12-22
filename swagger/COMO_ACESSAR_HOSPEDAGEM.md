# 🌐 Como Acessar o Swagger - Guia de Hospedagem

**Data:** 09 de Dezembro de 2024  
**Versão:** 1.0

---

## 📍 Localização dos Arquivos Swagger

Após fazer upload da pasta `swagger/` para seu servidor, os arquivos estarão em:

```
seu-dominio.com/
├── swagger/
│   ├── index.html                    ← Interface Swagger (abrir no navegador)
│   ├── openapi.yaml                  ← Especificação YAML
│   ├── openapi.json                  ← Especificação JSON
│   └── API_DOCUMENTATION.md          ← Documentação Markdown
└── api/v1/                           ← API endpoints
    ├── /passport
    ├── /registro (GET)
    └── /registro (POST)
```

---

## 🚀 Como Acessar (3 Formas)

### 1️⃣ **Interface Web (Recomendado)**

Abra no navegador:

```
https://seu-dominio.com/swagger/
```

ou

```
https://seu-dominio.com/swagger/index.html
```

**O que você vê:**

- ✅ Documentação interativa
- ✅ Botão "Try it out" para testar
- ✅ Modelos de requisição
- ✅ Exemplos de resposta

---

### 2️⃣ **Importar em Ferramentas**

#### **Postman**

1. Abra Postman
2. Clique em `Import`
3. Cole a URL: `https://seu-dominio.com/swagger/openapi.json`
4. Clique em `Import`
5. Todos os endpoints aparecem automaticamente

#### **Insomnia**

1. Abra Insomnia
2. Clique em `Create` → `Import`
3. Cole a URL: `https://seu-dominio.com/swagger/openapi.json`
4. Clique em `Import`

#### **Swagger Editor Online**

1. Acesse: https://editor.swagger.io/
2. Clique em `File` → `Import URL`
3. Cole: `https://seu-dominio.com/swagger/openapi.json`
4. Documentação aparece automaticamente

#### **ReDoc (Documentação Bonita)**

1. Acesse: https://redocly.github.io/redoc/
2. Cole a URL no campo superior
3. Clique em `Load from URL`

---

### 3️⃣ **Acessar Especificações Diretamente**

**YAML (legível):**

```
https://seu-dominio.com/swagger/openapi.yaml
```

**JSON (máquinas):**

```
https://seu-dominio.com/swagger/openapi.json
```

**Markdown (texto):**

```
https://seu-dominio.com/swagger/API_DOCUMENTATION.md
```

---

## 🔧 Configuração de Hospedagem

### Requisitos Mínimos

- ✅ Servidor web (Apache, Nginx, IIS)
- ✅ Pasta `swagger/` no root ou subdiretório
- ✅ Permissão de leitura para arquivos estáticos

### Passos de Hospedagem

#### **1. Preparar Arquivos Localmente**

```bash
# Verificar pasta swagger
ls -la admcloud/swagger/
# Deve mostrar:
# index.html
# openapi.yaml
# openapi.json
# API_DOCUMENTATION.md
```

#### **2. Fazer Upload (FTP/SFTP)**

```
Origem:  admcloud/swagger/
Destino: /public_html/swagger/
         ou
         /var/www/seu-dominio/swagger/
```

#### **3. Verificar Permissões**

```bash
# Dar permissão de leitura
chmod 644 swagger/*.html
chmod 644 swagger/*.yaml
chmod 644 swagger/*.json
chmod 644 swagger/*.md
```

#### **4. Testar Acesso**

```bash
# Verificar se arquivo está acessível
curl https://seu-dominio.com/swagger/index.html
```

---

## 🎯 Acessos Finais

| Recurso                | URL                                                    | O Que É         |
| ---------------------- | ------------------------------------------------------ | --------------- |
| **Interface Web**      | `https://seu-dominio.com/swagger/`                     | UI Interativa   |
| **Especificação YAML** | `https://seu-dominio.com/swagger/openapi.yaml`         | Formato YAML    |
| **Especificação JSON** | `https://seu-dominio.com/swagger/openapi.json`         | Formato JSON    |
| **Documentação**       | `https://seu-dominio.com/swagger/API_DOCUMENTATION.md` | Markdown        |
| **API**                | `https://seu-dominio.com/api/v1/...`                   | Endpoints reais |

---

## 🔐 Segurança em Produção

### 1. Usando HTTPS

```
✅ https://seu-dominio.com/swagger/index.html
❌ http://seu-dominio.com/swagger/index.html
```

### 2. Proteger Acesso (Opcional)

**Apache .htaccess:**

```apache
<Directory "/var/www/seu-dominio/swagger">
    AuthType Basic
    AuthName "API Documentation"
    AuthUserFile /etc/apache2/.htpasswd
    Require valid-user
</Directory>
```

**Nginx:**

```nginx
location /swagger/ {
    auth_basic "API Documentation";
    auth_basic_user_file /etc/nginx/.htpasswd;
}
```

### 3. CORS (Se necessário)

**Apache:**

```apache
<Directory "/var/www/seu-dominio/swagger">
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
</Directory>
```

---

## 📊 Exemplo Completo de Hospedagem

### Cenário: Hospedagem em Shared Hosting com cPanel

**1. Fazer Upload**

```
1. Acessar cPanel
2. Ir para File Manager
3. Navegar para public_html/
4. Criar pasta "swagger"
5. Fazer upload dos arquivos:
   - index.html
   - openapi.yaml
   - openapi.json
   - API_DOCUMENTATION.md
```

**2. Acessar**

```
https://seu-dominio.com/swagger/index.html
```

**3. Testar Endpoints**

```
Na interface Swagger:
1. Clicar em "GET /passport"
2. Clicar em "Try it out"
3. Preencher parâmetros:
   - cgc: 12345678901234
   - hostname: DESKTOP-PC
   - guid: ABC-123-DEF
4. Clicar em "Execute"
5. Ver resposta
```

---

## 🐛 Troubleshooting

### ❌ Erro: "404 Not Found"

**Causa:** Arquivo swagger não encontrado no servidor

**Solução:**

1. Verificar se pasta swagger foi uploadada
2. Verificar caminho correto
3. Verificar permissões de arquivo

```bash
# No servidor
ls -la /var/www/seu-dominio/swagger/
# Deve mostrar arquivos
```

### ❌ Erro: "JSON Specification Error"

**Causa:** Arquivo openapi.json está corrompido ou inacessível

**Solução:**

1. Re-fazer upload do arquivo
2. Verificar se arquivo está íntegro
3. Verificar permissões (chmod 644)

### ❌ Erro: "CORS blocked"

**Causa:** Browser bloqueou requisição por CORS

**Solução:** Se API está em domínio diferente, adicionar CORS ao `api/v1/`:

**PHP (.htaccess):**

```apache
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type, Authorization"
```

### ❌ Erro: "API não responde"

**Causa:** API não está acessível de onde Swagger está

**Solução:**

1. Verificar se API está online
2. Verificar URL da API no Swagger
3. Verificar se há firewall bloqueando

---

## 🌐 URLs de Exemplo (Substitua seu-dominio.com)

### Domínio: admcloud.papion.com.br

```
Swagger UI:  https://admcloud.papion.com.br/swagger/
YAML Spec:   https://admcloud.papion.com.br/swagger/openapi.yaml
JSON Spec:   https://admcloud.papion.com.br/swagger/openapi.json
API:         https://admcloud.papion.com.br/api/v1/passport
```

### Domínio: localhost (desenvolvimento)

```
Swagger UI:  http://localhost/swagger/
YAML Spec:   http://localhost/swagger/openapi.yaml
JSON Spec:   http://localhost/swagger/openapi.json
API:         http://localhost/api/v1/passport
```

### Domínio: subdomínio (ex: api.empresa.com)

```
Swagger UI:  https://api.empresa.com/swagger/
YAML Spec:   https://api.empresa.com/swagger/openapi.yaml
JSON Spec:   https://api.empresa.com/swagger/openapi.json
API:         https://api.empresa.com/api/v1/passport
```

---

## 📱 Acessar em Dispositivos

### No Computador

```
Chrome:  https://seu-dominio.com/swagger/
Firefox: https://seu-dominio.com/swagger/
Safari:  https://seu-dominio.com/swagger/
Edge:    https://seu-dominio.com/swagger/
```

### No Celular

```
Safari (iOS):   https://seu-dominio.com/swagger/
Chrome (Android): https://seu-dominio.com/swagger/
```

---

## 🔗 Compartilhar Documentação

### Link para Compartilhar

```
https://seu-dominio.com/swagger/index.html
```

### Integrar em Seu Site

```html
<!-- Copiar para seu site -->
<iframe
  src="https://seu-dominio.com/swagger/index.html"
  width="100%"
  height="600"
></iframe>
```

### Em Documentação Interna

```markdown
[API Documentation](https://seu-dominio.com/swagger/)
```

---

## ✅ Checklist de Hospedagem

- [ ] Pasta swagger/ criada no servidor
- [ ] Arquivos uploadados (index.html, openapi.yaml, openapi.json, API_DOCUMENTATION.md)
- [ ] Permissões corretas (644 para arquivos, 755 para pasta)
- [ ] URL acessível via navegador
- [ ] HTTPS configurado
- [ ] Swagger UI carrega sem erros
- [ ] Endpoints aparecem na documentação
- [ ] Botão "Try it out" funciona
- [ ] Respostas aparecem corretamente
- [ ] Documentação compartilhada com time

---

## 💡 Dicas Úteis

### 1. Swagger UI Offline

Se quiser usar offline, copie a pasta `swagger/` para seu computador e abra `index.html` localmente.

### 2. Atualizar Documentação

Para atualizar após fazer mudanças:

1. Regenerar os arquivos localmente
2. Fazer upload apenas dos arquivos alterados
3. Limpar cache do navegador (Ctrl+Shift+Delete)

### 3. Diferentes Ambientes

```
Desenvolvimento:  https://dev.seu-dominio.com/swagger/
Staging:         https://staging.seu-dominio.com/swagger/
Produção:        https://seu-dominio.com/swagger/
```

### 4. Versioning

```
API v1:  https://seu-dominio.com/api/v1/
API v2:  https://seu-dominio.com/api/v2/
Docs v1: https://seu-dominio.com/swagger/v1/
Docs v2: https://seu-dominio.com/swagger/v2/
```

---

## 🚀 Próximos Passos

1. ✅ Upload arquivos swagger
2. ✅ Acessar via navegador
3. ✅ Testar endpoints
4. ✅ Compartilhar documentação com time
5. ✅ Integrar em portal interno se houver

---

## 📞 Suporte Rápido

**P: Onde coloco os arquivos swagger?**  
R: Na pasta raiz do seu servidor, em uma subpasta chamada `swagger/`

**P: Qual URL acessar?**  
R: `https://seu-dominio.com/swagger/` ou `https://seu-dominio.com/swagger/index.html`

**P: Como testar endpoints?**  
R: Use o botão "Try it out" na interface Swagger

**P: Posso editar online?**  
R: Não. Edite os arquivos localmente e faça upload novamente.

**P: Como compartilhar com outros?**  
R: Compartilhe o link: `https://seu-dominio.com/swagger/`

---

**Pronto para acessar seu Swagger! 🚀**
