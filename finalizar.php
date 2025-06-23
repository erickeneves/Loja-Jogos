<?php
session_start();
include 'includes/funcoes.php';
include 'header.php';

// Verifica se o carrinho está vazio
if (empty($_SESSION['carrinho'])) {
    redirect('index.php', 'Seu carrinho está vazio.', 'warning');
}

// Aqui você implementaria a lógica de salvar no banco de dados
// Para simplificar, apenas exibiremos uma mensagem de sucesso

// Limpa o carrinho após finalizar
unset($_SESSION['carrinho']);

include 'header.php';
?>

<div class="container py-5 text-center">
    <h1 class="mb-4">Aluguel Finalizado com Sucesso!</h1>
    
    <div class="alert alert-success" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        Obrigado por alugar conosco. Seus jogos estão a caminho!
    </div>
    
    <p>Um e-mail de confirmação foi enviado para você com os detalhes do aluguel.</p>
    
    <a href="index.php" class="btn btn-primary">Voltar à Loja</a>
</div>

<?php include 'footer.php'; ?>