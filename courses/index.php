<?php
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// جلب الفئات
$stmt = $pdo->query('SELECT * FROM categories ORDER BY name');
$categories = $stmt->fetchAll();

// جلب الدورات حسب الفئة المحددة
$category_id = $_GET['category'] ?? null;
$search = $_GET['search'] ?? '';

$query = '
    SELECT c.*, cat.name as category_name, 
           (SELECT COUNT(*) FROM user_courses WHERE course_id = c.id) as enrolled_count
    FROM courses c
    LEFT JOIN categories cat ON c.category_id = cat.id
    WHERE 1=1
';

$params = [];

if ($category_id) {
    $query .= ' AND c.category_id = ?';
    $params[] = $category_id;
}

if ($search) {
    $query .= ' AND (c.title LIKE ? OR c.description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= ' ORDER BY c.created_at DESC';

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الدورات التدريبية - <?php echo SITE_NAME; ?></title>
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
            <!-- الفلاتر -->
            <div class="col-lg-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">تصفية الدورات</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="GET">
                            <div class="mb-3">
                                <label for="search" class="form-label">بحث</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">الفئة</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">جميع الفئات</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">تصفية</button>
                        </form>
                    </div>
                </div>

                <!-- الفئات السريعة -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">الفئات</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($categories as $category): ?>
                            <a href="?category=<?php echo $category['id']; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
                                      <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo $category['course_count'] ?? 0; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- قائمة الدورات -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>الدورات التدريبية</h2>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary active" data-view="grid">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-view="list">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>
                </div>

                <div class="row" id="courses-grid">
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <?php if ($course['image']): ?>
                                    <img src="<?php echo htmlspecialchars($course['image']); ?>" 
                                         class="card-img-top" alt="<?php echo htmlspecialchars($course['title']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                    <p class="card-text text-muted">
                                        <?php echo htmlspecialchars(substr($course['description'], 0, 100)) . '...'; ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-primary">
                                            <?php echo htmlspecialchars($course['category_name']); ?>
                                        </span>
                                        <small class="text-muted">
                                            <?php echo $course['enrolled_count']; ?> متدرب
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="view.php?id=<?php echo $course['id']; ?>" 
                                       class="btn btn-primary w-100">عرض التفاصيل</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($courses)): ?>
                    <div class="alert alert-info">
                        لا توجد دورات متاحة حالياً
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/courses.js"></script>
</body>
</html> 