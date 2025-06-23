<?php
session_start();
include 'includes/funcoes.php';

// Verifica se os parâmetros foram enviados
if (!isset($_GET['id_jogo'], $_GET['id_plataforma'])) {
    redirect('index.php', 'Parâmetros inválidos.', 'danger');
}

$id_jogo = (int)$_GET['id_jogo'];
$id_plataforma = (int)$_GET['id_plataforma'];

// Busca informações do jogo
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

// Inicializa o carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Verifica se o item já está no carrinho
$itemExistente = false;
foreach ($_SESSION['carrinho'] as &$item) {
    if ($item['id_jogo'] == $id_jogo && $item['id_plataforma'] == $id_plataforma) {
        $item['dias'] += 1; // Adiciona mais um dia
        $itemExistente = true;
        break;
    }
}

// Se não existir, adiciona novo item
if (!$itemExistente) {
    $_SESSION['carrinho'][] = [
        'id_jogo' => $id_jogo,
        'id_plataforma' => $id_plataforma,
        'titulo' => $produto['titulo'],
        'plataforma' => $produto['plataforma'],
        'valor_diaria' => (float)$produto['valor_diaria'],
        'dias' => 1
    ];
}

redirect('carrinho.php', 'Jogo adicionado ao carrinho!');