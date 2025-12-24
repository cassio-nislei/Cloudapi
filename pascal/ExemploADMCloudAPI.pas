unit ExemploADMCloudAPI;

interface

uses
  SysUtils, Classes, ADMCloudAPI, ADMCloudAPIHelper;

procedure ExemploBasico;
procedure ExemploValidarPassport;
procedure ExemploRegistrarCliente;
procedure ExemploComErro;

implementation

// =====================================================================
// EXEMPLO 1: Uso Básico
// =====================================================================
procedure ExemploBasico;
var
  LAPI: TADMCloudAPI;
begin
  // Criar instância da API
  LAPI := TADMCloudAPI.Create('http://localhost/api/v1');
  try
    // Configurar credenciais (opcionais - vem com padrão)
    LAPI.ConfigurarCredenciais('api_frontbox', 'api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg');

    // Configurar timeout em millisegundos
    LAPI.ConfigurarTimeout(30000);

    // Testar conexão
    if LAPI.IsConectado then
      WriteLn('Conectado com sucesso!')
    else
      WriteLn('Erro ao conectar: ' + LAPI.GetUltimoErro);

  finally
    LAPI.Free;
  end;
end;

// =====================================================================
// EXEMPLO 2: Validar Passport de Cliente
// =====================================================================
procedure ExemploValidarPassport;
var
  LAPI: TADMCloudAPI;
  LCpf: string;
  LHostname: string;
  LGUID: string;
  LVersaoFBX: string;
  LVersaoPDV: string;
begin
  LAPI := TADMCloudAPI.Create('http://localhost/api/v1');
  try
    // Dados do cliente
    LCpf := '12345678901234';        // CGC/CNPJ
    LHostname := 'DESKTOP-VENDAS';   // Nome do computador
    LGUID := 'A1B2C3D4-E5F6-7890-ABCD-EF1234567890'; // GUID único
    LVersaoFBX := '4.5.2';           // Versão do FrontBox (opcional)
    LVersaoPDV := '1.2.3';           // Versão do PDV (opcional)

    // Chamar validação
    if LAPI.ValidarPassport(LCpf, LHostname, LGUID, LVersaoFBX, LVersaoPDV) then
    begin
      WriteLn('Passport válido!');
      WriteLn('Status HTTP: ' + IntToStr(LAPI.GetUltimoStatusCode));
    end
    else
    begin
      WriteLn('Passport inválido!');
      WriteLn('Erro: ' + LAPI.GetUltimoErro);
      WriteLn('Status HTTP: ' + IntToStr(LAPI.GetUltimoStatusCode));
    end;

  finally
    LAPI.Free;
  end;
end;

// =====================================================================
// EXEMPLO 3: Registrar Novo Cliente (com Helper)
// =====================================================================
procedure ExemploRegistrarCliente;
var
  LHelper: TADMCloudHelper;
  LRegistro: TRegistroData;
begin
  // Usar a classe helper é mais fácil
  LHelper := TADMCloudHelper.Create('http://localhost/api/v1');
  try
    // Registrar cliente com todos os dados
    if LHelper.RegistrarCliente(
      'EMPRESA LTDA',                    // Nome
      'Minha Empresa',                   // Fantasia
      '12.345.678/0001-90',              // CNPJ
      'João Silva',                      // Contato
      'joao@empresa.com.br',             // Email
      '(11) 3000-0000',                  // Telefone
      '(11) 99999-9999',                 // Celular (opcional)
      'Avenida Paulista',                // Endereço (opcional)
      '1000',                            // Número (opcional)
      'Sala 10',                         // Complemento (opcional)
      'Bela Vista',                      // Bairro (opcional)
      'São Paulo',                       // Cidade (opcional)
      'SP',                              // Estado (opcional)
      '01311-100'                        // CEP (opcional)
    ) then
    begin
      WriteLn('Cliente registrado com sucesso!');
      WriteLn('Status: ' + LHelper.GetRegistroStatus);
      WriteLn('Mensagem: ' + LHelper.GetRegistroMensagem);
    end
    else
    begin
      WriteLn('Erro ao registrar cliente!');
      WriteLn('Erro: ' + LHelper.GetUltimoErro);
      WriteLn('Status HTTP: ' + IntToStr(LHelper.GetUltimoStatusCode));
    end;

  finally
    LHelper.Free;
  end;
end;

// =====================================================================
// EXEMPLO 4: Tratamento de Erro Detalhado
// =====================================================================
procedure ExemploComErro;
var
  LAPI: TADMCloudAPI;
begin
  LAPI := TADMCloudAPI.Create('https://api.prod.example.com/api/v1');
  try
    // Tentar validar passport
    if LAPI.ValidarPassport('99999999999999', 'COMPUTER', 'GUID-123') then
    begin
      WriteLn('Sucesso!');
    end
    else
    begin
      // Tratamento de erro detalhado
      WriteLn('=== INFORMAÇÕES DE ERRO ===');
      WriteLn('Status HTTP: ' + IntToStr(LAPI.GetUltimoStatusCode));
      WriteLn('Mensagem: ' + LAPI.GetUltimoErro);

      case LAPI.GetUltimoStatusCode of
        0:
          WriteLn('Tipo: Erro de conexão');
        401:
          WriteLn('Tipo: Autenticação falhou');
        404:
          WriteLn('Tipo: Endpoint não encontrado');
        500:
          WriteLn('Tipo: Erro interno do servidor');
      else
        WriteLn('Tipo: Erro desconhecido (HTTP ' + IntToStr(LAPI.GetUltimoStatusCode) + ')');
      end;
    end;

  finally
    LAPI.Free;
  end;
end;

end.
