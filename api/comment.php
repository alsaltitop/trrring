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
$content = $data['content'] ?? null;

if (!$course_id || !$content) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'معرف الدورة والمحتوى مطلوبان']);
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

    // إضافة التعليق
    $stmt = $pdo->prepare('INSERT INTO course_comments (user_id, course_id, content, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$_SESSION['user_id'], $course_id, $content]);

    // جلب معلومات المستخدم
    $stmt = $pdo->prepare('SELECT full_name, avatar FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'full_name' => $user['full_name'],
        'avatar' => $user['avatar']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء إضافة التعليق']);
} 