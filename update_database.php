<?php
// Connect to database
require_once 'config.php';

try {
    // Create course_progress table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS course_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        course_id INT NOT NULL,
        progress INT DEFAULT 0,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
        UNIQUE KEY user_course (user_id, course_id)
    )");
    echo "Success: Table 'course_progress' created or already exists.<br>";

    // Create course_modules table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS course_modules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        sort_order INT DEFAULT 0,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )");
    echo "Success: Table 'course_modules' created or already exists.<br>";
    
    // Add sample modules for existing courses
    $coursesStmt = $pdo->query("SELECT id FROM courses");
    $courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($courses as $course) {
        // Check if course already has modules
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM course_modules WHERE course_id = ?");
        $checkStmt->execute([$course['id']]);
        $moduleCount = $checkStmt->fetchColumn();
        
        if ($moduleCount == 0) {
            // Add default modules for this course
            $moduleInsert = $pdo->prepare("INSERT INTO course_modules 
                (course_id, title, description, sort_order) VALUES 
                (?, 'مقدمة الدورة', 'تعريف بالدورة وأهدافها وكيفية الاستفادة منها.', 1),
                (?, 'أساسيات النظام', 'شرح للمفاهيم الأساسية والواجهة الرئيسية للنظام.', 2),
                (?, 'العمليات الأساسية', 'شرح العمليات الأكثر استخداماً في النظام بالتفصيل.', 3),
                (?, 'التطبيقات العملية', 'أمثلة عملية وتمارين تطبيقية على النظام.', 4),
                (?, 'الاختبار النهائي', 'اختبار لقياس مستوى تحصيل المتدرب للمحتوى التعليمي.', 5)");
            $moduleInsert->execute([$course['id'], $course['id'], $course['id'], $course['id'], $course['id']]);
            echo "Added default modules to course ID: {$course['id']}<br>";
        }
    }
    
    echo "<br>Database update completed successfully!";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
