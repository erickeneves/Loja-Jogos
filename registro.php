<?php
include 'includes/funcoes.php';
include 'includes/conexao.php';

// Se o usuário já está logado, redireciona
if (isset($_SESSION['usuario'])) {
    redirect('index.php');
}

// Processar registro
$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    // Limpar e validar CPF
    $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);
    
    // Validações
    if (empty($nome)) $erros[] = "Nome é obrigatório.";
    if (empty($cpf_limpo)) $erros[] = "CPF é obrigatório.";
    elseif (strlen($cpf_limpo) !== 11) $erros[] = "CPF deve conter 11 dígitos.";
    elseif (!validarCPF($cpf_limpo)) $erros[] = "CPF inválido.";
    if (empty($email)) $erros[] = "Email é obrigatório.";
    elseif (!validarEmail($email)) $erros[] = "Email inválido.";
    if (empty($senha)) $erros[] = "Senha é obrigatória.";
    elseif (strlen($senha) < 6) $erros[] = "Senha deve ter pelo menos 6 caracteres.";
    elseif ($senha !== $confirmar_senha) $erros[] = "As senhas não coincidem.";
    
    // Verificar se CPF já existe
    $stmt = $pdo->prepare("SELECT id_cliente FROM clientes WHERE cpf = ?");
    $stmt->execute([$cpf_limpo]);
    if ($stmt->fetch()) $erros[] = "Este CPF já está cadastrado.";
    
    // Verificar se email já existe
    $stmt = $pdo->prepare("SELECT id_cliente FROM clientes WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $erros[] = "Este email já está cadastrado.";
    
    // Se não há erros, cadastrar
    if (empty($erros)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO clientes (nome, cpf, email, senha, data_cadastro) 
                VALUES (?, ?, ?, ?, CURDATE())";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$nome, $cpf_limpo, $email, $senha_hash])) {
            redirect('login.php', 'Registro realizado com sucesso! Faça login.');
        } else {
            $erros[] = "Erro ao cadastrar. Tente novamente.";
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
                            <h2 class="card-title text-center mb-4">Criar Conta</h2>
                            
                            <?php if (!empty($erros)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($erros as $erro): ?>
                                            <li><?= $erro ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <form method="post">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome Completo</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="cpf" class="form-label">CPF</label>
                                    <input type="text" class="form-control" id="cpf" name="cpf" 
                                           placeholder="000.000.000-00" required>
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
                                    <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg">Registrar</button>
                                </div>
                                
                                <div class="text-center">
                                    <p class="mb-0">Já tem uma conta? <a href="login.php">Faça login</a></p>
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