<?php
include '../db/start_db_conn.php';
global $pdo;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM receitas WHERE id = ?")->execute([$id]);
        $pdo->commit();
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
