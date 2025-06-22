<?php
// إظهار جميع الأخطاء للتشخيص
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// بدء جلسة 
session_start();

// إنشاء جلسة اختبار
$_SESSION['test_user'] = 'admin';
$_SESSION['test_userid'] = 1;
$_SESSION['timestamp'] = time();

echo "<h1>اختبار الجلسة</h1>";
echo "<p>تم إنشاء جلسة اختبار. معرف الجلسة: " . session_id() . "</p>";
echo "<p>معلومات الجلسة:</p>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<p><a href='session_verify.php'>انقر هنا للتحقق من استمرار الجلسة</a></p>";
?>
