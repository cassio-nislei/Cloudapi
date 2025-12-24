# 📚 Guia de Uso - Classes Pascal ADMCloud API

**Versão:** 2.0  
**Data:** 09 de Dezembro de 2024  
**Linguagem:** Pascal (Delphi/Free Pascal)

---

## 📁 Arquivos Inclusos

### `ADMCloudAPI.pas`

Classe principal que implementa a comunicação com a API ADMCloud.

**Funcionalidades:**

- Autenticação Basic HTTP
- Requisições GET e POST
- Suporte a HTTP e HTTPS
- Tratamento de erros
- Timeout configurável

**Principais Classes:**

- `TADMCloudAPI` - Classe principal

**Principais Métodos:**

- `ValidarPassport()` - Valida passport do cliente
- `GetStatusRegistro()` - Obtém status do módulo de registro
- `RegistrarCliente()` - Registra novo cliente

---

### `ADMCloudAPIHelper.pas`

Classe auxiliar que simplifica o uso da API com convenções mais altas.

**Funcionalidades:**

- Métodos com parâmetros individuais
- Parsing automático de respostas JSON
- Métodos de conveniência para cada operação

**Principais Classes:**

- `TADMCloudHelper` - Classe helper com métodos simplificados

---

### `ADMCloudConsts.pas`

Constantes, tipos e funções utilitárias.

**Inclui:**

- URLs padrão (dev/prod)
- Credenciais padrão
- Códigos HTTP
- Validadores de CPF/CNPJ
- Formatadores

---

### `ExemploADMCloudAPI.pas`

Exemplos de código mostrando como usar as classes.

---

## 🚀 Como Começar

### 1. Adicionar as Units ao Seu Projeto

No seu arquivo principal (`.dpr` ou `.dpk`):

```pascal
uses
  ADMCloudAPI,
  ADMCloudAPIHelper,
  ADMCloudConsts;
```

### 2. Usar a API (Forma Simples)

```pascal
procedure MinhaFuncao;
var
  LHelper: TADMCloudHelper;
begin
  LHelper := TADMCloudHelper.Create;
  try
    // Validar Passport
    if LHelper.ValidarPassport('12345678901234', 'DESKTOP-PC', 'GUID-123') then
      ShowMessage('Passport válido!')
    else
      ShowMessage('Erro: ' + LHelper.GetUltimoErro);
  finally
    LHelper.Free;
  end;
end;
```

### 3. Usar a API (Forma Avançada)

```pascal
procedure MinhaFuncao;
var
  LAPI: TADMCloudAPI;
begin
  LAPI := TADMCloudAPI.Create('http://localhost/api/v1');
  try
    // Configurar credenciais
    LAPI.ConfigurarCredenciais('api_frontbox', 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg');

    // Configurar timeout
    LAPI.ConfigurarTimeout(30000);

    // Fazer requisição
    if LAPI.ValidarPassport('12345678901234', 'DESKTOP-PC', 'GUID-123') then
      ShowMessage('OK!')
    else
      ShowMessage('Erro: ' + LAPI.GetUltimoErro);
  finally
    LAPI.Free;
  end;
end;
```

---

## 📖 Exemplos Práticos

### Exemplo 1: Validar Passport

```pascal
procedure ValidarPassportCliente;
var
  LAPI: TADMCloudAPI;
  LCGC: string;
  LHostname: string;
  LGUID: string;
begin
  LAPI := TADMCloudAPI.Create('http://localhost/api/v1');
  try
    // Dados do cliente
    LCGC := '12345678901234';
    LHostname := ComputerName; // Nome do computador
    LGUID := 'A1B2C3D4-E5F6-7890-ABCD-EF1234567890';

    // Validar
    if LAPI.ValidarPassport(LCGC, LHostname, LGUID) then
      ShowMessage('Cliente válido!')
    else
      ShowMessage('Erro: ' + LAPI.GetUltimoErro);

  finally
    LAPI.Free;
  end;
end;
```

### Exemplo 2: Validar com Versões

```pascal
procedure ValidarComVersoes;
var
  LAPI: TADMCloudAPI;
begin
  LAPI := TADMCloudAPI.Create('http://localhost/api/v1');
  try
    if LAPI.ValidarPassport(
      '12345678901234',      // CGC
      'DESKTOP-PC',          // Hostname
      'GUID-123',            // GUID
      '4.5.2',               // Versão FrontBox
      '1.2.3'                // Versão PDV
    ) then
      ShowMessage('Validado com versões!')
    else
      ShowMessage('Erro: ' + LAPI.GetUltimoErro);
  finally
    LAPI.Free;
  end;
end;
```

### Exemplo 3: Registrar Novo Cliente

```pascal
procedure RegistrarCliente;
var
  LHelper: TADMCloudHelper;
begin
  LHelper := TADMCloudHelper.Create;
  try
    if LHelper.RegistrarCliente(
      'EMPRESA LTDA',                 // Nome
      'Minha Empresa',                // Fantasia
      '12.345.678/0001-90',           // CNPJ
      'João Silva',                   // Contato
      'joao@empresa.com.br',          // Email
      '(11) 3000-0000',               // Telefone
      '(11) 99999-9999',              // Celular
      'Avenida Paulista',             // Endereço
      '1000',                         // Número
      'Sala 10',                      // Complemento
      'Bela Vista',                   // Bairro
      'São Paulo',                    // Cidade
      'SP',                           // Estado
      '01311-100'                     // CEP
    ) then
    begin
      ShowMessage('Cliente registrado!');
      ShowMessage('Status: ' + LHelper.GetRegistroStatus);
      ShowMessage('Msg: ' + LHelper.GetRegistroMensagem);
    end
    else
      ShowMessage('Erro: ' + LHelper.GetUltimoErro);

  finally
    LHelper.Free;
  end;
end;
```

### Exemplo 4: Tratamento de Erro Detalhado

```pascal
procedure TratarErro;
var
  LAPI: TADMCloudAPI;
  LStatusCode: Integer;
  LErroPrincipal: string;
begin
  LAPI := TADMCloudAPI.Create;
  try
    if not LAPI.ValidarPassport('000000000000', 'PC', 'GUID') then
    begin
      LStatusCode := LAPI.GetUltimoStatusCode;
      LErroPrincipal := LAPI.GetUltimoErro;

      case LStatusCode of
        0:
          ShowMessage('Erro de conexão. Verifique URL da API.');
        401:
          ShowMessage('Erro de autenticação. Credenciais inválidas.');
        404:
          ShowMessage('API não encontrada. Verifique URL.');
        500:
          ShowMessage('Erro interno do servidor.');
      else
        ShowMessage('Erro HTTP ' + IntToStr(LStatusCode) + ': ' + LErroPrincipal);
      end;
    end;
  finally
    LAPI.Free;
  end;
end;
```

### Exemplo 5: Usar Constantes

```pascal
procedure UsarConstantes;
var
  LHelper: TADMCloudHelper;
begin
  // Usar constantes definidas em ADMCloudConsts
  LHelper := TADMCloudHelper.Create(ADMCloud_URL_DEV);
  try
    LHelper.ConfigurarCredenciais(ADMCloud_USER, ADMCloud_PASS);
    LHelper.ConfigurarTimeout(ADMCloud_TIMEOUT_PADRAO);

    if LHelper.RegistrarCliente(
      'EMPRESA',
      'Empresa',
      '12.345.678/0001-90',
      'Contato',
      'email@empresa.com',
      '(11) 3000-0000'
    ) then
      ShowMessage('OK!')
    else
      ShowMessage('Erro: ' + LHelper.GetUltimoErro);

  finally
    LHelper.Free;
  end;
end;
```

### Exemplo 6: Validar CPF/CNPJ Antes de Enviar

```pascal
procedure RegistrarComValidacao;
var
  LHelper: TADMCloudHelper;
  LCNPJ: string;
begin
  LCNPJ := '12.345.678/0001-90';

  // Validar CNPJ antes de enviar
  if not ValidarCNPJ(LCNPJ) then
  begin
    ShowMessage('CNPJ inválido!');
    Exit;
  end;

  LHelper := TADMCloudHelper.Create;
  try
    if LHelper.RegistrarCliente(
      'EMPRESA',
      'Empresa',
      LCNPJ,
      'Contato',
      'email@empresa.com',
      '(11) 3000-0000'
    ) then
      ShowMessage('Registrado com sucesso!')
    else
      ShowMessage('Erro: ' + LHelper.GetUltimoErro);

  finally
    LHelper.Free;
  end;
end;
```

---

## 🔧 Configuração Avançada

### Mudar URL da API

```pascal
var
  LAPI: TADMCloudAPI;
begin
  // URL de desenvolvimento
  LAPI := TADMCloudAPI.Create('http://localhost/api/v1');

  // Ou URL de produção
  LAPI := TADMCloudAPI.Create('https://admcloud.papion.com.br/api/v1');
end;
```

### Configurar Timeout

```pascal
LAPI.ConfigurarTimeout(60000); // 60 segundos

// Ou usar constantes
LAPI.ConfigurarTimeout(ADMCloud_TIMEOUT_LONGO);
```

### Configurar Credenciais Customizadas

```pascal
LAPI.ConfigurarCredenciais('seu_usuario', 'sua_senha');
```

---

## 📊 Estrutura de Dados

### TRegistroData

```pascal
type
  TRegistroData = record
    Nome: string;              // Obrigatório
    Fantasia: string;          // Obrigatório
    CGC: string;               // Obrigatório (CNPJ/CPF)
    Contato: string;           // Obrigatório
    Email: string;             // Obrigatório
    Telefone: string;          // Obrigatório
    Celular: string;           // Opcional
    Endereco: string;          // Opcional
    Numero: string;            // Opcional
    Complemento: string;       // Opcional
    Bairro: string;            // Opcional
    Cidade: string;            // Opcional
    Estado: string;            // Opcional
    CEP: string;               // Opcional
  end;
```

### TPassportResponse

```pascal
type
  TPassportResponse = record
    Status: Boolean;           // True se válido
    Mensagem: string;          // Mensagem de resposta
  end;
```

### TRegistroResponse

```pascal
type
  TRegistroResponse = record
    Status: string;            // 'OK', 'ERROR', etc
    Msg: string;              // Mensagem
    Data: string;             // Dados em JSON
  end;
```

---

## 🔐 Autenticação

### Credenciais Padrão

As credenciais padrão vêm configuradas na classe:

```
Usuário: api_frontbox
Senha:   api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg
```

### Alterar Credenciais

```pascal
LAPI.ConfigurarCredenciais('novo_usuario', 'nova_senha');
```

---

## 📋 Tratamento de Erros

### Códigos HTTP Comuns

```pascal
HTTP_OK = 200                    // Sucesso
HTTP_CREATED = 201              // Criado com sucesso
HTTP_BAD_REQUEST = 400          // Dados inválidos
HTTP_UNAUTHORIZED = 401         // Autenticação falhou
HTTP_FORBIDDEN = 403            // Acesso negado
HTTP_NOT_FOUND = 404            // Não encontrado
HTTP_INTERNAL_ERROR = 500       // Erro do servidor
HTTP_SERVICE_UNAVAILABLE = 503  // Serviço indisponível
```

### Verificar Erro

```pascal
if LAPI.GetUltimoStatusCode = HTTP_UNAUTHORIZED then
  ShowMessage('Credenciais inválidas!')
else if LAPI.GetUltimoStatusCode = HTTP_NOT_FOUND then
  ShowMessage('API não encontrada!')
else
  ShowMessage('Erro: ' + LAPI.GetUltimoErro);
```

---

## 🛠️ Funções Utilitárias

### Validar CPF

```pascal
if ValidarCPF('123.456.789-00') then
  ShowMessage('CPF válido!')
else
  ShowMessage('CPF inválido!');
```

### Validar CNPJ

```pascal
if ValidarCNPJ('12.345.678/0001-90') then
  ShowMessage('CNPJ válido!')
else
  ShowMessage('CNPJ inválido!');
```

### Formatar CPF

```pascal
ShowMessage(FormatarCPF('12345678900')); // Exibe: 123.456.789-00
```

### Formatar CNPJ

```pascal
ShowMessage(FormatarCNPJ('12345678901890')); // Exibe: 12.345.678/0190-00
```

### Remover Formatação

```pascal
ShowMessage(RemoverFormatacao('123.456.789-00')); // Exibe: 12345678900
```

---

## 🔒 Segurança

### HTTPS em Produção

```pascal
// Usar HTTPS em produção
LAPI := TADMCloudAPI.Create('https://admcloud.papion.com.br/api/v1');
```

### Nunca Hardcode Credenciais

```pascal
// ❌ Errado
LAPI.ConfigurarCredenciais('api_frontbox', 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg');

// ✅ Correto
LAPI.ConfigurarCredenciais(LerDoBancoDados, LerDoBancoDados);
```

---

## 📞 Troubleshooting

### Erro de Conexão

**Causa:** Não consegue conectar com a API.

**Solução:**

1. Verificar URL da API
2. Verificar conectividade com servidor
3. Verificar firewall/proxy

```pascal
if LAPI.GetUltimoStatusCode = 0 then
  ShowMessage('Erro de conexão. Verifique URL e conectividade.');
```

### Erro 401 (Unauthorized)

**Causa:** Autenticação falhou.

**Solução:**

1. Verificar usuário e senha
2. Verificar se credenciais expiradas

```pascal
if LAPI.GetUltimoStatusCode = HTTP_UNAUTHORIZED then
  ShowMessage('Erro de autenticação. Verifique credenciais.');
```

### Erro 404 (Not Found)

**Causa:** URL/endpoint não existe.

**Solução:**

1. Verificar URL da API
2. Verificar endpoint correto

```pascal
if LAPI.GetUltimoStatusCode = HTTP_NOT_FOUND then
  ShowMessage('API não encontrada. Verifique URL.');
```

---

## 📚 Referências

- 📖 `ADMCloudAPI.pas` - Documentação da classe principal
- 📖 `ADMCloudAPIHelper.pas` - Documentação da classe helper
- 📖 `ADMCloudConsts.pas` - Constantes e funções utilitárias
- 📖 `ExemploADMCloudAPI.pas` - Exemplos práticos

---

## ✅ Checklist de Implementação

- [ ] Adicionar units ao projeto
- [ ] Criar instância da classe
- [ ] Configurar credenciais (se necessário)
- [ ] Testar ValidarPassport
- [ ] Testar RegistrarCliente
- [ ] Implementar tratamento de erros
- [ ] Testar em ambiente de produção
- [ ] Documentar em seu código

---

**Versão:** 2.0  
**Data:** 09 de Dezembro de 2024  
**Status:** Pronto para Uso
