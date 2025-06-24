<?php
include 'includes/funcoes.php';

session_start();
if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit;
}

include '../includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pedido_id'])) {
    $stmt = $pdo->prepare("UPDATE Pedidos SET status = ? WHERE pedido_id = ?");
    $stmt->execute([$_POST['status'], $_POST['pedido_id']]);
    $mensagem = "Status do pedido atualizado!";
}

$pedidos = $pdo->query("
    SELECT p.*, c.nome AS cliente_nome 
    FROM Pedidos p
    JOIN Clientes c ON p.cliente_id = c.cliente_id
    ORDER BY p.data_pedido DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Gerenciar Pedidos</h1>
        
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-success"><?= $mensagem ?></div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?= $pedido['pedido_id'] ?></td>
                            <td><?= $pedido['cliente_nome'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></td>
                            <td>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
                            <td>
                                <form method="post" class="d-flex">
                                    <input type="hidden" name="pedido_id" value="<?= $pedido['pedido_id'] ?>">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="pendente" <?= $pedido['status'] == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                        <option value="processando" <?= $pedido['status'] == 'processando' ? 'selected' : '' ?>>Processando</option>
                                        <option value="enviado" <?= $pedido['status'] == 'enviado' ? 'selected' : '' ?>>Enviado</option>
                                        <option value="entregue" <?= $pedido['status'] == 'entregue' ? 'selected' : '' ?>>Entregue</option>
                                        <option value="cancelado" <?= $pedido['status'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <a href="detalhe_pedido.php?id=<?= $pedido['pedido_id'] ?>" class="btn btn-sm btn-info">Detalhes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>