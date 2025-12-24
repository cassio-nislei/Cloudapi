# EXEMPLOS PRÁTICOS - USANDO AS CLASSES PASCAL

**Data:** 24/12/2024  
**Objetivo:** Demonstrar casos reais de uso

---

## 📖 ÍNDICE

1. Exemplo 1: Validar Passport Simples
2. Exemplo 2: Registrar Empresa Completo
3. Exemplo 3: Sincronização Automática
4. Exemplo 4: Validação de Licença
5. Exemplo 5: Tratamento de Erros
6. Exemplo 6: Integração com Form
7. Exemplo 7: Cache Local e Offline
8. Exemplo 8: Logging e Debug

---

## 📝 EXEMPLO 1: Validar Passport Simples

### Cenário

Validar se um CNPJ tem acesso ao sistema

### Código

```pascal
uses ADMCloudAPIHelper, ADMCloudConsts;

procedure ValidarPassportSimples;
var
  LHelper: TADMCloudHelper;
  LCNPJEmpresa: string;
  LHostName: string;
  LGUID: string;
begin
  // Dados de entrada
  LCNPJEmpresa := '34028316000166';  // CNPJ da empresa
  LHostName := GetComputerName;       // Nome do computador
  LGUID := ObterGUIDMaquina;          // GUID único

  // Criar helper
  LHelper := TADMCloudHelper.Create(ADMCloud_URL_PROD);
  try
    // Validar
    if LHelper.ValidarPassport(LCNPJEmpresa, LHostName, LGUID) then
    begin
      // Sucesso!
      ShowMessage(
        'Passport válido!' + #13#10 +
        'Status: ' + BoolToStr(LHelper.GetPassportStatus, True) + #13#10 +
        'Mensagem: ' + LHelper.GetPassportMensagem
      );
    end
    else
    begin
      // Falha
      ShowMessage(
        'Erro ao validar passport:' + #13#10 +
        LHelper.GetUltimoErro
      );
    end;
  finally
    LHelper.Free;
  end;
end;
```

### Output Esperado

```
Passport válido!
Status: True
Mensagem: Acesso concedido
```

---

## 📝 EXEMPLO 2: Registrar Empresa Completo

### Cenário

Registrar uma nova empresa no sistema

### Código

```pascal
uses ADMCloudAPIHelper, ADMCloudConsts;

procedure RegistrarEmpresaCompleta;
var
  LHelper: TADMCloudHelper;
  LResult: Boolean;
begin
  // Criar helper com API
  LHelper := TADMCloudHelper.Create(ADMCloud_URL_PROD);
  try
    // Registrar empresa com todos os dados
    LResult := LHelper.RegistrarCliente(
      'Papion Solutions Ltda',              // Nome/Razão Social
      'Papion',                              // Fantasia
      '34.028.316/0001-66',                  // CNPJ
      'João Silva',                          // Contato
      'contato@papion.com.br',              // Email
      '(11) 3333-4444',                     // Telefone
      '(11) 99999-8888',                    // Celular
      'Rua das Flores, 123',                // Endereço
      '123',                                 // Número
      'Apto 456',                           // Complemento
      'Centro',                             // Bairro
      'São Paulo',                          // Cidade
      'SP',                                  // Estado
      '01310-100'                           // CEP
    );

    if LResult then
    begin
      ShowMessage(
        'Empresa registrada com sucesso!' + #13#10 +
        'Status: ' + LHelper.GetRegistroStatus + #13#10 +
        'Mensagem: ' + LHelper.GetRegistroMensagem + #13#10 +
        'Dados: ' + LHelper.GetRegistroData
      );
    end
    else
    begin
      ShowMessage('Erro ao registrar: ' + LHelper.GetUltimoErro);
    end;
  finally
    LHelper.Free;
  end;
end;
```

### Requisição HTTP Gerada

```
POST http://104.234.173.105:7010/api/v1/registro
Authorization: Basic YXBpX2Zyb250Ym94OmFwaV9GQlh6eWxYSTBabHVuZUYxbHQzcnd4WXpzZmF5cDBjQ3JLQ0dYMHJn
Content-Type: application/json

{
  "nome": "Papion Solutions Ltda",
  "fantasia": "Papion",
  "cgc": "34028316000166",
  "contato": "João Silva",
  "email": "contato@papion.com.br",
  "telefone": "(11) 3333-4444",
  "celular": "(11) 99999-8888",
  "endereco": "Rua das Flores, 123",
  "numero": "123",
  "complemento": "Apto 456",
  "bairro": "Centro",
  "cidade": "São Paulo",
  "estado": "SP",
  "cep": "01310100"
}
```

### Response Esperado

```json
{
  "status": "ok",
  "msg": "Empresa registrada com sucesso",
  "data": {
    "id": 123,
    "cgc": "34028316000166",
    "nome": "Papion Solutions Ltda",
    "data_criacao": "2024-12-24T10:30:00Z"
  }
}
```

---

## 📝 EXEMPLO 3: Sincronização Automática

### Cenário

Sincronizar licença a cada 5 minutos automaticamente

### Código

```pascal
uses uEmpresaLicencaManager;

// Em seu DataModule ou Form principal
var
  EmpresaLicenca: TEmpresaLicencaManager;

procedure TdmApplication.DataModuleCreate(Sender: TObject);
begin
  // Criar o gerenciador
  EmpresaLicenca := TEmpresaLicencaManager.Create(Application);

  // Configurar URL e credenciais
  EmpresaLicenca.ConfigurarURLAPI('http://104.234.173.105:7010/api/v1');
  EmpresaLicenca.ConfigurarCredenciaisAPI('api_frontbox', 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg');

  // Configurar auto-sync
  EmpresaLicenca.AutoSync := True;
  EmpresaLicenca.AutoSyncInterval := 300000;  // 5 minutos

  // Eventos
  EmpresaLicenca.OnLog := LicenseLog;
  EmpresaLicenca.OnStatusChange := LicenseStatusChange;
  EmpresaLicenca.OnBeforeSync := LicenseBeforeSync;
  EmpresaLicenca.OnAfterSync := LicenseAfterSync;

  // Tolerância offline
  EmpresaLicenca.DiasToleranciaCache := 7;

  // Iniciar primeira sincronização
  EmpresaLicenca.SincronizacaoPeriodica;
end;

procedure TdmApplication.LicenseLog(Sender: TObject; const AMsg: string);
begin
  // Logar mensagens
  OutputDebugString(PChar('[License] ' + AMsg));

  // Ou enviar para arquivo de log
  // AppendToLogFile(AMsg);
end;

procedure TdmApplication.LicenseStatusChange(Sender: TObject;
  AStatus: TLicenseStatus; const ADetail: string);
begin
  // Atualizar UI
  case AStatus of
    lsOk:
    begin
      frmPrincipal.StatusBar1.SimpleText := '✓ Licença OK';
      frmPrincipal.StatusBar1.Font.Color := clGreen;
    end;

    lsLicencaVencida:
    begin
      frmPrincipal.StatusBar1.SimpleText := '✗ Licença Vencida';
      frmPrincipal.StatusBar1.Font.Color := clRed;
      BloquearAcessoAoSistema;
    end;

    lsBloqueado:
    begin
      frmPrincipal.StatusBar1.SimpleText := '✗ Acesso Bloqueado';
      frmPrincipal.StatusBar1.Font.Color := clRed;
      BloquearAcessoAoSistema;
    end;

    lsSemConexaoWeb:
    begin
      frmPrincipal.StatusBar1.SimpleText := '⚠ Sem Conexão (usando cache)';
      frmPrincipal.StatusBar1.Font.Color := clOrange;
    end;

    else
    begin
      frmPrincipal.StatusBar1.SimpleText := '? Erro: ' + ADetail;
      frmPrincipal.StatusBar1.Font.Color := clMaroon;
    end;
  end;
end;

procedure TdmApplication.LicenseBeforeSync(Sender: TObject; var Cancel: Boolean);
begin
  // Pode cancelar a sincronização aqui se necessário
  Cancel := False; // False = continuar, True = cancelar

  // Log
  OutputDebugString('Iniciando sincronização...');
end;

procedure TdmApplication.LicenseAfterSync(Sender: TObject; AStatus: TLicenseStatus);
begin
  // Chamado após sincronização
  OutputDebugString(PChar('Sincronização finalizada: ' + IntToStr(Ord(AStatus))));
end;

procedure TdmApplication.BloquearAcessoAoSistema;
begin
  // Desabilitar funcionalidades críticas
  frmPrincipal.btnVendas.Enabled := False;
  frmPrincipal.btnEstoque.Enabled := False;
  frmPrincipal.btnFinanceiro.Enabled := False;

  ShowMessage('Acesso ao sistema bloqueado. Contate o suporte.');
end;

procedure TdmApplication.DataModuleDestroy(Sender: TObject);
begin
  // Limpar
  if Assigned(EmpresaLicenca) then
    EmpresaLicenca.Free;
end;
```

---

## 📝 EXEMPLO 4: Validação de Licença

### Cenário

Verificar vários aspectos da licença

### Código

```pascal
uses uEmpresaLicencaManager;

procedure ValidarLicencaCompleta(var LicenseStatus: TLicenseStatus);
var
  LManager: TEmpresaLicencaManager;
  LMsgErro: string;
begin
  LManager := TEmpresaLicencaManager.Create(Application);
  try
    LManager.ConfigurarURLAPI('http://104.234.173.105:7010/api/v1');

    // 1. Validar se está vencida
    if LManager.LicencaEstaVencida(LMsgErro) then
    begin
      ShowMessage('Licença vencida: ' + LMsgErro);
      LicenseStatus := lsLicencaVencida;
      Exit;
    end;

    // 2. Validar se está bloqueada
    if LManager.LicencaEstaBloqueada(LMsgErro) then
    begin
      ShowMessage('Licença bloqueada: ' + LMsgErro);
      LicenseStatus := lsBloqueado;
      Exit;
    end;

    // 3. Validar NSerie
    if not LManager.ValidarNSerieAntiFraude then
    begin
      ShowMessage('NSerie inválida - possível fraude detectada');
      LicenseStatus := lsErroNSerie;
      Exit;
    end;

    // 4. Validar terminais
    if not LManager.ValidarTerminais then
    begin
      ShowMessage('Terminal não autorizado');
      LicenseStatus := lsErroTerminal;
      Exit;
    end;

    // 5. Validação geral
    if not LManager.ValidarLicencaAtual then
    begin
      ShowMessage('Licença geral inválida: ' + LManager.GetUltimoErro);
      LicenseStatus := lsErroGeral;
      Exit;
    end;

    // Sucesso!
    ShowMessage('Todas as validações passaram! Licença OK.');
    LicenseStatus := lsOk;

  finally
    LManager.Free;
  end;
end;
```

---

## 📝 EXEMPLO 5: Tratamento de Erros Robusto

### Cenário

Tratar diferentes tipos de erro

### Código

```pascal
uses ADMCloudAPI, ADMCloudConsts, SysUtils;

procedure TratarErrosAPI;
var
  LAPI: TADMCloudAPI;
  LResponse: string;
begin
  LAPI := TADMCloudAPI.Create('http://104.234.173.105:7010/api/v1');
  try
    // Configurar credentials
    LAPI.ConfigurarCredenciais('api_frontbox', 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg');
    LAPI.ConfigurarTimeout(30000);

    // Tentar requisição
    if not LAPI.RequisicaoGET('passport?cgc=34028316000166&hostname=PC-TEST&guid=test-guid', LResponse) then
    begin
      // Tratamento de erro específico
      case LAPI.GetUltimoStatusCode of
        0:
          ShowMessage('Erro de conexão - Verifique internet' + #13#10 + LAPI.GetUltimoErro);

        HTTP_BAD_REQUEST:
          ShowMessage('Requisição inválida - Verifique parâmetros');

        HTTP_UNAUTHORIZED:
          ShowMessage('Não autorizado - Verifique credenciais');

        HTTP_FORBIDDEN:
          ShowMessage('Acesso proibido - Licença bloqueada?');

        HTTP_NOT_FOUND:
          ShowMessage('Recurso não encontrado - Verifique URL');

        HTTP_INTERNAL_ERROR:
          ShowMessage('Erro interno do servidor - Contate suporte');

        HTTP_SERVICE_UNAVAILABLE:
          ShowMessage('Serviço indisponível - Tente novamente mais tarde');

        else
          ShowMessage(
            'Erro desconhecido (Status ' + IntToStr(LAPI.GetUltimoStatusCode) + ')' + #13#10 +
            LAPI.GetUltimoErro
          );
      end;
    end
    else
    begin
      ShowMessage('Requisição bem-sucedida!' + #13#10 + LResponse);
    end;

  except
    on E: Exception do
    begin
      ShowMessage('Exceção: ' + E.Message);
    end;
  end;
end;
```

---

## 📝 EXEMPLO 6: Integração com Form

### Cenário

Integrar validação em um Form de login

### Código

```pascal
// Em TfrmLogin
uses ADMCloudAPIHelper, ADMCloudConsts, uEmpresaLicencaManager;

procedure TfrmLogin.BtnEntrarClick(Sender: TObject);
var
  LCNPJ: string;
  LHostname: string;
  LGUID: string;
  LHelper: TADMCloudHelper;
  LManager: TEmpresaLicencaManager;
  LLicenseOK: Boolean;
begin
  // Obter dados
  LCNPJ := edtCNPJ.Text;
  LHostname := GetComputerName;

  // Obter GUID da máquina
  LManager := TEmpresaLicencaManager.Create(Application);
  try
    LGUID := LManager.GetMachineGUID;
  finally
    LManager.Free;
  end;

  // Mostrar progresso
  btnEntrar.Enabled := False;
  lblStatus.Caption := 'Validando licença...';
  lblStatus.Font.Color := clBlack;
  Application.ProcessMessages;

  // Criar helper
  LHelper := TADMCloudHelper.Create(ADMCloud_URL_PROD);
  try
    // Validar passport
    LLicenseOK := LHelper.ValidarPassport(LCNPJ, LHostname, LGUID);

    if LLicenseOK then
    begin
      // Licença OK - permitir login
      lblStatus.Caption := '✓ Licença válida - Entrando...';
      lblStatus.Font.Color := clGreen;
      Application.ProcessMessages;

      Sleep(1000); // Mostrar mensagem por 1 segundo

      // Fechar login e abrir aplicação
      ModalResult := mrOk;
    end
    else
    begin
      // Licença inválida
      lblStatus.Caption := '✗ Licença inválida!';
      lblStatus.Font.Color := clRed;

      ShowMessage(
        'Não é possível acessar o sistema.' + #13#10#13#10 +
        'Motivo: ' + LHelper.GetPassportMensagem + #13#10 +
        'Erro: ' + LHelper.GetUltimoErro
      );
    end;

  finally
    LHelper.Free;
    btnEntrar.Enabled := True;
  end;
end;
```

---

## 📝 EXEMPLO 7: Cache Local e Offline

### Cenário

Usar cache quando rede estiver indisponível

### Código

```pascal
uses uEmpresaLicencaManager;

procedure TesteCacheOffline;
var
  LManager: TEmpresaLicencaManager;
  LStatus: TLicenseStatus;
begin
  LManager := TEmpresaLicencaManager.Create(Application);
  try
    LManager.ConfigurarURLAPI('http://104.234.173.105:7010/api/v1');
    LManager.DiasToleranciaCache := 7;

    // Cenário 1: Com conexão
    ShowMessage('Cenário 1: Com conexão à internet');
    if LManager.SincronizarComGerenciadorLicenca then
    begin
      ShowMessage('Sincronizado com sucesso!');
      ShowMessage('Data última sincronização: ' + DateTimeToStr(LManager.UltimaSincronizacao));
    end;

    // Cenário 2: Sem conexão (dias dentro do limite)
    ShowMessage('Cenário 2: Desligar rede e tentar validar dentro de 7 dias...');
    if LManager.ValidarLicencaAtual then
    begin
      ShowMessage('Usando cache local - Licença OK');
      ShowMessage('Dias sem sincronização: ' + IntToStr(LManager.GetDiasUltimoGetSucesso));
    end;

    // Cenário 3: Sem conexão (dias acima do limite)
    ShowMessage('Cenário 3: Após 8+ dias sem sincronização...');
    // (simulado)
    if not LManager.ValidarLicencaAtual then
    begin
      ShowMessage('Cache expirou - Licença bloqueada');
    end;

  finally
    LManager.Free;
  end;
end;
```

---

## 📝 EXEMPLO 8: Logging e Debug

### Cenário

Adicionar logs detalhados para debug

### Código

```pascal
uses uEmpresaLicencaManager, SysUtils, Classes;

// Arquivo de log
var
  GLogFile: string;

procedure InitializeLogging;
begin
  GLogFile := ExtractFilePath(Application.ExeName) + 'License.log';
end;

procedure AppendLog(const AMsg: string);
var
  LF: TextFile;
begin
  try
    AssignFile(LF, GLogFile);
    if FileExists(GLogFile) then
      Append(LF)
    else
      Rewrite(LF);

    WriteLn(LF, FormatDateTime('[dd/mm/yyyy hh:mm:ss] ', Now) + AMsg);
    CloseFile(LF);
  except
    // Silenciosamente falhar se não conseguir escrever
  end;
end;

procedure TfrmPrincipal.InitializeLicenseWithLogging;
var
  LManager: TEmpresaLicencaManager;
begin
  InitializeLogging;

  LManager := TEmpresaLicencaManager.Create(Application);
  LManager.ConfigurarURLAPI('http://104.234.173.105:7010/api/v1');

  // Logging de eventos
  LManager.OnLog := procedure(Sender: TObject; const AMsg: string)
  begin
    AppendLog('LOG: ' + AMsg);
    OutputDebugString(PChar(AMsg));
  end;

  LManager.OnBeforeSync := procedure(Sender: TObject; var Cancel: Boolean)
  begin
    AppendLog('SYNC: Iniciando sincronização...');
  end;

  LManager.OnAfterSync := procedure(Sender: TObject; AStatus: TLicenseStatus)
  begin
    AppendLog('SYNC: Finalizada com status ' + IntToStr(Ord(AStatus)));
  end;

  LManager.OnStatusChange := procedure(Sender: TObject; AStatus: TLicenseStatus; const ADetail: string)
  begin
    case AStatus of
      lsOk: AppendLog('STATUS: Licença OK');
      lsLicencaVencida: AppendLog('STATUS: VENCIDA - ' + ADetail);
      lsBloqueado: AppendLog('STATUS: BLOQUEADO - ' + ADetail);
      lsSemConexaoWeb: AppendLog('STATUS: Sem conexão web');
      else AppendLog('STATUS: Erro - ' + ADetail);
    end;
  end;

  // Iniciar auto-sync
  LManager.AutoSync := True;
  LManager.AutoSyncInterval := 60000;
end;

// Para visualizar o arquivo de log
procedure AbrirArquivoLog;
begin
  ShellExecute(GetDesktopWindow, 'open', PChar(GLogFile), nil, nil, SW_SHOW);
end;
```

---

## 🎯 PRÓXIMOS PASSOS

1. Adaptar os exemplos para sua aplicação específica
2. Testar com a URL nova: `http://104.234.173.105:7010/api/v1`
3. Implementar logging para debug
4. Testar sincronização periódica
5. Validar cache offline

---

**Exemplos preparados: 24/12/2024** ✅
