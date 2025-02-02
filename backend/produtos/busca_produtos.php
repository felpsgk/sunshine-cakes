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
        echo "Erro na consulta ou tabela vazia.";
    }
    return $produtos;
}
?>