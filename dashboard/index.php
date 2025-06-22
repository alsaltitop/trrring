<?php
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// جلب معلومات المستخدم
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// جلب الدورات المسجلة
$stmt = $pdo->prepare('
    SELECT c.*, uc.progress 
    FROM courses c 
    JOIN user_courses uc ON c.id = uc.course_id 
    WHERE uc.user_id = ? 
    ORDER BY uc.last_accessed DESC 
    LIMIT 5
');
$stmt->execute([$_SESSION['user_id']]);
$enrolled_courses = $stmt->fetchAll();

// جلب الإعلانات
$stmt = $pdo->prepare('
    SELECT * FROM announcements 
    WHERE (target_role = ? OR target_role = "all") 
    AND (expiry_date IS NULL OR expiry_date > NOW()) 
    ORDER BY created_at DESC 
    LIMIT 5
');
$stmt->execute([$user['role']]);
$announcements = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- القائمة العلوية -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../assets/images/moh-logo.png" alt="شعار وزارة الصحة" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../courses/index.php">الدورات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../forum/index.php">المنتديات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../help/index.php">المساعدة</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">الملف الشخصي</a></li>
                            <li><a class="dropdown-item" href="settings.php">الإعدادات</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/logout.php">تسجيل الخروج</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- المحتوى الرئيسي -->
    <div class="container my-4">
        <div class="row">
            <!-- القسم الرئيسي -->
            <div class="col-lg-8">
                <!-- الإعلانات -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">الإعلانات</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($announcements)): ?>
                            <p class="text-muted">لا توجد إعلانات حالياً</p>
                        <?php else: ?>
                            <?php foreach ($announcements as $announcement): ?>
                                <div class="announcement mb-3">
                                    <h6><?php echo htmlspecialchars($announcement['title']); ?></h6>
                                    <p class="text-muted small">
                                        <?php echo date('Y/m/d', strtotime($announcement['created_at'])); ?>
                                    </p>
                                    <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- الدورات المسجلة -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">دوراتي</h5>
                        <a href="../courses/index.php" class="btn btn-sm btn-primary">عرض جميع الدورات</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($enrolled_courses)): ?>
                            <p class="text-muted">لم تقم بالتسجيل في أي دورة بعد</p>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($enrolled_courses as $course): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h6>
                                                <div class="progress mb-2">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: <?php echo $course['progress']; ?>%">
                                                        <?php echo $course['progress']; ?>%
                                                    </div>
                                                </div>
                                                <a href="../courses/view.php?id=<?php echo $course['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">متابعة الدورة</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="col-lg-4">
                <!-- التقويم -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">التقويم</h5>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>

                <!-- المجموعات -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">مجموعاتي</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                الأطباء
                                <span class="badge bg-primary rounded-pill">14</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                الممرضون
                                <span class="badge bg-primary rounded-pill">8</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                فنيو المختبر
                                <span class="badge bg-primary rounded-pill">5</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script src="../js/dashboard.js"></script>
</body>
</html> 