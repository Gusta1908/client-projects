<?php

require_once __DIR__ . "/_config.php";
require_once __DIR__ . "/_layout.php";

$mensagem = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validar_csrf();
    $orcamentoId = (int) ($_POST["orcamento_id"] ?? 0);
    $novoStatus = (string) ($_POST["status"] ?? "");
    if (in_array($novoStatus, ["aprovado", "recusado"], true)) {
        $comando = $conexao->prepare(
            "UPDATE orcamentos o
             INNER JOIN servicos s ON s.id = o.servico_id
             INNER JOIN veiculos v ON v.id = s.veiculo_id
             SET o.status = ?
             WHERE o.id = ? AND v.usuario_id = ? AND o.status = 'pendente'"
        );
        $comando->execute([$novoStatus, $orcamentoId, (int) $_SESSION["id"]]);
        $mensagem = $comando->rowCount() ? "Orçamento atualizado." : "Este orçamento não está disponível para alteração.";
    }
}

$consulta = $conexao->prepare(
    "SELECT o.id, o.valor, o.status, o.observacoes, o.criado_em,
            s.descricao, v.marca, v.modelo, v.placa
     FROM orcamentos o
     INNER JOIN servicos s ON s.id = o.servico_id
     INNER JOIN veiculos v ON v.id = s.veiculo_id
     WHERE v.usuario_id = ?
     ORDER BY o.id DESC"
);
$consulta->execute([(int) $_SESSION["id"]]);
$orcamentos = $consulta->fetchAll(PDO::FETCH_ASSOC);
$status = ["pendente" => "Aguardando aprovação", "aprovado" => "Aprovado", "recusado" => "Recusado"];

cliente_cabecalho("Meus orçamentos");
?>
<main class="cliente-content">
    <h2>Meus orçamentos</h2>
    <p class="cliente-muted">Consulte os valores e responda aos orçamentos pendentes.</p>
    <?php if ($mensagem !== ""): ?><p class="sucesso" style="margin-top:20px"><?= escapar($mensagem) ?></p><?php endif; ?>
    <div class="cliente-table-wrap">
        <table class="cliente-table">
            <thead><tr><th>Número</th><th>Serviço</th><th>Veículo</th><th>Data</th><th>Valor</th><th>Status</th><th>Observações</th><th>Ação</th></tr></thead>
            <tbody>
            <?php foreach ($orcamentos as $orcamento): ?>
                <tr>
                    <td>#<?= (int) $orcamento["id"] ?></td>
                    <td><?= escapar($orcamento["descricao"]) ?></td>
                    <td><?= escapar(trim($orcamento["marca"] . " " . $orcamento["modelo"] . " / " . $orcamento["placa"])) ?></td>
                    <td><?= formatar_data($orcamento["criado_em"]) ?></td>
                    <td>R$ <?= number_format((float) $orcamento["valor"], 2, ",", ".") ?></td>
                    <td><?= escapar($status[$orcamento["status"] ?? ""] ?? "Não informado") ?></td>
                    <td><?= escapar($orcamento["observacoes"] ?? "-") ?></td>
                    <td>
                        <?php if ($orcamento["status"] === "pendente"): ?>
                            <form method="POST"><input type="hidden" name="csrf" value="<?= token_csrf() ?>"><input type="hidden" name="orcamento_id" value="<?= (int) $orcamento["id"] ?>"><button name="status" value="aprovado" type="submit">Aceitar</button> <button name="status" value="recusado" type="submit">Recusar</button></form>
                        <?php else: ?>-
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (!$orcamentos): ?><p class="cliente-muted" style="margin-top:28px">Nenhum orçamento encontrado.</p><?php endif; ?>
</main>
<?php cliente_rodape(); ?>