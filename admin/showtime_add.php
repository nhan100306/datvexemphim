<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once '../db_connect.php';
$msg = '';

// Lấy danh sách phim để đưa vào thẻ <select>
try {
    $stmt = $conn->query("SELECT id, title FROM movies ORDER BY id DESC");
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $movies = [];
}

// Xử lý khi bấm nút Lưu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $movie_id = (int)$_POST['movie_id'];
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];

    if ($movie_id > 0 && !empty($show_date) && !empty($show_time)) {
        try {
            $sql = "INSERT INTO showtimes (movie_id, show_date, show_time, format, is_hot) VALUES (:movie_id, :show_date, :show_time, :format, :is_hot)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'movie_id' => $movie_id,
                'show_date' => $show_date,
                'show_time' => $show_time,
                'format'    => $_POST['format'],
                'is_hot'    => $_POST['is_hot']
            ]);
            $msg = "<div class='alert alert-success'>Thêm suất chiếu thành công!</div>";
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning'>Vui lòng điền đủ thông tin!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm Lịch Chiếu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between">
                <h5 class="mb-0 fw-bold">Thêm suất chiếu mới</h5>
                <a href="showtimes.php" class="btn btn-sm btn-secondary">Quay lại</a>
            </div>
            <div class="card-body p-4">
                <?= $msg ?>
                <form action="showtime_add.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn Phim</label>
                        <select name="movie_id" class="form-select" required>
                            <option value="">-- Vui lòng chọn phim --</option>
                            <?php foreach ($movies as $movie): ?>
                                <option value="<?= $movie['id'] ?>"><?= htmlspecialchars($movie['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ngày chiếu</label>
                        <input type="date" name="show_date" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Giờ chiếu</label>
                        <input type="time" name="show_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Định dạng</label>
                        <select name="format" class="form-select" required>
                            <option value="2D Sub">2D Sub</option>
                            <option value="2D Lồng Tiếng">2D Lồng Tiếng</option>
                            <option value="3D Sub">3D Sub</option>
                            <option value="3D IMAX">3D IMAX</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Suất chiếu Hot</label>
                        <select name="is_hot" class="form-select">
                            <option value="0">Thường</option>
                            <option value="1">Hot</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">LƯU SUẤT CHIẾU</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>