<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"]) || ($_SESSION["tipo"] ?? "") !== "cliente") {
    header("Location: ../login.php");
    exit;
}
