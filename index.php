<?php
include 'includes/conexao.php';
include 'includes/funcoes.php';
include 'header.php';


$sql = "SELECT 
            j.id_jogo,
            j.titulo,
            MIN(jp.valor_diaria) AS menor_preco,
            COUNT(DISTINCT jp.id_plataforma) AS plataformas_disponiveis
        FROM jogos j
        JOIN jogo_plataforma jp ON j.id_jogo = jp.id_jogo
        WHERE jp.quantidade_estoque > 0
        GROUP BY j.id_jogo";
        
$produtos = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameRent - Aluguel de Jogos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            height: 500px;
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
        .produto-imagem:hover .wrapper::before,
        .wrapper::after{
            opacity: 1;
        }
        .produto-imagem:hover .wrapper::after{
            height: 120px;
        }
        .produto-imagem:hover .title {
            transform: translate3d(0%, -50px, 100px);
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <?= exibirMensagemFlash() ?>
        
        <h1 class="mb-4">Jogos Disponíveis</h1>
        
        <div class="produtos">
            <?php foreach ($produtos as $produto): 
                // Formatar o nome da imagem
                $imagemJogo = strtolower(str_replace([' ', ':'], ['_', ''], $produto['titulo'])) . '.png';
                $caminhoImagem = file_exists("img/$imagemJogo") ? "img/$imagemJogo" : "img/sem_imagem.png";
            ?>
                <div class="produto-card">
                    <img src="<?= $caminhoImagem ?>" alt="<?= htmlspecialchars($produto['titulo']) ?>" class="produto-imagem" id="card">
                    <div class="produto-body">
                        <h3><?= htmlspecialchars($produto['titulo']) ?></h3>
                        <p class="preco-destaque">A partir de <?= formatarMoeda($produto['menor_preco']) ?> <small class="text-muted">/dia</small></p>
                        <p class="text-muted">Disponível em <?= $produto['plataformas_disponiveis'] ?> plataforma(s)</p>
                        <a href="produto.php?id_jogo=<?= $produto['id_jogo'] ?>" class="btn btn-primary">
                            Ver detalhes e plataformas
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php include 'footer.php';?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>