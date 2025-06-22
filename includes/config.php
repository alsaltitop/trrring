<?php
// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_NAME', 'moodle');
define('DB_USER', 'root');
define('DB_PASS', '');

// إعدادات الموقع
define('SITE_NAME', 'منصة شفاء التعليمية');
define('SITE_URL', 'http://localhost/web');
define('ADMIN_EMAIL', 'admin@moh.gov.om');

// إعدادات المصادقة
define('AUTH_SAML_ENABLED', true);
define('AUTH_SAML_IDP_URL', 'https://sso.moh.gov.om/saml2/idp');
define('AUTH_SAML_SP_URL', 'https://training.moh.gov.om/saml2/sp');

// إعدادات اللغة
define('DEFAULT_LANG', 'ar');
define('AVAILABLE_LANGUAGES', ['ar', 'en']);

// إعدادات الأمان
define('SESSION_LIFETIME', 3600); // ساعة واحدة
define('PASSWORD_HASH_COST', 12);

// إعدادات الملفات
define('UPLOAD_MAX_SIZE', 10485760); // 10MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'jpg', 'png']);

// إعدادات البريد الإلكتروني
define('SMTP_HOST', 'smtp.moh.gov.om');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@moh.gov.om');
define('SMTP_PASS', '');

// إعدادات النسخ الاحتياطي
define('BACKUP_DIR', __DIR__ . '/../backups');
define('BACKUP_RETENTION_DAYS', 30);

// إعدادات الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تكوين المنطقة الزمنية
date_default_timezone_set('Asia/Muscat');

// تكوين معالجة الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// دالة للتحقق من تسجيل الدخول
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// دالة للحصول على معلومات المستخدم الحالي
function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) return null;
    
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
} 