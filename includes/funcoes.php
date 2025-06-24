<?php

/**
 * R$
 * @param float $valor
 * @return string
 */
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * form br data
 * @param string $data
 * @param bool $incluirHora
 * @return string
 */
function formatarData($data, $incluirHora = false) {
    if (empty($data)) return '';
    
    $timestamp = strtotime($data);
    if ($incluirHora) {
        return date('d/m/Y H:i', $timestamp);
    }
    return date('d/m/Y', $timestamp);
}

/**
 * status pedido
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
 * txt pagaamento
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
 * valida CPF
 * @param string $cpf
 * @return bool
 */
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11) {
        return false;
    }

    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

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
 * form cpf
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
 * form cep
 * @param string $cep
 * @return string
 */
function formatarCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
}

/**
 * validacao de email
 * @param string $email
 * @return bool
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * joga usuario com mensagem
 * @param string $url
 * @param string|null $mensagem
 * @param string $tipo
 */
function redirect($url, $mensagem = null, $tipo = 'success') {
    if ($mensagem) {
        $_SESSION['flash_mensagem'] = $mensagem;
        $_SESSION['flash_tipo'] = $tipo;
    }
    
    if (!headers_sent()) {
        header("Location: $url");
        exit;
    } else {
        echo "<script>window.location.href='$url';</script>";
        exit;
    }
}

/**
 * popup
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
 * gerar url
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
 * val total pedido
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
 * senha 
 * @param string $senha
 * @return bool
 */
function validarSenha($senha) {
    return strlen($senha) >= 8 && preg_match('/[A-Za-z]/', $senha) && preg_match('/[0-9]/', $senha);
}