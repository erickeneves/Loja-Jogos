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

<div class="container py-4 mt-4">
    <h1 class="mb-4 text-center display-4">🛒 Seu Carrinho</h1>

    <?php if (empty($carrinho)): ?>
        <div class="alert alert-info text-center py-4">
            <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
            <h3 class="mt-3">Seu carrinho está vazio</h3>
            <p class="mb-0">Explore nossa coleção de jogos e encontre algo incrível!</p>
            <a href="index.php" class="btn btn-primary mt-3">Voltar para a Loja</a>
        </div>
    <?php else: ?>
        <form method="post">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead class="bg-purple">
                        <tr>
                            <th scope="col">Jogo</th>
                            <th scope="col">Plataforma</th>
                            <th scope="col">Valor Diário</th>
                            <th scope="col">Dias</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col" class="text-center">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrinho as $indice => $item): 
                            // Formatar nome da imagem
                            $imagemJogo = strtolower(str_replace([' ', ':'], ['_', ''], $item['titulo'])) . '.png';
                            $caminhoImagem = file_exists("img/primary/$imagemJogo") ? "img/primary/$imagemJogo" : "img/sem_imagem.png";
                        ?>
                            <tr class="carrinho-item">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= $caminhoImagem ?>" alt="<?= htmlspecialchars($item['titulo']) ?>" 
                                             class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                        <span><?= htmlspecialchars($item['titulo']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($item['plataforma']) ?></td>
                                <td class="text-success fw-bold"><?= formatarMoeda($item['valor_diaria']) ?></td>
                                <td>
                                    <input type="number" name="dias[<?= $indice ?>]" 
                                           value="<?= $item['dias'] ?>" 
                                           min="1" max="7" class="form-control bg-dark text-light" style="width: 80px;">
                                </td>
                                <td class="text-warning fw-bold"><?= formatarMoeda($item['valor_diaria'] * $item['dias']) ?></td>
                                <td class="text-center">
                                    <a href="carrinho.php?remover=<?= $indice ?>" class="btn btn-danger btn-sm" title="Remover">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-group-divider">
                        <tr>
                            <td colspan="4" class="text-end fw-bold fs-5">Total:</td>
                            <td colspan="2" class="text-warning fw-bold fs-5"><?= formatarMoeda($total) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top border-secondary">
                <a href="index.php" class="btn btn-outline-light mb-3 mb-md-0">
                    <i class="bi bi-arrow-left me-2"></i> Continuar Comprando
                </a>
                
                <div class="d-flex gap-2">
                    <button type="submit" name="atualizar" class="btn btn-outline-info">
                        <i class="bi bi-arrow-repeat me-2"></i> Atualizar Carrinho
                    </button>
                    <a href="finalizar.php" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i> Finalizar Aluguel
                    </a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<style>
    /* Estilos específicos para o carrinho */
    .bg-purple {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    }
    
    .carrinho-item {
        transition: all 0.3s ease;
    }
    
    .carrinho-item:hover {
        background-color: rgba(255, 255, 255, 0.05);
        transform: translateX(5px);
    }
    
    .table-dark {
        --bs-table-bg: rgba(30, 30, 46, 0.8);
        --bs-table-striped-bg: rgba(40, 40, 60, 0.8);
        --bs-table-hover-bg: rgba(50, 50, 70, 0.8);
        --bs-table-border-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }
    
    .table-dark th {
        color: #e6e6ff;
    }
    
    .table-dark td {
        color: #f0f0ff;
    }
    
    .img-thumbnail {
        background-color: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .form-control.bg-dark {
        background-color: rgba(0, 0, 0, 0.3) !important;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff !important;
    }
    
    .form-control.bg-dark:focus {
        background-color: rgba(0, 0, 0, 0.4) !important;
        border-color: #6a11cb;
        box-shadow: 0 0 0 0.25rem rgba(106, 17, 203, 0.25);
    }
    
    .alert-info {
        background: rgba(13, 110, 253, 0.15);
        border: 1px solid rgba(13, 110, 253, 0.3);
        color: #a3d5ff;
    }
</style>