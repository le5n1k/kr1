<?php
require_once 'config.php';

$projectId = $_GET['id'] ?? null;


if (!$projectId || !is_numeric($projectId)) {
    header('Location: index.php?error=' . urlencode('Неверный ID проекта'));
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, title FROM tasks WHERE id = ?");
    $stmt->execute([$projectId]);
    $project = $stmt->fetch();
    
    if (!$project) {
        header('Location: index.php?error=' . urlencode('Идея проекта не найдена'));
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$projectId]);
    
    header('Location: index.php?success=' . urlencode('Идея проекта "' . $project['title'] . '" успешно удалена'));
    exit;
    
} catch(PDOException $e) {

    header('Location: index.php?error=' . urlencode('Ошибка при удалении идеи проекта: ' . $e->getMessage()));
    exit;
}
?>
