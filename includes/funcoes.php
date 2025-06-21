<?php
include 'includes/funcoes.php';
// Funções úteis para todo o sistema

/**
 * Formata um valor monetário
 */
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Formata uma data do banco para o formato brasileiro
 */
function formatarData($data, $incluirHora = false) {
    if (!$data) return '';
    
    $formato = $incluirHora ? 'd/m/Y H:i' : 'd/m/Y';
    return date($formato, strtotime($data));
}

/**
 * Obtém a classe CSS para o status do pedido
 */
function classeStatusPedido($status) {
    switch ($status) {
        case 'pendente': return 'warning';
        case 'processando': return 'info';
        case 'enviado': return 'primary';
        case 'entregue': return 'success';
        case 'cancelado': return 'danger';
        default: return 'secondary';
    }
}

/**
 * Obtém o texto formatado para o método de pagamento
 */
function formatarMetodoPagamento($metodo) {
    $metodos = [
        'cartao_credito' => 'Cartão de Crédito',
        'cartao_debito' => 'Cartão de Débito',
        'pix' => 'PIX',
        'boleto' => 'Boleto',
        'transferencia' => 'Transferência'
    ];
    
    return $metodos[$metodo] ?? ucfirst($metodo);
}

/**
 * Gera um código de rastreio fictício
 */
function gerarCodigoRastreio() {
    $prefixo = ['BR', 'LX', 'ML', 'AA', 'ZZ'];
    $codigo = strtoupper(substr(md5(uniqid()), 0, 10));
    return $prefixo[array_rand($prefixo)] . $codigo;
}

/**
 * Calcula o total do carrinho
 */
function calcularTotalCarrinho($carrinho) {
    $total = 0;
    foreach ($carrinho as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
    return $total;
}

/**
 * Valida um CPF
 */
function validarCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    // Valida o tamanho
    if (strlen($cpf) != 11) {
        return false;
    }
    
    // Cálculo para validar CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

/**
 * Redireciona com mensagem flash
 */
function redirect($url, $mensagem = null, $tipo = 'success') {
    if ($mensagem) {
        $_SESSION['flash_mensagem'] = $mensagem;
        $_SESSION['flash_tipo'] = $tipo;
    }
    header("Location: $url");
    exit;
}

/**
 * Exibe mensagens flash
 */
function exibirMensagemFlash() {
    if (isset($_SESSION['flash_mensagem'])) {
        $tipo = $_SESSION['flash_tipo'] ?? 'success';
        $mensagem = $_SESSION['flash_mensagem'];
        
        unset($_SESSION['flash_mensagem']);
        unset($_SESSION['flash_tipo']);
        
        return "<div class='alert alert-$tipo'>$mensagem</div>";
    }
    return '';
}