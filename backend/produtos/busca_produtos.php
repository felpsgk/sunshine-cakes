<?php
function buscarProdutos($pdo) {
    // Busca os produtos cadastrados
    $stmt = $pdo->prepare("SELECT * FROM produtos ORDER BY id DESC");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $produtos;
}
?>