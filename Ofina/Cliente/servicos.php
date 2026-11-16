<?php

require_once __DIR__ . "/_config.php";
require_once __DIR__ . "/_layout.php";

$consulta = $conexao->prepare(
    "SELECT s.id, s.descricao, s.status, s.data_entrada, s.previsao_entrega,
            s.valor, s.observacoes, v.marca, v.modelo, v.placa
     FROM servicos s
     INNER JOIN veiculos v ON v.id = s.veiculo_id
     WHERE v.usuario_id = ?
     ORDER BY s.id DESC"
);
$consulta->execute([(int) $_SESSION["id"]]);
$servicos = $consulta->fetchAll(PDO::FETCH_ASSOC);
$status = [
    "recebido" => "Veículo recebido",
    "diagnostico" => "Análise e diagnóstico",
    "aguardando_aprovacao" => "Aguardando aprovação",
    "em_execucao" => "Serviço em execução",
    "pronto" => "Pronto para entrega",
];

cliente_cabecalho("Meus serviços");
?>
<main class="cliente-content">
    <h2>Meus serviços</h2>
    <p class="cliente-muted">Histórico dos serviços vinculados aos seus veículos.</p>
    <div class="cliente-grid">
        <?php foreach ($servicos as $servico): ?>
            <article class="cliente-card">
                <h3>#<?= (int) $servico["id"] ?> · <?= escapar(trim($servico["marca"] . " " . $servico["modelo"])) ?></h3>
                <p>Placa: <?= escapar($servico["placa"]) ?></p>
                <p>Serviço: <?= escapar($servico["descricao"]) ?></p>
                <p>Status: <?= escapar($status[$servico["status"] ?? ""] ?? "Não informado") ?></p>
                <p>Entrada: <?= formatar_data($servico["data_entrada"]) ?></p>
                <p>Previsão: <?= formatar_data($servico["previsao_entrega"]) ?></p>
                <?php if ($servico["valor"] !== null): ?><p>Valor: R$ <?= number_format((float) $servico["valor"], 2, ",", ".") ?></p><?php endif; ?>
                <?php if ($servico["observacoes"]): ?><p>Observações: <?= escapar($servico["observacoes"]) ?></p><?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
    <?php if (!$servicos): ?><p class="cliente-muted" style="margin-top:28px">Nenhum serviço encontrado.</p><?php endif; ?>
</main>
<?php cliente_rodape(); ?>