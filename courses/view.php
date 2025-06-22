<?php
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$course_id = $_GET['id'] ?? null;

if (!$course_id) {
    header('Location: index.php');
    exit;
}

// جلب معلومات الدورة
$stmt = $pdo->prepare('
    SELECT c.*, cat.name as category_name,
           (SELECT COUNT(*) FROM user_courses WHERE course_id = c.id) as enrolled_count,
           (SELECT progress FROM user_courses WHERE course_id = c.id AND user_id = ?) as user_progress
    FROM courses c
    LEFT JOIN categories cat ON c.category_id = cat.id
    WHERE c.id = ?
');
$stmt->execute([$_SESSION['user_id'], $course_id]);
$course = $stmt->fetch();

if (!$course) {
    header('Location: index.php');
    exit;
}

// جلب وحدات الدورة
$stmt = $pdo->prepare('
    SELECT * FROM course_modules 
    WHERE course_id = ? 
    ORDER BY module_order
');
$stmt->execute([$course_id]);
$modules = $stmt->fetchAll();

// جلب التعليقات
$stmt = $pdo->prepare('
    SELECT c.*, u.full_name, u.avatar
    FROM course_comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.course_id = ?
    ORDER BY c.created_at DESC
');
$stmt->execute([$course_id]);
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- القائمة العلوية -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../dashboard/index.php">
                <img src="../assets/images/moh-logo.png" alt="شعار وزارة الصحة" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard/index.php">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">الدورات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../forum/index.php">المنتديات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../help/index.php">المساعدة</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- المحتوى الرئيسي -->
    <div class="container my-4">
        <div class="row">
            <!-- معلومات الدورة -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <?php if ($course['image']): ?>
                        <img src="<?php echo htmlspecialchars($course['image']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($course['title']); ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h1 class="card-title h2"><?php echo htmlspecialchars($course['title']); ?></h1>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($course['category_name']); ?></span>
                            <div>
                                <span class="text-muted me-3">
                                    <i class="bi bi-people"></i>
                                    <?php echo $course['enrolled_count']; ?> متدرب
                                </span>
                                <button class="btn btn-outline-primary btn-sm" id="favorite-<?php echo $course['id']; ?>"
                                        onclick="toggleFavorite(<?php echo $course['id']; ?>)">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </div>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                        
                        <?php if ($course['user_progress'] !== null): ?>
                            <div class="progress mb-3">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: <?php echo $course['user_progress']; ?>%">
                                    <?php echo $course['user_progress']; ?>%
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2">
                            <?php if ($course['user_progress'] === null): ?>
                                <button class="btn btn-primary" onclick="enrollCourse(<?php echo $course['id']; ?>)">
                                    التسجيل في الدورة
                                </button>
                            <?php else: ?>
                                <a href="learn.php?id=<?php echo $course['id']; ?>" class="btn btn-primary">
                                    متابعة الدورة
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- وحدات الدورة -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">محتويات الدورة</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($modules as $module): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($module['title']); ?></h6>
                                        <p class="text-muted small mb-0">
                                            <?php echo htmlspecialchars($module['description']); ?>
                                        </p>
                                    </div>
                                    <?php if ($course['user_progress'] !== null): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- التعليقات -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">التعليقات</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($course['user_progress'] !== null): ?>
                            <form class="mb-4" id="comment-form">
                                <div class="mb-3">
                                    <textarea class="form-control" rows="3" placeholder="اكتب تعليقك هنا..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">إرسال</button>
                            </form>
                        <?php endif; ?>

                        <div id="comments">
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment mb-3">
                                    <div class="d-flex">
                                        <img src="<?php echo $comment['avatar'] ?: '../assets/images/default-avatar.png'; ?>" 
                                             class="rounded-circle me-3" width="40" height="40" 
                                             alt="<?php echo htmlspecialchars($comment['full_name']); ?>">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($comment['full_name']); ?></h6>
                                            <p class="text-muted small">
                                                <?php echo date('Y/m/d', strtotime($comment['created_at'])); ?>
                                            </p>
                                            <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="col-lg-4">
                <!-- معلومات المدرب -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">المدرب</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?php echo $course['trainer_avatar'] ?: '../assets/images/default-avatar.png'; ?>" 
                                 class="rounded-circle me-3" width="60" height="60" 
                                 alt="<?php echo htmlspecialchars($course['trainer_name']); ?>">
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($course['trainer_name']); ?></h6>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($course['trainer_title']); ?></p>
                            </div>
                        </div>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($course['trainer_bio'])); ?></p>
                    </div>
                </div>

                <!-- متطلبات الدورة -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">متطلبات الدورة</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <?php foreach (explode("\n", $course['requirements']) as $requirement): ?>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    <?php echo htmlspecialchars($requirement); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- الدورات المشابهة -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">دورات مشابهة</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php
                        $stmt = $pdo->prepare('
                            SELECT c.*, cat.name as category_name
                            FROM courses c
                            LEFT JOIN categories cat ON c.category_id = cat.id
                            WHERE c.category_id = ? AND c.id != ?
                            LIMIT 3
                        ');
                        $stmt->execute([$course['category_id'], $course['id']]);
                        $similar_courses = $stmt->fetchAll();

                        foreach ($similar_courses as $similar):
                        ?>
                            <a href="view.php?id=<?php echo $similar['id']; ?>" class="list-group-item list-group-item-action">
                                <h6 class="mb-1"><?php echo htmlspecialchars($similar['title']); ?></h6>
                                <p class="text-muted small mb-0">
                                    <?php echo htmlspecialchars(substr($similar['description'], 0, 100)) . '...'; ?>
                                </p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/courses.js"></script>
</body>
</html> 