# 🔧 Resolver Erro 403 Forbidden - KingHost

**Erro:** `403 Forbidden - You don't have permission to access this resource`  
**Hospedagem:** KingHost  
**Data:** 09 de Dezembro de 2024

---

## 🔴 O Que Significa

KingHost está bloqueando o acesso aos arquivos da pasta `swagger/` por:

- ❌ Permissões incorretas de arquivo/pasta
- ❌ Arquivo .htaccess bloqueando acesso
- ❌ Diretório sem index.html visível
- ❌ Tipos de arquivo não permitidos

---

## ✅ Solução Rápida (KingHost específico)

### Passo 1: Acessar Gerenciador de Arquivos

1. Acesse **cPanel KingHost**
2. Clique em **Gerenciador de Arquivos** (File Manager)
3. Navegue para a pasta `swagger/`

### Passo 2: Alterar Permissões

#### **Forma 1: Via cPanel (Recomendado)**

1. Clique com botão direito na pasta `swagger`
2. Selecione **Alterar Permissões** ou **Change Permissions**
3. Defina para:

   ```
   Pasta (swagger/):    755
   Arquivos (.html, .json, .yaml): 644
   ```

4. Marque **Aplicar recursivamente** se houver essa opção
5. Clique em **Alterar** ou **Change**

#### **Forma 2: Via Terminal SSH (Se tiver acesso)**

```bash
# Conectar ao servidor
ssh seu-usuario@seu-dominio.com

# Navegar para pasta
cd public_html/swagger

# Dar permissão à pasta
chmod 755 .

# Dar permissão aos arquivos
chmod 644 *.html
chmod 644 *.json
chmod 644 *.yaml
chmod 644 *.md

# Verificar permissões
ls -la
```

### Passo 3: Adicionar .htaccess

Se a solução acima não funcionar, crie um arquivo `.htaccess` na pasta `swagger/`:

1. Clique em **Criar Novo Arquivo**
2. Nome: `.htaccess`
3. Conteúdo:

```apache
# Permitir acesso direto a todos os arquivos
<Files "*">
    Order Allow,Deny
    Allow from all
</Files>

# Permitir tipos de arquivo
AddType application/json .json
AddType application/x-yaml .yaml
AddType text/html .html
AddType text/markdown .md

# Remover proteções de acesso (se houver)
<Directory "/">
    Order Allow,Deny
    Allow from all
</Directory>

# Se usar autenticação, comentar as linhas abaixo:
# AuthType None
# AuthName ""
</Files>
```

4. Salve o arquivo
5. Limpe cache e tente acessar novamente

---

## 🔍 Verificar Permissões Atuais

### Via cPanel File Manager

1. Clique com direito na pasta/arquivo
2. Propriedades ou **Properties**
3. Verifique a aba **Permissions**
4. Anote os valores atuais

**O que você vê:**

```
Proprietário (Owner): seu-usuario
Grupo (Group): seu-usuario
Pasta: 755 (rwxr-xr-x)
Arquivo: 644 (rw-r--r--)
```

### Via SSH (Terminal)

```bash
# Ver permissões atuais
ls -la /home/seu-usuario/public_html/swagger/

# Resultado esperado:
# -rw-r--r-- 1 user group 12345 Dec  9 10:00 index.html
# -rw-r--r-- 1 user group 67890 Dec  9 10:00 openapi.json
# drwxr-xr-x 2 user group  4096 Dec  9 10:00 .
```

---

## 📋 Checklist de Resolução

### Verificação 1: Estrutura de Pasta

```
✅ /public_html/swagger/
   ✅ index.html
   ✅ openapi.json
   ✅ openapi.yaml
   ✅ API_DOCUMENTATION.md
```

Todas as pastas existem?

### Verificação 2: Permissões

```bash
chmod 755 /public_html/swagger/
chmod 644 /public_html/swagger/*.html
chmod 644 /public_html/swagger/*.json
chmod 644 /public_html/swagger/*.yaml
chmod 644 /public_html/swagger/*.md
```

### Verificação 3: Arquivos Inteiros

- Tamanho de `index.html` > 1 KB? ✅
- Tamanho de `openapi.json` > 5 KB? ✅
- Tamanho de `openapi.yaml` > 5 KB? ✅

Se algum arquivo é 0 bytes, fazer upload novamente.

### Verificação 4: Acesso URL

```
✅ https://seu-dominio.com/swagger/
✅ https://seu-dominio.com/swagger/index.html
✅ https://seu-dominio.com/swagger/openapi.json
```

Todas as URLs funcionam?

---

## 🛠️ Soluções Específicas por Causa

### ❌ Erro 403 - Permissão Negada

**Solução:**

```bash
# Corrigir permissões
chmod -R 755 /public_html/swagger/
```

### ❌ Erro 403 - Diretório Não Acessível

**Solução 1:** Crie um `index.html` na pasta raiz do swagger

```html
<!DOCTYPE html>
<html>
  <head>
    <title>API Documentation</title>
  </head>
  <body>
    <a href="index.html">Documentação da API</a>
  </body>
</html>
```

**Solução 2:** Adicione ao `.htaccess`:

```apache
DirectoryIndex index.html
```

### ❌ Erro 403 - Arquivo .htaccess Bloqueando

**Solução:** Verificar se há `.htaccess` na pasta pai (`public_html/`)

1. Abra `public_html/.htaccess`
2. Procure por linhas bloqueando swagger:
   ```apache
   # Remover ou comentar:
   # <Files "swagger">
   # Deny from all
   # </Files>
   ```
3. Salve o arquivo

### ❌ Erro 403 - Tipo de Arquivo Não Permitido

**Solução:** Adicione tipos MIME ao `.htaccess` da pasta swagger:

```apache
AddType application/json .json
AddType application/x-yaml .yaml
AddType text/html .html
AddType text/markdown .md
AddType text/plain .txt
```

---

## 📞 Contato KingHost (Se Problema Persistir)

### Suporte KingHost

- **Chat:** https://www.kinghost.com.br/suporte
- **Email:** suporte@kinghost.com.br
- **Telefone:** 0800-001-7999

### Mensagem para Suporte (Copie e Cole)

```
Olá,

Estou recebendo erro 403 Forbidden ao tentar acessar a pasta
/public_html/swagger/ e seus arquivos (index.html, openapi.json, openapi.yaml).

Informações:
- Domínio: seu-dominio.com
- Pasta: /public_html/swagger/
- Permissões atuais: 644 em arquivos, 755 em pasta
- Erro: 403 Forbidden - You don't have permission to access this resource

Já tentei:
✓ Alterar permissões para 644/755
✓ Adicionar .htaccess com Allow from all
✓ Limpar cache e tentar em navegador diferente

Poderiam me ajudar?

Obrigado,
[Seu Nome]
```

---

## 🚀 Solução Alternativa: Usar Subdomínio

Se a pasta `swagger/` continuar com erro 403:

### 1. Criar Subdomínio

1. Acesse cPanel → Domínios
2. Clique em **Adicionar Domínio Addon** ou **Subdomínio**
3. Nome: `api` ou `docs`
4. Raiz: `/public_html/swagger/`
5. Clique em **Criar**

### 2. Acessar

```
https://api.seu-dominio.com/
ou
https://docs.seu-dominio.com/
```

### 3. Ajustar Permissões do Subdomínio

```bash
chmod 755 /public_html/swagger/
chmod 644 /public_html/swagger/*
```

---

## 🔐 Solução Completa com .htaccess

Se ainda não funcionar, substitua o conteúdo do `.htaccess` por este (mais permissivo):

```apache
# ==================================================
# Swagger Documentation Access
# ==================================================

# Remover todas as restrições
<Files "*">
    Order Allow,Deny
    Allow from all
</Files>

# Permitir diretório listável (se necessário)
Options +Indexes

# Definir DirectoryIndex
DirectoryIndex index.html

# Adicionar tipos MIME
<FilesMatch "\.(json|yaml|yml|html|md|txt)$">
    Header set Content-Type "text/plain; charset=utf-8"
    Order Allow,Deny
    Allow from all
</FilesMatch>

# JSON
<FilesMatch "\.json$">
    AddType application/json .json
    Order Allow,Deny
    Allow from all
</FilesMatch>

# YAML
<FilesMatch "\.(yaml|yml)$">
    AddType application/x-yaml .yaml
    AddType application/x-yaml .yml
    Order Allow,Deny
    Allow from all
</FilesMatch>

# HTML
<FilesMatch "\.html?$">
    AddType text/html .html
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Markdown
<FilesMatch "\.md$">
    AddType text/markdown .md
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Remover proteção de acesso se existir
<Directory "/home/*/public_html/swagger">
    Order Allow,Deny
    Allow from all
</Directory>

# Desabilitar autenticação se houver
<IfModule mod_auth.c>
    Satisfy any
</IfModule>
```

---

## ✅ Teste Passo-a-Passo

### Teste 1: Acessar HTML

```
https://seu-dominio.com/swagger/index.html
```

Espera-se: Página carrega com interface Swagger

### Teste 2: Acessar JSON

```
https://seu-dominio.com/swagger/openapi.json
```

Espera-se: Arquivo JSON baixa ou abre no navegador

### Teste 3: Acessar YAML

```
https://seu-dominio.com/swagger/openapi.yaml
```

Espera-se: Arquivo YAML baixa ou abre no navegador

### Teste 4: Testar via Browser Dev Tools

```javascript
// Abra o console do navegador (F12)
// E execute:
fetch("https://seu-dominio.com/swagger/openapi.json")
  .then((r) => r.json())
  .then((d) => console.log(d))
  .catch((e) => console.error(e));
```

---

## 🎯 Ordem de Tentativas (De Mais Fácil para Mais Difícil)

1. ✅ **Alterar permissões via cPanel** (2 min)
2. ✅ **Adicionar .htaccess** (3 min)
3. ✅ **Subdomínio novo** (5 min)
4. ✅ **Suporte KingHost** (30 min ou mais)

---

## 💡 Dicas

### Não Use FTP Antigo

Use **SFTP** em vez de FTP para garantir que permissões sejam mantidas corretamente.

### Limpar Cache

Depois de fazer mudanças:

- **Browser:** Ctrl+Shift+Delete (limpar cache)
- **KingHost:** Sem cache para Swagger (não há)
- **Acesso:** Espere 2-5 minutos para aplicar

### Testar em Navegador Incógnito

```
Ctrl+Shift+N (Chrome)
Ctrl+Shift+P (Firefox)
Cmd+Shift+N (Safari)
```

### Verificar URL Exata

Comum fazer upload em:

- ❌ `public_html/swagger/swagger/` (pasta duplicada)
- ✅ `public_html/swagger/` (correto)

---

## 📝 Próximas Etapas

Após resolver o 403:

1. ✅ Acessar `https://seu-dominio.com/swagger/`
2. ✅ Ver interface Swagger carregando
3. ✅ Clicar em um endpoint (ex: GET /passport)
4. ✅ Clicar em "Try it out"
5. ✅ Preencher parâmetros
6. ✅ Clicar em "Execute"
7. ✅ Ver resposta da API

---

**Comece por alterar permissões via cPanel - resolve 90% dos casos!** 🚀
