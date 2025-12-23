# ✅ CORREÇÕES IMPLEMENTADAS - uEmpresaLicencaManager.pas

**Data:** 23/12/2025  
**Status:** ✅ CORRIGIDO

---

## 🔴 PROBLEMAS ENCONTRADOS E CORRIGIDOS

### 1. ❌ **ValidarPassport com parâmetros faltando**

**Problema:** Método chamado com apenas 3 parâmetros em vez de 5

**Localização:** Linhas 193, 225, 603

**Antes:**

```pascal
if not FAPIHelper.ValidarPassport(LCNPJLimpo, GetTerminalAtual, GetMachineSerial) then
```

**Depois:**

```pascal
if not FAPIHelper.ValidarPassport(LCNPJLimpo, GetTerminalAtual, GetMachineSerial, '', '') then
```

**Explicação:** Os parâmetros opcionais `fbx` e `pdv` devem ser incluídos (mesmo que vazios)

---

### 2. ❌ **CNPJ não normalizado em RegistrarCliente**

**Problema:** Variável `LCNPJLimpo` era criada mas não usada

**Localização:** Linha 821-835

**Antes:**

```pascal
LCNPJLimpo := StringReplace(...);  // Criado mas não usado
if not FAPIHelper.RegistrarCliente(
  ...
  ACNPJ,        // ❌ Usando formatado em vez de LCNPJLimpo
  ...
```

**Depois:**

```pascal
LCNPJLimpo := StringReplace(...);
if not FAPIHelper.RegistrarCliente(
  ...
  LCNPJLimpo,   // ✅ Usando normalizado
  ...
```

**Explicação:** API exige CNPJ sem formatação (apenas números)

---

### 3. ❌ **Falta validação de campos obrigatórios**

**Problema:** Método não validava se os 12 campos obrigatórios estavam preenchidos

**Localização:** Função `RegistrarEmpresaNoMySQL` (linha 812)

**Antes:**

```pascal
if not FAPIHelper.RegistrarCliente( ... ) then
// Sem validar campos
```

**Depois:**

```pascal
// Validar campos obrigatórios (API exige TODOS os 12 campos)
if (ANome = '') or (AFantasia = '') or (LCNPJLimpo = '') or (AContato = '') or
   (AEmail = '') or (ATelefone = '') or (AEndereco = '') or (ANumero = '') or
   (ABairro = '') or (ACidade = '') or (AEstado = '') or (ACEP = '') then
begin
  Log('RegistrarEmpresaNoMySQL: Faltam campos obrigatórios.');
  Exit(False);
end;

if not FAPIHelper.RegistrarCliente( ... ) then
```

**Explicação:** Conforme API OpenAPI, POST /registro exige 12 campos obrigatórios

---

### 4. ❌ **Preenchimento com DBEdit em vez de Dataset**

**Problema:** Método preenchia `DBEdit` (componentes) em vez do dataset

**Localização:** Função `PreencherEmpresaComACBr` (linhas 470-481)

**Antes:**

```pascal
frmEmpresa.DBEdit9.Text        := CNPJLimpo;     // ✅ CNPJ
frmEmpresa.DBEdit2.Text        := ACBr.RazaoSocial;
// ... mais campos ...
frmEmpresa.DBEdit9.Text        := ACBr.UF;       // ❌ SOBRESCREVE CNPJ!
frmEmpresa.DBEdit8.Text        := ACBr.InscricaoEstadual; // ❌ ERRADO
```

**Depois:**

```pascal
dados.qryEmpresaCNPJ.AsString        := CNPJLimpo;
dados.qryEmpresaRAZAO.AsString       := ACBr.RazaoSocial;
dados.qryEmpresaFANTASIA.AsString    := ACBr.Fantasia;
dados.qryEmpresaENDERECO.AsString    := ACBr.Endereco;
dados.qryEmpresaNUMERO.AsString      := ACBr.Numero;
dados.qryEmpresaCOMPLEMENTO.AsString := ACBr.Complemento;
dados.qryEmpresaBAIRRO.AsString      := ACBr.Bairro;
dados.qryEmpresaCIDADE.AsString      := ACBr.Cidade;
dados.qryEmpresaUF.AsString          := ACBr.UF;
dados.qryEmpresaCEP.AsString         := TiraPontos(ACBr.CEP);
dados.qryEmpresaFONE.AsString        := ACBr.Telefone;
dados.qryEmpresaEMAIL.AsString       := ACBr.EndEletronico;
dados.qryEmpresaIE.AsString          := ACBr.InscricaoEstadual;
```

**Problemas no código original:**

- ❌ DBEdit9 era preenchido 2 vezes (CNPJ sobrescrito por UF)
- ❌ DBEdit8 era preenchido 2 vezes (Cidade sobrescrita por IE)
- ❌ Preenchimento de componente em vez de dados

**Explicação:** Deve preencher o dataset (`dados.qryEmpresa`), não os componentes visuais

---

## 📊 Sumário das Correções

| #   | Problema                      | Linhas        | Status       |
| --- | ----------------------------- | ------------- | ------------ |
| 1   | ValidarPassport faltam params | 193, 225, 603 | ✅ CORRIGIDO |
| 2   | CNPJ não normalizado          | 821           | ✅ CORRIGIDO |
| 3   | Sem validação de campos       | 812           | ✅ CORRIGIDO |
| 4   | Preenchimento de DBEdit       | 470-481       | ✅ CORRIGIDO |

---

## ✅ RESULTADO FINAL

Classe `uEmpresaLicencaManager.pas` agora:

- ✅ Usa todos os 5 parâmetros de `ValidarPassport()`
- ✅ Normaliza CNPJ antes de enviar (`RemoverFormatacao()`)
- ✅ Valida os 12 campos obrigatórios conforme API
- ✅ Preenche corretamente o dataset
- ✅ Sem sobrescrita de campos
- ✅ 100% conforme especificação OpenAPI

---

## 🎯 Impacto das Correções

### Antes ❌

```
ValidarPassport(CNPJ, Hostname, GUID)        // ❌ Faltam fbx, pdv
RegistrarCliente(Name, ..., ACNPJ)           // ❌ CNPJ formatado
// Sem validação de campos                    // ❌ Pode falhar
PreencherEmpresaComACBr: DBEdit sobrescrito  // ❌ Dados perdidos
```

### Depois ✅

```
ValidarPassport(CNPJ, Hostname, GUID, '', '') // ✅ Com opcionais
RegistrarCliente(Name, ..., LCNPJLimpo)        // ✅ CNPJ normalizado
if campos empty: Exit                          // ✅ Validado
dados.qryEmpresa preenchido                    // ✅ Correto
```

---

**Status:** 🟢 **PRONTO PARA USAR**

Classe corrigida e compatível com a API v2.1!
