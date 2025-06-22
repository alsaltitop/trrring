<?php
// عرض جميع الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);

// تضمين ملف الإعدادات
require_once 'config.php';

echo "<h1>اختبار الاتصال بقاعدة البيانات</h1>";

try {
    // اختبار الاتصال
    $pdo->query('SELECT 1');
    echo "<p style='color: green;'>✓ تم الاتصال بقاعدة البيانات بنجاح</p>";

    // اختبار قاعدة البيانات
    $stmt = $pdo->query('SELECT DATABASE()');
    $db = $stmt->fetchColumn();
    echo "<p>قاعدة البيانات الحالية: " . htmlspecialchars($db) . "</p>";

    // اختبار الجداول
    $tables = [
        'users',
        'courses',
        'categories',
        'course_modules',
        'user_courses',
        'course_comments',
        'user_favorites'
    ];

    echo "<h2>التحقق من الجداول:</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        try {
            $pdo->query("SELECT 1 FROM $table LIMIT 1");
            echo "<li style='color: green;'>✓ جدول " . htmlspecialchars($table) . " موجود</li>";
        } catch (PDOException $e) {
            echo "<li style='color: red;'>✗ جدول " . htmlspecialchars($table) . " غير موجود</li>";
        }
    }
    echo "</ul>";

    // اختبار إعدادات PHP
    echo "<h2>إعدادات PHP:</h2>";
    echo "<ul>";
    echo "<li>إصدار PHP: " . phpversion() . "</li>";
    echo "<li>المنطقة الزمنية: " . date_default_timezone_get() . "</li>";
    echo "<li>الترميز: " . ini_get('default_charset') . "</li>";
    echo "</ul>";

    // اختبار الصلاحيات
    echo "<h2>التحقق من الصلاحيات:</h2>";
    $directories = [
        '../uploads',
        '../logs',
        '../backups'
    ];

    echo "<ul>";
    foreach ($directories as $dir) {
        if (is_writable($dir)) {
            echo "<li style='color: green;'>✓ المجلد " . htmlspecialchars($dir) . " قابل للكتابة</li>";
        } else {
            echo "<li style='color: red;'>✗ المجلد " . htmlspecialchars($dir) . " غير قابل للكتابة</li>";
        }
    }
    echo "</ul>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ خطأ في الاتصال بقاعدة البيانات: " . htmlspecialchars($e->getMessage()) . "</p>";
    
    // عرض معلومات الاتصال (بدون كلمة المرور)
    echo "<h2>معلومات الاتصال:</h2>";
    echo "<ul>";
    echo "<li>المضيف: " . DB_HOST . "</li>";
    echo "<li>قاعدة البيانات: " . DB_NAME . "</li>";
    echo "<li>المستخدم: " . DB_USER . "</li>";
    echo "</ul>";
} 