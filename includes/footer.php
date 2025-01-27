    </div> <!-- Penutup div.content -->
    <footer class="bg-dark py-5">
        <div class="container">
            <div class="row gy-4">
                <div class="col-sm-6 col-lg-4" data-aos="fade-up">
                    <h5 class="text-white fw-bold mb-3">Hubungi Kami</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <a href="https://wa.me/6289639913569" 
                               class="text-white-50 text-decoration-none d-flex align-items-center hover-primary">
                                <i class="bi bi-whatsapp fs-5 me-2"></i>
                                +62 896-3991-3569
                            </a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/teti.herawati.56884" 
                               class="text-white-50 text-decoration-none d-flex align-items-center hover-primary" 
                               target="_blank">
                                <i class="bi bi-facebook fs-5 me-2"></i>
                                Facebook
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <h5 class="text-white fw-bold mb-3">Lokasi Kontrakan</h5>
                    <p class="text-white-50 mb-0">
                        <i class="bi bi-geo-alt fs-5 me-2"></i>
                        Jl. Raya Bosih Gg Bunga, RT.05/RW.13<br>
                        <span class="ms-4">Cibitung, 17520</span><br>
                        <span class="ms-4">Indonesia</span>
                    </p>
                </div>
                <div class="col-sm-12 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <h5 class="text-white fw-bold mb-3">Jam Operasional</h5>
                    <p class="text-white-50 mb-0">
                        <i class="bi bi-clock fs-5 me-2"></i>
                        Senin - Minggu<br>
                        <span class="ms-4">08:00 - 21:00 WIB</span>
                    </p>
                    <?php if (!isset($_SESSION['admin'])): ?>
                        <div class="mt-3">
                            <a href="<?php echo isset($is_admin_page) ? '../' : ''; ?>login.php" 
                               class="text-white-50 text-decoration-none d-flex align-items-center hover-primary">
                                <i class="bi bi-shield-lock fs-5 me-2"></i>
                                <span>Admin Login</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="text-center">
                <p class="text-white-50 mb-0">
                    &copy; <?php echo date('Y'); ?> Helfer Quality Ware Studios
                    <span class="d-block d-sm-inline mt-1 mt-sm-0">All rights reserved.</span>
                </p>
            </div>
        </div>
    </footer>

    <style>
    footer {
        background: linear-gradient(to right, #1a1c2b, #2c3e50);
    }

    footer a.hover-primary:hover,
    footer a.text-white-50:hover {
        color: var(--primary-light) !important;
        text-decoration: none;
    }

    footer i {
        transition: transform 0.3s ease;
    }

    footer a:hover i {
        transform: translateX(5px);
    }
    </style>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html>
