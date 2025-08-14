<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locadora Top</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding-bottom: 70px; 
        }
        .hero-section {
            background: url('https://images.unsplash.com/photo-1553440569-b2dc52a5391c?q=80&w=2070') no-repeat center center;
            background-size: cover;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
        }
        .btn-gradient {
            background-image: linear-gradient(to right, #007bff 0%, #0056b3 100%);
            border: none;
            transition: transform 0.2s;
        }
        .btn-gradient:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>">Locadora Top</a>
        
        <div class="d-flex">
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#userOffcanvas" aria-controls="userOffcanvas">
                    <i class="bi bi-list" style="font-size: 1.5rem;"></i>
                </button>
            <?php else: ?>
                <ul class="navbar-nav flex-row">
                    <li class="nav-item me-2">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/user/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/user/register">Registo</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="offcanvas offcanvas-end" tabindex="-1" id="userOffcanvas" aria-labelledby="userOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="userOffcanvasLabel">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <p>Aceda às opções do seu painel.</p>
        <hr>
        <div class="list-group list-group-flush">
            <?php if ($_SESSION['user_type'] === 'funcionario'): ?>
                <a href="<?php echo BASE_URL; ?>/dashboard" class="list-group-item list-group-item-action">Painel Principal</a>
                <a href="<?php echo BASE_URL; ?>/car/index" class="list-group-item list-group-item-action">Gerir Carros</a>
                <a href="<?php echo BASE_URL; ?>/user/manage" class="list-group-item list-group-item-action">Gerir Utilizadores</a>
            <?php else: // 'comun' ?>
                <a href="<?php echo BASE_URL; ?>/dashboard" class="list-group-item list-group-item-action">Meu Painel</a>
                <a href="<?php echo BASE_URL; ?>/reservation/history" class="list-group-item list-group-item-action">Histórico de Reservas</a>
            <?php endif; ?>
        </div>

        <div class="mt-auto" style="position: absolute; bottom: 20px; width: 85%;">
            <hr>
            <a href="<?php echo BASE_URL; ?>/user/logout" class="btn btn-danger w-100">Sair</a>
        </div>
    </div>
</div>
<?php endif; ?>

<main class="container mt-4">