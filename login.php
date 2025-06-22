<?php
session_start();
include 'includes/conexao.php';
include 'includes/funcoes.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    // Buscar cliente pelo email
    $stmt = $pdo->prepare("SELECT * FROM Clientes WHERE email = ?");
    $stmt->execute([$email]);
    $cliente = $stmt->fetch();
    
    if ($cliente && password_verify($senha, $cliente['senha'])) {
        // Login bem sucedido
        $_SESSION['cliente_id'] = $cliente['cliente_id'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        
        // Redirecionar para página anterior ou home
        $redirect = $_SESSION['redirect_to'] ?? 'index.php';
        unset($_SESSION['redirect_to']);
        
        redirect($redirect, 'Login realizado com sucesso!');
    } else {
        $erro = "Email ou senha incorretos";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4">Login</h2>
        
        <?= exibirMensagemFlash() ?>
        
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>
        
        <form id="formLogin" method="post" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <div class="invalid-feedback">Por favor, informe seu email.</div>
            </div>
            
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
                <div class="invalid-feedback">Por favor, informe sua senha.</div>
            </div>
            
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
            </div>
            
            <div class="text-center">
                <p>Ainda não tem conta? <a href="registro.php">Crie uma agora</a></p>
                <p><a href="recuperar_senha.php">Esqueceu sua senha?</a></p>
            </div>
        </form>
    </div>

    <script>
        // Validação do formulário no cliente
        document.getElementById('formLogin').addEventListener('submit', function(event) {
            let formValido = true;
            
            if (!document.getElementById('email').value.trim()) {
                document.getElementById('email').classList.add('is-invalid');
                formValido = false;
            } else {
                document.getElementById('email').classList.remove('is-invalid');
            }
            
            if (!document.getElementById('senha').value) {
                document.getElementById('senha').classList.add('is-invalid');
                formValido = false;
            } else {
                document.getElementById('senha').classList.remove('is-invalid');
            }
            
            if (!formValido) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    </script>
</body>
</html>