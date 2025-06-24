<?php
ob_start();
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameRent - Aluguel de Jogos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-dark:rgb(18, 18, 18);
            --secondary-dark: #1e1e1e;
            --accent-color: #0d6efd;
            --text-light: #f8f9fa;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background-color: var(--primary-dark) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        .logo-header {
            height:90px;
            transition: transform 0.3s ease;
        }
        
        .logo-header:hover {
            transform: scale(1.05);
        }
        
        .carrinho-link {
            position: relative;
            color: var(--text-light) !important;
            transition: color 0.3s ease;
        }
        
        .carrinho-link:hover {
            color: var(--accent-color) !important;
        }
        
        .carrinho-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--accent-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .nav-link {
            color: var(--text-light) !important;
            transition: color 0.3s ease;
            font-weight: 500;
        }
        
        .nav-link:hover {
            color: var(--accent-color) !important;
        }
        
        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        
        .game-card {
            background: linear-gradient(145deg, #1e1e1e, #121212);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            color: var(--text-light);
        }
        
        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        
        .game-card img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        
        .game-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .game-platform {
            font-size: 0.9rem;
            color: #aaa;
        }
        
        .game-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #4CAF50;
        }
        .badge-admin {
            background-color: #dc3545;
            font-size: 0.6rem;
            vertical-align: super;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
                                    <span class="badge badge-admin">Admin</span>
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
                        <a class="nav-link carrinho-link" href="carrinho.php">
                            <i class="bi bi-cart"></i>
                            <?php if (isset($_SESSION['carrinho']) && count($_SESSION['carrinho']) > 0) : ?>
                                <span class="carrinho-count"><?= count($_SESSION['carrinho']) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-4">