<?php
buscarProdutos();
function buscarProdutos()
{
    include '../db/start_db_conn.php';
    // Busca os produtos cadastrados
    $stmt = $pdo->prepare("SELECT * FROM produtos ORDER BY id DESC");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print ("" . $produtos . "");
    return $produtos;
}
?>