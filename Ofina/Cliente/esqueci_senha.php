<?php

require_once __DIR__ . "/_config.php";

$mensagem = "Informe seu e-mail. Se houver uma conta, a orientação de recuperação será disponibilizada pela oficina.";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim((string) ($_POST["email"] ?? "")));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Informe um e-mail válido.";
    } else {
        $consulta = $conexao->prepare("SELECT id FROM usuarios WHERE email = ? AND tipo = 'cliente' LIMIT 1");
        $consulta->execute([$email]);
        $mensagem = "Se o e-mail estiver cadastrado, entre em contato com a oficina para concluir a recuperação da conta.";
    }
}

?><!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Recuperar senha | PJY autoTech</title><link rel="stylesheet" href="../index.css"><link rel="stylesheet" href="style.css"></head>
<body><main class="cliente-auth"><form method="POST" class="login-box cliente-form"><h1>Recuperar senha</h1><p><?= escapar($mensagem) ?></p><label for="email">E-mail</label><input id="email" type="email" name="email" required><button type="submit">Continuar</button><div class="login-links"><a href="../login.php">Voltar ao login</a></div></form></main></body>
</html>