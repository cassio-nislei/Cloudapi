# Script para executar testes PHPUnit - ADMCloud API
# Windows PowerShell Script

param(
    [string]$Filter = "",
    [switch]$Coverage = $false,
    [switch]$Verbose = $false,
    [switch]$StopOnFailure = $false
)

$ErrorActionPreference = "Stop"

# Cores
$Green = [System.ConsoleColor]::Green
$Red = [System.ConsoleColor]::Red
$Yellow = [System.ConsoleColor]::Yellow
$Blue = [System.ConsoleColor]::Blue

function Write-Info {
    param([string]$Message)
    Write-Host "[INFO] " -ForegroundColor $Blue -NoNewline
    Write-Host $Message
}

function Write-Success {
    param([string]$Message)
    Write-Host "[OK] " -ForegroundColor $Green -NoNewline
    Write-Host $Message
}

function Write-Warning {
    param([string]$Message)
    Write-Host "[WARN] " -ForegroundColor $Yellow -NoNewline
    Write-Host $Message
}

function Write-Error {
    param([string]$Message)
    Write-Host "[ERRO] " -ForegroundColor $Red -NoNewline
    Write-Host $Message
}

# Header
Write-Host ""
Write-Host "=========================================" -ForegroundColor $Blue
Write-Host "ADMCloud API - Test Suite" -ForegroundColor $Blue
Write-Host "=========================================" -ForegroundColor $Blue
Write-Host ""

# Verificar PHP
Write-Info "Verificando PHP..."
try {
    $phpVersion = php -v | Select-Object -First 1
    Write-Success $phpVersion
} catch {
    Write-Error "PHP nao encontrado no PATH"
    exit 1
}

# Verificar Composer
Write-Info "Verificando Composer..."
if (-not (Test-Path "vendor\bin\phpunit.bat")) {
    Write-Error "PHPUnit nao encontrado!"
    Write-Host ""
    Write-Info "Execute: composer require --dev phpunit/phpunit:^9.5"
    exit 1
}
Write-Success "PHPUnit encontrado"

# Preparar argumentos
$phpunitArgs = @()

if ($Filter) {
    Write-Info "Filtro de testes: $Filter"
    $phpunitArgs += "--filter=$Filter"
}

if ($Coverage) {
    Write-Info "Gerando relatório de cobertura..."
    if (-not (Test-Path "coverage")) {
        New-Item -ItemType Directory -Path "coverage" | Out-Null
    }
    $phpunitArgs += "--coverage-html=coverage"
    $phpunitArgs += "--coverage-text"
}

if ($Verbose) {
    Write-Info "Modo verboso ativado"
    $phpunitArgs += "-v"
}

if ($StopOnFailure) {
    Write-Info "Parando na primeira falha"
    $phpunitArgs += "--stop-on-failure"
}

# Executar testes
Write-Host ""
Write-Info "Executando testes..."
Write-Host ""

& "vendor\bin\phpunit.bat" @phpunitArgs

$testResult = $LASTEXITCODE

# Análise de resultado
Write-Host ""
if ($testResult -eq 0) {
    Write-Success "Todos os testes passaram!"
} else {
    Write-Error "Alguns testes falharam (Exit code: $testResult)"
}

# Se gerou cobertura, informar localização
if ($Coverage -and (Test-Path "coverage\index.html")) {
    Write-Host ""
    Write-Success "Relatório de cobertura: file://$(Resolve-Path 'coverage\index.html')"
}

Write-Host ""
exit $testResult
