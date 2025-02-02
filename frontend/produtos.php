<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include '../backend/db/start_db_conn.php';
include './include/head.php'; // Inclui o arquivo head.php 
?>

<body>
    <!-- Navbar -->
    <?php include './include/navbar.php'; // Inclui a navbar.php ?>
    <div class="container">
        <h2 class="text-center">Gerenciar Produtos</h2>

        <!-- Formulário para cadastrar um novo produto -->
        <div class="card p-3 mb-4">
            <h4>Cadastrar Novo Produto</h4>
            <!-- ALERTA DE RESPOSTA -->
            <div id="alerta" class="alert d-none alert-dismissible fade show" role="alert">
                <span id="alertaMensagem"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
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
                
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">Voltar para o Dashboard</a>
    </div>
    <!-- jQuery e DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Função para atualizar a tabela com produtos -->
    <script>
        function atualizarTabelaProdutos() {
            fetch("../backend/produtos/busca_produtos.php")
                .then(response => response.json())
                .then(data => {
                    // Obtém a instância do DataTable
                    let tabela = $('#produtosTable').DataTable();
                    tabela.clear(); // Limpa os dados existentes na tabela

                    // Itera sobre os dados e adiciona cada produto na tabela
                    data.forEach(produto => {
                        tabela.row.add([
                            produto.id,
                            produto.nome,
                            `R$ ${parseFloat(produto.preco).toFixed(2).replace('.', ',')}`,
                            produto.peso
                        ]);
                    });

                    tabela.draw(); // Redesenha a tabela com os novos dados
                })
                .catch(error => console.error("Erro ao atualizar a tabela:", error));
        }
    </script>

    <!-- Script para inicialização do DataTable e eventos -->
    <script>
        $(document).ready(function () {
            // Inicializa o DataTable
            $('#produtosTable').DataTable({
                "pageLength": 20,
                "lengthMenu": [20, 30, 50, 100],
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

            // Preenche a tabela com os produtos ao carregar a página
            atualizarTabelaProdutos();

            // Evento de submissão do formulário
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
                        alerta.classList.remove("d-none", "alert-success", "alert-danger");
                        alerta.classList.add(data.success ? "alert-success" : "alert-danger");
                        document.getElementById("alertaMensagem").innerHTML = data.message;

                        // Faz o alerta desaparecer após 5 segundos
                        setTimeout(() => {
                            alerta.classList.add("d-none");
                        }, 5000);

                        // Se o cadastro for bem-sucedido, limpa o formulário e atualiza a tabela
                        if (data.success) {
                            document.getElementById("produtoForm").reset();
                            atualizarTabelaProdutos();
                        }
                    })
                    .catch(error => console.error("Erro:", error));
            });
        });
    </script>

</body>

</html>