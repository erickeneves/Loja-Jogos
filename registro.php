<?php
session_start();
include 'includes/conexao.php';

include 'includes/funcoes.php';

// Se já está logado, redirecionar
if (isset($_SESSION['cliente_id'])) {
    header('Location: index.php');
    exit;
}

// Processar registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $cpf = $_POST['cpf'];
    
    try {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Clientes WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $erro = 'Este email já está cadastrado!';
        } else {
            // Inserir novo cliente
            $stmt = $pdo->prepare("INSERT INTO Clientes (nome, email, senha, cpf) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senha, $cpf]);
            
            // Logar automaticamente
            $cliente_id = $pdo->lastInsertId();
            $_SESSION['cliente_id'] = $cliente_id;
            $_SESSION['cliente_nome'] = $nome;
            
            header('Location: index.php');
            exit;
        }
    } catch (PDOException $e) {
        $erro = 'Erro ao registrar: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .registro-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="registro-container">
                    <h2 class="text-center mb-4">Criar Conta</h2>
                    
                    <?php if (isset($erro)): ?>
                        <div class="alert alert-danger"><?= $erro ?></div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" placeholder="000.000.000-00" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Registrar</button>
                        
                        <div class="text-center mt-3">
                            <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>