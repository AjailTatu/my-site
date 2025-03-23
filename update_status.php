<?php
include 'db.php'; // Подключение к базе данных
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

    // Проверяем статус
    if ($status === 'отменено' && empty($reason)) {
        // Если статус "отменено", а причина не указана, возвращаемся с ошибкой
        $_SESSION['error'] = "Причина отмены обязательна при выборе статуса 'отменено'.";
        header("Location: admin.php"); // Вернуться на панель администратора
        exit();
    }

    // Подготовка запроса для обновления статуса
    $query = "UPDATE requests SET status = ?, cancellation_reason = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    // Переменная для причины
    $cancellation_reason = ($status === 'отменено') ? $reason : null;

    // Привязываем параметры
    mysqli_stmt_bind_param($stmt, 'ssi', $status, $cancellation_reason, $request_id);

    // Выполняем запрос
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Статус успешно обновлен.";
    } else {
        $_SESSION['error'] = "Ошибка обновления статуса: " . mysqli_error($conn);
    }

    // Закрываем подготовленный запрос
    mysqli_stmt_close($stmt);
    
    // Перенаправляем обратно на панель администратора
    header("Location: admin.php");
    exit();
}
?>

