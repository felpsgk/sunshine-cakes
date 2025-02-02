<?php
include '../db/start_db_conn.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo json_encode(["success" => true, "message" => "Produto excluído com sucesso."]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao excluir o produto."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "ID inválido."]);
}
?>
