unit FormExemploIntegracao;

interface

uses
  Windows, Messages, SysUtils, Variants, Classes, Graphics, Controls, Forms,
  Dialogs, StdCtrls, ExtCtrls, ADMCloudAPI, ADMCloudAPIHelper, ADMCloudConsts;

type
  TFormExemplo = class(TForm)
    pnlTitulo: TPanel;
    pnlConteudo: TPanel;
    pnlBotoes: TPanel;
    
    lbTitulo: TLabel;
    lbResultado: TLabel;
    mmResultado: TMemo;
    
    edtCGC: TEdit;
    edtHostname: TEdit;
    edtGUID: TEdit;
    edtNome: TEdit;
    edtFantasia: TEdit;
    edtEmail: TEdit;
    edtTelefone: TEdit;
    
    btnValidarPassport: TButton;
    btnRegistrarCliente: TButton;
    btnLimpar: TButton;
    btnSair: TButton;
    
    procedure FormCreate(Sender: TObject);
    procedure FormDestroy(Sender: TObject);
    procedure btnValidarPassportClick(Sender: TObject);
    procedure btnRegistrarClienteClick(Sender: TObject);
    procedure btnLimparClick(Sender: TObject);
    procedure btnSairClick(Sender: TObject);

  private
    FAPI: TADMCloudHelper;
    procedure ExibirResultado(const ATitulo, AMensagem: string);
    procedure AdicionarLog(const ALog: string);

  public
    { Public declarations }
  end;

var
  FormExemplo: TFormExemplo;

implementation

{$R *.dfm}

procedure TFormExemplo.FormCreate(Sender: TObject);
begin
  { Criar instância da API }
  FAPI := TADMCloudHelper.Create(ADMCloud_URL_DEV);
  
  { Configurar valores padrão }
  edtCGC.Text := '12345678901234';
  edtHostname.Text := 'DESKTOP-PC';
  edtGUID.Text := 'A1B2C3D4-E5F6-7890-ABCD-EF1234567890';
  edtNome.Text := 'EMPRESA LTDA';
  edtFantasia.Text := 'Minha Empresa';
  edtEmail.Text := 'contato@empresa.com';
  edtTelefone.Text := '(11) 3000-0000';
  
  AdicionarLog('Aplicação iniciada.');
  AdicionarLog('API configurada: ' + ADMCloud_URL_DEV);
end;

procedure TFormExemplo.FormDestroy(Sender: TObject);
begin
  { Liberar instância da API }
  if Assigned(FAPI) then
    FAPI.Free;
end;

procedure TFormExemplo.ExibirResultado(const ATitulo, AMensagem: string);
begin
  lbResultado.Caption := ATitulo;
  mmResultado.Lines.Clear;
  mmResultado.Lines.Add(AMensagem);
end;

procedure TFormExemplo.AdicionarLog(const ALog: string);
begin
  mmResultado.Lines.Add('[' + FormatDateTime('HH:mm:ss', Now) + '] ' + ALog);
end;

procedure TFormExemplo.btnValidarPassportClick(Sender: TObject);
var
  LCGC: string;
  LHostname: string;
  LGUID: string;
begin
  { Obter valores dos campos }
  LCGC := Trim(edtCGC.Text);
  LHostname := Trim(edtHostname.Text);
  LGUID := Trim(edtGUID.Text);

  { Validar campos }
  if LCGC = '' then
  begin
    ExibirResultado('Erro', 'CPF/CNPJ obrigatório!');
    edtCGC.SetFocus;
    Exit;
  end;

  if LHostname = '' then
  begin
    ExibirResultado('Erro', 'Hostname obrigatório!');
    edtHostname.SetFocus;
    Exit;
  end;

  if LGUID = '' then
  begin
    ExibirResultado('Erro', 'GUID obrigatório!');
    edtGUID.SetFocus;
    Exit;
  end;

  { Exibir status }
  lbResultado.Caption := 'Validando Passport...';
  mmResultado.Clear;
  mmResultado.Lines.Add('Aguarde...');
  Application.ProcessMessages;

  try
    { Chamar API }
    if FAPI.ValidarPassport(LCGC, LHostname, LGUID) then
    begin
      AdicionarLog('✓ Passport válido!');
      AdicionarLog('Status HTTP: ' + IntToStr(FAPI.GetUltimoStatusCode));
      ExibirResultado('Sucesso', 'Passport validado com sucesso!');
    end
    else
    begin
      AdicionarLog('✗ Erro ao validar passport');
      AdicionarLog('Erro: ' + FAPI.GetUltimoErro);
      AdicionarLog('Status HTTP: ' + IntToStr(FAPI.GetUltimoStatusCode));
      ExibirResultado('Erro', 
        'Erro: ' + FAPI.GetUltimoErro + #13#10 +
        'Status HTTP: ' + IntToStr(FAPI.GetUltimoStatusCode));
    end;

  except
    on E: Exception do
    begin
      AdicionarLog('✗ Exceção: ' + E.Message);
      ExibirResultado('Erro', 'Exceção: ' + E.Message);
    end;
  end;
end;

procedure TFormExemplo.btnRegistrarClienteClick(Sender: TObject);
var
  LNome: string;
  LFantasia: string;
  LCGC: string;
  LEmail: string;
  LTelefone: string;
begin
  { Obter valores }
  LNome := Trim(edtNome.Text);
  LFantasia := Trim(edtFantasia.Text);
  LCGC := Trim(edtCGC.Text);
  LEmail := Trim(edtEmail.Text);
  LTelefone := Trim(edtTelefone.Text);

  { Validar campos }
  if LNome = '' then
  begin
    ExibirResultado('Erro', 'Nome da empresa obrigatório!');
    edtNome.SetFocus;
    Exit;
  end;

  if LCGC = '' then
  begin
    ExibirResultado('Erro', 'CNPJ obrigatório!');
    edtCGC.SetFocus;
    Exit;
  end;

  { Validar CNPJ antes de enviar }
  if not ValidarCNPJ(LCGC) then
  begin
    ExibirResultado('Erro', 'CNPJ inválido: ' + LCGC);
    edtCGC.SetFocus;
    Exit;
  end;

  if LEmail = '' then
  begin
    ExibirResultado('Erro', 'Email obrigatório!');
    edtEmail.SetFocus;
    Exit;
  end;

  if LTelefone = '' then
  begin
    ExibirResultado('Erro', 'Telefone obrigatório!');
    edtTelefone.SetFocus;
    Exit;
  end;

  { Exibir status }
  lbResultado.Caption := 'Registrando cliente...';
  mmResultado.Clear;
  mmResultado.Lines.Add('Aguarde...');
  Application.ProcessMessages;

  try
    { Chamar API }
    if FAPI.RegistrarCliente(
      LNome,
      LFantasia,
      LCGC,
      'Contato', { Você poderia adicionar um campo para isso }
      LEmail,
      LTelefone
    ) then
    begin
      AdicionarLog('✓ Cliente registrado com sucesso!');
      AdicionarLog('Status: ' + FAPI.GetRegistroStatus);
      AdicionarLog('Mensagem: ' + FAPI.GetRegistroMensagem);
      ExibirResultado('Sucesso', 
        'Cliente registrado!' + #13#10 +
        'Status: ' + FAPI.GetRegistroStatus + #13#10 +
        'Mensagem: ' + FAPI.GetRegistroMensagem);
    end
    else
    begin
      AdicionarLog('✗ Erro ao registrar cliente');
      AdicionarLog('Erro: ' + FAPI.GetUltimoErro);
      AdicionarLog('Status HTTP: ' + IntToStr(FAPI.GetUltimoStatusCode));
      ExibirResultado('Erro', 
        'Erro ao registrar!' + #13#10 +
        'Erro: ' + FAPI.GetUltimoErro + #13#10 +
        'Status HTTP: ' + IntToStr(FAPI.GetUltimoStatusCode));
    end;

  except
    on E: Exception do
    begin
      AdicionarLog('✗ Exceção: ' + E.Message);
      ExibirResultado('Erro', 'Exceção: ' + E.Message);
    end;
  end;
end;

procedure TFormExemplo.btnLimparClick(Sender: TObject);
begin
  mmResultado.Clear;
  AdicionarLog('Log limpo.');
end;

procedure TFormExemplo.btnSairClick(Sender: TObject);
begin
  Close;
end;

end.
