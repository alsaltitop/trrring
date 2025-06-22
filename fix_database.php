<?php
// إظهار الأخطاء للتشخيص
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>إصلاح قاعدة البيانات وإعادة إنشائها</h1>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; direction: rtl; }
.success { color: green; }
.error { color: red; }
</style>";

// الاتصال بقاعدة البيانات
$host = 'localhost';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    // الاتصال بخادم MySQL بدون تحديد قاعدة البيانات
    $rootPdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $db = 'alshifa_training';
    
    // حذف قاعدة البيانات إذا كانت موجودة
    $rootPdo->exec("DROP DATABASE IF EXISTS `$db`");
    echo "<p class='success'>✓ تم حذف قاعدة البيانات القديمة</p>";
    
    // إنشاء قاعدة البيانات من جديد
    $rootPdo->exec("CREATE DATABASE `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p class='success'>✓ تم إنشاء قاعدة البيانات الجديدة</p>";
    
    // الاتصال بقاعدة البيانات الجديدة
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // إنشاء جدول المستخدمين
    $pdo->exec("
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            fullname VARCHAR(100) NOT NULL,
            department VARCHAR(100) NOT NULL,
            role ENUM('admin', 'user', 'instructor') NOT NULL DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p class='success'>✓ تم إنشاء جدول المستخدمين</p>";
    
    // إنشاء جدول التصنيفات
    $pdo->exec("
        CREATE TABLE categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p class='success'>✓ تم إنشاء جدول التصنيفات</p>";
    
    // إنشاء جدول الدورات
    $pdo->exec("
        CREATE TABLE courses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            category_id INT NOT NULL,
            image_url VARCHAR(255) DEFAULT 'images/course-default.jpg',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p class='success'>✓ تم إنشاء جدول الدورات</p>";
    
    // إنشاء جدول الوحدات التعليمية
    $pdo->exec("
        CREATE TABLE course_modules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            order_number INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (course_id) REFERENCES courses(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p class='success'>✓ تم إنشاء جدول وحدات الدورات</p>";
    
    // إنشاء جدول التسجيلات
    $pdo->exec("
        CREATE TABLE enrollments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            course_id INT NOT NULL,
            enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            completed BOOLEAN DEFAULT FALSE,
            UNIQUE KEY user_course (user_id, course_id),
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (course_id) REFERENCES courses(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p class='success'>✓ تم إنشاء جدول التسجيلات</p>";
    
    // إنشاء جدول تقدم الطالب
    $pdo->exec("
        CREATE TABLE course_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            module_id INT NOT NULL,
            is_completed BOOLEAN DEFAULT FALSE,
            completion_date TIMESTAMP NULL,
            UNIQUE KEY user_module (user_id, module_id),
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (module_id) REFERENCES course_modules(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p class='success'>✓ تم إنشاء جدول تقدم الطالب</p>";
    
    // إضافة البيانات الافتراضية
    
    // إضافة مستخدم مشرف افتراضي
    $hashDefault = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("
        INSERT INTO users (fullname, email, department, username, password, role)
        VALUES ('Administrator', 'admin@health.gov.om', 'IT', 'admin', '$hashDefault', 'admin')
    ");
    echo "<p class='success'>✓ تم إنشاء حساب المشرف الافتراضي (admin/admin123)</p>";
    
    // إضافة تصنيفات افتراضية
    $pdo->exec("
        INSERT INTO categories (name, description) VALUES
        ('نظام الشفاء', 'دورات تدريبية خاصة بنظام الشفاء الإلكتروني'),
        ('الأنظمة الطبية', 'دورات تدريبية للأنظمة الطبية المختلفة'),
        ('التقنيات الصحية', 'دورات تدريبية في مجال التقنيات الصحية الحديثة')
    ");
    echo "<p class='success'>✓ تم إضافة التصنيفات الافتراضية</p>";
    
    // إضافة دورات افتراضية
    $pdo->exec("
        INSERT INTO courses (name, description, category_id, image_url) VALUES
        ('أساسيات نظام الشفاء', 'تعلم أساسيات استخدام نظام الشفاء الإلكتروني للرعاية الصحية', 1, 'images/course1.jpg'),
        ('إدارة السجلات الطبية', 'كيفية إنشاء وإدارة السجلات الطبية للمرضى بفعالية', 1, 'images/course2.jpg'),
        ('إدارة المواعيد', 'تعلم كيفية إدارة مواعيد المرضى في نظام الشفاء', 2, 'images/course3.jpg'),
        ('التقارير الطبية', 'دورة متقدمة في إعداد واستخراج التقارير الطبية من النظام', 2, 'images/course4.jpg'),
        ('تطبيقات الهاتف الصحية', 'كيفية استخدام التطبيقات الصحية على الأجهزة المحمولة', 3, 'images/course5.jpg')
    ");
    echo "<p class='success'>✓ تم إضافة الدورات الافتراضية</p>";
    
    // إضافة وحدات تعليمية افتراضية
    $pdo->exec("
        INSERT INTO course_modules (course_id, title, content, order_number) VALUES
        (1, 'مقدمة في نظام الشفاء', 'محتوى تعريفي عن نظام الشفاء وأهميته في القطاع الصحي العماني.', 1),
        (1, 'تسجيل الدخول وإدارة الحساب', 'خطوات تسجيل الدخول وإدارة حساب المستخدم في نظام الشفاء.', 2),
        (1, 'الواجهة الرئيسية', 'شرح مفصل للواجهة الرئيسية وعناصرها المختلفة.', 3),
        (2, 'إنشاء سجل طبي جديد', 'خطوات إنشاء سجل طبي جديد للمريض وإدخال البيانات الأساسية.', 1),
        (2, 'البحث في السجلات الطبية', 'كيفية البحث عن سجلات طبية باستخدام معايير مختلفة.', 2),
        (3, 'جدولة المواعيد', 'كيفية إنشاء وإدارة جدول المواعيد للمرضى والأطباء.', 1),
        (3, 'إشعارات المواعيد', 'إعداد وإدارة إشعارات المواعيد للمرضى.', 2),
        (4, 'أنواع التقارير الطبية', 'استعراض لأنواع التقارير الطبية المختلفة في النظام.', 1),
        (4, 'إنشاء وتصدير التقارير', 'خطوات إنشاء وتصدير التقارير بصيغ مختلفة.', 2),
        (5, 'تثبيت تطبيق الهاتف', 'خطوات تثبيت وإعداد تطبيق الشفاء على الهاتف.', 1),
        (5, 'استخدام تطبيق الهاتف', 'شرح مفصل لكيفية استخدام تطبيق الشفاء على الهاتف.', 2)
    ");
    echo "<p class='success'>✓ تم إضافة وحدات الدورات الافتراضية</p>";
    
    // تسجيل المشرف في بعض الدورات
    $admin_id = $pdo->query("SELECT id FROM users WHERE username = 'admin'")->fetchColumn();
    $pdo->exec("
        INSERT INTO enrollments (user_id, course_id) VALUES
        ($admin_id, 1),
        ($admin_id, 2)
    ");
    echo "<p class='success'>✓ تم تسجيل المشرف في دورتين افتراضيتين</p>";
    
    echo "<div style='margin-top: 20px; padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<h2 style='color: #155724;'>تم إنشاء قاعدة البيانات وتهيئتها بنجاح!</h2>";
    echo "<p>يمكنك الآن الانتقال إلى <a href='login.php' style='color: #155724; font-weight: bold;'>صفحة تسجيل الدخول</a> لاستخدام النظام.</p>";
    echo "<p>بيانات تسجيل الدخول الافتراضية:</p>";
    echo "<ul><li>اسم المستخدم: admin</li><li>كلمة المرور: admin123</li></ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='margin-top: 20px; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h2 style='color: #721c24;'>خطأ في قاعدة البيانات</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
