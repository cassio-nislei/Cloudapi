# 🐳 ADMCloud API - Docker Setup

**Data:** 22 de Dezembro de 2025  
**Status:** ✅ Pronto para Produção

---

## 📋 O que inclui este setup Docker?

- ✅ **PHP 8.2** com Apache e extensões necessárias
- ✅ **MySQL 8.0** com dados de teste pré-configurados
- ✅ **phpMyAdmin** para gerenciar banco de dados
- ✅ **Composer** para dependências PHP
- ✅ **Health checks** automáticos
- ✅ **Volumes persistentes** para banco de dados
- ✅ **Network isolada** para segurança

---

## 🚀 Quick Start - Subir Container em 3 passos

### 1️⃣ Pré-requisitos

Você precisa ter instalado:

- **Docker** ([baixar](https://www.docker.com/products/docker-desktop))
- **Docker Compose** (geralmente vem com Docker Desktop)

Verificar instalação:

```bash
docker --version
docker-compose --version
```

### 2️⃣ Clonar/Baixar o Projeto

```bash
# Navegar até a pasta do projeto
cd c:\Users\nislei\Desktop\DLL\admcloud

# Copiar arquivo de variáveis de ambiente
copy .env.example .env
```

### 3️⃣ Iniciar os Containers

```bash
# Subir containers em background
docker-compose up -d

# Ver status dos containers
docker-compose ps

# Ver logs da aplicação
docker-compose logs -f admcloud-api
```

**Pronto! A API está rodando!** 🎉

---

## 🌐 Acessar a API

### Endpoints

| Serviço            | URL                                   | Credenciais               |
| ------------------ | ------------------------------------- | ------------------------- |
| **API REST**       | http://localhost:8080                 | N/A                       |
| **phpMyAdmin**     | http://localhost:8081                 | papion / Pap10nL4vrAs2024 |
| **GET /passport**  | http://localhost:8080/api/v1/passport | Pública                   |
| **POST /registro** | http://localhost:8080/api/v1/registro | Basic Auth                |

### Teste Rápido do /passport

```bash
curl -X GET "http://localhost:8080/api/v1/passport?cgc=01611275000205&hostname=DOCKER-TEST&guid=550e8400-e29b-41d4-a716-446655440000"
```

**Resposta esperada:**

```json
{
  "Status": true,
  "Mensagem": "Passport OK!",
  "Dados": {
    "id_pessoa": 1,
    "nome": "PAPION INFORMÁTICA",
    "cgc": "01611275000205",
    "email": "papion@papion.com.br",
    ...
  }
}
```

---

## 📂 Estrutura de Arquivos Docker

```
admcloud/
├── Dockerfile                    # Imagem da API PHP
├── docker-compose.yml            # Orquestração de containers
├── .dockerignore                 # Arquivos ignorados no build
├── .env.example                  # Variáveis de ambiente (exemplo)
├── docker/
│   ├── apache.conf              # Configuração do Apache
│   ├── init.sql                 # Script SQL de inicialização
│   └── php.ini (opcional)       # Configurações do PHP
├── application/                 # Código da aplicação
├── vendor/                       # Dependências Composer
└── uploads/                      # Diretório de uploads (volume)
```

---

## 🛠️ Comandos Úteis

### Gerenciar Containers

```bash
# Ver containers rodando
docker-compose ps

# Ver logs de um serviço específico
docker-compose logs -f admcloud-api
docker-compose logs -f mysql

# Parar containers
docker-compose stop

# Parar e remover containers
docker-compose down

# Remover volumes (⚠️ deleta banco de dados!)
docker-compose down -v

# Reiniciar serviços
docker-compose restart
docker-compose restart admcloud-api
```

### Acessar Container

```bash
# Acessar shell do PHP/Apache
docker-compose exec admcloud-api bash

# Acessar MySQL
docker-compose exec mysql mysql -u papion -p papion

# Ver variáveis de ambiente
docker-compose exec admcloud-api env
```

### Build e Deploy

```bash
# Reconstruir imagem (se modificou Dockerfile)
docker-compose build --no-cache

# Reconstruir e subir
docker-compose up -d --build

# Verificar imagens locais
docker images

# Remover imagens não utilizadas
docker image prune
```

---

## 📊 Monitorar Saúde da API

### Health Check Automático

O Docker verifica automaticamente se a API está saudável:

```bash
# Ver status do health check
docker-compose ps

# Output esperado:
# admcloud-api          Up 2 minutes (healthy)
# mysql                 Up 2 minutes (healthy)
```

### Acessar Logs de Erro

```bash
# Logs do Apache
docker-compose exec admcloud-api tail -f /var/log/apache2/error.log

# Logs da aplicação
docker-compose exec admcloud-api tail -f /var/www/html/application/logs/*

# Logs do MySQL
docker-compose logs -f mysql
```

---

## 🔐 Segurança - Próximos Passos

### ⚠️ Antes de ir para Produção

1. **Mudar Senhas Padrão**

   ```bash
   # Editar .env
   DB_PASSWORD=mudar_para_senha_forte
   MYSQL_PASSWORD=mudar_para_senha_forte
   ```

2. **Habilitar HTTPS**

   ```bash
   # Descomentar em docker-compose.yml
   - "443:443"

   # Adicionar certificado SSL
   COPY docker/ssl /etc/apache2/ssl
   ```

3. **Restringir Acesso ao phpMyAdmin**

   ```yaml
   # Em docker-compose.yml, adicionar:
   environment:
     PMA_HOST: mysql
     PMA_PMADB: phpmyadmin
   ```

4. **Habilitar Firewall**
   ```bash
   # Apenas permitir portas necessárias
   docker network inspect admcloud-network
   ```

---

## 📈 Escalar para Produção

### Usar Docker Swarm (para múltiplas máquinas)

```bash
# Inicializar Swarm
docker swarm init

# Deploy stack
docker stack deploy -c docker-compose.yml admcloud
```

### Usar Kubernetes (para alta disponibilidade)

```bash
# Converter docker-compose para Kubernetes
kompose convert -f docker-compose.yml

# Deploy no Kubernetes
kubectl apply -f .
```

---

## 🐛 Troubleshooting

### Problema: "Connection refused" ao tentar conectar ao MySQL

```bash
# Verificar se MySQL está rodando
docker-compose ps

# Se não estiver, ver logs do MySQL
docker-compose logs mysql

# Reiniciar MySQL
docker-compose restart mysql

# Aguardar health check passar (pode levar alguns segundos)
docker-compose ps
```

### Problema: "Port 8080 already in use"

```bash
# Ver qual processo está usando a porta
lsof -i :8080

# Ou em Windows:
netstat -ano | findstr :8080

# Mudar porta em docker-compose.yml:
# ports:
#   - "8888:80"  # Usar 8888 em vez de 8080
```

### Problema: "Permission denied" ao acessar volumes

```bash
# Corrigir permissões dentro do container
docker-compose exec admcloud-api chown -R www-data:www-data /var/www/html
docker-compose exec admcloud-api chmod -R 755 application/logs
```

### Problema: Composer timeout no build

```bash
# Aumentar timeout no Dockerfile:
# RUN composer install --no-dev --optimize-autoloader --no-timeout
```

---

## 📝 Variáveis de Ambiente

As principais variáveis estão em `.env.example`. Copie para `.env` e ajuste:

```bash
# Banco de Dados
DB_HOST=mysql              # Nome do serviço no docker-compose.yml
DB_NAME=papion
DB_USER=papion
DB_PASSWORD=Pap10nL4vrAs2024

# Aplicação
APP_ENV=production
APP_DEBUG=false
APP_BASE_URL=http://localhost:8080

# PHP
PHP_MEMORY_LIMIT=256M
PHP_MAX_EXECUTION_TIME=300
```

---

## 🚀 Deployment em Servidor Linux

### 1. Transferir arquivos para servidor

```bash
scp -r admcloud user@seu-servidor.com:/home/user/
```

### 2. SSH para o servidor

```bash
ssh user@seu-servidor.com
cd ~/admcloud
```

### 3. Subir containers

```bash
docker-compose up -d --build

# Verificar status
docker-compose ps
```

### 4. Configurar reverse proxy (Nginx/Apache)

```nginx
# /etc/nginx/sites-available/admcloud
upstream api_backend {
    server localhost:8080;
}

server {
    listen 80;
    server_name admcloud.papion.com.br;

    location / {
        proxy_pass http://api_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

---

## ✅ Checklist - Docker Pronto para Usar

- [ ] Docker e Docker Compose instalados
- [ ] Arquivo `.env` criado (cópia de `.env.example`)
- [ ] `docker-compose up -d` executado com sucesso
- [ ] `docker-compose ps` mostra containers "healthy"
- [ ] Teste GET /passport respondendo com sucesso
- [ ] phpMyAdmin acessível em http://localhost:8081
- [ ] Senhas padrão alteradas (se for usar em produção)
- [ ] HTTPS configurado (se necessário)
- [ ] Backups do banco de dados configurados

---

## 📞 Suporte

Para problemas com Docker:

- Documentação oficial: https://docs.docker.com/
- Docker Compose: https://docs.docker.com/compose/
- Verificar logs: `docker-compose logs -f`

Para problemas com a API:

- Ver arquivo `TABELAS_MINIMAS_API.md`
- Verificar estrutura de banco de dados no phpMyAdmin
- Testes em `EXEMPLOS_INTEGRACAO_PASSPORT.md`

---

**Docker setup completo! 🐳 Agora você tem a API rodando em container.** 🚀
