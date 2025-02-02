<?php
header('Content-Type: application/json'); // Define resposta como JSON
include '../db/start_db_conn.php';

global $pdo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome_produto'], $_POST['preco'], $_POST['peso'])) {
    $nome = $_POST['nome_produto'];
    $preco = $_POST['preco'];
    $peso = $_POST['peso'];

    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, peso) VALUES (?, ?, ?)");

    if ($stmt->execute([$nome, $preco, $peso])) {
        echo json_encode(["success" => true, "message" => "Produto cadastrado com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao cadastrar o produto."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Dados inválidos!"]);
}
$stmt->close();
$pdo = null;

header('Location: ../../frontend/produtos.php');
?>