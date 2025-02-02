<!-- processa_login.php -->
<?php
include './db/start_db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if (autenticarUsuario($pdo, $email, $senha)) {
        header("Location: dashboard.php"); // Redireciona para dashboard
        exit();
    } else {
        echo "<script>alert('Email ou senha incorretos!'); window.location.href='login.php';</script>";
    }
}
?>