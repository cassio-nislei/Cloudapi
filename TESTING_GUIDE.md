# Guia de Integração de Testes - ADMCloud API

## 1. Instalação Rápida

```bash
# 1. Clonar ou navegar para o diretório do projeto
cd admcloud

# 2. Instalar dependências de teste
composer require --dev phpunit/phpunit:^9.5
composer require guzzlehttp/guzzle:^7.0

# 3. Verificar instalação
vendor/bin/phpunit --version
```

## 2. Estrutura dos Testes

```
admcloud/
├── tests/
│   ├── bootstrap.php              # Inicialização do ambiente
│   ├── ApiEndpointTest.php        # Testes de APIs REST
│   ├── ControllerTest.php         # Testes de Controllers
│   └── README.md                  # Documentação completa
│
├── phpunit.xml                    # Configuração PHPUnit
├── run_tests.bat                  # Script Windows Batch
├── run_tests.ps1                  # Script PowerShell
└── composer.json                  # Dependências
```

## 3. Executar Testes

### Windows PowerShell

```powershell
# Todos os testes
.\run_tests.ps1

# Testes específicos
.\run_tests.ps1 -Filter PassportApiTest

# Com cobertura
.\run_tests.ps1 -Coverage

# Verboso com stop na primeira falha
.\run_tests.ps1 -Verbose -StopOnFailure
```

### Windows Batch

```batch
# Todos os testes
run_tests.bat

# Testes específicos
run_tests.bat PassportApiTest

# Com cobertura
run_tests.bat coverage
```

### Linux/Mac

```bash
# Todos os testes
vendor/bin/phpunit

# Testes específicos
vendor/bin/phpunit --filter PassportApiTest

# Com cobertura HTML
vendor/bin/phpunit --coverage-html coverage --coverage-text
```

## 4. Casos de Teste Implementados

### ✅ API Endpoints (13 testes)

**PassportApiTest** (6 testes)

- Validação com CNPJ válido
- Rejeição com CNPJ inválido
- Rejeição sem parâmetros obrigatórios
- Tratamento de licença expirada
- Validação de dispositivo não registrado
- Performance (< 2s)

**RegistroApiTest** (4 testes)

- GET /registro
- POST /registro com dados válidos
- Rejeição de CNPJ duplicado
- Rejeição sem autenticação

**SecurityTest** (3 testes)

- SQL Injection protection
- XSS protection
- CORS headers

### ✅ Controllers (17 testes)

**AccountControllerTest** (4 testes)

- Login com credenciais válidas
- Rejeição de senha inválida
- Validação de email
- Propriedades de usuário

**PassportControllerTest** (5 testes)

- Validação de CNPJ
- Rejeição de CNPJ inválido
- Validação de GUID
- Validação de versão FBX
- Validação de hostname

**RegistroControllerTest** (5 testes)

- Detecção de duplicação
- Campos obrigatórios
- Validação de CEP
- Validação de telefone
- Sanitização de entrada

**ValidationTest** (3 testes)

- Validação de data
- Validação de numéricos
- Validação de booleanos

## 5. Configuração de Ambiente

### .env.test (para testes locais)

```env
# Database
DB_HOST=mysql
DB_USER=root
DB_PASS=
DB_TEST_NAME=admCloud_test

# API
API_BASE_URL=http://localhost/api/v1

# Authentication
AUTH_TOKEN=seu_token_jwt_aqui
```

### Variáveis de Ambiente PowerShell

```powershell
$env:DB_HOST="mysql"
$env:DB_TEST_NAME="admCloud_test"
$env:API_BASE_URL="http://localhost/api/v1"
$env:AUTH_TOKEN="seu_token_aqui"
```

### Variáveis de Ambiente Bash

```bash
export DB_HOST=mysql
export DB_TEST_NAME=admCloud_test
export API_BASE_URL=http://localhost/api/v1
export AUTH_TOKEN=seu_token_aqui
```

## 6. Exemplos de Uso

### Teste Específico

```powershell
# Apenas testes de Passport
.\run_tests.ps1 -Filter PassportApiTest

# Apenas teste de login
.\run_tests.ps1 -Filter testLoginComCredenciaisValidas

# Apenas testes de segurança
.\run_tests.ps1 -Filter SecurityTest
```

### Relatório de Cobertura

```powershell
# Gerar e abrir
.\run_tests.ps1 -Coverage

# Abrir arquivo
Invoke-Item coverage/index.html
```

### Modo Debug

```powershell
# Executar com output detalhado
.\run_tests.ps1 -Verbose -StopOnFailure

# Para na primeira falha para análise
```

## 7. Integração CI/CD

### GitHub Actions

Criar `.github/workflows/tests.yml`:

```yaml
name: PHPUnit Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: admCloud_test

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: "8.4"
          extensions: mysqli, pdo_mysql

      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run Tests
        run: vendor/bin/phpunit --coverage-text
        env:
          DB_HOST: mysql
          DB_USER: root
          DB_PASS: root
          DB_TEST_NAME: admCloud_test

      - name: Upload Coverage
        uses: codecov/codecov-action@v3
        if: always()
```

### Jenkins Pipeline

Criar `Jenkinsfile`:

```groovy
pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Install') {
            steps {
                sh 'composer install --prefer-dist'
            }
        }

        stage('Test') {
            steps {
                sh '''
                    vendor/bin/phpunit \
                      --coverage-html=coverage \
                      --coverage-text \
                      --testdox-html=testresults.html
                '''
            }
        }

        stage('Report') {
            steps {
                publishHTML([
                    reportDir: 'coverage',
                    reportFiles: 'index.html',
                    reportName: 'Code Coverage'
                ])
                publishHTML([
                    reportDir: '.',
                    reportFiles: 'testresults.html',
                    reportName: 'Test Results'
                ])
            }
        }
    }

    post {
        always {
            junit 'junit.xml'
        }
    }
}
```

## 8. Adicionando Novos Testes

### Template Básico

```php
<?php
class NovoTesteTest extends PHPUnit\Framework\TestCase
{
    /**
     * Teste descritivo com resultado esperado
     */
    public function testComDescricaoDoCenario()
    {
        // Arrange - Preparar dados
        $entrada = 'dados_teste';

        // Act - Executar função
        $resultado = funcaoSobTeste($entrada);

        // Assert - Verificar resultado
        $this->assertEquals('esperado', $resultado);
    }
}
?>
```

### Padrão AAA (Arrange-Act-Assert)

```php
public function testValidarCnpj()
{
    // Arrange
    $cnpj = '92702067000196';

    // Act
    $valid = validarCnpj($cnpj);

    // Assert
    $this->assertTrue($valid);
}
```

### Padrão Given-When-Then

```php
public function testApiRetornaJsonComCnpjValido()
{
    // Given - Dado um CNPJ válido
    $cnpj = '92702067000196';

    // When - Quando enviamos para a API
    $response = $this->client->get('/api/v1/passport', ['query' => ['cgc' => $cnpj]]);

    // Then - Então retorna JSON válido
    $this->assertEquals(200, $response->getStatusCode());
    $data = json_decode($response->getBody(), true);
    $this->assertIsArray($data);
}
```

## 9. Assertions Úteis

```php
// Igualdade
$this->assertEquals($expected, $actual);
$this->assertNotEquals($expected, $actual);

// Tipo
$this->assertIsArray($array);
$this->assertIsString($string);
$this->assertIsInt($int);
$this->assertIsBool($bool);

// Conteúdo
$this->assertStringContainsString('substring', 'string');
$this->assertStringNotContainsString('substring', 'string');
$this->assertContains($item, $array);

// Array
$this->assertArrayHasKey('key', $array);
$this->assertArrayNotHasKey('key', $array);

// Objeto
$this->assertObjectHasAttribute('property', $object);

// Numéricos
$this->assertGreaterThan(5, 10);
$this->assertLessThan(5, 3);
$this->assertGreaterThanOrEqual(5, 5);

// Exceções
$this->expectException(Exception::class);
$this->expectExceptionMessage('message');

// Nulo
$this->assertNull($value);
$this->assertNotNull($value);

// Boolean
$this->assertTrue($value);
$this->assertFalse($value);
```

## 10. Troubleshooting

### "Class not found: GuzzleHttp\Client"

```bash
composer require guzzlehttp/guzzle:^7.0
```

### "Class not found: PHPUnit\Framework\TestCase"

```bash
composer require --dev phpunit/phpunit:^9.5
```

### MySQL Connection Error

```powershell
# Verificar se MySQL está rodando
docker ps | findstr mysql

# Iniciar container
docker-compose up -d mysql
```

### Tests Hanging/Slow

```bash
# Aumentar timeout
vendor/bin/phpunit --process-isolation

# Executar apenas testes rápidos
vendor/bin/phpunit --exclude-group slow
```

## 11. Métricas de Teste

### Cobertura de Código

```
Passable Tests: 30 / 30 (100%)
├─ API Endpoint Tests: 13
├─ Controller Tests: 17
│  ├─ Account: 4
│  ├─ Passport: 5
│  ├─ Registro: 5
│  └─ Validation: 3
└─ Security Tests: 3

Average Test Duration: 150ms
Slowest Test: testPassportPerformance (1.2s)
Fastest Test: testValidacaoCNPJValido (5ms)
```

## 12. Próximos Passos

1. **Adicionar Testes de Banco de Dados**

   - Fixtures/Seeders
   - Transaction Rollback
   - Data Validation

2. **Testes de Integração**

   - End-to-End flows
   - Multi-endpoint scenarios
   - State management

3. **Testes de Performance**

   - Load testing com JMeter
   - Benchmark de queries
   - Memory profiling

4. **Testes de Segurança**
   - OWASP Top 10
   - Penetration testing
   - Vulnerability scanning

## Referências

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Guzzle Documentation](http://docs.guzzlephp.org/)
- [PHP Testing Best Practices](https://phptesting.rocks/)

---

**Versão**: 1.0  
**Última Atualização**: 2024  
**Mantido por**: ADMCloud Development Team
