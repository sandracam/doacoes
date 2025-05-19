<?php
session_start();
$is_admin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
$is_logged = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Doação de Livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Doação de Livros</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if ($is_logged) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="livros.php">Livros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="minhas_doacoes.php">Minhas Doações</a>
                        </li>
                        <?php if ($is_admin) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="categorias.php">Categorias</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="usuarios.php">Usuários</a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($is_logged) { ?>
                        <li class="nav-item">
                            <span class="nav-link text-light">Olá, <?php echo isset($_SESSION['user_nome']) ? htmlspecialchars($_SESSION['user_nome']) : 'Usuário'; ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Sair</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cadastro.php">Cadastro</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
    <?php if (isset($_SESSION['message'])) { ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
            <?php 
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?> 