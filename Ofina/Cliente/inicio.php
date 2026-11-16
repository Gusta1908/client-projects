<?php

require_once __DIR__ . "/_config.php";
require_once __DIR__ . "/_layout.php";

$usuarioId = (int) $_SESSION["id"];
$consulta = $conexao->prepare(
    "SELECT v.marca, v.modelo, v.placa, s.id AS servico_id,
            s.status, s.data_entrada, s.previsao_entrega,
            o.id AS orcamento_id, o.valor, o.status AS orcamento_status
     FROM veiculos v
     LEFT JOIN servicos s ON s.veiculo_id = v.id
     LEFT JOIN orcamentos o ON o.servico_id = s.id
     WHERE v.usuario_id = ?
     ORDER BY s.id DESC, o.id DESC, v.id DESC LIMIT 1"
);
$consulta->execute([$usuarioId]);
$resumo = $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
$etapas = [
    "recebido" => ["Veículo recebido", "Seu veículo já está com a nossa equipe."],
    "diagnostico" => ["Análise e diagnóstico", "Estamos avaliando o problema e as soluções."],
    "aguardando_aprovacao" => ["Aguardando aprovação", "Aguardamos sua resposta ao orçamento."],
    "em_execucao" => ["Serviço em execução", "Nossa equipe está realizando o serviço."],
    "pronto" => ["Pronto para entrega", "Seu veículo está pronto para retirada."],
];
$statusOrcamento = [
    "pendente" => "Aguardando aprovação",
    "aprovado" => "Aprovado",
    "recusado" => "Recusado",
];
$indiceAtual = $resumo && $resumo["servico_id"]
    ? array_search($resumo["status"], array_keys($etapas), true)
    : -1;
if ($indiceAtual === false) {
    $indiceAtual = 0;
}

cliente_cabecalho("Início");
?>
<main class="cliente-content">
    <h2>Olá, <?= escapar($_SESSION["nome"]) ?></h2>
    <p class="cliente-muted">Acompanhe seus veículos, serviços e orçamentos.</p>
    <div class="cliente-grid">
        <section class="cliente-card">
            <h3><?= $resumo ? escapar(trim($resumo["marca"] . " " . $resumo["modelo"])) : "Meu veículo" ?></h3>
            <?php if ($resumo): ?>
                <p>Placa: <?= escapar($resumo["placa"]) ?></p>
                <p>Data de entrada: <?= formatar_data($resumo["data_entrada"]) ?></p>
                <p>Previsão de entrega: <?= formatar_data($resumo["previsao_entrega"]) ?></p>
            <?php else: ?>
                <p class="cliente-muted">Você ainda não possui veículos ou serviços cadastrados.</p>
                <div class="cliente-actions"><a href="veiculos.php">Cadastrar veículo</a></div>
            <?php endif; ?>
        </section>
        <section class="cliente-card">
            <h3>Status do serviço</h3>
            <?php if ($resumo && $resumo["servico_id"]): ?>
                <p><strong><?= escapar($etapas[$resumo["status"]][0] ?? "Aguardando atendimento") ?></strong></p>
                <div class="cliente-progress" aria-label="Progresso do serviço">
                    <?php foreach (array_values($etapas) as $indice => $etapa): ?>
                        <span class="<?= $indice < $indiceAtual ? "concluida" : ($indice === $indiceAtual ? "atual" : "") ?>" title="<?= escapar($etapa[0]) ?>"></span>
                    <?php endforeach; ?>
                </div>
                <div class="cliente-actions"><a href="servicos.php">Meus Serviços</a></div>
            <?php else: ?>
                <p class="cliente-muted">Nenhum serviço em andamento.</p>
            <?php endif; ?>
        </section>
        <section class="cliente-card">
            <h3>Meus orçamentos</h3>
            <?php if ($resumo && $resumo["orcamento_id"]): ?>
                <p>Orçamento #<?= (int) $resumo["orcamento_id"] ?></p>
                <p>Valor: R$ <?= number_format((float) $resumo["valor"], 2, ",", ".") ?></p>
                <p>Status: <?= escapar($statusOrcamento[$resumo["orcamento_status"]] ?? "Não informado") ?></p>
            <?php else: ?>
                <p class="cliente-muted">Você ainda não possui orçamentos.</p>
            <?php endif; ?>
            <div class="cliente-actions"><a href="orcamentos.php">Ver orçamentos</a></div>
        </section>
    </div>
    <section class="cliente-card cliente-next-steps">
        <h3>Próximos passos</h3>
        <?php foreach (array_values($etapas) as $indice => $etapa): ?>
            <div class="cliente-step <?= $indice < $indiceAtual ? "concluido" : ($indice === $indiceAtual ? "atual" : "") ?>">
                <span><?= $indice + 1 ?></span>
                <div><strong><?= escapar($etapa[0]) ?></strong><p class="cliente-muted"><?= escapar($etapa[1]) ?></p></div>
            </div>
        <?php endforeach; ?>
    </section>
</main>
<?php cliente_rodape(); ?>