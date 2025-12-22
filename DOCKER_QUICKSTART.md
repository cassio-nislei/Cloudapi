# 🐳 Docker Setup - Resumo Rápido

**Data:** 22 de Dezembro de 2025  
**Status:** ✅ Pronto para Usar

---

## 📦 Arquivos Criados

```
admcloud/
├── Dockerfile                    # Imagem Docker da API
├── docker-compose.yml            # Orquestração de 3 serviços
├── .dockerignore                 # Arquivos ignorados no build
├── .env.example                  # Variáveis de ambiente
├── deploy.sh                     # Script Linux/Mac
├── deploy.bat                    # Script Windows
├── DOCKER_SETUP.md              # Documentação completa
└── docker/
    ├── apache.conf              # Configuração do Apache
    └── init.sql                 # Script SQL de inicialização
```

---

## 🚀 Como Subir a API em Docker?

### Windows

```bash
# 1. Copiar arquivo de ambiente
copy .env.example .env

# 2. Subir containers
deploy.bat start

# 3. Verificar se está rodando
deploy.bat status
```

### Linux/Mac

```bash
# 1. Copiar arquivo de ambiente
cp .env.example .env

# 2. Dar permissão ao script
chmod +x deploy.sh

# 3. Subir containers
./deploy.sh start

# 4. Verificar se está rodando
./deploy.sh status
```

### Direto com Docker Compose

```bash
docker-compose up -d
```

---

## 🌐 Acessar os Serviços

| Serviço        | URL                   | Usuário | Senha            |
| -------------- | --------------------- | ------- | ---------------- |
| **API**        | http://localhost:8080 | -       | -                |
| **phpMyAdmin** | http://localhost:8081 | papion  | Pap10nL4vrAs2024 |
| **MySQL**      | localhost:3306        | papion  | Pap10nL4vrAs2024 |

---

## ✅ Teste Rápido

### Usar script

```bash
# Windows
deploy.bat test

# Linux/Mac
./deploy.sh test
```

### Manualmente

```bash
curl "http://localhost:8080/api/v1/passport?cgc=01611275000205&hostname=DOCKER-TEST&guid=550e8400-e29b-41d4-a716-446655440000"
```

**Resposta esperada:**

```json
{
  "Status": true,
  "Mensagem": "Passport OK!",
  "Dados": { ... }
}
```

---

## 📊 Serviços Inclusos

### 1. **admcloud-api** (PHP 8.2 + Apache)

- Port: 8080
- Health check a cada 30s
- Volumes: logs, cache, uploads

### 2. **mysql** (MySQL 8.0)

- Port: 3306
- Database: papion
- Dados persistentes em volume

### 3. **phpmyadmin** (Gerenciador DB)

- Port: 8081
- Acesso imediato ao banco de dados

---

## 🛠️ Comandos Úteis

### Gerenciar Containers

```bash
# Ver status
docker-compose ps

# Parar
docker-compose stop

# Reiniciar
docker-compose restart

# Deletar tudo
docker-compose down -v
```

### Ver Logs

```bash
# Todos os serviços
docker-compose logs -f

# Serviço específico
docker-compose logs -f admcloud-api
docker-compose logs -f mysql
```

### Acessar Shell

```bash
# Shell da API
docker-compose exec admcloud-api bash

# MySQL
docker-compose exec mysql bash
```

---

## 🔐 Segurança para Produção

1. **Mudar senhas padrão** em `.env`
2. **Habilitar HTTPS** com certificado SSL
3. **Restringir acesso ao phpMyAdmin**
4. **Usar variáveis de ambiente** para dados sensíveis
5. **Configurar backup automático** do banco

Ver `DOCKER_SETUP.md` para detalhes completos.

---

## 📋 Checklist

- [ ] Docker e Docker Compose instalados
- [ ] `.env` criado (cópia de `.env.example`)
- [ ] `deploy.bat start` ou `./deploy.sh start` executado
- [ ] `docker-compose ps` mostra "healthy"
- [ ] Teste GET /passport funcionando
- [ ] phpMyAdmin acessível
- [ ] Banco de dados com dados de teste

---

## 📞 Arquivos de Referência

- **DOCKER_SETUP.md** - Documentação completa do Docker
- **TABELAS_MINIMAS_API.md** - Estrutura do banco de dados
- **EXEMPLOS_INTEGRACAO_PASSPORT.md** - Testes de integração

---

**Docker setup pronto! A API está 100% containerizada e pronta para produção.** 🚀

Próximos passos:

1. Subir containers com `deploy.bat start` ou `./deploy.sh start`
2. Testar endpoints
3. Configurar HTTPS para produção
4. Setup de backups automáticos
