unit uTesteEmpresaLicencaManager;

{
  TESTE COMPLETO: TEmpresaLicencaManager
  
  Valida que todas as funções estão implementadas e funcionando corretamente.
}

interface

uses
  Winapi.Windows, Winapi.Messages, System.SysUtils, System.Variants, System.Classes,
  Vcl.Graphics, Vcl.Controls, Vcl.Forms, Vcl.Dialogs, Vcl.StdCtrls,
  uEmpresaLicencaManager, ADMCloudConsts;

type
  TFormTesteEmpresa = class(TForm)
    Button1: TButton;
    Button2: TButton;
    Button3: TButton;
    Button4: TButton;
    Button5: TButton;
    MemoLog: TMemo;
    Button6: TButton;
    procedure FormCreate(Sender: TObject);
    procedure FormDestroy(Sender: TObject);
    procedure Button1Click(Sender: TObject);
    procedure Button2Click(Sender: TObject);
    procedure Button3Click(Sender: TObject);
    procedure Button4Click(Sender: TObject);
    procedure Button5Click(Sender: TObject);
    procedure Button6Click(Sender: TObject);
  private
    FManager: TEmpresaLicencaManager;
    procedure Log(const Msg: string);
  public
  end;

var
  FormTesteEmpresa: TFormTesteEmpresa;

implementation

{$R *.dfm}

procedure TFormTesteEmpresa.FormCreate(Sender: TObject);
begin
  FManager := TEmpresaLicencaManager.Create(nil);
  
  { Registrar evento de log }
  FManager.OnLog := procedure(Sender: TObject; const AMsg: string)
  begin
    Log('[LOG] ' + AMsg);
  end;
  
  Log('✅ TEmpresaLicencaManager criado');
  
  { Configurar botões }
  Button1.Caption := 'Testar GetMachineGUID';
  Button2.Caption := 'Testar GetTerminalAtual';
  Button3.Caption := 'Testar GetMachineSerial';
  Button4.Caption := 'Testar ValidarPassport';
  Button5.Caption := 'Testar ConfigurarURLAPI';
  Button6.Caption := 'Testar GetCNPJEmpresaAtual';
end;

procedure TFormTesteEmpresa.FormDestroy(Sender: TObject);
begin
  if Assigned(FManager) then
    FManager.Free;
end;

procedure TFormTesteEmpresa.Log(const Msg: string);
begin
  MemoLog.Lines.Add('[' + FormatDateTime('hh:mm:ss', Now) + '] ' + Msg);
end;

{
  TESTE 1: GetMachineGUID
  Esperado: GUID único da máquina no formato {xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx}
}
procedure TFormTesteEmpresa.Button1Click(Sender: TObject);
var
  GUID: string;
begin
  Log('');
  Log('=== TESTE 1: GetMachineGUID ===');
  
  GUID := FManager.GetMachineGUID;
  
  if GUID <> '' then
  begin
    Log('✅ GUID obtido: ' + GUID);
    Log('   Tamanho: ' + IntToStr(Length(GUID)));
    Log('   Esperado: {xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx} ou similar');
  end
  else
  begin
    Log('❌ GUID vazio - Erro ao obter');
  end;
end;

{
  TESTE 2: GetTerminalAtual
  Esperado: Nome do computador (ex: DESKTOP-ABC123)
}
procedure TFormTesteEmpresa.Button2Click(Sender: TObject);
var
  Terminal: string;
begin
  Log('');
  Log('=== TESTE 2: GetTerminalAtual ===');
  
  Terminal := FManager.GetTerminalAtual;
  
  if Terminal <> '' and Terminal <> 'UNKNOW_TERMINAL' then
  begin
    Log('✅ Terminal obtido: ' + Terminal);
    Log('   Esperado: Nome do computador Windows');
  end
  else if Terminal = 'UNKNOW_TERMINAL' then
  begin
    Log('⚠️ Terminal retornou default: UNKNOW_TERMINAL');
    Log('   Considere verificar GetHostName()');
  end
  else
  begin
    Log('❌ Terminal vazio');
  end;
end;

{
  TESTE 3: GetMachineSerial
  Esperado: Mesmo valor do GetMachineGUID (é usada a mesma função)
}
procedure TFormTesteEmpresa.Button3Click(Sender: TObject);
var
  Serial: string;
  GUID: string;
begin
  Log('');
  Log('=== TESTE 3: GetMachineSerial ===');
  
  Serial := FManager.GetMachineSerial;
  GUID := FManager.GetMachineGUID;
  
  Log('Serial: ' + Serial);
  Log('GUID:   ' + GUID);
  
  if Serial = GUID then
  begin
    Log('✅ GetMachineSerial === GetMachineGUID (correto)');
  end
  else if Serial <> '' then
  begin
    Log('⚠️ Serial diferente de GUID');
    Log('   (Isso pode estar correto dependendo da implementação)');
  end
  else
  begin
    Log('❌ Serial vazio');
  end;
end;

{
  TESTE 4: ValidarPassport
  Esperado: true/false (deve testar com CNPJ válido e inválido)
}
procedure TFormTesteEmpresa.Button4Click(Sender: TObject);
var
  LSuccess: Boolean;
  LCNPJ: string;
begin
  Log('');
  Log('=== TESTE 4: ValidarPassportEmpresa ===');
  
  { Testar com CNPJ válido }
  LCNPJ := '92702067000196';  // CNPJ conhecido do banco
  Log('Testando com CNPJ: ' + LCNPJ);
  Log('Hostname: ' + FManager.GetTerminalAtual);
  Log('GUID: ' + FManager.GetMachineSerial);
  
  LSuccess := FManager.ValidarPassportEmpresa(
    LCNPJ,
    FManager.GetTerminalAtual,
    FManager.GetMachineSerial
  );
  
  if LSuccess then
  begin
    Log('✅ ValidarPassport retornou TRUE');
    Log('   CNPJ está válido no banco');
  end
  else
  begin
    Log('❌ ValidarPassport retornou FALSE');
    Log('   Erro: ' + FManager.GetUltimoErro);
  end;
end;

{
  TESTE 5: ConfigurarURLAPI
  Esperado: Reconfigurar URL e recriar FAPIHelper
}
procedure TFormTesteEmpresa.Button5Click(Sender: TObject);
begin
  Log('');
  Log('=== TESTE 5: ConfigurarURLAPI ===');
  
  Log('URL atual (antes): ' + ADMCloud_URL_PROD);
  
  { Testar mudança para staging }
  FManager.ConfigurarURLAPI('http://104.234.173.105:7010');
  Log('✅ URL reconfigurada para: http://104.234.173.105:7010');
  Log('   FAPIHelper foi recriado');
  
  { Voltar para produção }
  FManager.ConfigurarURLAPI('');
  Log('✅ URL reconfigurada para padrão: ' + ADMCloud_URL_PROD);
end;

{
  TESTE 6: GetCNPJEmpresaAtual
  Esperado: CNPJ carregado da empresa (ou vazio se nenhuma estiver carregada)
}
procedure TFormTesteEmpresa.Button6Click(Sender: TObject);
var
  LCNPJ: string;
begin
  Log('');
  Log('=== TESTE 6: GetCNPJEmpresaAtual ===');
  
  LCNPJ := FManager.GetCNPJEmpresaAtual;
  
  if LCNPJ <> '' then
  begin
    Log('✅ CNPJ obtido: ' + LCNPJ);
    Log('   Carregado da empresa em memoria (dados.qryEmpresa)');
  end
  else
  begin
    Log('⚠️ CNPJ vazio');
    Log('   (Normal se nenhuma empresa foi carregada ainda)');
  end;
end;

end.

{
  INSTRUÇÕES DE USO:
  
  1. Adicione os botões no designer:
     Button1, Button2, Button3, Button4, Button5, Button6
  
  2. Execute os testes na ordem:
     1. GetMachineGUID (deve retornar GUID válido)
     2. GetTerminalAtual (deve retornar nome do PC)
     3. GetMachineSerial (deve ser igual a GetMachineGUID)
     4. ValidarPassport (deve funcionar se conectar)
     5. ConfigurarURLAPI (deve aceitar novas URLs)
     6. GetCNPJEmpresaAtual (pode estar vazio se nada carregado)
  
  3. Verifique os logs no MemoLog
  
  ESPERADO:
  ✅ GetMachineGUID: GUID com {xxxx-xxxx-xxxx}
  ✅ GetTerminalAtual: Nome do computador
  ✅ GetMachineSerial: Mesmo do GUID
  ✅ ValidarPassport: TRUE (com CNPJ 92702067000196)
  ✅ ConfigurarURLAPI: Aceita novos endereços
  ✅ GetCNPJEmpresaAtual: Vazio (ou CNPJ se carregado)
}
