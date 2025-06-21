<?php
include 'includes/funcoes.php';

session_start();

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

// Redirecionar para página inicial
header('Location: index.php');
exit;
?>