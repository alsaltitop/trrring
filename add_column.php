<?php
// Connect to database
require_once 'config.php';

try {
    // Add image_url column if it doesn't exist
    $checkColumn = "SHOW COLUMNS FROM courses LIKE 'image_url'";
    $columnExists = $pdo->query($checkColumn)->rowCount() > 0;
    
    if (!$columnExists) {
        $addColumn = "ALTER TABLE courses ADD COLUMN image_url VARCHAR(255) DEFAULT 'images/default-course.jpg'";
        $pdo->exec($addColumn);
        echo "Success: Column 'image_url' added to the 'courses' table.";
    } else {
        echo "Column 'image_url' already exists in the 'courses' table.";
    }
    
    // Update image_url for existing courses if they don't have values
    $updateDefault = "UPDATE courses SET image_url = 'images/default-course.jpg' WHERE image_url IS NULL";
    $stmt = $pdo->exec($updateDefault);
    echo "<br>{$stmt} courses updated with default image URL.";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
