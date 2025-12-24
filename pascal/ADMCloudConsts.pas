unit ADMCloudConsts;

interface

const
  // URLs padrão
  ADMCloud_URL_DEV = 'http://localhost/api/v1';
  ADMCloud_URL_PROD = 'http://104.234.173.105:7010/api/v1';

  // Credenciais padrão
  ADMCloud_USER = 'api_frontbox';
  ADMCloud_PASS = 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg';

  // Endpoints
  ADMCloud_ENDPOINT_PASSPORT = 'passport';
  ADMCloud_ENDPOINT_REGISTRO_GET = 'registro';
  ADMCloud_ENDPOINT_REGISTRO_POST = 'registro';

  // Timeouts (em millisegundos)
  ADMCloud_TIMEOUT_PADRAO = 30000;
  ADMCloud_TIMEOUT_CURTO = 10000;
  ADMCloud_TIMEOUT_LONGO = 60000;

  // Códigos HTTP
  HTTP_OK = 200;
  HTTP_CREATED = 201;
  HTTP_BAD_REQUEST = 400;
  HTTP_UNAUTHORIZED = 401;
  HTTP_FORBIDDEN = 403;
  HTTP_NOT_FOUND = 404;
  HTTP_INTERNAL_ERROR = 500;
  HTTP_SERVICE_UNAVAILABLE = 503;

  // Mensagens de erro padrão
  ERRO_CONEXAO = 'Erro ao conectar com a API';
  ERRO_AUTENTICACAO = 'Erro de autenticação - Verifique usuário e senha';
  ERRO_DADOS_INVALIDOS = 'Dados inválidos ou incompletos';
  ERRO_SERVIDOR = 'Erro interno do servidor';
  ERRO_NAO_ENCONTRADO = 'Recurso não encontrado';

type
  // Estados possíveis de registro
  TStatusRegistro = (srOK, srERROR, srPENDING, srDESCONHECIDO);

  // Estados de conexão
  TEstadoConexao = (ecOK, ecERRO_CONEXAO, ecERRO_AUTENTICACAO, ecERRO_SERVIDOR);

  // Função helper para converter string para TStatusRegistro
  function StringParaTStatusRegistro(const AStatus: string): TStatusRegistro;

  // Função helper para converter TStatusRegistro para string
  function TStatusRegistroParaString(const AStatus: TStatusRegistro): string;

  // Função helper para validar CPF/CNPJ
  function ValidarCPF(const ACPF: string): Boolean;
  function ValidarCNPJ(const ACNPJ: string): Boolean;

  // Função helper para formatar CPF
  function FormatarCPF(const ACPF: string): string;

  // Função helper para formatar CNPJ
  function FormatarCNPJ(const ACNPJ: string): string;

  // Função helper para remover formatação
  function RemoverFormatacao(const ATexto: string): string;

implementation

uses
  SysUtils, StrUtils;

function StringParaTStatusRegistro(const AStatus: string): TStatusRegistro;
begin
  case IndexStr(AnsiUpperCase(AStatus), ['OK', 'ERROR', 'PENDING']) of
    0:
      Result := srOK;
    1:
      Result := srERROR;
    2:
      Result := srPENDING;
  else
    Result := srDESCONHECIDO;
  end;
end;

function TStatusRegistroParaString(const AStatus: TStatusRegistro): string;
begin
  case AStatus of
    srOK:
      Result := 'OK';
    srERROR:
      Result := 'ERROR';
    srPENDING:
      Result := 'PENDING';
  else
    Result := 'DESCONHECIDO';
  end;
end;

function ValidarCPF(const ACPF: string): Boolean;
var
  LCPFLimpo: string;
  I, LDV1, LDV2, LSoma: Integer;
begin
  Result := False;

  // Remover formatação
  LCPFLimpo := '';
  for I := 1 to Length(ACPF) do
    if CharInSet(ACPF[I], ['0'..'9']) then
      LCPFLimpo := LCPFLimpo + ACPF[I];

  // Verificar se tem 11 dígitos
  if Length(LCPFLimpo) <> 11 then
    Exit;

  // Verificar se não é todos iguais
  if (LCPFLimpo = '00000000000') or (LCPFLimpo = '11111111111') or
     (LCPFLimpo = '22222222222') or (LCPFLimpo = '33333333333') or
     (LCPFLimpo = '44444444444') or (LCPFLimpo = '55555555555') or
     (LCPFLimpo = '66666666666') or (LCPFLimpo = '77777777777') or
     (LCPFLimpo = '88888888888') or (LCPFLimpo = '99999999999') then
    Exit;

  // Calcular primeiro dígito verificador
  LSoma := 0;
  for I := 1 to 9 do
    LSoma := LSoma + (StrToInt(LCPFLimpo[I]) * (11 - I));
  LDV1 := 11 - (LSoma mod 11);
  if LDV1 >= 10 then
    LDV1 := 0;

  // Calcular segundo dígito verificador
  LSoma := 0;
  for I := 1 to 10 do
    LSoma := LSoma + (StrToInt(LCPFLimpo[I]) * (12 - I));
  LDV2 := 11 - (LSoma mod 11);
  if LDV2 >= 10 then
    LDV2 := 0;

  // Comparar
  Result := (StrToInt(LCPFLimpo[10]) = LDV1) and
            (StrToInt(LCPFLimpo[11]) = LDV2);
end;

function ValidarCNPJ(const ACNPJ: string): Boolean;
var
  LCNPJ: string;
  I, LDV1, LDV2, LSoma: Integer;
  LPeso1, LPeso2: array[0..7] of Integer;
begin
  Result := False;

  // Remover formatação
  LCNPJ := '';
  for I := 1 to Length(ACNPJ) do
    if CharInSet(ACNPJ[I], ['0'..'9']) then
      LCNPJ := LCNPJ + ACNPJ[I];

  // Verificar se tem 14 dígitos
  if Length(LCNPJ) <> 14 then
    Exit;

  // Verificar se não é todos iguais
  if (LCNPJ = '00000000000000') or (LCNPJ = '11111111111111') or
     (LCNPJ = '22222222222222') or (LCNPJ = '33333333333333') or
     (LCNPJ = '44444444444444') or (LCNPJ = '55555555555555') or
     (LCNPJ = '66666666666666') or (LCNPJ = '77777777777777') or
     (LCNPJ = '88888888888888') or (LCNPJ = '99999999999999') then
    Exit;

  // Pesos
  LPeso1[0] := 5; LPeso1[1] := 4; LPeso1[2] := 3; LPeso1[3] := 2;
  LPeso1[4] := 9; LPeso1[5] := 8; LPeso1[6] := 7; LPeso1[7] := 6;

  LPeso2[0] := 6; LPeso2[1] := 5; LPeso2[2] := 4; LPeso2[3] := 3;
  LPeso2[4] := 2; LPeso2[5] := 9; LPeso2[6] := 8; LPeso2[7] := 7;

  // Calcular primeiro dígito
  LSoma := 0;
  for I := 0 to 7 do
    LSoma := LSoma + (StrToInt(LCNPJ[I + 1]) * LPeso1[I]);
  LDV1 := 11 - (LSoma mod 11);
  if LDV1 >= 10 then
    LDV1 := 0;

  // Calcular segundo dígito
  LSoma := 0;
  for I := 0 to 7 do
    LSoma := LSoma + (StrToInt(LCNPJ[I + 1]) * LPeso2[I]);
  LSoma := LSoma + (LDV1 * 2);
  LDV2 := 11 - (LSoma mod 11);
  if LDV2 >= 10 then
    LDV2 := 0;

  // Comparar
  Result := (StrToInt(LCNPJ[13]) = LDV1) and
            (StrToInt(LCNPJ[14]) = LDV2);
end;

function FormatarCPF(const ACPF: string): string;
var
  LCPFLimpo: string;
  I: Integer;
begin
  // Remover formatação
  LCPFLimpo := '';
  for I := 1 to Length(ACPF) do
    if CharInSet(ACPF[I], ['0'..'9']) then
      LCPFLimpo := LCPFLimpo + ACPF[I];

  if Length(LCPFLimpo) = 11 then
    Result := Copy(LCPFLimpo, 1, 3) + '.' + Copy(LCPFLimpo, 4, 3) + '.' +
              Copy(LCPFLimpo, 7, 3) + '-' + Copy(LCPFLimpo, 10, 2)
  else
    Result := ACPF;
end;

function FormatarCNPJ(const ACNPJ: string): string;
var
  LCNPJLimpo: string;
  I: Integer;
begin
  // Remover formatação
  LCNPJLimpo := '';
  for I := 1 to Length(ACNPJ) do
    if CharInSet(ACNPJ[I], ['0'..'9']) then
      LCNPJLimpo := LCNPJLimpo + ACNPJ[I];

  if Length(LCNPJLimpo) = 14 then
    Result := Copy(LCNPJLimpo, 1, 2) + '.' + Copy(LCNPJLimpo, 3, 3) + '.' +
              Copy(LCNPJLimpo, 6, 3) + '/' + Copy(LCNPJLimpo, 9, 4) + '-' +
              Copy(LCNPJLimpo, 13, 2)
  else
    Result := ACNPJ;
end;

function RemoverFormatacao(const ATexto: string): string;
var
  I: Integer;
begin
  Result := '';
  for I := 1 to Length(ATexto) do
    if CharInSet(ATexto[I], ['0'..'9']) then
      Result := Result + ATexto[I];
end;

end.
