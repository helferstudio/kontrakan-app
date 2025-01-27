<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user adalah admin
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

$is_admin_page = true; // Untuk navigasi
require_once '../config/database.php';

// Konfigurasi pagination
$items_per_page = 10; // Jumlah item per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Query untuk total data
$total_query = "SELECT COUNT(*) as total FROM kontrakan";
$total_result = $mysqli->query($total_query);
$total_items = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

// Query untuk dashboard
$query_total = "SELECT COUNT(*) as total FROM kontrakan";
$query_tersedia = "SELECT COUNT(*) as tersedia FROM kontrakan WHERE status='tersedia'";
$query_terisi = "SELECT COUNT(*) as terisi FROM kontrakan WHERE status='terisi'";

$result_total = mysqli_query($mysqli, $query_total);
$result_tersedia = mysqli_query($mysqli, $query_tersedia);
$result_terisi = mysqli_query($mysqli, $query_terisi);

$total = mysqli_fetch_assoc($result_total)['total'];
$tersedia = mysqli_fetch_assoc($result_tersedia)['tersedia'];
$terisi = mysqli_fetch_assoc($result_terisi)['terisi'];

// Update query dengan LIMIT dan OFFSET
$query = "SELECT k.*, ki.image_name 
          FROM kontrakan k 
          LEFT JOIN kontrakan_images ki ON k.id = ki.kontrakan_id AND ki.is_primary = 1 
          ORDER BY k.nomor_kontrakan ASC
          LIMIT ? OFFSET ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ii", $items_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

include '../includes/header.php';
?>

<div class="dashboard-container">
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <h2 class="section-title mb-0">Dashboard Admin</h2>
            <a href="kontrakan/add.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Kontrakan
            </a>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card stat-card bg-gradient-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-2">Total Kontrakan</h6>
                                <h2 class="mb-0"><?php echo $total; ?></h2>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-houses-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card bg-gradient-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-2">Kontrakan Tersedia</h6>
                                <h2 class="mb-0"><?php echo $tersedia; ?></h2>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-house-check-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card bg-gradient-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-2">Kontrakan Terisi</h6>
                                <h2 class="mb-0"><?php echo $terisi; ?></h2>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-house-dash-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Daftar Kontrakan</h5>
            </div>
            <div class="card-body px-0 px-md-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th>Nomor Kontrakan</th>
                                <th>Harga</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            
                            while ($row = mysqli_fetch_assoc($result)):
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($row['image_name'])): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($row['image_name']); ?>" 
                                                 class="rounded me-3" 
                                                 style="width: 48px; height: 48px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 48px; height: 48px;">
                                                <i class="bi bi-house text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-0">No. <?php echo htmlspecialchars($row['nomor_kontrakan']); ?></h6>
                                            <?php if (!empty($row['deskripsi'])): ?>
                                                <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">
                                                    <?php echo htmlspecialchars($row['deskripsi']); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo $row['status'] == 'tersedia' ? 'success' : 'danger'; ?> rounded-pill">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="kontrakan/edit.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete(<?php echo $row['id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tambahkan pagination setelah tabel -->
        <div class="card-footer bg-white">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    if ($start_page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                        if ($start_page > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                        echo '<a class="page-link" href="?page=' . $i . '">' . $i . '</a>';
                        echo '</li>';
                    }
                    
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                    }
                    ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    background-color: #f8f9fa;
    min-height: calc(100vh - 76px);
}

.section-title {
    position: relative;
    padding-bottom: 0.5rem;
    font-weight: 600;
}

.section-title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 60px;
    height: 3px;
    background: var(--primary-color);
    border-radius: 2px;
}

.stat-card {
    border: none;
    border-radius: 12px;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #2563eb, #3b82f6);
}

.bg-gradient-success {
    background: linear-gradient(45deg, #059669, #10b981);
}

.bg-gradient-info {
    background: linear-gradient(45deg, #0284c7, #0ea5e9);
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.8;
}

.table {
    font-size: 0.95rem;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    transition: all 0.2s;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
}

.btn:hover {
    transform: translateY(-1px);
}

.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}

.pagination {
    margin: 0;
}

.page-link {
    padding: 0.5rem 0.75rem;
    color: var(--primary-color);
    background-color: #fff;
    border: 1px solid #dee2e6;
    transition: all 0.2s;
}

.page-link:hover {
    color: var(--primary-dark);
    background-color: #e9ecef;
    border-color: #dee2e6;
    transform: translateY(-1px);
}

.page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}
</style>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus kontrakan ini?')) {
        window.location.href = 'kontrakan/delete.php?id=' + id;
    }
}
</script>

<?php include '../includes/footer.php'; ?> 