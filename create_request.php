<?php
include 'db.php'; // Подключение к базе данных
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Получение истории заявок
$query = "SELECT * FROM requests WHERE user_id='$user_id'";
$requests = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Просмотр своих заявок, а также создание заявки на клининговые услуги Мой Не Сам. Оформите заявку на уборку.">
    <meta name="keywords" content="клининговые услуги, заявка, уборка, Мой Не Сам">
    <title>Все заявки</title>
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
        <a class="nav-link active" href="create_request.php">Мои заявки</a>
        <a class="nav-link" href="new_request.php">Оформить заявку</a>
        <a class="nav-link" href="login.php">Выйти</a>
      </div>
    </div>
  </div>
</nav>
<div class="container flex-grow-1 mt-4">
    <h2>Ваши заявки</h2>
    <p><a href="new_request.php" class="btn btn-primary mt-3">Создать новую заявку</a></p>
    <div class="list-group">
        <?php while ($row = mysqli_fetch_assoc($requests)): ?>
            <div class="list-group-item">
                <h5 class="mb-1 text-success fw-semibold"><?= htmlspecialchars($row['service_type']) ?></h5>
                <p class="mb-1"><strong>Дата и время:</strong> <?= htmlspecialchars(date('d.m.Y H:i', strtotime($row['date_time']))) ?></p>
                <p class="mb-1"><strong>Статус:</strong> 
                    <?php 
                        switch ($row['status']) {
                            case 'новая':
                                echo 'Новая';
                                break;
                            case 'в работе':
                                echo 'В работе';
                                break;
                            case 'выполнено':
                                echo 'Выполнено';
                                break;
                            case 'отменено':
                                echo 'Отменено';
                                break;
                            default:
                                echo 'Неизвестный статус';
                                break;
                        }
                    ?>
                </p>
                <?php if ($row['status'] === 'отменено' && !empty($row['cancellation_reason'])): ?>
                    <p class="mb-1"><strong>Причина отмены:</strong> <?= htmlspecialchars($row['cancellation_reason']) ?></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<footer class="bg-dark text-white text-center py-3">
    <p>Мой Не Сам</p>
    <hr class="hr_footer" />
    <p>&#169; Все права защищены - 2025</p>
</footer>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

