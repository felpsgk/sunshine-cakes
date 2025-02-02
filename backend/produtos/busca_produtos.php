<?php
include '../db/start_db_conn.php';
function buscarProdutos()
{
    global $pdo;
    // Busca os produtos cadastrados
    $stmt = $pdo->prepare("SELECT * FROM produtos ORDER BY id DESC");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$produtos) {
        echo json_encode(["erro" => "Erro na consulta ou tabela vazia."]);
        exit();
    }

    return json_encode($produtos);
}
?>