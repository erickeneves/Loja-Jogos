<?php include 'includes/conexao.php'; include 'includes/funcoes.php';?>
<!DOCTYPE html>
<html>
<head>
    <title>Minha Loja</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <header>
        <h1>Minha Loja Virtual</h1>
    </header>
    
    <div class="produtos">
        <?php
        $stmt = $pdo->query("SELECT * FROM Produtos WHERE estoque > 0");
        while ($produto = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="produto">';
            echo '<h3>' . $produto['nome'] . '</h3>';
            echo '<p>R$ ' . number_format($produto['preco'], 2, ',', '.') . '</p>';
            echo '<a href="produto.php?id=' . $produto['produto_id'] . '">Ver Detalhes</a>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>