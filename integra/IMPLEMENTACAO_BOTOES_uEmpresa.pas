// Adicionar ao uEmpresa.pas - IMPLEMENTAÇÃO DE BOTÕES DA API

// ===== SEÇÃO: ADICIONAR AOS USES =====
// Adicionar ao final da linha de uses:
// uEmpresaLicencaManager,

// ===== SEÇÃO: ADICIONAR ÀS VARIÁVEIS PRIVADAS =====
// Adicionar na seção private de TfrmEmpresa:
//    FLicencaManager: TEmpresaLicencaManager;

// ===== SEÇÃO: CRIAR BOTÕES NO FORM =====
// Adicionar após o constructor Create:

procedure TfrmEmpresa.CriarBotoesAPI;
begin
  // Painel para os botões de API (opcional)
  if not Assigned(PanelBotoesAPI) then
  begin
    PanelBotoesAPI := TPanel.Create(Self);
    PanelBotoesAPI.Parent := Self;
    PanelBotoesAPI.Align := alBottom;
    PanelBotoesAPI.Height := 50;
    PanelBotoesAPI.BevelOuter := bvNone;
    PanelBotoesAPI.Color := clBtnFace;
  end;

  // Botão 1: Validar Passport/Checkin
  btnValidarPassport := TButton.Create(Self);
  btnValidarPassport.Parent := PanelBotoesAPI;
  btnValidarPassport.Left := 10;
  btnValidarPassport.Top := 10;
  btnValidarPassport.Width := 150;
  btnValidarPassport.Height := 30;
  btnValidarPassport.Caption := 'Validar Passport';
  btnValidarPassport.OnClick := btnValidarPassportClick;

  // Botão 2: Sincronizar Licença
  btnSincronizar := TButton.Create(Self);
  btnSincronizar.Parent := PanelBotoesAPI;
  btnSincronizar.Left := 170;
  btnSincronizar.Top := 10;
  btnSincronizar.Width := 150;
  btnSincronizar.Height := 30;
  btnSincronizar.Caption := 'Sincronizar Licença';
  btnSincronizar.OnClick := btnSincronizarClick;

  // Botão 3: Validar Licença Atual
  btnValidarLicenca := TButton.Create(Self);
  btnValidarLicenca.Parent := PanelBotoesAPI;
  btnValidarLicenca.Left := 330;
  btnValidarLicenca.Top := 10;
  btnValidarLicenca.Width := 150;
  btnValidarLicenca.Height := 30;
  btnValidarLicenca.Caption := 'Validar Licença';
  btnValidarLicenca.OnClick := btnValidarLicencaClick;

  // Botão 4: Registrar Empresa
  btnRegistrarEmpresa := TButton.Create(Self);
  btnRegistrarEmpresa.Parent := PanelBotoesAPI;
  btnRegistrarEmpresa.Left := 490;
  btnRegistrarEmpresa.Top := 10;
  btnRegistrarEmpresa.Width := 150;
  btnRegistrarEmpresa.Height := 30;
  btnRegistrarEmpresa.Caption := 'Registrar Empresa';
  btnRegistrarEmpresa.OnClick := btnRegistrarEmpresaClick;
end;

// ===== SEÇÃO: EVENTOS DOS BOTÕES =====

procedure TfrmEmpresa.btnValidarPassportClick(Sender: TObject);
var
  LCNPJ, LHostname, LGUID: string;
begin
  try
    if not Assigned(FLicencaManager) then
    begin
      ShowMessage('Gerenciador de licenças não inicializado.');
      Exit;
    end;

    LCNPJ := qryEmpresaCNPJ.AsString;
    if LCNPJ = '' then
    begin
      ShowMessage('É necessário preencher o CNPJ da empresa.');
      Exit;
    end;

    LHostname := FLicencaManager.GetHostName;
    LGUID := FLicencaManager.GetMachineGUID;

    if FLicencaManager.ValidarPassportEmpresa(LCNPJ, LHostname, LGUID) then
    begin
      ShowMessage('✅ Passport validado com sucesso!' + sLineBreak +
                  'CNPJ: ' + LCNPJ + sLineBreak +
                  'Hostname: ' + LHostname + sLineBreak +
                  'GUID: ' + LGUID);
      FLicencaManager.Log('Validação de Passport bem-sucedida para CNPJ: ' + LCNPJ);
    end
    else
    begin
      ShowMessage('❌ Falha na validação de Passport:' + sLineBreak +
                  FLicencaManager.GetUltimoErro);
      FLicencaManager.Log('Falha na validação de Passport: ' + FLicencaManager.GetUltimoErro);
    end;
  except
    on E: Exception do
    begin
      ShowMessage('Erro ao validar Passport: ' + E.Message);
      FLicencaManager.Log('Erro: ' + E.Message);
    end;
  end;
end;

procedure TfrmEmpresa.btnSincronizarClick(Sender: TObject);
begin
  try
    if not Assigned(FLicencaManager) then
    begin
      ShowMessage('Gerenciador de licenças não inicializado.');
      Exit;
    end;

    btnSincronizar.Enabled := False;
    btnSincronizar.Caption := 'Sincronizando...';
    Application.ProcessMessages;

    if FLicencaManager.SincronizarComGerenciadorLicenca then
    begin
      ShowMessage('✅ Sincronização bem-sucedida!' + sLineBreak +
                  'Data: ' + DateTimeToStr(FLicencaManager.UltimaSincronizacao));
      FLicencaManager.Log('Sincronização manual bem-sucedida.');
    end
    else
    begin
      ShowMessage('❌ Falha na sincronização.' + sLineBreak +
                  'Verifique a conexão com a API.');
      FLicencaManager.Log('Falha na sincronização manual.');
    end;
  except
    on E: Exception do
      ShowMessage('Erro ao sincronizar: ' + E.Message);
  finally
    btnSincronizar.Enabled := True;
    btnSincronizar.Caption := 'Sincronizar Licença';
  end;
end;

procedure TfrmEmpresa.btnValidarLicencaClick(Sender: TObject);
begin
  try
    if not Assigned(FLicencaManager) then
    begin
      ShowMessage('Gerenciador de licenças não inicializado.');
      Exit;
    end;

    if FLicencaManager.ValidarLicencaAtual then
    begin
      ShowMessage('✅ Licença válida!' + sLineBreak +
                  'Última sincronização: ' + DateTimeToStr(FLicencaManager.UltimaSincronizacao) + sLineBreak +
                  'GUID: ' + FLicencaManager.GetMachineGUID);
      FLicencaManager.Log('Validação de licença bem-sucedida.');
    end
    else
    begin
      ShowMessage('❌ Licença inválida ou vencida!' + sLineBreak +
                  'Contate o administrador do sistema.');
      FLicencaManager.Log('Falha na validação de licença.');
    end;
  except
    on E: Exception do
      ShowMessage('Erro ao validar licença: ' + E.Message);
  end;
end;

procedure TfrmEmpresa.btnRegistrarEmpresaClick(Sender: TObject);
var
  LNome, LFantasia, LCNPJ, LContato, LEmail, LTelefone: string;
  LEndereco, LNumero, LBairro, LCidade, LEstado, LCEP: string;
begin
  try
    if not Assigned(FLicencaManager) then
    begin
      ShowMessage('Gerenciador de licenças não inicializado.');
      Exit;
    end;

    // Validar campos obrigatórios
    LNome := qryEmpresaRAZAO.AsString;
    LFantasia := qryEmpresaFANTASIA.AsString;
    LCNPJ := qryEmpresaCNPJ.AsString;
    LContato := '';  // Adicionar DBEdit se necessário
    LEmail := qryEmpresaEMAIL.AsString;
    LTelefone := qryEmpresaFONE.AsString;
    LEndereco := qryEmpresaENDERECO.AsString;
    LNumero := qryEmpresaNUMERO.AsString;
    LBairro := qryEmpresaBAIRRO.AsString;
    LCidade := qryEmpresaCIDADE.AsString;
    LEstado := qryEmpresaUF.AsString;
    LCEP := qryEmpresaCEP.AsString;

    if (LNome = '') or (LFantasia = '') or (LCNPJ = '') or (LEmail = '') or (LTelefone = '') then
    begin
      ShowMessage('⚠️ Preencha todos os campos obrigatórios:' + sLineBreak +
                  '- Razão Social' + sLineBreak +
                  '- Nome Fantasia' + sLineBreak +
                  '- CNPJ' + sLineBreak +
                  '- Email' + sLineBreak +
                  '- Telefone');
      Exit;
    end;

    btnRegistrarEmpresa.Enabled := False;
    btnRegistrarEmpresa.Caption := 'Registrando...';
    Application.ProcessMessages;

    if FLicencaManager.RegistrarEmpresaNoMySQL(
      LNome, LFantasia, LCNPJ, LContato, LEmail, LTelefone,
      '', LEndereco, LNumero, '', LBairro, LCidade, LEstado, LCEP) then
    begin
      ShowMessage('✅ Empresa registrada com sucesso!' + sLineBreak +
                  'CNPJ: ' + LCNPJ + sLineBreak +
                  'Nome: ' + LNome);
      FLicencaManager.Log('Empresa registrada com sucesso: ' + LCNPJ);
      
      // Sincronizar automaticamente após registrar
      if FLicencaManager.SincronizarComGerenciadorLicenca then
        ShowMessage('✅ Sincronização automática concluída.')
      else
        ShowMessage('⚠️ Empresa registrada, mas falha na sincronização.');
    end
    else
    begin
      ShowMessage('❌ Falha ao registrar empresa:' + sLineBreak +
                  FLicencaManager.GetUltimoErro);
      FLicencaManager.Log('Falha ao registrar empresa: ' + FLicencaManager.GetUltimoErro);
    end;
  except
    on E: Exception do
      ShowMessage('Erro ao registrar empresa: ' + E.Message);
  finally
    btnRegistrarEmpresa.Enabled := True;
    btnRegistrarEmpresa.Caption := 'Registrar Empresa';
  end;
end;

// ===== SEÇÃO: ADICIONAR AO CONSTRUTOR =====
// Adicionar no FormCreate:
//    FLicencaManager := TEmpresaLicencaManager.Create(Self);
//    FLicencaManager.OnLog := LicencaManagerLog;
//    CriarBotoesAPI;

procedure TfrmEmpresa.LicencaManagerLog(Sender: TObject; const AMsg: string);
begin
  // Adicionar log a um Memo se existir, ou simplesmente ignorar
  // Exemplo:
  // if Assigned(mmoLog) then
  //   mmoLog.Lines.Add(AMsg);
end;

// ===== SEÇÃO: ADICIONAR AO DESTRUTOR =====
// if Assigned(FLicencaManager) then
//   FLicencaManager.Free;
