<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Validation/NotfisValidator.php';
require_once __DIR__ . '/src/SswFacade.php';



// Payload de exemplo para envio de notas fiscais
$payloadLote = [
    "lote"  => "lote_xyz",
    "dados" => [
        [
            "remetente" => [
                "cnpj" => "00000000000001",
                "nome" => "Empresa Remetente",
                "endereco" => [
                    "rua"    => "Rua Central",
                    "numero" => "100",
                    "bairro" => "Centro",
                    "cidade" => "SAO PAULO",
                    "uf"     => "SP",
                    "cep"    => 12345000
                ]
            ],
            "destinatario" => [
                [
                    "cnpj"  => "00000000000002",
                    "nome"  => "Cliente A",
                    "nf" => [
                        [
                            "condicaoFrete"   => "FOB",
                            "numero"          => 1111,
                            "dataEmissao"     => "2025-01-01",
                            "qtdeVolumes"     => 2,
                            "valorMercadoria" => 250.00,
                            "pesoReal"        => 35.5
                        ]
                    ]
                ]
            ]
        ]
    ]
];

try {
    $sswFacade = new SswFacade(
        DOMAIN,
        USERNAME,
        PASSWORD,
        CNPJ_EDI,
        API_TOKEN_URL,
        API_NOTFIS_URL
    );

    $token = $sswFacade->gerarToken();
    echo "Token gerado com sucesso: $token\n\n";

    try {
        validarPayloadNotFis($payloadLote);
        $retorno = $sswFacade->enviarNotas($payloadLote);
        print_r($retorno);

    } catch (Exception $e) {
        echo "Erro na validação ou envio: " . $e->getMessage();
    }

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}