<?php

declare(strict_types=1);

require_once __DIR__ . "/includes/conexao.php";

function voltarComErro(string $mensagem): never
{
    header("Location: NovoOrçamento.html?erro=" . rawurlencode($mensagem));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: NovoOrçamento.html");
    exit;
}

$nome = trim((string) ($_POST["nome"] ?? ""));
$telefone = trim((string) ($_POST["telefone"] ?? ""));
$placa = strtoupper(trim((string) ($_POST["placa"] ?? "")));
$placa = preg_replace("/[^A-Z0-9]/i", "", $placa);
$marcaModelo = trim((string) ($_POST["marca_modelo"] ?? ""));
$cor = trim((string) ($_POST["cor"] ?? ""));
$combustivel = trim((string) ($_POST["combustivel"] ?? ""));
$prioridade = trim((string) ($_POST["prioridade"] ?? "normal"));
$problema = trim((string) ($_POST["problema"] ?? ""));
$responsavel = trim((string) ($_POST["responsavel"] ?? ""));
$observacoes = trim((string) ($_POST["observacoes"] ?? ""));
$orcamentoAnterior = (int) ($_POST["orcamento_anterior"] ?? 0) === 1 ? 1 : 0;
$ano = filter_input(INPUT_POST, "ano", FILTER_VALIDATE_INT, ["options" => ["min_range" => 1900, "max_range" => 2100]]);
$quilometragem = filter_input(INPUT_POST, "quilometragem", FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 9999999]]);
$previsaoEntrega = trim((string) ($_POST["previsao_entrega"] ?? ""));

if ($nome === "" || mb_strlen($nome) > 100) {
    voltarComErro("Informe um nome de cliente válido.");
}

if ($placa === "" || !preg_match("/^[A-Z0-9]{4,10}$/", $placa)) {
    voltarComErro("Informe uma placa válida.");
}

if ($marcaModelo === "" || mb_strlen($marcaModelo) > 150) {
    voltarComErro("Informe a marca e o modelo do veículo.");
}

if ($problema === "" || mb_strlen($problema) > 5000) {
    voltarComErro("Descreva o problema relatado.");
}

if (!in_array($prioridade, ["normal", "urgente", "muito_urgente"], true)) {
    voltarComErro("Prioridade inválida.");
}

if ($previsaoEntrega !== "") {
    $data = DateTime::createFromFormat("Y-m-d", $previsaoEntrega);
    if (!$data || $data->format("Y-m-d") !== $previsaoEntrega) {
        voltarComErro("A previsão de entrega é inválida.");
    }
    $previsaoEntrega .= " 18:00:00";
} else {
    $previsaoEntrega = null;
}

$arquivoSalvo = null;
if (isset($_FILES["anexo"]) && $_FILES["anexo"]["error"] !== UPLOAD_ERR_NO_FILE) {
    $arquivo = $_FILES["anexo"];
    if ($arquivo["error"] !== UPLOAD_ERR_OK || $arquivo["size"] > 10 * 1024 * 1024) {
        voltarComErro("O anexo deve ter no máximo 10MB.");
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo["tmp_name"]);
    $tiposPermitidos = [
        "application/pdf" => "pdf",
        "image/jpeg" => "jpg",
        "image/png" => "png",
    ];
    if (!isset($tiposPermitidos[$mime])) {
        voltarComErro("Formato de anexo inválido. Use PDF, JPG ou PNG.");
    }

    $diretorioUpload = __DIR__ . DIRECTORY_SEPARATOR . "uploads";
    if (!is_dir($diretorioUpload) && !mkdir($diretorioUpload, 0750, true) && !is_dir($diretorioUpload)) {
        voltarComErro("Não foi possível preparar o diretório de anexos.");
    }

    $arquivoSalvo = bin2hex(random_bytes(16)) . "." . $tiposPermitidos[$mime];
    if (!move_uploaded_file($arquivo["tmp_name"], $diretorioUpload . DIRECTORY_SEPARATOR . $arquivoSalvo)) {
        voltarComErro("Não foi possível salvar o anexo.");
    }
}

$usuarioId = $_SESSION["id"] ?? null;
$transacaoIniciada = false;

try {
    $conexao->beginTransaction();
    $transacaoIniciada = true;

    if (!$usuarioId || (($_SESSION["tipo"] ?? "") !== "cliente")) {
        $email = "cliente+" . bin2hex(random_bytes(8)) . "@local.ofina";
        $senhaTemporaria = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $comando = $conexao->prepare(
            "INSERT INTO usuarios (nome, email, senha, tipo, telefone) VALUES (?, ?, ?, 'cliente', ?)"
        );
        $comando->execute([$nome, $email, $senhaTemporaria, $telefone !== "" ? $telefone : null]);
        $usuarioId = (int) $conexao->lastInsertId();
    } else {
        $comando = $conexao->prepare("UPDATE usuarios SET nome = ?, telefone = ? WHERE id = ? AND tipo = 'cliente'");
        $comando->execute([$nome, $telefone !== "" ? $telefone : null, $usuarioId]);
    }

    $partesVeiculo = preg_split("/\s+/", $marcaModelo, 2);
    $marca = trim($partesVeiculo[0] ?? "");
    $modelo = trim($partesVeiculo[1] ?? $marca);
    $comando = $conexao->prepare(
        "INSERT INTO veiculos (usuario_id, marca, modelo, ano, placa, cor, quilometragem, combustivel)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $comando->execute([
        $usuarioId,
        mb_substr($marca, 0, 50),
        mb_substr($modelo, 0, 100),
        $ano ?: null,
        $placa,
        $cor !== "" ? mb_substr($cor, 0, 50) : null,
        $quilometragem === false ? null : $quilometragem,
        $combustivel !== "" ? mb_substr($combustivel, 0, 20) : null,
    ]);
    $veiculoId = (int) $conexao->lastInsertId();

    $comando = $conexao->prepare(
        "INSERT INTO servicos
            (veiculo_id, descricao, status, data_entrada, previsao_entrega, observacoes, prioridade, orcamento_anterior, anexo, responsavel)
         VALUES (?, ?, 'recebido', NOW(), ?, ?, ?, ?, ?, ?)"
    );
    $comando->execute([
        $veiculoId,
        $problema,
        $previsaoEntrega,
        $observacoes !== "" ? $observacoes : null,
        $prioridade,
        $orcamentoAnterior,
        $arquivoSalvo,
        $responsavel !== "" && $responsavel !== "Selecione o mecânico" ? $responsavel : null,
    ]);
    $servicoId = (int) $conexao->lastInsertId();

    $comando = $conexao->prepare(
        "INSERT INTO orcamentos (servico_id, valor, status, observacoes) VALUES (?, 0, 'pendente', ?)"
    );
    $comando->execute([$servicoId, $observacoes !== "" ? $observacoes : null]);

    $comando = $conexao->prepare(
        "INSERT INTO historico_servicos (servico_id, status, descricao) VALUES (?, 'recebido', 'Orçamento recebido')"
    );
    $comando->execute([$servicoId]);

    $conexao->commit();
    header("Location: index.php?sucesso=1");
    exit;
} catch (Throwable $erro) {
    if ($transacaoIniciada && $conexao->inTransaction()) {
        $conexao->rollBack();
    }
    if ($arquivoSalvo) {
        @unlink(__DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $arquivoSalvo);
    }
    error_log("falha cadastro: " . $erro->getMessage());
    voltarComErro("Não foi possível salvar o orçamento. Verifique os dados e tente novamente.");
}
