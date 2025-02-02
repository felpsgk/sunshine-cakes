<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome_produto'], $_POST['preco'], $_POST['quantidade'])) {
    $nome = $_POST['nome_produto'];
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade'];

    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, quantidade) VALUES (?, ?, ?)");
    if ($stmt->execute([$nome, $preco, $quantidade])) {
        echo "<script>alert('Produto cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar o produto.');</script>";
    }
}

?>