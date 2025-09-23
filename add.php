<?php
require_once 'config.php';

$success = false;
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? 'веб-разработка');
    $complexity = $_POST['complexity'] ?? 'средне';
    

    if (empty($title)) {
        $error = 'Название проекта обязательно для заполнения';
    } elseif (strlen($title) > 255) {
        $error = 'Название проекта не должно превышать 255 символов';
    } elseif (!in_array($complexity, ['легко', 'средне', 'сложно'])) {
        $error = 'Недопустимое значение сложности';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO tasks (title, description, category, complexity) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $category, $complexity]);
            $success = true;
            
            
            $title = $description = $category = '';
            $complexity = 'средне';
        } catch(PDOException $e) {
            $error = 'Ошибка при добавлении идеи проекта: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить идею - База идей для проектов</title>
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
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-lightbulb text-warning"></i> Добавить новую идею проекта
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle"></i> Идея проекта успешно добавлена!
                                <a href="index.php" class="alert-link">Вернуться к базе идей</a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    <i class="fas fa-rocket"></i> Название проекта <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       value="<?php echo htmlspecialchars($title ?? ''); ?>"
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
                                          placeholder="Подробное описание проекта, технологии, цели (необязательно)"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">
                                    <i class="fas fa-tag"></i> Категория
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="category" 
                                       name="category" 
                                       value="<?php echo htmlspecialchars($category ?? 'веб-разработка'); ?>"
                                       maxlength="100"
                                       placeholder="Например: веб-разработка, мобильные приложения, ИИ, игры">
                                <div class="form-text">Максимум 100 символов</div>
                            </div>

                            <div class="mb-4">
                                <label for="complexity" class="form-label">
                                    <i class="fas fa-chart-bar"></i> Сложность
                                </label>
                                <select class="form-select" id="complexity" name="complexity">
                                    <option value="легко" <?php echo (isset($complexity) && $complexity === 'легко') ? 'selected' : ''; ?>>
                                        ⭐ Легко (простой проект для начинающих)
                                    </option>
                                    <option value="средне" <?php echo (!isset($complexity) || $complexity === 'средне') ? 'selected' : ''; ?>>
                                        🌟 Средне (требует опыта)
                                    </option>
                                    <option value="сложно" <?php echo (isset($complexity) && $complexity === 'сложно') ? 'selected' : ''; ?>>
                                        🔥 Сложно (для экспертов)
                                    </option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-arrow-left"></i> Назад
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Добавить идею
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
