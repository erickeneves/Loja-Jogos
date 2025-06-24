<?php
include 'includes/funcoes.php';
include 'includes/conexao.php';

if (isset($_SESSION['usuario'])) {
    redirect('index.php');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        $stmt = $pdo->prepare("SELECT id_cliente, nome, email, senha FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario'] = [
                'id' => $usuario['id_cliente'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email'],
                'tipo' => 'usuario'
            ];
            redirect('index.php', 'Login realizado com sucesso!');
        } else {
            $erro = "Email ou senha incorretos.";
        }
    }
}

include 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="row g-0">
                    <div class="col-md-6 d-none d-md-block">
                        <img src="img/banner.jpg" alt="GameRent" class="img-fluid h-100" style="object-fit: cover;">
                    </div>
                    <div class="col-md-6">
                        <div class="card-body p-5">
                            <h2 class="card-title text-center mb-4">Login de Usuário</h2>
                            
                            <?php if ($erro): ?>
                                <div class="alert alert-danger"><?= $erro ?></div>
                            <?php endif; ?>
                            
                            <form method="post">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="senha" class="form-label">Senha</label>
                                    <input type="password" class="form-control" id="senha" name="senha" required>
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                                </div>
                                
                                <div class="text-center">
                                    <p class="mb-0">Não tem uma conta? <a href="registro.php">Registre-se</a></p>
                                    <p class="mt-3"><a href="admin/login_admin.php">Acesso de administrador</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>