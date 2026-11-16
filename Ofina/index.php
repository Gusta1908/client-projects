<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (($_SESSION["tipo"] ?? "") === "cliente") {
    header("Location: Cliente/inicio.php");
    exit;
}
?>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <!-- Faz o site se adaptar ao celular -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PJY autotech</title>

    <!-- Arquivo CSS -->
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="servicos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

    <!-- ========================= -->
    <!-- MENU SUPERIOR -->
    <!-- ========================= -->

    <header class="cabecalho">

        <!-- Logo -->
        <div class="logo">

            <div class="logo-icone">
                <i class="fa-solid fa-gear"></i>
            </div>

            <div>
                <h1>PJY</h1>
                <span>AUTOTECH</span>
            </div>

        </div>


        <!-- Botões -->
        <div class="menu-direito">

            <a href="#contato" class="botao-contato">

                <i class="fa-brands fa-whatsapp"></i>

                Fale conosco

            </a>

            <a href="login.php" class="botao-perfil" aria-label="Área do cliente">

                <i class="fa-regular fa-user"></i>

            </a>

        </div>

    </header>



    <!-- ========================= -->
    <!-- BANNER PRINCIPAL -->
    <!-- ========================= -->

    <section class="banner">

        <div class="banner-conteudo">

            <h2>

                Sua oficina,

                <br>

                <span>na palma da mão.</span>

            </h2>


            <p>

                Acompanhe o status do seu veículo,
                orçamentos e serviços de forma rápida,
                simples e transparente.

            </p>


            <a href="novo_orcamento.html" class="botao-principal">

                <i class="fa-solid fa-car"></i>

                Novo Orçamento

            </a>

        </div>

    </section>



    <?php if (false): ?>
    <!-- ========================= -->
    <!-- ÁREA DE SERVIÇOS -->
    <!-- ========================= -->




        <!-- Área principal -->

        <div class="servicos-grid">


            <!-- Informações do veículo -->

            <div class="card-servico">

                <div class="veiculo">

                    <div class="icone-carro">

                        <i class="fa-solid fa-car-side"></i>

                    </div>


                    <div>

                        <h3><?= $servico ? htmlspecialchars(trim($servico["marca"] . " " . $servico["modelo"]), ENT_QUOTES, "UTF-8") : "Nenhum veículo cadastrado" ?></h3>

                        <p><?= $servico ? "Placa: " . htmlspecialchars($servico["placa"], ENT_QUOTES, "UTF-8") : "Cadastre um orçamento para acompanhar o serviço." ?></p>

                        <span class="status-verde">

                            <?= htmlspecialchars($statusTexto, ENT_QUOTES, "UTF-8") ?>

                        </span>

                    </div>

                </div>



                <!-- Informações -->

                <div class="informacoes-servico">


                    <div>

                        <i class="fa-regular fa-calendar"></i>

                        <span>Data de entrada</span>

                        <strong><?= $formatarData($servico["data_entrada"] ?? null) ?></strong>

                        <small><?= $formatarHora($servico["data_entrada"] ?? null) ?></small>

                    </div>


                    <div>

                        <i class="fa-regular fa-calendar"></i>

                        <span>Previsão de entrega</span>

                        <strong><?= $formatarData($servico["previsao_entrega"] ?? null) ?></strong>

                        <small><?= $formatarHora($servico["previsao_entrega"] ?? null) ?></small>

                    </div>

                </div>



                <!-- Mensagem -->

                <div class="mensagem-oficina">

                    <div class="mensagem-icone">

                        <i class="fa-regular fa-comment"></i>

                    </div>


                    <div>

                        <strong>

                            Em caso de dúvidas,
                            nossa equipe está pronta
                            para te atender!

                        </strong>

                        <p>

                            Clique no botão ao lado
                            para falar conosco.

                        </p>

                    </div>


                    <a href="#" class="botao-falar">

                        <i class="fa-brands fa-whatsapp"></i>

                        Falar com a oficina

                    </a>

                </div>

            </div>



            <!-- Próximos passos -->

            <aside class="proximos-passos">

                <h2>

                    <i class="fa-regular fa-clipboard"></i>

                    Próximos passos

                </h2>


                <div class="passo concluido">

                    <div class="passo-icone">

                        <i class="fa-solid fa-car"></i>

                    </div>

                    <div>

                        <h3>Recebemos seu veículo</h3>

                        <p>

                            Seu veículo já está
                            com a nossa equipe.

                        </p>

                    </div>

                    <i class="fa-solid fa-circle-check"></i>

                </div>



                <div class="passo atual">

                    <div class="passo-icone">

                        <i class="fa-solid fa-screwdriver-wrench"></i>

                    </div>

                    <div>

                        <h3>Análise e diagnóstico</h3>

                        <p>

                            Estamos avaliando o problema
                            para encontrar as melhores soluções.

                        </p>

                    </div>

                </div>



                <div class="passo">

                    <div class="passo-icone">

                        <i class="fa-regular fa-file-lines"></i>

                    </div>

                    <div>

                        <h3>Aguardando aprovação</h3>

                        <p>

                            Assim que o orçamento
                            for aprovado, iniciaremos o serviço.

                        </p>

                    </div>

                </div>



                <div class="passo">

                    <div class="passo-icone">

                        <i class="fa-solid fa-screwdriver-wrench"></i>

                    </div>

                    <div>

                        <h3>Serviço em execução</h3>

                        <p>

                            Realizando os reparos
                            e testes necessários.

                        </p>

                    </div>

                </div>



                <div class="passo">

                    <div class="passo-icone">

                        <i class="fa-solid fa-check"></i>

                    </div>

                    <div>

                        <h3>Pronto para entrega</h3>

                        <p>

                            Finalizaremos e entraremos
                            em contato com você.

                        </p>

                    </div>

                </div>

            </aside>

        </div>

    </section>



    <!-- ========================= -->
    <!-- STATUS DO SERVIÇO -->
    <!-- ========================= -->

    <section class="status-servico">

        <h2>

            <i class="fa-regular fa-file-lines"></i>

            Status do serviço

        </h2>


        <div class="linha-status">


            <div class="etapa concluida">

                <div>

                    <i class="fa-solid fa-car"></i>

                </div>

                <h3>Recebido</h3>

                <p>20/07 - 14:30</p>

            </div>


            <div class="etapa atual">

                <div>

                    <i class="fa-solid fa-magnifying-glass"></i>

                </div>

                <h3>Análise e diagnóstico</h3>

                <p>Em andamento</p>

            </div>


            <div class="etapa">

                <div>

                    <i class="fa-regular fa-file-lines"></i>

                </div>

                <h3>Aguardando aprovação</h3>

                <p>Próxima etapa</p>

            </div>


            <div class="etapa">

                <div>

                    <i class="fa-solid fa-screwdriver-wrench"></i>

                </div>

                <h3>Serviço em execução</h3>

                <p>Aguardando</p>

            </div>


            <div class="etapa">

                <div>

                    <i class="fa-solid fa-check"></i>

                </div>

                <h3>Pronto para entrega</h3>

                <p>Aguardando</p>

            </div>

        </div>

    </section>



    <!-- ========================= -->
    <!-- ORÇAMENTOS -->
    <!-- ========================= -->

    <section class="orcamentos" id="orcamentos">

        <h2>

        <a href="#" onclick="acessarOrcamentos(event)">
          <i class="fa-regular fa-file-lines"></i>
           Meus orçamentos
           </a>

        </h2>


        <div class="card-orcamento">

            <div>

                <h3><?= $servico && $servico["orcamento_id"] ? "Orçamento #" . (int) $servico["orcamento_id"] : "Nenhum orçamento cadastrado" ?></h3>

                <p>

                    <?= $servico ? htmlspecialchars(trim($servico["marca"] . " " . $servico["modelo"] . " • " . $servico["placa"]), ENT_QUOTES, "UTF-8") : "Cadastre um novo orçamento para começar." ?>

                </p>

            </div>


            <div>

                <span>Valor estimado</span>

                <strong><?= $servico ? "R$ " . number_format((float) $servico["valor"], 2, ",", ".") : "R$ 0,00" ?></strong>

            </div>


            <span class="status-amarelo">

                <?= htmlspecialchars($statusOrcamento[$servico["orcamento_status"] ?? ""] ?? "Sem orçamento", ENT_QUOTES, "UTF-8") ?>

            </span>


            <button class="botao-principal">

                Ver orçamento

            </button>

        </div>

    </section>
    <?php endif; ?>

    <!-- =========================
         SERVIÇOS
    ========================== -->

    <section class="servicos">

        <div class="container">

            <!-- Cabeçalho -->
            <div class="section-header">

                <div>
                    <span class="mini-title">
                        <span></span>
                        NOSSOS SERVIÇOS
                    </span>

                    <h1>
                        Tudo o que seu veículo precisa,
                        <strong>em um só lugar.</strong>
                    </h1>

                    <p>
                        Realizamos serviços com qualidade, agilidade e
                        transparência para manter seu veículo sempre em
                        perfeito estado.
                    </p>
                </div>

                <a href="#" class="btn-outline">
                    Ver todos os serviços
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </div>


            <!-- Cards -->
            <div class="servicos-grid">

                <!-- Card 1 -->
                <article class="servico-card">

                    <div class="card-content">

                        <div class="icon">
                            <i class="fa-solid fa-wrench"></i>
                        </div>

                        <h2> Mecânica Geral</h2>

                        <p>
                            Diagnóstico preciso e reparos
                            com profissionais experientes.
                        </p>

                    </div>

                    <img src="img/mecanica.jpg" alt="Mecânica automotiva">

                </article>


                <!-- Card 2 -->
                <article class="servico-card">

                    <div class="card-content">

                        <div class="icon">
                            <i class="fa-solid fa-circle-stop"></i>
                        </div>

                        <h2>Freios</h2>

                        <p>
                            Mais segurança para você
                            e sua família.
                        </p>

                    </div>

                    <img src="img/freios.jpg" alt="Sistema de freios">

                </article>


                <!-- Card 3 -->
                <article class="servico-card">

                    <div class="card-content">

                        <div class="icon">
                            <i class="fa-solid fa-circle-dot"></i>
                        </div>

                        <h2>Pneus</h2>

                        <p>
                            Alinhamento, balanceamento
                            e troca de pneus.
                        </p>

                    </div>

                    <img src="img/pneus.jpg" alt="Pneu automotivo">

                </article>


                <!-- Card 4 -->
                <article class="servico-card">

                    <div class="card-content">

                        <div class="icon">
                            <i class="fa-solid fa-car-battery"></i>
                        </div>

                        <h2>Elétrica</h2>

                        <p>
                            Solução para falhas elétricas
                            e muito mais.
                        </p>

                    </div>

                    <img src="img/eletrica.jpg" alt="Elétrica automotiva">

                </article>


                <!-- Card 5 -->
                <article class="servico-card">

                    <div class="card-content">

                        <div class="icon">
                            <i class="fa-solid fa-snowflake"></i>
                        </div>

                        <h2>Ar-condicionado</h2>

                        <p>
                            Conforto em todas as estações.
                        </p>

                    </div>

                    <img src="img/ar-condicionado.jpg"
                         alt="Ar condicionado automotivo">

                </article>

            </div>

        </div>
    </section>


    <!-- =========================
         POR QUE ESCOLHER
    ========================== -->

    <section class="diferenciais">

        <div class="container diferenciais-container">

            <div class="diferenciais-title">

                <span class="mini-title">
                    <span></span>
                    POR QUE ESCOLHER A PJY AUTOTECH?
                </span>

                <h2>
                    Qualidade, confiança
                    <strong>e transparência.</strong>
                </h2>

            </div>


            <div class="diferenciais-list">

                <div class="diferencial">

                    <i class="fa-solid fa-shield-halved"></i>

                    <h3>
                        Profissionais
                        qualificados
                    </h3>

                    <p>
                        Equipe experiente e
                        em constante atualização.
                    </p>

                </div>


                <div class="diferencial">

                    <i class="fa-solid fa-clock"></i>

                    <h3>
                        Atendimento
                        ágil e eficiente
                    </h3>

                    <p>
                        Seu tempo é valioso.
                        Nós respeitamos.
                    </p>

                </div>


                <div class="diferencial">

                    <i class="fa-solid fa-award"></i>

                    <h3>
                        Peças de
                        qualidade
                    </h3>

                    <p>
                        Trabalhamos com as
                        melhores marcas do mercado.
                    </p>

                </div>


                <div class="diferencial">

                    <i class="fa-solid fa-handshake"></i>

                    <h3>
                        Transparência
                        em todo o processo
                    </h3>

                    <p>
                        Você sempre sabe
                        o que está sendo feito.
                    </p>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================
         COMO FUNCIONA
    ========================== -->

    <section class="como-funciona">

        <div class="container">

            <div class="funciona-header">

                <div>

                    <span class="mini-title">
                        <span></span>
                        COMO FUNCIONA
                    </span>

                    <h2>
                        É simples, rápido
                        <strong>e sem complicação.</strong>
                    </h2>

                </div>

            </div>


            <div class="steps">

                <!-- 1 -->
                <div class="step">

                    <div class="step-number">1</div>

                    <i class="fa-regular fa-calendar"></i>

                    <h3>Agende seu serviço</h3>

                    <p>
                        Escolha o melhor dia e horário
                        para você.
                    </p>

                </div>


                <div class="arrow">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>


                <!-- 2 -->
                <div class="step">

                    <div class="step-number">2</div>

                    <i class="fa-solid fa-car"></i>

                    <h3>Traga seu veículo</h3>

                    <p>
                        Nossa equipe fará uma análise
                        completa.
                    </p>

                </div>


                <div class="arrow">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>


                <!-- 3 -->
                <div class="step">

                    <div class="step-number">3</div>

                    <i class="fa-solid fa-wrench"></i>

                    <h3>Acompanhamento</h3>

                    <p>
                        Você fica por dentro de todo
                        o processo.
                    </p>

                </div>


                <div class="arrow">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>


                <!-- 4 -->
                <div class="step">

                    <div class="step-number">4</div>

                    <i class="fa-regular fa-circle-check"></i>

                    <h3>Retire seu veículo</h3>

                    <p>
                        Pronto para rodar com
                        mais segurança.
                    </p>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================
         CTA
    ========================== -->

    <section class="cta">

        <div class="cta-content">

            <div class="cta-logo">
                <i class="fa-solid fa-gear"></i>
            </div>

            <div>

                <span>SEU VEÍCULO EM BOAS MÃOS</span>

                <h2>
                    Agende agora mesmo seu serviço!
                </h2>

                <p>
                    A PJY autoTech está pronta para te atender.
                </p>

            </div>

            <a href="#" class="btn-yellow">
                Agendar serviço
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </section>

</body>

    <!-- ========================= -->
    <!-- RODAPÉ -->
    <!-- ========================= -->

    <footer id="contato">

        <div>

            <h2>⚙ PJY autoTech</h2>

            <p>

                Tecnologia e transparência
                para cuidar do seu veículo.

            </p>

        </div>


        <div>

            <h3>Contato</h3>

            <p>

                <i class="fa-brands fa-whatsapp"></i>

                (11) 99999-9999

            </p>

            <p>

                <i class="fa-regular fa-envelope"></i>

                contato@topautomotiva.com

            </p>

        </div>


        <div>

            <h3>Horário</h3>

            <p>

                Segunda a sexta

                <br>

                08:00 às 18:00

            </p>

        </div>

    </footer>
    

</body>

</html>