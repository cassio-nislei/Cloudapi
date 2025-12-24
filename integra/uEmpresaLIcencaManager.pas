unit uEmpresaLicencaManager;

interface

uses
  System.SysUtils, System.Classes, System.DateUtils, System.IOUtils, System.JSON,
  Vcl.ExtCtrls, Vcl.Forms, Vcl.StdCtrls, Vcl.DBCtrls,
  Data.DB,
  uDados, uDadosWeb, //uPrincipal,
  ACBrConsultaCNPJ, // Componente em TDadosWeb (DataModule) - DadosWeb.ACBrConsultaCNPJ1
  serial,           // SerialNum(FDrive: String) - ajustado em GetMachineSerial
  ADMCloudAPI,      // API REST para validação de licenças
  ADMCloudConsts,
  ADMCloudAPIHelper, // Helper com métodos de conveniência
  Windows, Registry; // Para GUID e cache de máquina (como em uDMPassport)

type
  TLicenseStatus = (
    lsOk,
    lsSemEmpresa,
    lsLicencaVencida,
    lsBloqueado,
    lsSemConexaoWeb,
    lsErroNSerie,
    lsErroTerminal,
    lsErroGeral
  );

  TOnLogEvent = procedure(Sender: TObject; const AMsg: string) of object;
  TOnStatusChangeEvent = procedure(Sender: TObject; AStatus: TLicenseStatus;
    const ADetail: string) of object;
  TOnBeforeSyncEvent = procedure(Sender: TObject; var Cancel: Boolean) of object;
  TOnAfterSyncEvent = procedure(Sender: TObject; AStatus: TLicenseStatus) of object;
  TOnUpdateStatusBarEvent = procedure(Sender: TObject; const APanel3Text, APanel5Text: string) of object;

  TEmpresaLicencaManager = class(TComponent)
  private
    FOnLog: TOnLogEvent;
    FOnStatusChange: TOnStatusChangeEvent;
    FOnBeforeSync: TOnBeforeSyncEvent;
    FOnAfterSync: TOnAfterSyncEvent;
    FOnUpdateStatusBar: TOnUpdateStatusBarEvent;
    FLastStatus: TLicenseStatus;
    FUltimaSincronizacao: TDateTime;
    FAutoSync: Boolean;
    FAutoSyncInterval: Integer; // ms
    FTimer: TTimer;
    FAPIHelper: TADMCloudHelper;  // Helper API ADMCloud
    FURL_API: string;             // URL base da API
    FMachineGUID: string;          // GUID único da máquina (cache em Registry)
    FDiasToleranciaCache: Integer; // Dias de tolerância sem conexão à API (padrão: 7)
    FVersaoFBX: string;            // Versão do FBX para enviar na validação
    FVersaoPDV: string;            // Versão do PDV para enviar na validação
    FUltimoErro: string;           // Armazena última mensagem de erro



  public
    constructor Create(AOwner: TComponent); override;
    destructor Destroy; override;

    // ===== MÉTODOS NOVOS (de uDMPassport) =====
    function GetMachineGUID: String;        // Obtém GUID único da máquina (Registry)
    procedure SetDataUltimoGetSucesso;      // Salva timestamp do último sync bem-sucedido
    function GetDataUltimoGetSucesso: TDateTime; // Retorna data do último sync bem-sucedido
    function GetDiasUltimoGetSucesso: Integer;  // Retorna dias desde último sync bem-sucedido
    function Encrypt(const S: String; Key: Word): String;  // Criptografia simples XOR
    function Decrypt(const S: ShortString; Key: Word): String; // Descriptografia simples
    function GenerateMachineGUID: String;   // Gera novo GUID único
    function GetHostName: String;           // Obtém nome do computador (Windows API)

    // ===== MÉTODOS ORIGINAIS (mantidos) =====
    // 1) Garante que exista EMPRESA e integra com form uEmpresa
    procedure InicializarEmpresa;

    procedure AtualizarFormEmpresa;
    // 2) Preencher EMPRESA com ACBrConsultaCNPJ (pessoa jurídica)
    procedure PreencherEmpresaComACBr(const ACBr: TACBrConsultaCNPJ);

    // 3) Sincronizar com gerenciador de licenças (MySQL)
    function SincronizarComGerenciadorLicenca: Boolean;

    // 4) Rodar validações locais (licença, NTERM, NSERIE)
    function ValidarLicencaAtual: Boolean;


      procedure Log(const S: string);
    procedure ChangeStatus(AStatus: TLicenseStatus; const Detail: string = '');

    function GetCNPJEmpresaAtual: string;
    function GetTerminalAtual: string;
    function GetMachineSerial: string;
    function CarregarEmpresaDoMySQL(const CNPJ: string): Boolean;
    function VerificarCNPJNaAPI(const ACNPJ: string): Boolean;
    function RegistrarEmpresaNoMySQL(const ANome, AFantasia, ACNPJ, AContato, AEmail, ATelefone: string;
      const ACelular: string = ''; const AEndereco: string = ''; const ANumero: string = '';
      const AComplemento: string = ''; const ABairro: string = ''; const ACidade: string = '';
      const AEstado: string = ''; const ACEP: string = ''): Boolean;
    function ValidarPassportEmpresa(const ACNPJ, AHostname, AGUID: string): Boolean;

    function ValidarNSerieAntiFraude: Boolean;
    function ValidarTerminais: Boolean;
    function LicencaEstaVencida(out Msg: string): Boolean;
    function LicencaEstaBloqueada(out Msg: string): Boolean;

    procedure TimerSync(Sender: TObject);
    procedure AtualizaStatusBar;

    procedure SetAutoSync(const Value: Boolean);
    procedure SetAutoSyncInterval(const Value: Integer);

    // 5) Método que você pode chamar de Timer1 ou usar o Timer interno
    procedure SincronizacaoPeriodica;
    procedure PreencherFormEmpresa;

    // Configuração da API
    procedure ConfigurarURLAPI(const AURL: string);
    procedure ConfigurarCredenciaisAPI(const AUsername, APassword: string);
    function GetUltimoErro: string;
    function GetDebugInfo: string;

    property UltimaSincronizacao: TDateTime read FUltimaSincronizacao;
    property MachineGUID: string read GetMachineGUID;
    property DiasToleranciaCache: Integer read FDiasToleranciaCache write FDiasToleranciaCache;
    property VersaoFBX: string read FVersaoFBX write FVersaoFBX;
    property VersaoPDV: string read FVersaoPDV write FVersaoPDV;
  published
    property AutoSync: Boolean read FAutoSync write SetAutoSync;
    property AutoSyncInterval: Integer read FAutoSyncInterval write SetAutoSyncInterval;

    property OnLog: TOnLogEvent read FOnLog write FOnLog;
    property OnStatusChange: TOnStatusChangeEvent read FOnStatusChange write FOnStatusChange;
    property OnBeforeSync: TOnBeforeSyncEvent read FOnBeforeSync write FOnBeforeSync;
    property OnAfterSync: TOnAfterSyncEvent read FOnAfterSync write FOnAfterSync;
    property OnUpdateStatusBar: TOnUpdateStatusBarEvent read FOnUpdateStatusBar write FOnUpdateStatusBar;
  end;

var
  EmpresaLicencaManager: TEmpresaLicencaManager;

implementation

uses
  Dialogs, uEmpresa;

{ TEmpresaLicencaManager }

// ===== MÉTODOS DE MÁQUINA E CRIPTOGRAFIA (de uDMPassport) =====

function TEmpresaLicencaManager.GenerateMachineGUID: String;
var
 GUID: TGUID;
begin
  CreateGUID(GUID);
  Result := GUIDToString(GUID);
end;

function TEmpresaLicencaManager.GetHostName: String;
var
  Buffer: array[0..MAX_COMPUTERNAME_LENGTH + 1] of Char;
  Size: DWORD;
begin
  //retorna pela API do Windows
  Size := SizeOf(Buffer) div SizeOf(Buffer[0]);
  if GetComputerName(Buffer, Size) then
    Result := Buffer
  else
  begin
    //se nao funcionou, retorna por ENVIRONMENT
    Result := GetEnvironmentVariable('COMPUTERNAME');
    if Result = '' then
      Result := 'UNKNOW';
  end;
end;

function TEmpresaLicencaManager.GetMachineGUID: String;
var
  Registry: TRegistry;
begin
  Result := '';
  Registry := TRegistry.Create(KEY_READ or KEY_WRITE);
  try
    Registry.RootKey := HKEY_CURRENT_USER;
    if Registry.OpenKey('Software\is5\ADMCloud', True) then
    begin
      if Registry.ValueExists('GUID') then
        Result := Registry.ReadString('GUID')
      else
      begin
        Result := GenerateMachineGUID;
        Registry.WriteString('GUID', Result);
      end;
    end;
  finally
    Registry.Free;
  end;
end;

function TEmpresaLicencaManager.GetCNPJEmpresaAtual: string;
begin
  { Retornar CNPJ da empresa carregada em dados }
  if Assigned(dados) and Assigned(dados.qryEmpresa) then
  begin
    if dados.qryEmpresa.Active and not dados.qryEmpresa.IsEmpty then
      Result := dados.qryEmpresaCNPJ.AsString
    else
      Result := '';
  end
  else
    Result := '';
end;

function TEmpresaLicencaManager.GetTerminalAtual: string;
begin
  { Retornar hostname da máquina }
  Result := GetHostName;
  
  if Result = '' then
    Result := 'UNKNOW_TERMINAL';
end;

function TEmpresaLicencaManager.GetMachineSerial: string;
begin
  { Retornar identificador único da máquina - usar GUID (determinístico) }
  Result := GetMachineGUID;
  
  if Result = '' then
    Result := 'UNKNOWN_SERIAL';
end;

procedure TEmpresaLicencaManager.SetDataUltimoGetSucesso;
var
 LDC: String; //Last Date Checkin
 Registry: TRegistry;
 KEY: Word;
begin
  Registry := TRegistry.Create(KEY_READ or KEY_WRITE);
  try
    KEY := 2024; // Constante como em uDMPassport
    LDC := Encrypt(FormatDateTime('dd/MM/yyyy', NOW), KEY);

    Registry.RootKey := HKEY_CURRENT_USER;
    Registry.OpenKey('Software\is5\ADMCloud', True);
    Registry.WriteString('LDC', LDC);
    Log('Data do último sincronização bem-sucedida armazenada em Registry.');
  finally
    Registry.Free;
  end;
end;

function TEmpresaLicencaManager.GetDataUltimoGetSucesso: TDateTime;
var
 LDC: String; //Last Date Checkin
 Registry: TRegistry;
 KEY: Word;
begin
  Result := 0;
  Registry := TRegistry.Create(KEY_READ or KEY_WRITE);
  try
    try
      KEY := 2024; // Constante como em uDMPassport
      Registry.RootKey := HKEY_CURRENT_USER;
      Registry.OpenKey('Software\is5\ADMCloud', True);
      LDC := Registry.ReadString('LDC');

      if LDC <> '' then
        Result := StrToDate(Decrypt(LDC, KEY));
    except
      // Se houver erro ao descriptografar, retorna 0
      Result := 0;
    end;
  finally
    Registry.Free;
  end;
end;

function TEmpresaLicencaManager.GetDiasUltimoGetSucesso: Integer;
begin
  Result := Trunc(Date - GetDataUltimoGetSucesso);
end;

function TEmpresaLicencaManager.Encrypt(const S: String; Key: Word): String;
var
 I: integer;
 C1: Word;
 C2: Word;
begin
  C1 := 32810;
  C2 := 52010;
  Result := '';
  for I := 1 to Length(S) do
  begin
    Result := Result + IntToHex(byte(char(byte(S[I]) xor (Key shr 8))), 2);
    Key := (byte(char(byte(S[I]) xor (Key shr 8))) + Key) * C1 + C2;
  end;
end;

function TEmpresaLicencaManager.Decrypt(const S: ShortString; Key: Word): String;
var
 I: integer;
 x: char;
 C1: Word;
 C2: Word;
begin
  C1 := 32810;
  C2 := 52010;
  result := '';
  i := 1;
  while (i < Length(S)) do
  begin
    x := char(strToInt('$' + Copy(s, i, 2)));
    Result := result + char(byte(x) xor (Key shr 8));
    Key := (byte(x) + Key) * C1 + C2;
    Inc(i, 2);
  end;
end;

// Função auxiliar para remover pontos e caracteres especiais
function TiraPontos(const Str: string): string;
begin
  Result := StringReplace(StringReplace(StringReplace(Str, '.', '', [rfReplaceAll]), '/', '', [rfReplaceAll]), '-', '', [rfReplaceAll]);
end;

constructor TEmpresaLicencaManager.Create(AOwner: TComponent);
begin
  inherited Create(AOwner);
  FLastStatus := lsOk;
  FUltimaSincronizacao := 0;
  FAutoSync := False;
  FAutoSyncInterval := 15 * 60 * 1000; // 15 minutos padrão
  FDiasToleranciaCache := 7; // 7 dias de tolerância (como em uDMPassport)
  FVersaoFBX := ''; // Vazio por padrão, pode ser configurado
  FVersaoPDV := ''; // Vazio por padrão, pode ser configurado
  FUltimoErro := ''; // Inicializar string de erro

  FTimer := TTimer.Create(Self);
  FTimer.Enabled := False;
  FTimer.OnTimer := TimerSync;
  FTimer.Interval := FAutoSyncInterval;

  // Inicializar API (URL padrão de produção)
  FURL_API := ADMCloud_URL_PROD;  // https://admcloud.papion.com.br/api/v1
  FAPIHelper := TADMCloudHelper.Create(FURL_API);
  
  // Obter GUID da máquina (será carregado do Registry ou gerado novo)
  FMachineGUID := GetMachineGUID;
end;

destructor TEmpresaLicencaManager.Destroy;
begin
  FTimer.Free;
  if Assigned(FAPIHelper) then
    FAPIHelper.Free;
  inherited;
end;

function TEmpresaLicencaManager.CarregarEmpresaDoMySQL(const CNPJ: string): Boolean;
var
  LResponse: string;
  LJSON: TJSONObject;
  LValue: TJSONValue;
  LCNPJLimpo: string;
begin
  Result := False;

  try
    // Limpar CNPJ para enviar apenas números
    LCNPJLimpo := StringReplace(StringReplace(CNPJ, '.', '', [rfReplaceAll]), '/', '', [rfReplaceAll]);

    // Usar API para buscar dados da empresa
    // GET /passport?cgc=CNPJ&hostname=HOSTNAME&guid=GUID
    // A API retornará os dados da empresa se existir e estiver válida

    if not Assigned(FAPIHelper) then
    begin
      Log('CarregarEmpresaDoMySQL: FAPIHelper não inicializado.');
      Exit(False);
    end;

    // Validar passport (também traz dados de empresa se válida)
    if not FAPIHelper.ValidarPassport(LCNPJLimpo, GetTerminalAtual, GetMachineSerial, '', '') then
    begin
      Log('CarregarEmpresaDoMySQL: Empresa não encontrada ou inválida via API: ' + CNPJ);
      Log('Erro API: ' + FAPIHelper.GetUltimoErro);
      Exit(False);
    end;

    // Se a validação passou, a empresa existe na base. Agora gravar localmente
    // (em um cenário real, você teria endpoints específicos para GET /empresa/{cnpj})
    // Por enquanto, apenas confirmamos que existe e está válida

    Log('Empresa validada via API e cadastrada localmente: ' + CNPJ);
    Result := True;

  except
    on E: Exception do
    begin
      Log('Erro ao carregar empresa da API: ' + E.Message);
      Result := False;
    end;
  end;
end;

function TEmpresaLicencaManager.ValidarPassportEmpresa(const ACNPJ, AHostname, AGUID: string): Boolean;
var
  LCNPJLimpo: string;
begin
  Result := False;

  try
    if not Assigned(FAPIHelper) then
    begin
      Log('ValidarPassportEmpresa: FAPIHelper não inicializado.');
      Exit(False);
    end;

    // Limpar CNPJ para enviar apenas números
    LCNPJLimpo := StringReplace(StringReplace(ACNPJ, '.', '', [rfReplaceAll]), '/', '', [rfReplaceAll]);

    // Validar passport via API
    if FAPIHelper.ValidarPassport(LCNPJLimpo, AHostname, AGUID, '', '') then
    begin
      Log('ValidarPassportEmpresa: Empresa validada com sucesso - CNPJ: ' + ACNPJ);
      Result := True;
    end
    else
    begin
      Log('ValidarPassportEmpresa: Empresa não validada - CNPJ: ' + ACNPJ);
      Log('Erro API: ' + FAPIHelper.GetUltimoErro);
      Result := False;
    end;

  except
    on E: Exception do
    begin
      Log('Erro ao validar passport da empresa: ' + E.Message);
      Result := False;
    end;
  end;
end;

procedure TEmpresaLicencaManager.AtualizarFormEmpresa;
begin
  if frmEmpresa <> nil then
  begin
    frmEmpresa.qryEmpresa.Refresh;
    PreencherFormEmpresa;
    Log('Formulário atualizado após sincronização.');
  end;
end;


procedure TEmpresaLicencaManager.Log(const S: string);
var
  LogPath, Linha: string;
  FS: TextFile;
begin
  // Log simples em arquivo + evento
  Linha := FormatDateTime('dd/mm/yyyy hh:nn:ss', Now) + ' - ' + S;

  if Assigned(FOnLog) then
    FOnLog(Self, Linha);

  try
    LogPath := TPath.Combine(ExtractFilePath(ParamStr(0)), 'Licenca.log');
    AssignFile(FS, LogPath);
    if FileExists(LogPath) then
      Append(FS)
    else
      Rewrite(FS);
    Writeln(FS, Linha);
    CloseFile(FS);
  except
    // não deixa log quebrar o fluxo
  end;
end;

procedure TEmpresaLicencaManager.PreencherFormEmpresa;
begin
  if frmEmpresa = nil then
    Exit;

  // Preenche os DBEdits diretamente com os dados do dataset EMPRESA
  frmEmpresa.DBEdit9.Text        := dados.qryEmpresaCNPJ.AsString;
  frmEmpresa.DBEdit2.Text           := dados.qryEmpresaRAZAO.AsString;
  frmEmpresa.DBEdit3.Text           := dados.qryEmpresaFANTASIA.AsString;
  frmEmpresa.DBEdit4.Text           := dados.qryEmpresaENDERECO.AsString;
  frmEmpresa.DBEdit5.Text           := dados.qryEmpresaNUMERO.AsString;
  frmEmpresa.DBEdit6.Text           := dados.qryEmpresaCOMPLEMENTO.AsString;
  frmEmpresa.DBEdit7.Text           := dados.qryEmpresaBAIRRO.AsString;
  frmEmpresa.DBEdit8.Text           := dados.qryEmpresaCIDADE.AsString;
  frmEmpresa.DBEdit9.Text           := dados.qryEmpresaUF.AsString;
  frmEmpresa.DBEdit10.Text          := dados.qryEmpresaCEP.AsString;
  frmEmpresa.DBEdit11.Text          := dados.qryEmpresaFONE.AsString;
  frmEmpresa.DBEdit12.Text          := dados.qryEmpresaEMAIL.AsString;
  // Caso exista CNAE:
  if frmEmpresa.FindComponent('DBEdit48') <> nil then
    (frmEmpresa.FindComponent('DBEdit48') as TDBEdit).Text := dados.qryEmpresaCNAE.AsString;

  Log('Formulário TfrmEmpresa preenchido automaticamente.');
end;


procedure TEmpresaLicencaManager.ChangeStatus(AStatus: TLicenseStatus; const Detail: string);
begin
  FLastStatus := AStatus;
  if Assigned(FOnStatusChange) then
    FOnStatusChange(Self, AStatus, Detail);
  Log(Format('Status: %d - %s', [Ord(AStatus), Detail]));
end;

procedure TEmpresaLicencaManager.InicializarEmpresa;
var
  ExisteEmpresa: Boolean;
  CNPJDigitado: string;
begin
  Log('InicializarEmpresa: verificando EMPRESA local.');

  dados.qryEmpresa.Close;
  dados.qryEmpresa.Open;
  ExisteEmpresa := not dados.qryEmpresa.IsEmpty;

  //  CASO 1: Empresa já cadastrada localmente
  if ExisteEmpresa then
  begin
    Log('Empresa já existente localmente. Prosseguindo.');
    Exit;
  end;

  //  CASO 2: Empresa não existe localmente → abrir tela
  Log('Nenhuma empresa local encontrada. Abrindo TfrmEmpresa.');
  Application.CreateForm(TfrmEmpresa, frmEmpresa);

  frmEmpresa.qryEmpresa.Close;
  frmEmpresa.qryEmpresa.Open;
  frmEmpresa.qryEmpresa.Insert;
  frmEmpresa.qryEmpresaTIPO.AsString := 'JURIDICA';

  frmEmpresa.ShowModal;

  // Se o usuário não gravou → erro
  if frmEmpresa.qryEmpresa.State in [dsEdit, dsInsert] then
    frmEmpresa.qryEmpresa.Cancel;

  CNPJDigitado := frmEmpresa.qryEmpresaCNPJ.AsString;

  frmEmpresa.Release;
  frmEmpresa := nil;

  //  Se usuário fechou sem digitar CNPJ → ERRO
  if CNPJDigitado = '' then
    raise Exception.Create('É necessário informar um CNPJ para iniciar o sistema.');

  //  CASO 2A: Verificar se já existe na API antes de usar ACBr
  if CarregarEmpresaDoMySQL(CNPJDigitado) then
  begin
    Log('Empresa validada via API. ACBr ignorado.');
    PreencherFormEmpresa;
    Exit;
  end;

  //  CASO 2B: Se não existe na API → usa ACBrConsultaCNPJ como fallback
  Log('Empresa não validada na API. Usando ACBr como fallback para preencher dados.');

  // ACBrConsultaCNPJ1 está em DadosWeb (TDataModule)
  try
    if Assigned(DadosWeb.ACBrConsultaCNPJ1) then
    begin
      if DadosWeb.ACBrConsultaCNPJ1.Consulta(CNPJDigitado) then
      begin
        PreencherEmpresaComACBr(DadosWeb.ACBrConsultaCNPJ1);
        PreencherFormEmpresa;
      end;
    end;
  except
    on E: Exception do
      Log('Erro ao consultar CNPJ via ACBr: ' + E.Message);
  end;

  dados.qryEmpresa.Post;
  dados.Conexao.CommitRetaining;

  Log('Empresa preenchida via ACBr e gravada localmente.');
end;


procedure TEmpresaLicencaManager.PreencherEmpresaComACBr(const ACBr: TACBrConsultaCNPJ);
var
  CNPJLimpo: string;
begin
  if ACBr = nil then
    Exit;

  // Garante modo edição
  if dados.qryEmpresa.State = dsBrowse then
    dados.qryEmpresa.Edit;

  CNPJLimpo := ACBr.CNPJ;

  // Preenchimento conforme o evento TF_Principal.ButBuscarClick
  // Preencher no dataset, não nos DBEdits (que são virtuais)
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

  // Pessoa jurídica SEMPRE
  if dados.qryEmpresaTIPO.IsNull or (dados.qryEmpresaTIPO.AsString = '') then
    dados.qryEmpresaTIPO.AsString := 'JURIDICA';

  Log('Emitente preenchido via ACBrConsultaCNPJ para CNPJ: ' + CNPJLimpo);
end;


function TEmpresaLicencaManager.ValidarNSerieAntiFraude: Boolean;
var
  SerialMaquina, SerialGravado: string;
begin
  Result := True; // só bloqueia se realmente tiver dado gravado
  SerialMaquina := GetMachineSerial;
  if SerialMaquina = '' then
  begin
    Log('ValidarNSerieAntiFraude: serial de máquina vazio. Não bloqueado.');
    Exit;
  end;

  if not dados.qryEmpresa.Active then
    dados.qryEmpresa.Open;

  SerialGravado := '';
  if not dados.qryEmpresaNSERIE.IsNull then
    SerialGravado := dados.Crypt('D', dados.qryEmpresaNSERIE.AsString);

  if SerialGravado = '' then
  begin
    // PRIMEIRA associação: grava serial atual
    dados.qryEmpresa.Edit;
    dados.qryEmpresaNSERIE.AsString := dados.Crypt('C', SerialMaquina);
    dados.qryEmpresa.Post;
    dados.Conexao.CommitRetaining;
    Log('NSERIE vinculado ao serial de máquina: ' + SerialMaquina);
    Exit(True);
  end;

  if not SameText(SerialMaquina, SerialGravado) then
  begin
    Log('ValidarNSerieAntiFraude: serial da máquina não confere com NSERIE gravado.');
    Result := False;
    Exit;
  end;

  Log('ValidarNSerieAntiFraude: NSERIE OK.');
end;

function TEmpresaLicencaManager.ValidarTerminais: Boolean;
var
  LimiteTerminais: Integer;
begin
  Result := True;

  if not dados.qryEmpresa.Active then
    dados.qryEmpresa.Open;

  try
    LimiteTerminais :=
      StrToIntDef(dados.Crypt('D', dados.qryEmpresaNTERM.Value), 1);
  except
    LimiteTerminais := 1;
  end;

  // Aqui você pode usar sua lógica existente de terminais:
  // Ex: contar quantos estão LOGADO='S' em qryTerminal
  if not dados.qryTerminal.Active then
    dados.qryTerminal.Open;

  dados.qryTerminal.Filtered := False;
  dados.qryTerminal.Filter := 'LOGADO = ''S''';
  dados.qryTerminal.Filtered := True;
  dados.qryTerminal.Last;
  Result := (dados.qryTerminal.RecordCount <= LimiteTerminais);
  dados.qryTerminal.Filtered := False;

  if not Result then
    Log(Format('ValidarTerminais: limite excedido. Em uso=%d, Limite=%d',
      [dados.qryTerminal.RecordCount, LimiteTerminais]))
  else
    Log('ValidarTerminais: OK. Em uso=' + dados.qryTerminal.RecordCount.ToString);
end;

function TEmpresaLicencaManager.LicencaEstaVencida(out Msg: string): Boolean;
var
  DtVal: TDate;
begin
  Result := False;
  Msg := '';

  if dados.qryEmpresaDATA_VALIDADE.IsNull then
    Exit;

  try
    DtVal := StrToDate(dados.Crypt('D', dados.qryEmpresaDATA_VALIDADE.Value));
  except
    DtVal := 0;
  end;

  if (DtVal > 0) and (DtVal < Date) then
  begin
    Result := True;
    Msg := 'Licença vencida em ' + DateToStr(DtVal);
  end;
end;

function TEmpresaLicencaManager.LicencaEstaBloqueada(out Msg: string): Boolean;
var
  Bloq: string;
begin
  Result := False;
  Msg := '';

  if dados.qryEmpresaCSENHA.IsNull then
    Exit;

  Bloq := dados.Crypt('D', dados.qryEmpresaCSENHA.Value);

  if SameText(Bloq, 'S') or SameText(Bloq, 'BLOQUEADO') then
  begin
    Result := True;
    Msg := 'Licença bloqueada pelo gerenciador.';
  end;
end;

function TEmpresaLicencaManager.SincronizarComGerenciadorLicenca: Boolean;
var
  Cancel: Boolean;
  LCNPJ: string;
  LHostname: string;
  LGUID: string;
  LEmpresaExisteNaAPI: Boolean;
begin
  Result := False;

  Cancel := False;
  if Assigned(FOnBeforeSync) then
    FOnBeforeSync(Self, Cancel);

  if Cancel then
  begin
    Log('SincronizarComGerenciadorLicenca: cancelado por OnBeforeSync.');
    Exit(False);
  end;

  try
    Log('SincronizarComGerenciadorLicenca: iniciando sincronizacao via API ADMCloud.');

    // Obter dados necessarios para validacao
    LCNPJ := GetCNPJEmpresaAtual;
    LHostname := GetHostName;
    LGUID := GetMachineGUID;

    Log('LCNPJ: ' + LCNPJ);
    Log('Hostname: ' + LHostname);
    Log('GUID: ' + LGUID);

    if LCNPJ = '' then
    begin
      Log('SincronizarComGerenciadorLicenca: CNPJ nao informado.');
      ChangeStatus(lsSemEmpresa, 'CNPJ nao configurado.');
      Exit(False);
    end;

    // Validar Passport via API
    if not ValidarPassportEmpresa(LCNPJ, LHostname, LGUID) then
    begin
      Log('Validacao Passport falhou. Verificando se empresa existe na API...');
      
      // Se a validacao falhou, verificar se a empresa existe na API
      LEmpresaExisteNaAPI := VerificarCNPJNaAPI(LCNPJ);
      
      if not LEmpresaExisteNaAPI then
      begin
        Log('Empresa nao encontrada na API. Tentando registrar...');
        
        // Empresa nao existe - tentar registrar automaticamente
        // Obter dados de dados.qryEmpresa
        if not Assigned(dados) or not Assigned(dados.qryEmpresa) or dados.qryEmpresa.IsEmpty then
        begin
          Log('Erro: dados.qryEmpresa nao disponivel ou vazio para obter informacoes de registro.');
          ChangeStatus(lsSemEmpresa, 'Dados da empresa nao carregados.');
          Exit(False);
        end;

        // Registrar empresa com dados do qryEmpresa
        if RegistrarEmpresaNoMySQL(
          dados.qryEmpresaRAZAO.AsString,           // Nome
          dados.qryEmpresaFANTASIA.AsString,        // Fantasia
          LCNPJ,                                     // CNPJ
          'Administrativo',                          // Contato (pode ser customizado)
          dados.qryEmpresaEMAIL.AsString,           // Email
          dados.qryEmpresaFONE.AsString,            // Telefone
          '',                                        // Celular (opcional)
          dados.qryEmpresaENDERECO.AsString,        // Endereco
          dados.qryEmpresaNUMERO.AsString,          // Numero
          dados.qryEmpresaCOMPLEMENTO.AsString,     // Complemento (opcional)
          dados.qryEmpresaBAIRRO.AsString,          // Bairro
          dados.qryEmpresaCIDADE.AsString,          // Cidade
          dados.qryEmpresaUF.AsString,              // Estado
          dados.qryEmpresaCEP.AsString              // CEP
        ) then
        begin
          Log('Empresa registrada com sucesso na API! CNPJ: ' + LCNPJ);
          
          // Tentar validar novamente apos registro
          if ValidarPassportEmpresa(LCNPJ, LHostname, LGUID) then
          begin
            Log('Sincronizacao bem-sucedida apos registro automatico!');
            FUltimaSincronizacao := Now;
            ChangeStatus(lsOk, 'Empresa registrada e sincronizada com sucesso.');
            Result := True;
            Exit;
          end
          else
          begin
            Log('Empresa registrada mas validacao Passport ainda falha.');
            ChangeStatus(lsOk, 'Empresa registrada na API com sucesso.');
            Result := True;
            FUltimaSincronizacao := Now;
            Exit;
          end;
        end
        else
        begin
          Log('Falha ao registrar empresa na API. Erro: ' + GetUltimoErro);
          ChangeStatus(lsSemConexaoWeb, 'Falha ao registrar empresa na API.');
          Exit(False);
        end;
      end
      else
      begin
        Log('Empresa existe na API mas validacao Passport falhou.');
        ChangeStatus(lsSemConexaoWeb, 'Falha ao validar Passport da empresa na API.');
        Exit(False);
      end;
    end;

    // Se chegou aqui, sincronizacao foi bem-sucedida (passport validado)
    FUltimaSincronizacao := Now;
    Log('Sincronizacao bem-sucedida!');
    ChangeStatus(lsOk, 'Sincronizacao concluida com sucesso via API.');
    Result := True;

  except
    on E: Exception do
    begin
      Log('Erro ao sincronizar com gerenciador de licencas: ' + E.Message);
      ChangeStatus(lsSemConexaoWeb, E.Message);
      Result := False;
    end;
  end;

  if Assigned(FOnAfterSync) then
    FOnAfterSync(Self, FLastStatus);

  AtualizaStatusBar;
end;

function TEmpresaLicencaManager.ValidarLicencaAtual: Boolean;
var
  Msg: string;
  LBookmark: TBookmark;
  LCNPJAtual: string;
  LResultadoIndividual: Boolean;
  LTodosCNPJsValidos: Boolean;
  LContadorCNPJs: Integer;
  LContadorValidos: Integer;
begin
  Result := False;

  Log('ValidarLicencaAtual: Iniciando validação de TODOS os CNPJs...');

  if dados.qryEmpresa.IsEmpty then
  begin
    Log('ValidarLicencaAtual: ERRO - Nenhuma empresa cadastrada.');
    ChangeStatus(lsSemEmpresa, 'Nenhuma empresa cadastrada.');
    Exit(False);
  end;

  // Salvar posição atual
  LBookmark := dados.qryEmpresa.GetBookmark;
  LTodosCNPJsValidos := True;
  LContadorCNPJs := 0;
  LContadorValidos := 0;

  try
    // Ir para o primeiro registro
    dados.qryEmpresa.First;

    while not dados.qryEmpresa.Eof do
    begin
      Inc(LContadorCNPJs);
      LCNPJAtual := dados.qryEmpresaCNPJ.AsString;
      
      Log('');
      Log('═══════════════════════════════════════════════════════════');
      Log('ValidarLicencaAtual: [' + IntToStr(LContadorCNPJs) + '] Validando CNPJ: ' + LCNPJAtual);
      Log('═══════════════════════════════════════════════════════════');

      LResultadoIndividual := True;

      // 1) Checa validade
      Log('  [1/5] Checando se licença está vencida...');
      if LicencaEstaVencida(Msg) then
      begin
        Log('  ❌ FALHA - Licença vencida: ' + Msg);
        LResultadoIndividual := False;
        LTodosCNPJsValidos := False;
      end
      else
      begin
        Log('  ✅ Licença não vencida - Campo DATA_VALIDADE OK');
      end;

      // 2) Checa bloqueio
      if LResultadoIndividual then
      begin
        Log('  [2/5] Checando se licença está bloqueada...');
        if LicencaEstaBloqueada(Msg) then
        begin
          Log('  ❌ FALHA - Licença bloqueada: ' + Msg);
          LResultadoIndividual := False;
          LTodosCNPJsValidos := False;
        end
        else
        begin
          Log('  ✅ Licença não bloqueada - Campo CSSENHA OK');
        end;
      end;

      // 3) Checa NTERM / terminais
      if LResultadoIndividual then
      begin
        Log('  [3/5] Checando limite de terminais...');
        if not ValidarTerminais then
        begin
          Log('  ❌ FALHA - Quantidade de terminais excedida');
          LResultadoIndividual := False;
          LTodosCNPJsValidos := False;
        end
        else
        begin
          Log('  ✅ Terminais válidos - Dentro do limite NTERM');
        end;
      end;

      // 4) Anti-fraude NSERIE
      if LResultadoIndividual then
      begin
        Log('  [4/5] Checando número de série contra fraude...');
        if not ValidarNSerieAntiFraude then
        begin
          Log('  ❌ FALHA - Número de série não confere');
          LResultadoIndividual := False;
          LTodosCNPJsValidos := False;
        end
        else
        begin
          Log('  ✅ Número de série válido - Serial da máquina confere');
        end;
      end;

      // 5) ChecaValidade
      if LResultadoIndividual then
      begin
        Log('  [5/5] Executando ChecaValidade (rotina original)...');
        try
          dados.ChecaValidade;
          Log('  ✅ ChecaValidade passou');
        except
          on E: Exception do
          begin
            Log('  ❌ FALHA em ChecaValidade: ' + E.Message);
            Log('      Classe da exceção: ' + E.ClassName);
            LResultadoIndividual := False;
            LTodosCNPJsValidos := False;
          end;
        end;
      end;

      // Registrar resultado
      if LResultadoIndividual then
      begin
        Log('ValidarLicencaAtual: ✅ CNPJ [' + LCNPJAtual + '] - VÁLIDO');
        Inc(LContadorValidos);
      end
      else
        Log('ValidarLicencaAtual: ❌ CNPJ [' + LCNPJAtual + '] - INVÁLIDO');

      Log('');

      // Próximo registro
      dados.qryEmpresa.Next;
    end;

    // Restaurar posição
    if LBookmark <> nil then
      dados.qryEmpresa.GotoBookmark(LBookmark);

  finally
    if LBookmark <> nil then
      dados.qryEmpresa.FreeBookmark(LBookmark);
  end;

  // Resumo final
  Log('═══════════════════════════════════════════════════════════');
  Log('RESUMO FINAL DA VALIDAÇÃO');
  Log('═══════════════════════════════════════════════════════════');
  Log('Total de CNPJs validados: ' + IntToStr(LContadorCNPJs));
  Log('CNPJs válidos: ' + IntToStr(LContadorValidos));
  Log('CNPJs inválidos: ' + IntToStr(LContadorCNPJs - LContadorValidos));
  Log('');

  if LTodosCNPJsValidos and (LContadorCNPJs > 0) then
  begin
    Log('ValidarLicencaAtual: ✅✅✅ TODAS AS LICENÇAS VÁLIDAS!');
    ChangeStatus(lsOk, 'Todas as licenças válidas.');
    Result := True;
  end
  else
  begin
    Log('ValidarLicencaAtual: ❌ PELO MENOS UMA LICENÇA INVÁLIDA!');
    ChangeStatus(lsErroGeral, 'Uma ou mais licenças inválidas.');
    Result := False;
  end;

  Log('═══════════════════════════════════════════════════════════');
  AtualizaStatusBar;
end;

procedure TEmpresaLicencaManager.SincronizacaoPeriodica;
begin
  Log('SincronizacaoPeriodica: chamada.');

  if dados.IsGlobalOffline then
  begin
    Log('SincronizacaoPeriodica: sistema em modo offline. Abortando sync.');
    Exit;
  end;

  if SincronizarComGerenciadorLicenca then
  begin
    ValidarLicencaAtual;
  end;
end;

procedure TEmpresaLicencaManager.TimerSync(Sender: TObject);
begin
  try
    FTimer.Enabled := False;
    SincronizacaoPeriodica;
  finally
    if FAutoSync then
      FTimer.Enabled := True;
  end;
end;

procedure TEmpresaLicencaManager.AtualizaStatusBar;
var
  Panel3Text, Panel5Text: string;
begin
  try
    Panel3Text := 'Terminais.:' + dados.Crypt('D', dados.qryEmpresaNTERM.Value);
    Panel5Text := 'Licenciado:' + dados.Crypt('D', dados.qryEmpresaDATA_VALIDADE.Value);
    
    // Notifica interessados para atualizar a StatusBar
    if Assigned(FOnUpdateStatusBar) then
      FOnUpdateStatusBar(Self, Panel3Text, Panel5Text);
  except
    // não quebra se algo der erro
  end;
end;

procedure TEmpresaLicencaManager.SetAutoSync(const Value: Boolean);
begin
  FAutoSync := Value;
  FTimer.Enabled := FAutoSync;
end;

procedure TEmpresaLicencaManager.SetAutoSyncInterval(const Value: Integer);
begin
  FAutoSyncInterval := Value;
  FTimer.Interval := FAutoSyncInterval;
end;

procedure TEmpresaLicencaManager.ConfigurarURLAPI(const AURL: string);
begin
  if AURL = '' then
    FURL_API := ADMCloud_URL_PROD
  else
    FURL_API := AURL;

  // Recria a instância com a nova URL
  if Assigned(FAPIHelper) then
    FAPIHelper.Free;

  FAPIHelper := TADMCloudHelper.Create(FURL_API);
  Log('URL da API ADMCloud configurada: ' + FURL_API);
end;

procedure TEmpresaLicencaManager.ConfigurarCredenciaisAPI(const AUsername, APassword: string);
begin
  if Assigned(FAPIHelper) then
    FAPIHelper.ConfigurarCredenciais(AUsername, APassword);
  Log('Credenciais da API ADMCloud configuradas.');
end;

function TEmpresaLicencaManager.GetUltimoErro: string;
begin
  // Retorna primeiro o erro próprio (FUltimoErro), depois o da API
  if FUltimoErro <> '' then
    Result := FUltimoErro
  else if Assigned(FAPIHelper) then
    Result := FAPIHelper.GetUltimoErro
  else
    Result := 'Gerenciador de API não inicializado';
end;

function TEmpresaLicencaManager.GetDebugInfo: string;
begin
  Result := '';
  if Assigned(FAPIHelper) then
  begin
    Result := 'Status Code: ' + IntToStr(FAPIHelper.GetUltimoStatusCode) + ' | ';
    Result := Result + 'Erro API: ' + FAPIHelper.GetUltimoErro + ' | ';
  end;
  Result := Result + 'FUltimoErro: ' + FUltimoErro;
end;

function TEmpresaLicencaManager.VerificarCNPJNaAPI(const ACNPJ: string): Boolean;
var
  LCNPJLimpo: string;
  LResponse: string;
  LJSON: TJSONObject;
  LJSONValue: TJSONValue;
  LArray: TJSONArray;
begin
  Result := False;
  FUltimoErro := '';
  LJSONValue := nil;
  LArray := nil;

  try
    // Limpar CNPJ para enviar apenas números
    LCNPJLimpo := StringReplace(StringReplace(ACNPJ, '.', '', [rfReplaceAll]), '/', '', [rfReplaceAll]);

    if not Assigned(FAPIHelper) then
    begin
      Log('    [DEBUG-001] VerificarCNPJNaAPI: FAPIHelper não inicializado.');
      Exit(False);
    end;

    Log('    [DEBUG-002] CNPJ original: ' + ACNPJ);
    Log('    [DEBUG-003] CNPJ limpo: ' + LCNPJLimpo);
    
    Log('    [DEBUG-004] Chamando ConsultarPessoaPorCNPJ...');
    if FAPIHelper.ConsultarPessoaPorCNPJ(LCNPJLimpo, LResponse) then
    begin
      Log('    [DEBUG-005] ✓ ConsultarPessoaPorCNPJ retornou TRUE');
      Log('    [DEBUG-006] Comprimento da resposta: ' + IntToStr(Length(LResponse)));
      
      if Length(LResponse) > 300 then
        Log('    [DEBUG-007] Resposta (primeiros 300 chars): ' + Copy(LResponse, 1, 300))
      else
        Log('    [DEBUG-007] Resposta completa: ' + LResponse);
      
      if Trim(LResponse) = '' then
      begin
        Log('    [DEBUG-008] Resposta está VAZIA');
        Result := False;
      end
      else
      begin
        Log('    [DEBUG-009] Tentando fazer parse JSON...');
        try
          LJSONValue := TJSONObject.ParseJSONValue(LResponse);
          
          if Assigned(LJSONValue) then
          begin
            Log('    [DEBUG-010] ✓ Parse bem-sucedido');
            
            // A resposta do endpoint é um objeto JSON com estrutura:
            // { "status": true/false, "msg": "...", "data": {...} ou null }
            if LJSONValue is TJSONObject then
            begin
              LJSON := TJSONObject(LJSONValue);
              Log('    [DEBUG-012] É um OBJETO com ' + IntToStr(LJSON.Count) + ' campos');
              
              // Verificar se tem campo 'status' - novo formato de resposta
              if (LJSON.GetValue('status') <> nil) then
              begin
                Log('    [DEBUG-012a] Detectado novo formato de resposta (com status)');
                // Novo formato: {status, msg, data}
                // A pessoa foi encontrada se status=true
                Result := LJSON.GetValue<Boolean>('status', False);
                
                if (LJSON.GetValue('data') <> nil) then
                begin
                  Log('    [DEBUG-012b] Campo data: presente');
                end;
              end
              else
              begin
                Log('    [DEBUG-012c] Formato antigo (resposta direta do banco)');
                // Formato antigo: resposta direta é um objeto com dados da pessoa
                Result := LJSON.Count > 0;
              end;
            end
            else if LJSONValue is TJSONArray then
            begin
              LArray := TJSONArray(LJSONValue);
              Log('    [DEBUG-011] É um ARRAY com ' + IntToStr(LArray.Count) + ' elementos');
              Result := LArray.Count > 0;
            end
            else
            begin
              Log('    [DEBUG-013] Tipo desconhecido: ' + LJSONValue.ClassName);
              Result := False;
            end;
          end
          else
          begin
            Log('    [DEBUG-014] ✗ Parse retornou nil');
            Result := False;
          end;
        except
          on E: Exception do
          begin
            Log('    [DEBUG-015] ✗ Exceção em parse: ' + E.Message);
            Result := False;
          end;
        end;
      end;
    end
    else
    begin
      Log('    [DEBUG-016] ✗ ConsultarPessoaPorCNPJ retornou FALSE');
      Log('    [DEBUG-017] Status Code: ' + IntToStr(FAPIHelper.GetUltimoStatusCode));
      Log('    [DEBUG-018] Erro: ' + FAPIHelper.GetUltimoErro);
      Log('    [DEBUG-019] Resposta: ' + LResponse);
      Result := False;
    end;

  except
    on E: Exception do
    begin
      Log('    [DEBUG-020] ✗ Exceção geral: ' + E.Message);
      Result := False;
    end;
  end;
  
  if Assigned(LJSONValue) then
    FreeAndNil(LJSONValue);
end;

function TEmpresaLicencaManager.RegistrarEmpresaNoMySQL(const ANome, AFantasia, ACNPJ, AContato, AEmail, ATelefone: string;
  const ACelular: string = ''; const AEndereco: string = ''; const ANumero: string = '';
  const AComplemento: string = ''; const ABairro: string = ''; const ACidade: string = '';
  const AEstado: string = ''; const ACEP: string = ''): Boolean;
var
  LCNPJLimpo: string;
  LResponseRaw: string;
  LJSON: TJSONObject;
  LStatus: string;
  LMsg: string;
begin
  Result := False;
  FUltimoErro := '';

  try
    // Limpar CNPJ para enviar apenas números
    LCNPJLimpo := StringReplace(StringReplace(ACNPJ, '.', '', [rfReplaceAll]), '/', '', [rfReplaceAll]);

    if not Assigned(FAPIHelper) then
    begin
      Log('RegistrarEmpresaNoMySQL: FAPIHelper não inicializado.');
      FUltimoErro := 'FAPIHelper não inicializado';
      Exit(False);
    end;

    // Registrar cliente na API usando endpoint POST /registro com TODOS os dados necessários
    // IMPORTANTE: Todos os 12 campos obrigatórios devem estar preenchidos!
    if (ANome = '') or (AFantasia = '') or (LCNPJLimpo = '') or (AContato = '') or
       (AEmail = '') or (ATelefone = '') or (AEndereco = '') or (ANumero = '') or
       (ABairro = '') or (ACidade = '') or (AEstado = '') or (ACEP = '') then
    begin
      Log('RegistrarEmpresaNoMySQL: Faltam campos obrigatórios.');
      FUltimoErro := 'Faltam campos obrigatórios para registro';
      Exit(False);
    end;

    // Registrar cliente na API usando endpoint POST /registro
    if not FAPIHelper.RegistrarCliente(
      ANome,          // Obrigatório: Nome da empresa
      AFantasia,      // Obrigatório: Nome fantasia
      LCNPJLimpo,     // Obrigatório: CNPJ (normalizado, sem formatação)
      AContato,       // Obrigatório: Pessoa de contato
      AEmail,         // Obrigatório: Email
      ATelefone,      // Obrigatório: Telefone
      ACelular,       // Opcional: Celular
      AEndereco,      // Obrigatório: Endereço
      ANumero,        // Obrigatório: Número
      AComplemento,   // Opcional: Complemento
      ABairro,        // Obrigatório: Bairro
      ACidade,        // Obrigatório: Cidade
      AEstado,        // Obrigatório: Estado (UF)
      ACEP            // Obrigatório: CEP
    ) then
    begin
      Log('RegistrarEmpresaNoMySQL: RegistrarCliente retornou FALSE para: ' + ACNPJ);
      Log('  Nome=' + ANome);
      Log('  Fantasia=' + AFantasia);
      Log('  CNPJ=' + LCNPJLimpo);
      Log('  Contato=' + AContato);
      Log('  Email=' + AEmail);
      Log('  Endereco=' + AEndereco);
      Log('  Numero=' + ANumero);
      Log('  Bairro=' + ABairro);
      Log('  Cidade=' + ACidade);
      Log('  Estado=' + AEstado);
      Log('  CEP=' + ACEP);
      FUltimoErro := 'RegistrarCliente retornou FALSE';
      Exit(False);
    end;

    Log('RegistrarEmpresaNoMySQL: RegistrarCliente retornou TRUE para: ' + ACNPJ);

    // VALIDAR RESPOSTA DA API - VERIFICA SE HOUVE ERRO
    LResponseRaw := FAPIHelper.GetRegistroResponseRaw;
    Log('RegistrarEmpresaNoMySQL: Resposta bruta da API (comprimento=' + IntToStr(Length(LResponseRaw)) + '): [' + LResponseRaw + ']');
    Log('  Erro HTTP da API: [' + FAPIHelper.GetUltimoErro + ']');
    
    // Tentar fazer parse da resposta JSON para validar status
    if Trim(LResponseRaw) <> '' then
    begin
      try
        LJSON := TJSONObject.ParseJSONValue(LResponseRaw) as TJSONObject;
        if Assigned(LJSON) then
        try
          Log('  JSON parseado com sucesso');
          
          // Verificar campo "status" ou "erro"
          if LJSON.TryGetValue<string>('status', LStatus) then
          begin
            Log('  Status da API: [' + LStatus + ']');
            // Status = 'ERRO' indica falha
            if LowerCase(Trim(LStatus)) = 'erro' then
            begin
              if LJSON.TryGetValue<string>('msg', LMsg) then
              begin
                FUltimoErro := LMsg;
                Log('  Campo msg encontrado: [' + LMsg + ']');
              end
              else if LJSON.TryGetValue<string>('message', LMsg) then
              begin
                FUltimoErro := LMsg;
                Log('  Campo message encontrado: [' + LMsg + ']');
              end
              else
              begin
                FUltimoErro := 'Erro retornado pela API sem mensagem detalhada';
                Log('  Nenhum campo msg/message encontrado');
              end;
              
              Log('  ERRO na API: ' + FUltimoErro);
              Exit(False);
            end;
          end
          else
          begin
            Log('  Campo status nao encontrado no JSON');
          end;
          
          // Verificar se há campo "erro" com mensagem de erro
          if LJSON.TryGetValue<string>('erro', LMsg) then
          begin
            FUltimoErro := LMsg;
            Log('  Campo erro encontrado: ' + LMsg);
            Exit(False);
          end;
        finally
          LJSON.Free;
        end;
      except
        on E: Exception do
        begin
          Log('  Aviso ao fazer parse da resposta: ' + E.ClassName + ' - ' + E.Message);
          // Continua mesmo se falhar parse - pode ser sucesso sem JSON válido
        end;
      end;
    end
    else
    begin
      Log('  Resposta vazia da API!');
      Log('  Verificando se houve erro HTTP...');
      Log('  Erro: [' + FAPIHelper.GetUltimoErro + ']');
      
      // Se a resposta está vazia, algo deu errado
      if FAPIHelper.GetUltimoErro <> '' then
      begin
        FUltimoErro := 'Resposta vazia - Erro: ' + FAPIHelper.GetUltimoErro;
        Log('  FALHA: ' + FUltimoErro);
        Exit(False);
      end
      else
      begin
        Log('  Sem erro HTTP - assumindo sucesso');
      end;
    end;

    // Se a requisição foi bem-sucedida e API respondeu OK, gravar localmente também
    // GRAVAR NA TABELA PESSOA_LICENCAS (qryEmpresa)
    dados.qryEmpresa.Edit;
    dados.qryEmpresaCNPJ.AsString := ACNPJ;
    dados.qryEmpresaRAZAO.AsString := ANome;
    dados.qryEmpresaFANTASIA.AsString := AFantasia;
    dados.qryEmpresaEMAIL.AsString := AEmail;
    dados.qryEmpresaFONE.AsString := ATelefone;
    
    // Gravar dados opcionais se preenchidos
    if ACelular <> '' then
      dados.qryEmpresafone.AsString := ACelular;
    if AEndereco <> '' then
      dados.qryEmpresaENDERECO.AsString := AEndereco;
    if ANumero <> '' then
      dados.qryEmpresaNUMERO.AsString := ANumero;
    if AComplemento <> '' then
      dados.qryEmpresaCOMPLEMENTO.AsString := AComplemento;
    if ABairro <> '' then
      dados.qryEmpresaBAIRRO.AsString := ABairro;
    if ACidade <> '' then
      dados.qryEmpresaCIDADE.AsString := ACidade;
    if AEstado <> '' then
      dados.qryEmpresaUF.AsString := AEstado;
    if ACEP <> '' then
      dados.qryEmpresaCEP.AsString := ACEP;
    
    dados.qryEmpresaTIPO.AsString := 'JURIDICA';
    dados.qryEmpresa.Post;
    Log('  Dados salvos em PESSOA_LICENCAS');

    // GRAVAR NA TABELA PESSOAS (qryPessoas)
    // Procurar se pessoa já existe nesta tabela
    dados.qryPessoas.Locate('CNPJ', ACNPJ, []);
    
    if dados.qryPessoas.Eof then
    begin
      // Inserir novo registro na tabela PESSOAS
      dados.qryPessoas.Insert;
    end
    else
    begin
      // Atualizar registro existente
      dados.qryPessoas.Edit;
    end;
    
    // Preencher os campos da tabela PESSOAS
    dados.qryPessoasCNPJ.AsString := ACNPJ;
    dados.qryPessoasRAZAO.AsString := ANome;
    dados.qryPessoasFANTASIA.AsString := AFantasia;
    dados.qryPessoasENDERECO.AsString := AEndereco;
    dados.qryPessoasNUMERO.AsString := ANumero;
    
    if AComplemento <> '' then
      dados.qryPessoasCOMPLEMENTO.AsString := AComplemento;
    if ABairro <> '' then
      dados.qryPessoasBAIRRO.AsString := ABairro;
    if ACidade <> '' then
      dados.qryPessoasCODMUN.AsString := ACidade;
    if AEstado <> '' then
      dados.qryPessoasUF.AsString := AEstado;
    if ACEP <> '' then
      dados.qryPessoasCEP.AsString := ACEP;
    
    dados.qryPessoas.Post;
    Log('  Dados salvos em PESSOAS');
    
    dados.Conexao.CommitRetaining;

    Log('Empresa registrada com sucesso na API e cadastrada localmente: ' + ACNPJ);
    FUltimoErro := '';
    Result := True;

  except
    on E: Exception do
    begin
      Log('Erro ao registrar empresa: ' + E.Message);
      FUltimoErro := E.Message;
      Result := False;
    end;
  end;
end;

initialization
  EmpresaLicencaManager := nil;

finalization
  EmpresaLicencaManager.Free;

end.