<?php
// إظهار جميع الأخطاء للتشخيص
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// بدء جلسة 
session_start();

echo "<h1>التحقق من حالة الجلسة</h1>";
echo "<p>معرف الجلسة الحالي: " . session_id() . "</p>";

if(isset($_SESSION['test_user']) && isset($_SESSION['test_userid'])) {
    echo '<p style="color: green; font-weight: bold;">نجاح! تم استعادة بيانات الجلسة بنجاح.</p>';
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    
    echo '<p>الخطوة التالية: <a href="login.php">محاولة تسجيل الدخول</a></p>';
    echo '<hr>';
    echo '<p>معلومات عن ملفات تعريف الارتباط (Cookies):</p>';
    echo '<pre>' . print_r($_COOKIE, true) . '</pre>';
} else {
    echo '<p style="color: red; font-weight: bold;">فشل! لم يتم العثور على بيانات الجلسة.</p>';
    echo '<p>بيانات الجلسة الموجودة:</p>';
    echo '<pre>' . print_r($_SESSION, true) . '</pre>';
    
    echo '<p>أسباب محتملة:</p>';
    echo '<ul>';
    echo '<li>خطأ في إعدادات PHP لملفات الجلسة</li>';
    echo '<li>إعدادات ملفات تعريف الارتباط (Cookies) غير صحيحة</li>';
    echo '<li>حظر متصفحك لملفات تعريف الارتباط</li>';
    echo '<li>مشاكل في تكوين خادم الويب</li>';
    echo '</ul>';
    
    echo '<p>معلومات عن ملفات تعريف الارتباط (Cookies):</p>';
    echo '<pre>' . print_r($_COOKIE, true) . '</pre>';
    
    echo '<p>معلومات عن إعدادات PHP:</p>';
    echo '<p>مسار الجلسة: ' . session_save_path() . '</p>';
    echo '<p>اسم الجلسة: ' . session_name() . '</p>';
}

// إضافة زر للعودة
echo '<p><a href="session_test.php">العودة لإنشاء جلسة اختبار جديدة</a></p>';
?>
