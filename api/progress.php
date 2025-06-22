<?php
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول أولاً']);
    exit;
}

// التحقق من البيانات المرسلة
$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'] ?? null;
$module_id = $data['module_id'] ?? null;

if (!$course_id || !$module_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'معرف الدورة والوحدة مطلوبان']);
    exit;
}

try {
    // التحقق مما إذا كان المستخدم مسجل في الدورة
    $stmt = $pdo->prepare('SELECT id FROM user_courses WHERE user_id = ? AND course_id = ?');
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    $enrollment = $stmt->fetch();

    if (!$enrollment) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'يجب التسجيل في الدورة أولاً']);
        exit;
    }

    // التحقق مما إذا كانت الوحدة موجودة في الدورة
    $stmt = $pdo->prepare('SELECT id FROM course_modules WHERE id = ? AND course_id = ?');
    $stmt->execute([$module_id, $course_id]);
    $module = $stmt->fetch();

    if (!$module) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'الوحدة غير موجودة في الدورة']);
        exit;
    }

    // تحديث تقدم المستخدم
    $stmt = $pdo->prepare('
        UPDATE user_courses 
        SET progress = (
            SELECT (COUNT(*) * 100) / (
                SELECT COUNT(*) 
                FROM course_modules 
                WHERE course_id = ?
            )
            FROM user_progress 
            WHERE user_id = ? AND course_id = ?
        )
        WHERE user_id = ? AND course_id = ?
    ');
    $stmt->execute([$course_id, $_SESSION['user_id'], $course_id, $_SESSION['user_id'], $course_id]);

    // جلب التقدم المحدث
    $stmt = $pdo->prepare('SELECT progress FROM user_courses WHERE user_id = ? AND course_id = ?');
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    $progress = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'progress' => $progress['progress']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء تحديث التقدم']);
} 