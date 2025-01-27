<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user adalah admin
if (!isset($_SESSION['admin'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil info foto sebelum menghapus
    $query = "SELECT foto FROM kontrakan WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $kontrakan = $result->fetch_assoc();
    
    // Hapus data dari database
    $query = "DELETE FROM kontrakan WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Hapus foto jika ada
        if (!empty($kontrakan['foto'])) {
            $file_path = "../../uploads/" . $kontrakan['foto'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    // Hapus gambar-gambar terkait
    $query = "SELECT image_name FROM kontrakan_images WHERE kontrakan_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($image = $result->fetch_assoc()) {
        $file_path = "../../uploads/" . $image['image_name'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
}

header("Location: ../dashboard.php");
exit();
?> 