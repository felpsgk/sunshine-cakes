<?php
include '../db/start_db_conn.php';
global $pdo;

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $pdo->prepare("SELECT * FROM receitas WHERE id = ?");
    $stmt->execute([$id]);
    $receita = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT p.nome, ri.quantidade FROM receita_ingredientes ri 
                           JOIN produtos p ON ri.produto_id = p.id WHERE ri.receita_id = ?");
    $stmt->execute([$id]);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["nome" => $receita["nome"], "rendimento" => $receita["rendimento"], "produtos" => $produtos]);
}
?>
