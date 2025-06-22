</main>
<footer class="footer mt-auto py-4 bg-dark text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <h5 style="color: #009688;"><?php echo __('site_name'); ?></h5>
                <p class="text-muted"><?php echo __('footer_desc'); ?></p>
            </div>
            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h5><?php echo __('quick_links'); ?></h5>
                <ul class="list-unstyled mb-0">
                    <li><a href="index.php" class="text-white-50"><?php echo __('home'); ?></a></li>
                    <li><a href="index.php#courses" class="text-white-50"><?php echo __('courses'); ?></a></li>
                    <li><a href="login.php" class="text-white-50"><?php echo __('login'); ?></a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5><?php echo __('contact_us'); ?></h5>
                <ul class="list-unstyled mb-0">
                    <li class="text-white-50"><i class="bi bi-geo-alt-fill <?php echo get_current_direction() === 'rtl' ? 'me-2' : 'ms-2'; ?>"></i> <?php echo __('address'); ?></li>
                    <li class="text-white-50"><i class="bi bi-telephone-fill <?php echo get_current_direction() === 'rtl' ? 'me-2' : 'ms-2'; ?>"></i> <?php echo __('phone'); ?></li>
                    <li class="text-white-50"><i class="bi bi-envelope-fill <?php echo get_current_direction() === 'rtl' ? 'me-2' : 'ms-2'; ?>"></i> <?php echo __('email_contact'); ?></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5><?php echo __('follow_us'); ?></h5>
                <a href="#" class="text-white-50 <?php echo get_current_direction() === 'rtl' ? 'me-3' : 'ms-3'; ?> fs-4"><i class="bi bi-twitter"></i></a>
                <a href="#" class="text-white-50 <?php echo get_current_direction() === 'rtl' ? 'me-3' : 'ms-3'; ?> fs-4"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-white-50 fs-4"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center text-white-50">
            <p>&copy; <?php echo date('Y'); ?> <?php echo __('copyright'); ?></p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
