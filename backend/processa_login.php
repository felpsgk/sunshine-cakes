<!-- processa_login.php -->
<?php
include './db/start_db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    echo $email;
    echo $senha;
    if (autenticarUsuario($pdo, $email, $senha)) {
        header("Location: ../frontend/dashboard.php"); // Redireciona para dashboard
        exit();
    } else {
        echo "<script>alert('Email ou senha incorretos!'); window.location.href='../frontend/login.php';</script>";
    }
}
?>