// تبديل عرض الدورات بين الشبكة والقائمة
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('[data-view]');
    const coursesGrid = document.getElementById('courses-grid');

    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // تحديث حالة الأزرار
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // تحديث عرض الدورات
            if (view === 'list') {
                coursesGrid.classList.remove('row');
                coursesGrid.classList.add('list-view');
            } else {
                coursesGrid.classList.remove('list-view');
                coursesGrid.classList.add('row');
            }
        });
    });
});

// تصفية الدورات
function filterCourses() {
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const courseCards = document.querySelectorAll('.card');

    const searchTerm = searchInput.value.toLowerCase();
    const categoryId = categorySelect.value;

    courseCards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const description = card.querySelector('.card-text').textContent.toLowerCase();
        const category = card.querySelector('.badge').textContent.toLowerCase();
        const cardCategoryId = card.dataset.categoryId;

        const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
        const matchesCategory = !categoryId || cardCategoryId === categoryId;

        card.closest('.col-md-6').style.display = matchesSearch && matchesCategory ? 'block' : 'none';
    });
}

// إضافة تأثيرات التمرير
const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
};

const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

document.querySelectorAll('.card').forEach(card => {
    observer.observe(card);
});

// تحديث عدد المتدربين في الوقت الفعلي
function updateEnrollmentCount(courseId, count) {
    const countElement = document.querySelector(`#course-${courseId} .enrollment-count`);
    if (countElement) {
        countElement.textContent = `${count} متدرب`;
    }
}

// إضافة دورة إلى المفضلة
function toggleFavorite(courseId) {
    const button = document.getElementById(`favorite-${courseId}`);
    const icon = button.querySelector('i');
    
    fetch('../api/favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ course_id: courseId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            icon.classList.toggle('bi-heart');
            icon.classList.toggle('bi-heart-fill');
            button.classList.toggle('btn-outline-primary');
            button.classList.toggle('btn-primary');
        }
    })
    .catch(error => console.error('Error:', error));
}

// التسجيل في الدورة
function enrollCourse(courseId) {
    fetch('../api/enroll.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ course_id: courseId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = `learn.php?id=${courseId}`;
        }
    })
    .catch(error => console.error('Error:', error));
}

// إضافة تعليق
document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const textarea = this.querySelector('textarea');
            const content = textarea.value.trim();
            
            if (!content) return;
            
            const courseId = new URLSearchParams(window.location.search).get('id');
            
            fetch('../api/comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    course_id: courseId,
                    content: content
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // إضافة التعليق الجديد للصفحة
                    const commentsDiv = document.getElementById('comments');
                    const commentHtml = `
                        <div class="comment mb-3">
                            <div class="d-flex">
                                <img src="${data.avatar || '../assets/images/default-avatar.png'}" 
                                     class="rounded-circle me-3" width="40" height="40" 
                                     alt="${data.full_name}">
                                <div>
                                    <h6 class="mb-1">${data.full_name}</h6>
                                    <p class="text-muted small">الآن</p>
                                    <p>${content}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    commentsDiv.insertAdjacentHTML('afterbegin', commentHtml);
                    textarea.value = '';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
});

// تحديث تقدم الدورة
function updateProgress(courseId, moduleId) {
    fetch('../api/progress.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            course_id: courseId,
            module_id: moduleId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث شريط التقدم
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.style.width = `${data.progress}%`;
                progressBar.textContent = `${data.progress}%`;
            }
            
            // تحديث حالة الوحدة
            const moduleBadge = document.querySelector(`#module-${moduleId} .badge`);
            if (moduleBadge) {
                moduleBadge.classList.remove('bg-secondary');
                moduleBadge.classList.add('bg-success');
                moduleBadge.innerHTML = '<i class="bi bi-check-circle"></i>';
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

// تحميل المزيد من الدورات
let currentPage = 1;
const loadMoreButton = document.getElementById('load-more');

if (loadMoreButton) {
    loadMoreButton.addEventListener('click', function() {
        currentPage++;
        const category = document.getElementById('category').value;
        const search = document.getElementById('search').value;

        fetch(`/api/courses?page=${currentPage}&category=${category}&search=${search}`)
            .then(response => response.json())
            .then(data => {
                if (data.courses.length > 0) {
                    const coursesGrid = document.getElementById('courses-grid');
                    data.courses.forEach(course => {
                        coursesGrid.appendChild(createCourseCard(course));
                    });
                } else {
                    loadMoreButton.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء تحميل المزيد من الدورات');
            });
    });
}

// إنشاء بطاقة دورة جديدة
function createCourseCard(course) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4 mb-4';
    col.dataset.categoryId = course.category_id;

    col.innerHTML = `
        <div class="card h-100">
            ${course.image ? `
                <img src="${course.image}" class="card-img-top" alt="${course.title}">
            ` : ''}
            <div class="card-body">
                <h5 class="card-title">${course.title}</h5>
                <p class="card-text text-muted">
                    ${course.description.substring(0, 100)}...
                </p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary">${course.category_name}</span>
                    <small class="text-muted enrollment-count">${course.enrolled_count} متدرب</small>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="view.php?id=${course.id}" class="btn btn-primary w-100">عرض التفاصيل</a>
            </div>
        </div>
    `;

    return col;
} 