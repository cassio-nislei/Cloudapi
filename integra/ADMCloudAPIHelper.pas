unit ADMCloudAPIHelper;

interface

uses
  SysUtils, Classes, JSON, ADMCloudAPI, ADMCloudConsts;

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
  LPair: TJSONPair;
begin
  Result := '';
  if AJSON.Trim = '' then
    Exit;

  try
    LJSON := TJSONObject.ParseJSONValue(AJSON) as TJSONObject;
    try
      if Assigned(LJSON) then
      begin
        LPair := LJSON.Get(AKey);
        if Assigned(LPair) then
          Result := LPair.JsonValue.Value;
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
  LCGCLimpo: string;
begin
  Result := False;
  FLastPassportResponse := '';

  if not Assigned(FAPI) then
    Exit;

  // Remover formatação do CNPJ/CPF
  LCGCLimpo := RemoverFormatacao(ACGC);
  
  // Validar parâmetros obrigatórios
  if (LCGCLimpo = '') or (AHostname = '') or (AGUID = '') then
  begin
    FLastPassportResponse := '';
    Exit;
  end;

  // Fazer requisição
  if FAPI.ValidarPassport(LCGCLimpo, AHostname, AGUID, AFBX, APDV) then
  begin
    Result := True;
    FLastPassportResponse := FAPI.GetLastPassportResponseRaw;
  end;
end;
end;

function TADMCloudHelper.GetPassportStatus: Boolean;
var
  LJSON: TJSONObject;
  LPair: TJSONPair;
  LValue: TJSONValue;
begin
  Result := False;
  
  if FLastPassportResponse.Trim = '' then
    Exit;

  try
    LJSON := TJSONObject.ParseJSONValue(FLastPassportResponse) as TJSONObject;
    if Assigned(LJSON) then
    try
      LPair := LJSON.Get('Status');
      if Assigned(LPair) then
      begin
        LValue := LPair.JsonValue;
        // Parse como boolean (não como string)
        if LValue is TJSONTrue then
          Result := True
        else if LValue is TJSONFalse then
          Result := False
        else
          // Tenta como string em caso de resposta não padrão
          Result := LowerCase(LValue.Value) = 'true';
      end;
    finally
      LJSON.Free;
    end;
  except
    Result := False;
  end;
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
  LCGCLimpo: string;
begin
  Result := False;
  FLastRegistroResponse := '';

  if not Assigned(FAPI) then
    Exit;

  // Validar campos obrigatórios
  if (ANome = '') or (AFantasia = '') or (AContato = '') or
     (AEmail = '') or (ATelefone = '') or (AEndereco = '') or
     (ANumero = '') or (ABairro = '') or (ACidade = '') or
     (AEstado = '') or (ACEP = '') then
  begin
    Exit;  // Retorna False se algum campo obrigatório falta
  end;

  // Preencher registro
  LCGCLimpo := RemoverFormatacao(ACGC);
  
  LRegistro.Nome := ANome;
  LRegistro.Fantasia := AFantasia;
  LRegistro.CGC := LCGCLimpo;
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
  
  if Result then
    FLastRegistroResponse := FAPI.GetLastRegistroResponseRaw;
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
