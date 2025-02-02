<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include '../backend/db/start_db_conn.php';
include '../backend/produtos/busca_produtos.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

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
            <!-- ALERTA DE RESPOSTA (inicialmente oculto) -->
            <div id="alerta" class="alert d-none"></div>
            <form id="produtoForm">
                <div class="mb-3">
                    <label class="form-label">Nome do Produto</label>
                    <input type="text" class="form-control" name="nome_produto" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Preço (R$)</label>
                    <input type="number" step="0.01" class="form-control" name="preco" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Peso (gr) / Quantidade</label>
                    <input type="number" class="form-control" name="peso" required>
                </div>
                <button type="submit" class="btn btn-custom">Cadastrar</button>
            </form>
        </div>

        <!-- Lista de produtos cadastrados com DataTable -->
        <h4>Lista de Produtos</h4>
        <table id="produtosTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço (R$)</th>
                    <th>peso(gr)/quantidade</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $produtos = buscarProdutos();
                foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?= $produto['id']; ?></td>
                        <td><?= $produto['nome']; ?></td>
                        <td>R$ <?= number_format($produto['preco'], 2, ',', '.'); ?></td>
                        <td><?= $produto['peso']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">Voltar para o Dashboard</a>
    </div>
    <script>
        document.getElementById("produtoForm").addEventListener("submit", function (event) {
            event.preventDefault(); // Impede o recarregamento da página

            let formData = new FormData(this); // Coleta os dados do formulário

            fetch("../backend/produtos/insere_produtos.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    let alerta = document.getElementById("alerta");
                    alerta.classList.remove("d-none", "alert-success", "alert-danger"); // Remove classes anteriores
                    alerta.classList.add(data.success ? "alert-success" : "alert-danger"); // Define sucesso ou erro
                    alerta.innerHTML = data.message; // Exibe a mensagem
                    if (data.success) {
                        alerta.innerHTML = "ATUALIZANDO TABELA"; // Exibe a mensagem
                        // Atualiza a tabela com o novo produto sem recarregar a página
                        let tabelaBody = document.querySelector("#produtosTable tbody");
                        let novaLinha = document.createElement("tr");
                        novaLinha.innerHTML = `
                            <td>${data.produto.id}</td>
                            <td>${data.produto.nome}</td>
                            <td>R$ ${parseFloat(data.produto.preco).toFixed(2).replace('.', ',')}</td>
                            <td>${data.produto.peso}</td>
                        `;
                        tabelaBody.appendChild(novaLinha);

                        // Atualiza o DataTable
                        let dataTable = $('#produtosTable').DataTable();

                        // Reinicia o DataTable para que ele reconheça a nova linha
                        dataTable.clear().draw();
                        dataTable.rows.add($(tabelaBody).find('tr')).draw();
                    }
                })
                .catch(error => console.error("Erro:", error));
        });
    </script>

    <!-- jQuery e DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <!-- Inicialização do DataTable -->
    <script>
        $(document).ready(function () {
            $('#produtosTable').DataTable({
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "Nenhum produto encontrado",
                    "info": "Mostrando página _PAGE_ de _PAGES_",
                    "infoEmpty": "Nenhum produto disponível",
                    "infoFiltered": "(filtrado de _MAX_ registros totais)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primeiro",
                        "last": "Último",
                        "next": "Próximo",
                        "previous": "Anterior"
                    }
                }
            });
        });
    </script>

</body>

</html>