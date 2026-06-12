<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ООО «Сибирь» - Оптовая торговля зерном</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <div class="container flex-grow-1">
        <nav class="navbar navbar-expand-lg navbar-dark mb-4 rounded" style="background: linear-gradient(135deg, #2d6a1f 0%, #1a4d0f 100%);">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold fs-4" href="index.php">ООО «Сибирь»</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link text-white" href="index.php">Главная</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="catalog.php">Каталог</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li class="nav-item"><a class="nav-link text-white" href="order_form.php">Оформить заказ</a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="my_orders.php">Мои заказы</a></li>
                            <?php if (isAdmin()): ?>
                                <li class="nav-item"><a class="nav-link text-warning" href="admin.php">Админ-панель</a></li>
                            <?php endif; ?>
                            <li class="nav-item"><a class="nav-link text-white" href="logout.php">Выход (<?php echo $_SESSION['user_name']; ?>)</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link text-white" href="register.php">Регистрация</a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="login.php">Авторизация</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>