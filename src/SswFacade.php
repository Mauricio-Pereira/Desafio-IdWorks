<?php



require_once __DIR__ . '/SswApiClient.php';

class SswFacade
{
    private $client;

    public function __construct($domain, $username, $password, $cnpjEdi, $apiTokenUrl, $apiNotfisUrl)
    {
        $this->client = new SswApiClient(
            $domain,
            $username,
            $password,
            $cnpjEdi,
            $apiTokenUrl,
            $apiNotfisUrl
        );
    }

    public function gerarToken()
    {
        return $this->client->gerarToken();
    }

    public function enviarNotas(array $payload)
    {
        return $this->client->enviarNotasFiscais($payload);
    }

}

