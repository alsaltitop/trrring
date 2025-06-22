<?php
// إظهار جميع الأخطاء للتشخيص
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// بدء جلسة المستخدم
session_start();
require_once 'includes/language.php';
require_once 'config.php';

// تأكد من تسجيل دخول المستخدم
if (!isset($_SESSION['userid'])) {
    // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول
    header("Location: login.php");
    exit();
}

// تخزين بيانات المستخدم من الجلسة
$user_id = $_SESSION['userid'];
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : '';

// جلب الدورات المسجل بها المستخدم مع معلومات التقدم
$enrolled_courses = [];
try {
    $stmt = $pdo->prepare("
        SELECT 
            c.id, 
            c.name, 
            c.description, 
            c.image_url, 
            cat.name as category_name,
            e.progress,
            e.enrollment_date
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN categories cat ON c.category_id = cat.id
        WHERE e.user_id = ?
        ORDER BY e.enrollment_date DESC
    ");
    $stmt->execute([$user_id]);
    $enrolled_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = __('database_error') . ': ' . $e->getMessage();
}

// جلب جميع الدورات مجمعة حسب التصنيف
$courses_by_category = [];
try {
    $stmt = $pdo->query("
        SELECT 
            co.id, 
            co.name, 
            co.description, 
            co.image_url, 
            ca.name as category_name,
            ca.id as category_id
        FROM courses co
        JOIN categories ca ON co.category_id = ca.id
        ORDER BY ca.name, co.name
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $courses_by_category[$row['category_name']][] = $row;
    }
} catch (PDOException $e) {
    $error_message = __('database_error') . ': ' . $e->getMessage();
}

// تعيين عنوان الصفحة قبل تضمين الهيدر
$page_title = __('dashboard');
include 'includes/new-header.php';
?>

<div class="container my-4">
    <!-- قسم الترحيب -->
    <div class="welcome-card">
        <h1><?php echo __('welcome_back'); ?> <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p><?php echo __('dashboard_welcome_text'); ?></p>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger my-4">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <!-- قسم الدورات المسجل بها -->
    <section class="mt-5">
        <div class="section-header">
            <i class="bi bi-journal-bookmark"></i>
            <h2><?php echo __('my_courses'); ?></h2>
        </div>
        
        <?php if (empty($enrolled_courses)): ?>
            <div class="alert alert-info">
                <p class="mb-0"><?php echo __('no_courses'); ?></p>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($enrolled_courses as $course): ?>
                    <div class="col">
                        <div class="card h-100 course-card">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success"><?php echo __('enrolled'); ?></span>
                            </div>
                            <?php if (!empty($course['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($course['image_url']); ?>" 
                                    class="card-img-top" 
                                    alt="<?php echo htmlspecialchars($course['name']); ?>">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex justify-content-center align-items-center" style="height: 180px;">
                                    <i class="bi bi-journal-text" style="font-size: 4rem; color: var(--primary-color);"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($course['name']); ?></h5>
                                <div class="mb-3">
                                    <span class="small text-muted"><?php echo __('your_progress'); ?>:</span>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" 
                                            style="width: <?php echo $course['progress']; ?>%;" 
                                            aria-valuenow="<?php echo $course['progress']; ?>" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            <?php echo $course['progress']; ?>%
                                        </div>
                                    </div>
                                </div>
                                <p class="card-text mb-4"><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...</p>
                                <a href="course_details.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-primary mt-auto align-self-<?php echo get_current_direction() === 'rtl' ? 'start' : 'end'; ?>">
                                    <i class="bi bi-arrow-<?php echo get_current_direction() === 'rtl' ? 'left' : 'right'; ?>"></i> 
                                    <?php echo __('continue_course'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    
    <!-- قسم الدورات المتاحة -->
    <section class="mt-5">
        <div class="section-header">
            <i class="bi bi-collection"></i>
            <h2><?php echo __('available_courses'); ?></h2>
        </div>

        <?php if (empty($courses_by_category)): ?>
            <div class="alert alert-info text-center">
                <p class="mb-0"><?php echo __('no_available_courses'); ?></p>
            </div>
        <?php else: ?>
            <!-- أزرار تصنيفات الدورات -->
            <ul class="nav nav-pills mb-4" id="coursesTab" role="tablist">
                <?php $first = true; foreach (array_keys($courses_by_category) as $index => $category): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $first ? 'active' : ''; ?>" 
                                id="<?php echo 'cat-' . $index . '-tab'; ?>" 
                                data-bs-toggle="pill" 
                                data-bs-target="<?php echo '#cat-' . $index; ?>" 
                                type="button" 
                                role="tab" 
                                aria-controls="<?php echo 'cat-' . $index; ?>" 
                                aria-selected="<?php echo $first ? 'true' : 'false'; ?>">
                            <?php echo htmlspecialchars($category); ?>
                        </button>
                    </li>
                <?php $first = false; endforeach; ?>
            </ul>

            <!-- محتوى التصنيفات -->
            <div class="tab-content" id="coursesTabContent">
                <?php $first = true; foreach ($courses_by_category as $index => $courses): ?>
                    <div class="tab-pane fade <?php echo $first ? 'show active' : ''; ?>" 
                         id="<?php echo 'cat-' . array_search($index, array_keys($courses_by_category)); ?>" 
                         role="tabpanel" 
                         aria-labelledby="<?php echo 'cat-' . array_search($index, array_keys($courses_by_category)) . '-tab'; ?>">
                        
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            <?php foreach ($courses as $course): 
                                // تخطي الدورات التي سجل فيها المستخدم بالفعل
                                $is_enrolled = false;
                                foreach ($enrolled_courses as $enrolled) {
                                    if ($enrolled['id'] == $course['id']) {
                                        $is_enrolled = true;
                                        break;
                                    }
                                }
                                
                                if ($is_enrolled) continue;
                            ?>
                                <div class="col">
                                    <div class="card h-100 course-card">
                                        <?php if (!empty($course['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($course['image_url']); ?>" 
                                                class="card-img-top" 
                                                alt="<?php echo htmlspecialchars($course['name']); ?>">
                                        <?php else: ?>
                                            <div class="card-img-top bg-light d-flex justify-content-center align-items-center" style="height: 180px;">
                                                <i class="bi bi-journal-text" style="font-size: 4rem; color: var(--primary-color);"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo htmlspecialchars($course['name']); ?></h5>
                                            <p class="card-text mb-4"><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...</p>
                                            <div class="mt-auto d-flex justify-content-<?php echo get_current_direction() === 'rtl' ? 'start' : 'end'; ?>">
                                                <a href="course_details.php?id=<?php echo $course['id']; ?>" class="btn btn-primary">
                                                    <?php echo __('view_details'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php $first = false; endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include 'includes/new-footer.php'; ?>
