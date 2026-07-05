<?php
session_start();

// Hàng rào bảo vệ Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once '../db_connect.php';
$admin_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Admin';

// Lấy danh sách lịch chiếu kết hợp (JOIN) với bảng movies để lấy tên phim
try {
    $sql = "SELECT s.id AS showtime_id, 
                   s.show_date, 
                   s.show_time, 
                   m.title AS movie_title, 
                   m.duration 
            FROM showtimes s
            INNER JOIN movies m ON s.movie_id = m.id
            ORDER BY s.show_date DESC, s.show_time DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $showtimes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Lỗi tải dữ liệu: " . $e->getMessage();
    $showtimes = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Lịch chiếu - Nova Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; overflow-x: hidden; }
        .sidebar { min-height: 100vh; background-color: #212529; color: #fff; width: 250px; position: fixed; left: 0; top: 0; z-index: 100; }
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 12px 20px; display: block; border-radius: 5px; margin: 0 10px 5px 10px; transition: 0.2s; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { color: #000; background-color: #ffc107; }
        .sidebar .nav-icon { margin-right: 12px; font-size: 1.1rem; }
        .content-wrapper { padding: 20px; margin-left: 250px; }
    </style>
</head>
<body>

    <div class="sidebar py-3 shadow-lg">
        <div class="text-center mb-4">
            <img src="../assets/images/logo_vip.png" alt="Logo" style="height: 45px;">
        </div>
        <hr class="text-secondary mx-3">
        <nav class="mt-3">
            <a href="dashboard.php"><i class="bi bi-speedometer2 nav-icon"></i> Tổng quan</a>
            <a href="movies.php"><i class="bi bi-film nav-icon"></i> Quản lý Phim</a>
            <a href="showtimes.php" class="active"><i class="bi bi-calendar-event nav-icon"></i> Lịch chiếu</a>
            <a href="bookings.php"><i class="bi bi-ticket-perforated nav-icon"></i> Hóa đơn & Vé</a>
            <a href="members.php"><i class="bi bi-people nav-icon"></i> Thành viên</a>
            <hr class="text-secondary mx-3 mt-4">
            <a href="../logout.php" class="text-danger mt-2"><i class="bi bi-box-arrow-left nav-icon"></i> Đăng xuất</a>
        </nav>
    </div>

    <div class="content-wrapper">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded-3">
            <h5 class="mb-0 fw-bold text-secondary">QUẢN LÝ LỊCH CHIẾU</h5>
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

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2"></i>Các suất chiếu trong hệ thống</h6>
                <a href="showtime_add.php" class="btn btn-warning fw-bold btn-sm shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Thêm suất chiếu
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">ID</th>
                                <th width="35%">Tên phim</th>
                                <th width="20%">Ngày chiếu</th>
                                <th width="15%">Giờ chiếu</th>
                                <th width="15%">Thời lượng</th>
                                <th class="text-center" width="10%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($error_msg)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-danger"><?= $error_msg ?></td>
                                </tr>
                            <?php elseif (count($showtimes) > 0): ?>
                                <?php foreach ($showtimes as $show): ?>
                                    <tr>
                                        <td class="text-center fw-bold text-secondary">#<?= $show['showtime_id'] ?></td>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($show['movie_title']) ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y', strtotime($show['show_date'])) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock-fill me-1"></i> <?= date('H:i', strtotime($show['show_time'])) ?></span>
                                        </td>
                                        <td><?= $show['duration'] ?> phút</td>
                                        <td class="text-center">
                                            <a href="showtime_delete.php?id=<?= $show['showtime_id'] ?>" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Xóa suất chiếu này sẽ ảnh hưởng đến vé đã đặt. Bạn chắc chắn chứ?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Chưa có suất chiếu nào được tạo.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>