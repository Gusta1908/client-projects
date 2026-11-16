<?php

session_start();

// Apaga todas as informações da sessão
session_unset();

// Destrói a sessão
session_destroy();

// Volta para a tela de login
header("Location: login.php");
exit;

?>