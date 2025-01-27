<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
include 'includes/header.php';

// Query untuk mengambil data kontrakan dengan gambar utama
$query = "SELECT k.*, ki.image_name 
          FROM kontrakan k 
          LEFT JOIN kontrakan_images ki ON k.id = ki.kontrakan_id AND ki.is_primary = 1 
          ORDER BY k.nomor_kontrakan ASC";
$result = $mysqli->query($query);
?>

<div class="hero bg-light py-5 mb-5">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="display-4 fw-bold mb-3">Temukan Kontrakan Nyaman di Lokasi Strategis</h1>
                <p class="lead mb-4">Kontrakan Bu Teti menyediakan hunian nyaman dengan harga terjangkau dan lokasi strategis.</p>
                <div class="d-flex flex-gap flex-wrap">
                    <a href="#daftar-kontrakan" class="btn btn-primary">
                        <i class="bi bi-house-door"></i> Lihat Kontrakan
                    </a>
                    <a href="https://wa.me/6289639913569" class="btn btn-success">
                        <i class="bi bi-whatsapp"></i> Hubungi Kami
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded-3 shadow-lg">
                        <div class="carousel-item active">
                            <img src="assets/images/image.jpg" class="d-block w-100" alt="Kontrakan Image 1" 
                                 style="height: 400px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/images/image2.jpg" class="d-block w-100" alt="Kontrakan Image 2"
                                 style="height: 400px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/images/image3.jpg" class="d-block w-100" alt="Kontrakan Image 3"
                                 style="height: 400px; object-fit: cover;">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="daftar-kontrakan" class="container py-5">
    <h2 class="section-title" data-aos="fade-up">Daftar Kontrakan</h2>
    
    <div class="row g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="card h-100">
                        <?php if (!empty($row['image_name'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image_name']); ?>" 
                                 class="card-img-top" 
                                 alt="Foto Kontrakan"
                                 style="height: 250px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 250px;">
                                <i class="bi bi-house text-muted" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-3">Kontrakan No. <?php echo htmlspecialchars($row['nomor_kontrakan']); ?></h5>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-primary fw-bold">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?>/bulan</span>
                                <span class="badge bg-<?php echo $row['status'] == 'tersedia' ? 'success' : 'danger'; ?> rounded-pill">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </div>
                            <?php if (!empty($row['deskripsi'])): ?>
                                <p class="card-text text-muted text-truncate-2">
                                    <?php echo htmlspecialchars($row['deskripsi']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent border-0 pb-3">
                            <a href="details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary w-100">
                                <i class="bi bi-info-circle"></i> Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Belum ada kontrakan yang ditambahkan.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- CSS tambahan -->
<style>
.card {
    transition: transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.card-img-top {
    border-bottom: 1px solid #eee;
}

.badge {
    font-size: 0.9em;
}

.btn-whatsapp {
    background-color: #25D366;
    border-color: #25D366;
    color: white;
}

.btn-whatsapp:hover {
    background-color: #128C7E;
    border-color: #128C7E;
    color: white;
}

.carousel-inner {
    border-radius: 12px;
    overflow: hidden;
}

.carousel-item img {
    transition: transform 0.3s ease;
}

.carousel-item.active img {
    transform: scale(1.02);
}

.carousel-control-prev,
.carousel-control-next {
    width: 40px;
    height: 40px;
    background: rgba(0,0,0,0.5);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    margin: 0 10px;
}

.carousel-control-prev {
    left: 10px;
}

.carousel-control-next {
    right: 10px;
}
</style>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>

<?php include 'includes/footer.php'; ?> 