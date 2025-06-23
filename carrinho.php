<?php
include 'includes/funcoes.php';
include 'header.php';

// Verifica se o carrinho existe
$carrinho = $_SESSION['carrinho'] ?? [];
$total = 0;

// Processar remoção de item
if (isset($_GET['remover'])) {
    $indice = (int)$_GET['remover'];
    if (isset($carrinho[$indice])) {
        unset($carrinho[$indice]);
        $_SESSION['carrinho'] = array_values($carrinho); // Reindexa o array
    }
}

// Processar atualização de dias
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    foreach ($_POST['dias'] as $indice => $dias) {
        if (isset($carrinho[$indice])) {
            $dias = max(1, min(7, (int)$dias)); // Limita entre 1 e 7 dias
            $carrinho[$indice]['dias'] = $dias;
        }
    }
    $_SESSION['carrinho'] = $carrinho;
}

// Calcular total
foreach ($carrinho as $item) {
    $total += $item['valor_diaria'] * $item['dias'];
}
?>

<h1 class="mb-4">Seu Carrinho</h1>

<?php if (empty($carrinho)): ?>
    <div class="alert alert-info">Seu carrinho está vazio.</div>
<?php else: ?>
    <form method="post">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Jogo</th>
                    <th>Plataforma</th>
                    <th>Valor Diário</th>
                    <th>Dias</th>
                    <th>Subtotal</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrinho as $indice => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['titulo']) ?></td>
                        <td><?= htmlspecialchars($item['plataforma']) ?></td>
                        <td><?= formatarMoeda($item['valor_diaria']) ?></td>
                        <td>
                            <input type="number" name="dias[<?= $indice ?>]" 
                                   value="<?= $item['dias'] ?>" 
                                   min="1" max="7" class="form-control" style="width: 80px;">
                        </td>
                        <td><?= formatarMoeda($item['valor_diaria'] * $item['dias']) ?></td>
                        <td>
                            <a href="carrinho.php?remover=<?= $indice ?>" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                    <td colspan="2"><strong><?= formatarMoeda($total) ?></strong></td>
                </tr>
            </tfoot>
        </table>
        
        <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-secondary">Continuar Comprando</a>
            <div>
                <button type="submit" name="atualizar" class="btn btn-info">Atualizar Carrinho</button>
                <a href="finalizar.php" class="btn btn-success">Finalizar Aluguel</a>
            </div>
        </div>
    </form>
<?php endif; ?>

<?php include 'footer.php'; ?>