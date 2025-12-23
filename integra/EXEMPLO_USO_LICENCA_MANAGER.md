# Exemplo de Uso: TEmpresaLicencaManager com Novas Funcionalidades

## 1. Inicialização em TDataModule ou Form Principal

```pascal
procedure TfrmPrincipal.FormCreate(Sender: TObject);
begin
  // Criar instância do gerenciador de licenças
  FLicencaManager := TEmpresaLicencaManager.Create(Self);

  // Configurar callbacks de eventos
  FLicencaManager.OnLog := LicencaManagerLog;
  FLicencaManager.OnStatusChange := LicencaManagerStatusChange;
  FLicencaManager.OnBeforeSync := LicencaManagerBeforeSync;
  FLicencaManager.OnAfterSync := LicencaManagerAfterSync;
  FLicencaManager.OnUpdateStatusBar := LicencaManagerUpdateStatusBar;

  // Configurar versões de software
  FLicencaManager.VersaoFBX := '1.0.5';
  FLicencaManager.VersaoPDV := '2.3.0';

  // Configurar tolerância de dias sem conexão (padrão é 7)
  FLicencaManager.DiasToleranciaCache := 7;

  // Habilitar sincronização automática a cada 15 minutos
  FLicencaManager.AutoSync := True;
  FLicencaManager.AutoSyncInterval := 15 * 60 * 1000; // 15 minutos

  Log('Sistema licenciado iniciado. GUID: ' + FLicencaManager.MachineGUID);
end;
```

---

## 2. Implementação dos Eventos

### Evento de Log

```pascal
procedure TfrmPrincipal.LicencaManagerLog(Sender: TObject; const AMsg: string);
begin
  // Registrar em um Memo ou arquivo
  mmLog.Lines.Add(AMsg);

  // Ou salvar em arquivo
  // AppendToFile('Licenca.log', AMsg);
end;
```

### Evento de Mudança de Status

```pascal
procedure TfrmPrincipal.LicencaManagerStatusChange(Sender: TObject;
  AStatus: TLicenseStatus; const ADetail: string);
begin
  case AStatus of
    lsOk:
      begin
        StatusBar1.Panels[0].Text := 'Status: ✓ OK';
        StatusBar1.Panels[0].ParentFont.Color := clGreen;
      end;
    lsSemEmpresa:
      begin
        StatusBar1.Panels[0].Text := 'Status: ✗ Sem Empresa';
        StatusBar1.Panels[0].ParentFont.Color := clRed;
      end;
    lsLicencaVencida:
      begin
        StatusBar1.Panels[0].Text := 'Status: ✗ Licença Vencida';
        StatusBar1.Panels[0].ParentFont.Color := clRed;
        ShowMessage('Licença vencida! ' + ADetail);
      end;
    lsBloqueado:
      begin
        StatusBar1.Panels[0].Text := 'Status: ✗ Bloqueado';
        StatusBar1.Panels[0].ParentFont.Color := clRed;
        ShowMessage('Sistema bloqueado! ' + ADetail);
      end;
    lsSemConexaoWeb:
      begin
        StatusBar1.Panels[0].Text := 'Status: ⚠ Sem Conexão (cache)';
        StatusBar1.Panels[0].ParentFont.Color := clOlive;
        Log('Aviso: ' + ADetail);
      end;
    lsErroNSerie:
      begin
        StatusBar1.Panels[0].Text := 'Status: ✗ Serial Inválido';
        StatusBar1.Panels[0].ParentFont.Color := clRed;
        ShowMessage('Erro de validação de série: ' + ADetail);
      end;
    lsErroTerminal:
      begin
        StatusBar1.Panels[0].Text := 'Status: ✗ Terminais Excedidos';
        StatusBar1.Panels[0].ParentFont.Color := clRed;
        ShowMessage('Limite de terminais excedido: ' + ADetail);
      end;
  end;
end;
```

### Evento Antes de Sincronizar

```pascal
procedure TfrmPrincipal.LicencaManagerBeforeSync(Sender: TObject; var Cancel: Boolean);
begin
  Log('Iniciando sincronização com servidor...');

  // Pode cancelar se necessário:
  // Cancel := True;
end;
```

### Evento Após Sincronizar

```pascal
procedure TfrmPrincipal.LicencaManagerAfterSync(Sender: TObject; AStatus: TLicenseStatus);
begin
  case AStatus of
    lsOk:
      Log('✓ Sincronização bem-sucedida!');
    lsSemConexaoWeb:
      Log('⚠ Usando cache local (sem conexão com servidor)');
    else
      Log('✗ Falha na sincronização: ' + IntToStr(Ord(AStatus)));
  end;
end;
```

### Evento de Atualizar StatusBar

```pascal
procedure TfrmPrincipal.LicencaManagerUpdateStatusBar(Sender: TObject;
  const APanel3Text, APanel5Text: string);
begin
  StatusBar1.Panels[2].Text := APanel3Text;   // Terminais
  StatusBar1.Panels[4].Text := APanel5Text;   // Licença válida até
end;
```

---

## 3. Validação Inicial no Startup

```pascal
procedure TfrmPrincipal.FormShow(Sender: TObject);
begin
  try
    // Garantir que empresa está cadastrada
    FLicencaManager.InicializarEmpresa;

    // Validar licença local
    if not FLicencaManager.ValidarLicencaAtual then
    begin
      ShowMessage('Falha na validação de licença!');
      Close;
      Exit;
    end;

    // Sincronizar com servidor
    FLicencaManager.SincronizarComGerenciadorLicenca;

    Log('Sistema validado e pronto para uso.');
  except
    on E: Exception do
    begin
      ShowMessage('Erro ao validar sistema: ' + E.Message);
      Close;
    end;
  end;
end;
```

---

## 4. Sincronização Manual (Botão)

```pascal
procedure TfrmPrincipal.btnSincronizarClick(Sender: TObject);
begin
  btnSincronizar.Enabled := False;
  try
    Log('Sincronização manual iniciada...');

    if FLicencaManager.SincronizarComGerenciadorLicenca then
    begin
      ShowMessage('Sincronização bem-sucedida!');
      Log('Data última sincronização: ' + DateTimeToStr(FLicencaManager.UltimaSincronizacao));
    end
    else
    begin
      ShowMessage('Falha na sincronização. Verifique sua conexão.');
    end;
  finally
    btnSincronizar.Enabled := True;
  end;
end;
```

---

## 5. Consultar Status Atual

```pascal
procedure TfrmPrincipal.btnStatusClick(Sender: TObject);
var
  Info: string;
  DiasRestantes: Integer;
begin
  Info := '';
  Info := Info + 'GUID da Máquina: ' + FLicencaManager.MachineGUID + sLineBreak;
  Info := Info + 'CNPJ: ' + FLicencaManager.GetCNPJEmpresaAtual + sLineBreak;
  Info := Info + 'Hostname: ' + FLicencaManager.GetHostName + sLineBreak;
  Info := Info + sLineBreak;

  Info := Info + 'Última sincronização: ' +
    DateTimeToStr(FLicencaManager.UltimaSincronizacao) + sLineBreak;
  Info := Info + 'Último sucesso: ' +
    DateToStr(FLicencaManager.GetDataUltimoGetSucesso) + sLineBreak;
  Info := Info + 'Dias desde sucesso: ' +
    IntToStr(FLicencaManager.GetDiasUltimoGetSucesso) + sLineBreak;

  DiasRestantes := FLicencaManager.DiasToleranciaCache -
    FLicencaManager.GetDiasUltimoGetSucesso;
  Info := Info + 'Dias restantes de tolerância: ' + IntToStr(DiasRestantes) + sLineBreak;
  Info := Info + sLineBreak;

  Info := Info + 'Versão FBX: ' + FLicencaManager.VersaoFBX + sLineBreak;
  Info := Info + 'Versão PDV: ' + FLicencaManager.VersaoPDV + sLineBreak;

  ShowMessage(Info);
end;
```

---

## 6. Configurar Versões Dinamicamente

```pascal
procedure TfrmPrincipal.ConfigurarVersoesDeProducto;
var
  VersaoFBX, VersaoPDV: string;
begin
  // Obter versões do seu ini, registry ou const
  VersaoFBX := ObterVersaoFBX; // Sua função
  VersaoPDV := ObterVersaoPDV; // Sua função

  FLicencaManager.VersaoFBX := VersaoFBX;
  FLicencaManager.VersaoPDV := VersaoPDV;

  Log(Format('Versões configuradas: FBX=%s, PDV=%s', [VersaoFBX, VersaoPDV]));
end;
```

---

## 7. Alterar Tolerância de Dias Sem Conexão

```pascal
procedure TfrmPrincipal.AlterarTolerancia;
begin
  // Aumentar para 14 dias
  FLicencaManager.DiasToleranciaCache := 14;
  Log('Tolerância alterada para 14 dias.');

  // Ou deixar o usuário escolher
  FLicencaManager.DiasToleranciaCache := StrToIntDef(
    InputBox('Tolerância', 'Dias sem conexão (padrão 7):', '7'), 7);
end;
```

---

## 8. Registrar Nova Empresa Automaticamente

```pascal
procedure TfrmPrincipal.RegistrarEmpresa;
begin
  with FLicencaManager do
  begin
    if RegistrarEmpresaNoMySQL(
      'Minha Empresa LTDA',         // Nome
      'Minha Empresa',              // Fantasia
      '12.345.678/0001-90',         // CNPJ
      'João Silva',                 // Contato
      'joao@empresa.com.br',        // Email
      '(11) 3000-0000',             // Telefone
      '(11) 99999-9999',            // Celular (opcional)
      'Rua das Flores, 100',        // Endereço
      '100',                        // Número
      'Apto 201',                   // Complemento (opcional)
      'Centro',                     // Bairro
      'São Paulo',                  // Cidade
      'SP',                         // Estado
      '01000-000'                   // CEP
    ) then
    begin
      ShowMessage('Empresa registrada com sucesso!');
      SincronizarComGerenciadorLicenca;
    end
    else
    begin
      ShowMessage('Erro ao registrar empresa: ' + GetUltimoErro);
    end;
  end;
end;
```

---

## 9. Estrutura Completa em Form

```pascal
type
  TfrmPrincipal = class(TForm)
    StatusBar1: TStatusBar;
    Panel1: TPanel;
    mmLog: TMemo;
    btnSincronizar: TButton;
    btnStatus: TButton;
    btnRegistrarEmpresa: TButton;
    procedure FormCreate(Sender: TObject);
    procedure FormShow(Sender: TObject);
    procedure FormDestroy(Sender: TObject);
    procedure btnSincronizarClick(Sender: TObject);
    procedure btnStatusClick(Sender: TObject);
    procedure btnRegistrarEmpresaClick(Sender: TObject);
  private
    FLicencaManager: TEmpresaLicencaManager;
    procedure LicencaManagerLog(Sender: TObject; const AMsg: string);
    procedure LicencaManagerStatusChange(Sender: TObject; AStatus: TLicenseStatus;
      const ADetail: string);
    procedure LicencaManagerBeforeSync(Sender: TObject; var Cancel: Boolean);
    procedure LicencaManagerAfterSync(Sender: TObject; AStatus: TLicenseStatus);
    procedure LicencaManagerUpdateStatusBar(Sender: TObject;
      const APanel3Text, APanel5Text: string);
    procedure Log(const AMsg: string);
  end;

// ... implementação dos métodos como acima
```

---

## 10. Teste de Tolerância (Sem Conexão)

```pascal
procedure TfrmPrincipal.TestarTolerancia;
var
  i: Integer;
  DiasSimulados: Integer;
begin
  Log('Iniciando teste de tolerância...');
  Log('Sincronizando com servidor (deve ter sucesso)...');

  FLicencaManager.SincronizarComGerenciadorLicenca;
  Log('Última sincronização bem-sucedida: ' +
    DateToStr(FLicencaManager.GetDataUltimoGetSucesso));

  // Simular N dias passados (sem fazer nada)
  DiasSimulados := 3;
  Log(Format('Simulando %d dias sem sincronização...', [DiasSimulados]));
  Log('Sistema deve continuar funcionando (dentro da tolerância)...');

  // Tentar sincronizar (vai falhar por rede ou API, mas deve aceitar por cache)
  Log('Tentando sincronizar (vai falhar, mas deve usar cache)...');
  if FLicencaManager.SincronizarComGerenciadorLicenca then
    Log('✓ Sistema aceitou sincronização (cache em vigor)')
  else
    Log('✗ Sincronização bloqueada (período de tolerância expirou)');
end;
```

---

## Resumo de Uso

| Situação                | Código                                              |
| ----------------------- | --------------------------------------------------- |
| Configurar versões      | `FLicencaManager.VersaoFBX := '1.0.5';`             |
| Habilitar auto-sync     | `FLicencaManager.AutoSync := True;`                 |
| Sincronizar manualmente | `FLicencaManager.SincronizarComGerenciadorLicenca;` |
| Obter GUID              | `ShowMessage(FLicencaManager.MachineGUID);`         |
| Alterar tolerância      | `FLicencaManager.DiasToleranciaCache := 10;`        |
| Registrar empresa       | `FLicencaManager.RegistrarEmpresaNoMySQL(...);`     |
| Validar licença         | `if FLicencaManager.ValidarLicencaAtual then...`    |
