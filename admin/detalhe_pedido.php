<?php
include 'includes/funcoes.php';

session_start();
// Verificar se o usuário está logado como admin
if (!isset($_SESSION['admin_logado'])) {
    header('Location: login_admin.php');
    exit;
}

include '../includes/conexao.php';

$pedido_id = $_GET['id'] ?? 0;

// Buscar informações do pedido
$stmt = $pdo->prepare("
    SELECT p.*, c.nome AS cliente_nome, c.email AS cliente_email, c.cpf AS cliente_cpf,
           e.cep, e.logradouro, e.numero, e.complemento, e.bairro, e.cidade, e.estado,
           pg.metodo AS metodo_pagamento, pg.status AS status_pagamento
    FROM Pedidos p
    JOIN Clientes c ON p.cliente_id = c.cliente_id
    JOIN Enderecos e ON p.endereco_entrega_id = e.endereco_id
    LEFT JOIN Pagamentos pg ON p.pedido_id = pg.pedido_id
    WHERE p.pedido_id = ?
");
$stmt->execute([$pedido_id]);
$pedido = $stmt->fetch();

// Buscar itens do pedido
$stmt = $pdo->prepare("
    SELECT pi.*, pr.nome AS produto_nome, pr.descricao AS produto_descricao
    FROM Pedido_Itens pi
    JOIN Produtos pr ON pi.produto_id = pr.produto_id
    WHERE pi.pedido_id = ?
");
$stmt->execute([$pedido_id]);
$itens = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Pedido #<?= $pedido_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5em 0.8em;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Detalhes do Pedido #<?= $pedido_id ?></h1>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="h5">Informações do Pedido</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Cliente:</strong> <?= $pedido['cliente_nome'] ?></p>
                        <p><strong>Email:</strong> <?= $pedido['cliente_email'] ?></p>
                        <p><strong>CPF:</strong> <?= $pedido['cliente_cpf'] ?></p>
                        <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <strong>Status:</strong> 
                            <span class="badge bg-<?= 
                                $pedido['status'] == 'pendente' ? 'warning' : 
                                ($pedido['status'] == 'processando' ? 'info' : 
                                ($pedido['status'] == 'enviado' ? 'primary' : 
                                ($pedido['status'] == 'entregue' ? 'success' : 'danger'))) 
                            ?> status-badge">
                                <?= ucfirst($pedido['status']) ?>
                            </span>
                        </p>
                        <p><strong>Valor Total:</strong> R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></p>
                        <p><strong>Pagamento:</strong> <?= ucfirst(str_replace('_', ' ', $pedido['metodo_pagamento'])) ?> 
                            (<?= ucfirst($pedido['status_pagamento']) ?>)
                        </p>
                        <?php if ($pedido['codigo_rastreio']): ?>
                            <p><strong>Código Rastreio:</strong> <?= $pedido['codigo_rastreio'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="h5">Endereço de Entrega</h2>
            </div>
            <div class="card-body">
                <p><?= $pedido['logradouro'] ?>, <?= $pedido['numero'] ?> <?= $pedido['complemento'] ?></p>
                <p><?= $pedido['bairro'] ?></p>
                <p><?= $pedido['cidade'] ?> - <?= $pedido['estado'] ?></p>
                <p>CEP: <?= $pedido['cep'] ?></p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="h5">Itens do Pedido</h2>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Descrição</th>
                            <th>Preço Unitário</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td><?= $item['produto_nome'] ?></td>
                                <td><?= substr($item['produto_descricao'], 0, 50) ?>...</td>
                                <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                                <td><?= $item['quantidade'] ?></td>
                                <td>R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-light">
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="pedidos.php" class="btn btn-secondary">Voltar para Pedidos</a>
            <button class="btn btn-primary" onclick="window.print()">Imprimir Detalhes</button>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>