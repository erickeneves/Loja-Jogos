<?php
// includes/funcoes.php

/**
 * Formata um valor monetário
 * @param float $valor
 * @return string
 */
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Formata uma data do banco (formato Y-m-d H:i:s) para o formato brasileiro
 * @param string $data
 * @param bool $incluirHora
 * @return string
 */
function formatarData($data, $incluirHora = false) {
    if (empty($data) return '';
    
    $timestamp = strtotime($data);
    if ($incluirHora) {
        return date('d/m/Y H:i', $timestamp);
    }
    return date('d/m/Y', $timestamp);
}

/**
 * Retorna a classe CSS para o status do pedido
 * @param string $status
 * @return string
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
 * Formata o método de pagamento para um texto mais amigável
 * @param string $metodo
 * @return string
 */
function formatarMetodoPagamento($metodo) {
    $metodos = [
        'cartao_credito' => 'Cartão de Crédito',
        'cartao_debito' => 'Cartão de Débito',
        'pix' => 'PIX',
        'boleto' => 'Boleto',
        'transferencia' => 'Transferência Bancária'
    ];
    return $metodos[$metodo] ?? ucwords(str_replace('_', ' ', $metodo));
}

/**
 * Valida um CPF
 * @param string $cpf
 * @return bool
 */
function validarCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se é uma sequência de dígitos repetidos
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Faz o cálculo para validar o CPF
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
 * Formata um CPF para o padrão XXX.XXX.XXX-XX
 * @param string $cpf
 * @return string
 */
function formatarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    return substr($cpf, 0, 3) . '.' . 
           substr($cpf, 3, 3) . '.' . 
           substr($cpf, 6, 3) . '-' . 
           substr($cpf, 9, 2);
}

/**
 * Formata um CEP para o padrão XXXXX-XXX
 * @param string $cep
 * @return string
 */
function formatarCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
}

/**
 * Valida um e-mail
 * @param string $email
 * @return bool
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Redireciona o usuário para uma URL com uma mensagem flash
 * @param string $url
 * @param string|null $mensagem
 * @param string $tipo
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
 * Exibe uma mensagem flash se existir
 * @return string
 */
function exibirMensagemFlash() {
    if (isset($_SESSION['flash_mensagem'])) {
        $mensagem = $_SESSION['flash_mensagem'];
        $tipo = $_SESSION['flash_tipo'] ?? 'success';
        unset($_SESSION['flash_mensagem']);
        unset($_SESSION['flash_tipo']);
        return "<div class='alert alert-{$tipo}'>{$mensagem}</div>";
    }
    return '';
}

/**
 * Gera uma URL amigável para um produto
 * @param string $nome
 * @return string
 */
function gerarSlug($nome) {
    $slug = strtolower($nome);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

/**
 * Calcula o valor total de um pedido
 * @param array $itens
 * @return float
 */
function calcularTotalPedido($itens) {
    $total = 0;
    foreach ($itens as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
    return $total;
}

/**
 * Valida uma senha (mínimo 8 caracteres, com letra e número)
 * @param string $senha
 * @return bool
 */
function validarSenha($senha) {
    return strlen($senha) >= 8 && preg_match('/[A-Za-z]/', $senha) && preg_match('/[0-9]/', $senha);
}