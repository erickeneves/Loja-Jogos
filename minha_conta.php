<?php
include 'includes/funcoes.php';

?>
<!DOCTYPE html>
<html>
<body>
    <div class="card">
        <div class="card-body">
            <p><strong>Nome:</strong> <?= htmlspecialchars($cliente['nome']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($cliente['email']) ?></p>
            <p><strong>CPF:</strong> <?= formatarCPF($cliente['cpf']) ?></p>
            <p><strong>Data de Cadastro:</strong> <?= formatarData($cliente['data_cadastro']) ?></p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td>#<?= $pedido['pedido_id'] ?></td>
                        <td><?= formatarData($pedido['data_pedido'], true) ?></td>
                        <td><?= formatarMoeda($pedido['valor_total']) ?></td>
                        <td>
                            <span class="badge bg-<?= classeStatusPedido($pedido['status']) ?>">
                                <?= ucfirst($pedido['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>