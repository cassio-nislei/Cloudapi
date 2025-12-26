unit uEmpresa;

interface

uses
  Winapi.Windows, Winapi.Messages, System.SysUtils, System.Variants,
  System.Classes, System.StrUtils, Vcl.Graphics, aCBRUtil,
  Vcl.Controls, Vcl.Forms, Vcl.Dialogs, Data.DB, Vcl.Grids, Vcl.DBGrids,
  Vcl.StdCtrls, Vcl.ExtCtrls, Vcl.Buttons, FireDAC.Stan.Intf,
  FireDAC.Stan.Option, FireDAC.Stan.Param, FireDAC.Stan.Error, FireDAC.DatS,
  FireDAC.Phys.Intf, FireDAC.DApt.Intf, FireDAC.Stan.Async, FireDAC.DApt,
  FireDAC.Comp.DataSet, FireDAC.Comp.Client, Vcl.ComCtrls, Vcl.DBCtrls,
  Vcl.Mask,
  Vcl.ExtDlgs,  ACBrBase, ACBrEnterTab,
  ACBrValidador, ACBrSocket, ACBrCEP, uEmpresaLicencaManager, uDadosWeb,
  cxGraphics, cxControls, cxLookAndFeels, cxLookAndFeelPainters, cxContainer,
  cxEdit, cxTextEdit, cxDBEdit, System.IOUtils;

type
  TfrmEmpresa = class(TForm)
    Panel4: TPanel;
    OpenPicture: TOpenPictureDialog;
    dsEmpresa: TDataSource;
    PageControl1: TPageControl;
    TabSheet1: TTabSheet;
    Label1: TLabel;
    DBEdit1: TEdit;
    Label2: TLabel;
    DBEdit2: TEdit;
    Label3: TLabel;
    DBEdit3: TEdit;
    Label4: TLabel;
    DBEdit4: TEdit;
    Label6: TLabel;
    Label11: TLabel;
    DBEdit11: TEdit;
    Label10: TLabel;
    DBEdit10: TEdit;
    DBEdit8: TEdit;
    Label12: TLabel;
    Label9: TLabel;
    DBEdit9: TEdit;
    DBEdit7: TEdit;
    Label8: TLabel;
    DBEdit5: TEdit;
    Label5: TLabel;
    DBImage1: TDBImage;
    Label7: TLabel;
    DBEdit6: TEdit;
    Label13: TLabel;
    DBEdit13: TEdit;
    btnGravar: TSpeedButton;
    btnCancelar: TSpeedButton;
    ACBrEnterTab1: TACBrEnterTab;
    Label14: TLabel;
    DBEdit14: TEdit;
    Label15: TLabel;
    DBEdit15: TEdit;
    Label16: TLabel;
    TabSheet2: TTabSheet;
    Label17: TLabel;
    DBEdit17: TEdit;
    Label18: TLabel;
    Label19: TLabel;
    Label20: TLabel;
    DBEdit18: TEdit;
    DBEdit19: TEdit;
    dsCidade: TDataSource;
    Label21: TLabel;
    DBEdit12: TEdit;
    Button1: TButton;
    Label34: TLabel;
    DBEdit31: TEdit;
    Label35: TLabel;
    DBEdit32: TEdit;
    Label36: TLabel;
    DBEdit33: TEdit;
    Label37: TLabel;
    DBEdit34: TEdit;
    cbPessoa: TDBComboBox;
    Label39: TLabel;
    TabSheet4: TTabSheet;
    TabSheet5: TTabSheet;
    Label22: TLabel;
    DBEdit35: TEdit;
    ACBrValidador1: TACBrValidador;
    Label38: TLabel;
    DBEdit36: TEdit;
    DBEdit37: TEdit;
    Label40: TLabel;
    DBEdit16: TEdit;
    Label33: TLabel;
    BitBtn2: TBitBtn;
    ACBrCEP1: TACBrCEP;
    Label42: TLabel;
    DBEdit30: TEdit;
    DBEdit39: TEdit;
    Label43: TLabel;
    DBEdit40: TEdit;
    Label44: TLabel;
    PageControl2: TPageControl;
    TabSheet7: TTabSheet;
    DBCheckBox12: TDBCheckBox;
    DBCheckBox1: TDBCheckBox;
    DBCheckBox2: TDBCheckBox;
    DBCheckBox3: TDBCheckBox;
    DBCheckBox4: TDBCheckBox;
    DBCheckBox5: TDBCheckBox;
    DBCheckBox6: TDBCheckBox;
    DBCheckBox8: TDBCheckBox;
    DBCheckBox7: TDBCheckBox;
    DBCheckBox9: TDBCheckBox;
    DBCheckBox10: TDBCheckBox;
    DBCheckBox13: TDBCheckBox;
    DBCheckBox14: TDBCheckBox;
    DBCheckBox11: TDBCheckBox;
    DBCheckBox15: TDBCheckBox;
    TabSheet3: TTabSheet;
    GroupBox2: TGroupBox;
    Label23: TLabel;
    Label24: TLabel;
    Label25: TLabel;
    Label26: TLabel;
    DBEdit20: TEdit;
    DBEdit21: TEdit;
    DBEdit22: TEdit;
    DBEdit23: TEdit;
    GroupBox3: TGroupBox;
    Label27: TLabel;
    Label28: TLabel;
    Label29: TLabel;
    Label30: TLabel;
    DBEdit24: TEdit;
    DBEdit25: TEdit;
    DBEdit26: TEdit;
    DBEdit27: TEdit;
    GroupBox5: TGroupBox;
    Label31: TLabel;
    Label32: TLabel;
    DBEdit28: TEdit;
    DBEdit29: TEdit;
    TabSheet6: TTabSheet;
    Label45: TLabel;
    Label46: TLabel;
    DBEdit41: TEdit;
    DBEdit42: TEdit;
    DBCheckBox16: TDBCheckBox;
    qryEmpresa: TFDQuery;
    qryEmpresaFANTASIA: TStringField;
    qryEmpresaRAZAO: TStringField;
    qryEmpresaCNPJ: TStringField;
    qryEmpresaIE: TStringField;
    qryEmpresaIM: TStringField;
    qryEmpresaENDERECO: TStringField;
    qryEmpresaNUMERO: TStringField;
    qryEmpresaCOMPLEMENTO: TStringField;
    qryEmpresaBAIRRO: TStringField;
    qryEmpresaCIDADE: TStringField;
    qryEmpresaUF: TStringField;
    qryEmpresaCEP: TStringField;
    qryEmpresaFONE: TStringField;
    qryEmpresaFAX: TStringField;
    qryEmpresaSITE: TStringField;
    qryEmpresaLOGOMARCA: TBlobField;
    qryEmpresaID_PLANO_TRANSFERENCIA_C: TIntegerField;
    qryEmpresaID_PLANO_TRANSFERENCIA_D: TIntegerField;
    qryEmpresaID_CAIXA_GERAL: TIntegerField;
    qryEmpresaBLOQUEAR_ESTOQUE_NEGATIVO: TStringField;
    qryEmpresaID_CIDADE: TIntegerField;
    qryEmpresaCRT: TSmallintField;
    qryEmpresaID_UF: TSmallintField;
    qryEmpresaID_PLANO_VENDA: TIntegerField;
    qryEmpresaOBSFISCO: TMemoField;
    qryEmpresaCFOP: TStringField;
    qryEmpresaCSOSN: TStringField;
    qryEmpresaCST_ICMS: TStringField;
    qryEmpresaCST_ENTRADA: TStringField;
    qryEmpresaCST_SAIDA: TStringField;
    qryEmpresaCST_IPI: TStringField;
    qryEmpresaTIPO: TStringField;
    qryEmpresaFUNDACAO: TDateField;
    qryEmpresaUSU_CAD: TSmallintField;
    qryEmpresaUSU_ATU: TSmallintField;
    qryEmpresaNSERIE: TStringField;
    qryEmpresaCSENHA: TStringField;
    qryEmpresaIMP_F5: TStringField;
    qryEmpresaIMP_F6: TStringField;
    qryEmpresaMOSTRA_RESUMO_CAIXA: TStringField;
    qryEmpresaID_PLA_CONTA_FICHA_CLI: TIntegerField;
    qryEmpresaID_PLANO_CONTA_RETIRADA: TIntegerField;
    qryEmpresaUSA_PDV: TStringField;
    qryEmpresaRECIBO_VIAS: TStringField;
    qryEmpresaID_PLANO_TAXA_CARTAO: TIntegerField;
    qryEmpresaOBS_CARNE: TMemoField;
    qryEmpresaCAIXA_UNICO: TStringField;
    qryEmpresaCAIXA_RAPIDO: TStringField;
    qryEmpresaEMPRESA_PADRAO: TSmallintField;
    qryEmpresaID_PLANO_CONTA_DEVOLUCAO: TIntegerField;
    qryEmpresaN_INICIAL_NFE: TIntegerField;
    qryEmpresaN_INICIAL_NFCE: TIntegerField;
    qryEmpresaCHECA_ESTOQUE_FISCAL: TStringField;
    qryEmpresaNTERM: TStringField;
    qryEmpresaDESCONTO_PROD_PROMO: TStringField;
    qryEmpresaENVIAR_EMAIL_NFE: TStringField;
    qryEmpresaFILTRAR_EMPRESA_LOGIN: TStringField;
    qryEmpresaEMAIL: TStringField;
    qryEmpresaLANCAR_CARTAO_CREDITO: TStringField;
    qryEmpresaTRANSPORTADORA: TStringField;
    qryEmpresaAUTOPECAS: TStringField;
    qryEmpresaEMAIL_CONTADOR: TStringField;
    qryEmpresaTABELA_PRECO: TStringField;
    qryEmpresaINFORMAR_GTIN: TStringField;
    qryEmpresaATUALIZA_PR_VENDA: TStringField;
    qryEmpresaEXCLUI_PDV: TStringField;
    qryEmpresaRECOLHE_FCP: TStringField;
    qryEmpresaVENDA_SEMENTE: TStringField;
    qryEmpresaVIRTUAL_ID_UF: TIntegerField;
    qryEmpresaVIRTUAL_UF: TStringField;
    qryEmpresaULTIMONSU: TStringField;
    qryEmpresaCODIGO: TIntegerField;
    qryEmpresaALIQ_ICMS: TFMTBCDField;
    qryEmpresaALIQ_PIS: TFMTBCDField;
    qryEmpresaALIQ_COF: TFMTBCDField;
    qryEmpresaALIQ_IPI: TFMTBCDField;
    qryEmpresaLIMITE_DIARIO: TFMTBCDField;
    qryEmpresaPRAZO_MAXIMO: TSmallintField;
    qryEmpresaDIFAL_ORIGEM: TFMTBCDField;
    qryEmpresaDIFAL_DESTINO: TFMTBCDField;
    qryEmpresaULTIMO_PEDIDO: TIntegerField;
    qryEmpresaTAXA_VENDA_PRAZO: TFMTBCDField;
    qryEmpresaDATA_CADASTRO: TStringField;
    qryEmpresaDATA_VALIDADE: TStringField;
    qryEmpresaFLAG: TStringField;
    qryEmpresaCHECA: TStringField;
    qryEmpresaTIPO_CONTRATO: TIntegerField;
    qryEmpresaBLOQUEAR_PRECO: TStringField;
    DBCheckBox17: TDBCheckBox;
    DBCheckBox18: TDBCheckBox;
    qryEmpresaEXIBE_RESUMO_CAIXA: TStringField;
    DBCheckBox19: TDBCheckBox;
    DBCheckBox20: TDBCheckBox;
    qryEmpresaEXIBE_F3: TStringField;
    qryEmpresaEXIBE_F4: TStringField;
    DBCheckBox21: TDBCheckBox;
    qryEmpresaRESTAURANTE: TStringField;
    DBCheckBox22: TDBCheckBox;
    qryEmpresaPESQUISA_REFERENCIA: TStringField;
    DBCheckBox23: TDBCheckBox;
    qryEmpresaCARENCIA_JUROS: TIntegerField;
    qryEmpresaRESPONSAVEL_TECNICO: TStringField;
    Label41: TLabel;
    DBEdit38: TEdit;
    qryEmpresaID_PLANO_COMPRA: TIntegerField;
    DBCheckBox24: TDBCheckBox;
    qryEmpresaLER_PESO: TStringField;
    DBCheckBox25: TDBCheckBox;
    qryEmpresaFARMACIA: TStringField;
    qryEmpresaTIPO_EMPRESA: TIntegerField;
    qryEmpresaQTD_MESAS: TSmallintField;
    qryEmpresaTIPO_JUROS: TStringField;
    qryEmpresaJUROS_DIA: TFMTBCDField;
    qryEmpresaJUROS_MES: TFMTBCDField;
    qryEmpresaLOJA_ROUPA: TStringField;
    DBCheckBox26: TDBCheckBox;
    DBCheckBox27: TDBCheckBox;
    qryEmpresaCHECA_LIMITE: TStringField;
    qryEmpresaEMITE_ECF: TStringField;
    qryEmpresaDESCONTO_MAXIMO: TFMTBCDField;
    DBEdit43: TEdit;
    Label47: TLabel;
    Label48: TLabel;
    DBEdit44: TEdit;
    Label49: TLabel;
    DBEdit45: TEdit;
    qryEmpresaRESPONSAVEL_EMPRESA: TStringField;
    qryEmpresaPAGAMENTO_DINHEIRO: TStringField;
    DBCheckBox28: TDBCheckBox;
    qryEmpresaHABILITA_DESCONTO_PDV: TStringField;
    DBCheckBox29: TDBCheckBox;
    qryEmpresaPUXA_CFOP_PRODUTO: TStringField;
    DBCheckBox30: TDBCheckBox;
    DBEdit46: TEdit;
    Label50: TLabel;
    qryEmpresaUSA_BLUETOOTH_RESTA: TStringField;
    qryEmpresaLANCAR_CARTAO_CR: TStringField;
    DBCheckBox32: TDBCheckBox;
    qryEmpresaCFOP_EXTERNO: TStringField;
    BitBtn1: TBitBtn;
    TabSheet8: TTabSheet;
    qryEmpresaCNAE: TStringField;
    qryEmpresaOBSNFCE: TMemoField;
    DBEdit48: TEdit;
    Label52: TLabel;
    Label53: TLabel;
    Label54: TLabel;
    DBEdit49: TEdit;
    Label55: TLabel;
    qryEmpresaCODIGO_PAIS: TIntegerField;
    dsPaises: TDataSource;
    qryEmpresaMULTI_IDIOMA: TStringField;
    DBCheckBox31: TDBCheckBox;
    qryEmpresaHABILITA_ACRESCIMO: TStringField;
    DBCheckBox33: TDBCheckBox;
    DBEdit50: TEdit;
    Label56: TLabel;
    qryEmpresaCOD_FPG_DINHEIRO: TIntegerField;
    qryEmpresaCSOSN_EXTERNO: TStringField;
    qryEmpresaCST_EXTERNO: TStringField;
    qryEmpresaALIQ_ICMS_EXTERNO: TFMTBCDField;
    GroupBox1: TGroupBox;
    Label58: TLabel;
    Label59: TLabel;
    Label60: TLabel;
    Label61: TLabel;
    DBEdit52: TEdit;
    DBEdit53: TEdit;
    DBEdit54: TEdit;
    DBEdit55: TEdit;
    qryEmpresaID_CAD_CLI: TIntegerField;
    qryEmpresaDT_INST: TDateField;
    qryEmpresaDT_HJ: TDateField;
    qryEmpresaDT_PR: TDateField;
    DBCheckBox34: TDBCheckBox;
    qryEmpresaUSAR_TEF: TStringField;
    DBCheckBox35: TDBCheckBox;
    qryEmpresaTEF_GERAR_NFCE_AUTO: TStringField;
    DBCheckBox36: TDBCheckBox;
    qryEmpresaHABILITA_FUNC_SOFTHOUSE: TStringField;
    DBCheckBox37: TDBCheckBox;
    DBCheckBox38: TDBCheckBox;
    qryEmpresaPETSHOP: TStringField;
    qryEmpresaNT_COMPRA_IMP_CUSTO: TStringField;
    DBCheckBox39: TDBCheckBox;
    qryEmpresaNAO_ATUALIZA_GRADE: TStringField;
    DBCheckBox40: TDBCheckBox;
    qryEmpresaUSAAPIPIX_MERCADOPAGO: TStringField;
    qryEmpresaACCESSTOKEN_MERCADOPAGO: TStringField;
    TabSheet9: TTabSheet;
    qryEmpresaUSAR_VLR_FECHAMENTO_ABERTURA: TStringField;
    DBCheckBox42: TDBCheckBox;
    qryEmpresaPERMITIR_BAIXA_ESTOQUE_F5: TStringField;
    DBCheckBox43: TDBCheckBox;
    qryEmpresaACRESCIMO_MAXIMO: TFMTBCDField;
    Label57: TLabel;
    DBEdit51: TEdit;
    qryEmpresaUSAR_SISTEMA_WEB: TStringField;
    qryEmpresaCADASTRO_WEB: TStringField;
    DBCheckBox44: TDBCheckBox;
    DBCheckBox45: TDBCheckBox;
    qryEmpresaUSAAPIPIX_BB: TStringField;
    qryEmpresaBASICTOKEN_BB: TMemoField;
    qryEmpresaAPPKEY_BB: TStringField;
    qryEmpresaCLIENTID_BB: TStringField;
    qryEmpresaCLIENTSECRET_BB: TStringField;
    DBRadioGroup1: TDBRadioGroup;
    PageControl3: TPageControl;
    tabBancoBrasil: TTabSheet;
    GroupBox6: TGroupBox;
    Label62: TLabel;
    Label63: TLabel;
    Label64: TLabel;
    Label65: TLabel;
    DBCheckBox46: TDBCheckBox;
    DBMemo1: TDBMemo;
    DBEdit56: TEdit;
    DBEdit57: TEdit;
    DBEdit58: TEdit;
    tabGerenciaNet: TTabSheet;
    tabSantander: TTabSheet;
    tabSicoob: TTabSheet;
    tabBradesco: TTabSheet;
    tabOutros: TTabSheet;
    tabMercadoPago: TTabSheet;
    GroupBox4: TGroupBox;
    Label51: TLabel;
    DBCheckBox41: TDBCheckBox;
    DBEdit47: TEdit;
    qryEmpresaAPI_PIX_BANCO: TIntegerField;
    qryEmpresaAPI_PIX_AMBIENTE: TIntegerField;
    Panel1: TPanel;
    DBRadioGroup2: TDBRadioGroup;
    qryEmpresaCHAVE_PIX_BB: TStringField;
    qryEmpresaAPI_PIX_TIPO_CHAVE_PIX: TIntegerField;
    DBRadioGroup3: TDBRadioGroup;
    Label66: TLabel;
    DBEdit59: TEdit;
    TabSheet10: TTabSheet;
    Label67: TLabel;
    DBEdit60: TEdit;
    qryEmpresaNUMERO_RECIBO: TIntegerField;
    SpeedButton1: TSpeedButton;
    SpeedButton2: TSpeedButton;
    procedure btnSairClick(Sender: TObject);
    procedure btnCancelarClick(Sender: TObject);
    procedure btnGravarClick(Sender: TObject);
    procedure FormKeyDown(Sender: TObject; var Key: Word; Shift: TShiftState);
    procedure DBImage1Click(Sender: TObject);
    procedure cbCidadeExit(Sender: TObject);
    procedure Button1Click(Sender: TObject);
    procedure FormCreate(Sender: TObject);
    procedure DBEdit6Exit(Sender: TObject);
    procedure BitBtn2Click(Sender: TObject);
    procedure ACBrCEP1BuscaEfetuada(Sender: TObject);
    procedure FormShow(Sender: TObject);
    procedure DBEdit5KeyDown(Sender: TObject; var Key: Word;
      Shift: TShiftState);
    procedure cbCidadeEnter(Sender: TObject);
    procedure cbCidadeKeyPress(Sender: TObject; var Key: Char);
    procedure DBEdit9KeyPress(Sender: TObject; var Key: Char);
    procedure qryEmpresaNewRecord(DataSet: TDataSet);
    procedure qryEmpresaAfterPost(DataSet: TDataSet);
    procedure qryEmpresaBeforePost(DataSet: TDataSet);
    procedure qryEmpresaBeforeEdit(DataSet: TDataSet);
    procedure BitBtn1Click(Sender: TObject);
    procedure DBEdit9KeyDown(Sender: TObject; var Key: Word;
      Shift: TShiftState);
    procedure FormActivate(Sender: TObject);
    procedure DBCheckBox44Click(Sender: TObject);
    procedure dsEmpresaDataChange(Sender: TObject; Field: TField);
    procedure DBRadioGroup1Change(Sender: TObject);
    procedure DBCheckBox37Click(Sender: TObject);
    procedure SpeedButton1Click(Sender: TObject);
    procedure SpeedButton2Click(Sender: TObject);
    procedure btnValidarPassportClick(Sender: TObject);
    procedure btnSincronizarClick(Sender: TObject);
    procedure btnValidarLicencaClick(Sender: TObject);
    procedure btnRegistrarEmpresaClick(Sender: TObject);
  private
    { Private declarations }
    FLogList: TStringList;
    procedure TentarRegistrarEmpresaNaAPI;
    procedure LicencaManagerLog(Sender: TObject; const AMsg: string);
  public
    Tela: string;
    Cnpj, inscricao: string;
    { Public declarations }
  end;

var
  frmEmpresa: TfrmEmpresa;

implementation

{$R *.dfm}

uses Udados, uRotinasComuns;

procedure TfrmEmpresa.btnSairClick(Sender: TObject);
begin
  close;
end;

procedure TfrmEmpresa.Button1Click(Sender: TObject);
begin
  qryEmpresaLOGOMARCA.Clear;
  DBImage1.Picture := nil;
end;

procedure TfrmEmpresa.DBCheckBox37Click(Sender: TObject);
begin
  if DBCheckBox37.Checked then
    begin
      if qryEmpresa.State in [dsBrowse] then
        Exit;
//      if not Dados.PedirSenha then
//        begin
//          if not (qryEmpresa.State in dsEditModes) then
//            qryEmpresa.Edit;
//          qryEmpresaHABILITA_FUNC_SOFTHOUSE.AsString  :=  'N';
//          DBCheckBox37.Checked  :=  False;
//        end;
    end;
end;

procedure TfrmEmpresa.DBCheckBox44Click(Sender: TObject);
begin
  DBCheckBox45.Visible  :=  DBCheckBox44.Checked;
end;

procedure TfrmEmpresa.DBEdit5KeyDown(Sender: TObject; var Key: Word;
  Shift: TShiftState);
begin
  if Key = vk_f2 then
    BitBtn2.Click;
end;

procedure TfrmEmpresa.DBEdit6Exit(Sender: TObject);
begin
  if (qryEmpresa.State in dsEditModes) then
    if trim(DBEdit6.Text) = '' then
    begin
      qryEmpresaFANTASIA.Value := qryEmpresaRAZAO.Value;
    end;
end;

procedure TfrmEmpresa.DBEdit9KeyDown(Sender: TObject; var Key: Word;
  Shift: TShiftState);
var
  LCNPJ: string;
  LManager: TEmpresaLicencaManager;
  LCNPJLimpo: string;
begin
  if Key = vk_f2 then
    BitBtn1Click(SELF)
  else if Key = VK_RETURN then
  begin
    // Ao pressionar ENTER, buscar CNPJ na API
    LCNPJ := Trim(DBEdit9.Text);

    if LCNPJ = '' then
    begin
      ShowMessage('Informe o CNPJ antes de pressionar ENTER.');
      Key := 0;
      Exit;
    end;

    // Remover formatação do CNPJ (manter apenas números)
    LCNPJLimpo := StringReplace(LCNPJ, '.', '', [rfReplaceAll]);
    LCNPJLimpo := StringReplace(LCNPJLimpo, '/', '', [rfReplaceAll]);
    LCNPJLimpo := StringReplace(LCNPJLimpo, '-', '', [rfReplaceAll]);
    LCNPJLimpo := Trim(LCNPJLimpo);

    // Validar comprimento do CNPJ
    if Length(LCNPJLimpo) <> 14 then
    begin
      ShowMessage('CNPJ inválido. Deve conter 14 dígitos.');
      Key := 0;
      Exit;
    end;

    // Criar instância do gerenciador de licença
    LManager := TEmpresaLicencaManager.Create(nil);
    try
      // CASO 1: Tentar carregar empresa da API (usando CarregarEmpresaDoMySQL)
      if LManager.CarregarEmpresaDoMySQL(LCNPJLimpo) then
      begin
        ShowMessage('Empresa encontrada na API e carregada com sucesso!');
        qryEmpresa.Refresh;
        Key := 0;
        Exit;
      end;

      // CASO 2: Se não existe na API, usar PreencherEmpresaComACBr como fallback
      if not Assigned(DadosWeb) then
      begin
        ShowMessage('DataModule DadosWeb não está disponível. Verifique a inicialização da aplicação.');
        Key := 0;
        Exit;
      end;

      if not Assigned(DadosWeb.ACBrConsultaCNPJ1) then
      begin
        ShowMessage('Componente ACBrConsultaCNPJ não está inicializado. Verifique as dependências do ACBr.');
        Key := 0;
        Exit;
      end;

      try
        // Tentar consultar CNPJ via ACBr (com timeout e tratamento de erros)
        Application.ProcessMessages; // Processar eventos pendentes

        if DadosWeb.ACBrConsultaCNPJ1.Consulta(LCNPJLimpo) then
        begin
          // Mostrar dados retornados pelo ACBr
          ShowMessage(
            'DADOS ENCONTRADOS NO ACBr:' + sLineBreak + sLineBreak +
            'CNPJ: ' + DadosWeb.ACBrConsultaCNPJ1.CNPJ + sLineBreak +
            'Razão Social: ' + DadosWeb.ACBrConsultaCNPJ1.RazaoSocial + sLineBreak +
            'Fantasia: ' + DadosWeb.ACBrConsultaCNPJ1.Fantasia + sLineBreak +
            'Endereço: ' + DadosWeb.ACBrConsultaCNPJ1.Endereco + sLineBreak +
            'Número: ' + DadosWeb.ACBrConsultaCNPJ1.Numero + sLineBreak +
            'Complemento: ' + DadosWeb.ACBrConsultaCNPJ1.Complemento + sLineBreak +
            'Bairro: ' + DadosWeb.ACBrConsultaCNPJ1.Bairro + sLineBreak +
            'Cidade: ' + DadosWeb.ACBrConsultaCNPJ1.Cidade + sLineBreak +
            'UF: ' + DadosWeb.ACBrConsultaCNPJ1.UF + sLineBreak +
            'CEP: ' + DadosWeb.ACBrConsultaCNPJ1.CEP + sLineBreak +
            'Telefone: ' + DadosWeb.ACBrConsultaCNPJ1.Telefone + sLineBreak +
            'Email: ' + DadosWeb.ACBrConsultaCNPJ1.EndEletronico + sLineBreak +
            'CNAE Principal: ' + DadosWeb.ACBrConsultaCNPJ1.CNAE1 + sLineBreak +
            'Inscrição Estadual: ' + DadosWeb.ACBrConsultaCNPJ1.InscricaoEstadual
          );
          
          // Empresa encontrada no ACBr, preencher formulário usando o manager
          LManager.PreencherEmpresaComACBr(DadosWeb.ACBrConsultaCNPJ1);
          
          // Forçar atualização visual
          Application.ProcessMessages;
          
          ShowMessage('Empresa consultada via ACBr e preenchida automaticamente!');
          Key := 0;
          Exit;
        end
        else
        begin
          ShowMessage('CNPJ não encontrado na API e na base ACBr. Preencha os dados manualmente.');
        end;
      except
        on E: Exception do
        begin
          ShowMessage('Erro ao consultar CNPJ via ACBr:' + sLineBreak + 
            'Tipo: ' + E.ClassName + sLineBreak +
            'Mensagem: ' + E.Message + sLineBreak +
            'Verifique sua conexão com a internet e tente novamente.');
        end;
      end;

    finally
      LManager.Free;
    end;

    Key := 0;
  end;
end;

procedure TfrmEmpresa.DBEdit9KeyPress(Sender: TObject; var Key: Char);
begin
  if not(Key in ['0' .. '9', #8, #9, #13, #27]) then
    Key := #0;
end;

procedure TfrmEmpresa.DBImage1Click(Sender: TObject);
begin
  OpenPicture.Execute;
  if OpenPicture.FileName <> '' then
  begin
    qryEmpresaLOGOMARCA.LoadFromFile(OpenPicture.FileName);
  end;

end;

procedure TfrmEmpresa.DBRadioGroup1Change(Sender: TObject);
begin
  try
    if not Assigned(qryEmpresaAPI_PIX_BANCO) then
      Exit;
    if qryEmpresaAPI_PIX_BANCO.IsNull then
      Exit;
    if not Assigned(PageControl3) then
      Exit;
    if not Assigned(DBRadioGroup1) then
      Exit;
      
    case DBRadioGroup1.Value.ToInteger of
      0:
        if Assigned(tabBancoBrasil) then
          PageControl3.ActivePage := tabBancoBrasil;
      1:
        if Assigned(tabGerenciaNet) then
          PageControl3.ActivePage := tabGerenciaNet;
      2:
        if Assigned(tabSantander) then
          PageControl3.ActivePage := tabSantander;
      3:
        if Assigned(tabSicoob) then
          PageControl3.ActivePage := tabSicoob;
      4:
        if Assigned(tabBradesco) then
          PageControl3.ActivePage := tabBradesco;
      5:
        if Assigned(tabOutros) then
          PageControl3.ActivePage := tabOutros;
      6:
        if Assigned(tabMercadoPago) then
          PageControl3.ActivePage := tabMercadoPago;
    end;
  except on E: Exception do
    begin
      // Nao exibir mensagem para nao interromper a inicializacao
      // ShowMessage('Erro em DBRadioGroup1Change: ' + E.Message);
    end;
  end;
end;

procedure TfrmEmpresa.dsEmpresaDataChange(Sender: TObject; Field: TField);
begin
  DBCheckBox45.Visible  :=  DBCheckBox44.Checked;
end;

procedure TfrmEmpresa.cbCidadeEnter(Sender: TObject);
begin
  ACBrEnterTab1.EnterAsTab := false;
end;

procedure TfrmEmpresa.cbCidadeExit(Sender: TObject);
begin
  ACBrEnterTab1.EnterAsTab := True;
  if (qryEmpresa.State in dsEditModes) then
    qryEmpresaCIDADE.Value := dados.qryCidadeDESCRICAO.Value;
  qryEmpresaID_UF.Value := dados.qryCidadeCODUF.Value;
  qryEmpresaUF.Value := dados.qryCidadeUF.Value;
end;

procedure TfrmEmpresa.cbCidadeKeyPress(Sender: TObject; var Key: Char);
begin
  if Key = #13 then
    SendMessage(SELF.Handle, WM_NEXTDLGCTL, 0, 0);
end;

procedure TfrmEmpresa.FormActivate(Sender: TObject);
begin
  dados.vForm := nil;
  dados.vForm := self; dados.GetComponentes;
end;

procedure TfrmEmpresa.FormCreate(Sender: TObject);
begin
  try
    PageControl1.ActivePageIndex := 0;

    // Verificar se a conexao esta ativa
    if not Assigned(dados) then
    begin
      ShowMessage('Modulo de dados nao foi inicializado.');
      Exit;
    end;

    if not dados.Conexao.Connected then
    begin
      ShowMessage('Conexao com banco de dados nao esta ativa. Tente novamente.');
      Exit;
    end;

    // Fechar e reabrir as queries com seguranca
    try
      if Assigned(dados.qryCidade) then
      begin
        if dados.qryCidade.Active then
          dados.qryCidade.Close;
        if Trim(dados.qryCidade.SQL.Text) <> '' then
          dados.qryCidade.Open;
      end;
    except
      // Ignorar erros de cidade
    end;

    try
      if Assigned(dados.qryPaises) then
      begin
        if dados.qryPaises.Active then
          dados.qryPaises.Close;
        if Trim(dados.qryPaises.SQL.Text) <> '' then
          dados.qryPaises.Open;
      end;
    except
      // Ignorar erros de paises
    end;

    tabBancoBrasil.TabVisible :=  False;
    tabGerenciaNet.TabVisible :=  False;
    tabSantander.TabVisible :=  False;
    tabSicoob.TabVisible :=  False;
    tabBradesco.TabVisible :=  False;
    tabOutros.TabVisible :=  False;
    tabMercadoPago.TabVisible :=  False;
  except on E: Exception do
    ShowMessage('Erro ao inicializar formulario: ' + E.Message);
  end;
end;

procedure TfrmEmpresa.FormKeyDown(Sender: TObject; var Key: Word;
  Shift: TShiftState);
begin
  if Key = VK_F5 then
    btnGravarClick(SELF);
  if Key = VK_escape then
    if Application.messageBox
      ('Tem Certeza de que deseja sair do cadastro de produtos?', 'Confirma��o',
      mb_YesNo) = mrYes then
    begin
      btnCancelar.Click;
    end

end;

procedure TfrmEmpresa.FormShow(Sender: TObject);
begin
  qryEmpresa.Open;
   qryEmpresa.Insert;
  try
    if Assigned(DBEdit2) then
      DBEdit2.SetFocus;
    if Assigned(DBRadioGroup1) then
      DBRadioGroup1Change(Self);
  except
    // Ignorar erros de focus
  end;
end;

procedure TfrmEmpresa.qryEmpresaAfterPost(DataSet: TDataSet);
var
  idempresa: integer;
begin
  try
    dados.Conexao.CommitRetaining;

    idempresa := dados.qryEmpresaCODIGO.Value;

    if dados.qryEmpresa.Active then
      dados.qryEmpresa.Close;
    dados.qryEmpresa.Open;
    dados.qryEmpresa.Locate('CODIGO', idempresa, []);
  except on E: Exception do
    ShowMessage('Erro ao atualizar empresa: ' + E.Message);
  end;
end;

procedure TfrmEmpresa.qryEmpresaBeforeEdit(DataSet: TDataSet);
begin
  Cnpj := qryEmpresaCNPJ.Value;
  inscricao := qryEmpresaIE.Value;
end;

procedure TfrmEmpresa.qryEmpresaBeforePost(DataSet: TDataSet);
begin
  if qryEmpresa.State = dsEdit then
  begin
    qryEmpresaCNPJ.Value := TiraPontos(qryEmpresaCNPJ.Value);

    if Cnpj <> qryEmpresaCNPJ.Value then // verifica se alterou cnpj e bloquei
      qryEmpresaDATA_VALIDADE.Value := dados.Crypt('C', datetostr(Date - 1));

    if inscricao <> qryEmpresaIE.Value then // verifica se alterou ie e bloqueia
      qryEmpresaDATA_VALIDADE.Value := dados.Crypt('C', datetostr(Date - 1));
  end;
end;

procedure TfrmEmpresa.qryEmpresaNewRecord(DataSet: TDataSet);
begin
  qryEmpresaNSERIE.Value := '';
  qryEmpresaCSENHA.Value := '';
  qryEmpresaDATA_CADASTRO.AsString := dados.Crypt('C', datetostr(Date));
  qryEmpresaDATA_VALIDADE.AsString := dados.Crypt('C', datetostr(Date + 1));
  qryEmpresaCHECA.AsString := dados.Crypt('C', 'DEMONSTRACAO');
  qryEmpresaFUNDACAO.Value := NOW;
  qryEmpresaUSU_CAD.Value := 0;
  qryEmpresaUSU_ATU.Value := 0;
  qryEmpresaID_PLANO_TRANSFERENCIA_C.Value := 3;
  qryEmpresaID_PLANO_TRANSFERENCIA_D.Value := 4;
  qryEmpresaID_CAIXA_GERAL.Value := 1;
  qryEmpresaBLOQUEAR_ESTOQUE_NEGATIVO.Value := 'S';
  qryEmpresaIE.Value := '';
  qryEmpresaRESPONSAVEL_TECNICO.Value := 'S';
  qryEmpresaEXIBE_F3.Value := 'S';
  qryEmpresaEXIBE_F4.Value := 'S';
  qryEmpresaIMP_F5.Value := 'N';
  qryEmpresaIMP_F6.Value := 'N';
  qryEmpresaDIFAL_ORIGEM.Value := 0;
  qryEmpresaDIFAL_DESTINO.Value := 100;
  qryEmpresaRECIBO_VIAS.Value := 'S';
  qryEmpresaCRT.Value := 1;
  qryEmpresaID_PLANO_VENDA.Value := 2;
  qryEmpresaOBSFISCO.Value :=
    'I - "DOCUMENTO EMITIDO POR ME OU EPP OPTANTE PELO SIMPLES NACIONAL"; e II - "N�O GERA DIREITO A CR�DITO FISCAL DE ICMS, DE ISS E DE IPI".';
  qryEmpresaCFOP.Value := '5102';
  qryEmpresaCFOP_EXTERNO.Value := '6102';
  qryEmpresaCSOSN.Value := '102';
  qryEmpresaCST_ICMS.Value := '041';
  qryEmpresaCST_ENTRADA.Value := '07';
  qryEmpresaCST_SAIDA.Value := '07';
  qryEmpresaCST_IPI.Value := '53';
  qryEmpresaALIQ_PIS.Value := 0;
  qryEmpresaALIQ_COF.Value := 0;
  qryEmpresaALIQ_IPI.Value := 0;
  qryEmpresaALIQ_ICMS.Value := 0;
  qryEmpresaNSERIE.Value := dados.Crypt('C', 'DEMONSTRACAO');
  qryEmpresaNTERM.Value := dados.Crypt('C', '3');
  qryEmpresaMOSTRA_RESUMO_CAIXA.Value := 'N';
  qryEmpresaLIMITE_DIARIO.Value := 1;
  qryEmpresaPRAZO_MAXIMO.Value := 1;
  qryEmpresaUSA_PDV.Value := 'S';
  qryEmpresaRECIBO_VIAS.Value := '1';
  qryEmpresaOBS_CARNE.Value := 'OBRIGADO PELA PREFER�NCIA!';
  qryEmpresaCAIXA_UNICO.Value := 'N';
  qryEmpresaCHECA_ESTOQUE_FISCAL.Value := 'S';
  qryEmpresaBLOQUEAR_PRECO.Value := 'N';
  qryEmpresaRECOLHE_FCP.Value := 'N';
  qryEmpresaN_INICIAL_NFCE.Value := 1;
  qryEmpresaN_INICIAL_NFE.Value := 1;
  qryEmpresaID_PLANO_CONTA_DEVOLUCAO.Value := 9;
  qryEmpresaID_PLA_CONTA_FICHA_CLI.Value := 10;
  qryEmpresaID_PLANO_CONTA_RETIRADA.Value := 11;
  qryEmpresaID_PLANO_TAXA_CARTAO.Value := 8;
  qryEmpresaEMPRESA_PADRAO.Value := 1;
  qryEmpresaCAIXA_RAPIDO.Value := 'N';
  qryEmpresaENVIAR_EMAIL_NFE.Value := 'N';
  qryEmpresaLANCAR_CARTAO_CREDITO.Value := 'N';
  qryEmpresaTABELA_PRECO.Value := 'N';
  qryEmpresaEXCLUI_PDV.Value := 'N';
  qryEmpresaDESCONTO_PROD_PROMO.Value := 'N';
  qryEmpresaRECIBO_VIAS.Value := 'N';
  qryEmpresaTRANSPORTADORA.Value := 'N';
  qryEmpresaVENDA_SEMENTE.Value := 'N';
  qryEmpresaINFORMAR_GTIN.Value := 'N';
  qryEmpresaPESQUISA_REFERENCIA.Value := 'N';
  qryEmpresaBLOQUEAR_PRECO.Value := 'N';
  qryEmpresaRESTAURANTE.Value := 'N';
  qryEmpresaFARMACIA.Value := 'N';
  qryEmpresaLER_PESO.Value := 'N';
  qryEmpresaLANCAR_CARTAO_CR.Value := 'N';
  qryEmpresaEXIBE_RESUMO_CAIXA.Value := 'N';
  qryEmpresaPUXA_CFOP_PRODUTO.Value := 'N';
  qryEmpresaHABILITA_DESCONTO_PDV.Value := 'N';
  qryEmpresaCHECA_LIMITE.Value := 'N';
  qryEmpresaPAGAMENTO_DINHEIRO.Value := 'N';
  qryEmpresaLOJA_ROUPA.Value := 'N';
  qryEmpresaRESPONSAVEL_TECNICO.Value := 'N';
  qryEmpresaID_CAD_CLI.Value  :=  20;
  (*Padr�es seu Jos�*)
  qryEmpresaHABILITA_FUNC_SOFTHOUSE.AsString  :=  'N';
  qryEmpresaLANCAR_CARTAO_CREDITO.AsString  :=  'S';
  qryEmpresaEXCLUI_PDV.AsString :=  'S';
  qryEmpresaCAIXA_RAPIDO.AsString :=  'S';
  qryEmpresaEXIBE_RESUMO_CAIXA.AsString :=  'S';
  qryEmpresaBLOQUEAR_ESTOQUE_NEGATIVO.AsString  :=  'N';
  qryEmpresaNAO_ATUALIZA_GRADE.AsString :=  'N';
  qryEmpresaUSA_PDV.AsString  :=  'N';
  qryEmpresaEXIBE_F3.AsString :=  'N';
  qryEmpresaIMP_F6.AsString :=  'N';
  qryEmpresaPUXA_CFOP_PRODUTO.AsString  :=  'S';
  qryEmpresaAUTOPECAS.AsString  :=  'N';
  qryEmpresaPETSHOP.AsString  :=  'N';
  qryEmpresaNT_COMPRA_IMP_CUSTO.AsString  :=  'N';
end;

procedure TfrmEmpresa.SpeedButton1Click(Sender: TObject);
var
  LCodigo: Integer;
begin
  try
    // DEBUG 1: Verificar se há registro selecionado
    if dados.qryEmpresa.IsEmpty then
    begin
      ShowMessage('DEBUG: Nenhuma empresa carregada para sincronizar!');
      Exit;
    end;

    // DEBUG 2: Obter código da empresa
    LCodigo := 10;//dados.qryEmpresaCODIGO.AsInteger;
    ShowMessage(Format('DEBUG 1: Sincronizando empresa código %d', [LCodigo]));

    // DEBUG 3: Verificar CNPJ
    if Trim(dados.qryEmpresaCNPJ.AsString) = '' then
    begin
      ShowMessage('DEBUG: CNPJ vazio na empresa!');
      Exit;
    end;
    ShowMessage(Format('DEBUG 2: CNPJ = %s', [dados.qryEmpresaCNPJ.AsString]));

    // DEBUG 4: Fechar e reabrir query do formulário
    if qryEmpresa.Active then
      qryEmpresa.Close;
    
    qryEmpresa.ParamByName('cod').AsInteger := LCodigo;
    qryEmpresa.Open;
    
    if qryEmpresa.IsEmpty then
    begin
      ShowMessage('DEBUG: Query local não retornou registros!');
      Exit;
    end;
    
    ShowMessage('DEBUG 3: Query local carregada com sucesso');

    // DEBUG 5: Chamar função de sincronização com API
    ShowMessage('DEBUG 4: Iniciando sincronização com API...');
    TentarRegistrarEmpresaNaAPI;
    ShowMessage('DEBUG 5: Sincronização concluída!');
    
  except
    on E: Exception do
    begin
      ShowMessage(Format('ERRO em SpeedButton1Click: %s | Tipo: %s', 
        [E.Message, E.ClassName]));
    end;
  end;
end;

procedure TfrmEmpresa.SpeedButton2Click(Sender: TObject);
var
  LCNPJ: string;
  LManager: TEmpresaLicencaManager;
  LDebugMsg: string;
  LCaminhoLog: string;
  LLogContent: TStringList;
begin
  LLogContent := TStringList.Create;
  try
    LCaminhoLog := ExtractFilePath(Application.ExeName) + 'passport_test.log';
    
    // Se arquivo existe, carregar conteúdo anterior
    if FileExists(LCaminhoLog) then
    try
      LLogContent.LoadFromFile(LCaminhoLog);
    except
      LLogContent.Clear;
    end;
    
    LLogContent.Add('');
    LLogContent.Add('=== TESTE PASSPORT - ' + FormatDateTime('dd/mm/yyyy hh:mm:ss', Now) + ' ===');
    
    try
      // Verificar se query está vazia
//       qryEmpresa.Close;
//       qryEmpresa.ParamByName('cod').AsString := '10';
//       qryEmpresa.Open;
//
//      if qryEmpresa.IsEmpty then
//      begin
//        LDebugMsg := 'ERRO: Nenhuma empresa carregada. Selecione uma empresa primeiro.';
//        LLogContent.Add(LDebugMsg);
//        LLogContent.SaveToFile(LCaminhoLog);
//        ShowMessage(LDebugMsg);
//        Exit;
//      end;
      
      // Obter CNPJ
      LCNPJ := '01611275000205';//Trim(qryEmpresaCNPJ.AsString);
      if LCNPJ = '' then
      begin
        LDebugMsg := 'ERRO: CNPJ está vazio. Preencha o CNPJ antes de testar.';
        LLogContent.Add(LDebugMsg);
        LLogContent.SaveToFile(LCaminhoLog);
        ShowMessage(LDebugMsg);
        Exit;
      end;
      
      LDebugMsg := 'Testando CNPJ: ' + LCNPJ;
      LLogContent.Add(LDebugMsg);
      LLogContent.Add('Hostname: ' + dados.nometerminal);
      ShowMessage('Validando Passport na API...' + sLineBreak + 'CNPJ: ' + LCNPJ);
      
      // Criar gerenciador
      LManager := TEmpresaLicencaManager.Create(nil);
      try
        // Chamar validação do passport
        if LManager.ValidarPassportEmpresa(LCNPJ, dados.nometerminal, LManager.GetMachineSerial) then
        begin
          LDebugMsg := '✓ RESULTADO: CNPJ JÁ EXISTE NA API!' + sLineBreak +
                       'Este CNPJ já foi registrado anteriormente.' + sLineBreak + sLineBreak +
                       'Se você já sincronizou com SpeedButton1, o CNPJ deve estar aqui.';
          LLogContent.Add('✓ SUCESSO: CNPJ ' + LCNPJ + ' já existe na API');
        end
        else
        begin
          LDebugMsg := '✗ RESULTADO: CNPJ NÃO EXISTE NA API' + sLineBreak +
                       'Este CNPJ pode ser registrado.' + sLineBreak +
                       'Use SpeedButton1 para registrar na API.' + sLineBreak + sLineBreak +
                       '*** PROBLEMA DETECTADO ***' + sLineBreak +
                       'Se você já sincronizou com SpeedButton1 e recebeu "SUCESSO",' + sLineBreak +
                       'mas o Passport não encontra o CNPJ, pode indicar:' + sLineBreak +
                       '1. A API está com latência (dados não replicados ainda)' + sLineBreak +
                       '2. O registro foi feito em outro servidor/banco' + sLineBreak +
                       '3. Houve erro na sincronização mas mostrou como sucesso';
          LLogContent.Add('✗ INFO: CNPJ ' + LCNPJ + ' não encontrado (disponível para registro)');
        end;
        
        LLogContent.Add('Data/Hora: ' + FormatDateTime('dd/mm/yyyy hh:mm:ss', Now));
        LLogContent.SaveToFile(LCaminhoLog);
        ShowMessage(LDebugMsg + sLineBreak + sLineBreak + 'Log: ' + LCaminhoLog);
        
      finally
        LManager.Free;
      end;
      
    except
      on E: Exception do
      begin
        LDebugMsg := 'ERRO: ' + E.Message;
        LLogContent.Add('EXCEPTION: ' + E.Message + ' (' + E.ClassName + ')');
        LLogContent.SaveToFile(LCaminhoLog);
        ShowMessage(LDebugMsg + sLineBreak + sLineBreak + 'Log: ' + LCaminhoLog);
      end;
    end;
    
  finally
    LLogContent.Free;
  end;
end;

procedure TfrmEmpresa.ACBrCEP1BuscaEfetuada(Sender: TObject);
begin
  if ACBrCEP1.Enderecos.Count < 1 then
    ShowMessage('Nenhum Endere�o encontrado')
  else
  begin
    with ACBrCEP1.Enderecos[0] do
    begin
      qryEmpresaENDERECO.Value := UpperCase(Tipo_Logradouro + ' ' + Logradouro);
      qryEmpresaBAIRRO.Value := UpperCase(Bairro);
      qryEmpresaCIDADE.Value := UpperCase(Municipio);
      qryEmpresaID_CIDADE.AsString := IBGE_Municipio;
      qryEmpresaUF.AsString := UpperCase(UF);
    end;
  end;
end;

procedure TfrmEmpresa.BitBtn1Click(Sender: TObject);
begin
  if qryEmpresaTIPO.Value = 'JUR�DICA' then
  begin

    try
      dmrotinas.Pessoa.Clear;
      dmrotinas.BuscaCNPJ(TiraPontos(DBEdit9.text));
      qryEmpresaRAZAO.Value := UpperCase(dmrotinas.Pessoa.razao);
      qryEmpresaFANTASIA.Value := UpperCase(dmrotinas.Pessoa.fantasia);
      qryEmpresaENDERECO.Value := UpperCase(dmrotinas.Pessoa.Logradouro);
      qryEmpresaNUMERO.Value := UpperCase(dmrotinas.Pessoa.numero);
      qryEmpresaBAIRRO.Value := UpperCase(dmrotinas.Pessoa.Bairro);
      qryEmpresaCIDADE.Value := UpperCase(dmrotinas.Pessoa.Municipio);
      qryEmpresaUF.Value := UpperCase(dmrotinas.Pessoa.UF);
      qryEmpresaCEP.Value := UpperCase(TiraPontos(dmrotinas.Pessoa.cep));
      qryEmpresaEMAIL.Value := UpperCase(dmrotinas.Pessoa.email);
      qryEmpresaID_CIDADE.Value := dados.BuscaCodigoIbge(qryEmpresaCIDADE.Value,
        qryEmpresaUF.Value);
    except
      on E: Exception do
        raise Exception.Create(E.Message);
    end;
  end
  else
    ShowMessage('N�o � possivel buscar informa��es de pessoas f�sicas!');
end;

procedure TfrmEmpresa.BitBtn2Click(Sender: TObject);
begin
  try
    ACBrCEP1.BuscarPorCEP(DBEdit5.Text);
  except
    On E: Exception do
    begin
      ShowMessage(E.Message);
    end;
  end;
end;

procedure TfrmEmpresa.btnCancelarClick(Sender: TObject);
begin
  if qryEmpresa.State in [dsInsert, dsEdit] then
    qryEmpresa.Cancel;

  close;
end;

procedure TfrmEmpresa.TentarRegistrarEmpresaNaAPI;
var
  LManager: TEmpresaLicencaManager;
  LCNPJ, LNome, LFantasia, LContato, LEmail, LTelefone: string;
  LCelular, LEndereco, LNumero, LComplemento, LBairro, LCidade, LEstado, LCEP: string;
  LDebugMsg: string;
  LCaminhoLog: string;
  LLogContent: TStringList;
begin
  LDebugMsg := '';
  LLogContent := TStringList.Create;
  
  try
    // Criar arquivo de log
    LCaminhoLog := ExtractFilePath(Application.ExeName) + 'api_sync_debug.log';
    
    // Se arquivo existe, carregar conteúdo anterior
    if FileExists(LCaminhoLog) then
    try
      LLogContent.LoadFromFile(LCaminhoLog);
    except
      LLogContent.Clear;
    end;
    
    // Adicionar cabeçalho
    LLogContent.Add('=== SINCRONIZAÇÃO API - ' + FormatDateTime('dd/mm/yyyy hh:mm:ss', Now) + ' ===');
    
    try
      // DEBUG: Verificar se query está vazia
      if qryEmpresa.IsEmpty then
      begin
        LLogContent.Add('ERRO A1: qryEmpresa.IsEmpty = True');
        LLogContent.SaveToFile(LCaminhoLog);
        ShowMessage('DEBUG A1: qryEmpresa.IsEmpty = True');
        Exit;
      end;

      LDebugMsg := LDebugMsg + 'A1: Query carregada' + sLineBreak;
      LLogContent.Add('A1: Query carregada');

      // Criar instância do gerenciador
      LManager := TEmpresaLicencaManager.Create(nil);
      try
        // Obter CNPJ
        LCNPJ := qryEmpresaCNPJ.AsString;
        LDebugMsg := LDebugMsg + Format('A2: CNPJ obtido = %s', [LCNPJ]) + sLineBreak;
        LLogContent.Add(Format('A2: CNPJ obtido = %s', [LCNPJ]));
        ShowMessage(LDebugMsg);

        // PASSO 1: Validar se empresa JÁ EXISTE na API usando Passport
        LDebugMsg := LDebugMsg + 'B1: Validando Passport na API...' + sLineBreak;
        LLogContent.Add('B1: Validando Passport na API...');
        if LManager.ValidarPassportEmpresa(LCNPJ, dados.nometerminal, LManager.GetMachineSerial) then
        begin
          LDebugMsg := LDebugMsg + 'B2: Empresa já existe na API - abortando' + sLineBreak;
          LLogContent.Add('B2: Empresa já existe na API - abortando');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        
        LDebugMsg := LDebugMsg + 'B3: Empresa NÃO existe na API - será registrada' + sLineBreak;
        LLogContent.Add('B3: Empresa NÃO existe na API - será registrada');
        ShowMessage(LDebugMsg);

        // PASSO 2: Empresa não existe na API - tentar registrar
        LNome := qryEmpresaRAZAO.AsString;
        LFantasia := qryEmpresaFANTASIA.AsString;
        LContato := qryEmpresaRESPONSAVEL_EMPRESA.AsString;
        // IMPORTANTE: Se EMAIL estiver vazio, usar um email padrão
        LEmail := Trim(qryEmpresaEMAIL.AsString);
        if LEmail = '' then
          LEmail := 'contato@empresa.com.br';  // Email padrão para evitar campo vazio
        LTelefone := qryEmpresaFONE.AsString;
        LCelular := '';
        if not qryEmpresaFONE.IsNull then
          LCelular := Trim(qryEmpresaFONE.AsString);
        LEndereco := qryEmpresaENDERECO.AsString;
        LNumero := qryEmpresaNUMERO.AsString;
        LComplemento := qryEmpresaCOMPLEMENTO.AsString;
        LBairro := qryEmpresaBAIRRO.AsString;
        LCidade := qryEmpresaCIDADE.AsString;
        LEstado := qryEmpresaUF.AsString;
        LCEP := qryEmpresaCEP.AsString;

        // VALIDAÇÃO DOS CAMPOS OBRIGATÓRIOS PARA A API
        LDebugMsg := LDebugMsg + '=== VALIDANDO CAMPOS OBRIGATÓRIOS ===' + sLineBreak;
        LLogContent.Add('=== VALIDANDO CAMPOS OBRIGATÓRIOS ===');
        
        // 1. CNPJ/CPF (cgc)
        if Trim(LCNPJ) = '' then
        begin
          LDebugMsg := LDebugMsg + '❌ CGC (CNPJ/CPF) está vazio!' + sLineBreak;
          LLogContent.Add('ERRO: CGC (CNPJ/CPF) está vazio!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        LDebugMsg := LDebugMsg + '✓ CGC: ' + LCNPJ + sLineBreak;
        LLogContent.Add('✓ CGC: ' + LCNPJ);
        
        // 2. Razão Social (nome)
        if Trim(LNome) = '' then
        begin
          LDebugMsg := LDebugMsg + '❌ Nome (Razão Social) está vazio!' + sLineBreak;
          LLogContent.Add('ERRO: Nome (Razão Social) está vazio!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        LDebugMsg := LDebugMsg + '✓ Nome: ' + LNome + sLineBreak;
        LLogContent.Add('✓ Nome: ' + LNome);
        
        // 3. Nome Fantasia (fantasia)
        if Trim(LFantasia) = '' then
        begin
          LDebugMsg := LDebugMsg + '❌ Fantasia está vazio!' + sLineBreak;
          LLogContent.Add('ERRO: Fantasia está vazio!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        LDebugMsg := LDebugMsg + '✓ Fantasia: ' + LFantasia + sLineBreak;
        LLogContent.Add('✓ Fantasia: ' + LFantasia);
        
        // 4. Contato (contato)
        if Trim(LContato) = '' then
        begin
          LDebugMsg := LDebugMsg + '❌ Contato (Pessoa) está vazio!' + sLineBreak;
          LLogContent.Add('ERRO: Contato (Pessoa) está vazio!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        LDebugMsg := LDebugMsg + '✓ Contato: ' + LContato + sLineBreak;
        LLogContent.Add('✓ Contato: ' + LContato);
        
        // 5. Email (email) - Já fornecemos default se vazio
        LDebugMsg := LDebugMsg + '✓ Email: ' + LEmail + sLineBreak;
        LLogContent.Add('✓ Email: ' + LEmail);
        
        // 6. Telefone (telefone)
        if Trim(LTelefone) = '' then
        begin
          LDebugMsg := LDebugMsg + '❌ Telefone está vazio!' + sLineBreak;
          LLogContent.Add('ERRO: Telefone está vazio!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        LDebugMsg := LDebugMsg + '✓ Telefone: ' + LTelefone + sLineBreak;
        LLogContent.Add('✓ Telefone: ' + LTelefone);
        
        // 7. Endereço (endereco)
        if Trim(LEndereco) = '' then
        begin
          LDebugMsg := LDebugMsg + '❌ Endereço está vazio!' + sLineBreak;
          LLogContent.Add('ERRO: Endereço está vazio!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        LDebugMsg := LDebugMsg + '✓ Endereço: ' + LEndereco + sLineBreak;
        LLogContent.Add('✓ Endereço: ' + LEndereco);
        
        // 8. Número (numero)
        if Trim(LNumero) = '' then
        begin
          LDebugMsg := LDebugMsg + '❌ Número está vazio!' + sLineBreak;
          LLogContent.Add('ERRO: Número está vazio!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        LDebugMsg := LDebugMsg + '✓ Número: ' + LNumero + sLineBreak;
        LLogContent.Add('✓ Número: ' + LNumero);
        
        // 9. Bairro (bairro)
        if Trim(LBairro) = '' then
        begin
          LDebugMsg := LDebugMsg + '❌ Bairro está vazio!' + sLineBreak;
          LLogContent.Add('ERRO: Bairro está vazio!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        LDebugMsg := LDebugMsg + '✓ Bairro: ' + LBairro + sLineBreak;
        LLogContent.Add('✓ Bairro: ' + LBairro);
        
        // 10. Cidade (cidade)
        if Trim(LCidade) = '' then
        begin
          LDebugMsg := LDebugMsg + '❌ Cidade está vazia!' + sLineBreak;
          LLogContent.Add('ERRO: Cidade está vazia!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        LDebugMsg := LDebugMsg + '✓ Cidade: ' + LCidade + sLineBreak;
        LLogContent.Add('✓ Cidade: ' + LCidade);
        
        // 11. UF/Estado (estado)
        if Trim(LEstado) = '' then
        begin
          LDebugMsg := LDebugMsg + '❌ UF (Estado) está vazio!' + sLineBreak;
          LLogContent.Add('ERRO: UF (Estado) está vazio!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        LDebugMsg := LDebugMsg + '✓ UF: ' + LEstado + sLineBreak;
        LLogContent.Add('✓ UF: ' + LEstado);
        
        // 12. CEP (cep)
        if Trim(LCEP) = '' then
        begin
          LDebugMsg := LDebugMsg + '❌ CEP está vazio!' + sLineBreak;
          LLogContent.Add('ERRO: CEP está vazio!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg);
          Exit;
        end;
        LDebugMsg := LDebugMsg + '✓ CEP: ' + LCEP + sLineBreak;
        LLogContent.Add('✓ CEP: ' + LCEP);
        
        // Campos opcionais (celular, complemento) são ignorados se vazios
        if Trim(LComplemento) <> '' then
          LDebugMsg := LDebugMsg + '✓ Complemento: ' + LComplemento + sLineBreak;
        
        if Trim(LCelular) <> '' then
          LDebugMsg := LDebugMsg + '✓ Celular: ' + LCelular + sLineBreak;
        
        LDebugMsg := LDebugMsg + sLineBreak + '=== TODOS OS CAMPOS OBRIGATÓRIOS VALIDADOS COM SUCESSO ===' + sLineBreak;
        LLogContent.Add('=== TODOS OS CAMPOS OBRIGATÓRIOS VALIDADOS COM SUCESSO ===');
        ShowMessage(LDebugMsg);

        LDebugMsg := LDebugMsg + 'C2: Chamando RegistrarEmpresaNoMySQL...' + sLineBreak;
        LLogContent.Add('C2: Chamando RegistrarEmpresaNoMySQL...');
        ShowMessage(LDebugMsg);

        // Registrar empresa na API com TODOS os dados disponíveis
        LLogContent.Add('Enviando para API com parâmetros:');
        LLogContent.Add(Format('  Nome: %s', [LNome]));
        LLogContent.Add(Format('  Fantasia: %s', [LFantasia]));
        LLogContent.Add(Format('  CNPJ: %s', [LCNPJ]));
        LLogContent.Add(Format('  Contato: %s', [LContato]));
        LLogContent.Add(Format('  Email: %s', [LEmail]));
        LLogContent.Add(Format('  Telefone: %s', [LTelefone]));
        LLogContent.Add(Format('  Endereço: %s', [LEndereco]));
        LLogContent.Add(Format('  Número: %s', [LNumero]));
        LLogContent.Add(Format('  Bairro: %s', [LBairro]));
        LLogContent.Add(Format('  Cidade: %s', [LCidade]));
        LLogContent.Add(Format('  Estado: %s', [LEstado]));
        LLogContent.Add(Format('  CEP: %s', [LCEP]));
        
        if LManager.RegistrarEmpresaNoMySQL(
          LNome,
          LFantasia,
          LCNPJ,
          LContato,
          LEmail,
          LTelefone,
          LCelular,
          LEndereco,
          LNumero,
          LComplemento,
          LBairro,
          LCidade,
          LEstado,
          LCEP
        ) then
        begin
          LDebugMsg := LDebugMsg + 'C3: RegistrarEmpresaNoMySQL retornou TRUE - SUCESSO!' + sLineBreak;
          LLogContent.Add('C3: RegistrarEmpresaNoMySQL retornou TRUE - SUCESSO!');
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg + 'Empresa sincronizada com sucesso na nuvem!');
        end
        else
        begin
          LDebugMsg := LDebugMsg + 'C3: RegistrarEmpresaNoMySQL retornou FALSE - FALHA!' + sLineBreak;
          LLogContent.Add('C3: RegistrarEmpresaNoMySQL retornou FALSE - FALHA!');
          LDebugMsg := LDebugMsg + sLineBreak + '=== INFORMAÇÕES DE DEBUG ===' + sLineBreak;
          LDebugMsg := LDebugMsg + 'Possíveis causas:' + sLineBreak;
          LDebugMsg := LDebugMsg + '1. Erro na conexão com a API' + sLineBreak;
          LDebugMsg := LDebugMsg + '2. Credenciais inválidas' + sLineBreak;
          LDebugMsg := LDebugMsg + '3. CNPJ já registrado na API' + sLineBreak;
          LDebugMsg := LDebugMsg + '4. Dados inválidos ou formatação incorreta' + sLineBreak;
          LDebugMsg := LDebugMsg + '5. Servidor da API indisponível' + sLineBreak;
          LDebugMsg := LDebugMsg + sLineBreak + '=== DADOS ENVIADOS ===' + sLineBreak;
          LDebugMsg := LDebugMsg + Format(
            'Nome: %s' + sLineBreak +
            'Fantasia: %s' + sLineBreak +
            'CNPJ: %s' + sLineBreak +
            'Contato: %s' + sLineBreak +
            'Email: %s' + sLineBreak +
            'Telefone: %s' + sLineBreak +
            'Endereço: %s' + sLineBreak +
            'Número: %s' + sLineBreak +
            'Bairro: %s' + sLineBreak +
            'Cidade: %s' + sLineBreak +
            'Estado: %s' + sLineBreak +
            'CEP: %s',
            [LNome, LFantasia, LCNPJ, LContato, LEmail, LTelefone, LEndereco, LNumero, LBairro, LCidade, LEstado, LCEP]) + sLineBreak;
          LLogContent.Add('=== DADOS ENVIADOS ===');
          LLogContent.Add(LDebugMsg);
          LLogContent.SaveToFile(LCaminhoLog);
          ShowMessage(LDebugMsg + sLineBreak + sLineBreak + 'Log salvo em: ' + LCaminhoLog);
        end;
      finally
        LManager.Free;
        LLogContent.Add('=== FIM DA SINCRONIZAÇÃO ===');
        LLogContent.SaveToFile(LCaminhoLog);
      end;
    finally
      // Arquivo salvo via TStringList
    end;
  except
    on E: Exception do
    begin
      LDebugMsg := LDebugMsg + Format('ERRO: %s | Tipo: %s', [E.Message, E.ClassName]) + sLineBreak;
      LLogContent.Add(Format('ERRO EXCEPTION: %s | Tipo: %s', [E.Message, E.ClassName]));
      LLogContent.SaveToFile(LCaminhoLog);
      ShowMessage(LDebugMsg + sLineBreak + sLineBreak + 'Log salvo em: ' + LCaminhoLog);
    end;
  end;
//finally
  LLogContent.Free;
end;

procedure TfrmEmpresa.btnGravarClick(Sender: TObject);
begin
  try
    PageControl1.ActivePageIndex := 0;
    
    // Validações básicas
    if trim(DBEdit2.Text) = '' then
    begin
      ShowMessage('Digite a Razão!');
      DBEdit2.SetFocus;
      Exit;
    end;

    if trim(DBEdit3.Text) = '' then
    begin
      ShowMessage('Digite o Nome Fantasia!');
      DBEdit3.SetFocus;
      Exit;
    end;

    if trim(DBEdit4.Text) = '' then
    begin
      ShowMessage('Digite o Endereço!');
      DBEdit4.SetFocus;
      Exit;
    end;

    if trim(DBEdit5.Text) = '' then
    begin
      ShowMessage('Digite o Número!');
      DBEdit5.SetFocus;
      Exit;
    end;

    if trim(DBEdit7.Text) = '' then
    begin
      ShowMessage('Digite o Bairro!');
      DBEdit7.SetFocus;
      Exit;
    end;

    if trim(DBEdit8.Text) = '' then
    begin
      ShowMessage('Digite a Cidade!');
      DBEdit8.SetFocus;
      Exit;
    end;

    if trim(DBEdit9.Text) = '' then
    begin
      ShowMessage('Digite o CNPJ!');
      DBEdit9.SetFocus;
      Exit;
    end;

    if trim(DBEdit11.Text) = '' then
    begin
      ShowMessage('Digite o Telefone!');
      DBEdit11.SetFocus;
      Exit;
    end;

    // Validar CPF/CNPJ
    if trim(DBEdit9.Text) <> '' then
    begin
      ACBrValidador1.Documento := DBEdit9.Text;
      
      if Pos('FÍSICA', UpperCase(DBEdit20.Text)) > 0 then
      begin
        ACBrValidador1.TipoDocto := docCPF;
        if not ACBrValidador1.Validar then
          raise Exception.Create(ACBrValidador1.MsgErro);
      end
      else
      begin
        if trim(DBEdit9.Text) <> '11111111111111' then
        begin
          ACBrValidador1.TipoDocto := docCNPJ;
          if not ACBrValidador1.Validar then
            raise Exception.Create(ACBrValidador1.MsgErro);
        end;
      end;
    end;

    // Validar IE se preenchido
    if trim(DBEdit13.Text) <> '' then
    begin
      if trim(DBEdit13.Text) <> '111111111111' then
      begin
        ACBrValidador1.TipoDocto := docInscEst;
        ACBrValidador1.Complemento := DBEdit1.Text;
        ACBrValidador1.Documento := DBEdit13.Text;
        if not ACBrValidador1.Validar then
          raise Exception.Create(ACBrValidador1.MsgErro);
      end;
    end;

    // Inserir novo registro e preencher com os valores dos Edit controls
    dados.qryEmpresa.Insert;
    dados.qryEmpresacodigo.AsString := DBEdit1.Text;
    dados.qryEmpresaCNPJ.AsString := DBEdit9.Text;
    dados.qryEmpresaRAZAO.AsString := DBEdit2.Text;
    dados.qryEmpresaFANTASIA.AsString := DBEdit3.Text;
    dados.qryEmpresaENDERECO.AsString := DBEdit4.Text;
    dados.qryEmpresaNUMERO.AsString := DBEdit5.Text;
    dados.qryEmpresaCOMPLEMENTO.AsString := DBEdit6.Text;
    dados.qryEmpresaBAIRRO.AsString := DBEdit7.Text;
    dados.qryEmpresaCIDADE.AsString := DBEdit8.Text;
    dados.qryEmpresaUF.AsString := DBEdit1.Text;
    dados.qryEmpresaCEP.AsString := DBEdit10.Text;
    dados.qryEmpresaFONE.AsString := DBEdit11.Text;
    dados.qryEmpresaEMAIL.AsString := DBEdit12.Text;
    dados.qryEmpresaIE.AsString := DBEdit13.Text;
    dados.qryEmpresaTIPO.AsString := 'JURIDICA';

    // Fazer o Post e Commit
    dados.qryEmpresa.Post;
   // dados.Conexao.CommitRetaining;
    
    // Tentar registrar na API

    
    ShowMessage('Empresa gravada com sucesso!');
    Close;
  except
    on E: Exception do
    begin
      ShowMessage('Erro ao gravar: ' + E.Message);
    end;
  end;
end;

procedure TfrmEmpresa.btnValidarPassportClick(Sender: TObject);
var
  FLicencaManager: TEmpresaLicencaManager;
  LCNPJ, LHostname, LGUID: string;
  LDebugMsg: string;
  LLogPath: string;
  LLog: TStringList;
begin
  LLog := TStringList.Create;
  try
    try
      LLogPath := ExtractFilePath(Application.ExeName) + 'teste_passport_debug.log';
      
      // Adicionar cabeçalho de teste
      LLog.Add('=== TESTE PASSPORT VALIDATION - ' + FormatDateTime('dd/mm/yyyy hh:mm:ss', Now) + ' ===');
      LLog.Add('');
      
      FLicencaManager := TEmpresaLicencaManager.Create(Self);
      try
        LCNPJ := DBEdit9.Text;//qryEmpresaCNPJ.AsString;
        LLog.Add('1. CNPJ digitado: [' + LCNPJ + ']');
        
        if LCNPJ = '' then
        begin
          LLog.Add('ERRO: CNPJ vazio!');
          LLog.SaveToFile(LLogPath);
          ShowMessage('É necessário preencher o CNPJ da empresa.');
          Exit;
        end;

        LHostname := FLicencaManager.GetHostName;
        LLog.Add('2. Hostname obtido: [' + LHostname + ']');
        
        LGUID := FLicencaManager.GetMachineGUID;
        LLog.Add('3. GUID obtido: [' + LGUID + ']');
        LLog.Add('');
        LLog.Add('=== CHAMANDO ValidarPassportEmpresa ===');

        if FLicencaManager.ValidarPassportEmpresa(LCNPJ, LHostname, LGUID) then
        begin
          LLog.Add('✅ SUCESSO: Passport validado com sucesso!');
          LLog.SaveToFile(LLogPath);
          ShowMessage('✅ Passport validado com sucesso!' + sLineBreak +
                      'CNPJ: ' + LCNPJ + sLineBreak +
                      'Hostname: ' + LHostname + sLineBreak +
                      'GUID: ' + LGUID);
        end
        else
        begin
          LDebugMsg := FLicencaManager.GetUltimoErro;
          LLog.Add('❌ FALHA: Validação de Passport falhou!');
          LLog.Add('Erro retornado: [' + LDebugMsg + ']');
          LLog.SaveToFile(LLogPath);
          ShowMessage('❌ Falha na validação de Passport:' + sLineBreak +
                      'Erro: ' + LDebugMsg + sLineBreak + sLineBreak +
                      'Log salvo em: ' + LLogPath);
        end;
      finally
        FLicencaManager.Free;
      end;
    except
      on E: Exception do
      begin
        LLog.Add('❌ EXCEPTION: ' + E.ClassName);
        LLog.Add('Mensagem: ' + E.Message);
        LLog.SaveToFile(LLogPath);
        ShowMessage('Erro ao validar Passport: ' + E.Message + sLineBreak + sLineBreak +
                    'Log salvo em: ' + LLogPath);
      end;
    end;
  finally
    LLog.Free;
  end;
end;

procedure TfrmEmpresa.btnSincronizarClick(Sender: TObject);
var
  FLicencaManager: TEmpresaLicencaManager;
  LLog: TStringList;
  LLogPath: string;
begin
  LLog := TStringList.Create;
  try
    try
      LLogPath := ExtractFilePath(Application.ExeName) + 'teste_sincronizar.log';
      
      LLog.Add('=== TESTE SINCRONIZAR LICENCA - ' + FormatDateTime('dd/mm/yyyy hh:mm:ss', Now) + ' ===');
      LLog.Add('');
      
      FLicencaManager := TEmpresaLicencaManager.Create(Self);
      try
        // Conectar evento de log
        FLicencaManager.OnLog := LicencaManagerLog;
        
        LLog.Add('1. Gerenciador de Licenca criado');
        LLog.Add('');
        LLog.Add('=== CHAMANDO SincronizarComGerenciadorLicenca ===');
        LLog.Add('');
        
        if FLicencaManager.SincronizarComGerenciadorLicenca then
        begin
          LLog.Add('SUCESSO: Sincronizacao bem-sucedida!');
          LLog.Add('Data do Sincronismo: ' + DateTimeToStr(FLicencaManager.UltimaSincronizacao));
          
          // Adicionar logs capturados
          if Assigned(FLogList) and (FLogList.Count > 0) then
          begin
            LLog.Add('');
            LLog.Add('=== DETALHES DA SINCRONIZACAO ===');
            LLog.AddStrings(FLogList);
          end;
          
          LLog.SaveToFile(LLogPath);
          ShowMessage('SUCESSO: Sincronizacao bem-sucedida!' + sLineBreak +
                      'Data: ' + DateTimeToStr(FLicencaManager.UltimaSincronizacao) + sLineBreak + sLineBreak +
                      'Log salvo em: ' + LLogPath);
        end
        else
        begin
          LLog.Add('FALHA: Sincronizacao falhou!');
          
          // Adicionar logs capturados com detalhes do erro
          if Assigned(FLogList) and (FLogList.Count > 0) then
          begin
            LLog.Add('');
            LLog.Add('=== DETALHES DO ERRO ===');
            LLog.AddStrings(FLogList);
          end;
          
          LLog.SaveToFile(LLogPath);
          ShowMessage('FALHA: Sincronizacao falhou!' + sLineBreak + sLineBreak +
                      'Possiveis motivos:' + sLineBreak +
                      '- Sem conexao com a internet' + sLineBreak +
                      '- API ADMCloud indisponivel' + sLineBreak +
                      '- CNPJ nao cadastrado' + sLineBreak +
                      '- Credenciais invalidas' + sLineBreak + sLineBreak +
                      'Verifique a conexao com a API.' + sLineBreak +
                      'Log salvo em: ' + LLogPath);
        end;
      finally
        FLicencaManager.Free;
      end;
    except
      on E: Exception do
      begin
        LLog.Add('EXCEPTION: ' + E.ClassName);
        LLog.Add('Mensagem: ' + E.Message);
        LLog.SaveToFile(LLogPath);
        ShowMessage('Erro ao sincronizar licenca: ' + E.Message + sLineBreak + sLineBreak +
                    'Log salvo em: ' + LLogPath);
      end;
    end;
  finally
    LLog.Free;
  end;
end;

procedure TfrmEmpresa.LicencaManagerLog(Sender: TObject; const AMsg: string);
begin
  if Assigned(FLogList) then
    FLogList.Add(AMsg);
end;

procedure TfrmEmpresa.btnValidarLicencaClick(Sender: TObject);
var
  FLicencaManager: TEmpresaLicencaManager;
  LLog: TStringList;
  LLogPath: string;
  LMensagemErro: string;
  i: Integer;
  LCNPJAtual: string;
  LErroAtual: string;
begin
  FLogList := TStringList.Create;
  LLog := TStringList.Create;
  try
    try
      LLogPath := ExtractFilePath(Application.ExeName) + 'teste_licenca_debug.log';
      LMensagemErro := '';
      
      LLog.Add('=== TESTE VALIDACAO DE LICENCA - ' + FormatDateTime('dd/mm/yyyy hh:mm:ss', Now) + ' ===');
      LLog.Add('');
      
      FLicencaManager := TEmpresaLicencaManager.Create(Self);
      try
        // Conectar evento de log
        FLicencaManager.OnLog := LicencaManagerLog;
        
        LLog.Add('1. Gerenciador de Licenca criado');
        LLog.Add('');
        LLog.Add('=== CHAMANDO ValidarLicencaAtual ===');
        LLog.Add('');
        
        if FLicencaManager.ValidarLicencaAtual then
        begin
          LLog.Add('SUCESSO: Licenca valida!');
          LLog.Add('Ultima sincronizacao: ' + DateTimeToStr(FLicencaManager.UltimaSincronizacao));
          LLog.Add('GUID: ' + FLicencaManager.GetMachineGUID);
          
          // Adicionar logs capturados
          if Assigned(FLogList) and (FLogList.Count > 0) then
          begin
            LLog.Add('');
            LLog.Add('=== DETALHES DA VALIDACAO ===');
            LLog.AddStrings(FLogList);
          end;
          
          LLog.SaveToFile(LLogPath);
          ShowMessage('SUCESSO: Licenca valida!' + sLineBreak +
                      'Ultima sincronizacao: ' + DateTimeToStr(FLicencaManager.UltimaSincronizacao) + sLineBreak +
                      'GUID: ' + FLicencaManager.GetMachineGUID);
        end
        else
        begin
          LLog.Add('FALHA: Licenca invalida ou vencida!');
          
          // Extrair detalhes de erros dos logs
          LCNPJAtual := '';
          LErroAtual := '';
          
          if Assigned(FLogList) and (FLogList.Count > 0) then
          begin
            LLog.Add('');
            LLog.Add('=== DETALHES DA VALIDACAO ===');
            LLog.AddStrings(FLogList);
            
            // Processar logs para extrair CNPJs e erros
            for i := 0 to FLogList.Count - 1 do
            begin
              if Pos('Validando CNPJ:', FLogList[i]) > 0 then
              begin
                // Extrair CNPJ
                LCNPJAtual := Trim(Copy(FLogList[i], Pos('CNPJ:', FLogList[i]) + 5, 20));
                LErroAtual := '';
              end
              else if (Pos('FALHA', FLogList[i]) > 0) and (LCNPJAtual <> '') then
              begin
                // Capturar tipo de erro
                if Pos('vencida', FLogList[i]) > 0 then
                  LErroAtual := 'Licenca vencida'
                else if Pos('bloqueada', FLogList[i]) > 0 then
                  LErroAtual := 'Licenca bloqueada'
                else if Pos('terminais', FLogList[i]) > 0 then
                begin
                  LErroAtual := 'Limite de terminais excedido';
                  // Tentar extrair o valor atual/limite
                  if Pos('Em uso=', FLogList[i]) > 0 then
                    LErroAtual := LErroAtual + ' - ' + Trim(Copy(FLogList[i], Pos('Em uso=', FLogList[i]), 50));
                end
                else if Pos('serie', FLogList[i]) > 0 then
                  LErroAtual := 'Numero de serie invalido'
                else if Pos('ChecaValidade', FLogList[i]) > 0 then
                  LErroAtual := 'Erro em ChecaValidade';
                
                if LErroAtual <> '' then
                  LMensagemErro := LMensagemErro + sLineBreak + '- CNPJ ' + LCNPJAtual + ': ' + LErroAtual;
              end;
            end;
          end;
          
          LLog.SaveToFile(LLogPath);
          
          // Mostrar mensagem detalhada
          if LMensagemErro <> '' then
            ShowMessage('FALHA: Uma ou mais licencas sao invalidas!' + sLineBreak + sLineBreak +
                        'Empresas com problemas:' + LMensagemErro + sLineBreak + sLineBreak +
                        'Log completo em: ' + LLogPath)
          else
            ShowMessage('FALHA: Licenca invalida ou vencida!' + sLineBreak +
                        'Contate o administrador do sistema.' + sLineBreak + sLineBreak +
                        'Log salvo em: ' + LLogPath);
        end;
      finally
        FLicencaManager.Free;
      end;
    except
      on E: Exception do
      begin
        LLog.Add('EXCEPTION: ' + E.ClassName);
        LLog.Add('Mensagem: ' + E.Message);
        LLog.SaveToFile(LLogPath);
        ShowMessage('Erro ao validar licenca: ' + E.Message + sLineBreak + sLineBreak +
                    'Log salvo em: ' + LLogPath);
      end;
    end;
  finally
    LLog.Free;
    FLogList.Free;
  end;
end;

procedure TfrmEmpresa.btnRegistrarEmpresaClick(Sender: TObject);
var
  FLicencaManager: TEmpresaLicencaManager;
  LLog: TStringList;
  LLogPath: string;
  LBookmark: TBookmark;
  LCNPJ: string;
  LCNPJsRegistrados: Integer;
  LCNPJsFalhados: Integer;
  LCNPJsJaRegistrados: Integer;
  LMsgErros: string;
  LMsgJaRegistrados: string;
  LNome, LFantasia, LEmail, LTelefone, LEndereco, LNumero, LBairro, LCidade, LEstado, LCEP: string;
  LJaExiste: Boolean;
begin
  LLog := TStringList.Create;
  try
    try
      LLogPath := ExtractFilePath(Application.ExeName) + 'teste_registrar_empresa.log';
      
      LLog.Add('=== TESTE REGISTRAR TODAS AS EMPRESAS DA BASE - ' + FormatDateTime('dd/mm/yyyy hh:mm:ss', Now) + ' ===');
      LLog.Add('');
      
      // Garantir que a query está aberta
      if not dados.qryEmpresa.Active then
      begin
        LLog.Add('AVISO: Query nao estava ativa, abrindo...');
        dados.qryEmpresa.Open;
      end;
      
      LLog.Add('Total de registros na query: ' + IntToStr(dados.qryEmpresa.RecordCount));
      LLog.Add('');
      
      FLicencaManager := TEmpresaLicencaManager.Create(Self);
      try
        // Verificar e registrar TODOS os CNPJs da base
        LLog.Add('Verificando e registrando todas as empresas da base de dados...');
        LLog.Add('');
        
        LCNPJsRegistrados := 0;
        LCNPJsFalhados := 0;
        LCNPJsJaRegistrados := 0;
        LMsgErros := '';
        LMsgJaRegistrados := '';
        LBookmark := dados.qryEmpresa.GetBookmark;
        
        try
          dados.qryEmpresa.First;
          while not dados.qryEmpresa.Eof do
          begin
            LCNPJ := Trim(dados.qryEmpresaCNPJ.AsString);
            
            if LCNPJ <> '' then
            begin
              LLog.Add('[' + IntToStr(LCNPJsRegistrados + LCNPJsFalhados + LCNPJsJaRegistrados + 1) + '] CNPJ: ' + LCNPJ);
              
              // Obter dados do banco
              LNome := Trim(dados.qryEmpresaRAZAO.AsString);
              LFantasia := Trim(dados.qryEmpresaFANTASIA.AsString);
              LEmail := Trim(dados.qryEmpresaEMAIL.AsString);
              LTelefone := Trim(dados.qryEmpresaFONE.AsString);
              LEndereco := Trim(dados.qryEmpresaENDERECO.AsString);
              LNumero := Trim(dados.qryEmpresaNUMERO.AsString);
              LBairro := Trim(dados.qryEmpresaBAIRRO.AsString);
              LCidade := Trim(dados.qryEmpresaCIDADE.AsString);
              LEstado := Trim(dados.qryEmpresaUF.AsString);
              LCEP := Trim(dados.qryEmpresaCEP.AsString);
              
              LLog.Add('    Nome: ' + LNome);
              LLog.Add('    Email: ' + LEmail);
              
              // PASSO 1: VERIFICAR SE JÁ ESTÁ REGISTRADO NA API
              LLog.Add('    [VERIFICANDO] Testando se CNPJ já existe na API...');
              LJaExiste := FLicencaManager.VerificarCNPJNaAPI(LCNPJ);
              LLog.Add('    [RESULTADO] VerificarCNPJNaAPI retornou: ' + IfThen(LJaExiste, 'TRUE (EXISTE)', 'FALSE (NÃO EXISTE)'));
              LLog.Add('    [DEBUG-INFO] ' + FLicencaManager.GetDebugInfo);
              
              // PASSO 2: SE NÃO EXISTE, REGISTRAR NA API
              // ----------------------------------------- 
              if LJaExiste then
              begin
                Inc(LCNPJsJaRegistrados);
                LLog.Add('    [OK] CNPJ JÁ ESTÁ REGISTRADO NA API');
                LMsgJaRegistrados := LMsgJaRegistrados + sLineBreak + '- CNPJ ' + LCNPJ + ': JÁ REGISTRADO';
              end
              else
              begin
                // CNPJ NÃO EXISTE - TENTAR REGISTRAR
                LLog.Add('    [NOVO] CNPJ não encontrado na API - Tentando registrar automaticamente...');
                LLog.Add('');
                
                if FLicencaManager.RegistrarEmpresaNoMySQL(
                  LNome,                                          // Nome
                  LFantasia,                                      // Fantasia
                  dados.qryEmpresaCNPJ.AsString,                 // CNPJ (de dados.qryEmpresa)
                  IfThen(LNome <> '', Copy(LNome, 1, 50), 'Administrativo'),  // Contato
                  LEmail,                                         // Email
                  LTelefone,                                      // Telefone
                  dados.qryEmpresaFONE.AsString,                 // Celular (telefone de dados.qryEmpresa)
                  LEndereco,                                      // Endereco
                  LNumero,                                        // Numero
                  '',                                             // Complemento (opcional)
                  LBairro,                                        // Bairro
                  LCidade,                                        // Cidade
                  LEstado,                                        // Estado
                  LCEP                                            // CEP
                ) then
                begin
                  Inc(LCNPJsRegistrados);
                  LLog.Add('    [SUCESSO] CNPJ registrado com sucesso na API!');
                  LMsgJaRegistrados := LMsgJaRegistrados + sLineBreak + '- CNPJ ' + LCNPJ + ': REGISTRADO COM SUCESSO';
                end
                else
                begin
                  Inc(LCNPJsFalhados);
                  LLog.Add('    [ERRO] Falha ao registrar CNPJ na API');
                  LLog.Add('    Erro: ' + FLicencaManager.GetUltimoErro);
                  LMsgErros := LMsgErros + sLineBreak + '- CNPJ ' + LCNPJ + ': ERRO AO REGISTRAR';
                end;
              end;
              
              LLog.Add('');
            end
            else
            begin
              LLog.Add('  AVISO: CNPJ vazio, pulando registro');
            end;
            
            dados.qryEmpresa.Next;
          end;
        finally
          if LBookmark <> nil then
            dados.qryEmpresa.GotoBookmark(LBookmark);
        end;
        
        LLog.Add('===== RESUMO FINAL - REGISTRO DE EMPRESAS =====');
        LLog.Add('Total de CNPJs processados: ' + IntToStr(LCNPJsRegistrados + LCNPJsFalhados + LCNPJsJaRegistrados));
        LLog.Add('CNPJs já registrados na API: ' + IntToStr(LCNPJsJaRegistrados));
        LLog.Add('CNPJs registrados nesta execução: ' + IntToStr(LCNPJsRegistrados));
        LLog.Add('CNPJs com erro: ' + IntToStr(LCNPJsFalhados));
        
        LLog.SaveToFile(LLogPath);
        
        // Mostrar resultado
        if LCNPJsFalhados = 0 then
        begin
          ShowMessage('✓ SUCESSO: Todos os CNPJs foram encontrados na API!' + sLineBreak +
                      'Total encontrado: ' + IntToStr(LCNPJsJaRegistrados) + sLineBreak + sLineBreak +
                      'Log salvo em: ' + LLogPath);
        end
        else if LCNPJsJaRegistrados = 0 then
        begin
          ShowMessage('✗ NENHUM encontrado: Nenhum CNPJ foi encontrado na API!' + sLineBreak +
                      'Total NÃO encontrado: ' + IntToStr(LCNPJsFalhados) + sLineBreak + sLineBreak +
                      'Log salvo em: ' + LLogPath);
        end
        else
        begin
          ShowMessage('⚠ RESULTADO MISTO: Alguns encontrados, alguns não!' + sLineBreak + sLineBreak +
                      'Encontrados: ' + IntToStr(LCNPJsJaRegistrados) + sLineBreak +
                      'NÃO encontrados: ' + IntToStr(LCNPJsFalhados) + sLineBreak + sLineBreak +
                      'Detalhes: ' + LMsgErros + sLineBreak + sLineBreak +
                      'Log completo em: ' + LLogPath);
        end;
        
      finally
        FLicencaManager.Free;
      end;
    except
      on E: Exception do
      begin
        LLog.Add('EXCEPTION: ' + E.ClassName);
        LLog.Add('Mensagem: ' + E.Message);
        LLog.Add('Chamada de pilha: ' + E.StackTrace);
        LLog.SaveToFile(LLogPath);
        ShowMessage('Erro ao registrar empresas: ' + E.Message + sLineBreak + sLineBreak +
                    'Log salvo em: ' + LLogPath);
      end;
    end;
  finally
    LLog.Free;
  end;
end;

end.
