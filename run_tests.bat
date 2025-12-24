@echo off
REM Script para executar testes PHPUnit - ADMCloud API
REM Windows Batch Script

echo.
echo =========================================
echo ADMCloud API - Test Suite
echo =========================================
echo.

REM Verificar se vendor/bin/phpunit existe
if not exist "vendor\bin\phpunit.bat" (
    echo [ERRO] PHPUnit nao encontrado!
    echo.
    echo Execute: composer require --dev phpunit/phpunit:^9.5
    pause
    exit /b 1
)

REM Cores para output
REM Não suportadas nativamente no CMD, usar em PowerShell se necessário

echo [INFO] Diretório atual: %cd%
echo [INFO] PHP Version:
php -v
echo.

REM Parse argumentos
set TEST_FILTER=%1
set COVERAGE=%2

REM Se nenhum argumento, executar todos os testes
if "%TEST_FILTER%"=="" (
    echo [INFO] Executando todos os testes...
    vendor\bin\phpunit.bat
    goto end
)

REM Se argumento é "coverage", gerar relatório de cobertura
if "%TEST_FILTER%"=="coverage" (
    echo [INFO] Gerando relatório de cobertura de testes...
    if not exist "coverage" mkdir coverage
    vendor\bin\phpunit.bat --coverage-html coverage --coverage-text
    echo [OK] Relatório disponível em: coverage/index.html
    goto end
)

REM Se argumento é um filtro, executar testes específicos
echo [INFO] Executando testes filtrados: %TEST_FILTER%
vendor\bin\phpunit.bat --filter=%TEST_FILTER%

:end
echo.
echo [INFO] Execução concluída!
pause
exit /b %ERRORLEVEL%
