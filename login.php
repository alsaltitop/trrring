<?php
// إظهار الأخطاء للتشخيص
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// بدء جلسة المستخدم قبل أي استخدام لمتغيرات الجلسة
session_start();

require_once 'config.php';
require_once 'includes/language.php';

// التأكد من وجود جلسة نشطة
echo "<!-- معرف الجلسة: " . session_id() . " -->";

// توجيه المستخدم إلى لوحة التحكم إذا كان مسجل الدخول بالفعل
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch user from DB
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $userRow = $stmt->fetch();

    if ($userRow && password_verify($password, $userRow['password'])) {
        // Login success
        $_SESSION['user'] = $userRow['username']; // Use username
        $_SESSION['userid'] = $userRow['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error_message = __('invalid_credentials');
    }
}

$page_title = __('login');
include 'header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <img src="images/moh-logo.png" alt="<?php echo __('moh_logo'); ?>" style="height: 80px;">
                        <h3 class="mt-3 mb-0" style="color: #005776;"><?php echo __('login'); ?></h3>
                        <p class="text-muted"><?php echo __('login_subtitle'); ?></p>
                    </div>

                    <?php if(isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label"><?php echo __('username'); ?></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label"><?php echo __('password'); ?></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg"><?php echo __('login'); ?></button>
                            <button type="button" class="btn btn-outline-secondary"><?php echo __('login_sso'); ?></button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <a href="signup.php" class="text-decoration-none"><?php echo __('no_account'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
