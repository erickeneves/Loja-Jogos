<?php
include 'includes/conexao.php';
include 'includes/funcoes.php'; // Inclui as funções

$produtos = $pdo->query("SELECT * FROM Produtos WHERE estoque > 0")->fetchAll();
?>

<!DOCTYPE html>
<html>
<body>
    <?= exibirMensagemFlash() ?>
    
    <div class="produtos">
        <?php foreach ($produtos as $produto): ?>
            <div class="produto">
                <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                <p><?= formatarMoeda($produto['preco']) ?></p>
                <a href="produto.php?id=<?= $produto['produto_id'] ?>&slug=<?= gerarSlug($produto['nome']) ?>">
                    Ver Detalhes
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>