<?php
include 'includes/funcoes.php';

session_start();
// Verificar se o usuário está logado como admin
if (!isset($_SESSION['admin_logado']) {
    header('Location: login_admin.php');
    exit;
}

include '../includes/conexao.php';

// Adicionar/Editar Produto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $categoria_id = $_POST['categoria_id'];
    
    if (isset($_POST['produto_id'])) {
        // Editar produto existente
        $stmt = $pdo->prepare("UPDATE Produtos SET nome = ?, descricao = ?, preco = ?, estoque = ?, categoria_id = ? WHERE produto_id = ?");
        $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria_id, $_POST['produto_id']]);
        $mensagem = "Produto atualizado com sucesso!";
    } else {
        // Adicionar novo produto
        $stmt = $pdo->prepare("INSERT INTO Produtos (nome, descricao, preco, estoque, categoria_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria_id]);
        $mensagem = "Produto adicionado com sucesso!";
    }
}

// Excluir Produto
if (isset($_GET['excluir'])) {
    $stmt = $pdo->prepare("DELETE FROM Produtos WHERE produto_id = ?");
    $stmt->execute([$_GET['excluir']]);
    $mensagem = "Produto excluído com sucesso!";
}

// Buscar todos os produtos
$produtos = $pdo->query("SELECT * FROM Produtos")->fetchAll(PDO::FETCH_ASSOC);

// Buscar categorias para o dropdown
$categorias = $pdo->query("SELECT * FROM Categorias")->fetchAll(PDO::FETCH_ASSOC);

// Se estiver editando, buscar o produto
$produtoEdit = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM Produtos WHERE produto_id = ?");
    $stmt->execute([$_GET['editar']]);
    $produtoEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 1200px; }
        .table-responsive { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Gerenciar Produtos</h1>
        
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-success"><?= $mensagem ?></div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="h5"><?= $produtoEdit ? 'Editar' : 'Adicionar' ?> Produto</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <?php if ($produtoEdit): ?>
                        <input type="hidden" name="produto_id" value="<?= $produtoEdit['produto_id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               value="<?= $produtoEdit['nome'] ?? '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" required><?= $produtoEdit['descricao'] ?? '' ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="preco" class="form-label">Preço (R$)</label>
                            <input type="number" step="0.01" class="form-control" id="preco" name="preco" 
                                   value="<?= $produtoEdit['preco'] ?? '' ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="estoque" class="form-label">Estoque</label>
                            <input type="number" class="form-control" id="estoque" name="estoque" 
                                   value="<?= $produtoEdit['estoque'] ?? '0' ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categoria_id" class="form-label">Categoria</label>
                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['categoria_id'] ?>" 
                                    <?= ($produtoEdit && $produtoEdit['categoria_id'] == $categoria['categoria_id']) ? 'selected' : '' ?>>
                                    <?= $categoria['nome'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <?php if ($produtoEdit): ?>
                        <a href="produtos.php" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="h5">Lista de Produtos</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td><?= $produto['produto_id'] ?></td>
                                    <td><?= $produto['nome'] ?></td>
                                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                    <td><?= $produto['estoque'] ?></td>
                                    <td>
                                        <a href="produtos.php?editar=<?= $produto['produto_id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                        <a href="produtos.php?excluir=<?= $produto['produto_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>