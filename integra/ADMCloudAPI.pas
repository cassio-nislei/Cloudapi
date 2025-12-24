unit ADMCloudAPI;

interface

uses
  SysUtils, Classes, JSON, DateUtils, StrUtils, IdHTTP, IdSSLOpenSSL,
  IdException, IdExceptionCore, Generics.Collections, System.NetEncoding,
  ADMCloudConsts;

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
    FLastPassportResponse: string;
    FLastRegistroResponse: string;

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
    constructor Create(const AURL: string = '');
    destructor Destroy; override;

    // Configuração
    procedure ConfigurarCredenciais(const AUsername, APassword: string);
    procedure ConfigurarTimeout(const AMS: Integer);

    // Endpoints da API
    function ValidarPassport(const ACGC, AHostname, AGUID: string; 
      const AFBX: string = ''; const APDV: string = ''): Boolean;
    function GetStatusRegistro: Boolean;
    function RegistrarCliente(const ARegistro: TRegistroData): Boolean;
    function ConsultarPessoa(const ACNPJ: string; out AResponse: string): Boolean;

    // Métodos de resposta
    function GetPassportResponse: TPassportResponse;
    function GetRegistroResponse: TRegistroResponse;
    function GetLastPassportResponseRaw: string;
    function GetLastRegistroResponseRaw: string;

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

constructor TADMCloudAPI.Create(const AURL: string = '');
begin
  inherited Create;
  // Se AURL vazio, usar URL padrão de produção
  if AURL = '' then
    FURL := ADMCloud_URL_PROD
  else
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
    FSSL.SSLOptions.SSLVersions := [sslvTLSv1_2];
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
  Result := 'Basic ' + TNetEncoding.Base64.Encode(LCredenciais);
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
    FHTTPClient.Request.CustomHeaders.AddValue('Content-Type', 'application/json');
    
    // /api/passport é público (sem autenticação)
    // /registro, /api/pessoas e /pessoas requerem Bearer Token
    if AnsiStartsText('registro', AEndpoint) or 
       AnsiStartsText('pessoas', AEndpoint) or 
       AnsiStartsText('api/pessoas', AEndpoint) then
      FHTTPClient.Request.CustomHeaders.AddValue('Authorization', 'Bearer ' + FPassword);

    LResponse := FHTTPClient.Get(LURL);
    FLastStatusCode := FHTTPClient.ResponseCode;
    AResponse := LResponse;

    // Armazenar resposta conforme endpoint
    if AnsiStartsText('api/passport', AEndpoint) or AnsiStartsText('passport', AEndpoint) then
      FLastPassportResponse := LResponse
    else if AnsiStartsText('registro', AEndpoint) then
      FLastRegistroResponse := LResponse
    else if AnsiStartsText('pessoas', AEndpoint) or AnsiStartsText('api/pessoas', AEndpoint) then
      FLastRegistroResponse := LResponse;  // Reusar para pessoas também

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
      // POST /registro requer Bearer Token
      FHTTPClient.Request.CustomHeaders.AddValue('Authorization', 'Bearer ' + FPassword);
      FHTTPClient.Request.ContentType := 'application/json';

      LResponse := FHTTPClient.Post(LURL, LStream);
      FLastStatusCode := FHTTPClient.ResponseCode;
      AResponse := LResponse;

      // Armazenar resposta
      if AnsiStartsText('registro', AEndpoint) then
        FLastRegistroResponse := LResponse;

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
  //OutputDebugString(PChar('ADMCloud API Error: ' + AErro));
end;

function TADMCloudAPI.ValidarPassport(const ACGC, AHostname, AGUID: string;
  const AFBX: string = ''; const APDV: string = ''): Boolean;
var
  LResponse: string;
  LEndpoint: string;
begin
  Result := False;
  
  // Validar campos obrigatórios
  if (ACGC = '') or (AHostname = '') or (AGUID = '') then
  begin
    TratarErro('Parâmetros obrigatórios não preenchidos: cgc, hostname, guid');
    Exit;
  end;
  
  // Endpoint correto conforme Swagger: GET /api/passport
  LEndpoint := 'api/passport?cgc=' + ACGC + '&hostname=' + AHostname + '&guid=' + AGUID;

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
  // Endpoint correto conforme Swagger: GET /api/pessoas
  Result := RequisicaoGET('api/pessoas', LResponse);
end;

function TADMCloudAPI.RegistrarCliente(const ARegistro: TRegistroData): Boolean;
var
  LResponse: string;
  LJSON: TJSONObject;
  LRegistroJSON: TJSONObject;
begin
  Result := False;
  
  // Validar campos obrigatórios conforme API OpenAPI
  if (ARegistro.Nome = '') or (ARegistro.Fantasia = '') or
     (ARegistro.CGC = '') or (ARegistro.Contato = '') or
     (ARegistro.Email = '') or (ARegistro.Telefone = '') or
     (ARegistro.Endereco = '') or (ARegistro.Numero = '') or
     (ARegistro.Bairro = '') or (ARegistro.Cidade = '') or
     (ARegistro.Estado = '') or (ARegistro.CEP = '') then
  begin
    TratarErro('Todos os campos são obrigatórios para registro: ' +
      'nome, fantasia, cgc, contato, email, telefone, endereco, numero, bairro, cidade, estado, cep');
    Exit;
  end;
  
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
      LRegistroJSON.AddPair('endereco', ARegistro.Endereco);
      LRegistroJSON.AddPair('numero', ARegistro.Numero);
      LRegistroJSON.AddPair('bairro', ARegistro.Bairro);
      LRegistroJSON.AddPair('cidade', ARegistro.Cidade);
      LRegistroJSON.AddPair('estado', ARegistro.Estado);
      LRegistroJSON.AddPair('cep', ARegistro.CEP);

      // Preencher dados opcionais
      if ARegistro.Celular <> '' then
        LRegistroJSON.AddPair('celular', ARegistro.Celular);
      if ARegistro.Complemento <> '' then
        LRegistroJSON.AddPair('complemento', ARegistro.Complemento);

      LJSON.AddPair('registro', LRegistroJSON);

      // Endpoint correto conforme Swagger: POST /api/pessoas
      Result := RequisicaoPOST('api/pessoas', LJSON.ToJSON, LResponse);
    finally
      // LRegistroJSON será destruído com LJSON
    end;
  finally
    LJSON.Free;
  end;
end;

function TADMCloudAPI.ConsultarPessoa(const ACNPJ: string; out AResponse: string): Boolean;
var
  LCNPJLimpo: string;
  LResponseLocal: string;
begin
  Result := False;
  AResponse := '';
  LResponseLocal := '';

  // Limpar CNPJ
  LCNPJLimpo := StringReplace(StringReplace(ACNPJ, '.', '', [rfReplaceAll]), '/', '', [rfReplaceAll]);
  LCNPJLimpo := StringReplace(LCNPJLimpo, '-', '', [rfReplaceAll]);

  // Fazer requisição GET /api/pessoas?cnpj=XXXXX para buscar a pessoa na API
  // O endpoint /api/pessoas não requer autenticação de sessão, mas pode usar Bearer Token
  Result := RequisicaoGET('api/pessoas?cnpj=' + LCNPJLimpo, LResponseLocal);
  AResponse := LResponseLocal;
  
  // Armazenar resposta
  if Result then
    FLastRegistroResponse := AResponse;
    
  // DEBUG: Log para entender o que está acontecendo
  if not Result then
  begin
    // Se falhou, armazenar resposta mesmo assim para debug
    FLastRegistroResponse := AResponse;
  end;
end;

function TADMCloudAPI.GetPassportResponse: TPassportResponse;
var
  LJSON: TJSONObject;
begin
  Result.Status := False;
  Result.Mensagem := 'Nenhuma resposta recebida';
  
  if FLastPassportResponse = '' then
    Exit;

  try
    LJSON := TJSONObject.ParseJSONValue(FLastPassportResponse) as TJSONObject;
    if Assigned(LJSON) then
    try
      if LJSON.TryGetValue<Boolean>('Status', Result.Status) then
        // Success
      else
        Result.Status := False;
        
      if LJSON.TryGetValue<string>('Mensagem', Result.Mensagem) then
        // Success
      else
        Result.Mensagem := 'Erro ao parse da mensagem';
    finally
      LJSON.Free;
    end;
  except
    on E: Exception do
    begin
      Result.Status := False;
      Result.Mensagem := 'Erro ao processar resposta: ' + E.Message;
    end;
  end;
end;

function TADMCloudAPI.GetRegistroResponse: TRegistroResponse;
begin
  Result.Status := 'ERRO';
  Result.Msg := 'Nenhuma resposta recebida';
  Result.Data := '';
  
  // Você pode expandir isso para fazer parse completo do JSON
  // Por enquanto, retorna a resposta bruta em Data
  Result.Data := FLastRegistroResponse;
end;

function TADMCloudAPI.GetLastPassportResponseRaw: string;
begin
  Result := FLastPassportResponse;
end;

function TADMCloudAPI.GetLastRegistroResponseRaw: string;
begin
  Result := FLastRegistroResponse;
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
  // Testar conexão fazendo um GET em /api/passport (endpoint público)
  // Qualquer CGC/hostname/GUID válido serve para teste
  Result := RequisicaoGET('api/passport?cgc=00000000000000&hostname=TEST&guid=00000000-0000-0000-0000-000000000000', LResponse);
end;

end.
