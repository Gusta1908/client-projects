<?php

require_once "includes/verificar_login.php";
require_once __DIR__ . "/includes/conexao.php";

if ($_SESSION["tipo"] != "admin") {

    header("Location: Cliente/inicio.php");
    exit;
}

?>

<?php
$metricas = [
    "chamados" => (int) $conexao->query("SELECT COUNT(*) FROM servicos WHERE status <> 'pronto'")->fetchColumn(),
    "orcamentos" => (int) $conexao->query("SELECT COUNT(*) FROM orcamentos WHERE status = 'pendente'")->fetchColumn(),
    "execucao" => (int) $conexao->query("SELECT COUNT(*) FROM servicos WHERE status = 'em_execucao'")->fetchColumn(),
    "entregas" => (int) $conexao->query("SELECT COUNT(*) FROM servicos WHERE status = 'pronto' AND DATE(previsao_entrega) = CURDATE()")->fetchColumn(),
];
$chamado = $conexao->query(
    "SELECT s.id, u.nome, v.placa, s.status
     FROM servicos s
     INNER JOIN veiculos v ON v.id = s.veiculo_id
     INNER JOIN usuarios u ON u.id = v.usuario_id
     ORDER BY s.id DESC LIMIT 1"
)->fetch(PDO::FETCH_ASSOC) ?: null;
$statusNomes = [
    "recebido" => "Recebido",
    "diagnostico" => "Diagnóstico",
    "aguardando_aprovacao" => "Aguardando aprovação",
    "em_execucao" => "Em execução",
    "pronto" => "Pronto",
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
        Painel Administrativo
    </title>

    <link
        rel="stylesheet"
        href="style (1).css"
    >

</head>

<body>


<aside class="menu-admin">

    <h2>
        ⚙ TOP
    </h2>

    <a href="#">
        🏠 Dashboard
    </a>

    <a href="#">
        📋 Chamados
    </a>

    <a href="#">
        👥 Clientes
    </a>

    <a href="#">
        🚗 Veículos
    </a>

    <a href="#">
        🔧 Serviços
    </a>

    <a href="#">
        🧾 Orçamentos
    </a>

    <a href="#">
        💰 Financeiro
    </a>

    <a href="#">
        📊 Relatórios
    </a>

    <a
        href="logout.php"
    >

        🚪 Sair

    </a>

</aside>


<main class="painel">


    <header>

        <h1>

            Olá,

            <?php

            echo
            $_SESSION["nome"];

            ?>

        </h1>

        <p>

            Gestão completa
            para sua oficina

        </p>

    </header>


    <section class="cards">


        <div class="card">

            <p>
                Chamados abertos
            </p>

            <h2>
                <?= $metricas["chamados"] ?>
            </h2>

        </div>


        <div class="card">

            <p>
                Orçamentos pendentes
            </p>

            <h2>
                <?= $metricas["orcamentos"] ?>
            </h2>

        </div>


        <div class="card">

            <p>
                Serviços em execução
            </p>

            <h2>
                <?= $metricas["execucao"] ?>
            </h2>

        </div>


        <div class="card">

            <p>
                Entregas hoje
            </p>

            <h2>
                <?= $metricas["entregas"] ?>
            </h2>

        </div>


    </section>


    <section class="chamados">


        <h2>
            Chamados recentes
        </h2>


        <table>


            <tr>

                <th>
                    Número
                </th>

                <th>
                    Cliente
                </th>

                <th>
                    Placa
                </th>

                <th>
                    Status
                </th>

            </tr>


            <tr>

                <td>
                    <?= $chamado ? "#" . (int) $chamado["id"] : "Sem registros" ?>
                </td>

                <td>
                    <?= htmlspecialchars($chamado["nome"] ?? "Nenhum cliente", ENT_QUOTES, "UTF-8") ?>
                </td>

                <td>
                    <?= htmlspecialchars($chamado["placa"] ?? "-", ENT_QUOTES, "UTF-8") ?>
                </td>

                <td>
                    <?= htmlspecialchars($statusNomes[$chamado["status"] ?? ""] ?? "-", ENT_QUOTES, "UTF-8") ?>
                </td>

            </tr>


        </table>


    </section>


</main>

</body>

</html>