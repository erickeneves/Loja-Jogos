<?php
session_start();
include '../includes/funcoes.php';
include '../includes/conexao.php';

// Se o administrador já está logado, redireciona
if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'admin') {
    redirect('index.php');
}

// Processar login
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    // Validação básica
    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        // Buscar administrador
        $stmt = $pdo->prepare("SELECT id_funcionario, nome, email, senha FROM funcionários WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($senha, $admin['senha'])) {
            $_SESSION['usuario'] = [
                'id' => $admin['id_funcionario'],
                'nome' => $admin['nome'],
                'email' => $admin['email'],
                'tipo' => 'admin'
            ];
            redirect('index.php', 'Login de administrador realizado com sucesso!');
        } else {
            $erro = "Email ou senha incorretos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - GameRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-container {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .login-header {
            background: rgba(0, 0, 0, 0.3);
            padding: 30px 20px;
            text-align: center;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            color: #fff;
        }
        
        .btn-admin {
            background: linear-gradient(45deg, #0d6efd, #0b5ed7);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-admin:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        }
        
        .admin-logo {
            width: 80px;
            margin-bottom: 20px;
            filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.2));
        }
        
        .text-light {
            color: #f8f9fa !important;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="../img/bannerpng.png" alt="GameRent Admin" class="admin-logo">
                <h2 class="text-light">Área Administrativa</h2>
            </div>
            
            <div class="login-body">
                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?= $erro ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label text-light">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="senha" class="form-label text-light">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-admin text-light">Entrar</button>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="../login.php" class="text-light">Voltar para área do cliente</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>