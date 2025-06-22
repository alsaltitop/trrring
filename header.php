<?php
// تأكد من عدم تكرار فتح الجلسة لتجنب تعارض الجلسات
// ملاحظة: لا تفتح الجلسة هنا لأن الجلسة يجب أن تفتح في الملفات المستدعية لهذا الملف

// تضمين ملف التحكم باللغات
require_once 'includes/language.php';
?>

<!DOCTYPE html>
<html lang="<?php echo get_current_language(); ?>" dir="<?php echo get_current_direction(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : __('site_name'); ?></title>
    
    <!-- Bootstrap CSS -->
    <?php if (get_current_direction() === 'rtl'): ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet" integrity="sha384-nU14brUcp6StFntEOOEBvcJm4huWjB0OcIeQ3fltAfSmuZFrkAif0T+UtNGlKKQv" crossorigin="anonymous">
    <?php else: ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <?php endif; ?>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="images/moh-logo.png" alt="<?php echo get_current_language() === 'ar' ? 'شعار وزارة الصحة' : 'Ministry of Health Logo'; ?>" style="height: 40px;" class="d-inline-block align-text-top <?php echo get_current_direction() === 'rtl' ? 'me-2' : 'ms-2'; ?>">
            <span style="font-weight: 700; color: #005776;"><?php echo __('site_name'); ?></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><?php echo __('home'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#courses"><?php echo __('courses'); ?></a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <!-- Language Switcher -->
                <div class="dropdown me-3">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo __('language'); ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                        <li><a class="dropdown-item <?php echo get_current_language() === 'ar' ? 'active' : ''; ?>" href="<?php echo get_language_switch_link('ar'); ?>">العربية</a></li>
                        <li><a class="dropdown-item <?php echo get_current_language() === 'en' ? 'active' : ''; ?>" href="<?php echo get_language_switch_link('en'); ?>">English</a></li>
                    </ul>
                </div>
                <?php if (isset($_SESSION['user'])): ?>
                    <span class="navbar-text <?php echo get_current_direction() === 'rtl' ? 'me-3' : 'ms-3'; ?>">
                        <?php echo __('dashboard_welcome'); ?> <?php echo htmlspecialchars($_SESSION['user']); ?>
                    </span>
                    <a href="dashboard.php" class="btn btn-outline-primary <?php echo get_current_direction() === 'rtl' ? 'me-2' : 'ms-2'; ?>"><?php echo __('dashboard'); ?></a>
                    <a href="logout.php" class="btn btn-primary"><?php echo __('logout'); ?></a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-primary <?php echo get_current_direction() === 'rtl' ? 'me-2' : 'ms-2'; ?>"><?php echo __('login'); ?></a>
                    <a href="signup.php" class="btn btn-primary"><?php echo __('signup'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main class="flex-shrink-0">
