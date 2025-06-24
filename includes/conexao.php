<?php
$host = 'localhost';
$port = '3306'; 
$dbname = 'gamerent'; 
$usuario = 'root';
$senha = 'erick';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>