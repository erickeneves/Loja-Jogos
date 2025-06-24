<?php
include 'includes/conexao.php';
include 'includes/funcoes.php';

$id_jogo = $_GET['id_jogo'] ?? null;

if (!$id_jogo || !is_numeric($id_jogo)) {
    header('Location: index.php');
    exit;
}

// Consulta para obter informações gerais do jogo
$sqlJogo = "SELECT 
            j.id_jogo,
            j.titulo,
            j.descricao,
            j.ano_lancamento,
            j.faixa_etaria,
            GROUP_CONCAT(DISTINCT g.nome SEPARATOR ', ') AS gêneros
        FROM jogos j
        LEFT JOIN jogo_gênero jg ON j.id_jogo = jg.id_jogo
        LEFT JOIN gêneros g ON jg.id_genero = g.id_genero
        WHERE j.id_jogo = :id_jogo
        GROUP BY j.id_jogo";

$stmtJogo = $pdo->prepare($sqlJogo);
$stmtJogo->execute([':id_jogo' => $id_jogo]);
$jogo = $stmtJogo->fetch();

if (!$jogo) {
    header('Location: index.php');
    exit;
}

// Consulta para obter as plataformas disponíveis para este jogo
$sqlPlataformas = "SELECT 
            jp.id_plataforma,
            p.nome AS plataforma_nome,
            jp.valor_diaria,
            jp.quantidade_estoque
        FROM jogo_plataforma jp
        JOIN plataformas p ON jp.id_plataforma = p.id_plataforma
        WHERE jp.id_jogo = :id_jogo
          AND jp.quantidade_estoque > 0";

$stmtPlataformas = $pdo->prepare($sqlPlataformas);
$stmtPlataformas->execute([':id_jogo' => $id_jogo]);
$plataformas = $stmtPlataformas->fetchAll();

$tituloPagina = $jogo['titulo'];
include 'header.php';
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-6">
            <?php
            include 'includes/image-mapping.php';
            $images = getGameImages($jogo['titulo']);
            $primaryImage = "img/primary/" . $images['primary'];
            $altImage = "img/alternate/" . $images['alternate'];
            ?>
            <div class="game-container mb-4" style="height: 500px;">
        <div class="game-card">
            <img src="<?= $primaryImage ?>" 
                 alt="<?= htmlspecialchars($jogo['titulo']) ?>" 
                 class="background-image"
                 style="object-fit: contain; background: #1a1a2e;">
            
            <img src="<?= $altImage ?>" 
                 alt="<?= htmlspecialchars($jogo['titulo']) ?>" 
                 class="floating-image"
                 style="object-fit: contain;">
            
            <div class="card-light"></div>
        </div>
    </div>
        </div>
        
        <div class="col-md-5">
            <div class="card bg-dark text-light p-4">
                <h1 class="mb-3"><?= htmlspecialchars($jogo['titulo']) ?></h1>
                
                <div class="mb-4">
                    <span class="badge bg-primary me-2">
                        <?= $jogo['faixa_etaria'] ?> anos
                    </span>
                    <span class="badge bg-secondary me-2">
                        Lançamento: <?= $jogo['ano_lancamento'] ?>
                    </span>
                </div>
                
                <h3 class="mt-4">Descrição</h3>
                <p><?= nl2br(htmlspecialchars($jogo['descricao'])) ?></p>
                
                <h3 class="mt-4">Gêneros</h3>
                <p><?= htmlspecialchars($jogo['gêneros'] ?? 'Não informado') ?></p>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="text-light mb-4">Plataformas Disponíveis</h2>
            
            <?php if (count($plataformas) > 0): ?>
                <div class="row">
                    <?php foreach ($plataformas as $plataforma): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-dark text-light h-100">
                                <div class="card-body">
                                    <h4 class="card-title"><?= htmlspecialchars($plataforma['plataforma_nome']) ?></h4>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fs-5 text-success fw-bold">
                                            <?= formatarMoeda($plataforma['valor_diaria']) ?> /dia
                                        </span>
                                        <span class="badge bg-<?= $plataforma['quantidade_estoque'] > 0 ? 'success' : 'danger' ?>">
                                            <?= $plataforma['quantidade_estoque'] > 0 ? 'Disponível' : 'Esgotado' ?>
                                        </span>
                                    </div>
                                    
                                    <?php if ($plataforma['quantidade_estoque'] > 0): ?>
                                        <form action="adicionar_carrinho.php" method="get">
                                            <input type="hidden" name="id_jogo" value="<?= $id_jogo ?>">
                                            <input type="hidden" name="id_plataforma" value="<?= $plataforma['id_plataforma'] ?>">
                                            
                                            <div class="d-flex align-items-center mb-3">
                                                <label class="me-2">Dias:</label>
                                                <input type="number" class="form-control w-50" name="dias" min="1" max="7" value="1">
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="bi bi-cart-plus me-2"></i> Adicionar ao Carrinho
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary w-100" disabled>
                                            Indisponível
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    Este jogo não está disponível em nenhuma plataforma no momento.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>