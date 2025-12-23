// Adicionar ao uEmpresa.dfm - DEFINIÇÃO DOS BOTÕES

// ===== SEÇÃO: ADICIONAR AOS COMPONENTES =====
// Adicionar ao final da lista de componentes do form:

object PanelBotoesAPI: TPanel
  Left = 0
  Top = 652
  Width = 800
  Height = 50
  Align = alBottom
  BevelOuter = bvNone
  Color = clBtnFace
  TabOrder = 20
  object btnValidarPassport: TButton
    Left = 10
    Top = 10
    Width = 150
    Height = 30
    Caption = 'Validar Passport'
    TabOrder = 0
    OnClick = btnValidarPassportClick
  end
  object btnSincronizar: TButton
    Left = 170
    Top = 10
    Width = 150
    Height = 30
    Caption = 'Sincronizar Licen'#231'a'
    TabOrder = 1
    OnClick = btnSincronizarClick
  end
  object btnValidarLicenca: TButton
    Left = 330
    Top = 10
    Width = 150
    Height = 30
    Caption = 'Validar Licen'#231'a'
    TabOrder = 2
    OnClick = btnValidarLicencaClick
  end
  object btnRegistrarEmpresa: TButton
    Left = 490
    Top = 10
    Width = 150
    Height = 30
    Caption = 'Registrar Empresa'
    TabOrder = 3
    OnClick = btnRegistrarEmpresaClick
  end
end

// ===== SE PREFERIR USAR TOOLBAR AO INVÉS DE PANEL =====
// Substitua acima por:

object ToolBarAPI: TToolBar
  Left = 0
  Top = 0
  Width = 800
  Height = 40
  AutoSize = True
  ButtonHeight = 30
  ButtonWidth = 150
  Images = ImageList1  // Opcional: se tiver ImageList
  TabOrder = 0
  object btnValidarPassport: TToolButton
    Caption = 'Validar Passport'
    ImageIndex = 0
    OnClick = btnValidarPassportClick
    Width = 150
  end
  object Separator1: TToolButton
    Width = 8
    ImageIndex = -1
  end
  object btnSincronizar: TToolButton
    Caption = 'Sincronizar Licen'#231'a'
    ImageIndex = 1
    OnClick = btnSincronizarClick
    Width = 150
  end
  object Separator2: TToolButton
    Width = 8
    ImageIndex = -1
  end
  object btnValidarLicenca: TToolButton
    Caption = 'Validar Licen'#231'a'
    ImageIndex = 2
    OnClick = btnValidarLicencaClick
    Width = 150
  end
  object Separator3: TToolButton
    Width = 8
    ImageIndex = -1
  end
  object btnRegistrarEmpresa: TToolButton
    Caption = 'Registrar Empresa'
    ImageIndex = 3
    OnClick = btnRegistrarEmpresaClick
    Width = 150
  end
end
