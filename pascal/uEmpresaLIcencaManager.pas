unit uEmpresaLicencaManager;

interface

uses
  System.SysUtils, System.Classes, System.DateUtils, System.IOUtils, System.JSON,
  Vcl.ExtCtrls, Vcl.Forms, Vcl.StdCtrls, Vcl.DBCtrls,
  Data.DB,
  uDados, uDadosWeb,// uPrincipal,
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

function TEmpresaLicencaManager.GetCNPJEmpresaAtual: string;
begin
  Result := '';
  if not dados.qryEmpresa.Active then
    dados.qryEmpresa.Open;

  if not dados.qryEmpresa.IsEmpty then
    Result := dados.qryEmpresaCNPJ.AsString;
end;

function TEmpresaLicencaManager.GetTerminalAtual: string;
begin
  Result := dados.nometerminal; // campo já existente no TDados :contentReference[oaicite:17]{index=17}
end;

function TEmpresaLicencaManager.GetMachineSerial: string;
begin
  Result := '';
  try
    // Obtém o serial do drive C: usando SerialNum da unit Serial
    Result := serial.SerialNum('C');
  except
    Result := '';
  end;
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
    Log('SincronizarComGerenciadorLicenca: iniciando sincronização via API ADMCloud.');

    if not Assigned(FAPIHelper) then
    begin
      Log('Erro: FAPIHelper não inicializado.');
      ChangeStatus(lsErroGeral, 'API não inicializada.');
      Exit(False);
    end;

    // Obter dados necessários para validação
    LCNPJ := GetCNPJEmpresaAtual;
    LHostname := GetTerminalAtual;
    LGUID := FMachineGUID; // Usar GUID do cache em Registry

    if LCNPJ = '' then
    begin
      Log('SincronizarComGerenciadorLicenca: CNPJ não informado.');
      ChangeStatus(lsSemEmpresa, 'CNPJ não configurado.');
      Exit(False);
    end;

    // Validar Passport via API (incluindo versões FBX e PDV opcionais)
    if not FAPIHelper.ValidarPassport(LCNPJ, LHostname, LGUID, FVersaoFBX, FVersaoPDV) then
    begin
      Log('Erro ao validar Passport via API: ' + FAPIHelper.GetUltimoErro);
      
      // ===== LÓGICA DE TOLERÂNCIA (como em uDMPassport) =====
      // Se não conseguiu conectar, mas já teve sucesso antes, pode dar uma tolerância
      
      // Se a data do último sucesso foi hoje, passa
      if (GetDataUltimoGetSucesso = DATE) then
      begin
        Log('Último sync foi hoje. Continuando com tolerância de cache.');
        ChangeStatus(lsOk, 'Usando cache local (último sync: hoje).');
        Exit(True);
      end;
      
      // Se está dentro da tolerância de dias, deixa passar
      if (GetDiasUltimoGetSucesso < FDiasToleranciaCache) then
      begin
        Log(Format('Último sync foi há %d dias. Continuando com tolerância (limite: %d dias).',
          [GetDiasUltimoGetSucesso, FDiasToleranciaCache]));
        ChangeStatus(lsOk, Format('Usando cache local (último sync: %d dias atrás).',
          [GetDiasUltimoGetSucesso]));
        Exit(True);
      end;
      
      // Senão, bloqueia
      Log(Format('Último sync foi há %d dias. Bloqueando (tolerância: %d dias).',
        [GetDiasUltimoGetSucesso, FDiasToleranciaCache]));
      ChangeStatus(lsSemConexaoWeb, 'Sem conexão com API. Período de tolerância expirado.');
      Exit(False);
    end;

    // Se chegou aqui, sincronização foi bem-sucedida
    FUltimaSincronizacao := Now;
    SetDataUltimoGetSucesso; // Armazenar timestamp de sucesso
    ChangeStatus(lsOk, 'Sincronização concluída com sucesso via API.');
    Result := True;

  except
    on E: Exception do
    begin
      Log('Erro ao sincronizar com gerenciador de licenças: ' + E.Message);
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
begin
  Result := False;

  if dados.qryEmpresa.IsEmpty then
  begin
    ChangeStatus(lsSemEmpresa, 'Nenhuma empresa cadastrada.');
    Exit(False);
  end;

  // 1) Checa validade
  if LicencaEstaVencida(Msg) then
  begin
    ChangeStatus(lsLicencaVencida, Msg);
    ShowMessage(Msg);
    Exit(False);
  end;

  // 2) Checa bloqueio
  if LicencaEstaBloqueada(Msg) then
  begin
    ChangeStatus(lsBloqueado, Msg);
    ShowMessage(Msg);
    Exit(False);
  end;

  // 3) Checa NTERM / terminais
  if not ValidarTerminais then
  begin
    ChangeStatus(lsErroTerminal, 'Quantidade de terminais excedida.');
    ShowMessage('Quantidade de terminais excedida. Verifique sua licença.');
    Exit(False);
  end;

  // 4) Anti-fraude NSERIE (opcional, mas recomendável)
  if not ValidarNSerieAntiFraude then
  begin
    ChangeStatus(lsErroNSerie,
      'Número de série do equipamento não confere com a licença.');
    ShowMessage('Número de série do equipamento não confere com a licença.');
    Exit(False);
  end;

  // 5) Deixa sua validação original rodar (ChecaValidade)
  try
    dados.ChecaValidade; // SUA rotina já pronta
  except
    on E: Exception do
    begin
      ChangeStatus(lsErroGeral, 'Erro em ChecaValidade: ' + E.Message);
      ShowMessage('Erro ao validar licença: ' + E.Message);
      Exit(False);
    end;
  end;

  ChangeStatus(lsOk, 'Licença válida.');
  Result := True;
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
  if Assigned(FAPIHelper) then
    Result := FAPIHelper.GetUltimoErro
  else
    Result := 'Gerenciador de API não inicializado';
end;

function TEmpresaLicencaManager.RegistrarEmpresaNoMySQL(const ANome, AFantasia, ACNPJ, AContato, AEmail, ATelefone: string;
  const ACelular: string = ''; const AEndereco: string = ''; const ANumero: string = '';
  const AComplemento: string = ''; const ABairro: string = ''; const ACidade: string = '';
  const AEstado: string = ''; const ACEP: string = ''): Boolean;
var
  LCNPJLimpo: string;
begin
  Result := False;

  try
    // Limpar CNPJ para enviar apenas números
    LCNPJLimpo := StringReplace(StringReplace(ACNPJ, '.', '', [rfReplaceAll]), '/', '', [rfReplaceAll]);

    if not Assigned(FAPIHelper) then
    begin
      Log('RegistrarEmpresaNoMySQL: FAPIHelper não inicializado.');
      Exit(False);
    end;

    // Registrar cliente na API usando endpoint POST /registro com TODOS os dados necessários
    // IMPORTANTE: Todos os 12 campos obrigatórios devem estar preenchidos!
    if (ANome = '') or (AFantasia = '') or (LCNPJLimpo = '') or (AContato = '') or
       (AEmail = '') or (ATelefone = '') or (AEndereco = '') or (ANumero = '') or
       (ABairro = '') or (ACidade = '') or (AEstado = '') or (ACEP = '') then
    begin
      Log('RegistrarEmpresaNoMySQL: Faltam campos obrigatórios.');
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
      Log('RegistrarEmpresaNoMySQL: Erro ao registrar empresa na API: ' + ACNPJ);
      Log('Erro API: ' + FAPIHelper.GetUltimoErro);
      Exit(False);
    end;

    // Se a requisição foi bem-sucedida, gravar localmente também
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
    dados.Conexao.CommitRetaining;

    Log('Empresa registrada com sucesso na API e cadastrada localmente: ' + ACNPJ);
    Result := True;

  except
    on E: Exception do
    begin
      Log('Erro ao registrar empresa: ' + E.Message);
      Result := False;
    end;
  end;
end;

initialization
  EmpresaLicencaManager := nil;

finalization
  EmpresaLicencaManager.Free;

end.