<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrakan Bu Teti</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo isset($is_admin_page) ? '../' : ''; ?>assets/images/favicon.jpg">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Primary Colors */
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            
            /* Secondary Colors */
            --secondary-color: #64748b;
            --secondary-dark: #475569;
            --secondary-light: #94a3b8;
            
            /* Success Colors */
            --success-color: #059669;
            --success-light: #10b981;
            
            /* Danger Colors */
            --danger-color: #dc2626;
            --danger-light: #ef4444;
            
            /* Warning Colors */
            --warning-color: #d97706;
            --warning-light: #f59e0b;
            
            /* Neutral Colors */
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            
            /* Spacing */
            --section-spacing: 5rem;
            --content-spacing: 2rem;
        }

        /* Global Styles */
        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background-color: var(--bg-light);
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            line-height: 1.2;
        }

        .section-title {
            font-size: 2rem;
            margin-bottom: var(--content-spacing);
            position: relative;
            padding-bottom: 1rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        /* Buttons */
        .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--primary-color) !important;
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-dark) !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        /* Forms */
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border-color: #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Badges */
        .badge {
            padding: 0.5em 1em;
            border-radius: 6px;
            font-weight: 500;
        }

        /* Tables */
        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            background-color: var(--bg-light);
        }

        /* Utilities */
        .text-primary {
            color: var(--primary-color) !important;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        /* Gradients */
        .bg-gradient-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, var(--success-color), var(--success-light));
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, var(--danger-color), var(--danger-light));
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Responsive Styles */
        @media (max-width: 991.98px) {
            .navbar {
                padding: 0.5rem 0;
            }

            .navbar-collapse {
                background: rgba(255, 255, 255, 0.98);
                padding: 1rem;
                border-radius: 8px;
                margin-top: 0.5rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .nav-item {
                padding: 0.25rem 0;
            }

            .dropdown-menu {
                border: none;
                background: transparent;
                padding-left: 1rem;
            }

            .section-title {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 767.98px) {
            .btn-lg {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }

            .display-4 {
                font-size: 2rem;
            }

            .lead {
                font-size: 1rem;
            }

            .card-title {
                font-size: 1.25rem;
            }

            .table-responsive {
                margin: 0 -1rem;
                padding: 0 1rem;
                width: calc(100% + 2rem);
            }
        }

        @media (max-width: 575.98px) {
            .container {
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .card {
                margin-left: -0.25rem;
                margin-right: -0.25rem;
                border-radius: 8px;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-icon {
                font-size: 2rem;
            }
        }

        /* Utility Classes for Responsive Design */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .flex-gap {
            gap: 1rem;
        }

        @media (max-width: 767.98px) {
            .flex-gap {
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body class="wrapper">
    <?php 
    // Cek apakah ini halaman edit atau create
    $is_edit_page = strpos($_SERVER['PHP_SELF'], '/kontrakan/edit.php') !== false;
    $is_add_page = strpos($_SERVER['PHP_SELF'], '/kontrakan/add.php') !== false;
    $hide_navbar = $is_edit_page || $is_add_page;
    
    // Tampilkan navbar jika bukan halaman edit atau create
    if (!$hide_navbar): 
    ?>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo isset($is_admin_page) ? '../' : ''; ?>index.php">
                <i class="bi bi-house-door"></i> Kontrakan Bu Teti
            </a>
                <ul class="navbar-nav me-auto">
                    <!-- Menghapus menu Beranda -->
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['admin'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> Admin
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo isset($is_admin_page) ? '' : 'admin/'; ?>dashboard.php">Dashboard Admin</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo isset($is_admin_page) ? 'logout.php' : 'admin/logout.php'; ?>">Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <!-- Sesuaikan margin-top berdasarkan keberadaan navbar -->
    <div class="content" style="margin-top: <?php echo $hide_navbar ? '0' : '76px'; ?>;">
