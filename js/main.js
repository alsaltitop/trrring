// التحقق من تسجيل الدخول
document.getElementById('loginBtn').addEventListener('click', function() {
    // هنا يمكن إضافة منطق تسجيل الدخول
    alert('سيتم توجيهك إلى صفحة تسجيل الدخول');
});

// تحريك سلس للروابط
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// إضافة تأثيرات التمرير
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.backgroundColor = '#0d6efd';
    } else {
        navbar.style.backgroundColor = 'transparent';
    }
}); 