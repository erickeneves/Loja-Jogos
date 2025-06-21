<?php
include 'includes/funcoes.php';

$host = 'localhost';
$port = '3307'; // Especifique a porta aqui
$dbname = 'EcommerceDB';
$usuario = 'root';
$senha = ''; // Senha do seu MySQL

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>