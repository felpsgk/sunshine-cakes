<?php
session_start();
$host = 'localhost';
$db = 'felpst09_sunshine_cakes';
$user = 'felpst09_sunshinecakes';
$pass = '976431852Gk@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Criar tabela de usuários
$query = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL
)";
$pdo->exec($query);

// Criar tabela de produtos
$query = "CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    peso DECIMAL(10,2) NOT NULL
)";
$pdo->exec($query);

// Criar tabela de receitas
$query = "CREATE TABLE IF NOT EXISTS receitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    custo_total DECIMAL(10,2) NOT NULL,
    lucro DECIMAL(10,2) NOT NULL
)";
$pdo->exec($query);

// Criar tabela de ingredientes por receita
$query = "CREATE TABLE IF NOT EXISTS receita_ingredientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receita_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (receita_id) REFERENCES receitas(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
)";
$pdo->exec($query);

// Função para cadastrar usuário
function cadastrarUsuario($pdo, $nome, $email, $senha) {
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    return $stmt->execute([$nome, $email, $senhaHash]);
}

// Função para autenticar usuário
function autenticarUsuario($pdo, $email, $senha) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = SHA1(?)");
    $stmt->execute([$email, $senha]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($usuario) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        print_r($usuario['id']);
        print_r($usuario['nome']);
        return true;
    }
    return false;
}
?>