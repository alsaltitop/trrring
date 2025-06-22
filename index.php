<?php
require_once 'config.php';

// تضمين ملف اللغة
require_once 'includes/language.php';

$page_title = __('home');
include 'includes/new-header.php';

$stmt = $pdo->query("SELECT id, name, description, image_url FROM courses ORDER BY id");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = __('home') . ' - ' . __('site_name');

<!-- Hero Section -->
<div class="container-fluid bg-light py-5 text-center hero-section">
    <div class="container">
        <h1 class="display-4 fw-bold" style="color: #005776;"><?php echo __('welcome_message'); ?></h1>
        <p class="lead text-muted"><?php echo __('welcome_desc'); ?></p>
        <a href="#courses" class="btn btn-primary btn-lg <?php echo get_current_direction() === 'rtl' ? 'me-2' : 'ms-2'; ?>"><?php echo __('all_courses'); ?></a>
        <?php if (!isset($_SESSION['user'])): ?>
            <a href="signup.php" class="btn btn-outline-secondary btn-lg"><?php echo __('signup'); ?></a>
        <?php endif; ?>
    </div>
</div>

<!-- Courses Section -->
<div class="container py-5" id="courses">
    <h2 class="text-center mb-5 fw-bold"><?php echo __('all_courses'); ?></h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($courses as $course): ?>
            <div class="col">
                <div class="card h-100 shadow-sm course-card">
                    <img src="<?php echo htmlspecialchars($course['image_url'] ?? ''); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($course['name'] ?? ''); ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($course['name'] ?? ''); ?></h5>
                        <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($course['description'] ?? ''); ?></p>
                        <a href="<?php echo isset($_SESSION['user']) ? 'course_details.php?id=' . $course['id'] : 'login.php'; ?>" class="btn btn-primary mt-auto align-self-<?php echo get_current_direction() === 'rtl' ? 'start' : 'end'; ?>"><?php echo isset($_SESSION['user']) ? __('view_details') : __('enroll_now'); ?></a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/new-footer.php'; ?>
