<?php
session_start();

// HÀNG RÀO BẢO VỆ: Nếu chưa đăng nhập hoặc không phải Admin (role = 1) thì đuổi ra
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit;
}

// Lấy tên admin để chào mừng
$admin_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Điều Khiển - Nova Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f4f6f9; 
            overflow-x: hidden;
        }
        /* Style cho thanh Sidebar bên trái */
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
            color: #fff;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-radius: 5px;
            margin: 0 10px 5px 10px;
            transition: 0.2s;
            font-weight: 500;
        }
        .sidebar a:hover, .sidebar a.active {
            color: #fff;
            background-color: #ffc107; /* Màu vàng của Nova Cinema */
            color: #000;
        }
        .sidebar .nav-icon { margin-right: 12px; font-size: 1.1rem; }
        
        /* Style cho phần nội dung chính bên phải */
        .content-wrapper { 
            padding: 20px; 
            margin-left: 250px; /* Lùi vào một khoảng bằng độ rộng của Sidebar */
        }
        
        /* Style cho các thẻ thống kê */
        .stat-card { border-radius: 10px; color: #fff; padding: 25px 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .bg-gradient-blue { background: linear-gradient(45deg, #0d6efd, #0dcaf0); }
        .bg-gradient-green { background: linear-gradient(45deg, #198754, #20c997); }
        .bg-gradient-orange { background: linear-gradient(45deg, #fd7e14, #ffc107); }
        .bg-gradient-red { background: linear-gradient(45deg, #dc3545, #f87171); }
    </style>
</head>
<body>

    <div class="sidebar py-3 shadow-lg">
        <div class="text-center mb-4">
            <img src="../assets/images/logo_vip.png" alt="Logo" style="height: 45px;">
            <h6 class="mt-3 text-white fw-bold tracking-wider">HỆ THỐNG QUẢN TRỊ</h6>
        </div>
        <hr class="text-secondary mx-3">
        <nav class="mt-3">
            <a href="dashboard.php" class="active"><i class="bi bi-speedometer2 nav-icon"></i> Tổng quan</a>
           <a href="movies.php"><i class="bi bi-film nav-icon"></i> Quản lý Phim</a>
            <a href="#"><i class="bi bi-calendar-event nav-icon"></i> Lịch chiếu</a>
            <a href="#"><i class="bi bi-ticket-perforated nav-icon"></i> Hóa đơn & Vé</a>
            <a href="#"><i class="bi bi-people nav-icon"></i> Thành viên</a>
            <hr class="text-secondary mx-3 mt-4">
            <a href="../logout.php" class="text-danger mt-2"><i class="bi bi-box-arrow-left nav-icon"></i> Đăng xuất</a>
        </nav>
    </div>

    <div class="content-wrapper">
        
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded-3">
            <h5 class="mb-0 fw-bold text-secondary">BẢNG ĐIỀU KHIỂN</h5>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle fw-bold border" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle text-warning fs-5 align-middle me-1"></i> Xin chào, <?= htmlspecialchars($admin_name) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="../index.php" target="_blank"><i class="bi bi-house me-2"></i>Xem trang rạp phim</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                </ul>
            </div>
        </header>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="stat-card bg-gradient-blue">
                    <h3 class="fw-bold fs-1">12</h3>
                    <p class="mb-0 fs-5 opacity-75"><i class="bi bi-film me-2"></i>Phim trong kho</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="stat-card bg-gradient-green">
                    <h3 class="fw-bold fs-1">340</h3>
                    <p class="mb-0 fs-5 opacity-75"><i class="bi bi-ticket me-2"></i>Vé đã bán</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="stat-card bg-gradient-orange text-dark">
                    <h3 class="fw-bold fs-1">15</h3>
                    <p class="mb-0 fs-5 opacity-75"><i class="bi bi-calendar-check me-2"></i>Suất chiếu hôm nay</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="stat-card bg-gradient-red">
                    <h3 class="fw-bold fs-1">24.5M</h3>
                    <p class="mb-0 fs-5 opacity-75"><i class="bi bi-cash-coin me-2"></i>Doanh thu (VNĐ)</p>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center p-5">
                <i class="bi bi-rocket-takeoff text-secondary opacity-25" style="font-size: 5rem;"></i>
                <h4 class="mt-4 fw-bold text-dark">Chào mừng trở lại khu vực điều hành!</h4>
                <p class="text-muted fs-5">Hãy chọn một chức năng bên menu trái để bắt đầu thêm, sửa, xóa dữ liệu hệ thống.</p>
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>