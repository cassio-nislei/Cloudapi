# 📋 Fluxo: PC Local → Git → VPS → Docker

**Status:** ✅ PRONTO PARA DEPLOY REMOTO

---

## 🎯 O Que Fazer (Em Ordem)

### 📱 Seu PC Local (Windows)

```
1️⃣  Você JÁ TEM PRONTO:
   ✅ Dockerfile
   ✅ docker-compose.yml
   ✅ .env.example
   ✅ .gitignore
   ✅ Todos os arquivos da API
   ✅ Scripts deploy.bat e deploy.sh

2️⃣  Você PRECISA FAZER:
   - Abrir Git Bash ou PowerShell
   - cd c:\Users\nislei\Desktop\DLL\admcloud
   - git init
   - git add .
   - git commit -m "ADMCloud API v1.0 com Docker"
   - git remote add origin https://github.com/SEU_USUARIO/admcloud.git
   - git push -u origin main
```

---

### 🖥️ VPS Remoto (Linux)

```
1️⃣  SETUP DOCKER (uma única vez):
   ssh usuario@seu-vps.com.br

   sudo apt-get update
   sudo apt-get install -y docker.io docker-compose
   sudo usermod -aG docker $USER

   docker --version  # Verificar

2️⃣  CLONAR E SUBIR:
   mkdir -p /home/usuario/projetos
   cd /home/usuario/projetos

   git clone https://github.com/SEU_USUARIO/admcloud.git
   cd admcloud

   cp .env.example .env
   nano .env  # Editar com dados de produção

   docker-compose up -d
   docker-compose ps  # Verificar status

3️⃣  TESTAR:
   curl "http://localhost:8080/api/v1/passport?cgc=01611275000205&hostname=VPS&guid=550e8400-e29b-41d4-a716-446655440000"
```

---

## 📦 Arquivos Criados Neste Setup

```
✅ Dockerfile                    (Imagem PHP 8.2 + Apache)
✅ docker-compose.yml            (Orquestra API + MySQL + phpMyAdmin)
✅ .dockerignore                 (Exclusões do build)
✅ .env.example                  (Variáveis de ambiente padrão)
✅ .gitignore                    (Não commita .env real, vendor/, logs/)
✅ deploy.sh                     (Script para Linux/Mac)
✅ deploy.bat                    (Script para Windows)
✅ docker/apache.conf            (Configuração Apache com rewrite)
✅ docker/init.sql               (SQL de inicialização com dados teste)
✅ DOCKER_SETUP.md               (Documentação completa)
✅ DOCKER_QUICKSTART.md          (Quick start 5 minutos)
✅ DOCKER_CHECKLIST.md           (Verificação pré/pós deploy)
✅ DOCKER_SUMMARY.md             (Resumo visual)
✅ GIT_VPS_DOCKER.md             (Este guia!)
```

---

## 🌐 Fluxo Visual

```
┌─────────────────────────────────────────────────────────┐
│  SEU PC LOCAL (Windows)                                 │
│  ✅ Dockerfile                                          │
│  ✅ docker-compose.yml                                  │
│  ✅ .env.example                                        │
│  ✅ application/ (código PHP)                           │
│  ✅ .gitignore                                          │
└────────────────────┬────────────────────────────────────┘
                     │
                  git init
                  git add .
                  git commit
                  git push
                     │
┌────────────────────▼────────────────────────────────────┐
│  GITHUB/GITLAB (Cloud)                                  │
│  📁 seu-usuario/admcloud                                │
│  └── Repositório sincronizado                           │
└────────────────────┬────────────────────────────────────┘
                     │
                  git clone
                  https://github.com/seu-usuario/admcloud
                     │
┌────────────────────▼────────────────────────────────────┐
│  VPS LINUX (seu-vps.com.br)                             │
│  ✅ Docker instalado                                    │
│  ✅ Projeto clonado                                     │
│  ✅ .env criado (com dados de produção)                 │
│  ✅ docker-compose up -d                                │
└────────────────────┬────────────────────────────────────┘
                     │
              docker-compose up -d
              3 containers rodando:
              - admcloud-api:8080
              - mysql:3306
              - phpmyadmin:8081
                     │
┌────────────────────▼────────────────────────────────────┐
│  API RODANDO EM PRODUÇÃO                                │
│  🚀 http://admcloud.papion.com.br (com HTTPS)           │
│  ✅ GET /passport funcionando                           │
│  ✅ POST /registro funcionando                          │
│  ✅ Banco de dados persistente                          │
└─────────────────────────────────────────────────────────┘
```

---

## ⚡ Quick Commands

### Local (Windows PowerShell)

```powershell
# Inicializar Git
git config --global user.name "Seu Nome"
git config --global user.email "seu@email.com"

# Ir para projeto
cd c:\Users\nislei\Desktop\DLL\admcloud

# Criar .env local (não vai para Git)
copy .env.example .env

# Adicionar e commitar
git init
git add .
git commit -m "ADMCloud API com Docker setup"

# Conectar ao GitHub
git remote add origin https://github.com/SEU_USUARIO/admcloud.git
git branch -M main
git push -u origin main

# Verificar
git remote -v
git log --oneline
```

### VPS (Terminal SSH)

```bash
# Conectar ao VPS
ssh usuario@seu-vps.com.br

# Instalar Docker (primeira vez)
sudo apt-get update && sudo apt-get install -y docker.io docker-compose
sudo usermod -aG docker $USER
docker --version

# Clonar projeto
mkdir -p ~/projetos
cd ~/projetos
git clone https://github.com/SEU_USUARIO/admcloud.git
cd admcloud

# Preparar ambiente
cp .env.example .env
nano .env  # Editar com dados REAIS de produção

# Subir
docker-compose up -d
docker-compose ps

# Testar
curl "http://localhost:8080/api/v1/passport?cgc=01611275000205&hostname=VPS&guid=550e8400-e29b-41d4-a716-446655440000"

# Monitorar
docker-compose logs -f admcloud-api
```

---

## 🔑 Pontos Importantes

### ✅ O QUE SERÁ ENVIADO AO GIT

```
Dockerfile
docker-compose.yml
.dockerignore
.gitignore
.env.example           ← Sem dados sensíveis!
deploy.sh
deploy.bat
docker/apache.conf
docker/init.sql
application/           ← Código da API
assets/
vendor/
index.php
... todos os arquivos
```

### ❌ O QUE NÃO SERÁ ENVIADO AO GIT

```
.env                   ← Dados sensíveis (local)
.env.production        ← Dados sensíveis (produção)
vendor/composer.lock   ← Dependências
application/logs/*     ← Logs locais
uploads/*              ← Uploads temporários
node_modules/          ← Node packages
*.log                  ← Arquivos de log
.DS_Store              ← Mac específico
```

---

## 📊 Variáveis .env

### Local (seu PC)

```env
APP_ENV=development
APP_DEBUG=true
APP_BASE_URL=http://localhost:8080
DB_HOST=mysql
DB_NAME=papion
DB_USER=papion
DB_PASSWORD=Pap10nL4vrAs2024
```

### Produção (VPS)

```env
APP_ENV=production
APP_DEBUG=false
APP_BASE_URL=https://admcloud.papion.com.br
DB_HOST=mysql
DB_NAME=papion_prod
DB_USER=papion_prod
DB_PASSWORD=SenhaForteDiferente123!@#
PHP_MEMORY_LIMIT=512M
PHP_MAX_EXECUTION_TIME=300
```

---

## 🚀 Próximas Etapas (Produção)

1. **HTTPS/SSL**

   ```bash
   sudo apt-get install -y nginx certbot python3-certbot-nginx
   sudo certbot certonly --nginx -d admcloud.papion.com.br
   # Configurar Nginx como reverse proxy
   ```

2. **Backup Automático**

   ```bash
   # Script cron para backup diário
   crontab -e
   # 0 2 * * * docker-compose exec -T mysql mysqldump -u papion_prod -p papion_prod papion_prod > /backups/backup_$(date +\%Y\%m\%d).sql
   ```

3. **Monitoramento**

   ```bash
   docker stats
   docker-compose logs -f
   # Ou setup ELK stack / Prometheus+Grafana
   ```

4. **CI/CD (Opcional)**
   ```bash
   # GitHub Actions para deploy automático na cada push
   # .github/workflows/deploy.yml
   ```

---

## 🎁 Você Recebeu

### Arquivos Docker Prontos

✅ Dockerfile configurado  
✅ docker-compose.yml com 3 serviços  
✅ Nginx/Apache configurado  
✅ MySQL com dados iniciais  
✅ phpMyAdmin incluído  
✅ Health checks automáticos  
✅ Volumes persistentes

### Scripts de Deploy

✅ deploy.bat para Windows  
✅ deploy.sh para Linux/Mac

### Documentação Completa

✅ DOCKER_SETUP.md (guia completo)  
✅ DOCKER_QUICKSTART.md (5 minutos)  
✅ DOCKER_CHECKLIST.md (verificação)  
✅ DOCKER_SUMMARY.md (resumo visual)  
✅ GIT_VPS_DOCKER.md (este guia!)

### Banco de Dados Pronto

✅ PESSOAS (clientes)  
✅ PESSOA_LICENCAS (dispositivos)  
✅ Dados de teste inclusos  
✅ Índices otimizados  
✅ Foreign keys configuradas

---

## ⏱️ Tempo Total Esperado

| Tarefa                    | Tempo       |
| ------------------------- | ----------- |
| Preparar Git local        | 5 min       |
| Push para GitHub          | 2 min       |
| Instalar Docker no VPS    | 10 min      |
| Clonar e subir containers | 5 min       |
| Configurar HTTPS          | 10 min      |
| **Total**                 | **~30 min** |

---

## 📞 Em Caso de Dúvidas

### Git

- Documentação: https://git-scm.com/doc
- GitHub Help: https://docs.github.com

### Docker

- Documentação: https://docs.docker.com
- Troubleshooting: `docker-compose logs -f`

### Linux/VPS

- SSH: `ssh usuario@seu-vps.com.br`
- Logs: `sudo tail -f /var/log/syslog`
- Help: `man comando`

---

## ✅ Checklist Final

- [ ] Repositório GitHub/GitLab criado
- [ ] Projeto commitado localmente
- [ ] Push feito com sucesso
- [ ] VPS tem Docker instalado
- [ ] Projeto clonado no VPS
- [ ] `.env` criado no VPS com dados de produção
- [ ] `docker-compose up -d` executado
- [ ] `docker-compose ps` mostra "healthy"
- [ ] API respondendo em http://seu-vps.com.br:8080
- [ ] HTTPS configurado (opcional mas recomendado)

---

## 🎉 Conclusão

Você tem tudo pronto para:

1. ✅ **Fazer push para Git** do seu PC
2. ✅ **Clonar no VPS** com um único comando
3. ✅ **Subir a API em Docker** em menos de 5 minutos
4. ✅ **Acessar em produção** com HTTPS configurado

**Sua API está 100% containerizada e pronta para escala! 🚀**

---

**Criado:** 22 de Dezembro de 2025  
**Versão:** 1.0.0  
**Status:** ✅ PRONTO PARA DEPLOY
