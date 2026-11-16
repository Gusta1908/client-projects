<?php

require_once __DIR__ . "/../includes/verificar_cliente.php";

function cliente_cabecalho(string $titulo): void
{
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= escapar($titulo) ?> | PJY autoTech</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="cliente-layout">
    <header class="cliente-header">
        <h1>PJY autoTech</h1>
        <nav class="cliente-nav">
            <a href="inicio.php">Início</a>
            <a href="dados.php">Meus dados</a>
            <a href="veiculos.php">Meus veículos</a>
            <a href="servicos.php">Meus serviços</a>
            <a href="orcamentos.php">Meus orçamentos</a>
            <a href="logout.php">Sair</a>
        </nav>
    </header>
    <?php
}

function cliente_rodape(): void
{
    echo "</body></html>";
}
