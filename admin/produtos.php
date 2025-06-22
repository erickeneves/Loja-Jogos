<?php
session_start();
include '../includes/conexao.php';
include '../includes/funcoes.php';

// Verificar se é admin
if (!isset($_SESSION['admin_logado'])) {
    redirect('login_admin.php');
}

$erro = '';
$sucesso = '';
$produtoEdit = null;

// Buscar categorias
$categorias = $pdo->query("SELECT * FROM Categorias")->fetchAll();

// Se estiver editando, buscar o produto
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM Produtos WHERE produto_id = ?");
    $stmt->execute([$_GET['editar']]);
    $produtoEdit = $stmt->fetch();
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = (float) $_POST['preco'];
    $estoque = (int) $_POST['estoque'];
    $categoria_id = (int) $_POST['categoria_id'];
    $produto_id = $_POST['produto_id'] ?? null;
    
    // Validações
    $erros = [];
    
    if (empty($nome)) $erros[] = "Nome do produto é obrigatório";
    if (empty($descricao)) $erros[] = "Descrição é obrigatória";
    if ($preco <= 0) $erros[] = "Preço deve ser maior que zero";
    if ($estoque < 0) $erros[] = "Estoque não pode ser negativo";
    if ($categoria_id <= 0) $erros[] = "Selecione uma categoria válida";
    
    if (empty($erros)) {
        try {
            if ($produto_id) {
                // Atualizar produto existente
                $stmt = $pdo->prepare("UPDATE Produtos SET 
                                      nome = ?, 
                                      descricao = ?, 
                                      preco = ?, 
                                      estoque = ?, 
                                      categoria_id = ? 
                                      WHERE produto_id = ?");
                $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria_id, $produto_id]);
                $sucesso = "Produto atualizado com sucesso!";
            } else {
                // Criar novo produto
                $stmt = $pdo->prepare("INSERT INTO Produtos 
                                      (nome, descricao, preco, estoque, categoria_id) 
                                      VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria_id]);
                $sucesso = "Produto adicionado com sucesso!";
            }
        } catch (PDOException $e) {
            $erro = "Erro ao salvar produto: " . $e->getMessage();
        }
    } else {
        $erro = implode("<br>", $erros);
    }
}

// Excluir produto
if (isset($_GET['excluir'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM Produtos WHERE produto_id = ?");
        $stmt->execute([$_GET['excluir']]);
        $sucesso = "Produto excluído com sucesso!";
    } catch (PDOException $e) {
        $erro = "Erro ao excluir produto: " . $e->getMessage();
    }
}

// Listar produtos
$produtos = $pdo->query("SELECT p.*, c.nome AS categoria_nome 
                         FROM Produtos p
                         JOIN Categorias c ON p.categoria_id = c.categoria_id
                         ORDER BY p.produto_id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-form {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Gerenciar Produtos</h1>
        
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>
        
        <div class="card card-form">
            <div class="card-header">
                <h2 class="h5"><?= $produtoEdit ? 'Editar Produto' : 'Adicionar Novo Produto' ?></h2>
            </div>
            <div class="card-body">
                <form id="formProduto" method="post">
                    <?php if ($produtoEdit): ?>
                        <input type="hidden" name="produto_id" value="<?= $produtoEdit['produto_id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               value="<?= $produtoEdit['nome'] ?? '' ?>" required>
                        <div class="invalid-feedback">Por favor, informe o nome do produto.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" required><?= $produtoEdit['descricao'] ?? '' ?></textarea>
                        <div class="invalid-feedback">Por favor, informe a descrição do produto.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="preco" class="form-label">Preço (R$)</label>
                            <input type="number" step="0.01" class="form-control" id="preco" name="preco" 
                                   value="<?= $produtoEdit['preco'] ?? '' ?>" min="0.01" required>
                            <div class="invalid-feedback">Por favor, informe um preço válido.</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="estoque" class="form-label">Estoque</label>
                            <input type="number" class="form-control" id="estoque" name="estoque" 
                                   value="<?= $produtoEdit['estoque'] ?? '0' ?>" min="0" required>
                            <div class="invalid-feedback">Por favor, informe a quantidade em estoque.</div>
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
                        <div class="invalid-feedback">Por favor, selecione uma categoria.</div>
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
                                <th>Categoria</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td><?= $produto['produto_id'] ?></td>
                                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                                    <td><?= formatarMoeda($produto['preco']) ?></td>
                                    <td><?= $produto['estoque'] ?></td>
                                    <td><?= htmlspecialchars($produto['categoria_nome']) ?></td>
                                    <td>
                                        <a href="produtos.php?editar=<?= $produto['produto_id'] ?>" 
                                           class="btn btn-sm btn-warning">Editar</a>
                                        <a href="produtos.php?excluir=<?= $produto['produto_id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validação do formulário
        document.getElementById('formProduto').addEventListener('submit', function(event) {
            let formValido = true;
            
            if (!document.getElementById('nome').value.trim()) {
                document.getElementById('nome').classList.add('is-invalid');
                formValido = false;
            }
            
            if (!document.getElementById('descricao').value.trim()) {
                document.getElementById('descricao').classList.add('is-invalid');
                formValido = false;
            }
            
            if (document.getElementById('preco').value <= 0) {
                document.getElementById('preco').classList.add('is-invalid');
                formValido = false;
            }
            
            if (document.getElementById('estoque').value < 0) {
                document.getElementById('estoque').classList.add('is-invalid');
                formValido = false;
            }
            
            if (!document.getElementById('categoria_id').value) {
                document.getElementById('categoria_id').classList.add('is-invalid');
                formValido = false;
            }
            
            if (!formValido) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    </script>
</body>
</html>