<?php
require_once 'config.php';
require_once 'includes/language.php';

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;

// Track module completion
if (isset($_POST['complete_module']) && $user_id > 0) {
    $module_id = (int)$_POST['module_id'];
    
    // Verify module belongs to this course
    $checkStmt = $pdo->prepare("SELECT id FROM course_modules WHERE id = ? AND course_id = ?");
    $checkStmt->execute([$module_id, $course_id]);
    
    if ($checkStmt->fetch()) {
        // Get current progress
        $progressStmt = $pdo->prepare("SELECT id, progress FROM course_progress WHERE user_id = ? AND course_id = ?");
        $progressStmt->execute([$user_id, $course_id]);
        $progressData = $progressStmt->fetch(PDO::FETCH_ASSOC);
        
        // Get total number of modules
        $totalModulesStmt = $pdo->prepare("SELECT COUNT(*) FROM course_modules WHERE course_id = ?");
        $totalModulesStmt->execute([$course_id]);
        $totalModules = $totalModulesStmt->fetchColumn();
        
        // Calculate new progress percentage
        $newProgress = $totalModules > 0 ? round(($module_id / $totalModules) * 100) : 0;
        
        if ($progressData) {
            // Update existing progress if new is higher
            if ($newProgress > $progressData['progress']) {
                $updateStmt = $pdo->prepare("UPDATE course_progress SET progress = ? WHERE id = ?");
                $updateStmt->execute([$newProgress, $progressData['id']]);
            }
        } else {
            // Insert new progress record
            $insertStmt = $pdo->prepare("INSERT INTO course_progress (user_id, course_id, progress) VALUES (?, ?, ?)");
            $insertStmt->execute([$user_id, $course_id, $newProgress]);
        }
        
        $message = '<div class="alert alert-success">' . __('progress_updated') . '</div>';
    }
}

if ($course_id === 0) {
    header('Location: index.php');
    exit;
}

$message = '';
$is_enrolled = false;

// Handle enrollment request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    if ($user_id > 0) {
        // Check if already enrolled
        $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);
        if ($stmt->fetch()) {
            $message = '<div class="alert alert-warning">' . __('already_enrolled') . '</div>';
        } else {
            // Enroll the user
            $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
            if ($stmt->execute([$user_id, $course_id])) {
                $message = '<div class="alert alert-success">' . __('enrollment_success') . '</div>';
            }
        }
    } else {
        // Redirect to login if not logged in
        header('Location: login.php?redirect=course_details.php?id=' . $course_id);
        exit;
    }
}

// Fetch course details
$stmt = $pdo->prepare("SELECT co.*, ca.name as category_name FROM courses co JOIN categories ca ON co.category_id = ca.id WHERE co.id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    http_response_code(404);
    $page_title = __('course_not_found');
    include 'header.php';
    echo '<div class="container my-5 text-center"><div class="alert alert-danger"><h2>' . __('error_404') . '</h2><p>' . __('course_not_found_desc') . '</p><a href="index.php" class="btn btn-primary">' . __('back_to_home') . '</a></div></div>';
    include 'footer.php';
    exit;
}

// Check enrollment status for button display
if ($user_id > 0) {
    $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    if ($stmt->fetch()) {
        $is_enrolled = true;
        
        // Fetch user's progress for this course
        $progressStmt = $pdo->prepare("SELECT progress FROM course_progress WHERE user_id = ? AND course_id = ?");
        $progressStmt->execute([$user_id, $course_id]);
        $progressData = $progressStmt->fetch(PDO::FETCH_ASSOC);
        $userProgress = $progressData ? $progressData['progress'] : 0;
    }
}

// Fetch course modules
$modulesStmt = $pdo->prepare("SELECT id, title, description, sort_order FROM course_modules WHERE course_id = ? ORDER BY sort_order");
$modulesStmt->execute([$course_id]);
$modules = $modulesStmt->fetchAll(PDO::FETCH_ASSOC);
$totalModules = count($modules);

$page_title = htmlspecialchars($course['name']);
include 'header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            
            <?php echo $message; // Display enrollment messages ?>

            <div class="card shadow-sm">
                <img src="<?php echo htmlspecialchars($course['image_url'] ?? ''); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($course['name'] ?? ''); ?>" style="max-height: 400px; object-fit: cover;">
                <div class="card-body p-4">
                    <span class="badge bg-primary mb-3"><?php echo htmlspecialchars($course['category_name'] ?? ''); ?></span>
                    <h1 class="card-title" style="color: #005776;"><?php echo htmlspecialchars($course['name'] ?? ''); ?></h1>
                    
                    <?php if ($is_enrolled): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="card-title display-6"><?php echo __('your_progress'); ?></h3>
                        <span class="badge bg-primary rounded-pill fs-6"><?php echo $userProgress; ?>%</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $userProgress; ?>%;"
                            aria-valuenow="<?php echo $userProgress; ?>" aria-valuemin="0" aria-valuemax="100">
                            <?php echo $userProgress; ?>%
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <p class="lead card-text mt-3">
                        <?php echo nl2br(htmlspecialchars($course['description'] ?? '')); ?>
                    </p>
                    
                    <hr class="my-4">
                    
                    <?php if (!$is_enrolled): ?>
                    <form method="POST" action="">
                        <div class="d-grid gap-2">
                            <button class="btn btn-success btn-lg" type="submit" name="enroll"><?php echo __('enroll_now'); ?></button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($is_enrolled && !empty($modules)): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h3 class="mb-0"><?php echo __('course_modules'); ?></h3>
                </div>
                <div class="card-body">
                    <div class="accordion" id="moduleAccordion">
                        <?php foreach ($modules as $index => $module): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?php echo $module['id']; ?>">
                                    <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse<?php echo $module['id']; ?>" 
                                            aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                                            aria-controls="collapse<?php echo $module['id']; ?>">
                                        <?php echo htmlspecialchars($module['title'] ?? ''); ?>
                                        <?php 
                                        // Show check mark if this module contributes to the current progress
                                        if ($is_enrolled && isset($userProgress) && $userProgress >= round(($module['sort_order'] / $totalModules) * 100)): ?>
                                            <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                        <?php endif; ?>
                                    </button>
                                </h2>
                                <div id="collapse<?php echo $module['id']; ?>" 
                                     class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                                     aria-labelledby="heading<?php echo $module['id']; ?>" 
                                     data-bs-parent="#moduleAccordion">
                                    <div class="accordion-body">
                                        <p><?php echo htmlspecialchars($module['description'] ?? ''); ?></p>
                                        <form method="POST" action="" class="mt-3">
                                            <input type="hidden" name="module_id" value="<?php echo $module['id']; ?>">
                                            <button type="submit" name="complete_module" class="btn btn-outline-success">
                                                <i class="bi bi-check-lg"></i> <?php echo __('mark_complete'); ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
