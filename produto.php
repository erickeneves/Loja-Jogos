<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/conexao.php';
include 'includes/funcoes.php';
include 'header.php';

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
            GROUP_CONCAT(DISTINCT g.nome SEPARATOR ', ') AS generos
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
        .plataforma-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .plataforma-card:hover {
            background-color: #f8f9fa;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="jogo-header">
            <div class="row">
                <div class="col-md-4">
                    <?php
                    $imagemJogo = strtolower(str_replace([' ', ':'], ['_', ''], $jogo['titulo'])) . '.png';
                    $caminhoImagem = file_exists("img/$imagemJogo") ? "img/$imagemJogo" : "img/sem_imagem.png";
                    ?>
                    <img src="<?= $caminhoImagem ?>" alt="<?= htmlspecialchars($jogo['titulo']) ?>" class="jogo-imagem">
                </div>
                <div class="col-md-8">
                    <h1><?= htmlspecialchars($jogo['titulo']) ?></h1>
                    
                    <div class="d-flex flex-wrap mb-3">
                        <span class="badge bg-primary info-badge">
                            <?= $jogo['faixa_etaria'] ?> anos
                        </span>
                        <span class="badge bg-secondary info-badge">
                            Lançamento: <?= $jogo['ano_lancamento'] ?>
                        </span>
                    </div>
                    
                    <h3 class="mt-4">Gêneros</h3>
                    <p><?= htmlspecialchars($jogo['generos'] ?? 'Não informado') ?></p>
                    
                    <h3 class="mt-4">Descrição</h3>
                    <p><?= nl2br(htmlspecialchars($jogo['descricao'])) ?></p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <h2>Plataformas Disponíveis</h2>
                <p>Selecione a plataforma para alugar:</p>
                
                <?php if (count($plataformas) > 0): ?>
                    <div class="plataformas-lista">
                        <?php foreach ($plataformas as $plataforma): ?>
                            <div class="plataforma-card">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <h4 class="mb-0"><?= htmlspecialchars($plataforma['plataforma_nome']) ?></h4>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-0"><?= formatarMoeda($plataforma['valor_diaria']) ?> <small class="text-muted">/dia</small></p>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="badge bg-<?= $plataforma['quantidade_estoque'] > 0 ? 'success' : 'danger' ?>">
                                            <?= $plataforma['quantidade_estoque'] > 0 ? 'Disponível' : 'Esgotado' ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <?php if ($plataforma['quantidade_estoque'] > 0): ?>
                                            <a href="alugar.php?id_jogo=<?= $id_jogo ?>&id_plataforma=<?= $plataforma['id_plataforma'] ?>" class="btn btn-primary">
                                                Alugar
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary" disabled>Indisponível</button>
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
<?php include 'footer.php';?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>