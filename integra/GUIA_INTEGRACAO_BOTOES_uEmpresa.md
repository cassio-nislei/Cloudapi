# 🔘 Integração de Botões de API em uEmpresa.pas e uEmpresa.dfm

## 📋 4 Botões a Serem Adicionados

1. **Validar Passport** - Valida a empresa via API
2. **Sincronizar Licença** - Sincroniza com gerenciador de licenças
3. **Validar Licença** - Valida a licença local
4. **Registrar Empresa** - Registra nova empresa na API

---

## ✅ PASSO 1: Adicionar uses em uEmpresa.pas

Localize a seção `uses` em uEmpresa.pas e adicione:

```pascal
uses
  // ... uses existentes ...
  uEmpresaLicencaManager;  // ← ADICIONAR ESTA LINHA
```

---

## ✅ PASSO 2: Adicionar Variáveis Privadas em uEmpresa.pas

Na seção `private` da classe `TfrmEmpresa`, adicione:

```pascal
private
  // ... outras variáveis ...
  FLicencaManager: TEmpresaLicencaManager;
  PanelBotoesAPI: TPanel;
  btnValidarPassport: TButton;
  btnSincronizar: TButton;
  btnValidarLicenca: TButton;
  btnRegistrarEmpresa: TButton;

  // Métodos para os botões
  procedure btnValidarPassportClick(Sender: TObject);
  procedure btnSincronizarClick(Sender: TObject);
  procedure btnValidarLicencaClick(Sender: TObject);
  procedure btnRegistrarEmpresaClick(Sender: TObject);
  procedure CriarBotoesAPI;
  procedure LicencaManagerLog(Sender: TObject; const AMsg: string);
```

---

## ✅ PASSO 3: Adicionar Code ao FormCreate

No método `FormCreate` de TfrmEmpresa, adicione ao final:

```pascal
procedure TfrmEmpresa.FormCreate(Sender: TObject);
begin
  // ... código existente ...

  // Inicializar gerenciador de licenças
  FLicencaManager := TEmpresaLicencaManager.Create(Self);
  FLicencaManager.OnLog := LicencaManagerLog;

  // Configurar versões (opcional)
  FLicencaManager.VersaoFBX := '1.0.0';
  FLicencaManager.VersaoPDV := '1.0.0';

  // Criar botões da API
  CriarBotoesAPI;
end;
```

---

## ✅ PASSO 4: Adicionar Code ao FormDestroy

No método `FormDestroy` de TfrmEmpresa, adicione:

```pascal
procedure TfrmEmpresa.FormDestroy(Sender: TObject);
begin
  if Assigned(FLicencaManager) then
    FLicencaManager.Free;

  // ... código existente ...
end;
```

---

## ✅ PASSO 5: Copiar Implementação dos Eventos

Copie todo o código da seção **SEÇÃO: EVENTOS DOS BOTÕES** do arquivo:
`IMPLEMENTACAO_BOTOES_uEmpresa.pas`

E cole em `uEmpresa.pas`, no final da implementação (antes do `end.` final).

---

## ✅ PASSO 6: Copiar Método de Criar Botões

Copie o método `CriarBotoesAPI` do arquivo:
`IMPLEMENTACAO_BOTOES_uEmpresa.pas`

E cole em `uEmpresa.pas` (pode ser logo após o destructor).

---

## ✅ PASSO 7: Copiar Método de Log

Copie o método `LicencaManagerLog` do arquivo:
`IMPLEMENTACAO_BOTOES_uEmpresa.pas`

E cole em `uEmpresa.pas`.

---

## ✅ PASSO 8: Adicionar Componentes ao DFM

Abra `uEmpresa.dfm` no editor de text (ou use Object Inspector):

**Opção A: Usando Panel (Recomendado)**

Adicione ao final do arquivo DFM:

```
object PanelBotoesAPI: TPanel
  Left = 0
  Top = 652
  Width = 800
  Height = 50
  Align = alBottom
  BevelOuter = bvNone
  Color = clBtnFace
  TabOrder = 20
  object btnValidarPassport: TButton
    Left = 10
    Top = 10
    Width = 150
    Height = 30
    Caption = 'Validar Passport'
    TabOrder = 0
    OnClick = btnValidarPassportClick
  end
  object btnSincronizar: TButton
    Left = 170
    Top = 10
    Width = 150
    Height = 30
    Caption = 'Sincronizar Licen'#231'a'
    TabOrder = 1
    OnClick = btnSincronizarClick
  end
  object btnValidarLicenca: TButton
    Left = 330
    Top = 10
    Width = 150
    Height = 30
    Caption = 'Validar Licen'#231'a'
    TabOrder = 2
    OnClick = btnValidarLicencaClick
  end
  object btnRegistrarEmpresa: TButton
    Left = 490
    Top = 10
    Width = 150
    Height = 30
    Caption = 'Registrar Empresa'
    TabOrder = 3
    OnClick = btnRegistrarEmpresaClick
  end
end
```

**Opção B: Usando ToolBar (Mais moderno)**

Se preferir uma ToolBar em vez de Panel, use o código da seção:
`SE PREFERIR USAR TOOLBAR AO INVÉS DE PANEL`

---

## 🧪 TESTE DE COMPILAÇÃO

1. Abra o projeto em Delphi
2. Compile (Ctrl+Shift+F9)
3. Se houver erros, verifique:
   - ✓ `uEmpresaLicencaManager` foi adicionado aos uses
   - ✓ Todos os methods foram copiados corretamente
   - ✓ Nomes de campos do qry estão corretos

---

## 📊 Estrutura de Pastas com Novos Arquivos

```
integra/
├── uEmpresa.pas ⭐ (MODIFICAR)
├── uEmpresa.dfm ⭐ (MODIFICAR)
├── uEmpresaLicencaManager.pas ✅ (já existe)
├── IMPLEMENTACAO_BOTOES_uEmpresa.pas ← REFERÊNCIA (copiar daqui)
└── IMPLEMENTACAO_BOTOES_uEmpresa.dfm ← REFERÊNCIA (copiar daqui)
```

---

## 🎯 O Que Cada Botão Faz

### 1️⃣ Validar Passport

```
Função: Valida a empresa via GET /passport
Entrada: CNPJ do formulário
Saída: Mensagem de sucesso/erro com GUID e Hostname
```

### 2️⃣ Sincronizar Licença

```
Função: Sincroniza com gerenciador de licenças
Entrada: Dados da empresa e GUID
Saída: Mensagem com data/hora da sincronização
Tolerância: 7 dias sem conexão (automático)
```

### 3️⃣ Validar Licença

```
Função: Valida a licença local (validade, bloqueio, NSERIE, NTERM)
Entrada: Dados armazenados localmente
Saída: Mensagem indicando se licença é válida
```

### 4️⃣ Registrar Empresa

```
Função: Registra nova empresa na API
Entrada: Campos do formulário (Razão Social, CNPJ, Email, etc)
Saída: Mensagem de sucesso e sincronização automática
Validação: Verifica campos obrigatórios
```

---

## 💡 Personalizações Opcionais

### Adicionar Memo de Log

Se quiser ver os logs das operações, adicione um Memo:

```pascal
object mmoLog: TMemo
  Left = 0
  Top = 450
  Width = 800
  Height = 200
  Align = alClient
  ReadOnly = True
  ScrollBars = ssBoth
  TabOrder = 19
end
```

E modifique o método `LicencaManagerLog`:

```pascal
procedure TfrmEmpresa.LicencaManagerLog(Sender: TObject; const AMsg: string);
begin
  if Assigned(mmoLog) then
    mmoLog.Lines.Add(AMsg);
end;
```

### Adicionar Barra de Status

Adicione uma StatusBar para feedback visual:

```pascal
object StatusBar1: TStatusBar
  Left = 0
  Top = 700
  Width = 800
  Height = 19
  Panels = <
    item
      Width = 50
    end
    item
      Width = 50
    end>
end
```

---

## ✅ Checklist Final

- [ ] Adicionado `uEmpresaLicencaManager` aos uses
- [ ] Adicionadas variáveis privadas (FLicencaManager, botões)
- [ ] Adicionado FormCreate com inicialização
- [ ] Adicionado FormDestroy com limpeza
- [ ] Copiados todos os métodos de click
- [ ] Copiado método CriarBotoesAPI
- [ ] Copiado método LicencaManagerLog
- [ ] Adicionados componentes ao DFM
- [ ] Projeto compila sem erros
- [ ] Botões aparecem no form
- [ ] Botões funcionam ao clicar

---

## 🚀 Próximas Funcionalidades Opcionais

1. **Adicionar ícones aos botões** - ImageList com ícones de API
2. **Adicionar atalhos de teclado** - F1=Passport, F2=Sync, etc
3. **Adicionar animações** - Progress bar durante operações
4. **Salvar histórico** - Log persistente em arquivo
5. **Dashboard** - Painel visual com status de cada validação

---

## 📞 Dúvidas?

Se houver erro ao compilar:

1. Verifique nomes dos campos: `qryEmpresaCNPJ`, `qryEmpresaRAZAO`, etc
2. Certifique-se de que `uEmpresaLicencaManager` está criado
3. Verifique se todos os métodos foram copiados
4. Compile com Ctrl+Shift+F9 para limpeza total
