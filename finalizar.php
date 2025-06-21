<?php
include 'includes/funcoes.php';

session_start();
include 'includes/conexao.php';

// Processar compra
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Registrar cliente
    $stmt = $pdo->prepare("INSERT INTO Clientes (nome, email) VALUES (?, ?)");
    $stmt->execute([$_POST['nome'], $_POST['email']]);
    $cliente_id = $pdo->lastInsertId();
    
    // 2. Registrar endereço
    $stmt = $pdo->prepare("INSERT INTO Enderecos (...) VALUES (...)");
    $stmt->execute([...]);
    $endereco_id = $pdo->lastInsertId();
    
    // 3. Registrar pedido
    $total = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
    
    $stmt = $pdo->prepare("INSERT INTO Pedidos (...) VALUES (...)");
    $stmt->execute([...]);
    $pedido_id = $pdo->lastInsertId();
    
    // 4. Registrar itens do pedido
    foreach ($_SESSION['carrinho'] as $produto_id => $item) {
        $stmt = $pdo->prepare("INSERT INTO Pedido_Itens (...) VALUES (...)");
        $stmt->execute([...]);
        
        // Atualizar estoque
        $stmt = $pdo->prepare("UPDATE Produtos SET estoque = estoque - ? WHERE produto_id = ?");
        $stmt->execute([$item['quantidade'], $produto_id]);
    }
    
    // 5. Limpar carrinho
    unset($_SESSION['carrinho']);
    
    // Redirecionar para página de obrigado
    header("Location: obrigado.php?pedido=$pedido_id");
    exit;
}
?>

<!DOCTYPE html>
<html>
<body>
    <h2>Finalizar Compra</h2>
    
    <form method="post">
        <h3>Dados Pessoais</h3>
        <input type="text" name="nome" placeholder="Nome completo" required>
        <input type="email" name="email" placeholder="Email" required>
        
        <h3>Endereço de Entrega</h3>
        <!-- Campos de endereço aqui -->
        
        <h3>Pagamento</h3>
        <select name="metodo_pagamento">
            <option value="pix">PIX</option>
            <option value="cartao">Cartão de Crédito</option>
            <option value="boleto">Boleto</option>
        </select>
        
        <button type="submit">Concluir Pedido</button>
    </form>
</body>
</html>