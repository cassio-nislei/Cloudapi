# Guia de Uso - Integração ADMCloud API (Pós-Correções)

## Visão Geral

Após as correções implementadas, a integração Delphi agora está em **conformidade completa** com a API OpenAPI.

---

## 1. Validar Passport de Cliente (GET /passport)

### Uso Correto

```pascal
procedure ValidarLicenca;
var
  LAPIHelper: TADMCloudHelper;
  LCNPJ: string;
  LHostname: string;
  LGUID: string;
begin
  LAPIHelper := TADMCloudHelper.Create('https://admcloud.papion.com.br/api/v1');
  try
    LCNPJ := '12.345.678/0001-90';        // Com formatação (api limpa)
    LHostname := 'DESKTOP-USUARIO';
    LGUID := '550e8400-e29b-41d4-a716-446655440000';

    // Fazer validação
    if LAPIHelper.ValidarPassport(LCNPJ, LHostname, LGUID) then
    begin
      // ✅ Requisição bem-sucedida
      if LAPIHelper.GetPassportStatus then
      begin
        ShowMessage('✅ Licença válida!');
        ShowMessage('Mensagem: ' + LAPIHelper.GetPassportMensagem);
      end
      else
      begin
        ShowMessage('❌ Licença inválida');
        ShowMessage('Motivo: ' + LAPIHelper.GetPassportMensagem);
      end;
    end
    else
    begin
      // ❌ Erro na requisição
      ShowMessage('❌ Erro ao validar: ' + LAPIHelper.GetUltimoErro);
    end;

  finally
    LAPIHelper.Free;
  end;
end;
```

### O que Mudou

| Antes                                | Depois                               |
| ------------------------------------ | ------------------------------------ |
| Não validava parâmetros obrigatórios | ✅ Valida cgc, hostname, guid        |
| Não armazenava resposta JSON         | ✅ Armazena em FLastPassportResponse |
| GetPassportResponse retornava padrão | ✅ Parse JSON correto                |
| Status comparado com string 'true'   | ✅ Parse como boolean nativo         |
| Usava BasicAuth em /passport         | ✅ /passport é público (sem auth)    |

---

## 2. Registrar Novo Cliente (POST /registro)

### Uso Correto

```pascal
procedure RegistrarClienteADMCloud;
var
  LAPIHelper: TADMCloudHelper;
  LUrl: string;
begin
  // Configurar para produção
  LUrl := 'https://admcloud.papion.com.br/api/v1';

  LAPIHelper := TADMCloudHelper.Create(LUrl);
  try
    // Configurar credenciais (se necessário, diferentes das padrão)
    LAPIHelper.ConfigurarCredenciais('api_frontbox', 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg');

    // Registrar cliente com TODOS os 12 campos obrigatórios
    if LAPIHelper.RegistrarCliente(
      ANome        := 'EMPRESA LTDA',
      AFantasia    := 'Minha Empresa',
      ACGC         := '12.345.678/0001-90',          // Pode ter formatação
      AContato     := 'João Silva',
      AEmail       := 'joao@empresa.com.br',
      ATelefone    := '(11) 3000-0000',
      ACelular     := '(11) 99999-9999',              // Opcional
      AEndereco    := 'Rua das Flores',
      ANumero      := '123',
      AComplemento := 'Sala 10',                      // Opcional
      ABairro      := 'Centro',
      ACidade      := 'São Paulo',
      AEstado      := 'SP',
      ACEP         := '01310-100'
    ) then
    begin
      // ✅ Requisição bem-sucedida
      if LAPIHelper.GetRegistroStatus = 'OK' then
      begin
        ShowMessage('✅ Cliente registrado com sucesso!');
        ShowMessage('Chave B: ' + LAPIHelper.GetRegistroMensagem);
        ShowMessage('ID Pessoa: ' + LAPIHelper.GetRegistroData);
      end
      else
      begin
        ShowMessage('❌ Erro no registro: ' + LAPIHelper.GetRegistroMensagem);
      end;
    end
    else
    begin
      // ❌ Erro na requisição
      ShowMessage('❌ Erro ao registrar: ' + LAPIHelper.GetUltimoErro);
      ShowMessage('Status Code: ' + IntToStr(LAPIHelper.GetUltimoStatusCode));
    end;

  finally
    LAPIHelper.Free;
  end;
end;
```

### O que Mudou

| Antes                         | Depois                                |
| ----------------------------- | ------------------------------------- |
| Tratava campos como opcionais | ✅ 12 campos são obrigatórios         |
| CNPJ não era normalizado      | ✅ RemoverFormatacao() automático     |
| Não validava antes de enviar  | ✅ Valida todos os campos             |
| Resposta não era armazenada   | ✅ Armazena em FLastRegistroResponse  |
| Sempre usava BasicAuth        | ✅ Usa BasicAuth correto em /registro |

---

## 3. Integração com Form uEmpresa.pas

### Chamada no Event Handler

```pascal
procedure TfrmEmpresa.btnRegistrarADMCloudClick(Sender: TObject);
var
  LAPIHelper: TADMCloudHelper;
begin
  if not qryEmpresa.Active then
    Exit;

  // Validar campos obrigatórios do form
  if Trim(qryEmpresaRAZAO.AsString) = '' then
  begin
    ShowMessage('Preencha a Razão Social');
    Exit;
  end;

  LAPIHelper := TADMCloudHelper.Create(ADMCloud_URL_PROD);
  try
    LAPIHelper.ConfigurarTimeout(60000);  // 60 segundos para POST

    if LAPIHelper.RegistrarCliente(
      qryEmpresaRAZAO.AsString,          // Nome
      qryEmpresaFANTASIA.AsString,        // Fantasia
      qryEmpresaCNPJ.AsString,            // CNPJ
      qryEmpresaNome_Contato.AsString,    // Contato (se houver field)
      qryEmpresaEMAIL.AsString,           // Email
      qryEmpresaFONE.AsString,            // Telefone
      '',                                 // Celular (opcional)
      qryEmpresaENDERECO.AsString,        // Endereço
      qryEmpresaNUMERO.AsString,          // Número
      '',                                 // Complemento (opcional)
      qryEmpresaBAIRRO.AsString,          // Bairro
      qryEmpresaCIDADE.AsString,          // Cidade
      qryEmpresaUF.AsString,              // Estado
      qryEmpresaCEP.AsString              // CEP
    ) then
    begin
      ShowMessage('✅ Empresa registrada na plataforma ADMCloud!');
    end
    else
    begin
      ShowMessage('❌ Erro ao registrar: ' + LAPIHelper.GetUltimoErro);
    end;

  finally
    LAPIHelper.Free;
  end;
end;
```

---

## 4. Erros Comuns (Pós-Correção)

### ❌ Erro: "Parâmetros obrigatórios não preenchidos"

**Causa:** ValidarPassport chamado sem cgc, hostname ou guid

**Solução:**

```pascal
// ❌ ERRADO
LAPIHelper.ValidarPassport('', 'DESKTOP', AGUID);

// ✅ CORRETO
if (LCNPJ <> '') and (LHostname <> '') and (LGUID <> '') then
  LAPIHelper.ValidarPassport(LCNPJ, LHostname, LGUID);
```

### ❌ Erro: "Todos os campos são obrigatórios para registro"

**Causa:** Faltando um dos 12 campos obrigatórios

**Campos Obrigatórios:**

1. ✅ Nome (Razão Social)
2. ✅ Fantasia
3. ✅ CNPJ/CPF
4. ✅ Contato
5. ✅ Email
6. ✅ Telefone
7. ✅ Endereço
8. ✅ Número
9. ✅ Bairro
10. ✅ Cidade
11. ✅ Estado
12. ✅ CEP

**Solução:**

```pascal
// ✅ Verificar antes de chamar
if (ANome = '') or (AFantasia = '') or (ACGC = '') or
   (AContato = '') or (AEmail = '') or (ATelefone = '') or
   (AEndereco = '') or (ANumero = '') or (ABairro = '') or
   (ACidade = '') or (AEstado = '') or (ACEP = '') then
begin
  ShowMessage('Preenchimento obrigatório de todos os campos!');
  Exit;
end;

LAPIHelper.RegistrarCliente(...);
```

### ❌ Erro: Status Code 401 (Unauthorized)

**Causa:** Credenciais incorretas em /registro

**Solução:**

```pascal
LAPIHelper := TADMCloudHelper.Create(LUrl);
LAPIHelper.ConfigurarCredenciais(
  'api_frontbox',                              // Username
  'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg'  // Password correto
);
```

---

## 5. Comparação de Respostas

### Resposta GET /passport

```json
{
  "Status": true,
  "Mensagem": "Passport OK!"
}
```

**Como acessar:**

```pascal
if LAPIHelper.GetPassportStatus then
  ShowMessage(LAPIHelper.GetPassportMensagem);
```

### Resposta POST /registro

```json
{
  "status": "OK",
  "msg": "CHAVE_B_AQUI",
  "data": {
    "id_pessoa": 1001,
    "nome": "EMPRESA LTDA",
    "fantasia": "Minha Empresa",
    "cgc": "12345678001234",
    "email": "joao@empresa.com.br",
    "ativo": "S",
    "licencas": 1,
    "cont_licencas": 1,
    "periodo": 30,
    "expira_em": "2024-01-15",
    "data_install": "2023-12-15T10:30:00"
  }
}
```

**Como acessar:**

```pascal
if LAPIHelper.GetRegistroStatus = 'OK' then
begin
  ShowMessage('Chave B: ' + LAPIHelper.GetRegistroMensagem);
  // Para acessar dados completos, usar GetRegistroResponseRaw
  ShowMessage(LAPIHelper.GetRegistroResponseRaw);
end;
```

---

## 6. Checklist de Conformidade

- [x] Parâmetros obrigatórios validados
- [x] Campos obrigatórios em POST validados
- [x] JSON parseado corretamente (boolean como boolean)
- [x] Autenticação diferenciada por endpoint
- [x] Respostas armazenadas para consulta
- [x] CNPJ/CPF normalizado automaticamente
- [x] Tratamento de erro estruturado
- [x] Métodos públicos para acessar respostas brutas
- [x] Timeout configurável
- [x] Suporte a parâmetros opcionais (fbx, pdv, celular, complemento)

---

## 7. Changelog de Correções

| Data       | Versão | Correção                  | Status |
| ---------- | ------ | ------------------------- | ------ |
| 23/12/2025 | 2.1    | Armazenar responses       | ✅     |
| 23/12/2025 | 2.1    | Validação de params       | ✅     |
| 23/12/2025 | 2.1    | Autenticação por endpoint | ✅     |
| 23/12/2025 | 2.1    | Parse JSON correto        | ✅     |
| 23/12/2025 | 2.1    | Normalização CNPJ/CPF     | ✅     |
| 23/12/2025 | 2.1    | Campos obrigatórios       | ✅     |

---

## Próximas Melhorias (Futuro)

- [ ] Validação de email com regex
- [ ] Logging detalhado de requests/responses
- [ ] Cache de responses em disco
- [ ] Retry automático em falhas de conexão
- [ ] Suporte a proxy HTTP/HTTPS
- [ ] Rate limiting (máx requisições por minuto)
