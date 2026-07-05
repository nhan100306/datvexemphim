<?php
session_start();

// Hàng rào bảo vệ Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once '../db_connect.php';
$admin_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Admin';
$msg = '';

// Xử lý khi Admin bấm nút Lưu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $duration = (int)$_POST['duration'];
    $price = (float)$_POST['price'];
    $release_date = $_POST['release_date'];
    $description = trim($_POST['description']);
    $image_name = 'default_movie.jpg'; // Tên ảnh mặc định nếu không up
    $trailer_url = trim($_POST['trailer_url']);

    // Logic xử lý Upload File Ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/";
        // Đổi tên ảnh bằng hàm time() để tránh trùng lặp file cũ
        $image_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        // Di chuyển file từ bộ nhớ tạm vào thư mục images
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    // Thêm dữ liệu vào Database
    if (!empty($title)) {
        try {
            $sql = "INSERT INTO movies (title, description, release_date, duration, price, image, trailer_url) 
                    VALUES (:title, :description, :release_date, :duration, :price, :image, :trailer_url)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'title' => $title,
                'description' => $description,
                'release_date' => $release_date,
                'duration' => $duration,
                'price' => $price,
                'image' => $image_name,
                'trailer_url' => $trailer_url
            ]);
            $msg = "<div class='alert alert-success'><i class='bi bi-check-circle me-2'></i> Thêm phim mới thành công!</div>";
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'>Lỗi hệ thống: " . $e->getMessage() . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning'>Vui lòng nhập tên phim!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Phim Mới - Nova Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
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
            <a href="movies.php" class="active"><i class="bi bi-film nav-icon"></i> Quản lý Phim</a>
            <a href="../logout.php" class="text-danger mt-5"><i class="bi bi-box-arrow-left nav-icon"></i> Đăng xuất</a>
        </nav>
    </div>

    <div class="content-wrapper">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <a href="movies.php" class="text-decoration-none text-secondary"><i class="bi bi-arrow-left me-2"></i>Quay lại</a> / Thêm phim mới
            </h5>
        </header>

        <div class="card shadow-sm border-0 rounded-3 max-w-700 mx-auto" style="max-width: 800px;">
            <div class="card-body p-4 p-md-5">
                <?= $msg ?>

                <form action="movie_add.php" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên phim <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="Nhập tên bộ phim...">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Thời lượng (phút) <span class="text-danger">*</span></label>
                            <input type="number" name="duration" class="form-control" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Giá vé cơ bản (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" required min="0" step="1000">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ngày khởi chiếu <span class="text-danger">*</span></label>
                        <input type="date" name="release_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung phim</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Tóm tắt nội dung phim..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Poster phim (Ảnh)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div class="form-text">Khuyên dùng ảnh tỷ lệ dọc (Ví dụ: 800x1200px).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Link Trailer (YouTube)</label>
                        <input type="url" name="trailer_url" class="form-control" placeholder="Ví dụ: https://www.youtube.com/watch?v=ABCXYZ...">
                        <small class="text-muted">Chỉ cần dán link YouTube bình thường của phim vào đây.</small>
                    </div>
                    <hr class="mb-4">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning fw-bold py-2 fs-5"><i class="bi bi-floppy me-2"></i>LƯU PHIM MỚI</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>

</html>