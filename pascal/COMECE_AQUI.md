# 🚀 COMECE AQUI - Classes Pascal ADMCloud

**Tl;dr:** Copie 3 arquivos, adicione no `uses`, e comece a usar.

---

## 3️⃣ Arquivos Essenciais

Copie para seu projeto:

```
pascal/
├── ADMCloudAPI.pas          ← COPIAR
├── ADMCloudAPIHelper.pas    ← COPIAR
├── ADMCloudConsts.pas       ← COPIAR
└── ... (documentação opcional)
```

---

## 1️⃣ Adicione no Uses

```pascal
uses
  ADMCloudAPI,
  ADMCloudAPIHelper,
  ADMCloudConsts;
```

---

## 2️⃣ Use em Seu Código

### Forma Simples (Recomendada)

```pascal
procedure MinhaFuncao;
var
  LHelper: TADMCloudHelper;
begin
  LHelper := TADMCloudHelper.Create;
  try
    // Validar Passport
    if LHelper.ValidarPassport('12345678901234', 'DESKTOP', 'GUID-123') then
      ShowMessage('Cliente válido!')
    else
      ShowMessage('Erro: ' + LHelper.GetUltimoErro);
  finally
    LHelper.Free;
  end;
end;
```

### Registrar Cliente

```pascal
if LHelper.RegistrarCliente(
  'EMPRESA LTDA',
  'Minha Empresa',
  '12.345.678/0001-90',
  'João Silva',
  'joao@empresa.com.br',
  '(11) 3000-0000'
) then
  ShowMessage('Registrado!')
else
  ShowMessage('Erro: ' + LHelper.GetUltimoErro);
```

---

## 3️⃣ Pronto!

Você agora pode:

- ✅ Validar Passport
- ✅ Registrar Cliente
- ✅ Verificar Status
- ✅ Validar CPF/CNPJ
- ✅ Formatar dados

---

## 📚 Próximas Leituras

- **5 min:** `QUICKSTART.md`
- **30 min:** `GUIA_CLASSES_PASCAL.md`
- **Exemplos:** `ExemploADMCloudAPI.pas`
- **Form Pronto:** `FormExemploIntegracao.pas`

---

## 🔐 Autenticação

Já vem configurada com as credenciais padrão. Para alterar:

```pascal
LHelper.ConfigurarCredenciais('novo_usuario', 'nova_senha');
```

---

## ⏱️ Timeout

Padrão: 30 segundos. Para alterar:

```pascal
LHelper.ConfigurarTimeout(60000); // 60 segundos
```

---

## 🐛 Erro?

Verifique:

```pascal
WriteLn('Erro: ' + LHelper.GetUltimoErro);
WriteLn('Status: ' + IntToStr(LHelper.GetUltimoStatusCode));
```

---

**Pronto para começar! 🎉**
