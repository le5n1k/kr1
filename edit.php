<?php
require_once 'config.php';

$success = false;
$error = '';
$task = null;

$taskId = $_GET['id'] ?? null;

if (!$taskId || !is_numeric($taskId)) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
    $project = $stmt->fetch();
    
    if (!$project) {
        header('Location: index.php');
        exit;
    }
} catch(PDOException $e) {
    $error = 'Ошибка при получении данных проекта: ' . $e->getMessage();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $project) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? 'общее');
    $complexity = $_POST['complexity'] ?? 'средне';
    

    if (empty($title)) {
        $error = 'Название проекта обязательно для заполнения';
    } elseif (strlen($title) > 255) {
        $error = 'Название проекта не должно превышать 255 символов';
    } elseif (!in_array($complexity, ['легко', 'средне', 'сложно'])) {
        $error = 'Недопустимое значение сложности';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, category = ?, complexity = ? WHERE id = ?");
            $stmt->execute([$title, $description, $category, $complexity, $taskId]);
            $success = true;

            $project['title'] = $title;
            $project['description'] = $description;
            $project['category'] = $category;
            $project['complexity'] = $complexity;
        } catch(PDOException $e) {
            $error = 'Ошибка при обновлении проекта: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать идею - База идей для проектов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }
        .status-info {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-edit text-primary"></i> Редактировать идею проекта
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle"></i> Идея проекта успешно обновлена!
                                <a href="index.php" class="alert-link">Вернуться к базе идей</a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($project): ?>

                            <div class="alert status-info" role="alert">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Статус:</strong> 
                                        <?php if ($project['status'] === 'выполнена'): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Реализовано
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">
                                                <i class="fas fa-lightbulb"></i> Идея
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Создана:</strong> 
                                        <?php echo date('d.m.Y H:i', strtotime($project['created_at'])); ?>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-rocket"></i> Название проекта <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="title" 
                                           name="title" 
                                           value="<?php echo htmlspecialchars($project['title']); ?>"
                                           maxlength="255" 
                                           required>
                                    <div class="form-text">Максимум 255 символов</div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left"></i> Описание проекта
                                    </label>
                                    <textarea class="form-control" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              placeholder="Подробное описание проекта, технологии, цели (необязательно)"><?php echo htmlspecialchars($project['description']); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">
                                        <i class="fas fa-tag"></i> Категория
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="category" 
                                           name="category" 
                                           value="<?php echo htmlspecialchars($project['category']); ?>"
                                           maxlength="100"
                                           placeholder="Например: веб-разработка, мобильные приложения, ИИ, игры">
                                    <div class="form-text">Максимум 100 символов</div>
                                </div>

                                <div class="mb-4">
                                    <label for="complexity" class="form-label">
                                        <i class="fas fa-chart-bar"></i> Сложность
                                    </label>
                                    <select class="form-select" id="complexity" name="complexity">
                                        <option value="легко" <?php echo $project['complexity'] === 'легко' ? 'selected' : ''; ?>>
                                            ⭐ Легко (простой проект для начинающих)
                                        </option>
                                        <option value="средне" <?php echo $project['complexity'] === 'средне' ? 'selected' : ''; ?>>
                                            🌟 Средне (требует опыта)
                                        </option>
                                        <option value="сложно" <?php echo $project['complexity'] === 'сложно' ? 'selected' : ''; ?>>
                                            🔥 Сложно (для экспертов)
                                        </option>
                                    </select>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="index.php" class="btn btn-outline-secondary me-md-2">
                                        <i class="fas fa-arrow-left"></i> Назад
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Сохранить изменения
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>


                <?php if ($project): ?>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Дополнительные действия</h6>
                        </div>
                        <div class="card-body">
                            <div class="btn-group w-100" role="group">
                                <?php if ($project['status'] === 'не выполнена'): ?>
                                    <a href="update_status.php?id=<?php echo $project['id']; ?>&status=выполнена" 
                                       class="btn btn-outline-success"
                                       onclick="return confirm('Отметить проект как реализованный?')">
                                        <i class="fas fa-rocket"></i> Отметить реализованным
                                    </a>
                                <?php else: ?>
                                    <a href="update_status.php?id=<?php echo $project['id']; ?>&status=не выполнена" 
                                       class="btn btn-outline-warning"
                                       onclick="return confirm('Вернуть проект в статус идеи?')">
                                        <i class="fas fa-lightbulb"></i> Вернуть в идеи
                                    </a>
                                <?php endif; ?>
                                
                                <a href="delete.php?id=<?php echo $project['id']; ?>" 
                                   class="btn btn-outline-danger"
                                   onclick="return confirm('Вы уверены, что хотите удалить эту идею проекта?')">
                                    <i class="fas fa-trash"></i> Удалить идею
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
