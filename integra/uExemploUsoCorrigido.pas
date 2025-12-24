unit uExemploUsoCorrigido;

{
  EXEMPLO DE USO CORRIGIDO - ADMCloudAPI
  
  Este exemplo mostra como usar a classe ADMCloudAPI corretamente,
  evitando o erro 404.
}

interface

uses
  Winapi.Windows, Winapi.Messages, System.SysUtils, System.Variants, System.Classes, 
  Vcl.Graphics, Vcl.Controls, Vcl.Forms, Vcl.Dialogs, Vcl.StdCtrls,
  ADMCloudAPI;

type
  TForm1 = class(TForm)
    btnValidarPassport: TButton;
    btnBuscarPessoas: TButton;
    btnRegistrarPessoa: TButton;
    memoResultado: TMemo;
    procedure FormCreate(Sender: TObject);
    procedure FormDestroy(Sender: TObject);
    procedure btnValidarPassportClick(Sender: TObject);
    procedure btnBuscarPessoasClick(Sender: TObject);
    procedure btnRegistrarPessoaClick(Sender: TObject);
  private
    FAPI: TADMCloudAPI;
    procedure AdicionarLog(const AMsg: string);
  public
  end;

var
  Form1: TForm1;

implementation

{$R *.dfm}

procedure TForm1.FormCreate(Sender: TObject);
begin
  // ✅ CORRETO: URL base aponta ao root do CodeIgniter, sem /api/v1
  FAPI := TADMCloudAPI.Create('http://104.234.173.105:7010');
  
  // Para produção:
  // FAPI := TADMCloudAPI.Create('https://admcloud.papion.com.br');
  
  // Configurar credenciais se necessário
  FAPI.ConfigurarCredenciais('usuario', 'senha');
  FAPI.ConfigurarTimeout(10000); // 10 segundos
  
  AdicionarLog('API inicializada em: ' + FAPI.URL);
end;

procedure TForm1.FormDestroy(Sender: TObject);
begin
  if Assigned(FAPI) then
    FAPI.Free;
end;

procedure TForm1.AdicionarLog(const AMsg: string);
begin
  memoResultado.Lines.Add('[' + FormatDateTime('hh:mm:ss', Now) + '] ' + AMsg);
end;

// =============================================
// EXEMPLO 1: Validar Passport (GET)
// =============================================
procedure TForm1.btnValidarPassportClick(Sender: TObject);
var
  LResponse: TPassportResponse;
  LCGC: string;
  LHostname: string;
  LGUID: string;
begin
  // ✅ Parâmetros obrigatórios
  LCGC := '92702067000196';        // CNPJ sem formatação
  LHostname := 'MEUPC';             // Nome do computador
  LGUID := 'teste-guid-12345';      // Identificador único
  
  AdicionarLog('Consultando Passport...');
  AdicionarLog('  CGC: ' + LCGC);
  AdicionarLog('  Hostname: ' + LHostname);
  AdicionarLog('  GUID: ' + LGUID);
  
  // Chamar API
  if FAPI.ValidarPassport(LCGC, LHostname, LGUID) then
  begin
    AdicionarLog('✓ Sucesso! Status: ' + FAPI.GetLastPassportResponseRaw);
    LResponse := FAPI.GetPassportResponse;
    AdicionarLog('  Validado: ' + BoolToStr(LResponse.Status));
    AdicionarLog('  Mensagem: ' + LResponse.Mensagem);
  end
  else
  begin
    AdicionarLog('✗ ERRO!');
    AdicionarLog('  Status Code: ' + IntToStr(FAPI.GetUltimoStatusCode));
    AdicionarLog('  Mensagem: ' + FAPI.GetUltimoErro);
  end;
end;

// =============================================
// EXEMPLO 2: Buscar Pessoas (GET)
// Nota: Requer autenticação Basic Auth
// =============================================
procedure TForm1.btnBuscarPessoasClick(Sender: TObject);
var
  LResponse: string;
begin
  AdicionarLog('Buscando Pessoas...');
  
  // Se a classe tiver método para GET de Pessoas, usar assim:
  // Caso contrário, adicionar método à classe:
  {
    if FAPI.BuscarPessoas(LResponse) then
    begin
      AdicionarLog('✓ Sucesso!');
      AdicionarLog(LResponse);
    end
    else
    begin
      AdicionarLog('✗ ERRO: ' + FAPI.GetUltimoErro);
    end;
  }
  
  AdicionarLog('(Implementar em ADMCloudAPI.pas - Linha ~330)');
end;

// =============================================
// EXEMPLO 3: Registrar Pessoa (POST)
// =============================================
procedure TForm1.btnRegistrarPessoaClick(Sender: TObject);
var
  LRegistro: TRegistroData;
begin
  AdicionarLog('Registrando Pessoa...');
  
  // Preencher dados
  with LRegistro do
  begin
    Nome := 'João Silva';
    Fantasia := 'JS Negócios';
    CGC := '92702067000196';
    Contato := 'joao@example.com';
    Email := 'joao@example.com';
    Telefone := '1133334444';
    Celular := '11999998888';
    Endereco := 'Rua das Flores';
    Numero := '100';
    Complemento := 'Apt 201';
    Bairro := 'Centro';
    Cidade := 'São Paulo';
    Estado := 'SP';
    CEP := '01310100';
  end;
  
  // Enviar
  if FAPI.RegistrarCliente(LRegistro) then
  begin
    AdicionarLog('✓ Sucesso!');
    AdicionarLog(FAPI.GetLastRegistroResponseRaw);
  end
  else
  begin
    AdicionarLog('✗ ERRO!');
    AdicionarLog('  Status Code: ' + IntToStr(FAPI.GetUltimoStatusCode));
    AdicionarLog('  Mensagem: ' + FAPI.GetUltimoErro);
  end;
end;

end.

{
  ============================================
  PONTOS-CHAVE PARA FUNCIONAR:
  ============================================
  
  1. URL BASE CORRETA:
     ✅ http://104.234.173.105:7010
     ❌ http://104.234.173.105:7010/api/v1
     ❌ http://104.234.173.105/admcloud
  
  2. ENDPOINTS ESPERADOS:
     Passport/consulta?cgc=...
     Pessoas/getAll
     Pessoas/salvar
     Atendimentos/getAll?id_pessoa=...
     Atendimentos/salvar
  
  3. AUTENTICAÇÃO:
     Passport: NÃO requer autenticação
     Pessoas: Requer Basic Auth
     Atendimentos: Requer Basic Auth
  
  4. SE AINDA RETORNAR 404:
     a) Verificar se URL está acessível no navegador:
        http://104.234.173.105:7010/Passport/consulta?cgc=92702067000196
     
     b) Verificar CORS:
        OPTIONS request deve retornar 200 OK
     
     c) Verificar servidor está rodando:
        php -S 104.234.173.105:7010
     
     d) Verificar firewall/rede
}
