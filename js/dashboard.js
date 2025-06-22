// تهيئة التقويم
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ar',
            direction: 'rtl',
            headerToolbar: {
                right: 'prev,next today',
                center: 'title',
                left: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'اليوم',
                month: 'شهر',
                week: 'أسبوع',
                day: 'يوم'
            },
            events: [
                // يمكن إضافة الأحداث هنا
                {
                    title: 'دورة تدريبية',
                    start: '2024-03-20',
                    end: '2024-03-22',
                    color: '#0d6efd'
                }
            ]
        });
        calendar.render();
    }
});

// تحديث شريط التقدم
function updateProgress(courseId, progress) {
    const progressBar = document.querySelector(`#course-${courseId} .progress-bar`);
    if (progressBar) {
        progressBar.style.width = `${progress}%`;
        progressBar.textContent = `${progress}%`;
    }
}

// إضافة إشعار
function addNotification(title, message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.innerHTML = `
        <strong>${title}</strong>
        <p class="mb-0">${message}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.notifications-container');
    if (container) {
        container.appendChild(notification);
        
        // إزالة الإشعار بعد 5 ثواني
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
}

// تحديث حالة الاتصال
function updateConnectionStatus() {
    const status = navigator.onLine ? 'متصل' : 'غير متصل';
    const statusElement = document.querySelector('.connection-status');
    if (statusElement) {
        statusElement.textContent = status;
        statusElement.className = `connection-status ${navigator.onLine ? 'text-success' : 'text-danger'}`;
    }
}

// مراقبة حالة الاتصال
window.addEventListener('online', updateConnectionStatus);
window.addEventListener('offline', updateConnectionStatus);

// تحديث حالة الاتصال عند تحميل الصفحة
updateConnectionStatus(); 