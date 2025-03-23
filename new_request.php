<?php
include 'db.php'; // Подключение к базе данных
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    // Экранирование входящих данных
    $address = htmlspecialchars($_POST['address']);
    $contact_info = htmlspecialchars($_POST['phone']);
    $service_type = htmlspecialchars($_POST['service_type']);
    $other_service = htmlspecialchars($_POST['other_service']);
    $payment_type = htmlspecialchars($_POST['payment_type']);
    $date_time = $_POST['date_time'];

    // Проверка формата телефона
    if (!preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $contact_info)) {
        $error = "Телефон должен быть в формате +7(XXX)-XXX-XX-XX.";
    }

    if (empty($service_type) && empty($other_service)) {
        $error = "Пожалуйста, выберите вид услуги или укажите иную услугу.";
    }

    if (empty($error)) {
        // Запись в базу данных
        $query = "INSERT INTO requests (user_id, address, contact_info, service_type, payment_type, date_time, other_service) VALUES ('$user_id', '$address', '$contact_info', '$service_type', '$payment_type', '$date_time', '$other_service')";
        if (mysqli_query($conn, $query)) {
            $success = "Заявка успешно создана!";
            // Можно перенаправить пользователя на другую страницу, если необходимо
        } else {
            $error = "Ошибка при создании заявки: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Формирование заявки на клининговые услуги Мой Не Сам. Укажите ваши данные для заказа.">
    <meta name="keywords" content="клининговые услуги, формирование заявки, уборка, Мой Не Сам">
    <title>Формирование заявки</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

     <!-- Open Graph метатеги -->
     <meta property="og:title" content="Формирование заявки на клининговые услуги">
    <meta property="og:description" content="Создайте заявку на клининговые услуги с помощью нашего сервиса.">
    <meta property="og:image" content="https://avatars.mds.yandex.net/get-altay/13267750/2a0000018f942fa10ca65afa328db6d907ae/XXXL"> <!-- Публичный URL изображения -->
    <meta property="og:url" content="http://marzar/washing/new_request.php"> <!-- Локальный URL страницы -->
    <meta property="og:type" content="website">

    <!-- Twitter Card метатеги -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Формирование заявки на клининговые услуги">
    <meta name="twitter:description" content="Создайте заявку на клининговые услуги с помощью нашего сервиса.">
    <meta name="twitter:image" content="https://avatars.mds.yandex.net/get-altay/13267750/2a0000018f942fa10ca65afa328db6d907ae/XXXL">
    <meta name="twitter:url" content="http://marzar/washing/new_request.php">
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
        <a class="nav-link" href="create_request.php">Мои заявки</a>
        <a class="nav-link active" href="new_request.php">Оформить заявку</a>
        <a class="nav-link" href="login.php">Выйти</a>
      </div>
    </div>
  </div>
</nav>
<div class="container flex-grow-1 mt-4">
    <h2>Формирование заявки</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="address" class="form-label">Адрес</label>
            <input type="text" class="form-control" id="address" name="address" required placeholder="Адрес">
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Контактные данные</label>
            <input type="text" class="form-control" id="phone" name="phone" required placeholder="+7(XXX)-XXX-XX-XX">
        </div>
        <div class="mb-3">
            <label for="date_time" class="form-label">Дата и время услуги</label>
            <input type="datetime-local" class="form-control" id="date_time" name="date_time" required>
        </div>
        <div class="mb-3">
            <label for="service_type" class="form-label">Вид услуги</label>
            <select name="service_type" id="service_type" required onchange="toggleOtherService()">
                <option value="">Выберите услугу</option>
                <option value="общий клининг">Общий клининг</option>
                <option value="генеральная уборка">Генеральная уборка</option>
                <option value="послестроительная уборка">Послестроительная уборка</option>
                <option value="химчистка ковров и мебели">Химчистка ковров и мебели</option>
                <option value="иная услуга">Иная услуга</option>
            </select>
        </div>
        <div class="mb-3" id="other_service_container" style="display: none;">
            <label for="other_service" class="form-label">Опишите, какая именно услуга вам нужна</label>
            <textarea class="form-control" id="other_service" name="other_service" placeholder="Введите описание услуги"></textarea>
        </div>
        <div class="mb-3">
            <label for="payment_type" class="form-label">Тип оплаты</label>
            <select name="payment_type" required>
                <option value="">Выберите тип оплаты</option>
                <option value="cash">Наличные</option>
                <option value="card">Банковская карта</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Создать заявку</button>
    </form>

    <?php if ($success): ?>
        <div class="alert alert-success mt-3"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="mt-5">
        <h2>Поделитесь с друзьями!</h2>
        <script src="https://vk.com/js/api/share.js?95" charset="utf-8"></script>
        <script>
        document.write(VK.Share.button(false, {type: 'round', text: 'Поделиться'}));
        </script>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3">
    <p>Мой Не Сам</p>
    <hr class="hr_footer" />
    <p>&#169; Все права защищены - 2025</p>
</footer>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleOtherService() {
        const serviceType = document.getElementById('service_type').value;
        const otherServiceContainer = document.getElementById('other_service_container');
        if (serviceType === 'иная услуга') {
            otherServiceContainer.style.display = 'block';
        } else {
            otherServiceContainer.style.display = 'none';
        }
    }
</script>
</body>
</html>
