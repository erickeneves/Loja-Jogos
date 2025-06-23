<?php
// Remova essas linhas antigas - não estão sendo usadas
// include 'includes/funcoes.php';
// define('DB_HOST', 'localhost:3306');
// define('DB_USER', 'seu_usuario');
// define('DB_PASSWORD', 'sua_senha');
// define('DB_NAME', 'nome_banco');

$host = 'localhost';
$port = '3306'; 
$dbname = 'gamerent'; // NOME DO SEU BANCO
$usuario = 'root';         // SEU USUÁRIO
$senha = 'erick';          // SUA SENHA

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("🚨 Erro de conexão: " . $e->getMessage());
}
?>