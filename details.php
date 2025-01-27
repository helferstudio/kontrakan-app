<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';

// Ambil ID kontrakan dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM kontrakan WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$kontrakan = $result->fetch_assoc();

// Get kontrakan images
$query_images = "SELECT * FROM kontrakan_images WHERE kontrakan_id = ? ORDER BY is_primary DESC";
$stmt_images = $mysqli->prepare($query_images);
$stmt_images->bind_param("i", $id);
$stmt_images->execute();
$images = $stmt_images->get_result();

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <?php if ($images->num_rows > 0): ?>
                <div id="kontrakanCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                    <div class="carousel-inner rounded-3 shadow-sm">
                        <?php while ($img = $images->fetch_assoc()): ?>
                            <div class="carousel-item <?php echo $img['is_primary'] ? 'active' : ''; ?>">
                                <img src="uploads/<?php echo htmlspecialchars($img['image_name']); ?>" 
                                     class="d-block w-100" 
                                     alt="Foto Kontrakan"
                                     style="height: 500px; object-fit: cover;">
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php if ($images->num_rows > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#kontrakanCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#kontrakanCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    <?php endif; ?>
                </div>
                
                <!-- Thumbnail Navigation -->
                <div class="row g-2 mb-4">
                    <?php 
                    $images->data_seek(0);
                    while ($img = $images->fetch_assoc()): 
                    ?>
                        <div class="col-3">
                            <img src="uploads/<?php echo htmlspecialchars($img['image_name']); ?>" 
                                 class="img-thumbnail" 
                                 style="height: 80px; object-fit: cover; cursor: pointer;"
                                 onclick="setActiveImage(this)"
                                 data-bs-target="#kontrakanCarousel" 
                                 data-bs-slide-to="<?php echo $images->current_field; ?>"
                                 alt="Thumbnail">
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <!-- Deskripsi Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4">Kontrakan No. <?php echo htmlspecialchars($kontrakan['nomor_kontrakan']); ?></h4>
                    
                    <h5 class="mb-3">Deskripsi</h5>
                    <?php if (!empty($kontrakan['deskripsi'])): ?>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($kontrakan['deskripsi'])); ?></p>
                    <?php else: ?>
                        <p class="text-muted">Tidak ada deskripsi.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Harga dan Status Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="text-primary mb-3 fs-4">
                        Rp <?php echo number_format($kontrakan['harga'], 0, ',', '.'); ?>/bulan
                    </h5>
                    <div class="mb-4">
                        <span class="badge bg-<?php echo $kontrakan['status'] == 'tersedia' ? 'success' : 'danger'; ?> rounded-pill">
                            <?php echo ucfirst($kontrakan['status']); ?>
                        </span>
                    </div>
                    
                    <?php if ($kontrakan['status'] == 'tersedia'): ?>
                        <?php
                        $pesan = "Halo Bu Teti, saya tertarik dengan Kontrakan No. " . $kontrakan['nomor_kontrakan'] . 
                                " dengan harga Rp " . number_format($kontrakan['harga'], 0, ',', '.') . "/bulan. " .
                                "Apakah masih tersedia?";
                        $whatsapp_link = "https://wa.me/6289639913569?text=" . urlencode($pesan);
                        ?>
                        <a href="<?php echo $whatsapp_link; ?>" class="btn btn-success w-100 mb-3" target="_blank">
                            <i class="bi bi-whatsapp me-2"></i>Pesan via WhatsApp
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100 mb-3" disabled>
                            Kontrakan Sudah Terisi
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informasi Kontak Card -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Informasi Kontak</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex align-items-center">
                            <i class="bi bi-person-circle fs-5 me-3"></i>
                            <div>
                                <strong>Pemilik</strong><br>
                                Bu Teti
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="bi bi-geo-alt fs-5 me-3"></i>
                            <div>
                                <strong>Alamat</strong><br>
                                Jl. Raya Bosih Gg Bunga, RT.05/RW.13<br>
                                Cibitung, 17520
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<script>
function setActiveImage(element) {
    document.querySelectorAll('.img-thumbnail').forEach(thumb => {
        thumb.classList.remove('border-primary');
    });
    element.classList.add('border-primary');
}
</script>

<style>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 12px;
}

.badge {
    font-size: 0.9em;
    padding: 8px 16px;
}

.btn-success {
    background-color: #25D366;
    border-color: #25D366;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background-color: #128C7E;
    border-color: #128C7E;
    transform: translateY(-2px);
}

.carousel-inner {
    border-radius: 12px;
    overflow: hidden;
}

.img-thumbnail {
    border-radius: 8px;
    transition: all 0.2s ease;
}

.img-thumbnail:hover {
    transform: scale(1.05);
}

.list-unstyled i {
    opacity: 0.8;
}
</style>

<?php include 'includes/footer.php'; ?> 