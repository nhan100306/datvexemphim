<?php
session_start();

// Hàng rào bảo vệ Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once '../db_connect.php';
$admin_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Admin';

// Lấy danh sách thành viên từ Database
try {
    $sql = "SELECT id, username, fullname, email, role, created_at FROM users ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Lỗi tải dữ liệu: " . $e->getMessage();
    $members = [];
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thành viên - Nova Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

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

        .sidebar a:hover,
        .sidebar a.active {
            color: #000;
            background-color: #ffc107;
        }

        .sidebar .nav-icon {
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .content-wrapper {
            padding: 20px;
            margin-left: 250px;
        }
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
            <a href="bookings.php"><i class="bi bi-ticket-perforated nav-icon"></i> Hóa đơn & Vé</a>
            <a href="members.php" class="active"><i class="bi bi-people nav-icon"></i> Thành viên</a>
            <hr class="text-secondary mx-3 mt-4">
            <a href="../logout.php" class="text-danger mt-2"><i class="bi bi-box-arrow-left nav-icon"></i> Đăng xuất</a>
        </nav>
    </div>

    <div class="content-wrapper">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded-3">
            <h5 class="mb-0 fw-bold text-secondary">QUẢN LÝ THÀNH VIÊN</h5>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle fw-bold border" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle text-warning fs-5 align-middle me-1"></i> Xin chào, <?= htmlspecialchars($admin_name) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="../index.php" target="_blank"><i class="bi bi-house me-2"></i>Xem trang rạp phim</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                </ul>
            </div>
        </header>
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> Đã xóa thành viên thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['msg'] == 'self_delete_error'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i> Bạn không thể tự xóa tài khoản Admin của chính mình!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['msg'] == 'foreign_key_error'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i> Không thể xóa! Thành viên này đã có lịch sử đặt vé xem phim trong hệ thống.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i>Tài khoản hệ thống</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">ID</th>
                                <th>Tên đăng nhập</th>
                                <th>Họ và tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Ngày đăng ký</th>
                                <th class="text-center" width="10%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($members) > 0): ?>
                                <?php foreach ($members as $user): ?>
                                    <tr>
                                        <td class="text-center fw-bold text-secondary">#<?= $user['id'] ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($user['username']) ?></td>
                                        <td><?= htmlspecialchars($user['fullname']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td>
                                            <?php if ($user['role'] == 1): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Thành viên</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                        <td class="text-center">
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="member_delete.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" title="Xóa tài khoản" onclick="return confirm('Bạn có chắc chắn muốn xóa thành viên này không?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-secondary disabled" title="Không thể tự xóa mình"><i class="bi bi-shield-lock"></i></button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Chưa có thành viên nào.</td>
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