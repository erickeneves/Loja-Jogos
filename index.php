<?php
include 'includes/conexao.php';
include 'includes/funcoes.php';

// Consulta corrigida com JOINs
$sql = "SELECT 
            jp.*, 
            j.titulo, 
            p.nome AS plataforma_nome
        FROM jogo_plataforma jp
        JOIN jogos j ON jp.id_jogo = j.id_jogo
        JOIN plataformas p ON jp.id_plataforma = p.id_plataforma
        WHERE jp.quantidade_estoque > 0";
        
$produtos = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html>
<body>
    <?= exibirMensagemFlash() ?>
    
    <div class="produtos">
        <?php foreach ($produtos as $produto): ?>
            <div class="produto">
                <h3><?= htmlspecialchars($produto['titulo']) ?></h3>
                <!-- Adicione esta linha para mostrar a plataforma -->
                <p>Plataforma: <?= htmlspecialchars($produto['plataforma_nome']) ?></p>
                <p><?= formatarMoeda($produto['valor_diaria']) ?></p>
                <a href="produto.php?id_jogo=<?= $produto['id_jogo'] ?>&id_plataforma=<?= $produto['id_plataforma'] ?>&slug=<?= gerarSlug($produto['titulo']) ?>">
                    Ver detalhes
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>