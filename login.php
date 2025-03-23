<?php
include 'db.php'; // Подключение к базе данных
session_start();

$error_login = ''; // Переменная для хранения ошибки логина
$error_password = ''; // Переменная для хранения ошибки пароля

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = htmlspecialchars($_POST['login']);
    $password = $_POST['password']; // Не экранируем пароль, он будет проверяться

    // Проверка на админа
    if ($login === 'adminka' && $password === 'password') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php'); // Перенаправление на панель администратора
        exit();
    }

    // Проверка на обычного пользователя
    $query = "SELECT * FROM users WHERE login='$login'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Ошибка запроса: " . mysqli_error($conn));
    }

    if ($row = mysqli_fetch_assoc($result)) {
        // Логин существует, проверяем пароль
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header('Location: create_request.php'); // Перенаправление на страницу создания заявки
            exit();
        } else {
            // Неверный пароль
            $error_password = "Неверный пароль!";
        }
    } else {
        // Неверный логин
        $error_login = "Пользователь с таким логином не найден!";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Авторизация на портале клининговых услуг Мой Не Сам. Войдите в свою учетную запись.">
    <meta name="keywords" content="клининговые услуги, авторизация, Мой Не Сам">
    <title>Авторизация</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand fs-1 text-warning">Мой Не Сам</span>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <div class="navbar-nav ms-auto">
        <a class="nav-link" href="register.php">Регистрация</a>
        <a class="nav-link active" href="login.php">Авторизация</a>
      </div>
    </div>
  </div>
</nav>
<div class="container flex-grow-1 mt-4">
    <h2>Авторизация</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="login" class="form-label">Логин</label>
            <input type="text" class="form-control" id="login" name="login" required>
            <?php if ($error_login): ?>
                <p class="text-danger"><?php echo $error_login; ?></p>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" required minlength="6">
            <?php if ($error_password): ?>
                <p class="text-danger"><?php echo $error_password; ?></p>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
</div>
<footer class="bg-dark text-white text-center py-3">
    <p>Мой Не Сам</p>
    <hr class="hr_footer" />
    <p>&#169; Все права защищены - 2025</p>
</footer>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
