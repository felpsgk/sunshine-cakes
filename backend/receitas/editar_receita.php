<?php
include '../db/start_db_conn.php';
global $pdo;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'], $_POST['nome_receita'], $_POST['rendimento'])) {
    $id = intval($_POST['id']);
    $nome_receita = trim($_POST['nome_receita']);
    $rendimento = intval($_POST['rendimento']);

    $stmt = $pdo->prepare("UPDATE receitas SET nome = ?, rendimento = ? WHERE id = ?");
    if ($stmt->execute([$nome_receita, $rendimento, $id])) {
        echo json_encode(["success" => true, "message" => "Receita atualizada com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao atualizar a receita."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Dados inválidos."]);
}
?>
