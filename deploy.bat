@echo off
REM Deploy script para ADMCloud API em Docker (Windows)
REM Uso: deploy.bat [start|stop|restart|logs|status|clean]

setlocal enabledelayedexpansion

REM Cores para output (Windows 10+)
set GREEN=[32m
set YELLOW=[33m
set RED=[31m
set NC=[0m

echo.
echo ============================================
echo   ADMCloud API - Docker Deploy Script
echo ============================================
echo.

if "%1"=="" goto :start
if /i "%1"=="start" goto :start
if /i "%1"=="stop" goto :stop
if /i "%1"=="restart" goto :restart
if /i "%1"=="logs" goto :logs
if /i "%1"=="status" goto :status
if /i "%1"=="test" goto :test
if /i "%1"=="db" goto :db
if /i "%1"=="shell" goto :shell
if /i "%1"=="clean" goto :clean
if /i "%1"=="help" goto :help

goto :help

:start
echo [*] Verificando Docker...
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERRO] Docker nao esta instalado!
    exit /b 1
)

echo [+] Iniciando containers...
docker-compose up -d --build
if %errorlevel% neq 0 (
    echo [ERRO] Falha ao iniciar containers
    exit /b 1
)

echo [*] Aguardando health checks...
timeout /t 10 /nobreak

echo [+] Status dos containers:
docker-compose ps

echo.
echo [OK] Containers iniciados com sucesso!
echo [INFO] API disponivel em: http://localhost:8080
echo [INFO] phpMyAdmin disponivel em: http://localhost:8081
goto :eof

:stop
echo [*] Parando containers...
docker-compose stop
if %errorlevel% equ 0 (
    echo [OK] Containers parados
) else (
    echo [ERRO] Falha ao parar containers
    exit /b 1
)
goto :eof

:restart
echo [WARN] Reiniciando containers...
docker-compose restart
timeout /t 5 /nobreak
echo [+] Status dos containers:
docker-compose ps
echo [OK] Containers reiniciados
goto :eof

:logs
if "%2"=="" (
    echo [*] Mostrando logs de todos os servicos (Ctrl+C para sair)...
    docker-compose logs -f
) else (
    echo [*] Mostrando logs de: %2
    docker-compose logs -f %2
)
goto :eof

:status
echo [+] Status dos containers:
docker-compose ps

echo.
echo [+] Espaco em disco:
docker system df

echo.
echo [+] Testando health check...
curl -s "http://localhost:8080/api/v1/passport?cgc=test" >nul 2>&1
if %errorlevel% equ 0 (
    echo [OK] API respondendo
) else (
    echo [WARN] API nao respondendo ainda
)
goto :eof

:test
echo [*] Testando endpoints da API...
echo.
echo [1] Teste GET /passport
curl -s "http://localhost:8080/api/v1/passport?cgc=01611275000205^&hostname=DOCKER-TEST^&guid=550e8400-e29b-41d4-a716-446655440000"

echo.
echo.
echo [2] Teste GET /registro
curl -s -u "api_frontbox:api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg" "http://localhost:8080/api/v1/registro"

echo.
echo [OK] Testes concluidos
goto :eof

:db
echo [*] Conectando ao banco de dados MySQL...
docker-compose exec -T mysql mysql -u papion -pPap10nL4vrAs2024 papion -e "SELECT ID_PESSOA, CGC, NOME, ATIVO FROM PESSOAS LIMIT 5;"
goto :eof

:shell
if "%2"=="" (
    echo [*] Abrindo shell do admcloud-api...
    docker-compose exec admcloud-api bash
) else (
    echo [*] Abrindo shell de: %2
    docker-compose exec %2 bash
)
goto :eof

:clean
echo [WARN] Remover containers e volumes vai deletar o banco de dados!
set /p confirm="Tem certeza? (s/n): "
if /i "%confirm%"=="s" (
    docker-compose down -v
    echo [OK] Limpeza concluida
) else (
    echo [WARN] Operacao cancelada
)
goto :eof

:help
echo.
echo Uso: deploy.bat [COMANDO] [OPCOES]
echo.
echo Comandos:
echo   start       Iniciar containers (padrao)
echo   stop        Parar containers
echo   restart     Reiniciar containers
echo   status      Ver status dos containers
echo   logs        Ver logs (opcional: especificar servico)
echo   test        Testar endpoints da API
echo   db          Ver dados do banco de dados
echo   shell       Acessar shell do container (opcional: especificar servico)
echo   clean       Remover containers e volumes (^!DELETA DADOS^!)
echo   help        Ver esta ajuda
echo.
echo Exemplos:
echo   deploy.bat start                 (Iniciar tudo)
echo   deploy.bat logs admcloud-api     (Ver logs da API)
echo   deploy.bat logs mysql            (Ver logs do MySQL)
echo   deploy.bat shell                 (Shell da API)
echo   deploy.bat test                  (Testar endpoints)
echo.
echo Documentacao: DOCKER_SETUP.md
echo.
goto :eof
