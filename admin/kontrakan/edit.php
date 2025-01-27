<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user adalah admin
if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

$is_admin_page = true;
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$id = $_GET['id'];

// Ambil data kontrakan
$query = "SELECT * FROM kontrakan WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$kontrakan = $result->fetch_assoc();

// Ambil gambar-gambar kontrakan
$query_images = "SELECT * FROM kontrakan_images WHERE kontrakan_id = ? ORDER BY is_primary DESC";
$stmt_images = $mysqli->prepare($query_images);
$stmt_images->bind_param("i", $id);
$stmt_images->execute();
$images = $stmt_images->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomor_kontrakan = $_POST['nomor_kontrakan'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];
    $deskripsi = $_POST['deskripsi'];
    
    // Update data kontrakan
    $query = "UPDATE kontrakan SET nomor_kontrakan = ?, harga = ?, status = ?, deskripsi = ? WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sissi", $nomor_kontrakan, $harga, $status, $deskripsi, $id);
    
    if ($stmt->execute()) {
        // Handle penghapusan gambar yang dipilih
        if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $image_id) {
                // Ambil nama file sebelum dihapus
                $query = "SELECT image_name FROM kontrakan_images WHERE id = ? AND kontrakan_id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("ii", $image_id, $id);
                $stmt->execute();
                $image = $stmt->get_result()->fetch_assoc();
                
                if ($image) {
                    // Hapus file fisik
                    $file_path = "../../uploads/" . $image['image_name'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                    
                    // Hapus record dari database
                    $query = "DELETE FROM kontrakan_images WHERE id = ? AND kontrakan_id = ?";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("ii", $image_id, $id);
                    $stmt->execute();
                }
            }
        }
        
        // Upload gambar baru jika ada
        if (!empty($_FILES['images']['name'][0])) {
            $upload_path = "../../uploads/";
            
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['images']['name'][$key];
                $file_size = $_FILES['images']['size'][$key];
                $file_tmp = $_FILES['images']['tmp_name'][$key];
                $file_type = $_FILES['images']['type'][$key];
                
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $unique_name = uniqid() . '_' . time() . '.' . $file_ext;
                
                $allowed = array("jpg", "jpeg", "png", "gif");
                
                if (in_array($file_ext, $allowed)) {
                    if ($file_size < 5242880) { // 5MB max
                        if (move_uploaded_file($file_tmp, $upload_path . $unique_name)) {
                            // Set sebagai primary jika belum ada gambar
                            $is_primary = ($images->num_rows == 0 && $key == 0) ? 1 : 0;
                            
                            $query = "INSERT INTO kontrakan_images (kontrakan_id, image_name, is_primary) VALUES (?, ?, ?)";
                            $stmt = $mysqli->prepare($query);
                            $stmt->bind_param("isi", $id, $unique_name, $is_primary);
                            $stmt->execute();
                        }
                    }
                }
            }
        }
        
        header("Location: ../dashboard.php");
        exit();
    }
}

include '../../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Kontrakan</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <!-- Form fields -->
                        <div class="mb-3">
                            <label for="nomor_kontrakan" class="form-label">Nomor Kontrakan</label>
                            <input type="text" class="form-control" id="nomor_kontrakan" name="nomor_kontrakan" 
                                   value="<?php echo htmlspecialchars($kontrakan['nomor_kontrakan']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" 
                                   value="<?php echo $kontrakan['harga']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="tersedia" <?php echo $kontrakan['status'] == 'tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                                <option value="terisi" <?php echo $kontrakan['status'] == 'terisi' ? 'selected' : ''; ?>>Terisi</option>
                            </select>
                        </div>
                        
                        <!-- Existing Images -->
                        <?php if ($images->num_rows > 0): ?>
                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label>
                            <div class="row g-3">
                                <?php while ($img = $images->fetch_assoc()): ?>
                                <div class="col-6 col-md-4">
                                    <div class="card h-100">
                                        <img src="../../uploads/<?php echo htmlspecialchars($img['image_name']); ?>" 
                                             class="card-img-top"
                                             style="height: 150px; object-fit: cover;"
                                             alt="Foto Kontrakan">
                                        <div class="card-body p-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="delete_images[]" 
                                                       value="<?php echo $img['id']; ?>" 
                                                       id="img_<?php echo $img['id']; ?>">
                                                <label class="form-check-label" for="img_<?php echo $img['id']; ?>">
                                                    Hapus
                                                </label>
                                            </div>
                                            <?php if ($img['is_primary']): ?>
                                                <small class="text-primary">Foto Utama</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Upload New Images -->
                        <div class="mb-3">
                            <label class="form-label">Tambah Foto Baru</label>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                                <label class="input-group-text" for="images">Upload</label>
                            </div>
                            <div id="imagePreview" class="row g-2 mt-2"></div>
                            <small class="text-muted">Anda dapat memilih beberapa foto sekaligus.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo htmlspecialchars($kontrakan['deskripsi']); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="../dashboard.php" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview gambar sebelum upload
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    [...e.target.files].forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3';
            
            const card = document.createElement('div');
            card.className = 'card h-100';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'card-img-top';
            img.style.height = '150px';
            img.style.objectFit = 'cover';
            
            const cardBody = document.createElement('div');
            cardBody.className = 'card-body p-2';
            cardBody.innerHTML = `<small class="text-muted">Foto ${index + 1}</small>`;
            
            card.appendChild(img);
            card.appendChild(cardBody);
            col.appendChild(card);
            preview.appendChild(col);
        }
        reader.readAsDataURL(file);
    });
});
</script>

<?php include '../../includes/footer.php'; ?> 