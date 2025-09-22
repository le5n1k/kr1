<?php
require_once 'config.php';

// Получение параметров из URL
$projectId = $_GET['id'] ?? null;
$newStatus = $_GET['status'] ?? null;

// Проверка валидности параметров
if (!$projectId || !is_numeric($projectId)) {
    header('Location: index.php?error=' . urlencode('Неверный ID проекта'));
    exit;
}

if (!$newStatus || !in_array($newStatus, ['выполнена', 'не выполнена'])) {
    header('Location: index.php?error=' . urlencode('Неверный статус проекта'));
    exit;
}

try {
    // Проверяем, существует ли проект
    $stmt = $pdo->prepare("SELECT id, title, status FROM tasks WHERE id = ?");
    $stmt->execute([$projectId]);
    $project = $stmt->fetch();
    
    if (!$project) {
        header('Location: index.php?error=' . urlencode('Идея проекта не найдена'));
        exit;
    }
    
    // Проверяем, нужно ли обновлять статус
    if ($project['status'] === $newStatus) {
        $statusText = $newStatus === 'выполнена' ? 'реализован' : 'является идеей';
        header('Location: index.php?info=' . urlencode('Проект "' . $project['title'] . '" уже ' . $statusText));
        exit;
    }
    
    // Обновляем статус проекта
    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $projectId]);
    
    // Формируем сообщение об успехе
    $statusText = $newStatus === 'выполнена' ? 'реализованным' : 'идеей';
    $successMessage = 'Проект "' . $project['title'] . '" отмечен как ' . $statusText;
    
    // Перенаправляем с сообщением об успехе
    header('Location: index.php?success=' . urlencode($successMessage));
    exit;
    
} catch(PDOException $e) {
    // Перенаправляем с сообщением об ошибке
    header('Location: index.php?error=' . urlencode('Ошибка при обновлении статуса проекта: ' . $e->getMessage()));
    exit;
}
?>
