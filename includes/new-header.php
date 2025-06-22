<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include language file if not already included
if (!function_exists('__')) {
    require_once 'language.php';
}

// Get current language direction
$dir = get_current_direction();
$lang = get_current_language();

// Set default title if not set
if (!isset($page_title)) {
    $page_title = __('site_title');
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['userid']) && !empty($_SESSION['userid']);

// Get user name if logged in
$user_name = $is_logged_in && isset($_SESSION['name']) ? $_SESSION['name'] : '';

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo __('site_title'); ?></title>
    
    <!-- Bootstrap CSS with RTL support if needed -->
    <?php if ($dir === 'rtl'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <?php else: ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <?php endif; ?>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts: Cairo (Arabic) and Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="assets/css/alshifa-theme.css">
    
    <!-- Additional Styles Based on Language -->
    <style>
        body {
            font-family: <?php echo ($lang === 'ar') ? "'Cairo', sans-serif" : "'Roboto', 'Cairo', sans-serif"; ?>;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/img/moh-logo-white.png" alt="<?php echo __('moh_logo'); ?>" height="40" class="me-2">
                <?php echo __('site_title'); ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="bi bi-house-door"></i> <?php echo __('home'); ?>
                        </a>
                    </li>
                    
                    <?php if ($is_logged_in): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> <?php echo __('dashboard'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                            <i class="bi bi-person-circle"></i> <?php echo __('profile'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> <?php echo __('logout'); ?>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" href="login.php">
                            <i class="bi bi-box-arrow-in-right"></i> <?php echo __('login'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'signup.php' ? 'active' : ''; ?>" href="signup.php">
                            <i class="bi bi-person-plus"></i> <?php echo __('signup'); ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Language Selector -->
                    <li class="nav-item dropdown lang-selector">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-globe"></i> <?php echo ($lang === 'ar') ? 'العربية' : 'English'; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item <?php echo ($lang === 'ar') ? 'active' : ''; ?>" href="?lang=ar">
                                    العربية
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($lang === 'en') ? 'active' : ''; ?>" href="?lang=en">
                                    English
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container py-4">
        <?php if ($is_logged_in): ?>
        <div class="welcome-user mb-4 d-none d-lg-block">
            <h5 class="text-muted"><?php echo __('welcome_user'); ?> <strong><?php echo htmlspecialchars($user_name); ?></strong></h5>
        </div>
        <?php endif; ?>
