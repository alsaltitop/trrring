<?php
// تشغيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// معلومات الإتصال بقاعدة البيانات
$host = 'localhost';
$db   = 'alshifa_training';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

echo "<h1>تشخيص صفحة لوحة التحكم</h1>";
echo "<h2>حالة الجلسة</h2>";
echo "معرف الجلسة: " . session_id() . "<br>";
echo "بيانات الجلسة: <pre>" . print_r($_SESSION, true) . "</pre><br>";

try {
    echo "<h2>اختبار الاتصال بقاعدة البيانات</h2>";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "تم الاتصال بقاعدة البيانات بنجاح<br>";

    // نتأكد من وجود بيانات
    echo "<h2>فحص البيانات</h2>";

    // التحقق من جدول المستخدمين
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "عدد المستخدمين: " . $userCount . "<br>";

    // التحقق من جدول الدورات
    $stmt = $pdo->query("SELECT COUNT(*) FROM courses");
    $courseCount = $stmt->fetchColumn();
    echo "عدد الدورات: " . $courseCount . "<br>";
    
    // تجربة استعلامات من ملف dashboard.php
    if (isset($_SESSION['userid'])) {
        $user_id = $_SESSION['userid'];
        echo "<h2>استعلام الدورات المسجلة للمستخدم (user_id = $user_id)</h2>";
        
        $stmt = $pdo->prepare("
            SELECT 
                c.id, 
                c.name, 
                c.description, 
                c.image_url, 
                cat.name as category_name,
                e.enrollment_date
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            JOIN categories cat ON c.category_id = cat.id
            WHERE e.user_id = ?
            ORDER BY e.enrollment_date DESC
        ");
        $stmt->execute([$user_id]);
        $enrolled_courses = $stmt->fetchAll();
        
        echo "عدد الدورات المسجلة: " . count($enrolled_courses) . "<br>";
        if (count($enrolled_courses) > 0) {
            echo "<ul>";
            foreach ($enrolled_courses as $course) {
                echo "<li>" . htmlspecialchars($course['name']) . "</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<div style='color: red; font-weight: bold;'>لم يتم العثور على معرف المستخدم في الجلسة!</div>";
    }
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold;'>خطأ في قاعدة البيانات: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<hr>";
echo "<h2>متغيرات النظام</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";

echo "<hr>";
echo "<a href='login.php' class='btn btn-primary'>العودة إلى صفحة تسجيل الدخول</a>";
?>
