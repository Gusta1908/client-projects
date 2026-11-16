<?php

require_once __DIR__ . "/includes/conexao.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = strtolower(trim((string) ($_POST["email"] ?? "")));
    $senha = (string) ($_POST["senha"] ?? "");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === "") {
        $mensagem = "Informe um e-mail e uma senha válidos.";
    }

    if ($mensagem === "") {
        $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";

        $comando = $conexao->prepare($sql);

        $comando->execute([$email]);

        $usuario = $comando->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario["senha"]) && in_array($usuario["tipo"], ["admin", "cliente"], true)) {
            session_regenerate_id(true);

            $_SESSION["id"] = (int) $usuario["id"];
            $_SESSION["nome"] = $usuario["nome"];
            $_SESSION["tipo"] = $usuario["tipo"];

            if ($usuario["tipo"] === "admin") {
                header("Location: dashboard.php");
            } else {
                header("Location: Cliente/inicio.php");
            }

            exit;
        }

        $mensagem = "E-mail ou senha incorretos.";
    }
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Login | PJY autoTech
    </title>

    <link
        rel="stylesheet"
        href="index.css"
    >

</head>

<body>

    <main class="login">

        <form
            method="POST"
            class="login-box"
        >

            <h1>
                ⚙ PJY autoTech
            </h1>

            <p>
                Entre na sua conta
            </p>


            <?php

            if (
                $mensagem != ""
            ) {

            ?>

                <p class="erro">

                    <?php
                    echo $mensagem;
                    ?>

                </p>

            <?php

            }

            ?>


            <label>
                E-mail
            </label>

            <input
                type="email"
                name="email"
                required
            >


            <label>
                Senha
            </label>

            <input
                type="password"
                name="senha"
                required
            >


            <button
                type="submit"
            >

                Entrar

            </button>

            <div class="login-links">
                <a href="cliente/cadastro.php">Ainda não tenho cadastro</a>
                <a href="cliente/esqueci_senha.php">Esqueci minha senha</a>
            </div>

        </form>

    </main>

</body>

</html>