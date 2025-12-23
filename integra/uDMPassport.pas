unit uDMPassport;

interface

uses
  System.SysUtils, System.Classes, REST.Types, REST.Client, Dialogs,
  Data.Bind.Components, Data.Bind.ObjectScope, REST.JSON, System.NetEncoding,

  Windows, Registry;

type
  TRetornoJson = class
  private
    FStatus: Boolean;
    FMensagem: String;
    procedure SetMensagem(const Value: String);
    procedure SetStatus(const Value: Boolean);
  public
    constructor Create;
    property Status: Boolean read FStatus write SetStatus;
    property Mensagem: String read FMensagem write SetMensagem;
  end;

  TRetornoPassport = class
  private
    FStatusText: String;
    FStatusCode: Integer;
    FRetorno: TRetornoJson;
    procedure SetStatusCode(const Value: Integer);
    procedure SetStatusText(const Value: String);
  public
    constructor Create;
    property StatusCode: Integer read FStatusCode write SetStatusCode;
    property StatusText: String read FStatusText write SetStatusText;
    property Retorno: TRetornoJson read FRetorno write FRetorno;
  end;

  TdmPassport = class(TDataModule)
    RESTClient: TRESTClient;
    reqPassport: TRESTRequest;
    respPassport: TRESTResponse;
  private
    { Private declarations }
    function GetHostName: String;
    function GenerateMachineGUID: String;

    function Encrypt(const S: String; Key: Word): String;
    function Decrypt(const S: ShortString; Key: Word): String;

  public
    { Public declarations }
    function GetMachineGUID: String;

    function Checkin(Cgc: String; VersaoFBX: String = ''; VersaoPDV: String = ''): TRetornoPassport;

    procedure SetDataUltimoGet;
    function  GetDataUltimoGet: TDateTime;
    function  GetDiasUltimoGet: Integer;

    //realiza o checkin, grava data de retorna, valida prazos, etc
    //essa funcao executa todo o processo de verificar conta
    function CheckinAccount(Cgc: String; VersaoFBX: String = ''; VersaoPDV: String = ''): Boolean;

  end;

var
  dmPassport: TdmPassport;

  {
    OBSERVACAO
    No Windows 7 o sistema não acessa o servidor HTTPS devido ao SSL obsoleto.
    Entao, uso aqui as DDL do FrontBox (copiar as DLL em OpenSSL_TLS12 para FBX, Windows, System32, etc),
    e configuro o componente RESTCLient (no object inspector):
      - SecureProtocols
          TLS12 = True
  }

const
   C1 = 32810;
   C2 = 52010;
   KEY = 2024;
   DIAS_LIMITE = 7; //SE NAO CONSEGUIR GET EM X DIAS, BLOQUEAR

implementation

{%CLASSGROUP 'Vcl.Controls.TControl'}

{$R *.dfm}

{ TdmPassport }

function TdmPassport.Checkin(Cgc: String; VersaoFBX: String = ''; VersaoPDV: String = ''): TRetornoPassport;
begin
  Result := TRetornoPassport.Create;
  try
    reqPassport.Params.ParameterByName('cgc').Value := Cgc;
    reqPassport.Params.ParameterByName('fbx').Value := VersaoFBX;
    reqPassport.Params.ParameterByName('pdv').Value := VersaoPDV;

    reqPassport.Params.ParameterByName('hostname').Value := GetHostName;
    reqPassport.Params.ParameterByName('guid').Value     := GetMachineGUID;

    //ShowMessage(reqPassport.GetFullRequestURL());
    
    reqPassport.Execute;

    //repassa status HTTP
    Result.StatusCode := respPassport.StatusCode;
    Result.StatusText := respPassport.StatusText;

    //verifica retorno
    if respPassport.StatusCode <> 200 then
      raise Exception.Create(Result.StatusCode.ToString + ' - ' + Result.StatusText);

    //processa retorno
    Result.Retorno := TJson.JsonToObject<TRetornoJson>(respPassport.JSONText);

  except
    on e:Exception do
    begin
      if pos('request failed', e.Message) > 0 then
         Result.Retorno.Mensagem := 'Parece que você está sem Internet.'
      else
         Result.Retorno.Mensagem := e.Message;
    end;
  end;
end;

function TdmPassport.CheckinAccount(Cgc, VersaoFBX, VersaoPDV: String): Boolean;
var
 R: TRetornoPassport;
begin
  Result := False;
  try
    R := Checkin(Cgc, VersaoFBX, VersaoPDV);

    //ocorreu erro de rede
    if R.StatusCode <> 200 then
    begin
      //se nao conseguiu acessar a rede/site, verifica se a data do ultimo checkin...

      //se a data do ultimo get foi hoje, passa
      if (GetDataUltimoGet = DATE) then
      begin
        Result := True;
        Exit;
      end;

      //eh menor q DIAS_LIMITE. Se for, deixa passar. Se nao, bloqueia.
      //isso dah um prazo de 7 dias para normalizar a rede ou site.
      if (GetDiasUltimoGet < DIAS_LIMITE) then
      begin
        Result := True;
        Exit;
      end;

      raise Exception.Create('Impossível verificar Licenças. Sistema bloqueado!')
    end;
    
    //se retornou True, esta tudo certo. Nao bloqueia
    if R.Retorno.Status then
    begin    
      SetDataUltimoGet;  
      Result := True;
      Exit;
    end;

    raise Exception.Create(R.Retorno.Mensagem);
    
  except
    on e:Exception do
    begin  
      ShowMessage(e.Message);
      Result := False;
    end;
  end;            
end;

function TdmPassport.GenerateMachineGUID: String;
var
 GUID: TGUID;
begin
  CreateGUID(GUID);
  Result := GUIDToString(GUID);
end;

function TdmPassport.GetHostName: String;
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
    //se nao funcionou, retorna por ENVIROMENT
    Result := GetEnvironmentVariable('COMPUTERNAME');
    if Result = '' then
      Result := 'UNKNOW';
  end;
end;

function TdmPassport.GetMachineGUID: String;
var
  Registry: TRegistry;
begin
  Result := '';
  Registry := TRegistry.Create(KEY_READ or KEY_WRITE);
  try
    Registry.RootKey := HKEY_CURRENT_USER; // Ou HKEY_LOCAL_MACHINE, se precisar ser global
    if Registry.OpenKey('Software\is5', True) then
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

procedure TdmPassport.SetDataUltimoGet;
var
 LDC: String; //Last Date Checkin
 Registry: TRegistry;
begin
  Registry := TRegistry.Create(KEY_READ or KEY_WRITE);
  try
    LDC := Encrypt(FormatDateTime('dd/MM/yyyy',NOW), KEY);

    Registry.RootKey := HKEY_CURRENT_USER;
    Registry.OpenKey('Software\is5', True);
    Registry.WriteString('LDC', LDC);
  finally
    Registry.Free;
  end;
end;

function TdmPassport.GetDataUltimoGet: TDateTime;
var
 LDC: String; //Last Date Checkin
 Registry: TRegistry;
begin
  Result   := 0;
  Registry := TRegistry.Create(KEY_READ or KEY_WRITE);
  try
    try
      Registry.RootKey := HKEY_CURRENT_USER;
      Registry.OpenKey('Software\is5', True);
      LDC := Registry.ReadString('LDC');

      if LDC <> '' then
        Result := StrToDate( Decrypt(LDC, KEY) );
    except
      //
    end;
  finally
    Registry.Free;
  end;
end;

function TdmPassport.GetDiasUltimoGet: Integer;
begin
  Result := Trunc(Date - GetDataUltimoGet);
end;

{ TRetornoPassport }

constructor TRetornoPassport.Create;
begin
  FStatusCode := 0;
  FStatusText := '';

  FRetorno := TRetornoJson.Create;
end;

procedure TRetornoPassport.SetStatusCode(const Value: Integer);
begin
  FStatusCode := Value;
end;

procedure TRetornoPassport.SetStatusText(const Value: String);
begin
  FStatusText := Value;
end;

{ TRetornoJson }

constructor TRetornoJson.Create;
begin
  FStatus   := False;
  FMensagem := '';
end;

procedure TRetornoJson.SetMensagem(const Value: String);
begin
  FMensagem := Value;
end;

procedure TRetornoJson.SetStatus(const Value: Boolean);
begin
  FStatus := Value;
end;

function TdmPassport.Encrypt(const S: String; Key: Word): String;
var
 I: integer; //byte
begin
  Result := '';
  for I := 1 to Length(S) do
  begin
    Result := Result + IntToHex(byte(char(byte(S[I]) xor (Key shr 8))), 2);
    Key := (byte(char(byte(S[I]) xor (Key shr 8))) + Key) * C1 + C2;
  end;
end;     

function TdmPassport.Decrypt(const S: ShortString; Key: Word): String;
var
 I: integer; //byte;
 x: char;
begin
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

end.
