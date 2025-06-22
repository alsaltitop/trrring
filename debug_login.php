<?php
require_once 'config.php';

// إظهار المعلومات التفصيلية للأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>اختبار تسجيل الدخول</h2>";

// التحقق من حالة الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h3>معلومات الجلسة الحالية:</h3>";
echo "حالة الجلسة: " . (session_status() === PHP_SESSION_ACTIVE ? "نشطة" : "غير نشطة") . "<br>";
echo "معرف الجلسة: " . session_id() . "<br>";

echo "<h3>متغيرات الجلسة:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>اختبار الاتصال بقاعدة البيانات:</h3>";
try {
    // التحقق من الاتصال بقاعدة البيانات
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "تم الاتصال بقاعدة البيانات بنجاح.<br>";
    echo "عدد المستخدمين في النظام: " . $userCount . "<br>";
} catch (PDOException $e) {
    echo "خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage();
}

if (isset($_POST['test_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    echo "<h3>محاولة تسجيل دخول اختبارية:</h3>";
    echo "اسم المستخدم: " . htmlspecialchars($username) . "<br>";
    
    // محاولة تسجيل الدخول
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $userRow = $stmt->fetch();
    
    if ($userRow) {
        echo "تم العثور على المستخدم في قاعدة البيانات.<br>";
        if (password_verify($password, $userRow['password'])) {
            echo "كلمة المرور صحيحة.<br>";
            $_SESSION['user'] = $userRow['username'];
            $_SESSION['userid'] = $userRow['id'];
            echo "تم تعيين متغيرات الجلسة.<br>";
            
            // إظهار زر للانتقال إلى لوحة التحكم
            echo "<a href='dashboard.php' class='btn btn-success'>انتقل إلى لوحة التحكم</a>";
        } else {
            echo "كلمة المرور غير صحيحة.<br>";
        }
    } else {
        echo "لم يتم العثور على المستخدم في قاعدة البيانات.<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار تسجيل الدخول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">تجربة تسجيل الدخول</h4>
                        <form method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">اسم المستخدم</label>
                                <input type="text" class="form-control" id="username" name="username" value="admin">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input type="password" class="form-control" id="password" name="password" value="admin123">
                            </div>
                            <button type="submit" name="test_login" class="btn btn-primary">اختبار تسجيل الدخول</button>
                        </form>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="index.php" class="btn btn-secondary">العودة إلى الصفحة الرئيسية</a>
                    <a href="login.php" class="btn btn-info">صفحة تسجيل الدخول العادية</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
