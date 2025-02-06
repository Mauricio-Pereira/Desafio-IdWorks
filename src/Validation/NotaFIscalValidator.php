<?php
function validarPayloadNotFis(array $payload)
{
    if (!isset($payload['dados']) || !is_array($payload['dados']) || count($payload['dados']) === 0) {
        throw new Exception("O campo 'dados' é obrigatório e deve ser um array não vazio.");
    }

    foreach ($payload['dados'] as $indexDado => $dado) {
        if (!isset($dado['remetente']) || !is_array($dado['remetente'])) {
            throw new Exception("O campo 'remetente' é obrigatório (dados[$indexDado]).");
        }
        if (empty($dado['remetente']['cnpj'])) {
            throw new Exception("O CNPJ do remetente é obrigatório (dados[$indexDado]).");
        }
        if (empty($dado['remetente']['nome'])) {
            throw new Exception("O nome do remetente é obrigatório (dados[$indexDado]).");
        }
        if (!isset($dado['remetente']['endereco']) || !is_array($dado['remetente']['endereco'])) {
            throw new Exception("O campo 'endereco' do remetente é obrigatório (dados[$indexDado]).");
        }
        validarEndereco($dado['remetente']['endereco'], "Remetente (dados[$indexDado])");

        if (!isset($dado['destinatario']) || !is_array($dado['destinatario']) || count($dado['destinatario']) === 0) {
            throw new Exception("O campo 'destinatario' é obrigatório e deve ser um array não vazio (dados[$indexDado]).");
        }

        foreach ($dado['destinatario'] as $indexDest => $dest) {
            if (empty($dest['cnpj'])) {
                throw new Exception("O CNPJ do destinatário é obrigatório (dados[$indexDado].destinatario[$indexDest]).");
            }
            if (empty($dest['nome'])) {
                throw new Exception("O nome do destinatário é obrigatório (dados[$indexDado].destinatario[$indexDest]).");
            }
            if (!isset($dest['endereco']) || !is_array($dest['endereco'])) {
                throw new Exception("O campo 'endereco' do destinatário é obrigatório (dados[$indexDado].destinatario[$indexDest]).");
            }
            validarEndereco($dest['endereco'], "Destinatário (dados[$indexDado].destinatario[$indexDest])");

            if (!isset($dest['nf']) || !is_array($dest['nf']) || count($dest['nf']) === 0) {
                throw new Exception("É obrigatório informar ao menos uma NF (dados[$indexDado].destinatario[$indexDest]).");
            }
            foreach ($dest['nf'] as $indexNF => $nf) {
                if (empty($nf['condicaoFrete']) || !in_array($nf['condicaoFrete'], ['CIF', 'FOB'])) {
                    throw new Exception("condicaoFrete inválida ou não informada, use 'CIF' ou 'FOB' (dados[$indexDado].destinatario[$indexDest].nf[$indexNF]).");
                }
                if (!isset($nf['numero']) || !is_numeric($nf['numero'])) {
                    throw new Exception("Número da NF é obrigatório e deve ser numérico (dados[$indexDado].destinatario[$indexDest].nf[$indexNF]).");
                }
                if (!isset($nf['serie'])) {
                    throw new Exception("Série da NF é obrigatória (pode estar vazia, mas o campo deve existir) (dados[$indexDado].destinatario[$indexDest].nf[$indexNF]).");
                }
                if (empty($nf['dataEmissao'])) {
                    throw new Exception("Data de Emissão da NF é obrigatória (dados[$indexDado].destinatario[$indexDest].nf[$indexNF]).");
                }
                if (!isset($nf['qtdeVolumes']) || !is_numeric($nf['qtdeVolumes'])) {
                    throw new Exception("Quantidade de Volumes é obrigatória e deve ser numérica (dados[$indexDado].destinatario[$indexDest].nf[$indexNF]).");
                }
                if (!isset($nf['valorMercadoria']) || !is_numeric($nf['valorMercadoria'])) {
                    throw new Exception("Valor da mercadoria é obrigatório e deve ser numérico (dados[$indexDado].destinatario[$indexDest].nf[$indexNF]).");
                }
                if (!isset($nf['pesoReal']) || !is_numeric($nf['pesoReal'])) {
                    throw new Exception("Peso real é obrigatório e deve ser numérico (dados[$indexDado].destinatario[$indexDest].nf[$indexNF]).");
                }
            }
        }
    }
}

function validarEndereco(array $endereco, $contexto)
{
    if (empty($endereco['rua'])) {
        throw new Exception("O campo 'rua' é obrigatório em $contexto.");
    }
    if (!isset($endereco['numero'])) {
        throw new Exception("O campo 'numero' é obrigatório em $contexto.");
    }
    if (empty($endereco['bairro'])) {
        throw new Exception("O campo 'bairro' é obrigatório em $contexto.");
    }
    if (empty($endereco['cidade'])) {
        throw new Exception("O campo 'cidade' é obrigatório em $contexto.");
    }
    if (empty($endereco['uf'])) {
        throw new Exception("O campo 'uf' é obrigatório em $contexto.");
    }
    if (!isset($endereco['cep'])) {
        throw new Exception("O campo 'cep' é obrigatório em $contexto.");
    }
    // Se quiser checar se cep é numérico:
    if (!is_numeric($endereco['cep'])) {
        throw new Exception("O campo 'cep' deve ser numérico em $contexto.");
    }
}