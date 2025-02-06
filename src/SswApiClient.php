<?php


class SswApiClient
{
    private $domain;
    private $username;
    private $password;
    private $cnpj_edi;
    private $api_token_url;
    private $api_notfis_url;
    private $token;


    public function __construct($domain, $username, $password, $cnpj_edi, $api_token_url, $api_notfis_url)
    {
        $this->domain = $domain;
        $this->username = $username;
        $this->password = $password;
        $this->cnpj_edi = $cnpj_edi;
        $this->api_token_url = $api_token_url;
        $this->api_notfis_url = $api_notfis_url;
    }


    public function gerarToken()
    {
        $payload = [
            'domain' => $this->domain,
            'username' => $this->username,
            'password' => $this->password,
            'cnpj_edi' => $this->cnpj_edi
        ];

        $response = $this->sendRequest($this->api_token_url, $payload);

        if (!isset($response['sucess']) || $response['sucess'] !== true) {
            $msgErro = isset($response['message']) ? $response['message'] : 'Erro desconhecido ao gerar token.';
            throw new \Exception("Falha ao gerar token: " . $msgErro);
        }

        $this->token = $response['token'];

        return $this->token;
    }

    public function enviarNotasFiscais(array $dados)
    {
        $payload = [$dados];
        $response = $this->sendCurl($this->api_notfis_url, $payload, $this->token);
        return $response;
    }

    private function sendCurl($url, $payload, $token = null)
    {
        $ch = curl_init($url);

        $headers = ['Content-Type: application/json'];
        if ($token) {
            $headers[] = "Authorization: {$token}";
        }

        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,     $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($payload));

        $rawResponse = curl_exec($ch);

        if ($rawResponse === false) {
            $errorMsg = curl_error($ch);
            curl_close($ch);
            throw new \Exception("Erro ao enviar requisição: " . $errorMsg);
        }


        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseDecoded = json_decode($rawResponse, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Resposta não é JSON válido: " . $rawResponse);
        }

        if ($httpCode >= 400) {
            $msg = isset($responseDecoded['message']) ? $responseDecoded['message'] : 'Erro retornado pela API.';
            throw new \Exception("HTTP {$httpCode} - {$msg}");
        }

        return $responseDecoded;
    }
}
