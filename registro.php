<?php
session_start();
include 'includes/conexao.php';
include 'includes/funcoes.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $cpf = $_POST['cpf'];
    
    // Validações
    $erros = [];
    
    if (empty($nome)) $erros[] = "Nome é obrigatório";
    if (empty($email)) $erros[] = "Email é obrigatório";
    if (empty($senha)) $erros[] = "Senha é obrigatória";
    if (empty($cpf)) $erros[] = "CPF é obrigatório";
    
    if (!empty($cpf) && !validarCPF($cpf)) {
        $erros[] = "CPF inválido";
    }
    
    if (!empty($email) && !validarEmail($email)) {
        $erros[] = "Email inválido";
    }
    
    if (!empty($senha) && !validarSenha($senha)) {
        $erros[] = "Senha deve ter no mínimo 8 caracteres com letras e números";
    }
    
    if (empty($erros)) {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Clientes WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetchColumn() == 0) {
            // Criar novo cliente
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO Clientes (nome, email, senha, cpf) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senha_hash, $cpf]);
            
            // Logar automaticamente
            $cliente_id = $pdo->lastInsertId();
            $_SESSION['cliente_id'] = $cliente_id;
            $_SESSION['cliente_nome'] = $nome;
            
            redirect('index.php', 'Registro realizado com sucesso!');
        } else {
            $erro = "Este email já está cadastrado";
        }
    } else {
        $erro = implode("<br>", $erros);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registro de Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2 class="text-center mb-4">Criar Nova Conta</h2>
        
        <?= exibirMensagemFlash() ?>
        
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>
        
        <form id="formRegistro" method="post" novalidate>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" name="nome" required
                       value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                <div class="invalid-feedback">Por favor, informe seu nome completo.</div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <div class="invalid-feedback">Por favor, informe um email válido.</div>
            </div>
            
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
                <div class="invalid-feedback">A senha deve ter pelo menos 8 caracteres com letras e números.</div>
                <small class="form-text text-muted">Mínimo 8 caracteres com letras e números</small>
            </div>
            
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf" required
                       value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>"
                       oninput="formatarCPFCampo(this)">
                <div class="invalid-feedback">Por favor, informe um CPF válido.</div>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Registrar</button>
            </div>
            
            <div class="mt-3 text-center">
                <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
            </div>
        </form>
    </div>

    <script>
        // Formatar CPF enquanto digita
        function formatarCPFCampo(input) {
            // Remove tudo que não é dígito
            let value = input.value.replace(/\D/g, '');
            
            // Limita a 11 dígitos
            value = value.substring(0, 11);
            
            // Aplica a formatação
            if (value.length > 9) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            } else if (value.length > 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            } else if (value.length > 3) {
                value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            }
            
            input.value = value;
        }
        
        // Validação do formulário no cliente
        document.getElementById('formRegistro').addEventListener('submit', function(event) {
            let formValido = true;
            const campos = [
                {id: 'nome', validar: v => v.length >= 3},
                {id: 'email', validar: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)},
                {id: 'senha', validar: v => v.length >= 8 && /\d/.test(v) && /[a-zA-Z]/.test(v)},
                {id: 'cpf', validar: function(v) {
                    v = v.replace(/\D/g, '');
                    return v.length === 11 && validarCPFJS(v);
                }}
            ];
            
            campos.forEach(campo => {
                const input = document.getElementById(campo.id);
                const valor = input.value.trim();
                
                if (!campo.validar(valor)) {
                    input.classList.add('is-invalid');
                    formValido = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!formValido) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
        
        // Função JavaScript para validar CPF
        function validarCPFJS(cpf) {
            if (/^(\d)\1{10}$/.test(cpf)) return false;
            
            for (let t = 9; t < 11; t++) {
                let d = 0;
                for (let c = 0; c < t; c++) {
                    d += parseInt(cpf[c]) * ((t + 1) - c);
                }
                d = (d * 10) % 11;
                if (d === 10) d = 0;
                if (d !== parseInt(cpf[t])) return false;
            }
            return true;
        }
    </script>
</body>
</html>