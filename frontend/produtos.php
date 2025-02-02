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
        <!-- Modal para Edição -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editForm" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Produto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label for="editNome" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" name="nome_produto" id="editNome" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPreco" class="form-label">Preço (R$)</label>
                            <input type="number" step="0.01" class="form-control" name="preco" id="editPreco" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPeso" class="form-label">Peso (gr) / Quantidade</label>
                            <input type="number" class="form-control" name="peso" id="editPeso" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
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
        // Função para abrir o modal de edição e preencher os campos
        function openEditModal(produto) {
            document.getElementById("editId").value = produto.id;
            document.getElementById("editNome").value = produto.nome;
            document.getElementById("editPreco").value = produto.preco;
            document.getElementById("editPeso").value = produto.peso;

            // Abre o modal (Bootstrap 5)
            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }

        // Função para excluir um produto
        function excluirProduto(id) {
            if (confirm("Tem certeza que deseja excluir este produto?")) {
                fetch("../backend/produtos/deleta_produto.php?id=" + id)
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        atualizarTabelaProdutos();
                    })
                    .catch(error => console.error("Erro ao excluir o produto:", error));
            }
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
                },// Define que a última coluna não é ordenável (os botões de ação)
                "columnDefs": [
                    { "orderable": false, "targets": -1 }
                ]
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

            // Evento de submissão do formulário de edição (modal)
            document.getElementById("editForm").addEventListener("submit", function (event) {
                event.preventDefault();
                let formData = new FormData(this);
                fetch("../backend/produtos/atualiza_produto.php", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        // Fecha o modal manualmente
                        var editModalEl = document.getElementById('editModal');
                        var modalInstance = bootstrap.Modal.getInstance(editModalEl);
                        modalInstance.hide();
                        atualizarTabelaProdutos();
                    })
                    .catch(error => console.error("Erro ao atualizar o produto:", error));
            });
        });
    </script>

</body>

</html>