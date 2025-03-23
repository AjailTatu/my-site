<?php
include 'db.php'; // Подключение к базе данных

$error = ''; // Переменная для хранения сообщения об ошибке

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Экранирование входящих данных
    $full_name = htmlspecialchars($_POST['full_name']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $login = htmlspecialchars($_POST['login']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Проверка формата ФИО (символы кириллицы и пробелы)
    if (!preg_match('/^[А-Яа-яЁё\s]+$/u', $full_name)) {
        $error = "ФИО должно содержать только кириллицу и пробелы.";
    }
    // Проверка формата телефона
    if (!preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $phone)) {
        $error = "Телефон должен быть в формате +7(XXX)-XXX-XX-XX.";
    }
    // Проверка формата электронной почты
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Неверный формат электронной почты.";
    }
    // Проверка, существует ли логин
    $checkLoginQuery = "SELECT * FROM users WHERE login = '$login'";
    $result = mysqli_query($conn, $checkLoginQuery);

    if (mysqli_num_rows($result) > 0) {
        $error = "Этот логин уже занят. Пожалуйста, выберите другой.";
    }

    // Если нет ошибок, добавляем пользователя
    if (empty($error)) {
        $query = "INSERT INTO users (full_name, phone, email, login, password) VALUES ('$full_name', '$phone', '$email', '$login', '$password')";
        if (mysqli_query($conn, $query)) {
            header('Location: login.php');
            exit();
        } else {
            $error = "Ошибка: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Регистрация на портале клининговых услуг Мой Не Сам. Создайте учетную запись и заказывайте уборку.">
    <meta name="keywords" content="клининговые услуги, регистрация, уборка, Мой Не Сам">
    <title>Регистрация</title>
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
        <a class="nav-link active" href="register.php">Регистрация</a>
        <a class="nav-link" href="login.php">Авторизация</a>
      </div>
    </div>
  </div>
</nav>
<div class="container flex-grow-1 mt-4">
    <h2>Регистрация</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
    <form action="register.php" method="POST">
        <div class="mb-3">
            <label for="full_name" class="form-label">ФИО</label>
            <input type="text" class="form-control" id="full_name" name="full_name" required placeholder="Фамилия Имя Отчество">
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Телефон</label>
            <input type="text" class="form-control" id="phone" name="phone" required 
            placeholder="+7(XXX)-XXX-XX-XX">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Электронная почта</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="login" class="form-label">Логин</label>
            <input type="text" class="form-control" id="login" name="login" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
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

