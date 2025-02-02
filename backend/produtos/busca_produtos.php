<?php
include '../db/start_db_conn.php';
function buscarProdutos()
{
    global $conn;

    $sql = "SELECT * FROM produtos";
    $result = $conn->query($sql);

    $produtos = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }
    }
    return $produtos;
}

header('Content-Type: application/json');
echo json_encode(buscarProdutos());
?>