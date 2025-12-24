# 🚀 QuickStart - Classes Pascal ADMCloud API

**Versão:** 2.0  
**Data:** 09 de Dezembro de 2024

---

## 📋 5 Minutos para Começar

### 1. Adicionar ao Seu Projeto

Copie estes arquivos para seu projeto:

- `ADMCloudAPI.pas`
- `ADMCloudAPIHelper.pas`
- `ADMCloudConsts.pas`

Adicione no seu arquivo principal:

```pascal
uses
  ADMCloudAPI,
  ADMCloudAPIHelper,
  ADMCloudConsts;
```

### 2. Usar em Seu Código

**Opção A: Forma Simples (Recomendada)**

```pascal
var
  LHelper: TADMCloudHelper;
begin
  LHelper := TADMCloudHelper.Create;
  try
    if LHelper.ValidarPassport('12345678901234', 'DESKTOP', 'GUID') then
      ShowMessage('OK!')
    else
      ShowMessage('Erro: ' + LHelper.GetUltimoErro);
  finally
    LHelper.Free;
  end;
end;
```

**Opção B: Forma Avançada**

```pascal
var
  LAPI: TADMCloudAPI;
begin
  LAPI := TADMCloudAPI.Create('http://localhost/api/v1');
  try
    if LAPI.ValidarPassport('12345678901234', 'DESKTOP', 'GUID') then
      ShowMessage('OK!')
    else
      ShowMessage('Erro: ' + LAPI.GetUltimoErro);
  finally
    LAPI.Free;
  end;
end;
```

---

## 📌 3 Operações Principais

### ✅ 1. Validar Passport

```pascal
LHelper.ValidarPassport(
  '12345678901234',      // CGC/CNPJ
  'DESKTOP-PC',          // Hostname
  'GUID-123',            // GUID único
  '4.5.2',               // Versão FBX (opcional)
  '1.2.3'                // Versão PDV (opcional)
);
```

### ✅ 2. Registrar Cliente

```pascal
LHelper.RegistrarCliente(
  'EMPRESA LTDA',                 // Nome
  'Minha Empresa',                // Fantasia
  '12.345.678/0001-90',           // CNPJ
  'João Silva',                   // Contato
  'joao@empresa.com.br',          // Email
  '(11) 3000-0000'                // Telefone
);
```

### ✅ 3. Verificar Status

```pascal
LHelper.VerificarStatusRegistro;
```

---

## 🔍 Tratamento de Erros

```pascal
if not LHelper.RegistrarCliente(...) then
begin
  WriteLn('Erro: ' + LHelper.GetUltimoErro);
  WriteLn('Status: ' + IntToStr(LHelper.GetUltimoStatusCode));
end;
```

---

## 🛠️ Funções Úteis

```pascal
// Validar CNPJ
if ValidarCNPJ('12.345.678/0001-90') then
  ShowMessage('CNPJ OK');

// Formatar CNPJ
ShowMessage(FormatarCNPJ('12345678901890'));
// Resultado: 12.345.678/0190-00

// Remover formatação
ShowMessage(RemoverFormatacao('123.456.789-00'));
// Resultado: 12345678900
```

---

## 🌐 URLs

```pascal
// Desenvolvimento
LHelper := TADMCloudHelper.Create(ADMCloud_URL_DEV);

// Produção
LHelper := TADMCloudHelper.Create(ADMCloud_URL_PROD);

// Custom
LHelper := TADMCloudHelper.Create('https://sua-url.com/api/v1');
```

---

## 📁 Arquivos Inclusos

| Arquivo                     | Descrição             |
| --------------------------- | --------------------- |
| `ADMCloudAPI.pas`           | Classe principal      |
| `ADMCloudAPIHelper.pas`     | Classe simplificada   |
| `ADMCloudConsts.pas`        | Constantes e funções  |
| `ExemploADMCloudAPI.pas`    | Exemplos de código    |
| `FormExemploIntegracao.pas` | Integração em Form    |
| `GUIA_CLASSES_PASCAL.md`    | Documentação completa |

---

## ✨ Exemplo Completo

```pascal
procedure MinhaFuncao;
var
  LHelper: TADMCloudHelper;
begin
  LHelper := TADMCloudHelper.Create;
  try
    // Validar Passport
    if LHelper.ValidarPassport('12345678901234', 'PC', 'GUID') then
    begin
      ShowMessage('Passport OK!');

      // Registrar novo cliente
      if LHelper.RegistrarCliente(
        'EMPRESA',
        'Empresa',
        '12.345.678/0001-90',
        'Contato',
        'email@empresa.com',
        '(11) 3000-0000'
      ) then
        ShowMessage('Cliente registrado!')
      else
        ShowMessage('Erro: ' + LHelper.GetUltimoErro);
    end
    else
      ShowMessage('Erro: ' + LHelper.GetUltimoErro);

  finally
    LHelper.Free;
  end;
end;
```

---

## 🔐 Autenticação

**Padrão (já vem configurado):**

```
Usuário: api_frontbox
Senha:   api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg
```

**Alterar:**

```pascal
LHelper.ConfigurarCredenciais('novo_user', 'nova_senha');
```

---

## ⏱️ Timeout

```pascal
// Usar padrão (30s)
LHelper := TADMCloudHelper.Create;

// Configurar customizado
LHelper.ConfigurarTimeout(60000); // 60 segundos

// Usar constantes
LHelper.ConfigurarTimeout(ADMCloud_TIMEOUT_LONGO);
```

---

## 🐛 Troubleshooting

### Erro de Conexão

- Verificar URL
- Verificar conectividade
- Verificar firewall

### Erro 401 (Autenticação)

- Verificar usuário/senha
- Verificar credenciais

### Erro 404 (Não Encontrado)

- Verificar URL da API
- Verificar endpoint

---

## 📚 Mais Informações

Ver: `GUIA_CLASSES_PASCAL.md`

---

**Pronto para começar! 🚀**
