# 🚀 Guia: Git → VPS → Docker

**Data:** 22 de Dezembro de 2025  
**Status:** ✅ Pronto para Git Push

---

## 📋 Passo 1: Preparar Projeto Local para Git

### 1.1 Inicializar Git (se ainda não fez)

```bash
cd c:\Users\nislei\Desktop\DLL\admcloud

# Inicializar repositório
git init

# Verificar status
git status
```

### 1.2 Verificar .gitignore

Verifique se o arquivo `.gitignore` contém:

```
.env           # Não enviar arquivo de produção
vendor/        # Não enviar dependências
node_modules/
application/logs/*
uploads/*
```

**✅ Arquivo `.gitignore` já foi criado!**

### 1.3 Configurar Git (primeiro uso)

```bash
# Configurar usuário
git config --global user.name "Seu Nome"
git config --global user.email "seu.email@example.com"

# Verificar
git config --list
```

---

## 📦 Passo 2: Fazer Commit e Push para GitHub/GitLab

### 2.1 Preparar Arquivo .env para Git

```bash
# Criar .env local (não será commitado)
copy .env.example .env

# Editar .env com dados locais
# DB_PASSWORD=sua_senha_local
# APP_BASE_URL=http://localhost:8080
```

**⚠️ IMPORTANTE:** Nunca committe o arquivo `.env` real!

### 2.2 Fazer Commit

```bash
# Adicionar todos os arquivos
git add .

# Verificar o que será commitado
git status

# Fazer commit
git commit -m "Initial commit: ADMCloud API com Docker setup"

# Ver histórico
git log --oneline
```

### 2.3 Conectar ao Repositório Remoto

#### GitHub

```bash
# Criar repositório em https://github.com/new

# Adicionar remote
git remote add origin https://github.com/seu-usuario/admcloud.git

# Fazer push (primeira vez)
git branch -M main
git push -u origin main
```

#### GitLab

```bash
# Criar repositório em https://gitlab.com/projects/new

# Adicionar remote
git remote add origin https://gitlab.com/seu-usuario/admcloud.git

# Fazer push
git push -u origin main
```

#### Verificar Remote

```bash
git remote -v

# Output esperado:
# origin  https://github.com/seu-usuario/admcloud.git (fetch)
# origin  https://github.com/seu-usuario/admcloud.git (push)
```

---

## 🖥️ Passo 3: Preparar VPS para Docker

### 3.1 Pré-requisitos no VPS

Seu VPS precisa ter:

- Linux (Ubuntu 20.04 LTS ou superior recomendado)
- SSH acesso
- 4GB RAM mínimo
- 20GB espaço em disco

### 3.2 Instalar Docker no VPS

#### Ubuntu/Debian

```bash
# Atualizar repositórios
sudo apt-get update
sudo apt-get upgrade -y

# Instalar Docker
sudo apt-get install -y docker.io docker-compose

# Adicionar seu usuário ao grupo docker (para não usar sudo)
sudo usermod -aG docker $USER
newgrp docker

# Verificar instalação
docker --version
docker-compose --version

# Teste rápido
docker run hello-world
```

#### CentOS/RHEL

```bash
# Instalar Docker
sudo yum install -y docker docker-compose

# Iniciar serviço
sudo systemctl start docker
sudo systemctl enable docker

# Verificar
docker --version
```

#### Verificar Instalação

```bash
docker ps
docker-compose --version
```

---

## 📥 Passo 4: Clonar Projeto no VPS

### 4.1 SSH para o VPS

```bash
# Windows (PowerShell)
ssh usuario@seu-vps.com.br
# ou
ssh -i "C:\caminho\chave_privada.pem" usuario@seu-vps.com.br

# Linux/Mac
ssh usuario@seu-vps.com.br
```

### 4.2 Clonar Repositório

```bash
# Criar diretório de trabalho
mkdir -p /home/usuario/projetos
cd /home/usuario/projetos

# Clonar repositório
git clone https://github.com/seu-usuario/admcloud.git
cd admcloud

# Verificar conteúdo
ls -la
```

### 4.3 Criar .env no VPS

```bash
# Copiar exemplo
cp .env.example .env

# Editar .env para produção
nano .env
```

**Editar valores para PRODUÇÃO:**

```env
# Banco de Dados
DB_HOST=mysql
DB_NAME=papion_prod
DB_USER=papion_prod
DB_PASSWORD=SenhaForte123!@#

# Aplicação
APP_ENV=production
APP_DEBUG=false
APP_BASE_URL=https://admcloud.papion.com.br

# PHP
PHP_MEMORY_LIMIT=512M
PHP_MAX_EXECUTION_TIME=300
```

**Salvar:** Ctrl+X, Y, Enter

---

## 🐳 Passo 5: Subir Docker no VPS

### 5.1 Iniciar Containers

```bash
# Ir para diretório do projeto
cd /home/usuario/projetos/admcloud

# Subir containers
docker-compose up -d

# Verificar status
docker-compose ps

# Output esperado:
# NAME            STATUS
# admcloud-api    Up X seconds (healthy)
# mysql           Up X seconds (healthy)
# phpmyadmin      Up X seconds (healthy)
```

### 5.2 Verificar Logs

```bash
# Logs da API
docker-compose logs -f admcloud-api

# Logs do MySQL
docker-compose logs -f mysql

# Parar de ver logs: Ctrl+C
```

### 5.3 Testar API

```bash
# Teste local no VPS
curl "http://localhost:8080/api/v1/passport?cgc=01611275000205&hostname=VPS-TEST&guid=550e8400-e29b-41d4-a716-446655440000"

# Resposta esperada:
# {"Status":true,"Mensagem":"Passport OK!","Dados":{...}}
```

---

## 🌐 Passo 6: Configurar Acesso Externo

### 6.1 Verificar Portas no Firewall

```bash
# UFW (Ubuntu)
sudo ufw allow 80
sudo ufw allow 443
sudo ufw allow 3306

# Verificar regras
sudo ufw status

# Se não estiver ativo
sudo ufw enable
```

### 6.2 Testar Acesso Externo

```bash
# Do seu PC local
curl "http://seu-vps.com.br:8080/api/v1/passport?cgc=01611275000205&hostname=VPS-TEST&guid=550e8400-e29b-41d4-a716-446655440000"

# Ou pelo navegador
# http://seu-vps.com.br:8080
```

---

## 🔒 Passo 7: Configurar HTTPS (Produção)

### 7.1 Instalar Nginx como Reverse Proxy

```bash
sudo apt-get install -y nginx

# Iniciar
sudo systemctl start nginx
sudo systemctl enable nginx

# Verificar
sudo systemctl status nginx
```

### 7.2 Criar Config do Nginx

```bash
# Criar arquivo de configuração
sudo nano /etc/nginx/sites-available/admcloud

# Copiar conteúdo:
```

```nginx
upstream api_backend {
    server localhost:8080;
}

server {
    listen 80;
    server_name admcloud.papion.com.br;

    # Redirecionar HTTP para HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name admcloud.papion.com.br;

    # Certificados SSL
    ssl_certificate /etc/letsencrypt/live/admcloud.papion.com.br/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/admcloud.papion.com.br/privkey.pem;

    # Configurações SSL
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    location / {
        proxy_pass http://api_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # phpMyAdmin
    location /phpmyadmin {
        return 403;  # Blocar acesso público
    }
}
```

### 7.3 Instalar SSL Let's Encrypt

```bash
# Instalar certbot
sudo apt-get install -y certbot python3-certbot-nginx

# Gerar certificado
sudo certbot certonly --nginx -d admcloud.papion.com.br

# Verificar
sudo ls -la /etc/letsencrypt/live/admcloud.papion.com.br/
```

### 7.4 Habilitar Nginx Config

```bash
# Habilitar site
sudo ln -s /etc/nginx/sites-available/admcloud /etc/nginx/sites-enabled/

# Testar config
sudo nginx -t

# Recarregar Nginx
sudo systemctl reload nginx
```

---

## 📊 Passo 8: Monitoramento e Manutenção

### 8.1 Ver Status dos Containers

```bash
# Status
docker-compose ps

# Recursos utilizados
docker stats

# Informações do sistema
docker system df
```

### 8.2 Ver Logs em Tempo Real

```bash
# Todos os serviços
docker-compose logs -f

# Serviço específico
docker-compose logs -f admcloud-api
docker-compose logs -f mysql
```

### 8.3 Backup do Banco de Dados

```bash
# Fazer backup
docker-compose exec -T mysql mysqldump -u papion_prod -p papion_prod > backup_$(date +%Y%m%d_%H%M%S).sql

# Restaurar
docker-compose exec -T mysql mysql -u papion_prod -p papion_prod < backup_20241222_120000.sql
```

### 8.4 Atualizar Código

```bash
# SSH no VPS
ssh usuario@seu-vps.com.br
cd /home/usuario/projetos/admcloud

# Puxar atualizações
git pull origin main

# Reconstruir containers se mudou Dockerfile
docker-compose up -d --build

# Verificar status
docker-compose ps
```

---

## 🔄 Passo 9: CI/CD com GitHub Actions (Opcional)

### 9.1 Criar Workflow

```bash
# No repositório local
mkdir -p .github/workflows
nano .github/workflows/deploy.yml
```

```yaml
name: Deploy to VPS

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v2

      - name: Deploy to VPS
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.VPS_HOST }}
          username: ${{ secrets.VPS_USER }}
          key: ${{ secrets.VPS_SSH_KEY }}
          script: |
            cd /home/usuario/projetos/admcloud
            git pull origin main
            docker-compose up -d --build
            docker-compose exec -T admcloud-api composer install
```

### 9.2 Configurar Secrets no GitHub

1. Ir para: Settings > Secrets and variables > Actions
2. Adicionar:
   - `VPS_HOST` = seu-vps.com.br
   - `VPS_USER` = seu_usuario
   - `VPS_SSH_KEY` = conteúdo da chave privada SSH

---

## 🛠️ Comandos Úteis no VPS

```bash
# Ver informações do sistema
hostnamectl
uname -a

# Espaço em disco
df -h

# Memória
free -h

# Processos
ps aux | grep docker

# Restart serviços
docker-compose restart
sudo systemctl restart nginx

# Ver portas abertas
sudo netstat -tuln | grep LISTEN

# Logs do sistema
sudo tail -f /var/log/syslog
```

---

## 📋 Checklist Completo

### Local (antes de fazer push)

- [ ] `.env.example` criado
- [ ] `.gitignore` configurado
- [ ] Git inicializado
- [ ] Todos os arquivos Docker criados
- [ ] Commit feito com mensagem clara
- [ ] Remote configurado (GitHub/GitLab)

### Git

- [ ] Repositório criado em GitHub/GitLab
- [ ] Push feito com sucesso
- [ ] Branches estão sincronizados
- [ ] README visualizável

### VPS Preparação

- [ ] VPS alugado e acessível
- [ ] SSH funcionando
- [ ] Docker instalado
- [ ] Docker Compose funcionando
- [ ] Firewall configurado

### VPS Deployment

- [ ] Repositório clonado
- [ ] `.env` criado com dados de produção
- [ ] `docker-compose up -d` executado
- [ ] Containers em status "healthy"
- [ ] API respondendo corretamente
- [ ] MySQL com dados de teste

### Produção

- [ ] Nginx instalado e configurado
- [ ] SSL/HTTPS ativo
- [ ] Backup automático do banco
- [ ] Monitoramento ativo
- [ ] Logs centralizados (opcional)

---

## 🆘 Troubleshooting

### Erro: "Permission denied" ao clonar

```bash
# Usar HTTPS em vez de SSH
git clone https://github.com/seu-usuario/admcloud.git

# Ou configurar SSH key no GitHub
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa
cat ~/.ssh/id_rsa.pub  # Copiar para GitHub
```

### Erro: "Connection refused" do MySQL

```bash
# Verificar se MySQL está rodando
docker-compose ps

# Ver logs
docker-compose logs mysql

# Reiniciar
docker-compose restart mysql
sleep 10
docker-compose ps
```

### Erro: "Porta 8080 já em uso"

```bash
# Ver qual processo está usando
sudo netstat -tuln | grep 8080

# Mudar porta em docker-compose.yml
# ports:
#   - "8888:80"
```

### API respondendo com erro 500

```bash
# Ver logs completos
docker-compose logs -f admcloud-api

# Verificar permissões
docker-compose exec admcloud-api ls -la application/logs/

# Corrigir
docker-compose exec admcloud-api chown -R www-data:www-data application/
```

---

## 📞 Referências Rápidas

**Git:**

- Docs: https://git-scm.com/doc
- GitHub: https://github.com
- GitLab: https://gitlab.com

**Docker:**

- Docs: https://docs.docker.com
- Hub: https://hub.docker.com

**VPS/Linux:**

- Ubuntu: https://ubuntu.com
- SSH: https://man7.org/linux/man-pages/man1/ssh.1.html
- Nginx: https://nginx.org/en/docs/

**SSL/HTTPS:**

- Let's Encrypt: https://letsencrypt.org
- Certbot: https://certbot.eff.org

---

## 🎯 Resumo do Fluxo

```
┌──────────────────────────────────────────────────────┐
│ 1. LOCAL: Preparar Git                               │
│    - .gitignore configurado                          │
│    - .env.example criado                             │
│    - git commit & git push                           │
└──────────────┬───────────────────────────────────────┘
               │
┌──────────────▼───────────────────────────────────────┐
│ 2. GITHUB/GITLAB: Repositório Online                │
│    - Código sincronizado                             │
│    - SSH keys configuradas                           │
└──────────────┬───────────────────────────────────────┘
               │
┌──────────────▼───────────────────────────────────────┐
│ 3. VPS: Clonar e Subir                              │
│    - git clone                                       │
│    - docker-compose up -d                            │
│    - Containers rodando (healthy)                    │
└──────────────┬───────────────────────────────────────┘
               │
┌──────────────▼───────────────────────────────────────┐
│ 4. PRODUÇÃO: Configurar Acesso                      │
│    - Nginx + SSL                                     │
│    - Firewall                                        │
│    - Monitoramento                                   │
└──────────────────────────────────────────────────────┘
```

---

**Você está pronto! 🚀 Seu projeto está 100% pronto para Git → VPS → Docker**

Próximas ações:

1. ✅ Fazer `git push` do seu PC local
2. ✅ Clonar no VPS com `git clone`
3. ✅ Subir com `docker-compose up -d`
4. ✅ Configurar HTTPS com Nginx

Tudo automatizado e pronto para produção! 🎉
