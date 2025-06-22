-- SQL schema for Al-Shifa Training Platform
CREATE DATABASE IF NOT EXISTS alshifa_training DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE alshifa_training;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL,
  role ENUM('student','admin') DEFAULT 'student',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY (username),
  UNIQUE KEY (email)
);

-- Sample admin user (password: admin123)
INSERT INTO users (username,password,email,role,created_at)
VALUES ('admin','$2y$10$g.pIdGgEerl5Dno72a2jRu/iPzU5uCAeBvuHDUaYp2B9aV2mAe9t.','admin@moh.gov.om','admin','2024-06-17 08:00:00')
ON DUPLICATE KEY UPDATE username=username;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL
);

-- Sample categories
INSERT INTO categories (name) VALUES
('الأطباء'),
('الممرضون'),
('فنيو المختبر والأشعة'),
('السكرتارية الطبية')
ON DUPLICATE KEY UPDATE name=name;

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  image_url VARCHAR(255) DEFAULT 'images/default-course.jpg',
  category_id INT,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Sample courses
INSERT INTO courses (name,description,image_url,category_id) VALUES
('أساسيات تطبيق الشفاء','دورة تعريفية شاملة حول كيفية استخدام نظام الشفاء الإلكتروني.','images/course-alshifa-basics.jpg',1),
('المتقدم في تطبيق الشفاء','دورة متقدمة للأطباء تغطي ميزات تحليل البيانات والتقارير المتقدمة.','images/course-alshifa-advanced.jpg',1),
('الاستشارات الإلكترونية','تعلم كيفية إجراء وإدارة الاستشارات الطبية عن بعد عبر المنصة.','images/course-telemedicine.jpg',1),
('مقدمة عن تطبيق الشفاء','دورة أساسية للممرضين للتعامل مع سجلات المرضى والمهام اليومية.','images/course-nursing-intro.jpg',2),
('إدارة الحالات ومتابعتها','كيفية متابعة حالات المرضى المزمنة وتحديث خطط العلاج.','images/course-case-management.jpg',2),
('استخدام التطبيق في نتائج المختبر','دورة متخصصة لفنيي المختبرات حول إدخال وتفسير نتائج التحاليل.','images/course-lab.jpg',3),
('إدارة الصور والملفات الطبية','تدريب فنيي الأشعة على رفع وأرشفة صور الأشعة والتقارير المرتبطة بها.','images/course-radiology.jpg',3),
('إدخال البيانات واسترجاعها','دورة تدريبية لموظفي السكرتارية حول التعامل الفعال مع بيانات المرضى.','images/course-data-entry.jpg',4),
('التعامل مع الجداول الزمنية والحجوزات','كيفية تنظيم مواعيد العيادات والأطباء وإدارة حجوزات المرضى.','images/course-scheduling.jpg',4)
ON DUPLICATE KEY UPDATE name=name;

-- Enrollments table
CREATE TABLE IF NOT EXISTS enrollments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  course_id INT NOT NULL,
  enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  UNIQUE KEY user_course_unique (user_id, course_id)
);
