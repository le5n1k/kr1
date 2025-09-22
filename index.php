<?php
require_once 'config.php';


try {
    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
    $projects = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = "Ошибка при получении идей проектов: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>База идей для проектов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .project-completed {
            opacity: 0.8;
            border: 2px solid #28a745;
        }
        .complexity-badge {
            font-size: 0.8em;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }
        .btn-sm {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-lightbulb text-warning"></i> База идей для проектов</h1>
                    <a href="add.php" class="btn btn-success">
                        <i class="fas fa-plus"></i> Добавить идею
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['info'])): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($_GET['info']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($projects)): ?>
                    <div class="alert alert-info text-center" role="alert">
                        <i class="fas fa-info-circle"></i> Идей проектов пока нет. 
                        <a href="add.php" class="alert-link">Добавить первую идею</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($projects as $project): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 <?php echo $project['status'] === 'выполнена' ? 'project-completed' : ''; ?>">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-rocket text-primary me-1"></i>
                                            <?php echo htmlspecialchars($project['title']); ?>
                                        </h6>
                                        <div>
                                            <?php
                                            $complexityColors = [
                                                'легко' => 'success',
                                                'средне' => 'warning',
                                                'сложно' => 'danger'
                                            ];
                                            $complexityIcons = [
                                                'легко' => 'star',
                                                'средне' => 'star-half-alt',
                                                'сложно' => 'fire'
                                            ];
                                            $color = $complexityColors[$project['complexity']] ?? 'secondary';
                                            $icon = $complexityIcons[$project['complexity']] ?? 'star';
                                            ?>
                                            <span class="badge bg-<?php echo $color; ?> complexity-badge">
                                                <i class="fas fa-<?php echo $icon; ?>"></i>
                                                <?php echo htmlspecialchars($project['complexity']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($project['description'])): ?>
                                            <p class="card-text"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                                        <?php endif; ?>
                                        
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-folder"></i> <?php echo htmlspecialchars($project['category']); ?>
                                            </small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-plus"></i> 
                                                <?php echo date('d.m.Y H:i', strtotime($project['created_at'])); ?>
                                            </small>
                                        </div>
                                        
                                        <div class="mb-3">
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
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <a href="edit.php?id=<?php echo $project['id']; ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i> Изменить
                                            </a>
                                            
                                            <?php if ($project['status'] === 'не выполнена'): ?>
                                                <a href="update_status.php?id=<?php echo $project['id']; ?>&status=выполнена" 
                                                   class="btn btn-outline-success btn-sm"
                                                   onclick="return confirm('Отметить проект как реализованный?')">
                                                    <i class="fas fa-rocket"></i> Реализовано
                                                </a>
                                            <?php else: ?>
                                                <a href="update_status.php?id=<?php echo $project['id']; ?>&status=не выполнена" 
                                                   class="btn btn-outline-warning btn-sm"
                                                   onclick="return confirm('Вернуть проект в статус идеи?')">
                                                    <i class="fas fa-lightbulb"></i> В идеи
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="delete.php?id=<?php echo $project['id']; ?>" 
                                               class="btn btn-outline-danger btn-sm"
                                               onclick="return confirm('Вы уверены, что хотите удалить эту идею?')">
                                                <i class="fas fa-trash"></i> Удалить
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
