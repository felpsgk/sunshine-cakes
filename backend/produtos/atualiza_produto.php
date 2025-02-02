<?php
include '../db/start_db_conn.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nome = $_POST['nome_produto'];
    $preco = $_POST['preco'];
    $peso = $_POST['peso'];
    $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, peso = ? WHERE id = ?");
    if ($stmt->execute([$nome, $preco, $peso, $id])) {
        echo json_encode(["success" => true, "message" => "Produto atualizado com sucesso."]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao atualizar o produto."]);
    }
}
?>
