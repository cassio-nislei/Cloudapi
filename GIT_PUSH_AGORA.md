# 📤 Guia Rápido: Git Push (PC Local → GitHub)

**Tempo estimado:** 10 minutos  
**Status:** Pronto agora

---

## 🎯 Você Vai Fazer Estas 3 Coisas

1. **Configurar Git** (primeira vez)
2. **Fazer Commit** dos arquivos Docker
3. **Fazer Push** para GitHub/GitLab

---

## Step 1️⃣: Configurar Git (Primeira Vez)

### Abra PowerShell ou CMD como Administrador

```powershell
# Copiar essas linhas e colar no PowerShell

# Configurar seu nome
git config --global user.name "Seu Nome Aqui"

# Configurar seu email
git config --global user.email "seu.email@gmail.com"

# Verificar configuração (opcional)
git config --list
```

**Pronto!** Git está configurado.

---

## Step 2️⃣: Preparar Projeto Local

### Ir para pasta do projeto

```powershell
# Copiar e colar:
cd c:\Users\nislei\Desktop\DLL\admcloud

# Verificar que está no diretório certo
pwd

# Ver arquivos (deve ver Dockerfile, docker-compose.yml, etc)
dir
```

### Criar arquivo .env local (não será enviado para Git)

```powershell
# Copiar arquivo de exemplo
copy .env.example .env

# Verificar que foi criado
ls -Name .env
```

---

## Step 3️⃣: Fazer Commit Local

### Inicializar repositório Git local

```powershell
# Apenas primeira vez
git init

# Adicionar TODOS os arquivos (exceto .gitignore)
git add .

# Verificar o que será adicionado
git status
```

**Output esperado:**

```
On branch master

No commits yet

Changes to be committed:
  new file:   Dockerfile
  new file:   docker-compose.yml
  new file:   .dockerignore
  new file:   .env.example
  new file:   .gitignore
  new file:   deploy.sh
  new file:   deploy.bat
  ... (muitos arquivos)
```

### Fazer Commit

```powershell
# Commitar com mensagem
git commit -m "Initial commit: ADMCloud API v1.0 com Docker setup"

# Verificar commit
git log --oneline
```

**Output esperado:**

```
abc1234 (HEAD -> master) Initial commit: ADMCloud API v1.0 com Docker setup
```

---

## Step 4️⃣: Criar Repositório no GitHub

### Ir para GitHub

1. Abrir: https://github.com/new
2. **Repository name:** `admcloud`
3. **Description:** `ADMCloud API - REST API com Docker`
4. **Visibility:** Public ou Private (escolher)
5. **Clicar:** Create repository

**Copiar a URL do repositório**, vai parecer com:

```
https://github.com/SEU_USUARIO/admcloud.git
```

---

## Step 5️⃣: Conectar Local ao GitHub

### De volta no PowerShell

```powershell
# IMPORTANTE: Mudar SEU_USUARIO pela sua conta GitHub!
# Copiar e adaptar:

git remote add origin https://github.com/SEU_USUARIO/admcloud.git

# Renomear branch para main
git branch -M main

# Fazer o push (enviará para GitHub)
git push -u origin main
```

**Se pedir username/senha:**

- Username: Seu usuário GitHub
- Password: Seu Personal Access Token (ver abaixo)

---

## 🔑 Se GitHub Pedir Autenticação

### Gerar Personal Access Token

1. Ir para: https://github.com/settings/tokens
2. Clicar: "Generate new token"
3. **Name:** Git Push
4. **Expiration:** 90 days
5. **Scopes:** Selecionar `repo` e `workflow`
6. Clicar: "Generate token"
7. **Copiar o token** (aparece uma única vez!)

### Usar o Token

```powershell
# Quando Git pedir "Password"
# Cole o token aqui (não é a senha normal!)

# Ou para não pedir novamente, salvar:
git config --global credential.helper store

# Fazer push novamente
git push -u origin main
```

---

## ✅ Verificar se Funcionou

### No PowerShell

```powershell
# Ver remote
git remote -v

# Output esperado:
# origin  https://github.com/SEU_USUARIO/admcloud.git (fetch)
# origin  https://github.com/SEU_USUARIO/admcloud.git (push)

# Ver branch
git branch -a

# Output esperado:
# * main
# remotes/origin/main
```

### No GitHub

1. Ir para: https://github.com/SEU_USUARIO/admcloud
2. Você deve ver:
   - Todos os arquivos listados
   - Branch `main` como padrão
   - Última mensagem de commit

---

## 🚀 Agora Pode Clonar no VPS

### SSH para seu VPS

```bash
ssh usuario@seu-vps.com.br

# Clonar projeto
cd ~
mkdir -p projetos
cd projetos

git clone https://github.com/SEU_USUARIO/admcloud.git
cd admcloud

# Verificar arquivos
ls -la
```

### Subir no Docker

```bash
# Copiar .env
cp .env.example .env

# Editar com dados de produção
nano .env

# Subir containers
docker-compose up -d

# Verificar
docker-compose ps
```

---

## 📋 Comandos Resumidos (Copiar/Colar)

### Primeira Vez (Seu PC)

```powershell
# 1. Configurar Git
git config --global user.name "Seu Nome"
git config --global user.email "seu.email@gmail.com"

# 2. Ir para projeto
cd c:\Users\nislei\Desktop\DLL\admcloud

# 3. Criar .env local
copy .env.example .env

# 4. Fazer commit
git init
git add .
git commit -m "Initial commit: ADMCloud API v1.0 com Docker"

# 5. Conectar ao GitHub (MUDAR SEU_USUARIO!)
git remote add origin https://github.com/SEU_USUARIO/admcloud.git
git branch -M main
git push -u origin main
```

### Depois (seu VPS)

```bash
# 1. SSH
ssh usuario@seu-vps.com.br

# 2. Preparar
mkdir -p ~/projetos
cd ~/projetos

# 3. Clonar (MUDAR SEU_USUARIO!)
git clone https://github.com/SEU_USUARIO/admcloud.git
cd admcloud

# 4. Setup
cp .env.example .env
nano .env  # Editar dados de produção

# 5. Docker
docker-compose up -d
docker-compose ps
```

---

## 🎯 Checklist do Git Push

- [ ] Git configurado com nome e email
- [ ] Você está na pasta `c:\...\admcloud`
- [ ] `.env` criado (cópia local de `.env.example`)
- [ ] `.gitignore` existe
- [ ] `git add .` executado
- [ ] `git commit -m "..."` feito
- [ ] Repositório criado no GitHub
- [ ] `git remote add origin ...` executado
- [ ] `git push -u origin main` sucedido
- [ ] Arquivos aparecem no GitHub online

---

## ❓ Dúvidas Frequentes

### P: Preciso ter GitHub account?

**R:** Sim, crie em https://github.com/signup

### P: Qual tipo de repositório criar?

**R:** Pode ser Public ou Private (você escolhe)

### P: E se eu cometer um erro?

**R:** Pode deletar o repositório e criar novamente

### P: Posso mudar o código depois?

**R:** Sim! Faz `git push` novamente

### P: Como atualizar o VPS com novo código?

**R:** No VPS: `git pull origin main` e `docker-compose up -d --build`

---

## 📚 Próximas Leituras

Depois de fazer push, leia:

1. `GIT_VPS_DOCKER.md` - Como clonar no VPS
2. `QUICK_DEPLOY.md` - Deploy rápido
3. `DOCKER_SETUP.md` - Setup completo

---

## 🆘 Se algo der Errado

### Erro: "fatal: not a git repository"

```powershell
git init
git add .
git commit -m "Initial commit"
```

### Erro: "remote origin already exists"

```powershell
git remote remove origin
git remote add origin https://github.com/SEU_USUARIO/admcloud.git
```

### Erro: "refused to merge unrelated histories"

```powershell
git pull origin main --allow-unrelated-histories
```

### Erro: "authentication failed"

- Usar Personal Access Token (não senha)
- Ver seção "Gerar Personal Access Token" acima

---

## ✅ Sucesso!

Quando você ver:

```
Enumerating objects: 342, done.
Counting objects: 100% (342/342), done.
...
To https://github.com/SEU_USUARIO/admcloud.git
 * [new branch]      main -> main
Branch 'main' set up to track remote branch 'main' from 'origin'.
```

**Significa que deu certo!** 🎉

---

## 🚀 Próximo Passo

Depois de confirmar no GitHub:

1. SSH para VPS
2. Clonar projeto
3. Subir Docker
4. Testar API

Aproximadamente 15 minutos no total! ⏱️

---

**Você está pronto! Comece o Git push agora.** 🚀

Tempo total: **10 minutos**  
Dificuldade: **Fácil** ⭐  
Resultado: **Código em produção!** 🎉
