<?php
session_start();
include 'includes/conexao.php';
include 'includes/funcoes.php';

// Captura os parâmetros da URL
$id_jogo = $_GET['id_jogo'] ?? null;
$id_plataforma = $_GET['id_plataforma'] ?? null; // Adicione este parâmetro

// Valida os parâmetros
if (!$id_jogo || !$id_plataforma) {
    header('Location: index.php');
    exit;
}

// Consulta para obter detalhes do produto específico
$sql = "SELECT 
            j.id_jogo,
            j.titulo,
            j.descricao,
            j.ano_lancamento,
            j.faixa_etaria,
            p.id_plataforma,
            p.nome AS plataforma_nome,
            jp.valor_diaria,
            jp.quantidade_estoque,
            GROUP_CONCAT(g.nome SEPARATOR ', ') AS generos
        FROM jogos j
        JOIN jogo_plataforma jp ON j.id_jogo = jp.id_jogo
        JOIN plataformas p ON jp.id_plataforma = p.id_plataforma
        LEFT JOIN jogo_genero jg ON j.id_jogo = jg.id_jogo
        LEFT JOIN generos g ON jg.id_genero = g.id_genero
        WHERE j.id_jogo = :id_jogo 
          AND p.id_plataforma = :id_plataforma
        GROUP BY j.id_jogo, p.id_plataforma";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id_jogo' => $id_jogo,
    ':id_plataforma' => $id_plataforma
]);
$produto = $stmt->fetch();

// Se não encontrou o produto, redireciona
if (!$produto) {
    header('Location: index.php');
    exit;
}

// Definir o título da página
$tituloPagina = $produto['titulo'] . ' - ' . $produto['plataforma_nome'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .jogo-imagem {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .jogo-detalhes {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-4">
        <div class="row">
            <div class="col-md-6">
                <!-- Imagem do jogo - se tivéssemos uma coluna de imagem, seria aqui -->
                <div class="bg-light border rounded p-4 text-center">
                    <p>Imagem não disponível</p>
                </div>
            </div>
            <div class="col-md-6">
                <h1><?= htmlspecialchars($produto['titulo']) ?></h1>
                <p class="lead"><?= htmlspecialchars($produto['plataforma_nome']) ?></p>
                <p class="h4 text-success"><?= formatarMoeda($produto['valor_diaria']) ?> <small class="text-muted">/ dia</small></p>
                
                <div class="mt-4">
                    <?php if ($produto['quantidade_estoque'] > 0): ?>
                        <p class="text-success">Disponível em estoque</p>
                        <form action="alugar.php" method="post" class="mt-3">
                            <input type="hidden" name="id_jogo" value="<?= $produto['id_jogo'] ?>">
                            <input type="hidden" name="id_plataforma" value="<?= $produto['id_plataforma'] ?>">
                            
                            <div class="mb-3">
                                <label for="dias" class="form-label">Dias de aluguel:</label>
                                <input type="number" class="form-control" id="dias" name="dias" min="1" max="7" value="1" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Alugar Agora</button>
                        </form>
                    <?php else: ?>
                        <p class="text-danger">Produto esgotado</p>
                    <?php endif; ?>
                </div>
                
                <div class="jogo-detalhes">
                    <h3>Detalhes</h3>
                    <p><strong>Gêneros:</strong> <?= htmlspecialchars($produto['generos'] ?? 'Não informado') ?></p>
                    <p><strong>Ano de lançamento:</strong> <?= $produto['ano_lancamento'] ?></p>
                    <p><strong>Classificação:</strong> <?= $produto['faixa_etaria'] ?></p>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h3>Descrição</h3>
                <p><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>