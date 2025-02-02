<?php
include '../db/start_db_conn.php';
global $pdo;
echo "chegou aqui";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome_produto'], $_POST['preco'], $_POST['peso'])) {
    echo "entrou if";
    $nome = $_POST['nome_produto'];
    $preco = $_POST['preco'];
    $peso = $_POST['peso'];

    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, peso) VALUES (?, ?, ?)");
    if ($stmt->execute([$nome, $preco, $peso])) {
        echo "<script>alert('Produto cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar o produto.');</script>";
    }
}

?>