<?php
include 'includes/funcoes.php';

session_start();

// Se não está logado, redirecionar para login
if (!isset($_SESSION['cliente_id'])) {
    header('Location: login.php');
    exit;
}

include 'includes/conexao.php';

// Buscar dados do cliente
$stmt = $pdo->prepare("SELECT * FROM Clientes WHERE cliente_id = ?");
$stmt->execute([$_SESSION['cliente_id']]);
$cliente = $stmt->fetch();

// Buscar pedidos do cliente
$pedidos = $pdo->prepare("
    SELECT * FROM Pedidos 
    WHERE cliente_id = ?
    ORDER BY data_pedido DESC
");
$pedidos->execute([$_SESSION['cliente_id']]);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minha Conta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Minha Conta</h1>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5">Informações Pessoais</h2>
                    </div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> <?= $cliente['nome'] ?></p>
                        <p><strong>Email:</strong> <?= $cliente['email'] ?></p>
                        <p><strong>CPF:</strong> <?= $cliente['cpf'] ?></p>
                        <p><strong>Data de Cadastro:</strong> <?= date('d/m/Y', strtotime($cliente['data_cadastro'])) ?></p>
                        
                        <a href="editar_perfil.php" class="btn btn-primary mt-3">Editar Perfil</a>
                        <a href="logout.php" class="btn btn-outline-danger mt-3">Sair</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5">Meus Pedidos</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($pedidos->rowCount() > 0): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Data</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($pedido = $pedidos->fetch()): ?>
                                            <tr>
                                                <td>#<?= $pedido['pedido_id'] ?></td>
                                                <td><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></td>
                                                <td>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
                                                <td>
                                                    <span class="badge bg-<?= 
                                                        $pedido['status'] == 'pendente' ? 'warning' : 
                                                        ($pedido['status'] == 'enviado' ? 'info' : 
                                                        ($pedido['status'] == 'entregue' ? 'success' : 'danger')) 
                                                    ?>">
                                                        <?= ucfirst($pedido['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="detalhe_pedido.php?id=<?= $pedido['pedido_id'] ?>" class="btn btn-sm btn-info">Detalhes</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>Você ainda não fez nenhum pedido.</p>
                            <a href="index.php" class="btn btn-primary">Ver Produtos</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>