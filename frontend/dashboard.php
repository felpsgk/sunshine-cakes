<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fce4ec;
        }
        .navbar {
            background-color: #d7a9a9;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .container-dashboard {
            margin-top: 20px;
        }
        .card-custom {
            background: #fff3e0;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sunshine Cakes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Receitas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Relatórios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Importação</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

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
