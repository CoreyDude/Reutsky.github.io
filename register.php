<?php
require_once 'config.php';
require_once 'functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($password)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } else {
        // чекаем существование юзера
        $check_query = "SELECT id FROM users WHERE username = '$username'";
        $check_result = mysqli_query($connection, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Пользователь с таким именем уже существует';
        } else {
            // ХЭШИКА пароля (чтобы данные случайно не укрались)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $created_at = date('Y-m-d H:i:s');
            
            $insert_query = "INSERT INTO users (username, password, created_at) 
                            VALUES ('$username', '$hashed_password', '$created_at')";
            
            if (mysqli_query($connection, $insert_query)) {
                $success = 'Регистрация прошла успешно! Теперь вы можете войти.';
            } else {
                $error = 'Ошибка регистрации: ' . mysqli_error($connection);
            }
        }
    }
}

include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-3">Регистрация</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?> <a href="login.php">Войти</a></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Имя пользователя</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="form-text">Минимум 6 символов</div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Подтверждение пароля</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            <a href="login.php" class="btn btn-link">Уже есть аккаунт?</a>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>