<?php
ob_start();
session_start();
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';
$base_url = rtrim($base_url, '/') . '/';

$carrinho_count = 0;
if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
    $carrinho_count = count($_SESSION['carrinho']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloPagina ?? 'GameRent' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/styles.css">
    <link rel="stylesheet" href="<?= $base_url ?>css/card-effects.css">
    <style>
        body {
                background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
                color: #fff;
                min-height: 100vh;
                padding-bottom: 60px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

        .card {
            background: rgba(30, 30, 46, 0.8);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-header {
            background: rgba(13, 42, 89, 0.8);
        }
        .navbar {
            background: rgba(10, 10, 30, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .carrinho-icon {
            position: relative;
            display: inline-block;
            margin-left: 20px;
        }
        
        .carrinho-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .logo-header {
            height: 100px;
            transition: transform 0.3s;
        }
        
        .logo-header:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="img/bannerpng.png" alt="GameRent" class="logo-header">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <li class="nav-item">
                            <span class="nav-link">
                                <?= htmlspecialchars($_SESSION['usuario']['nome']) ?>
                                <?php if ($_SESSION['usuario']['tipo'] === 'admin'): ?>
                                    <span class="badge bg-danger ms-2">Admin</span>
                                <?php endif; ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/login_admin.php">Admin</a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link carrinho-icon" href="carrinho.php">
                            <i class="bi bi-cart3 fs-4"></i>
                            <?php if ($carrinho_count > 0): ?>
                                <span class="carrinho-count"><?= $carrinho_count ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container py-4">