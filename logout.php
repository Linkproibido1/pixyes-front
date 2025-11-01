<?php
session_start();

// Destruir todas as variáveis de sessão
$_SESSION = array();

 session_destroy();

// Redirecionar para uma página após a destruição da sessão
header("Location: ../");
exit();
?>
