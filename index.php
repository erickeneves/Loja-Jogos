<?php
include 'includes/conexao.php';
include 'includes/funcoes.php';

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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameRent - Aluguel de Jogos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="..css/indexprodutos.css">
    
    <style>
        .produtos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }
        .produto-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .produto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .produto-imagem {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .produto-body {
            padding: 15px;
        }
        .preco-destaque {
            font-weight: bold;
            color: #198754;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <?= exibirMensagemFlash() ?>
        
        <h1 class="mb-4">Jogos Disponíveis</h1>
        
        <div class="produtos">
            <?php foreach ($produtos as $produto): 
                // Mapeia os nomes dos jogos para os arquivos de imagem
                $imagemJogo = strtolower(str_replace([' ', ':'], ['_', ''], $produto['titulo'])) . '.png';
                $caminhoImagem = file_exists("img/$imagemJogo") ? "img/$imagemJogo" : "img/sem_imagem.png";
            ?>
                <div class="produto-card">
                    <img src="<?= $caminhoImagem ?>" alt="<?= htmlspecialchars($produto['titulo']) ?>" class="produto-imagem">
                    <div class="produto-body">
                        <h3><?= htmlspecialchars($produto['titulo']) ?></h3>
                        <p class="text-muted">Plataforma: <?= htmlspecialchars($produto['plataforma_nome']) ?></p>
                        <p class="preco-destaque"><?= formatarMoeda($produto['valor_diaria']) ?> <small class="text-muted">/dia</small></p>
                        <a href="produto.php?id_jogo=<?= $produto['id_jogo'] ?>&id_plataforma=<?= $produto['id_plataforma'] ?>" class="btn btn-primary">
                            Ver detalhes
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>