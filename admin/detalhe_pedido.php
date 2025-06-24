<!--<?php
include '../includes/funcoes.php';

?>
<!DOCTYPE html>
<html>
<body>

    <p><strong>Data:</strong> <?= formatarData($pedido['data_pedido'], true) ?></p>
    <p><strong>Total:</strong> <?= formatarMoeda($pedido['valor_total']) ?></p>
    
    <p><strong>CEP:</strong> <?= formatarCEP($pedido['cep']) ?></p>
    
    <table class="table">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Preço Unitário</th>
                <th>Quantidade</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itens as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['produto_nome']) ?></td>
                    <td><?= formatarMoeda($item['preco_unitario']) ?></td>
                    <td><?= $item['quantidade'] ?></td>
                    <td><?= formatarMoeda($item['preco_unitario'] * $item['quantidade']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                <td><?= formatarMoeda($pedido['valor_total']) ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>