<?php

require_once __DIR__ . "/../includes/conexao.php";

if (!isset($_SESSION["csrf_cliente"])) {
    $_SESSION["csrf_cliente"] = bin2hex(random_bytes(32));
}

function escapar(?string $valor): string
{
    return htmlspecialchars($valor ?? "", ENT_QUOTES, "UTF-8");
}

function token_csrf(): string
{
    return escapar($_SESSION["csrf_cliente"]);
}

function validar_csrf(): void
{
    $token = (string) ($_POST["csrf"] ?? "");
    if (!hash_equals((string) ($_SESSION["csrf_cliente"] ?? ""), $token)) {
        http_response_code(419);
        exit("Sessão expirada. Volte e tente novamente.");
    }
}

function formatar_data(?string $data): string
{
    return $data ? (new DateTime($data))->format("d/m/Y") : "Não definida";
}
