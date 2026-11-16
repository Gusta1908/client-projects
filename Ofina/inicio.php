<?php
require_once __DIR__ . "/includes/verificar_login.php";

if (($_SESSION["tipo"] ?? "") === "admin") {
    header("Location: dashboard.php");
} else {
    header("Location: Cliente/inicio.php");
}
exit;
?>

<?php
$servico = $conexao->prepare(
    "SELECT v.marca, v.modelo, v.placa, s.status, s.previsao_entrega
     FROM veiculos v
     INNER JOIN servicos s ON s.veiculo_id = v.id
     WHERE v.usuario_id = ?
     ORDER BY s.id DESC LIMIT 1"
);
$servico->execute([$_SESSION["id"]]);
$servico = $servico->fetch(PDO::FETCH_ASSOC) ?: null;
$statusNomes = [
    "recebido" => "Veículo recebido",
    "diagnostico" => "Análise e diagnóstico",
    "aguardando_aprovacao" => "Aguardando aprovação",
    "em_execucao" => "Serviço em execução",
    "pronto" => "Pronto para entrega",
];
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
        Meus Serviços
    </title>
    <link rel="stylesheet" href="style.css">

</head>

<body>


<header>

    <h1>

        ⚙ PJY autoTech

    </h1>


    <nav>

        <a href="#">

            Início

        </a>


        <a href="#">

            Meus serviços

        </a>


        <a href="#">

            Orçamentos

        </a>


        <a
            href="logout.php"
        >

            Sair

        </a>

    </nav>

</header>


<main>


<section>


<h1>

    Olá,

    <?php

    echo
    $_SESSION["nome"];

    ?>

</h1>


<h2>

    Sua oficina,

    <span>

        na palma da mão.

    </span>

</h2>


<p>

    Acompanhe o serviço
    do seu veículo.

</p>


</section>


<section>


<h2>

    🚗 Acompanhe
    seu serviço

</h2>


<div class="card">


<h3>

    <?= htmlspecialchars($servico ? trim($servico["marca"] . " " . $servico["modelo"]) : "Nenhum veículo cadastrado", ENT_QUOTES, "UTF-8") ?>

</h3>


<p>

    Placa:
    <?= htmlspecialchars($servico["placa"] ?? "-", ENT_QUOTES, "UTF-8") ?>

</p>


<p>

    Status:

    <strong>

        <?= htmlspecialchars($statusNomes[$servico["status"] ?? ""] ?? "Aguardando atendimento", ENT_QUOTES, "UTF-8") ?>

    </strong>

</p>


<p>

    Previsão:

    <?= $servico && $servico["previsao_entrega"] ? (new DateTime($servico["previsao_entrega"]))->format("d/m/Y") : "Não definida" ?>

</p>


</div>


</section>


</main>

</body>

</html>