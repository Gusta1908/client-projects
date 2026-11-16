<?php

$servidor = "localhost";
$banco = "top_automotiva";
$usuario = "root";
$senha = "";

try {
    $conexao = new PDO(
        "mysql:host=$servidor;dbname=$banco;charset=utf8",
        $usuario,
        $senha
    );
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $erro) {
    error_log("falha de conexao com banco: " . $erro->getMessage());
    http_response_code(500);
    exit("Não foi possível conectar ao banco de dados.");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}