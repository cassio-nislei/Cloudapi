unit ADMCloudAPIHelper;

interface

uses
  SysUtils, Classes, JSON, ADMCloudAPI;

type
  // Classe helper com métodos de conveniência
  TADMCloudHelper = class(TObject)
  private
    FAPI: TADMCloudAPI;
    FLastPassportResponse: string;
    FLastRegistroResponse: string;

    function ParseJSONValue(const AJSON: string; const AKey: string): string;

  public
    constructor Create(const AURL: string = 'http://localhost/api/v1');
    destructor Destroy; override;

    // Validar Passport (GET /passport)
    // Retorna True se válido, False caso contrário
    function ValidarPassport(const ACGC, AHostname, AGUID: string;
      const AFBX: string = ''; const APDV: string = ''): Boolean;

    // Obter dados da resposta Passport
    function GetPassportStatus: Boolean;
    function GetPassportMensagem: string;
    function GetPassportResponseRaw: string;

    // Verificar status do módulo de Registro (GET /registro)
    // Requer autenticação
    function VerificarStatusRegistro: Boolean;

    // Registrar novo cliente (POST /registro)
    // Requer autenticação
    function RegistrarCliente(const ANome, AFantasia, ACGC, AContato, 
      AEmail, ATelefone: string; const ACelular: string = '';
      const AEndereco: string = ''; const ANumero: string = '';
      const AComplemento: string = ''; const ABairro: string = '';
      const ACidade: string = ''; const AEstado: string = '';
      const ACEP: string = ''): Boolean;

    // Obter dados da resposta Registro
    function GetRegistroStatus: string;
    function GetRegistroMensagem: string;
    function GetRegistroData: string;
    function GetRegistroResponseRaw: string;

    // Configurações
    procedure ConfigurarCredenciais(const AUsername, APassword: string);
    procedure ConfigurarTimeout(const AMS: Integer);

    // Informações de erro
    function GetUltimoErro: string;
    function GetUltimoStatusCode: Integer;
    function IsConectado: Boolean;

    // Propriedades
    property API: TADMCloudAPI read FAPI;
  end;

implementation

{ TADMCloudHelper }

constructor TADMCloudHelper.Create(const AURL: string = 'http://localhost/api/v1');
begin
  inherited Create;
  FAPI := TADMCloudAPI.Create(AURL);
  FLastPassportResponse := '';
  FLastRegistroResponse := '';
end;

destructor TADMCloudHelper.Destroy;
begin
  if Assigned(FAPI) then
    FAPI.Free;
  inherited;
end;

function TADMCloudHelper.ParseJSONValue(const AJSON, AKey: string): string;
var
  LJSON: TJSONObject;
  LValue: TJSONValue;
begin
  Result := '';
  if AJSON.Trim = '' then
    Exit;

  try
    LJSON := TJSONObject.ParseJSONValue(AJSON) as TJSONObject;
    try
      if Assigned(LJSON) then
      begin
        LValue := LJSON.Get(AKey);
        if Assigned(LValue) then
          Result := LValue.Value;
      end;
    finally
      LJSON.Free;
    end;
  except
    on E: Exception do
      Result := '';
  end;
end;

procedure TADMCloudHelper.ConfigurarCredenciais(const AUsername, 
  APassword: string);
begin
  if Assigned(FAPI) then
    FAPI.ConfigurarCredenciais(AUsername, APassword);
end;

procedure TADMCloudHelper.ConfigurarTimeout(const AMS: Integer);
begin
  if Assigned(FAPI) then
    FAPI.ConfigurarTimeout(AMS);
end;

function TADMCloudHelper.ValidarPassport(const ACGC, AHostname, AGUID: string;
  const AFBX: string = ''; const APDV: string = ''): Boolean;
var
  LEndpoint: string;
  LResponse: string;
begin
  Result := False;
  FLastPassportResponse := '';

  if not Assigned(FAPI) then
    Exit;

  // Montar endpoint com parâmetros
  LEndpoint := 'passport?cgc=' + AnsiReplaceText(ACGC, '.', '') + 
    AnsiReplaceText(ACGC, '/', '') + '&hostname=' + AHostname + '&guid=' + AGUID;

  if AFBX <> '' then
    LEndpoint := LEndpoint + '&fbx=' + AFBX;

  if APDV <> '' then
    LEndpoint := LEndpoint + '&pdv=' + APDV;

  // Fazer requisição
  if FAPI.ValidarPassport(ACGC, AHostname, AGUID, AFBX, APDV) then
  begin
    Result := True;
    // FLastPassportResponse seria preenchido com a resposta real
  end;
end;

function TADMCloudHelper.GetPassportStatus: Boolean;
begin
  Result := ParseJSONValue(FLastPassportResponse, 'Status') = 'true';
end;

function TADMCloudHelper.GetPassportMensagem: string;
begin
  Result := ParseJSONValue(FLastPassportResponse, 'Mensagem');
end;

function TADMCloudHelper.GetPassportResponseRaw: string;
begin
  Result := FLastPassportResponse;
end;

function TADMCloudHelper.VerificarStatusRegistro: Boolean;
begin
  Result := False;
  
  if not Assigned(FAPI) then
    Exit;

  Result := FAPI.GetStatusRegistro;
end;

function TADMCloudHelper.RegistrarCliente(const ANome, AFantasia, ACGC, 
  AContato, AEmail, ATelefone: string; const ACelular: string = '';
  const AEndereco: string = ''; const ANumero: string = '';
  const AComplemento: string = ''; const ABairro: string = '';
  const ACidade: string = ''; const AEstado: string = '';
  const ACEP: string = ''): Boolean;
var
  LRegistro: TRegistroData;
begin
  Result := False;
  FLastRegistroResponse := '';

  if not Assigned(FAPI) then
    Exit;

  // Preencher registro
  LRegistro.Nome := ANome;
  LRegistro.Fantasia := AFantasia;
  LRegistro.CGC := ACGC;
  LRegistro.Contato := AContato;
  LRegistro.Email := AEmail;
  LRegistro.Telefone := ATelefone;
  LRegistro.Celular := ACelular;
  LRegistro.Endereco := AEndereco;
  LRegistro.Numero := ANumero;
  LRegistro.Complemento := AComplemento;
  LRegistro.Bairro := ABairro;
  LRegistro.Cidade := ACidade;
  LRegistro.Estado := AEstado;
  LRegistro.CEP := ACEP;

  // Chamar API
  Result := FAPI.RegistrarCliente(LRegistro);
end;

function TADMCloudHelper.GetRegistroStatus: string;
begin
  Result := ParseJSONValue(FLastRegistroResponse, 'status');
end;

function TADMCloudHelper.GetRegistroMensagem: string;
begin
  Result := ParseJSONValue(FLastRegistroResponse, 'msg');
end;

function TADMCloudHelper.GetRegistroData: string;
begin
  Result := ParseJSONValue(FLastRegistroResponse, 'data');
end;

function TADMCloudHelper.GetRegistroResponseRaw: string;
begin
  Result := FLastRegistroResponse;
end;

function TADMCloudHelper.GetUltimoErro: string;
begin
  Result := '';
  if Assigned(FAPI) then
    Result := FAPI.GetUltimoErro;
end;

function TADMCloudHelper.GetUltimoStatusCode: Integer;
begin
  Result := 0;
  if Assigned(FAPI) then
    Result := FAPI.GetUltimoStatusCode;
end;

function TADMCloudHelper.IsConectado: Boolean;
begin
  Result := False;
  if Assigned(FAPI) then
    Result := FAPI.IsConectado;
end;

end.
