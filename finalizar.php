<?php
session_start();
include 'includes/conexao.php';
include 'includes/funcoes.php';

// Verificar se o cliente está logado
if (!isset($_SESSION['cliente_id'])) {
    $_SESSION['redirect_to'] = 'finalizar.php';
    redirect('login.php', 'Por favor, faça login para finalizar sua compra.');
}

// Verificar se há itens no carrinho
if (empty($_SESSION['carrinho'])) {
    redirect('carrinho.php', 'Seu carrinho está vazio!', 'warning');
}

// Buscar endereços do cliente
$stmt = $pdo->prepare("SELECT * FROM Enderecos WHERE cliente_id = ?");
$stmt->execute([$_SESSION['cliente_id']]);
$enderecos = $stmt->fetchAll();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $endereco_id = $_POST['endereco_id'];
    $metodo_pagamento = $_POST['metodo_pagamento'];
    
    // Validações
    $erros = [];
    
    if (empty($endereco_id)) $erros[] = "Selecione um endereço de entrega";
    if (empty($metodo_pagamento)) $erros[] = "Selecione um método de pagamento";
    
    if (empty($erros)) {
        // Criar pedido
        $total = calcularTotalPedido($_SESSION['carrinho']);
        
        try {
            $pdo->beginTransaction();
            
            // Inserir pedido
            $stmt = $pdo->prepare("INSERT INTO Pedidos (cliente_id, endereco_entrega_id, valor_total) 
                                   VALUES (?, ?, ?)");
            $stmt->execute([
                $_SESSION['cliente_id'], 
                $endereco_id, 
                $total
            ]);
            $pedido_id = $pdo->lastInsertId();
            
            // Inserir itens do pedido
            foreach ($_SESSION['carrinho'] as $produto_id => $item) {
                $stmt = $pdo->prepare("INSERT INTO Pedido_Itens (pedido_id, produto_id, quantidade, preco_unitario)
                                       VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $pedido_id,
                    $produto_id,
                    $item['quantidade'],
                    $item['preco']
                ]);
                
                // Atualizar estoque
                $stmt = $pdo->prepare("UPDATE Produtos SET estoque = estoque - ? WHERE produto_id = ?");
                $stmt->execute([$item['quantidade'], $produto_id]);
            }
            
            // Registrar pagamento
            $stmt = $pdo->prepare("INSERT INTO Pagamentos (pedido_id, metodo, valor, status)
                                   VALUES (?, ?, ?, 'pendente')");
            $stmt->execute([$pedido_id, $metodo_pagamento, $total]);
            
            $pdo->commit();
            
            // Limpar carrinho
            unset($_SESSION['carrinho']);
            
            redirect('obrigado.php?pedido='.$pedido_id, 'Compra finalizada com sucesso!');
        } catch (Exception $e) {
            $pdo->rollBack();
            $erro = "Erro ao processar pedido: " . $e->getMessage();
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
    <title>Finalizar Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .checkout-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .address-card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .address-card:hover, .address-card.selected {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        .payment-method {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover, .payment-method.selected {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h2 class="text-center mb-4">Finalizar Compra</h2>
        
        <?= exibirMensagemFlash() ?>
        
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>
        
        <form id="formFinalizar" method="post">
            <!-- Seção de Endereços -->
            <div class="mb-4">
                <h3 class="mb-3">Selecione o endereço de entrega</h3>
                
                <?php if (empty($enderecos)): ?>
                    <div class="alert alert-warning">
                        Você não possui endereços cadastrados. 
                        <a href="adicionar_endereco.php">Adicionar endereço</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($enderecos as $endereco): ?>
                            <div class="col-md-6">
                                <div class="address-card" 
                                     onclick="selecionarEndereco(this, <?= $endereco['endereco_id'] ?>)">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="endereco_id" 
                                               id="endereco<?= $endereco['endereco_id'] ?>" 
                                               value="<?= $endereco['endereco_id'] ?>" required>
                                        <label class="form-check-label" for="endereco<?= $endereco['endereco_id'] ?>">
                                            <strong><?= htmlspecialchars($endereco['logradouro']) ?>, 
                                            <?= htmlspecialchars($endereco['numero']) ?></strong><br>
                                            <?= htmlspecialchars($endereco['complemento']) ?><br>
                                            <?= htmlspecialchars($endereco['bairro']) ?><br>
                                            <?= htmlspecialchars($endereco['cidade']) ?> - 
                                            <?= htmlspecialchars($endereco['estado']) ?><br>
                                            CEP: <?= formatarCEP($endereco['cep']) ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Seção de Pagamento -->
            <div class="mb-4">
                <h3 class="mb-3">Método de Pagamento</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="payment-method" onclick="selecionarPagamento(this, 'pix')">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" 
                                       id="pix" value="pix" required>
                                <label class="form-check-label" for="pix">
                                    <strong>PIX</strong><br>
                                    Pagamento instantâneo
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="payment-method" onclick="selecionarPagamento(this, 'cartao_credito')">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" 
                                       id="cartao_credito" value="cartao_credito">
                                <label class="form-check-label" for="cartao_credito">
                                    <strong>Cartão de Crédito</strong><br>
                                    Parcele em até 12x
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="payment-method" onclick="selecionarPagamento(this, 'cartao_debito')">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" 
                                       id="cartao_debito" value="cartao_debito">
                                <label class="form-check-label" for="cartao_debito">
                                    <strong>Cartão de Débito</strong><br>
                                    Pagamento à vista
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="payment-method" onclick="selecionarPagamento(this, 'boleto')">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" 
                                       id="boleto" value="boleto">
                                <label class="form-check-label" for="boleto">
                                    <strong>Boleto Bancário</strong><br>
                                    Pagamento em até 3 dias
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resumo do Pedido -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="h5 mb-0">Resumo do Pedido</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço Unitário</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['carrinho'] as $produto_id => $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['nome']) ?></td>
                                    <td><?= $item['quantidade'] ?></td>
                                    <td><?= formatarMoeda($item['preco']) ?></td>
                                    <td><?= formatarMoeda($item['preco'] * $item['quantidade']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th><?= formatarMoeda(calcularTotalPedido($_SESSION['carrinho'])) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Finalizar Compra</button>
            </div>
        </form>
    </div>

    <script>
        // Selecionar endereço
        function selecionarEndereco(element, id) {
            document.querySelectorAll('.address-card').forEach(card => {
                card.classList.remove('selected');
            });
            element.classList.add('selected');
            document.getElementById('endereco' + id).checked = true;
        }
        
        // Selecionar método de pagamento
        function selecionarPagamento(element, id) {
            document.querySelectorAll('.payment-method').forEach(method => {
                method.classList.remove('selected');
            });
            element.classList.add('selected');
            document.getElementById(id).checked = true;
        }
        
        // Validação do formulário
        document.getElementById('formFinalizar').addEventListener('submit', function(event) {
            let formValido = true;
            
            // Verificar se endereço foi selecionado
            if (!document.querySelector('input[name="endereco_id"]:checked')) {
                formValido = false;
                document.querySelector('.address-card').classList.add('border-danger');
            }
            
            // Verificar se método de pagamento foi selecionado
            if (!document.querySelector('input[name="metodo_pagamento"]:checked')) {
                formValido = false;
                document.querySelector('.payment-method').classList.add('border-danger');
            }
            
            if (!formValido) {
                event.preventDefault();
                alert('Por favor, selecione um endereço e um método de pagamento');
            }
        });
    </script>
</body>
</html>