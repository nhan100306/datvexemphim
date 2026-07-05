<?php
session_start();

// Hàng rào bảo vệ Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit;
}

// Nhúng kết nối CSDL (Lưu ý: file db_connect.php nằm ở thư mục gốc nên phải dùng ../)
require_once '../db_connect.php';

$admin_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Admin';

// Lấy danh sách phim từ Database
try {
    $sql = "SELECT * FROM movies ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Lỗi tải dữ liệu: " . $e->getMessage();
    $movies = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Phim - Nova Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; overflow-x: hidden; }
        /* Style Sidebar giữ nguyên như Dashboard */
        .sidebar { min-height: 100vh; background-color: #212529; color: #fff; width: 250px; position: fixed; left: 0; top: 0; z-index: 100; }
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 12px 20px; display: block; border-radius: 5px; margin: 0 10px 5px 10px; transition: 0.2s; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { color: #000; background-color: #ffc107; }
        .sidebar .nav-icon { margin-right: 12px; font-size: 1.1rem; }
        .content-wrapper { padding: 20px; margin-left: 250px; }
        .movie-thumb { width: 50px; height: 70px; object-fit: cover; border-radius: 5px; }
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
            <a href="dashboard.php"><i class="bi bi-speedometer2 nav-icon"></i> Tổng quan</a>
            <a href="movies.php" class="active"><i class="bi bi-film nav-icon"></i> Quản lý Phim</a>
            <a href="showtimes.php"><i class="bi bi-calendar-event nav-icon"></i> Lịch chiếu</a>
            <a href="bookings.php"><i class="bi bi-ticket-perforated nav-icon"></i> Hóa đơn & Vé</a>
            <a href="members.php"><i class="bi bi-people nav-icon"></i> Thành viên</a>
            <hr class="text-secondary mx-3 mt-4">
            <a href="../logout.php" class="text-danger mt-2"><i class="bi bi-box-arrow-left nav-icon"></i> Đăng xuất</a>
        </nav>
    </div>

    <div class="content-wrapper">
        
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded-3">
            <h5 class="mb-0 fw-bold text-secondary">QUẢN LÝ DANH SÁCH PHIM</h5>
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
                <h6 class="mb-0 fw-bold"><i class="bi bi-list-ul me-2"></i>Tất cả phim</h6>
                <a href="movie_add.php" class="btn btn-warning fw-bold btn-sm shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Thêm phim mới
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">ID</th>
                                <th width="10%">Poster</th>
                                <th width="35%">Tên phim</th>
                                <th width="15%">Thời lượng</th>
                                <th width="15%">Giá vé cơ bản</th>
                                <th class="text-center" width="20%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($movies) > 0): ?>
                                <?php foreach ($movies as $movie): ?>
                                    <tr>
                                        <td class="text-center fw-bold text-secondary">#<?= $movie['id'] ?></td>
                                        <td>
                                            <img src="../assets/images/<?= htmlspecialchars($movie['image']) ?>" alt="Poster" class="movie-thumb shadow-sm">
                                        </td>
                                        <td class="fw-bold"><?= htmlspecialchars($movie['title']) ?></td>
                                        <td><i class="bi bi-clock me-1 text-muted"></i> <?= $movie['duration'] ?> phút</td>
                                        <td class="text-danger fw-bold"><?= number_format($movie['price'], 0, ',', '.') ?>đ</td>
                                        <td class="text-center">
                                            <a href="movie_edit.php?id=<?= $movie['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="movie_delete.php?id=<?= $movie['id'] ?>" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa phim này không?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Chưa có bộ phim nào trong cơ sở dữ liệu.</td>
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