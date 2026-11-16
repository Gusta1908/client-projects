<?php

// Inicia a sessão caso ainda não esteja iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION["id"])) {

    header("Location:login.php");
    exit;
}

?>