<?php
include 'includes/funcoes.php';

$pedido_id = $_GET['pedido'] ?? 0;
?>

<!DOCTYPE html>
<html>
<body>
    <h1>Obrigado por sua compra!</h1>
    <p>Seu pedido #<?= $pedido_id ?> foi recebido com sucesso.</p>
    <p>Você receberá uma confirmação por email.</p>
    <a href="index.php">Continuar comprando</a>
</body>
</html>