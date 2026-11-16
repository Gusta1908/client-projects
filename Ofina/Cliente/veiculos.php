<?php

require_once __DIR__ . "/_config.php";
require_once __DIR__ . "/_layout.php";

$usuarioId = (int) $_SESSION["id"];
$mensagem = "";
$sucesso = "";
$editarId = filter_input(INPUT_GET, "editar", FILTER_VALIDATE_INT) ?: 0;
$dadosEdicao = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validar_csrf();
    $veiculoId = (int) ($_POST["veiculo_id"] ?? 0);
    $marca = trim((string) ($_POST["marca"] ?? ""));
    $modelo = trim((string) ($_POST["modelo"] ?? ""));
    $ano = filter_var($_POST["ano"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1900, "max_range" => 2100]]);
    $placa = strtoupper(preg_replace("/[^A-Z0-9]/i", "", (string) ($_POST["placa"] ?? "")));
    $cor = trim((string) ($_POST["cor"] ?? ""));
    $quilometragem = filter_var($_POST["quilometragem"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 9999999]]);
    $combustivel = trim((string) ($_POST["combustivel"] ?? ""));
    if ($marca === "" || mb_strlen($marca) > 50 || $modelo === "" || mb_strlen($modelo) > 100 || !preg_match("/^[A-Z0-9]{4,10}$/", $placa)) {
        $mensagem = "Informe marca, modelo e uma placa válida.";
    } else {
        $duplicada = $conexao->prepare("SELECT id FROM veiculos WHERE placa = ? AND id <> ? LIMIT 1");
        $duplicada->execute([$placa, $veiculoId]);
        if ($duplicada->fetch()) {
            $mensagem = "Esta placa já está cadastrada.";
        } elseif ($veiculoId > 0) {
            $comando = $conexao->prepare("UPDATE veiculos SET marca = ?, modelo = ?, ano = ?, placa = ?, cor = ?, quilometragem = ?, combustivel = ? WHERE id = ? AND usuario_id = ?");
            $comando->execute([$marca, $modelo, $ano ?: null, $placa, $cor !== "" ? $cor : null, $quilometragem === false ? null : $quilometragem, $combustivel !== "" ? $combustivel : null, $veiculoId, $usuarioId]);
            $sucesso = "Veículo atualizado com sucesso.";
        } else {
            $comando = $conexao->prepare("INSERT INTO veiculos (usuario_id, marca, modelo, ano, placa, cor, quilometragem, combustivel) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $comando->execute([$usuarioId, $marca, $modelo, $ano ?: null, $placa, $cor !== "" ? $cor : null, $quilometragem === false ? null : $quilometragem, $combustivel !== "" ? $combustivel : null]);
            $sucesso = "Veículo cadastrado com sucesso.";
        }
    }
}

if ($editarId > 0) {
    $consulta = $conexao->prepare("SELECT * FROM veiculos WHERE id = ? AND usuario_id = ?");
    $consulta->execute([$editarId, $usuarioId]);
    $dadosEdicao = $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
}
$consulta = $conexao->prepare("SELECT * FROM veiculos WHERE usuario_id = ? ORDER BY id DESC");
$consulta->execute([$usuarioId]);
$veiculos = $consulta->fetchAll(PDO::FETCH_ASSOC);

cliente_cabecalho("Meus veículos");
?>
<main class="cliente-content">
    <h2>Meus veículos</h2>
    <p class="cliente-muted">Cadastre e acompanhe apenas os veículos da sua conta.</p>
    <?php if ($mensagem !== ""): ?><p class="erro" style="margin-top:20px"><?= escapar($mensagem) ?></p><?php endif; ?>
    <?php if ($sucesso !== ""): ?><p class="sucesso" style="margin-top:20px"><?= escapar($sucesso) ?></p><?php endif; ?>
    <div class="cliente-grid">
        <section class="cliente-card">
            <h3><?= $dadosEdicao ? "Editar veículo" : "Cadastrar veículo" ?></h3>
            <form method="POST">
                <input type="hidden" name="csrf" value="<?= token_csrf() ?>">
                <input type="hidden" name="veiculo_id" value="<?= (int) ($dadosEdicao["id"] ?? 0) ?>">
                <label>Marca<input name="marca" maxlength="50" value="<?= escapar($dadosEdicao["marca"] ?? "") ?>" required></label>
                <label>Modelo<input name="modelo" maxlength="100" value="<?= escapar($dadosEdicao["modelo"] ?? "") ?>" required></label>
                <label>Ano<input type="number" name="ano" min="1900" max="2100" value="<?= escapar((string) ($dadosEdicao["ano"] ?? "")) ?>"></label>
                <label>Placa<input name="placa" maxlength="10" value="<?= escapar($dadosEdicao["placa"] ?? "") ?>" required></label>
                <label>Cor<input name="cor" maxlength="50" value="<?= escapar($dadosEdicao["cor"] ?? "") ?>"></label>
                <label>Quilometragem<input type="number" name="quilometragem" min="0" max="9999999" value="<?= escapar((string) ($dadosEdicao["quilometragem"] ?? "")) ?>"></label>
                <label>Combustível<input name="combustivel" maxlength="20" value="<?= escapar($dadosEdicao["combustivel"] ?? "") ?>"></label>
                <button type="submit">Salvar veículo</button>
            </form>
        </section>
        <section>
            <?php foreach ($veiculos as $veiculo): ?>
                <article class="cliente-card" style="margin-bottom:18px">
                    <h3><?= escapar($veiculo["marca"] . " " . $veiculo["modelo"]) ?></h3>
                    <p>Placa: <?= escapar($veiculo["placa"]) ?></p>
                    <p>Ano: <?= escapar((string) ($veiculo["ano"] ?? "Não informado")) ?></p>
                    <p>Quilometragem: <?= escapar((string) ($veiculo["quilometragem"] ?? "Não informada")) ?></p>
                    <div class="cliente-actions"><a href="veiculos.php?editar=<?= (int) $veiculo["id"] ?>">Editar</a></div>
                </article>
            <?php endforeach; ?>
            <?php if (!$veiculos): ?><p class="cliente-muted">Nenhum veículo cadastrado.</p><?php endif; ?>
        </section>
    </div>
</main>
<?php cliente_rodape(); ?>