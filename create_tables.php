<?php
// إظهار الأخطاء للتشخيص
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// اتصال بقاعدة البيانات
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

echo "<h1>إنشاء جداول قاعدة البيانات</h1>";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // إنشاء جداول النظام إذا لم تكن موجودة
    
    // جدول المستخدمين (إذا لم يكن موجودًا بالفعل)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            fullname VARCHAR(100) NOT NULL,
            department VARCHAR(100) NOT NULL,
            role ENUM('admin', 'user', 'instructor') NOT NULL DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "<p>✅ تم التحقق من جدول المستخدمين (users)</p>";
    
    // جدول تصنيفات الدورات
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "<p>✅ تم التحقق من جدول التصنيفات (categories)</p>";
    
    // جدول الدورات
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS courses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            category_id INT NOT NULL,
            image_url VARCHAR(255) DEFAULT 'images/course-default.jpg',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "<p>✅ تم التحقق من جدول الدورات (courses)</p>";
    
    // جدول الوحدات التعليمية للدورات
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS course_modules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            order_number INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "<p>✅ تم التحقق من جدول وحدات الدورات (course_modules)</p>";
    
    // جدول التسجيلات (المفقود)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS enrollments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            course_id INT NOT NULL,
            enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            completed BOOLEAN DEFAULT FALSE,
            UNIQUE KEY user_course (user_id, course_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "<p>✅ تم إنشاء جدول التسجيلات (enrollments)</p>";
    
    // جدول تقدم الطالب في الدورة
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS course_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            module_id INT NOT NULL,
            is_completed BOOLEAN DEFAULT FALSE,
            completion_date TIMESTAMP NULL,
            UNIQUE KEY user_module (user_id, module_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (module_id) REFERENCES course_modules(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "<p>✅ تم التحقق من جدول تقدم الطالب (course_progress)</p>";
    
    // إضافة بيانات افتراضية للتصنيفات إذا لم يكن هناك بيانات
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO categories (name, description) VALUES
            ('نظام الشفاء', 'دورات تدريبية خاصة بنظام الشفاء الإلكتروني'),
            ('الأنظمة الطبية', 'دورات تدريبية للأنظمة الطبية المختلفة'),
            ('التقنيات الصحية', 'دورات تدريبية في مجال التقنيات الصحية الحديثة')
        ");
        echo "<p>✅ تم إضافة بيانات افتراضية لجدول التصنيفات</p>";
    }
    
    // إضافة دورات افتراضية إذا لم يكن هناك دورات
    $stmt = $pdo->query("SELECT COUNT(*) FROM courses");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO courses (name, description, category_id, image_url) VALUES
            ('أساسيات نظام الشفاء', 'تعلم أساسيات استخدام نظام الشفاء الإلكتروني للرعاية الصحية', 1, 'images/course1.jpg'),
            ('إدارة السجلات الطبية', 'كيفية إنشاء وإدارة السجلات الطبية للمرضى بفعالية', 1, 'images/course2.jpg'),
            ('إدارة المواعيد', 'تعلم كيفية إدارة مواعيد المرضى في نظام الشفاء', 2, 'images/course3.jpg'),
            ('التقارير الطبية', 'دورة متقدمة في إعداد واستخراج التقارير الطبية من النظام', 2, 'images/course4.jpg'),
            ('تطبيقات الهاتف الصحية', 'كيفية استخدام التطبيقات الصحية على الأجهزة المحمولة', 3, 'images/course5.jpg')
        ");
        echo "<p>✅ تم إضافة بيانات افتراضية لجدول الدورات</p>";
    }
    
    // إضافة وحدات تعليمية افتراضية للدورات
    $stmt = $pdo->query("SELECT COUNT(*) FROM course_modules");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
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
        echo "<p>✅ تم إضافة بيانات افتراضية لوحدات الدورات</p>";
    }
    
    // إضافة تسجيل افتراضي للمستخدم الإداري
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin' LIMIT 1");
    $stmt->execute();
    $admin_id = $stmt->fetchColumn();
    
    if ($admin_id) {
        $pdo->exec("
            INSERT IGNORE INTO enrollments (user_id, course_id) VALUES
            ($admin_id, 1),
            ($admin_id, 2)
        ");
        echo "<p>✅ تم تسجيل المشرف في دورتين افتراضيتين</p>";
    }
    
    echo "<div class='alert alert-success mt-4'>";
    echo "<h2>تم إنشاء وتحديث جميع الجداول المطلوبة بنجاح!</h2>";
    echo "<p>الآن يمكنك الانتقال إلى <a href='login.php' class='btn btn-primary'>صفحة تسجيل الدخول</a> للدخول إلى النظام.</p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h2>خطأ في قاعدة البيانات</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
