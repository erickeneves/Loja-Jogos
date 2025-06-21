<?php 
include 'includes/conexao.php';

include 'includes/funcoes.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM Produtos WHERE produto_id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<body>
    <h1><?= $produto['nome'] ?></h1>
    <p>Preço: R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
    <p><?= $produto['descricao'] ?></p>
    
    <form action="carrinho.php" method="post">
        <input type="hidden" name="acao" value="adicionar">
        <input type="hidden" name="produto_id" value="<?= $produto['produto_id'] ?>">
        Quantidade: 
        <input type="number" name="quantidade" value="1" min="1" max="<?= $produto['estoque'] ?>">
        <button type="submit">Adicionar ao Carrinho</button>
    </form>
</body>
</html>