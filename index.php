<?php
include 'includes/conexao.php';
include 'includes/funcoes.php';
include 'includes/image-mapping.php';
include 'header.php';

$sql = "SELECT 
            ANY_VALUE(j.id_jogo) AS id_jogo,
            j.titulo,
            MIN(jp.valor_diaria) AS menor_preco,
            GROUP_CONCAT(DISTINCT p.nome ORDER BY p.nome SEPARATOR ', ') AS plataformas,
            COUNT(DISTINCT jp.id_plataforma) AS num_plataformas
        FROM jogos j
        JOIN jogo_plataforma jp ON j.id_jogo = jp.id_jogo
        JOIN plataformas p ON jp.id_plataforma = p.id_plataforma
        WHERE jp.quantidade_estoque > 0
        GROUP BY j.titulo
        ORDER BY j.titulo";
        
$produtos = $pdo->query($sql)->fetchAll();
?>

<div class="container py-5">
    <h1 class="mb-4 text-center text-light">Jogos Disponíveis</h1>
    
    <div class="row">
        <?php foreach ($produtos as $produto): 
            $images = getGameImages($produto['titulo']);
            
            $primaryImage = "img/primary/" . $images['primary'];
            $altImage = "img/alternate/" . $images['alternate'];
            
            if (!file_exists($primaryImage)) {
                $primaryImage = "img/primary/default.png";
            }
            if (!file_exists($altImage)) {
                $altImage = "img/alternate/default.png";
            }
        ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-5">
                <div class="game-container">
                    <div class="game-card">
                        <img src="<?= $primaryImage ?>" 
                             alt="<?= htmlspecialchars($produto['titulo']) ?>" 
                             class="background-image">
                        
                        <img src="<?= $altImage ?>" 
                             alt="<?= htmlspecialchars($produto['titulo']) ?>" 
                             class="floating-image">
                        
                        <div class="card-light"></div>
                        
                        <div class="card-content">
                            <h5 class="game-title"><?= htmlspecialchars($produto['titulo']) ?></h5>
                            <p class="game-platform">
                                Plataformas: <?= htmlspecialchars($produto['plataformas']) ?>
                            </p>
                            <p class="game-price">A partir de <?= formatarMoeda($produto['menor_preco']) ?> /dia</p>
                            <a href="produto.php?id_jogo=<?= $produto['id_jogo'] ?>" 
                               class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>