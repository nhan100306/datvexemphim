<?php
session_start();

// Hàng rào bảo vệ Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once '../db_connect.php';
$admin_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Admin';

// Dùng GROUP_CONCAT để gom các ghế lại và GROUP BY để nhóm theo hóa đơn
try {
    $sql = "SELECT b.id AS booking_id, 
                   b.customer_name, 
                   b.customer_phone,
                   b.total_price, 
                   b.booking_date,
                   m.title AS movie_title,
                   s.show_date,
                   s.show_time,
                   GROUP_CONCAT(bs.seat_name SEPARATOR ', ') AS seats
            FROM bookings b
            LEFT JOIN showtimes s ON b.showtime_id = s.id
            LEFT JOIN movies m ON s.movie_id = m.id
            LEFT JOIN booked_seats bs ON b.id = bs.booking_id
            GROUP BY b.id, b.customer_name, b.customer_phone, b.total_price, b.booking_date, m.title, s.show_date, s.show_time
            ORDER BY b.id DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Lỗi tải dữ liệu: " . $e->getMessage();
    $bookings = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Hóa đơn - Nova Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; overflow-x: hidden; }
        .sidebar { min-height: 100vh; background-color: #212529; color: #fff; width: 250px; position: fixed; left: 0; top: 0; z-index: 100; }
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 12px 20px; display: block; border-radius: 5px; margin: 0 10px 5px 10px; transition: 0.2s; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { color: #000; background-color: #ffc107; }
        .sidebar .nav-icon { margin-right: 12px; font-size: 1.1rem; }
        .content-wrapper { padding: 20px; margin-left: 250px; }
        .ticket-code { font-family: 'Courier New', Courier, monospace; font-weight: bold; color: #dc3545; }
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
            <a href="showtimes.php"><i class="bi bi-calendar-event nav-icon"></i> Lịch chiếu</a>
            <a href="bookings.php" class="active"><i class="bi bi-ticket-perforated nav-icon"></i> Hóa đơn & Vé</a>
            <a href="members.php"><i class="bi bi-people nav-icon"></i> Thành viên</a>
            <hr class="text-secondary mx-3 mt-4">
            <a href="../logout.php" class="text-danger mt-2"><i class="bi bi-box-arrow-left nav-icon"></i> Đăng xuất</a>
        </nav>
    </div>

    <div class="content-wrapper">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded-3">
            <h5 class="mb-0 fw-bold text-secondary">QUẢN LÝ HÓA ĐƠN ĐẶT VÉ</h5>
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
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-receipt-cutoff me-2"></i>Lịch sử giao dịch</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="10%">Mã HĐ</th>
                                <th width="15%">Khách hàng</th>
                                <th width="15%">Số điện thoại</th>
                                <th width="20%">Phim & Suất chiếu</th>
                                <th width="15%">Ghế</th>
                                <th width="10%">Tổng tiền</th>
                                <th width="15%">Ngày đặt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($error_msg)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-danger"><?= $error_msg ?></td>
                                </tr>
                            <?php elseif (count($bookings) > 0): ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td class="text-center ticket-code">
                                            #NV-<?= str_pad($booking['booking_id'], 5, '0', STR_PAD_LEFT) ?>
                                        </td>
                                        <td class="fw-bold">
                                            <?= htmlspecialchars($booking['customer_name'] ?? 'Khách vãng lai') ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($booking['customer_phone'] ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary"><?= htmlspecialchars($booking['movie_title'] ?? 'N/A') ?></span><br>
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i><?= date('H:i', strtotime($booking['show_time'] ?? '00:00')) ?> - 
                                                <i class="bi bi-calendar me-1"></i><?= date('d/m/Y', strtotime($booking['show_date'] ?? '1970-01-01')) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark fs-6"><?= htmlspecialchars($booking['seats'] ?? 'N/A') ?></span>
                                        </td>
                                        <td class="fw-bold text-danger">
                                            <?= number_format($booking['total_price'] ?? 0, 0, ',', '.') ?>đ
                                        </td>
                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($booking['booking_date'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Chưa có giao dịch đặt vé nào.</td>
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