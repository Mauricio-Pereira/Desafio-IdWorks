# Projeto Exemplo de Integração com API SSW

Este projeto demonstra como enviar Notas Fiscais para a **API SSW** de forma organizada, utilizando:
- **Facades** e **Clients** para lidar com as requisições HTTP (cURL);
- **Funções de validação** de payload (dados obrigatórios) antes do envio.

## Estrutura de Pastas

```
meu_projeto/
├─ config/
│  └─ config.php             # Credenciais e URLs de configuração
├─ src/
│  ├─ Validation/
│  │   └─ NotfisValidator.php # Funções de validação de payload
│  ├─ SswApiClient.php       # Classe que faz as requisições HTTP
│  └─ SswFacade.php          # Facade que expõe métodos gerarToken e enviarNotas
└─ index.php                 # Exemplo de uso (script principal)
```

## Descrição dos Arquivos

- **config/config.php**  
  Contém definições como `DOMAIN`, `USERNAME`, `PASSWORD`, `CNPJ_EDI`, `API_TOKEN_URL` e `API_NOTFIS_URL`.  
  Exemplo:
  ```php
  <?php
  define('DOMAIN', 'TES');
  define('USERNAME', 'usuario_valido');
  define('PASSWORD', 'senha_valida');
  define('CNPJ_EDI', '12345678910123');
  define('API_TOKEN_URL', 'https://ssw.inf.br/api/generateToken');
  define('API_NOTFIS_URL', 'https://ssw.inf.br/api/notfis');
  ```
- **src/Validation/NotaFiscalValidator.php**  
  Contém as funções de validação, como `validarPayloadNotFis($payload)` e `validarEndereco($endereco, $contexto)`.  
  Esses métodos verificam se os campos obrigatórios (remetente, destinatário, NF etc.) estão preenchidos.

- **src/SswApiClient.php**  
  Classe que efetua as chamadas HTTP usando cURL:
    1. **gerarToken()**: envia as credenciais para `/generateToken`, obtendo o token.
    2. **enviarNotasFiscais(array $dados)**: faz POST no `/notfis` enviando JSON e o token via header `Authorization`.
    3. **sendCurl(...)**: método privado que configura e executa o cURL, tratando respostas, status HTTP e erros.

- **src/SswFacade.php**  
  Classe que **encapsula** (`Facade`) as operações da SSW. Exponibiliza métodos mais simples:
    - **gerarToken()**
    - **enviarNotas(array $payload)**  
      Internamente, delega para o `SswApiClient`. A ideia é que o resto do sistema só conheça a **Facade**, sem detalhes da implementação interna (cURL, endpoint etc.).

- **index.php**  
  Script de exemplo que:
    1. Carrega as configurações (`config.php`), as funções de validação, a Facade etc.
    2. Monta um **payload** de teste (ex.: `$payloadLote`).
    3. Chama `gerarToken()`.
    4. Chama `validarPayloadNotFis($payloadLote)` para garantir que os campos obrigatórios estejam corretos.
    5. Envia as notas usando `$sswFacade->enviarNotas($payloadLote)` e exibe o retorno.
    6. Trata exceções com `try/catch` (ex.: se o token não for gerado ou o payload for inválido).

## Como Executar

1. **Clonar ou baixar o projeto**.
2. **Instalar dependências** (se houver). Neste exemplo, não usamos Composer, mas em projetos reais pode haver bibliotecas extras (como libs de log).
3. **Ajustar configurações** em `config/config.php` com seu `USERNAME`, `PASSWORD`, `DOMAIN`, etc.
4. **Executar** o `index.php` no seu servidor local (ex.: `php -S localhost:8000 index.php`) ou via CLI (`php index.php`).
5. Verifique o **output** no console ou no navegador.
6. Se houver sucesso, você verá algo como:
    ```          
   Token gerado com sucesso: 123xyztoken...
   Array
   (
       [0] => Array
           (
               [sucesso] => 1
               [mensagem] => Inclusão realizada
               [remetente] => ...
               [destinatario] => ...
               [notaFiscal] => 1111
               [pedido] => 
               [protocolo] => 98765
           )
   )
   ```
   Ou uma mensagem de erro em caso de falha (ex.: "Falha ao gerar token...").

## Observações

- **Validação**: as funções verificam campos mínimos com `throw new Exception(...)` em caso de falha. Você pode estender essas validações para outros campos opcionais (expedidor, recebedor, etc.).
- **Try/Catch**: todo fluxo está envolvido em try/catch para capturar exceções tanto da validação quanto das requisições HTTP.
- **Organização**: a **Facade** oculta detalhes de `SswApiClient` e do cURL, expondo apenas `gerarToken()` e `enviarNotas()`.

---  
