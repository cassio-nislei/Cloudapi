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
    FLastGenericResponse: string;

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

    // ========== Endpoints Públicos ==========
    // /api/pessoas
    function ConsultarPessoa(const ACNPJ: string; out AResponse: string): Boolean;
    function ConsultarPessoaById(const AId: string; out AResponse: string): Boolean;
    
    // /api/v1/passport
    function ValidarPassport(const ACGC, AHostname, AGUID: string; 
      const AFBX: string = ''; const APDV: string = ''): Boolean;
    
    // /api/v1/registro
    function GetStatusRegistro: Boolean;
    function RegistrarCliente(const ARegistro: TRegistroData): Boolean;

    // ========== Endpoints Empresa ==========
    function GetEmpresas(out AResponse: string): Boolean;
    function GetEmpresaById(const AId: string; out AResponse: string): Boolean;
    function CriarEmpresa(const ADados: string): Boolean;

    // ========== Endpoints Usuarios ==========
    function GetUsuarios(out AResponse: string): Boolean;
    function GetUsuarioById(const AId: string; out AResponse: string): Boolean;
    function GetPermissoes(out AResponse: string): Boolean;
    function SolicitarResetSenha(const AEmail: string; out AResponse: string): Boolean;

    // ========== Endpoints Grupos ==========
    function GetGrupos(out AResponse: string): Boolean;
    function GetGrupoById(const AId: string; out AResponse: string): Boolean;
    function GetPermissoesGrupo(const AIdGrupo: string; out AResponse: string): Boolean;

    // ========== Endpoints Perfil ==========
    function GetPerfil(out AResponse: string): Boolean;
    function AtualizarPerfil(const ADados: string): Boolean;

    // ========== Endpoints FrontBox ==========
    function GetInfoFrontBox(const ACGC: string; out AResponse: string): Boolean;
    function VerificaAcessoImpostos(const ACGC: string; out AResponse: string): Boolean;

    // ========== Endpoints Outros ==========
    function GetFiliais(out AResponse: string): Boolean;
    function GetFilialById(const AId: string; out AResponse: string): Boolean;
    function GetProdutos(out AResponse: string): Boolean;
    function GetProdutoById(const AId: string; out AResponse: string): Boolean;
    function GetDiarios(out AResponse: string): Boolean;
    function GetDiarioById(const AId: string; out AResponse: string): Boolean;
    function GetModulos(out AResponse: string): Boolean;
    function GetModuloById(const AId: string; out AResponse: string): Boolean;
    function GetVisitantes(out AResponse: string): Boolean;
    function GetVisitanteById(const AId: string; out AResponse: string): Boolean;

    // Métodos de resposta
    function GetPassportResponse: TPassportResponse;
    function GetRegistroResponse: TRegistroResponse;
    function GetLastPassportResponseRaw: string;
    function GetLastRegistroResponseRaw: string;
    function GetLastGenericResponseRaw: string;

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
  FUsername := ADMCloud_USER;
  FPassword := ADMCloud_PASS;
  FTimeout := ADMCloud_TIMEOUT_PADRAO;
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
    // /api/pessoas requer Basic Auth
    if AnsiStartsText('pessoas', AEndpoint) or 
       AnsiStartsText('api/pessoas', AEndpoint) then
      FHTTPClient.Request.CustomHeaders.AddValue('Authorization', CodificarBasicAuth);

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
      // POST /registro requer Basic Auth
      FHTTPClient.Request.CustomHeaders.AddValue('Authorization', CodificarBasicAuth);
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
  
  // Endpoint correto: GET /api/passport
  LEndpoint := ADMCloud_ENDPOINT_PASSPORT + '?cgc=' + ACGC + '&hostname=' + AHostname + '&guid=' + AGUID;

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
  Result := RequisicaoGET(ADMCloud_ENDPOINT_REGISTRO_GET, LResponse);
end;

function TADMCloudAPI.RegistrarCliente(const ARegistro: TRegistroData): Boolean;
var
  LResponse: string;
  LJSON: TJSONObject;
  LRegistroJSON: TJSONObject;
  LJsonPayload: string;
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
  
  // Montar JSON de requisição COM WRAPPER 'registro' conforme Swagger
  LRegistroJSON := TJSONObject.Create;
  try
    // Preencher dados obrigatórios
    LRegistroJSON.AddPair('nome', ARegistro.Nome);
    LRegistroJSON.AddPair('fantasia', ARegistro.Fantasia);
    LRegistroJSON.AddPair('cgc', ARegistro.CGC);  // Campo 'cgc' (minúsculo) conforme Swagger
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

    // Enviar COM wrapper 'registro' conforme especificação Swagger
    var LWrapperJSON := TJSONObject.Create;
    try
      LWrapperJSON.AddPair('registro', LRegistroJSON);
      LJsonPayload := LWrapperJSON.ToJSON;
      
      // Endpoint correto: POST /api/v1/registro conforme Swagger
      Result := RequisicaoPOST(ADMCloud_ENDPOINT_REGISTRO_POST, LJsonPayload, LResponse);
      
      // Armazenar resposta para debug
      FLastRegistroResponse := LResponse;
    finally
      LWrapperJSON.Free;
    end;
  finally
    // LRegistroJSON será libertado quando LWrapperJSON for libertado (ele contém a referência)
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
  // O endpoint /pessoas não requer autenticação de sessão, mas pode usar Bearer Token
  Result := RequisicaoGET(ADMCloud_ENDPOINT_REGISTRO_GET + '?cnpj=' + LCNPJLimpo, LResponseLocal);
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

function TADMCloudAPI.ConsultarPessoaById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if AId = '' then
  begin
    TratarErro('ID da pessoa é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET(ADMCloud_ENDPOINT_REGISTRO_GET + '/id/' + AId, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

// ========== Empresa ==========
function TADMCloudAPI.GetEmpresas(out AResponse: string): Boolean;
begin
  Result := RequisicaoGET('api/empresa', AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.GetEmpresaById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if AId = '' then
  begin
    TratarErro('ID da empresa é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/empresa/' + AId, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.CriarEmpresa(const ADados: string): Boolean;
var
  LResponse: string;
begin
  Result := RequisicaoPOST('empresa', ADados, LResponse);
  if Result then
    FLastGenericResponse := LResponse;
end;

// ========== Usuarios ==========
function TADMCloudAPI.GetUsuarios(out AResponse: string): Boolean;
begin
  Result := RequisicaoGET('api/usuarios', AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.GetUsuarioById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if AId = '' then
  begin
    TratarErro('ID do usuário é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/usuarios/' + AId, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.GetPermissoes(out AResponse: string): Boolean;
begin
  Result := RequisicaoGET('api/usuarios/permissoes', AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.SolicitarResetSenha(const AEmail: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if AEmail = '' then
  begin
    TratarErro('Email é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/usuarios/requestPass/' + AEmail, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

// ========== Grupos ==========
function TADMCloudAPI.GetGrupos(out AResponse: string): Boolean;
begin
  Result := RequisicaoGET('api/grupos', AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.GetGrupoById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if AId = '' then
  begin
    TratarErro('ID do grupo é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/grupos/' + AId, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.GetPermissoesGrupo(const AIdGrupo: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if AIdGrupo = '' then
  begin
    TratarErro('ID do grupo é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/grupos/' + AIdGrupo + '/permissoes', AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

// ========== Perfil ==========
function TADMCloudAPI.GetPerfil(out AResponse: string): Boolean;
begin
  Result := RequisicaoGET('api/perfil', AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.AtualizarPerfil(const ADados: string): Boolean;
var
  LResponse: string;
begin
  Result := RequisicaoPOST('api/perfil', ADados, LResponse);
  if Result then
    FLastGenericResponse := LResponse;
end;

// ========== FrontBox ==========
function TADMCloudAPI.GetInfoFrontBox(const ACGC: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if ACGC = '' then
  begin
    TratarErro('CGC é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/frontbox/getInfo?q=' + ACGC, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.VerificaAcessoImpostos(const ACGC: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if ACGC = '' then
  begin
    TratarErro('CGC é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/frontbox/acessaImpostos?cgc=' + ACGC, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

// ========== Filiais ==========
function TADMCloudAPI.GetFiliais(out AResponse: string): Boolean;
begin
  Result := RequisicaoGET('api/filiais', AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.GetFilialById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if AId = '' then
  begin
    TratarErro('ID da filial é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/filiais/' + AId, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

// ========== Produtos ==========
function TADMCloudAPI.GetProdutos(out AResponse: string): Boolean;
begin
  Result := RequisicaoGET('api/produtos', AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.GetProdutoById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if AId = '' then
  begin
    TratarErro('ID do produto é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/produtos/' + AId, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

// ========== Diarios ==========
function TADMCloudAPI.GetDiarios(out AResponse: string): Boolean;
begin
  Result := RequisicaoGET('api/diarios', AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.GetDiarioById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if AId = '' then
  begin
    TratarErro('ID do diário é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/diarios/' + AId, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

// ========== Modulos ==========
function TADMCloudAPI.GetModulos(out AResponse: string): Boolean;
begin
  Result := RequisicaoGET('api/modulos', AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.GetModuloById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if AId = '' then
  begin
    TratarErro('ID do módulo é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/modulos/' + AId, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

// ========== Visitantes ==========
function TADMCloudAPI.GetVisitantes(out AResponse: string): Boolean;
begin
  Result := RequisicaoGET('api/visitantes', AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.GetVisitanteById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  
  if AId = '' then
  begin
    TratarErro('ID do visitante é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/visitantes/' + AId, AResponse);
  if Result then
    FLastGenericResponse := AResponse;
end;

function TADMCloudAPI.GetLastGenericResponseRaw: string;
begin
  Result := FLastGenericResponse;
end;

end.
