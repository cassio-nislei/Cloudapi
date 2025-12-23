object dmPassport: TdmPassport
  OldCreateOrder = False
  Height = 303
  Width = 379
  object RESTClient: TRESTClient
    Accept = 'application/json, text/plain; q=0.9, text/html;q=0.8,'
    AcceptCharset = 'utf-8, *;q=0.8'
    BaseURL = 'https://admcloud.papion.com.br/v1/'
    ContentType = 'text/plain'
    Params = <>
    SecureProtocols = [TLS12]
    SynchronizedEvents = False
    Left = 104
    Top = 24
  end
  object reqPassport: TRESTRequest
    Client = RESTClient
    Params = <
      item
        Name = 'cgc'
        ContentType = ctTEXT_PLAIN
      end
      item
        Name = 'hostname'
        ContentType = ctTEXT_PLAIN
      end
      item
        Name = 'guid'
        ContentType = ctTEXT_PLAIN
      end
      item
        Name = 'fbx'
        ContentType = ctTEXT_PLAIN
      end
      item
        Name = 'pdv'
        ContentType = ctTEXT_PLAIN
      end>
    Resource = 'Passport'
    Response = respPassport
    SynchronizedEvents = False
    Left = 48
    Top = 96
  end
  object respPassport: TRESTResponse
    ContentType = 'application/json'
    Left = 144
    Top = 96
  end
end
