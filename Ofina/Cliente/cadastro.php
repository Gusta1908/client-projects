<?php

require_once __DIR__ . "/_config.php";

$mensagem = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim((string) ($_POST["nome"] ?? ""));
    $cpf = preg_replace("/\D+/", "", (string) ($_POST["cpf"] ?? ""));
    $email = strtolower(trim((string) ($_POST["email"] ?? "")));
    $telefone = trim((string) ($_POST["telefone"] ?? ""));
    $senha = (string) ($_POST["senha"] ?? "");
    $confirmacao = (string) ($_POST["confirmacao"] ?? "");
    validar_csrf();

    if ($nome === "" || mb_strlen($nome) > 100) {
        $mensagem = "Informe seu nome completo.";
    } elseif (strlen($cpf) !== 11) {
        $mensagem = "Informe um CPF válido com 11 números.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $mensagem = "Informe um e-mail válido.";
    } elseif (mb_strlen($telefone) > 20) {
        $mensagem = "Informe um telefone válido.";
    } elseif (strlen($senha) < 8) {
        $mensagem = "A senha deve ter pelo menos 8 caracteres.";
    } elseif ($senha !== $confirmacao) {
        $mensagem = "A confirmação de senha não confere.";
    }

    if ($mensagem === "") {
        $consulta = $conexao->prepare("SELECT id FROM usuarios WHERE email = ? OR cpf = ? LIMIT 1");
        $consulta->execute([$email, $cpf]);
        if ($consulta->fetch()) {
            $mensagem = "O e-mail ou CPF informado já está cadastrado.";
        }
    }

    if ($mensagem === "") {
        try {
            $comando = $conexao->prepare(
                "INSERT INTO usuarios (nome, cpf, email, senha, tipo, telefone)
                 VALUES (?, ?, ?, ?, 'cliente', ?)"
            );
            $comando->execute([$nome, $cpf, $email, password_hash($senha, PASSWORD_DEFAULT), $telefone !== "" ? $telefone : null]);
            $sucesso = "Cadastro realizado. Agora você já pode entrar.";
        } catch (PDOException $erro) {
            $mensagem = "Não foi possível concluir o cadastro. Verifique os dados e tente novamente.";
            error_log("falha cadastro cliente: " . $erro->getMessage());
        }
    }
}

?><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | PJY autoTech</title>
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="cliente-auth">
    <form method="POST" class="login-box cliente-form">
        <h1>⚙ PJY autoTech</h1>
        <p>Crie sua conta de cliente</p>
        <?php if ($mensagem !== ""): ?><p class="erro"><?= escapar($mensagem) ?></p><?php endif; ?>
        <?php if ($sucesso !== ""): ?><p class="sucesso"><?= escapar($sucesso) ?></p><?php endif; ?>
        <input type="hidden" name="csrf" value="<?= token_csrf() ?>">
        <label for="nome">Nome completo</label>
        <input id="nome" type="text" name="nome" maxlength="100" required>
        <label for="cpf">CPF</label>
        <input id="cpf" type="text" name="cpf" inputmode="numeric" maxlength="14" required>
        <label for="email">E-mail</label>
        <input id="email" type="email" name="email" maxlength="150" required>
        <label for="telefone">Telefone</label>
        <input id="telefone" type="tel" name="telefone" maxlength="20">
        <label for="senha">Senha</label>
        <input id="senha" type="password" name="senha" minlength="8" required>
        <label for="confirmacao">Confirmação de senha</label>
        <input id="confirmacao" type="password" name="confirmacao" minlength="8" required>
        <button type="submit">Criar cadastro</button>
        <div class="login-links"><a href="../login.php">Já tenho cadastro</a></div>
    </form>
</main>
</body>
</html>