<?php

require_once __DIR__ . "/_config.php";
require_once __DIR__ . "/_layout.php";

$usuarioId = (int) $_SESSION["id"];
$mensagem = "";
$sucesso = "";
$consulta = $conexao->prepare("SELECT id, nome, cpf, email, telefone FROM usuarios WHERE id = ? AND tipo = 'cliente'");
$consulta->execute([$usuarioId]);
$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validar_csrf();
    $nome = trim((string) ($_POST["nome"] ?? ""));
    $cpf = preg_replace("/\D+", "", (string) ($_POST["cpf"] ?? ""));
    $email = strtolower(trim((string) ($_POST["email"] ?? "")));
    $telefone = trim((string) ($_POST["telefone"] ?? ""));
    if ($nome === "" || mb_strlen($nome) > 100 || strlen($cpf) !== 11 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Confira nome, CPF e e-mail.";
    } elseif (mb_strlen($telefone) > 20) {
        $mensagem = "Informe um telefone válido.";
    } else {
        $duplicado = $conexao->prepare("SELECT id FROM usuarios WHERE (email = ? OR cpf = ?) AND id <> ? LIMIT 1");
        $duplicado->execute([$email, $cpf, $usuarioId]);
        if ($duplicado->fetch()) {
            $mensagem = "O e-mail ou CPF já pertence a outro cadastro.";
        } else {
            $comando = $conexao->prepare("UPDATE usuarios SET nome = ?, cpf = ?, email = ?, telefone = ? WHERE id = ? AND tipo = 'cliente'");
            $comando->execute([$nome, $cpf, $email, $telefone !== "" ? $telefone : null, $usuarioId]);
            $_SESSION["nome"] = $nome;
            $sucesso = "Dados atualizados com sucesso.";
            $usuario = ["nome" => $nome, "cpf" => $cpf, "email" => $email, "telefone" => $telefone];
        }
    }
}

cliente_cabecalho("Meus dados");
?>
<main class="cliente-content">
    <h2>Meus dados</h2>
    <p class="cliente-muted">Mantenha seus dados de contato atualizados.</p>
    <section class="cliente-card" style="max-width: 680px; margin-top: 28px">
        <?php if ($mensagem !== ""): ?><p class="erro"><?= escapar($mensagem) ?></p><?php endif; ?>
        <?php if ($sucesso !== ""): ?><p class="sucesso"><?= escapar($sucesso) ?></p><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= token_csrf() ?>">
            <label>Nome completo<input type="text" name="nome" maxlength="100" value="<?= escapar($usuario["nome"] ?? "") ?>" required></label>
            <label>CPF<input type="text" name="cpf" maxlength="14" value="<?= escapar($usuario["cpf"] ?? "") ?>" required></label>
            <label>E-mail<input type="email" name="email" maxlength="150" value="<?= escapar($usuario["email"] ?? "") ?>" required></label>
            <label>Telefone<input type="tel" name="telefone" maxlength="20" value="<?= escapar($usuario["telefone"] ?? "") ?>"></label>
            <button type="submit">Salvar alterações</button>
        </form>
    </section>
</main>
<?php cliente_rodape(); ?>