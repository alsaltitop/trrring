    </main>
    
    <!-- Footer -->
    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5><?php echo __('site_title'); ?></h5>
                    <p class="mt-3">
                        <?php echo __('footer_about'); ?>
                    </p>
                    <div class="social-links mt-3">
                        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5><?php echo __('quick_links'); ?></h5>
                    <div class="footer-links">
                        <a href="index.php"><?php echo __('home'); ?></a>
                        <a href="dashboard.php"><?php echo __('dashboard'); ?></a>
                        <a href="#courses"><?php echo __('courses'); ?></a>
                        <a href="#about"><?php echo __('about'); ?></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5><?php echo __('useful_resources'); ?></h5>
                    <div class="footer-links">
                        <a href="#"><?php echo __('faq'); ?></a>
                        <a href="#"><?php echo __('terms'); ?></a>
                        <a href="#"><?php echo __('privacy_policy'); ?></a>
                        <a href="#"><?php echo __('help_center'); ?></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5><?php echo __('contact_us'); ?></h5>
                    <div class="footer-contact">
                        <p>
                            <i class="bi bi-geo-alt"></i> <?php echo __('address'); ?>
                        </p>
                        <p>
                            <i class="bi bi-telephone"></i> <?php echo __('phone'); ?>
                        </p>
                        <p>
                            <i class="bi bi-envelope"></i> <?php echo __('email_address'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center copyright">
                <p>&copy; <?php echo date('Y'); ?> <?php echo __('copyright_text'); ?></p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enable tooltips everywhere
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Enable popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    });
    </script>
</body>
</html>
