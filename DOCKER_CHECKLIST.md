# ✅ Docker - Checklist de Implementação

**Data:** 22 de Dezembro de 2025  
**Status:** Verificação pré-deploy

---

## 📋 Antes de Iniciar os Containers

### Sistema Operacional

- [ ] Windows 10+ com WSL 2 OU Linux OU Mac
- [ ] Docker Desktop instalado e rodando
- [ ] Docker Compose v1.29+ instalado
- [ ] 4GB RAM mínimo disponível para containers
- [ ] 5GB espaço em disco livre

### Verificar Instalação

```bash
# Windows (PowerShell)
docker --version
docker-compose --version
docker info

# Linux/Mac
docker --version
docker-compose --version
docker info
```

---

## 🔧 Preparação do Projeto

### 1. Arquivos Criados

- [ ] `Dockerfile` - Imagem da API
- [ ] `docker-compose.yml` - Orquestração
- [ ] `.dockerignore` - Exclusões do build
- [ ] `.env.example` - Variáveis de exemplo
- [ ] `.gitignore` - Arquivos Git ignorados
- [ ] `docker/apache.conf` - Config Apache
- [ ] `docker/init.sql` - SQL inicial
- [ ] `deploy.sh` - Script Linux/Mac
- [ ] `deploy.bat` - Script Windows
- [ ] `DOCKER_SETUP.md` - Documentação
- [ ] `DOCKER_QUICKSTART.md` - Quick start

### 2. Verificar Estrutura

```bash
# Listar arquivos Docker
ls -la | grep -E "(Dockerfile|docker-compose|\.env|deploy\.|\.dockerignore)"
ls -la docker/
```

### 3. Criar .env

```bash
# Windows
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

- [ ] Arquivo `.env` criado
- [ ] Verificar credenciais BD em `.env`

---

## 🚀 Inicializar Containers

### Opção 1: Usar Script (Recomendado)

#### Windows

```bash
# CMD ou PowerShell
deploy.bat start

# Verificar
deploy.bat status
```

- [ ] Script `deploy.bat` executado com sucesso
- [ ] Status mostra "healthy"

#### Linux/Mac

```bash
# Dar permissão
chmod +x deploy.sh

# Executar
./deploy.sh start

# Verificar
./deploy.sh status
```

- [ ] Script `deploy.sh` executado com sucesso
- [ ] Status mostra "healthy"

### Opção 2: Docker Compose Direto

```bash
docker-compose up -d
docker-compose ps
```

- [ ] Containers inicializados
- [ ] Status: `Up X minutes (healthy)`

---

## 🧪 Testes Pós-Deploy

### 1. Health Check

```bash
# Windows
deploy.bat status

# Linux/Mac
./deploy.sh status

# Docker
docker-compose ps
```

Resultado esperado:

```
NAME              STATUS       PORTS
admcloud-api      Up 2m (healthy) 0.0.0.0:8080->80/tcp
mysql             Up 2m (healthy) 0.0.0.0:3306->3306/tcp
phpmyadmin        Up 2m          0.0.0.0:8081->80/tcp
```

- [ ] admcloud-api: `Up ... (healthy)`
- [ ] mysql: `Up ... (healthy)`
- [ ] phpmyadmin: `Up ...`

### 2. Acessar Serviços

#### API

```bash
curl http://localhost:8080
```

- [ ] Responde com página HTML do CodeIgniter

#### phpMyAdmin

```bash
# Abrir no navegador
http://localhost:8081
```

- [ ] Faça login: papion / Pap10nL4vrAs2024
- [ ] Vê banco "papion"
- [ ] Tabelas PESSOAS e PESSOA_LICENCAS existem

#### Verificar MySQL

```bash
# Windows
deploy.bat db

# Linux/Mac
./deploy.sh db
```

- [ ] Tabela PESSOAS com 1 registro
- [ ] Tabela PESSOA_LICENCAS com 1 registro

### 3. Testar Endpoints da API

#### GET /passport

```bash
# Windows
deploy.bat test

# Linux/Mac
./deploy.sh test

# Manualmente
curl "http://localhost:8080/api/v1/passport?cgc=01611275000205&hostname=DOCKER-TEST&guid=550e8400-e29b-41d4-a716-446655440000"
```

Resposta esperada (Status = true):

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

- [ ] GET /passport responde com Status = true
- [ ] Retorna dados do cliente de teste

#### GET /registro

```bash
curl -u "api_frontbox:api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg" \
  "http://localhost:8080/api/v1/registro"
```

Resposta esperada:

```json
{
  "status": "OK",
  "msg": "GET",
  "data": []
}
```

- [ ] GET /registro responde com status OK

### 4. Ver Logs

```bash
# Windows
deploy.bat logs admcloud-api

# Linux/Mac
./deploy.sh logs admcloud-api

# Docker
docker-compose logs -f admcloud-api
```

- [ ] Nenhum erro crítico nos logs
- [ ] Apache rodando normalmente

---

## 🔐 Configurações de Segurança

### Antes de Usar em Produção

- [ ] Mudar senha do MySQL em `.env`
- [ ] Mudar senha do phpMyAdmin
- [ ] Gerar novo API_KEY em `.env`
- [ ] Configurar HTTPS com certificado SSL
- [ ] Limitar acesso ao phpMyAdmin por IP
- [ ] Desabilitar debug mode (APP_DEBUG=false)
- [ ] Configurar backups automáticos
- [ ] Configurar logs centralizados

---

## 📊 Performance e Recursos

### Monitorar Uso

```bash
# Ver uso de disco
docker system df

# Ver uso de memória
docker stats

# Logs de performance
docker-compose logs mysql | grep "performance"
```

- [ ] Verificar uso de memória (< 2GB ideal)
- [ ] Verificar espaço em disco (> 1GB livre)
- [ ] MySQL respondendo rápido (< 100ms)

---

## 🆘 Troubleshooting

### Problema: Container não inicia

```bash
docker-compose logs admcloud-api
docker-compose down
docker-compose up --build
```

- [ ] Ver logs para erro específico
- [ ] Reconstruir imagem se necessário

### Problema: Erro 503 Service Unavailable

```bash
docker-compose restart admcloud-api
docker-compose ps
```

- [ ] Aguardar health check passar
- [ ] Verificar conexão com MySQL

### Problema: MySQL não conecta

```bash
docker-compose logs mysql
docker-compose exec mysql mysql -u root -proot123 -e "status"
```

- [ ] Verificar se MySQL está saudável
- [ ] Verificar variáveis de ambiente

### Problema: Port já em uso

```bash
# Ver qual processo usa a porta
# Windows
netstat -ano | findstr :8080

# Linux/Mac
lsof -i :8080

# Mudar porta em docker-compose.yml
```

- [ ] Mudar para porta diferente (ex: 8888)

---

## 📈 Próximos Passos - Produção

- [ ] Configurar DNS (admcloud.papion.com.br)
- [ ] Instalar certificado SSL
- [ ] Configurar reverse proxy (Nginx/Apache)
- [ ] Setup de backups diários
- [ ] Configurar logs centralizados (ELK/Splunk)
- [ ] Monitoramento com Prometheus/Grafana
- [ ] Rate limiting e DDoS protection
- [ ] Disaster recovery plan
- [ ] Política de senha forte
- [ ] 2FA para phpMyAdmin

---

## ✅ Assinatura de Conclusão

- [ ] Todos os itens acima marcados
- [ ] API respondendo normalmente
- [ ] Banco de dados acessível
- [ ] Logs limpos (sem erros)
- [ ] Documentação verificada

**Data de Verificação:** ******\_******  
**Responsável:** ******\_******  
**Status:** ☐ OK | ☐ Problemas | ☐ Pronto para Produção

---

## 📞 Referências

- **Docker Docs:** https://docs.docker.com
- **Docker Compose:** https://docs.docker.com/compose
- **CodeIgniter:** https://codeigniter.com
- **MySQL:** https://dev.mysql.com
- **phpMyAdmin:** https://www.phpmyadmin.net

---

**Checklist concluído! 🎉 Sua API está pronta em Docker.** 🚀
