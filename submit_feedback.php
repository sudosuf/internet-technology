<?php
header('Content-Type: application/json');

// Настройки подключения к базе данных PostgreSQL
$host = 'localhost';
$port = '5432';
$dbname = 'tasknote_feedback';
$user = 'postgres';  // Замените на ваше имя пользователя
$password = '2003';  // Замените на ваш пароль

// Подключение к базе данных
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка подключения к базе данных']);
    exit;
}

// Получаем данные из POST-запроса
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

// Простая проверка данных
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Все поля обязательны']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Неверный формат email']);
    exit;
}

// Подготовка и выполнение SQL-запроса
$query = "INSERT INTO feedback_entries (name, email, subject, message) VALUES ($1, $2, $3, $4)";
$result = pg_prepare($conn, "insert_feedback", $query);
$execute = pg_execute($conn, "insert_feedback", array($name, $email, $subject, $message));

if ($execute === false) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка сохранения данных']);
} else {
    echo json_encode(['status' => 'success', 'message' => 'Сообщение успешно отправлено']);
}

// Закрываем соединение
pg_close($conn);
?>
