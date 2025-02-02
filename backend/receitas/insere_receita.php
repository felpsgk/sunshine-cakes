<?php
header('Content-Type: application/json');
include '../db/start_db_conn.php'; // Certifique-se de que este arquivo inicializa o objeto $pdo
global $pdo;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se os dados obrigatórios foram enviados
    if (isset($_POST['nome_receita'], $_POST['produtos'], $_POST['quantidades'], $_POST['gastos_incalculaveis'], $_POST['utens_perdas'], $_POST['mao_obra'], $_POST['margem_lucro'])) {

        // Dados da receita
        $nome_receita = trim($_POST['nome_receita']);
        $produtos = $_POST['produtos'];       // Array de IDs de produtos
        $quantidades = $_POST['quantidades']; // Array de quantidades utilizadas

        // Custos e lucros
        $gastos_incalculaveis = floatval($_POST['gastos_incalculaveis']);
        $utens_perdas = floatval($_POST['utens_perdas']);
        $mao_obra = floatval($_POST['mao_obra']);
        $margem_lucro = floatval($_POST['margem_lucro']);

        // Taxa do iFood (se informado)
        $taxa_ifood = 0.0;
        if (isset($_POST['taxa_ifood']) && !empty($_POST['taxa_ifood'])) {
            $taxa_ifood = floatval($_POST['taxa_ifood']);
        }

        try {
            // Inicia transação
            $pdo->beginTransaction();

            // 1. Insere a receita na tabela "receitas"
            $stmt = $pdo->prepare("INSERT INTO receitas (nome) VALUES (?)");
            $stmt->execute([$nome_receita]);
            $receita_id = $pdo->lastInsertId();

            // 2. Insere os produtos utilizados na tabela "receita_ingredientes"
            $stmt_produto = $pdo->prepare("INSERT INTO receita_ingredientes (receita_id, produto_id, quantidade) VALUES (?, ?, ?)");
            $numProdutos = count($produtos);
            for ($i = 0; $i < $numProdutos; $i++) {
                // Converte a quantidade para float, se necessário
                $quantidade = floatval($quantidades[$i]);
                $produto_id = $produtos[$i];
                $stmt_produto->execute([$receita_id, $produto_id, $quantidade]);
            }

            // 3. Insere os percentuais e informações de custo/lucro na tabela "receita_lucro"
            $stmt_lucro = $pdo->prepare("INSERT INTO receita_lucro (receita_id, gastos_incalculaveis, utens_perdas, mao_obra, margem_lucro, taxa_ifood) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_lucro->execute([$receita_id, $gastos_incalculaveis, $utens_perdas, $mao_obra, $margem_lucro, $taxa_ifood]);

            // Confirma a transação
            $pdo->commit();

            echo json_encode(["success" => true, "message" => "Receita criada com sucesso!"]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(["success" => false, "message" => "Erro ao criar a receita: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Dados inválidos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
}
?>
