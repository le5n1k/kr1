<?php
require_once 'config.php';

// Получение ID проекта из URL
$projectId = $_GET['id'] ?? null;

// Проверка валидности ID
if (!$projectId || !is_numeric($projectId)) {
    header('Location: index.php?error=' . urlencode('Неверный ID проекта'));
    exit;
}

try {
    // Проверяем, существует ли проект
    $stmt = $pdo->prepare("SELECT id, title FROM tasks WHERE id = ?");
    $stmt->execute([$projectId]);
    $project = $stmt->fetch();
    
    if (!$project) {
        header('Location: index.php?error=' . urlencode('Идея проекта не найдена'));
        exit;
    }
    
    // Удаляем проект
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$projectId]);
    
    // Перенаправляем с сообщением об успехе
    header('Location: index.php?success=' . urlencode('Идея проекта "' . $project['title'] . '" успешно удалена'));
    exit;
    
} catch(PDOException $e) {
    // Перенаправляем с сообщением об ошибке
    header('Location: index.php?error=' . urlencode('Ошибка при удалении идеи проекта: ' . $e->getMessage()));
    exit;
}
?>
