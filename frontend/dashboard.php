<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>
<?php include 'head.php'; // Inclui o arquivo head.php ?>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; // Inclui a navbar.php ?>
    <!-- Dashboard Content -->
    <div class="container container-dashboard">
        <h2 class="text-center">Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?>!</h2>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card card-custom">
                    <h5>Gerenciar Produtos</h5>
                    <a href="produtos.php" class="btn btn-sm btn-primary">Acessar</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom">
                    <h5>Gerenciar Receitas</h5>
                    <a href="receitas.php" class="btn btn-sm btn-primary">Acessar</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom">
                    <h5>Relatórios</h5>
                    <a href="relatorios.php" class="btn btn-sm btn-primary">Acessar</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom">
                    <h5>Importação de Planilhas</h5>
                    <a href="importacao.php" class="btn btn-sm btn-primary">Acessar</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>