# Documentação Swagger - API ADMCloud

## Informações Gerais

**Título:** ADMCloud - API de Ativação de Cliente  
**Versão:** 1.0.0  
**Contato:** papion@papion.com.br

A documentação completa em formato OpenAPI 3.0 está disponível no arquivo `openapi.yaml`.

## Servidor

- **Desenvolvimento:** `http://localhost/api/v1`
- **Produção:** `https://admcloud.papion.com.br/api/v1`

## Endpoints

### 1. Validar Passport de Cliente

**Endpoint:** `GET /passport`

**Descrição:**
Valida o passport de um cliente incluindo verificação de cadastro, status ativo, data de expiração, gerenciamento de licenças por dispositivo e atualização de último acesso.

**Parâmetros de Query:**

| Parâmetro | Obrigatório | Tipo   | Descrição                                   |
| --------- | ----------- | ------ | ------------------------------------------- |
| cgc       | Sim         | string | CNPJ/CPF do cliente (com ou sem formatação) |
| hostname  | Sim         | string | Nome do host/computador da máquina cliente  |
| guid      | Sim         | string | GUID único da instalação no cliente         |
| fbx       | Não         | string | Versão do FrontBox instalado                |
| pdv       | Não         | string | Versão do módulo PDV instalado              |

**Respostas:**

#### Sucesso (200 OK)

```json
{
  "Status": true,
  "Mensagem": "Passport OK!",
  "Dados": {
    "id_pessoa": 1001,
    "nome": "EMPRESA LTDA",
    "fantasia": "Minha Empresa",
    "cgc": "12345678901234",
    "email": "contato@empresa.com.br",
    "telefone": "(11) 3000-0000",
    "celular": "(11) 99999-9999",
    "contato": "João Silva",
    "endereco": "Rua das Flores",
    "numero": "123",
    "complemento": "Sala 10",
    "bairro": "Centro",
    "cidade": "São Paulo",
    "estado": "SP",
    "cep": "01310-100",
    "ativo": "S",
    "licencas": 5,
    "cont_licencas": 1,
    "periodo": 30,
    "expira_em": "2025-01-15",
    "data_cadastro": "2024-12-15",
    "ultimo_acesso": "2024-12-17T10:30:45",
    "versao_fbx": "4.5.2",
    "versao_pdv": "1.2.3",
    "obs": ""
  }
}
```

#### Cliente Inativo

```json
{
  "Status": false,
  "Mensagem": "Sua licença expirou ou a conta está desativada. Motivo da desativação"
}
```

#### Licença Expirada

```json
{
  "Status": false,
  "Mensagem": "Sua licença expirou em 15/12/2024. Entre em contato com o revendedor."
}
```

#### Limite de Licenças Atingido

```json
{
  "Status": false,
  "Mensagem": "Limite de licenças atingido: 5."
}
```

#### Dispositivo Inválido

```json
{
  "Status": false,
  "Mensagem": "A Licença não está associada a este dispositivo."
}
```

#### Cliente Não Encontrado

```json
{
  "Status": false,
  "Mensagem": "Registro não encontrado."
}
```

---

### 2. Registrar Novo Cliente

**Endpoint:** `POST /registro`

**Descrição:**
Registra um novo cliente no sistema com os dados completos. Após o registro bem-sucedido, o cliente é ativado com 1 licença e período de 30 dias. Email é enviado aos administradores.

**Autenticação:** Requer token Bearer

**Body da Requisição:**

```json
{
  "registro": {
    "nome": "EMPRESA LTDA",
    "fantasia": "Minha Empresa",
    "cgc": "12.345.678/0001-90",
    "contato": "João Silva",
    "email": "joao@empresa.com.br",
    "telefone": "(11) 3000-0000",
    "celular": "(11) 99999-9999",
    "endereco": "Rua das Flores",
    "numero": "123",
    "complemento": "Sala 10",
    "bairro": "Centro",
    "cidade": "São Paulo",
    "estado": "SP",
    "cep": "01310-100"
  }
}
```

**Campos Obrigatórios:**

- nome (Razão Social)
- fantasia (Nome Fantasia)
- cgc (CNPJ/CPF)
- contato (Pessoa de Contato)
- email (Email para Contato)
- telefone (Telefone Comercial)
- endereco (Endereço)
- numero (Número)
- bairro (Bairro)
- cidade (Cidade)
- estado (Estado/UF)
- cep (CEP)

**Respostas:**

#### Sucesso (200 OK)

```json
{
  "status": "OK",
  "msg": "CHAVE_B_AQUI",
  "data": {
    "id_pessoa": 1001,
    "nome": "EMPRESA LTDA",
    "fantasia": "Minha Empresa",
    "cgc": "12345678001234",
    "email": "joao@empresa.com.br",
    "ativo": "S",
    "licencas": 1,
    "cont_licencas": 1,
    "periodo": 30,
    "expira_em": "2024-01-15",
    "data_install": "2023-12-15T10:30:00"
  }
}
```

#### Erro de Validação

```json
{
  "status": "ERRO",
  "msg": "Email deve ser preenchido",
  "data": {}
}
```

#### Erro ao Salvar

```json
{
  "status": "ERRO",
  "msg": "Erro ao salvar dados. Tente novamente.",
  "data": {}
}
```

---

### 3. Obter Informações de Registro

**Endpoint:** `GET /registro`

**Descrição:**
Retorna informações sobre o módulo de registro. Requer autenticação via token.

**Autenticação:** Requer token Bearer

**Resposta (200 OK):**

```json
{
  "status": "OK",
  "msg": "GET",
  "data": {}
}
```

---

## Exemplos de Uso

### cURL - Validar Passport

```bash
curl -X GET "http://localhost/api/v1/passport?cgc=12345678901234&hostname=DESKTOP-ABC123&guid=550e8400-e29b-41d4-a716-446655440000&fbx=4.5.2&pdv=1.2.3"
```

### cURL - Registrar Novo Cliente

```bash
curl -X POST "http://localhost/api/v1/registro" \
  -H "Authorization: Bearer seu_token_aqui" \
  -H "Content-Type: application/json" \
  -d '{
    "registro": {
      "nome": "EMPRESA LTDA",
      "fantasia": "Minha Empresa",
      "cgc": "12.345.678/0001-90",
      "contato": "João Silva",
      "email": "joao@empresa.com.br",
      "telefone": "(11) 3000-0000",
      "celular": "(11) 99999-9999",
      "endereco": "Rua das Flores",
      "numero": "123",
      "complemento": "Sala 10",
      "bairro": "Centro",
      "cidade": "São Paulo",
      "estado": "SP",
      "cep": "01310-100"
    }
  }'
```

### JavaScript - Registrar Novo Cliente

```javascript
const data = {
  registro: {
    nome: "EMPRESA LTDA",
    fantasia: "Minha Empresa",
    cgc: "12.345.678/0001-90",
    contato: "João Silva",
    email: "joao@empresa.com.br",
    telefone: "(11) 3000-0000",
    celular: "(11) 99999-9999",
    endereco: "Rua das Flores",
    numero: "123",
    complemento: "Sala 10",
    bairro": "Centro",
    cidade: "São Paulo",
    estado: "SP",
    cep: "01310-100"
  }
};

fetch('http://localhost/api/v1/registro', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer seu_token_aqui'
  },
  body: JSON.stringify(data)
})
.then(response => response.json())
.then(data => console.log('Sucesso:', data))
.catch(error => console.error('Erro:', error));
```

---

## Fluxo de Autenticação e Ativação

### Fluxo 1: Validação de Passport (Autenticação)

1. Cliente envia requisição GET `/passport` com cgc, hostname e guid
2. Sistema verifica se o cliente existe
3. Sistema verifica se o cliente está ativo
4. Sistema verifica se a licença não expirou
5. Sistema verifica limite de licenças
6. Se é nova licença, registra no sistema
7. Se é licença existente, verifica se hostname corresponde
8. Sistema atualiza último acesso
9. Retorna status de sucesso ou erro

### Fluxo 2: Registro de Novo Cliente

1. Cliente envia requisição POST `/registro` com dados completos
2. Sistema valida todos os campos obrigatórios
3. Sistema converte dados para objeto Pessoa
4. Sistema verifica se há erros de validação
5. Sistema cria novo cliente com status ATIVO
6. Sistema asigna 1 licença com 30 dias de validade
7. Sistema salva dados no banco
8. Sistema envia email aos administradores
9. Retorna dados do cliente e chave B

---

## Status do Cliente

- **S (Ativo):** Cliente ativo e autorizado a usar o sistema
- **N (Desativado):** Cliente desativado e não pode usar o sistema
- **B (Bloqueado):** Cliente bloqueado por algum motivo

---

## Campos de Resposta

### Passport Response

| Campo    | Tipo    | Descrição                        |
| -------- | ------- | -------------------------------- |
| Status   | boolean | Indica sucesso ou falha          |
| Mensagem | string  | Mensagem descritiva do resultado |

### Cliente Data

| Campo         | Tipo     | Descrição           |
| ------------- | -------- | ------------------- |
| id_pessoa     | integer  | ID único do cliente |
| nome          | string   | Razão Social        |
| fantasia      | string   | Nome Fantasia       |
| cgc           | string   | CNPJ/CPF            |
| email         | string   | Email               |
| ativo         | string   | Status (S/N/B)      |
| licencas      | integer  | Limite de licenças  |
| cont_licencas | integer  | Licenças utilizadas |
| periodo       | integer  | Dias de validade    |
| expira_em     | date     | Data de expiração   |
| data_install  | datetime | Data de instalação  |

---

## Configurações de Segurança

### Autenticação Atual (Basic Auth)

A autenticação atual é implementada usando **HTTP Basic Authentication** com credenciais fixas:

```
Authorization: Basic api_frontbox:api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg
```

**Em cURL:**

```bash
curl -u "api_frontbox:api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg" \
  http://localhost/api/v1/registro
```

**Em Python:**

```python
import requests
auth = ("api_frontbox", "api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg")
response = requests.get("http://localhost/api/v1/registro", auth=auth)
```

### Autenticação Recomendada (JWT - Não Implementada)

**Status:** Documentado em `relatorios/AUTENTICACAO_JWT.md`

A transição para JWT Bearer Token é recomendada para produção:

```
Authorization: Bearer seu_token_jwt_aqui
```

### Validação de Dados

- CGC é validado quanto ao formato
- Email deve ser válido
- Todos os campos obrigatórios devem ser preenchidos
- CNPJ/CPF é convertido para apenas números para comparação

---

## Códigos HTTP

- **200 OK:** Requisição bem-sucedida
- **400 Bad Request:** Dados inválidos ou faltantes
- **401 Unauthorized:** Autenticação falhou ou token inválido
- **403 Forbidden:** Acesso negado
- **404 Not Found:** Recurso não encontrado
- **500 Internal Server Error:** Erro interno do servidor

---

## Notas Importantes

1. **Formatação de CGC:** O sistema aceita CNPJ/CPF com ou sem formatação, mas internamente armazena apenas números.

2. **Datas:** Datas são armazenadas em formato ISO 8601 (YYYY-MM-DD).

3. **Email de Notificação:** Ao registrar um novo cliente, emails são enviados para:

   - papion@papion.com.br
   - ivan@is5.com.br

4. **Licenças:** Cada cliente pode ter múltiplas licenças (uma por dispositivo/hostname), respeitando o limite.

5. **Expiração:** Se a data de expiração for nula, o cliente não tem data limite de uso.

6. **Versões:** Os parâmetros fbx e pdv são opcionais mas recomendados para rastreamento.

---

## Histórico de Versões

| Versão | Data       | Alterações                                          |
| ------ | ---------- | --------------------------------------------------- |
| 1.0.0  | 09/12/2024 | Versão inicial com endpoints de Passport e Registro |
