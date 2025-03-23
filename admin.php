<?php
include 'db.php'; // Подключение к базе данных
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Получение всех заявок с информацией о пользователе
$query = "
    SELECT r.*, u.full_name, u.phone 
    FROM requests r
    JOIN users u ON r.user_id = u.id
";
$requests = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script>
        function toggleReasonField(select) {
            const reasonField = select.parentElement.querySelector('.reason-field');
            if (select.value === 'отменено') {
                reasonField.style.display = 'block';
                reasonField.querySelector('input[name="reason"]').setAttribute('required', 'required');
            } else {
                reasonField.style.display = 'none';
                reasonField.querySelector('input[name="reason"]').removeAttribute('required');
                reasonField.querySelector('input[name="reason"]').value = ''; // Очищаем поле, если статус изменен
            }
        }
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand fs-1 text-warning">Панель администратора</span>
      <div class="navbar-nav ms-auto">
        <a class="nav-link" href="login.php">Выйти</a>
      </div>
  </div>
</nav>
<div class="container mt-5">
    <h2 class="text-center mb-4">Заявки</h2>

    <!-- Сообщения об ошибках и успехах -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ФИО</th>
                    <th>Контактные данные</th>
                    <th>Услуга</th>
                    <th>Иная услуга</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($requests)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['contact_info']) ?></td>
                        <td><?= htmlspecialchars($row['service_type']) ?></td>
                        <td><?= htmlspecialchars($row['other_service']) ?></td>
                        <td>
                            <form method="POST" action="update_status.php">
                                <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                <select name="status" class="form-select" onchange="toggleReasonField(this)" required>
                                    <option value="в работе" <?= $row['status'] == 'в работе' ? 'selected' : '' ?>>В работе</option>
                                    <option value="выполнено" <?= $row['status'] == 'выполнено' ? 'selected' : '' ?>>Выполнено</option>
                                    <option value="отменено" <?= $row['status'] == 'отменено' ? 'selected' : '' ?>>Отменено</option>
                                </select>
                                <div class="reason-field" style="display: <?= $row['status'] == 'отменено' ? 'block' : 'none' ?>;">
                                    <input type="text" name="reason" class="form-control mt-2" placeholder="Причина отмены" <?= $row['status'] == 'отменено' ? 'required' : '' ?>>
                                </div>
                                <button type="submit" class="btn btn-primary mt-2">Обновить</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>


