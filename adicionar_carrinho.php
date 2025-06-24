<?php
include 'includes/funcoes.php';
include 'header.php';

if (!isset($_GET['id_jogo'], $_GET['id_plataforma'])) {
    redirect('index.php', 'Parâmetros inválidos.', 'danger');
}

$id_jogo = (int)$_GET['id_jogo'];
$id_plataforma = (int)$_GET['id_plataforma'];

include 'includes/conexao.php';
$sql = "SELECT j.titulo, p.nome AS plataforma, jp.valor_diaria
        FROM jogos j
        JOIN jogo_plataforma jp ON j.id_jogo = jp.id_jogo
        JOIN plataformas p ON jp.id_plataforma = p.id_plataforma
        WHERE j.id_jogo = :id_jogo AND jp.id_plataforma = :id_plataforma";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id_jogo' => $id_jogo, ':id_plataforma' => $id_plataforma]);
$produto = $stmt->fetch();

if (!$produto) {
    redirect('index.php', 'Produto não encontrado.', 'danger');
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$_SESSION['carrinho'][] = [
    'id_jogo' => $id_jogo,
    'id_plataforma' => $id_plataforma,
    'titulo' => $produto['titulo'],
    'plataforma' => $produto['plataforma'],
    'valor_diaria' => (float)$produto['valor_diaria'],
    'dias' => 1 
];
redirect('produto.php?id_jogo=' . $id_jogo, 'Jogo adicionado ao carrinho! Você pode continuar escolhendo mais jogos.');
include 'footer.php';
?>