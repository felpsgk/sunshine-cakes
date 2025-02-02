<?php
include '../backend/produtos/busca_produtos.php';
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include '../backend/db/start_db_conn.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fce4ec;
        }

        .container {
            margin-top: 20px;
        }

        .btn-custom {
            background-color: #d7a9a9;
            color: white;
        }

        .btn-custom:hover {
            background-color: #b78b8b;
        }

        .card {
            background: #fff3e0;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="text-center">Gerenciar Produtos</h2>

        <!-- Formulário para cadastrar um novo produto -->
        <div class="card p-3 mb-4">
            <h4>Cadastrar Novo Produto</h4>
            <form method="POST" action="../backend/produtos/produtos.php">
                <div class="mb-3">
                    <label class="form-label">Nome do Produto</label>
                    <input type="text" class="form-control" name="nome_produto" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Preço (R$)</label>
                    <input type="number" step="0.01" class="form-control" name="preco" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantidade</label>
                    <input type="number" class="form-control" name="quantidade" required>
                </div>
                <button type="submit" class="btn btn-custom">Cadastrar</button>
            </form>
        </div>

        <!-- Lista de produtos cadastrados -->
        <h4>Lista de Produtos</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço (R$)</th>
                    <th>Quantidade</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($produtos === false) {
                    echo "<tr><td colspan='4'>Erro ao buscar produtos.</td></tr>";
                } elseif (empty($produtos)) {
                    echo "<tr><td colspan='4'>Nenhum produto encontrado.</td></tr>";
                } else {
                    foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?= $produto['id']; ?></td>
                            <td><?= $produto['nome']; ?></td>
                            <td>R$ <?= number_format($produto['preco'], 2, ',', '.'); ?></td>
                            <td><?= $produto['quantidade']; ?></td>
                        </tr>
                    <?php endforeach;
                } ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary">Voltar para o Dashboard</a>
    </div>

</body>

</html>