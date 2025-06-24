<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/conexao.php';
include 'includes/funcoes.php';

$id_jogo = $_GET['id_jogo'] ?? null;
$id_plataforma = $_GET['id_plataforma'] ?? null;

if (!$id_jogo || !$id_plataforma || !is_numeric($id_jogo) || !is_numeric($id_plataforma)) {
    header('Location: index.php');
    exit;
}

$sql = "SELECT 
            j.id_jogo,
            j.titulo,
            j.descricao,
            j.ano_lancamento,
            j.faixa_etaria,
            p.nome AS plataforma,
            jp.valor_diaria,
            jp.quantidade_estoque,
            GROUP_CONCAT(g.nome SEPARATOR ', ') AS gêneros
        FROM jogos j
        JOIN jogo_plataforma jp ON j.id_jogo = jp.id_jogo
        JOIN plataformas p ON jp.id_plataforma = p.id_plataforma
        LEFT JOIN jogo_gênero jg ON j.id_jogo = jg.id_jogo
        LEFT JOIN gêneros g ON jg.id_genero = g.id_genero
        WHERE j.id_jogo = :id_jogo 
          AND jp.id_plataforma = :id_plataforma
        GROUP BY j.id_jogo, jp.id_plataforma";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id_jogo' => $id_jogo,
    ':id_plataforma' => $id_plataforma
]);
$produto = $stmt->fetch();

if (!$produto) {
    header('Location: index.php');
    exit;
}

$tituloPagina = $produto['titulo'] . ' - ' . $produto['plataforma'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina) ?> - GameRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .jogo-header {
            background-color: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .jogo-imagem {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .preco-destaque {
            font-size: 1.75rem;
            font-weight: bold;
            color: #198754;
        }
        .btn-alugar {
            font-size: 1.1rem;
            padding: 0.75rem 1.5rem;
        }
        .info-badge {
            font-size: 0.9rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="jogo-header">
            <div class="row">
                <div class="col-md-4">
                    <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                        <span class="text-muted">Imagem do jogo</span>
                    </div>
                </div>
                <div class="col-md-8">
                    <h1><?= htmlspecialchars($produto['titulo']) ?></h1>
                    <h2 class="h4 text-muted"><?= htmlspecialchars($produto['plataforma']) ?></h2>
                    
                    <div class="my-4">
                        <span class="preco-destaque"><?= formatarMoeda($produto['valor_diaria']) ?></span>
                        <span class="text-muted">por dia</span>
                    </div>
                    
                    <div class="d-flex flex-wrap mb-3">
                        <span class="badge bg-primary info-badge">
                            <?= $produto['faixa_etaria'] ?> anos
                        </span>
                        <span class="badge bg-secondary info-badge">
                            Lançamento: <?= $produto['ano_lancamento'] ?>
                        </span>
                        <span class="badge bg-<?= $produto['quantidade_estoque'] > 0 ? 'success' : 'danger' ?> info-badge">
                            <?= $produto['quantidade_estoque'] > 0 ? 'Disponível' : 'Esgotado' ?>
                        </span>
                    </div>
                    
                    <?php if ($produto['quantidade_estoque'] > 0): ?>
                        <form action="alugar.php" method="post" class="mt-3">
                            <input type="hidden" name="id_jogo" value="<?= $produto['id_jogo'] ?>">
                            <input type="hidden" name="id_plataforma" value="<?= $id_plataforma ?>">
                            
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <label for="dias" class="col-form-label">Dias de aluguel:</label>
                                </div>
                                <div class="col-auto">
                                    <input type="number" class="form-control" id="dias" name="dias" min="1" max="7" value="1" style="width: 80px;">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary btn-alugar">Alugar Agora</button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning mt-3">
                            Este produto está temporariamente esgotado. Volte mais tarde!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <h3>Descrição</h3>
                <p><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
                
                <h3 class="mt-4">Gêneros</h3>
                <p><?= htmlspecialchars($produto['gêneros'] ?? 'Não informado') ?></p>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Detalhes Técnicos</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Plataforma:</span>
                                <span><?= htmlspecialchars($produto['plataforma']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Valor diário:</span>
                                <span class="fw-bold"><?= formatarMoeda($produto['valor_diaria']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Disponibilidade:</span>
                                <span class="badge bg-<?= $produto['quantidade_estoque'] > 0 ? 'success' : 'danger' ?>">
                                    <?= $produto['quantidade_estoque'] > 0 ? 'Em estoque' : 'Esgotado' ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Ano de lançamento:</span>
                                <span><?= $produto['ano_lancamento'] ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Classificação:</span>
                                <span><?= $produto['faixa_etaria'] ?> anos</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>