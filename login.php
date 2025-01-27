<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login sebagai admin, redirect ke dashboard
if (isset($_SESSION['admin'])) {
    header("Location: admin/dashboard.php");
    exit();
}

require_once 'config/database.php';

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Validasi username (hanya huruf dan angka)
    if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $error = "Username hanya boleh mengandung huruf dan angka!";
    }
    // Validasi password (hanya huruf, angka, dan underscore)
    elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $password)) {
        $error = "Password hanya boleh mengandung huruf, angka, dan underscore!";
    }
    else {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin'] = $user['username'];
                header("Location: admin/dashboard.php");
                exit();
            }
        }
        $error = "Username atau password salah!";
    }
}

include 'includes/header.php';
?>

<div class="login-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">Login Admin</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username" 
                                           name="username" 
                                           pattern="[a-zA-Z0-9]+"
                                           required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           pattern="[a-zA-Z0-9_]+"
                                           required>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary px-4">
                                    Login
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.login-page {
    background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('assets/images/login-bg.jpeg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 100vh;
}

.card {
    border: none;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    background-color: rgba(255, 255, 255, 0.95);
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    background-color: var(--primary-color) !important;
}

.input-group-text {
    background-color: transparent;
    border-right: none;
}

.input-group .form-control {
    border-left: none;
    background-color: transparent;
}

.input-group .form-control:focus {
    border-color: #dee2e6;
    box-shadow: none;
    background-color: transparent;
}

.input-group:focus-within {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    border-radius: 0.375rem;
}

.input-group:focus-within .input-group-text,
.input-group:focus-within .form-control {
    border-color: #86b7fe;
}

.btn-primary {
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}
</style>

<?php include 'includes/footer.php'; ?> 