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

if (!$course_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'معرف الدورة مطلوب']);
    exit;
}

try {
    // التحقق مما إذا كان المستخدم مسجل بالفعل في الدورة
    $stmt = $pdo->prepare('SELECT id FROM user_courses WHERE user_id = ? AND course_id = ?');
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    $enrollment = $stmt->fetch();

    if ($enrollment) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'أنت مسجل بالفعل في هذه الدورة']);
        exit;
    }

    // تسجيل المستخدم في الدورة
    $stmt = $pdo->prepare('INSERT INTO user_courses (user_id, course_id, progress, enrolled_at) VALUES (?, ?, 0, NOW())');
    $stmt->execute([$_SESSION['user_id'], $course_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء التسجيل في الدورة']);
} 