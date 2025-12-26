unit ADMCloudAPI;

interface

uses
  SysUtils, Classes, JSON, DateUtils, StrUtils, IdHTTP, IdSSLOpenSSL,
  IdException, IdExceptionCore, Generics.Collections, System.NetEncoding,
  Data.DB, ADMCloudConsts;

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
    CNAE: string;
    IM: string;
    Tipo: string;
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
    function GetInfoFrontBox(const ACGC: string; out AResponse: string): Boolean;
    function VerificaAcessoImpostos(const ACGC: string; out AResponse: string): Boolean;
    
    // Métodos adicionais para compatibilidade com TADMCloudHelper
    function ConsultarPessoaById(const AId: string; out AResponse: string): Boolean;
    function GetEmpresas(out AResponse: string): Boolean;
    function GetEmpresaById(const AId: string; out AResponse: string): Boolean;
    function CriarEmpresa(const ADados: string): Boolean;
    function GetUsuarios(out AResponse: string): Boolean;
    function GetUsuarioById(const AId: string; out AResponse: string): Boolean;
    function GetPermissoes(out AResponse: string): Boolean;
    function SolicitarResetSenha(const AEmail: string; out AResponse: string): Boolean;
    function GetGrupos(out AResponse: string): Boolean;
    function GetGrupoById(const AId: string; out AResponse: string): Boolean;
    function GetPermissoesGrupo(const AIdGrupo: string; out AResponse: string): Boolean;
    function GetPerfil(out AResponse: string): Boolean;
    function AtualizarPerfil(const ADados: string): Boolean;
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
    function AtualizarPessoa(const ACNPJ, ADados: string): Boolean;

    // Métodos de resposta
    function GetPassportResponse: TPassportResponse;
    function GetRegistroResponse: TRegistroResponse;
    function GetLastPassportResponseRaw: string;
    function GetLastRegistroResponseRaw: string;
    function GetInfoFrontBoxParsed(out AData: TRegistroData): Boolean;

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
    LRegistroJSON.AddPair('razao', ARegistro.Nome);
    LRegistroJSON.AddPair('fantasia', ARegistro.Fantasia);
    LRegistroJSON.AddPair('cgc', ARegistro.CGC);  // Campo 'cgc' (minúsculo) conforme Swagger
    LRegistroJSON.AddPair('contato', ARegistro.Contato);
    LRegistroJSON.AddPair('email', ARegistro.Email);
    LRegistroJSON.AddPair('telefone', ARegistro.Telefone);
    LRegistroJSON.AddPair('logradouro', ARegistro.Endereco);
    LRegistroJSON.AddPair('numero', ARegistro.Numero);
    LRegistroJSON.AddPair('bairro', ARegistro.Bairro);
    LRegistroJSON.AddPair('municipio', ARegistro.Cidade);
    LRegistroJSON.AddPair('uf', ARegistro.Estado);
    LRegistroJSON.AddPair('cep', ARegistro.CEP);

    // Preencher dados opcionais
    if ARegistro.Celular <> '' then
      LRegistroJSON.AddPair('whatsapp', ARegistro.Celular);
    if ARegistro.Complemento <> '' then
      LRegistroJSON.AddPair('complemento', ARegistro.Complemento);
    if ARegistro.Tipo <> '' then
      LRegistroJSON.AddPair('tipo', ARegistro.Tipo);
    if ARegistro.CNAE <> '' then
      LRegistroJSON.AddPair('cnae', ARegistro.CNAE);
    if ARegistro.IM <> '' then
      LRegistroJSON.AddPair('im', ARegistro.IM);

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

function TADMCloudAPI.GetInfoFrontBox(const ACGC: string; out AResponse: string): Boolean;
var
  LCGCLimpo: string;
begin
  Result := False;
  AResponse := '';
  
  // Remover formatação do CNPJ/CGC
  LCGCLimpo := StringReplace(StringReplace(ACGC, '.', '', [rfReplaceAll]), '/', '', [rfReplaceAll]);
  LCGCLimpo := StringReplace(LCGCLimpo, '-', '', [rfReplaceAll]);
  
  if LCGCLimpo = '' then
  begin
    TratarErro('CGC/CNPJ é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/frontbox/getInfo?q=' + LCGCLimpo, AResponse);
  
  // Armazenar resposta também em FLastRegistroResponse para compatibilidade
  if Result then
    FLastRegistroResponse := AResponse;
end;

function TADMCloudAPI.VerificaAcessoImpostos(const ACGC: string; out AResponse: string): Boolean;
var
  LCGCLimpo: string;
begin
  Result := False;
  AResponse := '';
  
  // Remover formatação do CNPJ/CGC
  LCGCLimpo := StringReplace(StringReplace(ACGC, '.', '', [rfReplaceAll]), '/', '', [rfReplaceAll]);
  LCGCLimpo := StringReplace(LCGCLimpo, '-', '', [rfReplaceAll]);
  
  if LCGCLimpo = '' then
  begin
    TratarErro('CGC/CNPJ é obrigatório');
    Exit;
  end;
  
  Result := RequisicaoGET('api/frontbox/acessaImpostos?cgc=' + LCGCLimpo, AResponse);
end;

{ Função auxiliar para extrair valor entre tags }
function ExtrairValorTag(const ATexto: string; const ACampo: string): string;
var
  LInicioTag, LFimTag: Integer;
begin
  Result := '';
  LInicioTag := AnsiPos('{' + ACampo + '}', ATexto);
  if LInicioTag > 0 then
  begin
    LInicioTag := LInicioTag + Length(ACampo) + 2;
    LFimTag := AnsiPos('{/' + ACampo + '}', ATexto);
    if LFimTag > LInicioTag then
      Result := Copy(ATexto, LInicioTag, LFimTag - LInicioTag);
  end;
end;

function TADMCloudAPI.GetInfoFrontBoxParsed(out AData: TRegistroData): Boolean;
var
  LResposta: string;
begin
  Result := False;
  FillChar(AData, SizeOf(AData), 0);
  LResposta := FLastRegistroResponse;
  if LResposta = '' then
  begin
    TratarErro('Nenhuma resposta disponível. Execute GetInfoFrontBox primeiro.');
    Exit;
  end;
  if AnsiContainsText(LResposta, '{status}ERRO{/status}') then
  begin
    TratarErro('Erro na resposta do FrontBox: ' + ExtrairValorTag(LResposta, 'mensagem'));
    Exit;
  end;
  AData.Nome := ExtrairValorTag(LResposta, 'nome');
  AData.Fantasia := ExtrairValorTag(LResposta, 'fantasia');
  AData.CGC := ExtrairValorTag(LResposta, 'cgc');
  AData.Email := ExtrairValorTag(LResposta, 'email');
  AData.Contato := ExtrairValorTag(LResposta, 'telefone');
  AData.Telefone := ExtrairValorTag(LResposta, 'telefone');
  AData.Endereco := ExtrairValorTag(LResposta, 'endereco');
  AData.Numero := ExtrairValorTag(LResposta, 'numero');
  AData.Complemento := ExtrairValorTag(LResposta, 'complemento');
  AData.Bairro := ExtrairValorTag(LResposta, 'bairro');
  AData.Cidade := ExtrairValorTag(LResposta, 'cidade');
  AData.Estado := ExtrairValorTag(LResposta, 'estado');
  AData.CEP := ExtrairValorTag(LResposta, 'cep');
  AData.CNAE := ExtrairValorTag(LResposta, 'cnae');
  AData.IM := ExtrairValorTag(LResposta, 'im');
  AData.Tipo := ExtrairValorTag(LResposta, 'tipo');
  Result := AData.Nome <> '';
end;

// Stub implementations for required API methods
function TADMCloudAPI.ConsultarPessoaById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('ConsultarPessoaById not yet implemented');
end;

function TADMCloudAPI.GetEmpresas(out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetEmpresas not yet implemented');
end;

function TADMCloudAPI.GetEmpresaById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetEmpresaById not yet implemented');
end;

function TADMCloudAPI.CriarEmpresa(const ADados: string): Boolean;
begin
  Result := False;
  TratarErro('CriarEmpresa not yet implemented');
end;

function TADMCloudAPI.GetUsuarios(out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetUsuarios not yet implemented');
end;

function TADMCloudAPI.GetUsuarioById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetUsuarioById not yet implemented');
end;

function TADMCloudAPI.GetPermissoes(out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetPermissoes not yet implemented');
end;

function TADMCloudAPI.SolicitarResetSenha(const AEmail: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('SolicitarResetSenha not yet implemented');
end;

function TADMCloudAPI.GetGrupos(out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetGrupos not yet implemented');
end;

function TADMCloudAPI.GetGrupoById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetGrupoById not yet implemented');
end;

function TADMCloudAPI.GetPermissoesGrupo(const AIdGrupo: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetPermissoesGrupo not yet implemented');
end;

function TADMCloudAPI.GetPerfil(out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetPerfil not yet implemented');
end;

function TADMCloudAPI.AtualizarPerfil(const ADados: string): Boolean;
begin
  Result := False;
  TratarErro('AtualizarPerfil not yet implemented');
end;

function TADMCloudAPI.GetFiliais(out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetFiliais not yet implemented');
end;

function TADMCloudAPI.GetFilialById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetFilialById not yet implemented');
end;

function TADMCloudAPI.GetProdutos(out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetProdutos not yet implemented');
end;

function TADMCloudAPI.GetProdutoById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetProdutoById not yet implemented');
end;

function TADMCloudAPI.GetDiarios(out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetDiarios not yet implemented');
end;

function TADMCloudAPI.GetDiarioById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetDiarioById not yet implemented');
end;

function TADMCloudAPI.GetModulos(out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetModulos not yet implemented');
end;

function TADMCloudAPI.GetModuloById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetModuloById not yet implemented');
end;

function TADMCloudAPI.GetVisitantes(out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetVisitantes not yet implemented');
end;

function TADMCloudAPI.GetVisitanteById(const AId: string; out AResponse: string): Boolean;
begin
  Result := False;
  AResponse := '';
  TratarErro('GetVisitanteById not yet implemented');
end;

function TADMCloudAPI.AtualizarPessoa(const ACNPJ, ADados: string): Boolean;
begin
  Result := False;
  TratarErro('AtualizarPessoa not yet implemented');
end;

end.
