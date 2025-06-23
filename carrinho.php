<?php
include 'includes/funcoes.php';

session_start();
include 'includes/conexao.php';

// Adicionar item ao carrinho
if ($_POST['acao'] == 'adicionar') {
    $produto_id = $_POST['produto_id'];
    $quantidade = $_POST['quantidade'];
    
    // Buscar produto no banco
    $stmt = $pdo->prepare("SELECT * FROM jogos WHERE produto_id = ?");
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch();
    
    // Adicionar à sessão
    $_SESSION['carrinho'][$produto_id] = [
        'nome' => $produto['nome'],
        'preco' => $produto['preco'],
        'quantidade' => $quantidade
    ];
    
    header("Location: carrinho.php");
    exit;
}

// Remover item do carrinho
if (isset($_GET['remover'])) {
    unset($_SESSION['carrinho'][$_GET['remover']]);
    header("Location: carrinho.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<body>
    <h2>Seu Carrinho</h2>
    
    <?php if (empty($_SESSION['carrinho'])): ?>
        <p>Carrinho vazio</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Produto</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th>Total</th>
                <th>Ações</th>
            </tr>
            
            <?php 
            $total = 0;
            foreach ($_SESSION['carrinho'] as $id => $item): 
                $subtotal = $item['preco'] * $item['quantidade'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= $item['nome'] ?></td>
                <td>R$ <?= echo formatarMoeda(29.90); // R$ 29,90</td>
                <td><?= $item['quantidade'] ?></td>
                <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                <td><a href="carrinho.php?remover=<?= $id ?>">Remover</a></td>
            </tr>
            <?php endforeach; ?>
            
            <tr>
                <td colspan="3">Total</td>
                <td>R$ <?= number_format($total, 2, ',', '.') ?></td>
            </tr>
        </table>
        
        <a href="finalizar.php">Finalizar Compra</a>
    <?php endif; ?>
</body>
</html>