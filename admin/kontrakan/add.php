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

// Proses form jika ada POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomor_kontrakan = $_POST['nomor_kontrakan'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];
    $deskripsi = $_POST['deskripsi'];
    
    // Insert data kontrakan
    $query = "INSERT INTO kontrakan (nomor_kontrakan, harga, status, deskripsi) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("siss", $nomor_kontrakan, $harga, $status, $deskripsi);
    
    if ($stmt->execute()) {
        $kontrakan_id = $mysqli->insert_id;
        
        // Upload multiple images
        if (!empty($_FILES['images']['name'][0])) {
            $upload_path = "../../uploads/";
            
            // Create upload directory if not exists
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['images']['name'][$key];
                $file_size = $_FILES['images']['size'][$key];
                $file_tmp = $_FILES['images']['tmp_name'][$key];
                $file_type = $_FILES['images']['type'][$key];
                
                // Generate unique filename
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $unique_name = uniqid() . '_' . time() . '.' . $file_ext;
                
                // Allowed file types
                $allowed = array("jpg", "jpeg", "png", "gif");
                
                if (in_array($file_ext, $allowed)) {
                    if ($file_size < 5242880) { // 5MB max
                        if (move_uploaded_file($file_tmp, $upload_path . $unique_name)) {
                            // Set first image as primary
                            $is_primary = ($key === 0) ? 1 : 0;
                            
                            // Insert image info to database
                            $query = "INSERT INTO kontrakan_images (kontrakan_id, image_name, is_primary) VALUES (?, ?, ?)";
                            $stmt = $mysqli->prepare($query);
                            $stmt->bind_param("isi", $kontrakan_id, $unique_name, $is_primary);
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
                    <h5 class="card-title mb-0">Tambah Kontrakan Baru</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nomor_kontrakan" class="form-label">Nomor Kontrakan</label>
                            <input type="text" class="form-control" id="nomor_kontrakan" name="nomor_kontrakan" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="tersedia">Tersedia</option>
                                <option value="terisi">Terisi</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Foto Kontrakan</label>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                                <label class="input-group-text" for="images">Upload</label>
                            </div>
                            <div id="imagePreview" class="row g-2 mt-2"></div>
                            <small class="text-muted">Anda dapat memilih beberapa foto sekaligus. Foto pertama akan menjadi foto utama.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="../dashboard.php" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
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
            cardBody.innerHTML = `<small class="text-muted">${index === 0 ? 'Foto Utama' : `Foto ${index + 1}`}</small>`;
            
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