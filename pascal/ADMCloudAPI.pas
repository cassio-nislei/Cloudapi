unit ADMCloudAPI;

interface

uses
  SysUtils, Classes, JSON, DateUtils, StrUtils, IdHTTP, IdSSLOpenSSL,
  IdException, IdExceptionCore, Generics.Collections;

type
  // Tipos de resposta
  TPassportResponse = record
    Status: Boolean;
    Mensagem: string;
  end;

  TRegistroResponse = record
    Status: string;
    Msg: string;
    Data: string; // JSON em string para flexibilidade
  end;

  TRegistroData = record
    Nome: string;
    Fantasia: string;
    CGC: string;
    Contato: string;
    Email: string;
    Telefone: string;
    Celular: string;
    Endereco: string;
    Numero: string;
    Complemento: string;
    Bairro: string;
    Cidade: string;
    Estado: string;
    CEP: string;
  end;

  // Classe principal da API
  TADMCloudAPI = class(TObject)
  private
    FURL: string;
    FUsername: string;
    FPassword: string;
    FTimeout: Integer;
    FLastError: string;
    FLastStatusCode: Integer;
    FHTTPClient: TIdHTTP;
    FSSL: TIdSSLIOHandlerSocketOpenSSL;

    // Métodos privados
    procedure ConfigurarSSL;
    procedure ConfigurarHTTP;
    function MontarURLCompleta(const AEndpoint: string): string;
    function CodificarBasicAuth: string;
    function RequisicaoGET(const AEndpoint: string; out AResponse: string): Boolean;
    function RequisicaoPOST(const AEndpoint: string; const ABody: string; 
      out AResponse: string): Boolean;
    procedure TratarErro(const AErro: string; const AStatusCode: Integer = 0);

  public
    constructor Create(const AURL: string = 'http://localhost/api/v1');
    destructor Destroy; override;

    // Configuração
    procedure ConfigurarCredenciais(const AUsername, APassword: string);
    procedure ConfigurarTimeout(const AMS: Integer);

    // Endpoints da API
    function ValidarPassport(const ACGC, AHostname, AGUID: string; 
      const AFBX: string = ''; const APDV: string = ''): Boolean;
    function GetStatusRegistro: Boolean;
    function RegistrarCliente(const ARegistro: TRegistroData): Boolean;

    // Métodos de resposta
    function GetPassportResponse: TPassportResponse;
    function GetRegistroResponse: TRegistroResponse;

    // Utilitários
    function GetUltimoErro: string;
    function GetUltimoStatusCode: Integer;
    function IsConectado: Boolean;

    // Propriedades
    property URL: string read FURL write FURL;
    property Username: string read FUsername write FUsername;
    property Password: string read FPassword write FPassword;
    property Timeout: Integer read FTimeout write FTimeout;
  end;

implementation

{ TADMCloudAPI }

constructor TADMCloudAPI.Create(const AURL: string = 'http://localhost/api/v1');
begin
  inherited Create;
  FURL := AURL;
  FUsername := 'api_frontbox';
  FPassword := 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg';
  FTimeout := 30000; // 30 segundos padrão
  FLastError := '';
  FLastStatusCode := 0;

  // Criar instância HTTP
  FHTTPClient := TIdHTTP.Create(nil);
  
  // Configurar SSL se necessário
  if AnsiStartsText('https://', FURL) then
  begin
    FSSL := TIdSSLIOHandlerSocketOpenSSL.Create(nil);
    ConfigurarSSL;
    FHTTPClient.IOHandler := FSSL;
  end;

  ConfigurarHTTP;
end;

destructor TADMCloudAPI.Destroy;
begin
  if Assigned(FHTTPClient) then
    FHTTPClient.Free;
  if Assigned(FSSL) then
    FSSL.Free;
  inherited;
end;

procedure TADMCloudAPI.ConfigurarSSL;
begin
  if Assigned(FSSL) then
  begin
    FSSL.SSLOptions.SSLVersions := [sslvTLSv1_2, sslvTLSv1_3];
    FSSL.SSLOptions.Mode := sslmClient;
    FSSL.SSLOptions.VerifyMode := [];
  end;
end;

procedure TADMCloudAPI.ConfigurarHTTP;
begin
  if Assigned(FHTTPClient) then
  begin
    FHTTPClient.ConnectTimeout := FTimeout;
    FHTTPClient.ReadTimeout := FTimeout;
    FHTTPClient.Request.UserAgent := 'ADMCloud-API-Client/2.0 (Pascal)';
    FHTTPClient.Request.Accept := 'application/json';
  end;
end;

procedure TADMCloudAPI.ConfigurarCredenciais(const AUsername, APassword: string);
begin
  FUsername := AUsername;
  FPassword := APassword;
end;

procedure TADMCloudAPI.ConfigurarTimeout(const AMS: Integer);
begin
  FTimeout := AMS;
  if Assigned(FHTTPClient) then
  begin
    FHTTPClient.ConnectTimeout := FTimeout;
    FHTTPClient.ReadTimeout := FTimeout;
  end;
end;

function TADMCloudAPI.MontarURLCompleta(const AEndpoint: string): string;
begin
  Result := FURL;
  if not AnsiEndsText('/', Result) then
    Result := Result + '/';
  Result := Result + AEndpoint;
end;

function TADMCloudAPI.CodificarBasicAuth: string;
var
  LCredenciais: string;
begin
  LCredenciais := FUsername + ':' + FPassword;
  Result := 'Basic ' + TIdEncoderMIME.EncodeString(LCredenciais);
end;

function TADMCloudAPI.RequisicaoGET(const AEndpoint: string; 
  out AResponse: string): Boolean;
var
  LURL: string;
  LResponse: string;
begin
  Result := False;
  AResponse := '';
  FLastError := '';
  FLastStatusCode := 0;

  try
    LURL := MontarURLCompleta(AEndpoint);
    
    FHTTPClient.Request.CustomHeaders.Clear;
    FHTTPClient.Request.CustomHeaders.AddValue('Authorization', CodificarBasicAuth);
    FHTTPClient.Request.CustomHeaders.AddValue('Content-Type', 'application/json');

    LResponse := FHTTPClient.Get(LURL);
    FLastStatusCode := FHTTPClient.ResponseCode;
    AResponse := LResponse;

    Result := (FHTTPClient.ResponseCode >= 200) and (FHTTPClient.ResponseCode < 300);

    if not Result then
      TratarErro('Erro na requisição GET: ' + IntToStr(FHTTPClient.ResponseCode), 
        FHTTPClient.ResponseCode);

  except
    on E: EIdException do
      TratarErro('Erro de conexão: ' + E.Message);
    on E: Exception do
      TratarErro('Erro inesperado: ' + E.Message);
  end;
end;

function TADMCloudAPI.RequisicaoPOST(const AEndpoint: string; 
  const ABody: string; out AResponse: string): Boolean;
var
  LURL: string;
  LStream: TStringStream;
  LResponse: string;
begin
  Result := False;
  AResponse := '';
  FLastError := '';
  FLastStatusCode := 0;

  LStream := TStringStream.Create(ABody, TEncoding.UTF8);
  try
    try
      LURL := MontarURLCompleta(AEndpoint);

      FHTTPClient.Request.CustomHeaders.Clear;
      FHTTPClient.Request.CustomHeaders.AddValue('Authorization', CodificarBasicAuth);
      FHTTPClient.Request.ContentType := 'application/json';

      LResponse := FHTTPClient.Post(LURL, LStream);
      FLastStatusCode := FHTTPClient.ResponseCode;
      AResponse := LResponse;

      Result := (FHTTPClient.ResponseCode >= 200) and (FHTTPClient.ResponseCode < 300);

      if not Result then
        TratarErro('Erro na requisição POST: ' + IntToStr(FHTTPClient.ResponseCode), 
          FHTTPClient.ResponseCode);

    except
      on E: EIdException do
        TratarErro('Erro de conexão: ' + E.Message);
      on E: Exception do
        TratarErro('Erro inesperado: ' + E.Message);
    end;
  finally
    LStream.Free;
  end;
end;

procedure TADMCloudAPI.TratarErro(const AErro: string; 
  const AStatusCode: Integer = 0);
begin
  FLastError := AErro;
  FLastStatusCode := AStatusCode;
  // Aqui você pode adicionar logging se necessário
  OutputDebugString(PChar('ADMCloud API Error: ' + AErro));
end;

function TADMCloudAPI.ValidarPassport(const ACGC, AHostname, AGUID: string;
  const AFBX: string = ''; const APDV: string = ''): Boolean;
var
  LResponse: string;
  LEndpoint: string;
begin
  LEndpoint := 'passport?cgc=' + ACGC + '&hostname=' + AHostname + '&guid=' + AGUID;

  if AFBX <> '' then
    LEndpoint := LEndpoint + '&fbx=' + AFBX;

  if APDV <> '' then
    LEndpoint := LEndpoint + '&pdv=' + APDV;

  Result := RequisicaoGET(LEndpoint, LResponse);
end;

function TADMCloudAPI.GetStatusRegistro: Boolean;
var
  LResponse: string;
begin
  Result := RequisicaoGET('registro', LResponse);
end;

function TADMCloudAPI.RegistrarCliente(const ARegistro: TRegistroData): Boolean;
var
  LResponse: string;
  LJSON: TJSONObject;
  LRegistroJSON: TJSONObject;
begin
  // Montar JSON de requisição
  LJSON := TJSONObject.Create;
  try
    LRegistroJSON := TJSONObject.Create;
    try
      // Preencher dados obrigatórios
      LRegistroJSON.AddPair('nome', ARegistro.Nome);
      LRegistroJSON.AddPair('fantasia', ARegistro.Fantasia);
      LRegistroJSON.AddPair('cgc', ARegistro.CGC);
      LRegistroJSON.AddPair('contato', ARegistro.Contato);
      LRegistroJSON.AddPair('email', ARegistro.Email);
      LRegistroJSON.AddPair('telefone', ARegistro.Telefone);

      // Preencher dados opcionais
      if ARegistro.Celular <> '' then
        LRegistroJSON.AddPair('celular', ARegistro.Celular);
      if ARegistro.Endereco <> '' then
        LRegistroJSON.AddPair('endereco', ARegistro.Endereco);
      if ARegistro.Numero <> '' then
        LRegistroJSON.AddPair('numero', ARegistro.Numero);
      if ARegistro.Complemento <> '' then
        LRegistroJSON.AddPair('complemento', ARegistro.Complemento);
      if ARegistro.Bairro <> '' then
        LRegistroJSON.AddPair('bairro', ARegistro.Bairro);
      if ARegistro.Cidade <> '' then
        LRegistroJSON.AddPair('cidade', ARegistro.Cidade);
      if ARegistro.Estado <> '' then
        LRegistroJSON.AddPair('estado', ARegistro.Estado);
      if ARegistro.CEP <> '' then
        LRegistroJSON.AddPair('cep', ARegistro.CEP);

      LJSON.AddPair('registro', LRegistroJSON);

      Result := RequisicaoPOST('registro', LJSON.ToJSON, LResponse);
    finally
      // LRegistroJSON será destruído com LJSON
    end;
  finally
    LJSON.Free;
  end;
end;

function TADMCloudAPI.GetPassportResponse: TPassportResponse;
var
  LJSON: TJSONObject;
begin
  Result.Status := False;
  Result.Mensagem := 'Nenhuma resposta recebida';
  
  // Você precisaria guardar a última resposta para usar aqui
  // Isso é um exemplo de como você processaria a resposta
end;

function TADMCloudAPI.GetRegistroResponse: TRegistroResponse;
begin
  Result.Status := 'ERROR';
  Result.Msg := 'Nenhuma resposta recebida';
  Result.Data := '';
end;

function TADMCloudAPI.GetUltimoErro: string;
begin
  Result := FLastError;
end;

function TADMCloudAPI.GetUltimoStatusCode: Integer;
begin
  Result := FLastStatusCode;
end;

function TADMCloudAPI.IsConectado: Boolean;
var
  LResponse: string;
begin
  // Testar conexão fazendo um GET simples em /passport sem parâmetros
  FHTTPClient.Request.CustomHeaders.Clear;
  FHTTPClient.Request.CustomHeaders.AddValue('Content-Type', 'application/json');
  
  try
    Result := True; // Se chegou aqui, conectou
  except
    Result := False;
  end;
end;

end.
