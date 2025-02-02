<?php
header('Content-Type: application/json');
include '../db/start_db_conn.php'; // Certifique-se de que este arquivo inicializa o objeto $pdo
global $pdo;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se os dados obrigatórios foram enviados
    if (
        isset(
        $_POST['nome_receita'],
        $_POST['rendimento'],
        $_POST['produtos'],
        $_POST['quantidades'],
        $_POST['gastos_incalculaveis'],
        $_POST['utens_perdas'],
        $_POST['mao_obra'],
        $_POST['margem_lucro']
    )
    ) {
        // Dados da receita
        $nome_receita = trim($_POST['nome_receita']);
        $rendimento = floatval($_POST['rendimento']); // Número de unidades que a receita renderá
        $produtos = $_POST['produtos'];       // Array de IDs de produtos
        $quantidades = $_POST['quantidades']; // Array de quantidades utilizadas para a receita

        // Custos e lucros (em porcentagem)
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

            /* 
             * 1. Insere a receita na tabela "receitas" 
             *    (Inicialmente, insere apenas nome e rendimento. Os valores de custo serão atualizados após os cálculos.)
             */
            $stmt = $pdo->prepare("INSERT INTO receitas (nome, rendimento) VALUES (?, ?)");
            $stmt->execute([$nome_receita, $rendimento]);
            $receita_id = $pdo->lastInsertId();

            // Inicializa o custo da receita (soma dos valor_proporcional de cada ingrediente)
            $custo_receita = 0.0;

            /*
             * 2. Insere os produtos utilizados na tabela "receita_ingredientes"
             *    Para cada produto, calcula o valor_proporcional:
             *      valor_proporcional = (quantidade utilizada * preco do produto) / quantidade real do produto (peso)
             */
            $stmt_produto = $pdo->prepare("INSERT INTO receita_ingredientes (receita_id, produto_id, quantidade, valor_proporcional) VALUES (?, ?, ?, ?)");
            // Prepara uma query para buscar os dados do produto
            $stmt_prod_info = $pdo->prepare("SELECT preco, peso FROM produtos WHERE id = ?");
            $numProdutos = count($produtos);
            for ($i = 0; $i < $numProdutos; $i++) {
                $quantidade_utilizada = floatval($quantidades[$i]);
                $produto_id = $produtos[$i];

                // Busca os dados do produto para obter o preço e a quantidade real (peso)
                $stmt_prod_info->execute([$produto_id]);
                $produto = $stmt_prod_info->fetch(PDO::FETCH_ASSOC);
                if (!$produto) {
                    throw new Exception("Produto de ID $produto_id não encontrado.");
                }
                $preco = floatval($produto['preco']);
                $peso_real = floatval($produto['peso']);
                if ($peso_real <= 0) {
                    throw new Exception("Produto de ID $produto_id possui quantidade real inválida.");
                }
                // Calcula o valor proporcional
                $valor_proporcional = ($quantidade_utilizada * $preco) / $peso_real;
                $custo_receita += $valor_proporcional;

                // Insere na tabela receita_ingredientes
                $stmt_produto->execute([$receita_id, $produto_id, $quantidade_utilizada, $valor_proporcional]);
            }

            /*
             * 3. Calcula os custos adicionais e os valores finais:
             *    - total_fees_percent: soma de gastos_incalculaveis, utens_perdas, mao_obra e taxa_ifood (em %)
             *    - custo_total: custo_receita + acréscimo das taxas sobre custo_receita
             *    - valor_venda: custo_total dividido pelo rendimento (custo por unidade)
             *    - lucro: custo_receita acrescido da margem de lucro (em %)
             */
            $custo_incalculaveis = $custo_receita * ($gastos_incalculaveis / 100);
            $custo_utens_perdas = $custo_receita * ($utens_perdas / 100);
            $custo_mao_obra = $custo_receita * ($mao_obra / 100);
            echo "valor ".$custo_receita;
            echo "valor1 ".$custo_incalculaveis;
            echo "valor2 ".$custo_utens_perdas;
            echo "valor3 ".$custo_mao_obra;
            if (isset($_POST['taxa_ifood']) && !empty($_POST['taxa_ifood'])) {
                $custo_ifood = $custo_receita * ($taxa_ifood / 100);                
                $custo_total = $custo_receita + $custo_incalculaveis + $custo_utens_perdas + $custo_mao_obra + $custo_ifood;
            } else {
                $custo_total = $custo_receita + $custo_incalculaveis + $custo_utens_perdas + $custo_mao_obra;
            }


            // Evita divisão por zero
            if ($rendimento <= 0) {
                throw new Exception("O rendimento deve ser maior que zero.");
            }
            $valor_venda = $custo_total / $rendimento;
            $lucro = $custo_receita + ($custo_receita * ($margem_lucro / 100));

            /*
             * 4. Atualiza o registro da receita na tabela "receitas" com os valores calculados
             *    (Assumindo que as colunas custo_receita, custo_total, valor_venda e lucro existem na tabela receitas)
             */
            $stmt_update = $pdo->prepare("UPDATE receitas SET custo_receita = ?, custo_total = ?, valor_venda = ?, lucro = ? WHERE id = ?");
            $stmt_update->execute([$custo_receita, $custo_total, $valor_venda, $lucro, $receita_id]);

            /*
             * 5. Insere os percentuais e informações de custo/lucro na tabela "receita_lucro"
             */
            $stmt_lucro = $pdo->prepare("INSERT INTO receita_lucro (receita_id, gastos_incalculaveis, utens_perdas, mao_obra, margem_lucro, taxa_ifood) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_lucro->execute([$receita_id, $gastos_incalculaveis, $utens_perdas, $mao_obra, $margem_lucro, $taxa_ifood]);

            // Confirma a transação
            $pdo->commit();

            echo json_encode([
                "success" => true,
                "message" => "Receita criada com sucesso!",
                "dados" => [
                    "receita_id" => $receita_id,
                    "custo_receita" => $custo_receita,
                    "custo_total" => $custo_total,
                    "valor_venda" => $valor_venda,
                    "lucro" => $lucro
                ]
            ]);
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