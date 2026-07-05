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
$movie = null;

// ==========================================
// 1. XỬ LÝ LẤY DỮ LIỆU CŨ ĐỔ VÀO FORM (GET)
// ==========================================
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Nếu ai đó nhập bừa ID trên thanh địa chỉ mà không có phim -> đuổi về trang danh sách
    if (!$movie) {
        header("Location: movies.php");
        exit;
    }
} elseif (!isset($_POST['id'])) {
    header("Location: movies.php");
    exit;
}

// ==========================================
// 2. XỬ LÝ KHI BẤM NÚT LƯU CẬP NHẬT (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $duration = (int)$_POST['duration'];
    $price = (float)$_POST['price'];
    $release_date = $_POST['release_date'];
    $description = trim($_POST['description']);
    
    // Lấy tên ảnh hiện tại đang lưu trong DB
    $stmt = $conn->prepare("SELECT image FROM movies WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $current_movie = $stmt->fetch(PDO::FETCH_ASSOC);
    $image_name = $current_movie['image']; // Mặc định là giữ nguyên ảnh cũ

    // Nếu có chọn file ảnh mới để upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/";
        $new_image_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $new_image_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // XÓA ẢNH CŨ (Nếu không phải là ảnh mặc định) để tiết kiệm dung lượng
            if (!empty($image_name) && $image_name != 'default_movie.jpg' && file_exists($target_dir . $image_name)) {
                unlink($target_dir . $image_name);
            }
            $image_name = $new_image_name; // Cập nhật tên ảnh mới để lưu vào DB
        }
    }

    // Tiến hành Update vào Database
    if (!empty($title)) {
        try {
            $sql = "UPDATE movies SET 
                        title = :title, 
                        description = :description, 
                        release_date = :release_date, 
                        duration = :duration, 
                        price = :price, 
                        image = :image 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'title' => $title,
                'description' => $description,
                'release_date' => $release_date,
                'duration' => $duration,
                'price' => $price,
                'image' => $image_name,
                'id' => $id
            ]);
            $msg = "<div class='alert alert-success'><i class='bi bi-check-circle me-2'></i> Cập nhật phim thành công!</div>";
            
            // Cập nhật lại biến $movie để form hiển thị ngay dữ liệu mới nhất
            $stmt = $conn->prepare("SELECT * FROM movies WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $movie = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'>Lỗi hệ thống: " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Phim - Nova Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; overflow-x: hidden; }
        .sidebar { min-height: 100vh; background-color: #212529; color: #fff; width: 250px; position: fixed; left: 0; top: 0; z-index: 100; }
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 12px 20px; display: block; border-radius: 5px; margin: 0 10px 5px 10px; transition: 0.2s; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { color: #000; background-color: #ffc107; }
        .sidebar .nav-icon { margin-right: 12px; font-size: 1.1rem; }
        .content-wrapper { padding: 20px; margin-left: 250px; }
        .current-poster { width: 120px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
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
                <a href="movies.php" class="text-decoration-none text-secondary"><i class="bi bi-arrow-left me-2"></i>Quay lại</a> / Chỉnh sửa phim
            </h5>
        </header>

        <div class="card shadow-sm border-0 rounded-3 max-w-700 mx-auto" style="max-width: 800px;">
            <div class="card-body p-4 p-md-5">
                <?= $msg ?>
                
                <form action="movie_edit.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $movie['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên phim <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($movie['title']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Thời lượng (phút) <span class="text-danger">*</span></label>
                            <input type="number" name="duration" class="form-control" value="<?= $movie['duration'] ?>" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Giá vé cơ bản (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" value="<?= $movie['price'] ?>" required min="0" step="1000">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ngày khởi chiếu <span class="text-danger">*</span></label>
                        <input type="date" name="release_date" class="form-control" value="<?= $movie['release_date'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung phim</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($movie['description']) ?></textarea>
                    </div>

                    <div class="mb-4 bg-light p-3 rounded border">
                        <label class="form-label fw-bold">Cập nhật Poster phim (Ảnh)</label>
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3">
                                <p class="mb-1 text-muted small">Ảnh hiện tại:</p>
                                <img src="../assets/images/<?= htmlspecialchars($movie['image']) ?>" class="current-poster">
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <div class="form-text text-primary mt-2"><i class="bi bi-info-circle me-1"></i>Bỏ trống nếu muốn giữ nguyên ảnh cũ.</div>
                            </div>
                        </div>
                    </div>

                    <hr class="mb-4">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold py-2 fs-5"><i class="bi bi-floppy me-2"></i>CẬP NHẬT THÔNG TIN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>