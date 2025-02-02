<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include '../backend/db/start_db_conn.php';

// Buscar produtos para o dropdown (considera que a tabela "produtos" tem colunas: id, nome, preco, peso)
$stmt = $pdo->prepare("SELECT id, CONCAT(NOME,' - ', peso,'gr/ml/unidade') AS nome, preco FROM produtos ORDER BY nome");
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Buscar receitas
$stmt = $pdo->prepare("SELECT id, nome, custo_total, lucro, custo_receita, valor_venda, rendimento FROM receitas ORDER BY id DESC");
$stmt->execute();
$receitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pdo = null;
include './include/head.php'; // Inclui o arquivo head.php 
?>

<body>
    <!-- Navbar -->
    <?php include './include/navbar.php'; // Inclui a navbar.php ?>
    <div class="container">
        <h2 class="text-center">Criar Receita</h2>
        <div class="card p-4">
            <form id="receitaForm">
                <!-- Dados básicos da receita -->
                <div class="mb-3">
                    <label class="form-label">Nome da Receita</label>
                    <input type="text" name="nome_receita" class="form-control" required>
                </div>

                <!-- Composição da receita: produtos utilizados -->
                <h5>Produtos Utilizados</h5>
                <table class="table" id="tabelaProdutos">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade Utilizada</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Linha inicial -->
                        <tr>
                            <td>
                                <select name="produtos[]" class="form-select" required>
                                    <option value="">Selecione um produto</option>
                                    <?php foreach ($produtos as $produto): ?>
                                        <option value="<?= $produto['id'] ?>" data-preco="<?= $produto['preco'] ?>"
                                            data-peso="<?= $produto['peso'] ?>">
                                            <?= $produto['nome'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="quantidades[]" class="form-control"
                                    placeholder="Quantidade utilizada" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger removerLinha">Remover</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" id="adicionarProduto" class="btn btn-secondary mb-3">Adicionar Produto</button>

                <!-- Percentuais para cálculo de custos e lucro -->
                <h5>Custos e Lucro</h5>
                <div class="mb-3">
                    <label class="form-label">Rendimento desta receita</label>
                    <input type="number" step="1" name="rendimento" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gastos Incalculáveis (%)</label>
                    <input type="number" step="0.01" name="gastos_incalculaveis" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Utensílios e Perdas (%)</label>
                    <input type="number" step="0.01" name="utens_perdas" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mão de Obra (%)</label>
                    <input type="number" step="0.01" name="mao_obra" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Margem de Lucro (%)</label>
                    <input type="number" step="0.01" name="margem_lucro" class="form-control" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="ifoodCheck" name="ifoodCheck">
                    <label class="form-check-label" for="ifoodCheck">Aplicar Taxa do iFood</label>
                </div>
                <div class="mb-3" id="ifoodPercentDiv" style="display: none;">
                    <label class="form-label">Taxa iFood (%)</label>
                    <input type="number" step="0.01" name="taxa_ifood" class="form-control">
                </div>

                <!-- Submissão do formulário -->
                <button type="submit" class="btn btn-custom">Criar Receita</button>
            </form>

            <!-- Área para exibir mensagens (Bootstrap Alert) -->
            <div id="alerta" class="alert d-none mt-3"></div>
            </di>
            <div class="card p-4">
                <h2 class="text-center">Lista de Receitas</h2>
                <table id="receitasTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Custo Total</th>
                            <th>Lucro</th>
                            <th>Custo Receita</th>
                            <th>Valor Venda</th>
                            <th>Rendimento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receitas as $receita): ?>
                            <tr>
                                <td><?= $receita['id']; ?></td>
                                <td><?= $receita['nome']; ?></td>
                                <td>R$ <?= number_format($receita['custo_total'], 2, ',', '.'); ?></td>
                                <td>R$ <?= number_format($receita['lucro'], 2, ',', '.'); ?></td>
                                <td>R$ <?= number_format($receita['custo_receita'], 2, ',', '.'); ?></td>
                                <td>R$ <?= number_format($receita['valor_venda'], 2, ',', '.'); ?></td>
                                <td><?= $receita['rendimento']; ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm editar-btn"
                                        data-id="<?= $receita['id']; ?>">Editar</button>
                                    <button class="btn btn-danger btn-sm excluir-btn"
                                        data-id="<?= $receita['id']; ?>">Excluir</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- Modal de edição -->
                <div class="modal fade" id="editarModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Receita</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editarReceitaForm">
                                    <input type="hidden" name="id" id="edit-id">

                                    <div class="mb-3">
                                        <label class="form-label">Nome da Receita</label>
                                        <input type="text" name="nome_receita" id="edit-nome" class="form-control"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Rendimento</label>
                                        <input type="number" name="rendimento" id="edit-rendimento" class="form-control"
                                            required>
                                    </div>

                                    <h5>Produtos Utilizados</h5>
                                    <table class="table" id="edit-tabelaProdutos">
                                        <thead>
                                            <tr>
                                                <th>Produto</th>
                                                <th>Quantidade</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <button type="button" id="addProdutoEdit" class="btn btn-secondary">Adicionar
                                        Produto</button>

                                    <button type="submit" class="btn btn-primary mt-3">Salvar Alterações</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- jQuery (para facilitar manipulação DOM e AJAX) -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function () {
                    // Exibe/esconde campo de taxa iFood conforme checkbox
                    $('#ifoodCheck').on('change', function () {
                        if ($(this).is(':checked')) {
                            $('#ifoodPercentDiv').show();
                        } else {
                            $('#ifoodPercentDiv').hide();
                            $('[name="taxa_ifood"]').val('');
                        }
                    });

                    // Adicionar nova linha na tabela de produtos
                    $('#adicionarProduto').click(function () {
                        let novaLinha = `<tr>
                                            <td>
                                            <select name="produtos[]" class="form-select" required>
                                                <option value="">Selecione um produto</option>
                                                <?php foreach ($produtos as $produto): ?>
                                                                                            <option value="<?= $produto['id'] ?>" data-preco="<?= $produto['preco'] ?>" data-peso="<?= $produto['peso'] ?>">
                                                                                                <?= $produto['nome'] ?>
                                                                                            </option>
                                                <?php endforeach; ?>
                                            </select>
                                            </td>
                                            <td>
                                            <input type="number" step="0.01" name="quantidades[]" class="form-control" placeholder="Quantidade utilizada" required>
                                            </td>
                                            <td>
                                            <button type="button" class="btn btn-danger removerLinha">Remover</button>
                                            </td>
                                        </tr>`;
                        $('#tabelaProdutos tbody').append(novaLinha);
                    });

                    // Remover linha de produto
                    $('#tabelaProdutos').on('click', '.removerLinha', function () {
                        $(this).closest('tr').remove();
                    });

                    // Submissão do formulário via AJAX
                    $('#receitaForm').on('submit', function (e) {
                        e.preventDefault(); // Impede o comportamento padrão do formulário

                        let formData = $(this).serialize();

                        $.ajax({
                            url: '../backend/receitas/insere_receita.php',
                            type: 'POST',
                            data: formData,
                            dataType: 'json',
                            success: function (response) {
                                let alerta = $('#alerta');
                                alerta.removeClass('d-none alert-success alert-danger');
                                if (response.success) {
                                    alerta.addClass('alert-success').html(response.message);
                                    $('#receitaForm')[0].reset();
                                    $('#ifoodPercentDiv').hide();
                                    // Reinicia a tabela de produtos deixando apenas a linha inicial
                                    $('#tabelaProdutos tbody').html($('#tabelaProdutos tbody tr:first').prop('outerHTML'));
                                } else {
                                    alerta.addClass('alert-danger').html(response.message);
                                }
                            },
                            error: function () {
                                alert('Erro na requisição.');
                            }
                        });
                    });
                    $('#receitasTable').DataTable({
                        "language": {
                            "lengthMenu": "Mostrar _MENU_ registros por página",
                            "zeroRecords": "Nenhuma receita encontrada",
                            "info": "Mostrando página _PAGE_ de _PAGES_",
                            "infoEmpty": "Nenhuma receita disponível",
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

                    // Abrir modal de edição
                    $(".editar-btn").click(function () {
                        $("#edit-id").val($(this).data("id"));
                        $("#edit-nome").val($(this).data("nome"));
                        $("#edit-rendimento").val($(this).data("rendimento"));
                        $("#editarModal").modal("show");
                    });

                    // Abrir modal de edição
                    $(".editar-btn").click(function () {
                        let receitaId = $(this).data("id");
                        $("#edit-id").val(receitaId);

                        $.getJSON("../backend/receitas/get_receita.php", { id: receitaId }, function (data) {
                            $("#edit-nome").val(data.nome);
                            $("#edit-rendimento").val(data.rendimento);

                            let tbody = $("#edit-tabelaProdutos tbody");
                            tbody.empty();
                            data.produtos.forEach(prod => {
                                let row = `<tr>
                                                <td><input type="text" class="form-control" name="produtos[]" value="${prod.nome}" readonly></td>
                                                <td><input type="number" class="form-control" name="quantidades[]" value="${prod.quantidade}" required></td>
                                                <td><button type="button" class="btn btn-danger removerProduto">Remover</button></td>
                                            </tr>`;
                                tbody.append(row);
                            });

                            $("#editarModal").modal("show");
                        });
                    });


                    $("#editarReceitaForm").submit(function (e) {
                        e.preventDefault();
                        $.post("../backend/receitas/editar_receita.php", $(this).serialize(), function (response) {
                            location.reload();
                        });
                    });


                    // Excluir receita
                    $(".excluir-btn").click(function () {
                        if (confirm("Tem certeza que deseja excluir esta receita?")) {
                            let receitaId = $(this).data("id");
                            $.post("../backend/receitas/excluir_receita.php", { id: receitaId }, function (response) {
                                location.reload();
                            });
                        }
                    });
                });
            </script>
</body>

</html>