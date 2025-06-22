<?php
/**
 * ملف التحكم باللغات
 * Language Controller File
 */

// بدء الجلسة إذا لم تكن قد بدأت بالفعل
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تحديد اللغات المدعومة
$supported_languages = ['ar', 'en'];

// التحقق إذا كان المستخدم يريد تغيير اللغة
if (isset($_GET['lang']) && in_array($_GET['lang'], $supported_languages)) {
    $_SESSION['lang'] = $_GET['lang'];
    
    // إعادة توجيه إلى نفس الصفحة بدون معلمة اللغة في URL
    $redirect = strtok($_SERVER['REQUEST_URI'], '?');
    $query = $_GET;
    unset($query['lang']);
    if (!empty($query)) {
        $redirect .= '?' . http_build_query($query);
    }
    header("Location: $redirect");
    exit;
}

// تعيين اللغة الافتراضية إذا لم يتم تعيين أي لغة
if (!isset($_SESSION['lang'])) {
    // يمكننا استخدام لغة المتصفح كاقتراح أولي
    $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'ar', 0, 2);
    $_SESSION['lang'] = in_array($browser_lang, $supported_languages) ? $browser_lang : 'ar';
}

// تحميل ملف اللغة المناسب
$lang_file = dirname(__DIR__) . '/languages/' . $_SESSION['lang'] . '.php';
if (file_exists($lang_file)) {
    include_once $lang_file;
} else {
    // تحميل اللغة العربية كلغة افتراضية في حالة عدم وجود الملف
    include_once dirname(__DIR__) . '/languages/ar.php';
}

/**
 * دالة للترجمة - تستخدم للحصول على نص في اللغة الحالية
 * 
 * @param string $key مفتاح النص في مصفوفة اللغة
 * @return string النص المترجم أو المفتاح نفسه إذا لم يوجد في مصفوفة اللغة
 */
function __($key) {
    global $lang;
    return $lang[$key] ?? $key;
}

/**
 * دالة للحصول على الاتجاه الحالي للغة
 * 
 * @return string rtl للعربية، ltr للإنجليزية وغيرها
 */
function get_current_direction() {
    return $_SESSION['lang'] === 'ar' ? 'rtl' : 'ltr';
}

/**
 * دالة للحصول على رمز اللغة الحالية
 * 
 * @return string رمز اللغة الحالية (ar أو en)
 */
function get_current_language() {
    return $_SESSION['lang'];
}

/**
 * دالة لإنشاء رابط تبديل اللغة
 * 
 * @param string $lang رمز اللغة المراد التبديل إليها
 * @return string الرابط المناسب لتبديل اللغة
 */
function get_language_switch_link($lang) {
    $current_url = $_SERVER['REQUEST_URI'];
    if (strpos($current_url, '?') !== false) {
        $base_url = strtok($current_url, '?');
        parse_str($_SERVER['QUERY_STRING'], $query_params);
        $query_params['lang'] = $lang;
        return $base_url . '?' . http_build_query($query_params);
    } else {
        return $current_url . '?lang=' . $lang;
    }
}
?>
