<?php
session_start();
include 'includes/conexao.php';
include 'includes/funcoes.php';

if (!isset($_SESSION['cliente_id'])) {
    redirect('login.php', 'Por favor, faça login para adicionar um endereço.');
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cep = $_POST['cep'];
    $logradouro = $_POST['logradouro'];
    $numero = $_POST['numero'];
    $complemento = $_POST['complemento'] ?? '';
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $tipo = $_POST['tipo'];
    
    $erros = [];
    
    if (empty($cep)) $erros[] = "CEP é obrigatório";
    if (empty($logradouro)) $erros[] = "Logradouro é obrigatório";
    if (empty($numero)) $erros[] = "Número é obrigatório";
    if (empty($bairro)) $erros[] = "Bairro é obrigatório";
    if (empty($cidade)) $erros[] = "Cidade é obrigatória";
    if (empty($estado)) $erros[] = "Estado é obrigatório";
    if (empty($tipo)) $erros[] = "Tipo de endereço é obrigatório";
    
    if (empty($erros)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO Enderecos 
                                  (cliente_id, cep, logradouro, numero, complemento, bairro, cidade, estado, tipo)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['cliente_id'],
                $cep,
                $logradouro,
                $numero,
                $complemento,
                $bairro,
                $cidade,
                $estado,
                $tipo
            ]);
            
            redirect('minha_conta.php', 'Endereço adicionado com sucesso!');
        } catch (PDOException $e) {
            $erro = "Erro ao salvar endereço: " . $e->getMessage();
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
    <title>Adicionar Endereço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .address-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="address-container">
        <h2 class="text-center mb-4">Adicionar Novo Endereço</h2>
        
        <?= exibirMensagemFlash() ?>
        
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>
        
        <form id="formEndereco" method="post" novalidate>
            <div class="mb-3">
                <label for="cep" class="form-label">CEP</label>
                <input type="text" class="form-control" id="cep" name="cep" 
                       value="<?= htmlspecialchars($_POST['cep'] ?? '') ?>" 
                       oninput="formatarCEPCampo(this)" required>
                <div class="invalid-feedback">Por favor, informe um CEP válido.</div>
            </div>
            
            <div class="row">
                <div class="col-md-9 mb-3">
                    <label for="logradouro" class="form-label">Logradouro</label>
                    <input type="text" class="form-control" id="logradouro" name="logradouro" 
                           value="<?= htmlspecialchars($_POST['logradouro'] ?? '') ?>" required>
                    <div class="invalid-feedback">Por favor, informe o logradouro.</div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" class="form-control" id="numero" name="numero" 
                           value="<?= htmlspecialchars($_POST['numero'] ?? '') ?>" required>
                    <div class="invalid-feedback">Por favor, informe o número.</div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="complemento" class="form-label">Complemento</label>
                <input type="text" class="form-control" id="complemento" name="complemento" 
                       value="<?= htmlspecialchars($_POST['complemento'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
                <label for="bairro" class="form-label">Bairro</label>
                <input type="text" class="form-control" id="bairro" name="bairro" 
                       value="<?= htmlspecialchars($_POST['bairro'] ?? '') ?>" required>
                <div class="invalid-feedback">Por favor, informe o bairro.</div>
            </div>
            
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" 
                           value="<?= htmlspecialchars($_POST['cidade'] ?? '') ?>" required>
                    <div class="invalid-feedback">Por favor, informe a cidade.</div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="">Selecione</option>
                        <option value="AC">Acre</option>
                        <option value="AL">Alagoas</option>
                        <option value="AP">Amapá</option>
                        <option value="AM">Amazonas</option>
                        <option value="BA">Bahia</option>
                        <option value="CE">Ceará</option>
                        <option value="DF">Distrito Federal</option>
                        <option value="ES">Espírito Santo</option>
                        <option value="GO">Goiás</option>
                        <option value="MA">Maranhão</option>
                        <option value="MT">Mato Grosso</option>
                        <option value="MS">Mato Grosso do Sul</option>
                        <option value="MG">Minas Gerais</option>
                        <option value="PA">Pará</option>
                        <option value="PB">Paraíba</option>
                        <option value="PR">Paraná</option>
                        <option value="PE">Pernambuco</option>
                        <option value="PI">Piauí</option>
                        <option value="RJ">Rio de Janeiro</option>
                        <option value="RN">Rio Grande do Norte</option>
                        <option value="RS">Rio Grande do Sul</option>
                        <option value="RO">Rondônia</option>
                        <option value="RR">Roraima</option>
                        <option value="SC">Santa Catarina</option>
                        <option value="SP">São Paulo</option>
                        <option value="SE">Sergipe</option>
                        <option value="TO">Tocantins</option>
                    </select>
                    <div class="invalid-feedback">Por favor, selecione o estado.</div>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Tipo de Endereço</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo" id="cobranca" value="cobrança" required>
                    <label class="form-check-label" for="cobranca">Cobrança</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo" id="entrega" value="entrega">
                    <label class="form-check-label" for="entrega">Entrega</label>
                </div>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Salvar Endereço</button>
            </div>
        </form>
    </div>

    <script>
        function formatarCEPCampo(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 8) value = value.substring(0, 8);
            
            if (value.length > 5) {
                value = value.replace(/(\d{5})(\d{1,3})/, '$1-$2');
            }
            input.value = value;
        }
        
        document.getElementById('formEndereco').addEventListener('submit', function(event) {
            let formValido = true;
            const campos = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado'];
            
            campos.forEach(campoId => {
                const campo = document.getElementById(campoId);
                if (!campo.value.trim()) {
                    campo.classList.add('is-invalid');
                    formValido = false;
                } else {
                    campo.classList.remove('is-invalid');
                }
            });
            
            if (!document.querySelector('input[name="tipo"]:checked')) {
                formValido = false;
                document.getElementById('cobranca').classList.add('is-invalid');
            } else {
                document.getElementById('cobranca').classList.remove('is-invalid');
            }
            
            if (!formValido) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    </script>
</body>
</html>